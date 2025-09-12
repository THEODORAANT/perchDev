<?php

class PerchShop_Products extends PerchShop_Factory
{
	public $singular_classname     = 'PerchShop_Product';
	public $static_fields          = ['title', 'productHasVariations', 'sku', 'parentID', 'stock_level', 'productCreated', 'productStatus'];

	protected $table               = 'shop_products';
	protected $pk                  = 'productID';
	protected $index_table         = 'shop_index';
	protected $master_template	   = 'shop/products/product.html';
	
	protected $default_sort_column = 'title';
	protected $created_date_column = 'productCreated';
	protected $deleted_date_column = 'productDeleted';

	protected $event_prefix = 'shop.product';

	protected $runtime_restrictions = [
		[
			'field'          => 'status',
			'values'         => ['1'],
			'negative_match' => false,
			'match'          => 'all',
			'fuzzy'			 => false,
			'defeatable'	 => true,
		],
		[
			'field'          => 'parentID',
			'values'         => [''],
			'negative_match' => false,
			'match'          => 'all',
			'fuzzy'			 => false,
			'defeatable'	 => true,
		],
	];


	public function get_filtered_listing($opts, $where_callback=null, $pre_template_callback=null)
    {
    	$opts['defeat-restrictions'] = true; // these are reimplemented as a WHERE in the callback, for performance.

    	return parent::get_filtered_listing($opts, $where_callback, $pre_template_callback);
    }

	public function standard_where_callback(PerchQuery $Query)
    {
    	$Query->where[] = $this->deleted_date_column . ' IS NULL';
    	$Query->where[] = 'productStatus=1';
    	$Query->where[] = 'parentID IS NULL';
		
        return $Query;
    }

	public function get_for_admin_listing($Paging=false)
	{
		if ($Paging && $Paging->enabled()) {
            $sql = $Paging->select_sql();
        }else{
            $sql = 'SELECT';
        }

        $sql .= ' *
                FROM ' . $this->table .'
                WHERE parentID IS NULL AND productDeleted IS NULL ';

        if (isset($this->default_sort_column)) {
            $sql .= ' ORDER BY ' . $this->default_sort_column . ' '.$this->default_sort_direction;
        }

        if ($Paging && $Paging->enabled()) {
            $sql .=  ' '.$Paging->limit_sql();
        }

        $results = $this->db->get_rows($sql);

        if ($Paging && $Paging->enabled()) {
            $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
        }

        return $this->return_instances($results);
	}


	/**
	 * Get products in a category - used by the product field type
	 */
	public function get_by_category($catSlug)
	{
		return $this->get_filtered_listing([
			'category' => $catSlug,
			'return-objects' => true,
			'sort' => 'sku',
			'sort-order' => 'ASC', 
			]);
	}

	public function get_for_order($orderID)
	{
		$sql = 'SELECT p.*, oi.itemQty
				FROM '.PERCH_DB_PREFIX.'shop_order_items oi, '.$this->table.' p
				WHERE 
					oi.productID = p.productID
					AND oi.orderID='.$this->db->pdb($orderID);

		return $this->return_instances($this->db->get_rows($sql));
	}

	public function runtime_pretemplate_callback($items, $opts=null)
	{
		$Shop       = PerchShop_Runtime::fetch();
		$is_regular = true;
		$is_sale    = $Shop->sale_enabled();
		$is_trade   = $Shop->trade_enabled();

		if ($is_sale || $is_trade) $is_regular = false;

		$get_variants = false;

		if (is_array($opts) && isset($opts['variants']) && $opts['variants']) {
			$get_variants = true;
			$variants_cache = $this->get_variants_for_set($items);
		}

		foreach($items as &$item) {
			$item['sale_pricing']    = $is_sale;
			$item['trade_pricing']   = $is_trade;
			$item['regular_pricing'] = $is_regular;

			if ($get_variants) {
				if (isset($variants_cache[$item['productID']])) {
					$item['variants'] = $variants_cache[$item['productID']];
				} else {
					$item['variants'] = [];
				}
			}
		}
		return $items;
	}

	public function find_from_options($productID, $options)
	{
		$KnownProduct = $this->find($productID);
		if ($KnownProduct && $KnownProduct->is_variant()) {
			$KnownProduct = $KnownProduct->get_parent();
		}

		if ($KnownProduct) {


			$sql = 'SELECT valueSKUCode FROM '.PERCH_DB_PREFIX.'shop_option_values
					WHERE valueID IN ('.$this->db->implode_for_sql_in($this->array_flatten($options)).') AND
						optionID IN ('.$this->db->implode_for_sql_in(array_keys($options)).')';
			$skus = $this->db->get_rows_flat($sql);

			$sql = 'SELECT productID FROM '.$this->table.'
					WHERE parentID='.$this->db->pdb((int)$KnownProduct->id()).' AND productDeleted IS NULL AND ';

			$opts = [];

			foreach($skus as $sku) {
				$opts[] = '(sku REGEXP "[[:<:]]'.($sku).'[[:>:]]")';
			}
			$sql .= implode(' AND ', $opts);

			$sql .= ' LIMIT 1';

			return $this->return_instance($this->db->get_row($sql));
		}

		return null;
	}

	public function get_product_variants($productID)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE parentID='.$this->db->pdb((int)$productID).' AND productDeleted IS NULL';
		return $this->return_instances($this->db->get_rows($sql));
	}

	private function get_variants_for_set($items) 
	{
		if (PerchUtil::count($items)) {
			$ids = [];
			foreach($items as $item) {
				$ids[] = $item['productID'];
			}

			$sql = 'SELECT * FROM '.$this->table.' WHERE parentID IN ('.$this->db->implode_for_sql_in($ids).') AND productDeleted IS NULL';
			$products = $this->return_instances($this->db->get_rows($sql));

			$result = [];
			if (PerchUtil::count($products)) {
				foreach($products as $Product) {
					if (!isset($result[$Product->parentID()])) $result[$Product->parentID()] = [];
					$result[$Product->parentID()][] = $Product;
				}
			}

			return $result;
		}

		return [];
	}

	private function array_flatten($arr) 
	{
	    $arr = array_values($arr);
	    while (list($k,$v)=each($arr)) {
	        if (is_array($v)) {
	            array_splice($arr,$k,1,$v);
	            next($arr);
	        }
	    }
	    return $arr;
	}



	

}




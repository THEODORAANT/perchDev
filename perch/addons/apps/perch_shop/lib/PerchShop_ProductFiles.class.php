<?php

class PerchShop_ProductFiles extends PerchAPI_Factory
{
    protected $table               = 'shop_product_files';
    protected $pk                  = 'fileID';
    protected $singular_classname  = 'PerchShop_ProductFile';

    protected $index_table         = 'shop_index';
    protected $namespace           = 'file';

    protected $event_prefix        = 'shop.productfile';

    protected $default_sort_column = 'fileOrder';

	public $static_fields          = array('fileID', 'productID', 'fileTitle', 'fileSlug', 'resourceID', 'fileDynamicFields', 'fileOrder');



	public function get_by_product_for_admin($productID)
	{
		$sql = 'SELECT pf.*, r.* FROM '.$this->table.' pf, '.PERCH_DB_PREFIX.'resources r
				WHERE pf.resourceID=r.resourceID
					AND pf.productID='.$this->db->pdb($productID);
		$rows = $this->db->get_rows($sql);

		return $this->return_instances($rows);
	}

	public function get_for_customer($Customer, $opts)
	{
		$Statuses = new PerchShop_OrderStatuses($this->api);
		$sql = 'SELECT pf.*, p.*
				FROM '.$this->table.' pf, '.PERCH_DB_PREFIX.'shop_products p
				WHERE pf.productID=p.productID
					AND p.productID IN (
							SELECT oi.productID FROM '.PERCH_DB_PREFIX.'shop_order_items oi
								JOIN '.PERCH_DB_PREFIX.'shop_orders o ON oi.orderID=o.orderID
								WHERE o.orderStatus IN ('.$this->db->implode_for_sql_in($Statuses->get_status_and_above('paid')).')
									AND o.customerID='.$this->db->pdb($Customer->id());

		if ($opts['order']) {
			$sql .= ' AND o.orderID='.$this->db->pdb($opts['order']);
		}

		$sql .= ')
				ORDER BY p.productID ASC';

		$rows = $this->db->get_rows($sql);

		$files = $this->return_instances($rows);

		if ($opts['skip-template']) {
			return $files;
		}

		if ($files) {
			$Template = $this->api->get('Template');
			$Template->set('shop/'.$opts['template'], 'shop');
			$out = $Template->render_group($files, true);
			return $out;
		}

		return false;
	}

	public function customer_has_purchased_file($Customer, $fileID)
	{
		$Statuses = new PerchShop_OrderStatuses($this->api);
		$sql = 'SELECT COUNT(oi.productID)
				FROM '.PERCH_DB_PREFIX.'shop_order_items oi
					JOIN '.PERCH_DB_PREFIX.'shop_orders o ON oi.orderID=o.orderID
					JOIN '.PERCH_DB_PREFIX.'shop_product_files f ON oi.productID=f.productID
					WHERE o.orderStatus IN ('.$this->db->implode_for_sql_in($Statuses->get_status_and_above('paid')).')
						AND o.customerID='.$this->db->pdb($Customer->id()).'
						AND f.fileID='.$this->db->pdb($fileID);
		$count = $this->db->get_count($sql);

		return $count > 0;
	}

	public function get_file_path_and_bucket($fileID)
	{
		$sql = 'SELECT r.resourceFile, r.resourceBucket
				FROM '.PERCH_DB_PREFIX.'resources r
					JOIN '.PERCH_DB_PREFIX.'shop_product_files pf ON pf.resourceID=r.resourceID
				WHERE pf.fileID='.$this->db->pdb($fileID).'
				LIMIT 1';
		$row = $this->db->get_row($sql);
		if (PerchUtil::count($row)) {
			return [
				$row['resourceFile'],
				$row['resourceBucket'],
			];
		}
		return [null, null];
	}

}

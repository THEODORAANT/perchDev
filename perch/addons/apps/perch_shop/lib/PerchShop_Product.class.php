<?php

class PerchShop_Product extends PerchShop_Base
{
	protected $factory_classname = 'PerchShop_Products';
	protected $table             = 'shop_products';
	protected $pk                = 'productID';
	protected $index_table       = 'shop_index';

	protected $modified_date_column = 'productUpdated';
    public $deleted_date_column  = 'productDeleted';

	protected $duplicate_fields  = [
                                    'productStockOnParent' => 'stock_location',
                                    'productSlug'          => 'slug',
                                    'productStatus'        => 'status',
									];

    protected $exclude_from_api  = ['regular_pricing', 'current_price', 'productStatus', 'productCreated', 'productUpdated', 'productDeleted', 'productTemplate', 'productOrder', 'stock_location', 'productHasVariants', 'productStockOnParent', 'catalog_only', ];

    protected $event_prefix = 'shop.product';

    public function delete()
    {
        // delete variants
        if ($this->has_variants()) {
            $variants = $this->get_variants();

            if (PerchUtil::count($variants)) {
                foreach($variants as $Product) {
                    $Product->delete();
                }
            }
        }

        $recount_parent = false;
        $Parent = null;

        if ($this->is_variant()) {
            $recount_parent = true;
            $Parent = $this->get_parent();
        }

        // then yourself
        $result = parent::delete();

        if ($recount_parent) {
            $Parent->update_variant_count();
        }

        return $result;
    }

	public function productTitle()
	{
		return $this->details['title'];
	}

    public function is_variant()
    {
        return $this->details['parentID']>0;
    }

    public function has_variants()
    {
        return $this->details['productHasVariants']>0;
    }

    public function update($data)
    {
        if ($this->has_variants()) {
            if (isset($data['productDynamicFields'])) {
                $fields = PerchUtil::json_safe_decode($data['productDynamicFields'], true);
                if (isset($fields['stock_location'])) {
                    $this->update_variants(['stock_location'=>$fields['stock_location']]);
                }
            }    
        }
        
        return parent::update($data);
    }

    public function to_array()
    {
        $child = parent::to_array();

        if ($this->is_variant()) {
            $Parent = $this->get_parent();
            $Parent->prefix_vars = $this->prefix_vars;
            $parent = $Parent->to_array();
            $out    = array_merge($parent, $child);
        }else{
            $out = $child;
        }

        if ($this->has_variants()) {
            $out['has_variants'] = true;
            $out['_variant_opts'] = $this->get_variant_select_opts();

            $out['options'] = $this->get_variant_opts();
        }

        if (isset($out['regular_pricing'])) {
            $out['current_price'] = $out['price'];

            if ($out['on_sale'] || $out['sale_pricing']) {
                $out['current_price'] = $out['sale_price'];
            }   

            if ($out['trade_pricing']) {
                $out['current_price'] = $out['trade_price'];
            }    
        }
        return $out;
    }

    public function to_array_for_api()
    {
        $this->prefix_vars = false;
        $child = parent::to_array_for_api();

        if ($this->is_variant()) {
            $Parent = $this->get_parent();
            $Parent->prefix_vars = false;
            $parent = $Parent->to_array_for_api();
            $out    = array_merge($parent, $child);
        }else{
            $out = $child;
        }

        if (isset($out['regular_pricing'])) {
            $out['current_price'] = $out['price'];

            if ($out['on_sale'] || $out['sale_pricing']) {
                $out['current_price'] = $out['sale_price'];
            }   

            if ($out['trade_pricing']) {
                $out['current_price'] = $out['trade_price'];
            }    
        }
        
        return $out;
    }

    public function get_variant_opts()
    {
        $Options = new PerchShop_Options($this->api);
        return $Options->get_for_product_template($this->id());
    }


    public function get_variant_select_opts()
    {
        $sql = 'SELECT productID, productVariantDesc, stock_level FROM '.$this->table.'
                WHERE parentID='.$this->db->pdb((int)$this->id()).' AND productDeleted IS NULL
                ORDER BY productOrder ASC';
        $rows = $this->db->get_rows($sql);

        if (PerchUtil::count($rows)) {
            $opts = [];

            $Lang = $this->api->get('Lang');

            $parent_stock = $this->get_stock_level();

            foreach($rows as $row) {

                $stock = $row['stock_level'];
                if ($stock === null) {
                    $stock = INF;
                }

                if ($this->productStockOnParent()) {
                    $stock = $parent_stock;
                }

                $opt = '';

                #PerchUtil::debug($stock);
                if ($stock!==INF && (int)$stock==0) {
                    $opt .= '!';
                }

                $opt .= str_replace(',', '\,', $row['productVariantDesc']);
                
                if ($stock != INF) {
                    if (!$this->productStockOnParent() || $stock===0) {
                        $opt .= ' ' . $Lang->get('(%d in stock)', $stock);
                    }    
                }
                
                $opts[] = $opt.'|'.$row['productID'];
            }
            return implode(',', $opts);
        }
        return '';
    }

    public function get_parent()
    {
        $Parents = $this->get_factory();
        return $Parents->find((int)$this->parentID());
    }

    public function set_options(array $optionIDs)
    {
        $sql = 'DELETE FROM '.PERCH_DB_PREFIX.'shop_product_options WHERE productID='.$this->db->pdb((int)$this->id());
        $this->db->execute($sql);

        if (PerchUtil::count($optionIDs)) {
            foreach($optionIDs as $id) {
                $this->db->insert(PERCH_DB_PREFIX.'shop_product_options', [
                        'productID' => $this->id(),
                        'optionID' => $id,
                    ]);
            }
        }
    }

    public function set_option_values($data)
    {
    	$sql = 'DELETE FROM '.PERCH_DB_PREFIX.'shop_product_option_values WHERE productID='.$this->db->pdb((int)$this->id());
        $this->db->execute($sql);

        if (PerchUtil::count($data)) {
            foreach($data as $datum) {
                $this->db->insert(PERCH_DB_PREFIX.'shop_product_option_values', [
                        'productID' => $this->id(),
                        'optionID'  => $datum['optionID'],
                        'valueID'   => $datum['valueID'],
                    ]);
            }
        }

        $sql = 'DELETE FROM '.PERCH_DB_PREFIX.'shop_product_option_values 
        		WHERE productID='.$this->db->pdb((int)$this->id()).' AND optionID NOT IN (
        				SELECT optionID FROM '.PERCH_DB_PREFIX.'shop_product_options 
        				WHERE productID='.$this->db->pdb((int)$this->id()).'
        			)';
        $this->db->execute($sql);
    }

    public function generate_variants()
    {
    	$Factory = new PerchShop_VariantFactory($this->api);
    	$Factory->generate_for_product($this->id());
    }

    public function get_stock_level()
    {
        $StockProduct = $this->_get_stockkeeping_product();
        if ($StockProduct) {
            if ($StockProduct->has_unlimited_stock()) {
                return INF;
            }
            return (int)$StockProduct->stock_level();    
        }
        return null;
    }

	public function update_stock_level($adjustment=0)
	{
        $StockProduct = $this->_get_stockkeeping_product();

        if (!$StockProduct) return null;

        $stock_level = (int)$StockProduct->stock_level() + $adjustment;

        if ($stock_level < 1) $stock_level = 0;

        $StockProduct->update([
            'stock_level' => $stock_level,
            ]);

        $Perch = Perch::fetch();
        $Perch->event('shop.product_stock_update', $StockProduct);
	}

    public function has_unlimited_stock()
    {
        $status = (int)$this->get('stock_status');
        if ($status < 1) return true;

        return false;
    }

    private function _get_stockkeeping_product()
    {

        if ($this->productStockOnParent() && $this->is_variant()) {
            // stock on parent, and this is a variant, so return the parent.
            return $this->get_parent();
        }

        // This is the parent, or is the variant and stock is on itself. Or something unforeseen.
        return $this;
    }

    public function get_tax_group()
    {
        $TaxGroups = new PerchShop_TaxGroups($this->api);
        if ($this->get('tax_group')) {
            return $TaxGroups->find((int)$this->get('tax_group'));
        }else{
            if ($this->is_variant()) {
                $Parent = $this->get_parent();
                return $Parent->get_tax_group();
            }
        }
        return false;
    }

    public function get_admin_display_prices()
    {
        $prices = $this->price();
        if (PerchUtil::count($prices)) {
            if (isset($prices['_default'])) unset($prices['_default']);    

            $Currencies = new PerchShop_Currencies($this->api);

            $out = [];

            foreach($prices as $currencyID=>$price) {
                $Currency = $Currencies->find((int)$currencyID);
                if ($Currency) {
                    $out[] = $Currency->get_formatted($price);
                }
            }

            return implode(', ', $out);
        }
        
        return '-';
    }


    public function get_prices($qty=1, $pricing='standard', $price_tax_mode='exc', PerchShop_TaxLocation $CustomerTaxLocation=null, PerchShop_TaxLocation $HomeTaxLocation=null, PerchShop_Currency $Currency=null, PerchShop_CartTotaliser &$Totaliser=null, $customer_pays_tax=true)
    {
        $qty = (int)$qty;

        $prices_tax_inclusive = ($price_tax_mode=='inc');

        // Which price field do we need?
        switch($pricing) {
            case 'sale':
                $price_field = 'sale_price';
                break;

            case 'trade':
                $price_field = 'trade_price';
                if (!PERCH_RUNWAY) $price_field = 'price';
                break;

            default:
                $price_field = 'price';

                if ($this->get_property('on_sale')) {
                    $price_field = 'sale_price';
                }

                break;
        }

        $prices = $this->get($price_field);

        // Variant with no different price? Return the parent.
        if (!$prices && $this->is_variant()) {
            $Parent = $this->get_parent();
            return $Parent->get_prices($qty, $pricing, $price_tax_mode, $CustomerTaxLocation, $HomeTaxLocation, $Currency, $Totaliser);
        }

        if ($prices) {

            $data = [];

            $TaxRates = new PerchShop_TaxRates($this->api);

            if (isset($prices[$Currency->id()])) {
                $base_price = floatval($prices[$Currency->id()]);

                // Whos tax rate do we use?
                $TaxGroup = $this->get_tax_group();

                // Always get the home tax rate
                $home_tax_rate = $TaxRates->get_rate_for_location((int)$TaxGroup->id(), (int)$HomeTaxLocation->id());

                if ($TaxGroup->groupTaxRate()=='buyer') {
                    $TaxLocation = $CustomerTaxLocation;
                }else{
                    $TaxLocation = $HomeTaxLocation;
                }

                // Rate to charge based on buyer location decision
                $tax_rate = $TaxRates->get_rate_for_location((int)$TaxGroup->id(), (int)$TaxLocation->id());

                // Calculate exclusive price
                if ($prices_tax_inclusive) {
                    $exclusive_price = $base_price / (1 + $home_tax_rate/100);
                }else{
                    $exclusive_price = $base_price;
                }

                // Calculate inclusive price using buyer's tax rate
                $inclusive_price = $exclusive_price * (1 + $tax_rate/100);

                $Totaliser->add_to_items($exclusive_price*$qty, $tax_rate);

                if ($customer_pays_tax) {
                    $Totaliser->add_to_tax(($inclusive_price - $exclusive_price)*$qty, $tax_rate);
                }

                if (!$customer_pays_tax) {
                    $inclusive_price = $exclusive_price;
                }

                $data['price_without_tax']           = $Currency->format_numeric($exclusive_price);
                $data['price_without_tax_formatted'] = $Currency->format_display($exclusive_price);

                $data['total_without_tax']           = $Currency->format_numeric($exclusive_price*$qty);
                $data['total_without_tax_formatted'] = $Currency->format_display($exclusive_price*$qty);

                $data['tax']                         = $Currency->format_numeric($inclusive_price - $exclusive_price);
                $data['tax_formatted']               = $Currency->format_display($inclusive_price - $exclusive_price);

                $data['total_tax']                   = $Currency->format_numeric(($inclusive_price - $exclusive_price)*$qty);
                $data['total_tax_formatted']         = $Currency->format_display(($inclusive_price - $exclusive_price)*$qty);

                $data['tax_rate']                    = $tax_rate;

                $data['price_with_tax']              = $Currency->format_numeric($inclusive_price);
                $data['price_with_tax_formatted']    = $Currency->format_display($inclusive_price);

                $data['total_with_tax']              = $Currency->format_numeric($inclusive_price*$qty);
                $data['total_with_tax_formatted']    = $Currency->format_display($inclusive_price*$qty);

                $data['discount']                    = 0;
                $data['tax_discount']                = 0;

                ksort($data);

                return $data;
            }
        }

    }

    public function get_weight_and_totalise_shipping($qty=1, PerchShop_CartTotaliser &$Totaliser=null)
    {
        $qty = (int)$qty;

        $Parent = null;
        if ($this->parentID()) {
            $Parent = $this->get_parent();
        }

        if ($this->get_property('requires_shipping', $Parent)) {
            $weight = $this->get_property('weight', $Parent);
            
            $Totaliser->add_to_weight($qty*$weight);

            $w = $this->get_property('width', $Parent);
            $h = $this->get_property('height', $Parent);
            $d = $this->get_property('depth', $Parent);

            $Totaliser->add_to_dimensions($w, $h, $d);

            $Totaliser->add_to_shippable_items(1);

            return $weight;    
        }

        return 0;

    }

    public function apply_tags_to_customer(PerchShop_Customer $Customer)
    {
        if ($this->is_variant()) {
            $Parent = $this->get_parent();
            if ($Parent) {
                $Parent->apply_tags_to_customer($Customer);
            }
        }

        $ProductTags = new PerchShop_ProductTags($this->api);
        $tags = $ProductTags->get_for_product($this->id());

        if ($tags) {
            $PerchMembers_Tags = new PerchMembers_Tags($this->api);

            foreach($tags as $Tag) {
                $MemberTag = $PerchMembers_Tags->find($Tag->tagID());

                if ($MemberTag) {
                    $MemberTag->add_to_member($Customer->memberID(), $Tag->tagExpiry());
                }
            }

            $Members = new PerchMembers_Members($this->api);
            $Member = $Members->find($Customer->memberID());

            if ($Member) {
                $Perch = Perch::fetch();
                $Perch->event('shop.member_update', $Member);    
            }

            
        }
    }

    public function update_search_text($text)
    {
        $table = PERCH_DB_PREFIX.'shop_search';
        $sql = 'DELETE FROM '.$table.' WHERE itemKey='.$this->db->pdb((int)$this->id()).' AND itemType='.$this->db->pdb('product');
        $this->db->execute($sql);

        $this->db->insert($table, [
                'itemKey'    => (int)$this->id(),
                'itemType'   => 'product',
                'searchBody' => $text
            ]);
    }

    public function update_variant_count()
    {
        $products = $this->get_variants();

        if (PerchUtil::count($products)) {
            $val = 1;
        } else {
            $val = 0;
        }

        $this->update([
                    'productHasVariants' => $val,
                ]);
    }

   public function update_tree_position($parentID=false, $order=false)
    {
        //PerchUtil::debug(sprintf('Update catID %s to parentID %s and order %s', $this->id(), $parentID, $order), 'notice');

        $data = array();

        if ($parentID !== false) {
            if ($parentID === null) {
                $data['parentID'] = null;
            } else {
                $data['parentID'] = $parentID;
            }
        } else {
            $data['parentID'] = $this->parentID();
        }

        if ($order) {
            $data['productOrder'] = $order;
        }else{
            $data['productOrder'] = $this->find_next_child_order($data['parentID']);
        }

      /*  if ($data['parentID'] == 'null') {
            $data['parentID'] = '0';
        }*/

        if (count($data)) $this->update($data);
    }

    public function get_property($prop, PerchShop_Product $Parent=null)
    {
        if ($this->parentID() == null) {
            return $this->get($prop);
        }

        if (isset($this->details[$prop])) {
            return $this->details[$prop];
        }else{
            $array = $this->to_array();
            if (isset($array[$prop])){
                return $array[$prop];
            }
            if (!$Parent) {
                $Parent = $this->get_parent();    
            }

            return $Parent->get_property($prop);
        }

        return false;
    }


    private function update_variants($data) 
    {
        $products = $this->get_variants();

        if (PerchUtil::count($products)) {
            foreach($products as $Product) {
                $Product->intelliupdate($data);
            }
        }
    }

    private function get_variants()
    {
        $Products =  $this->get_factory();
        return $Products->get_product_variants($this->id());
    }

    

}

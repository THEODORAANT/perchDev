<?php


class PerchShop_FieldType extends PerchFieldType
{
	public function get_value($details, $id=null)
	{
		if ($id) {
			$tagID = $id;
		}else{
			$tagID = $this->Tag->id();
		}


		if (isset($details[$tagID])) {
			$val = $details[$tagID];
			if (is_array($val) && isset($val['data']['key'])) {
				$details[$tagID] = $val['data']['key'];
			}
			if (is_array($val) && isset($val['data']['id'])) {
				$details[$tagID] = $val['data']['id'];
			}
		}

		return $this->Form->get($details, $tagID, $this->Tag->default(), $this->Tag->post_prefix());
	}

	public function get_search_text($raw=false)
	{
	    if ($raw===false) $raw = $this->get_raw();

	    if (is_array($raw)) {
	    	///PerchUtil::debug($raw, 'error');
	    	return '';
	    }

	    return $raw;
	}

}

class PerchShop_FieldType_API_Lookup extends PerchShop_FieldType
{
	protected $class;

	public function render_inputs($details=array())
	{
		$API  = new PerchAPI(1.0, 'perch_shop');
		$Items = new $this->class($API);
		$items = $Items->all();

		$mode = 'select';

        if ($this->Tag->display_as() && $this->Tag->display_as()=='checkboxes') {
            $mode = 'checkboxes';
        }

       	if ($mode == 'select') {
       		$opts = array();
			if (PerchUtil::bool_val($this->Tag->allowempty())== true) {
			    $opts[] = array('label'=>'', 'value'=>'');
			}

			if (PerchUtil::count($items)) {
				foreach($items as $Item) {
					$opts[] = array('label'=>$Item->title(), 'value'=>$Item->id());
				}
			}

			return $this->Form->select($this->Tag->input_id(), $opts, $this->get_value($details));
       	}

       	if ($mode == 'checkboxes') {
       		$opts = array();

			if (PerchUtil::count($items)) {
				foreach($items as $Item) {
					$opts[] = array('label'=>$Item->title(), 'value'=>$Item->id());
				}
			}

			$multicol = 'fieldtype';
	        if (PerchUtil::count($opts) > 4) {
	            $multicol .= ' multi-col';
	        }else{
	            $multicol .= ' uni-col';
	        }

	        return $this->Form->checkbox_set($this->Tag->input_id(), false, $opts, $this->Form->get($details, $this->Tag->id(), $this->Tag->default(), $this->Tag->post_prefix()), false, false, $multicol);

       	}

	}

	public function get_raw($post=false, $Item=false)
    {
        if ($post===false) {
            $post = $_POST;
        }

        $id = $this->Tag->id();
        if (isset($post[$id])) {

            $this->raw_item = $post[$id];
            return $this->raw_item;
        }

        return null;
    }

    public function get_processed($raw=false)
	{
		$API  = new PerchAPI(1.0, 'perch_shop');
		$Items = new $this->class($API);

		if (!is_array($raw)) {
			$Item = $Items->find($raw);

			if ($this->Tag->output()) {
				$field = $this->Tag->output();
				return $Item->$field();
			}

			return $Item->title();
		}else{
			$out = [];
			foreach($raw as $item) {
				$Item = $Items->find($item);

				if ($this->Tag->output()) {
					$field = $this->Tag->output();
					$out[] = $Item->$field();
				} else {
					$out[] = $Item->title();
				}
			}
			return implode(', ', $out);
		}

		return $raw;
	}

	public function get_index($raw=false)
	{
		if ($raw===false) $raw = $this->get_raw();
		
		$id    = $this->Tag->id();
		
		$out   = [];
		$Item  = false;
		
		$API   = new PerchAPI(1.0, 'perch_shop');
		$Items = new $this->class($API);
		

        if (is_array($raw) && PerchUtil::count($raw)) {
        	foreach($raw as $val) {
        		$out[] = array('key'=>$id, 'value'=>$val);
        	}
        } else {
        	$raw = trim($raw);
        	$out[] = array('key'=>$id, 'value'=>$raw);
        	$Item = $Items->find($raw);
        }

		if ($Item) {
			$raw = $Item->to_array();
			if (is_array($raw)) {

	            foreach($raw as $key=>$val) {
	                if (!is_array($val) && strpos($key, 'perch_')===false && strpos($key, 'DynamicFields')===false) {
	                if($val!=null){
	                $out[] = array('key'=>$id.'.'.$key, 'value'=>trim($val));
	                }

	                }
	            }

	        }
		}
		return $out;
	}

	public function get_search_text($raw=false)
    {
        if ($raw===false) $raw = $this->get_raw();
		
		$id    = $this->Tag->id();
		
		$out   = [];
		$Item  = false;
		
		$API   = new PerchAPI(1.0, 'perch_shop');
		$Items = new $this->class($API);
		

        if (is_array($raw) && PerchUtil::count($raw)) {
        	foreach($raw as $val) {
        		$out[] = $val;
        	}
        } else {
        	$raw = trim($raw);
        	$out[] = $raw;
        	$Item = $Items->find($raw);
        }

		if ($Item) {
			$raw = $Item->to_array();
			if (is_array($raw)) {

	            foreach($raw as $key=>$val) {
	                if (!is_array($val) &&  $val!=null &&strpos($key, 'perch_')===false && strpos($key, 'DynamicFields')===false) {
	                    $out[] = trim($val);
	                }
	            }

	        }
		}
		return implode(' ', $out);
    }

	public function get_api_value($raw=false)
    {
        if ($raw===false) $raw = $this->get_raw();

        $API   = new PerchAPI(1.0, 'perch_shop');
		$Items = new $this->class($API);

        if (is_array($raw) && count($raw)) {
            $out = array();
            foreach($raw as $itemID) {
                $Item = $Items->find((int)$itemID);
                $out[] = $Item->to_array_for_api();
            }

            return $out;

        } else {
        	$Item = $Items->find((int)$raw);
        	return $Item->to_array_for_api();
        }

        return $raw;
    }
}

class PerchShop_FieldType_bool extends PerchShop_FieldType
{

	protected $positive = 'Yes';
	protected $negative = 'No';

	public function render_inputs($details=array())
	{
		$API  = new PerchAPI(1.0, 'perch_shop');
		$Lang = $API->get('Lang');

		$opts = array();
		$opts[] = array('label'=>$Lang->get($this->negative), 'value'=>'0');
		$opts[] = array('label'=>$Lang->get($this->positive),  'value'=>'1');

		return $this->Form->select($this->Tag->input_id(), $opts, $this->get_value($details));

	}

}

/* ------------ COUNTRY ------------ */

class PerchFieldType_shop_country extends PerchShop_FieldType_API_Lookup
{
	protected $class = 'PerchShop_Countries';
}


/* ------------ BRAND ------------ */

class PerchFieldType_shop_brand extends PerchShop_FieldType_API_Lookup
{
	protected $class = 'PerchShop_Brands';
}

/* ------------ TAX BAND ------------ */

class PerchFieldType_shop_tax_band extends PerchShop_FieldType_API_Lookup
{
	protected $class = 'PerchShop_TaxGroups';
}

/* ------------ TAX GROUP ------------ */

class PerchFieldType_shop_tax_group extends PerchShop_FieldType_API_Lookup
{
	protected $class = 'PerchShop_TaxGroups';
}

/* ------------ COLLECTION ------------ */

class PerchFieldType_shop_collection extends PerchShop_FieldType_API_Lookup
{
	protected $class = 'PerchShop_Collections';
}

/* ------------ SHIPPING ------------ */

class PerchFieldType_shop_shipping extends PerchShop_FieldType_API_Lookup
{
	protected $class = 'PerchShop_Shippings';
}

/* ------------ ORDER STATUS ------------ */

class PerchFieldType_shop_order_status extends PerchShop_FieldType_API_Lookup
{
	protected $class = 'PerchShop_OrderStatuses';
}


/* ------------ STATUS (LIVE / DRAFT) ------------ */

class PerchFieldType_shop_status extends PerchShop_FieldType_bool
{

	protected $positive = 'Active';
	protected $negative = 'Inactive';

}

/* ------------ REQUIRES SHIPPING ------------ */

class PerchFieldType_shop_requires_shipping extends PerchShop_FieldType_bool
{

}

/* ------------ CATALOG ONLY ------------ */

class PerchFieldType_shop_catalog_only extends PerchShop_FieldType_bool
{

}


/* ------------ STANDARD BOOL ------------ */

class PerchFieldType_shop_bool extends PerchShop_FieldType_bool
{

}

/* ------------ STOCK STATUS ------------ */

class PerchFieldType_shop_stock_status extends PerchShop_FieldType
{
	private $data = [
		'0' => 'Unlimited',
		'1' => 'In Stock',
		'2' => 'Low Stock',
		'3' => 'Out of Stock',
		'4' => 'More Stock Ordered',
		'5' => 'Discontinued',
	];


	public function render_inputs($details=array())
	{
		$API  = new PerchAPI(1.0, 'perch_shop');
		$Lang = $API->get('Lang');

		$opts = array();
		foreach($this->data as $value=>$label) {
			$opts[] = array('label'=>$Lang->get($label), 'value'=>(string)$value);
		}

		return $this->Form->select($this->Tag->input_id(), $opts, $this->get_value($details));
	}

	public function get_processed($raw=false)
	{
		if ($this->Tag->output()=='code') {
			return $raw;
		}

		if (array_key_exists($raw, $this->data)) {
			$API  = new PerchAPI(1.0, 'perch_shop');
			$Lang = $API->get('Lang');
			return $Lang->get($this->data[$raw]);
		}

		return $raw;
	}

}

/* ------------ STOCKLOCATION (LIVE / DRAFT) ------------ */

class PerchFieldType_shop_stock_location extends PerchShop_FieldType_bool
{

	protected $positive = 'Centrally for the product';
	protected $negative = 'On individual variants';

}


/* ------------ VARIATION PRICE MODIFIER ------------ */

class PerchFieldType_shop_price_mod extends PerchShop_FieldType
{

	public function render_inputs($details=array())
	{
		$s = '';

		$opts = array();
		$opts[] = array('label'=>'+',	'value'=>'+');
		$opts[] = array('label'=>'-',	'value'=>'-');
		$opts[] = array('label'=>'=',	'value'=>'=');

		//PerchUtil::debug($details);

		$source = [];
		if (isset($details[$this->Tag->input_id()])) {
			$source = $details[$this->Tag->input_id()];
		}

		$s .= $this->Form->select($this->Tag->input_id().'_operator', $opts, $this->get_value($source, 'operator')).' ';
		$id = 'val';
		$s .= $this->Form->text($this->Tag->input_id().'_val', $this->Form->get($source, $id, $this->Tag->default(), $this->Tag->post_prefix()), $this->Tag->size(), $this->Tag->maxlength());

		$s .= $this->Form->hidden($this->Tag->input_id().'_id', $this->get_value($source, 'id'));

		return $s;

	}

	public function get_raw($post=false, $Item=false)
    {
        if ($post===false) {
            $post = $_POST;
        }

        $id = $this->Tag->id();
        if (isset($post[$id.'_val'])) {

        	$tmp = array();

            $fields = array('operator', 'val', 'id');
            foreach($fields as $field) {
                if (isset($post[$this->Tag->id().'_'.$field]) && $post[$this->Tag->id().'_'.$field]!=''){
                    $tmp[$field] = $post[$this->Tag->id().'_'.$field];
                }
            }

            $this->raw_item = $tmp;
            return $this->raw_item;
        }

        return null;
    }

}

/* ------------ MODIFIER TYPE ------------ */

class PerchFieldType_shop_modifier_type extends PerchShop_FieldType
{

	public function render_inputs($details=array())
	{
		$API  = new PerchAPI(1.0, 'perch_shop');
		$Lang = $API->get('Lang');

		$opts = array();
		$opts[] = array('label'=>$Lang->get('Variant'), 'value'=>'variant');
		$opts[] = array('label'=>$Lang->get('Single'), 	'value'=>'single');
		$opts[] = array('label'=>$Lang->get('Input'),   'value'=>'input');


		return $this->Form->select($this->Tag->input_id(), $opts, $this->get_value($details));

	}

}

/* ------------ TEXTAREA ------------ */

class PerchFieldType_shop_textarea extends PerchFieldType_textarea
{
	public function render_inputs($details=array())
	{
	    $classname = 'small autowidth';
	    if ($this->Tag->editor())     $classname .= ' '.$this->Tag->editor();
	    if ($this->Tag->size())       $classname .= ' '.$this->Tag->size();

	    $data_atrs = array();
	    if ($this->Tag->count()) {
	        if ($this->Tag->count()=='chars') $data_atrs['count'] = 'chars';
	        if ($this->Tag->count()=='words') $data_atrs['count'] = 'words';
	        $data_atrs['count-container'] = $this->Tag->input_id().'__count';
	    }

	    $s = $this->Form->textarea($this->Tag->input_id(), $this->Form->get($details, $this->Tag->id(), $this->Tag->default(), $this->Tag->post_prefix()), $classname, $data_atrs);
	    $s .= '<div class="clear"></div>';
	    if ($this->Tag->count()) {
	        $s .= '<div class="counter textarea" id="'.$this->Tag->input_id().'__count"></div>';
	    }

	    return $s;
	}

	public function get_raw($post=false, $Item=false)
	{
	    if ($post===false) {
	        $post = $_POST;
	    }

	    $id = $this->Tag->id();
	    if (isset($post[$id])) {
	        return trim(PerchUtil::safe_stripslashes($post[$id]));
	    }
	    return null;
	}

	public function get_processed($raw=false)
	{
	    if ($raw===false) $raw = $this->get_raw();
	    return $raw;
	}

	public function get_search_text($raw=false)
	{
	    if ($raw===false) $raw = $this->get_raw();
	    return $raw;
	}
}

/* ------------ CATEGORY ------------ */

class PerchFieldType_shop_category extends PerchShop_FieldType_API_Lookup
{
	protected $class = 'PerchShop_Categories';

	public function add_page_resources()
	{
	    $Perch = Perch::fetch();

	    $Perch->add_javascript(PERCH_LOGINPATH.'/core/assets/js/chosen.jquery.min.js');
	    $Perch->add_javascript(PERCH_LOGINPATH.'/core/assets/js/categories.js');
	}

	public function render_inputs($details=array())
	{
		$API  = new PerchAPI(1.0, 'perch_shop');
		$Items = new $this->class($API);
		$items = $Items->all();

		$opts = array();
		if (PerchUtil::bool_val($this->Tag->allowempty())== true) {
			$opts[] = array('label'=>'', 'value'=>'');
		}

		if (PerchUtil::count($items)) {
			foreach($items as $Item) {
				$opts[] = array('label'=>$Item->title(), 'value'=>$Item->id());
			}
		}

		if (isset($details[$this->Tag->id()])) {
			$val = $details[$this->Tag->id()];
			if (is_array($val) && isset($val['data'])) {
				$tmp = array();
				foreach($val['data'] as $cat) {
					$tmp[] = $cat['id'];
				}
				$details[$this->Tag->id()] = $tmp;
			}
		}

		$attributes = $this->Tag->get_data_attribute_string();
		return $this->Form->select($this->Tag->input_id(), $opts, $this->get_value($details), 'categories', true, $attributes);
	}

	public function get_raw($post=false, $Item=false)
	{
	    if ($post===false) $post = $_POST;
	    $id = $this->Tag->id();
	    if (isset($post[$id])) {
	        $this->raw_item = $post[$id];
	        return $this->raw_item;
	    }
	    return null;
	}

	public function get_index($raw=false)
    {
        if ($raw===false) $raw = $this->get_raw();

        $id = $this->Tag->id();

        $out = array();

        if (is_array($raw)) {

        	$API = new PerchAPI(1.0, 'perch_shop');
    		$Categories = new PerchShop_Categories($API);

	        foreach($raw as $key=>$val) {
                if (!is_array($val)) {
                    $out[] = array('key'=>'_category', 'value'=>$Categories->find_category_path_from_shop_id($val));
                }
            }

        }


        return $out;
    }


}

class PerchFieldType_shop_product extends PerchFieldType_select
{
	public function render_inputs($details=array())
    {
    	$API = new PerchAPI(1.0, 'perch_shop');
    	$Products = new PerchShop_Products($API);

    	$products = $Products->get_by_category($this->Tag->category());

    	$opts = array();

    	if (PerchUtil::bool_val($this->Tag->allowempty())== true) {
            $opts[] = array('label'=>'', 'value'=>'');
        }

    	if (PerchUtil::count($products)) {
    		foreach($products as $Product) {
    			$opts[] = array('label'=>$Product->productTitle(), 'value'=>$Product->id());
    		}
    	}

        return $this->Form->select($this->Tag->input_id(), $opts, $this->Form->get($details, $this->Tag->id(), $this->Tag->default(), $this->Tag->post_prefix()));
    }

    public function get_processed($raw=false)
    {
        if ($raw===false) $raw = $this->get_raw();

        $value = $raw;

        if ($this->Tag->output() && $this->Tag->output()!='productID') {

        		$API = new PerchAPI(1.0, 'perch_shop');
    			$Products = new PerchShop_Products($API);

    			$Product = $Products->find($value);

    			if (!$Product) return $value;

                $details = $Product->to_array();

                if (isset($details[$this->Tag->output()])) {
                	return $details[$this->Tag->output()];
                }
        }

        return $value;
    }

}

/* ------------ PROMO ACTION ------------ */

class PerchFieldType_shop_promo_action extends PerchShop_FieldType
{

	public function render_inputs($details=array())
	{
		$API  = new PerchAPI(1.0, 'perch_shop');
		$Lang = $API->get('Lang');

		$opts = array();
		$opts[] = array('label'=>$Lang->get('Free shipping'), 				'value'=>'free_ship');
		#$opts[] = array('label'=>$Lang->get('Buy X get Y free'), 			'value'=>'buy_x_get_y_free');
		$opts[] = array('label'=>$Lang->get('Discount by fixed'), 			'value'=>'discount_by_fixed');
		$opts[] = array('label'=>$Lang->get('Discount by percent'), 		'value'=>'discount_by_percent');
		if (PERCH_RUNWAY) $opts[] = array('label'=>$Lang->get('Use sale price'), 'value'=>'use_sale_price');
		#$opts[] = array('label'=>$Lang->get('Discount to fixed'),  			'value'=>'discount_to_fixed');
		#$opts[] = array('label'=>$Lang->get('Discount subtotal by fixed'), 	'value'=>'discount_subtotal_fixed');
		#$opts[] = array('label'=>$Lang->get('Discount subtotal by percent'),'value'=>'discount_subtotal_percent');

		return $this->Form->select($this->Tag->input_id(), $opts, $this->get_value($details));

	}

}

/* ------------ REQUIREMENTS FORM ------------ */

class PerchFieldType_shop_requirements extends PerchShop_FieldType
{
	public function render_inputs($details=array())
	{
		$API  = new PerchAPI(1.0, 'perch_shop');
		$Lang = $API->get('Lang');

		$opts = array();

		$files = PerchUtil::get_dir_contents(PerchUtil::file_path(PERCH_TEMPLATE_PATH.'/shop/requirements'), false);

		if (PerchUtil::count($files)) {
			foreach($files as $file) {
				$opts[] = array('label'=>PerchUtil::filename($file),'value'=>$file);
			}
		}

		return $this->Form->select($this->Tag->input_id(), $opts, $this->get_value($details));

	}

	public function get_processed($raw=false)
    {
        if ($raw===false) $raw = $this->get_raw();

        $value = $raw;

        $file = PerchUtil::file_path('shop/requirements/'.$value);

   		$API = new PerchAPI(1.0, 'perch_shop');
   		$Template = $API->get('Template');
   		$Template->set($file, 'shop');
   		$html = $Template->render(['1'=>'1']);

   		$html = $Template->apply_runtime_post_processing($html);
   		$this->processed_output_is_markup = true;

        return $html;
    }
}


/* ------------ CURRENCY VALUE ------------ */

class PerchFieldType_shop_currency_value extends PerchShop_FieldType
{
	public function render_inputs($details=array())
    {
    	$API  = new PerchAPI(1.0, 'perch_shop');
    	$Lang = $API->get('Lang');
    	$Settings = $API->get('Settings');

    	$tax_mode = 'excluding tax';
    	if ($Settings->get('perch_shop_price_tax_mode')->val()=='inc') {
    		$tax_mode = 'including tax';
    	}
    	if (PERCH_RUNWAY && $this->Tag->id()=='trade_price') {
    		$tax_mode = 'excluding tax';
	    	if ($Settings->get('perch_shop_trade_price_tax_mode')->val()=='inc') {
	    		$tax_mode = 'including tax';
	    	}
    	}

    	if ($this->Tag->pretax()) {
    		$tax_mode = 'before tax';
    	}

        $attributes = '';
        $attrs = array();
        $search = array('min', 'max', 'step');

        foreach($search as $s) {
            if ($this->Tag->is_set($s)) $attrs[] = $s.'='.$this->Tag->$s;
        }

        $attributes = implode(' ', $attrs);

        $currencies = $this->get_currencies();

        $out = '';

        if (PerchUtil::count($currencies)) {
        	foreach($currencies as $Currency) {

        		$id = $this->Tag->input_id().'_curr'.$Currency->id();

        		$prev = $details;
        		if (isset($details[$this->Tag->input_id()])) $prev = $details[$this->Tag->input_id()];
        		//PerchUtil::debug($prev, 'success');

        		$out .= '<div class="field-wrap compact-set">';
        		$out .= $this->Form->text($id,
                                $this->Form->get($prev, $Currency->id(), $this->Tag->default(), $this->Tag->post_prefix()),
                                $this->Tag->size(),
                                $this->Tag->maxlength(),
                                'number input-simple',
                                $attributes);
        		$out .= ' '.$Currency->currencyCode();
        		$out .= '</div>';

        	}
        	$out .= $this->Form->translated_hint($Lang->get($tax_mode));
        }

        return $out;


        return $this->Form->text($this->Tag->input_id(),
                                $this->Form->get($details, $this->Tag->id(), $this->Tag->default(), $this->Tag->post_prefix()),
                                $this->Tag->size(),
                                $this->Tag->maxlength(),
                                'number',
                                $attributes);
    }

    public function get_raw($post=false, $Item=false)
    {
    	$data = [];

    	$currencies = $this->get_currencies();

    	if (PerchUtil::count($currencies)) {
    		foreach($currencies as $Currency) {
    			$id = $this->Tag->id().'_curr'.$Currency->id();
    			if (isset($post[$id])) {
    				$data[$Currency->id()] = $post[$id];

    				if ($Currency->is_default()) {
						$data['_default'] = $post[$id];
    				}
    			}
    		}
    	}

    	return $data;
    }

    public function get_processed($raw=false)
    {
        if ($raw===false) $raw = $this->get_raw();

        $value = $raw;

        $Shop = PerchShop_Runtime::fetch();

        $currencyID = $Shop->get_currency_id();

        if (isset($value[$currencyID])) {
        	if ($this->Tag->output == 'raw') {
        		return $value[$currencyID];
        	}

        	$Currency = $Shop->get_currency();
        	return $Currency->get_formatted($value[$currencyID]);

        }

        return '';

        return $value;
    }

    public function get_index($raw=false)
    {
        if ($raw===false) $raw = $this->get_raw();

        $id = $this->Tag->id();

        $out = array();

        if (PerchUtil::count($raw)) {

        	foreach($raw as $currencyID=>$price) {
        		$Currency = $this->get_currency((int)$currencyID);
        		
        		if ($Currency) {
        			if ($Currency->is_default()) {
	        			$out[] = [
		        			'key' => $id,
		        			'value' => $price,
		        		];
	        		}
	        		
	        		$out[] = [
	        			'key' => $id.'.'.strtolower($Currency->currencyCode()),
	        			'value' => $price,
	        		];
        		}
        		
        	}

        	return $out;
        }


        return $out;
    }

    public function get_api_value($raw=false)
    {
    	if ($raw===false) $raw = $this->get_raw();

    	$id = $this->Tag->id();

        $out = array();

        if (PerchUtil::count($raw)) {

        	foreach($raw as $currencyID=>$price) {
        		$Currency = $this->get_currency((int)$currencyID);
        		
        		if ($Currency) {
	        		$out[strtoupper($Currency->currencyCode())] = $price;
        		}
        		
        	}

        	return $out;
        }


        return $out;
    }

    public function import_data($data)
    {
        $id = $this->Tag->id();
        if (array_key_exists($id, $data)) {

        	$currencies = $this->get_currencies();

        	if (PerchUtil::count($data[$id]) && PerchUtil::count($currencies)) {
        		foreach($data[$id] as $curr=>$val) {
        			foreach($currencies as $Currency) {
        				if (strtoupper($curr) == $Currency->currencyCode()) {
        					$data[$id.'_curr'.$Currency->id()] = $val;
        				}
        			}
        		}
        	}
        	//PerchUtil::debug($data[$id]);

            return $this->get_raw($data);
        }

        return null;
    }

    protected function get_currencies()
    {
    	$API  = new PerchAPI(1.0, 'perch_shop');
    	$Currencies = new PerchShop_Currencies($API);
        return $Currencies->get_active();
    }

    protected function get_currency($id)
    {
    	$API  = new PerchAPI(1.0, 'perch_shop');
    	$Currencies = new PerchShop_Currencies($API);
        return $Currencies->find($id);
    }
}

/* ------------ CURRENCY SYMBOL POSITION ------------ */

class PerchFieldType_shop_currency_symbol_position extends PerchShop_FieldType
{

	public function render_inputs($details=array())
	{
		$API  = new PerchAPI(1.0, 'perch_shop');
		$Lang = $API->get('Lang');

		$opts = array();
		$opts[] = array('label'=>$Lang->get('Before amount'), 'value'=>'before');
		$opts[] = array('label'=>$Lang->get('After amount'),  'value'=>'after');

		return $this->Form->select($this->Tag->input_id(), $opts, $this->get_value($details));

	}

}

/* ------------ SHIPPING PRICE ------------ */

class PerchFieldType_shop_shipping_price extends PerchFieldType_shop_currency_value
{
	public function render_inputs($details=array())
    {
    	$API  = new PerchAPI(1.0, 'perch_shop');
    	$Lang = $API->get('Lang');
    	$Settings = $API->get('Settings');
    	

    	$tax_mode = 'excluding tax';
    	if ($Settings->get('perch_shop_price_tax_mode')->val()=='inc') {
    		$tax_mode = 'including tax';
    	}
    	if (PERCH_RUNWAY && $this->Tag->id()=='trade_price') {
    		$tax_mode = 'excluding tax';
	    	if ($Settings->get('perch_shop_trade_price_tax_mode')->val()=='inc') {
	    		$tax_mode = 'including tax';
	    	}
    	}

    	if ($this->Tag->pretax()) {
    		$tax_mode = 'before tax';
    	}

        $attributes = '';
        $attrs = array();
        $search = array('min', 'max', 'step');

        foreach($search as $s) {
            if ($this->Tag->is_set($s)) $attrs[] = $s.'='.$this->Tag->$s;
        }

        $attributes = implode(' ', $attrs);

        $currencies = $this->get_currencies();
        $zones = $this->get_zones();

        $out = '';

        if (PerchUtil::count($zones)) {
        	foreach($zones as $Zone) {
        		$out .= '<div class="field" style="padding-left:0;">';
        		$out .= '<fieldset class="fieldset-clean"><legend><label>'.$Zone->title().'</label></legend>';

        		
        		
        		$id = $this->Tag->input_id().'_zone'.$Zone->id();
        		$prev = $details;
        		$checked = 0;
		        if (isset($details[$this->Tag->input_id()])) {
		        	$prev = $details[$this->Tag->input_id()];
		        	if (isset($prev['zones'])) {
		        		if (in_array($Zone->id(), $prev['zones'])) {
		        			$checked = 1;
		        		}
		        	}
		        }

        		
        		$out .= '<div class="checkbox-single">';
        		$out .= $this->Form->label($id, $Lang->get('Available'));
        		$out .= '<div class="form-entry">'.$this->Form->checkbox($id, 1, $checked).'</div>';
        		$out .= '</div>';

        		if (PerchUtil::count($currencies)) {
		        	foreach($currencies as $Currency) {

		        		$id = $this->Tag->input_id().'_curr'.$Currency->id().'_zone'.$Zone->id();

		        		$prev = $details;
		        		if (isset($details[$this->Tag->input_id()])) {
		        			$prev = $details[$this->Tag->input_id()];
		        			if (isset($prev['z'.$Zone->id()])) {
		        				$prev = $prev['z'.$Zone->id()];
		        			}
		        		}
		        		#PerchUtil::debug($prev, 'success');

		        		$out .= '<div class="field-wrap compact-set">';
		        		$out .= $this->Form->text($id,
		                                $this->Form->get($prev, $Currency->id(), $this->Tag->default(), $this->Tag->post_prefix()),
		                                $this->Tag->size(),
		                                $this->Tag->maxlength(),
		                                'number input-simple',
		                                $attributes);
		        		$out .= ' '.$Currency->currencyCode();
		        		$out .= '</div>';

		        	}
		        	$out .= $this->Form->translated_hint($Lang->get($tax_mode));

		        	$out .= '</fieldset>';
		        	$out .= '</div>';
		        }

        	}
        }


        

        return $out;
    }

    public function get_raw($post=false, $Item=false)
    {
    	$data = [];

    	$currencies = $this->get_currencies();
    	$zones = $this->get_zones();

    	#PerchUtil::debug($post);

    	$data['zones'] = [];

    	if (PerchUtil::count($zones)) {
    		foreach($zones as $Zone) {
    			$id = $this->Tag->id().'_zone'.$Zone->id();
    			if (isset($post[$id]) && $post[$id]) {
    				$data['zones'][] = $Zone->id();

    				if (PerchUtil::count($currencies)) {
			    		foreach($currencies as $Currency) {
			    			$id = $this->Tag->id().'_curr'.$Currency->id().'_zone'.$Zone->id();
			    			if (isset($post[$id])) {
			    				$data['z'.$Zone->id()][$Currency->id()] = $post[$id];
			    			}
			    		}
			    	}
    			}

    			
    		}
    	}
    	

    	return $data;
    }

    public function get_processed($raw=false)
    {
    	return '';
    }

    public function get_index($raw=false)
    {
    	return '';
    }

	protected function get_zones()
    {
    	$API  = new PerchAPI(1.0, 'perch_shop');
    	$Zones = new PerchShop_ShippingZones($API);
        return $Zones->all();
    }
}

/* ------------ EMAIL TEMPLATE ------------ */

class PerchFieldType_shop_email_template extends PerchShop_FieldType
{
	protected $class;

	public function render_inputs($details=array())
	{
		$API  = new PerchAPI(1.0, 'perch_shop');
		$items = $this->find_templates();

		$mode = 'select';

        if ($this->Tag->display_as() && $this->Tag->display_as()=='checkboxes') {
            $mode = 'checkboxes';
        }

       	if ($mode == 'select') {
			return $this->Form->select($this->Tag->input_id(), $items, $this->get_value($details));
       	}

       	if ($mode == 'checkboxes') {
			$multicol = 'fieldtype';
	        if (PerchUtil::count($items) > 4) {
	            $multicol .= ' multi-col';
	        }else{
	            $multicol .= ' uni-col';
	        }

	        return $this->Form->checkbox_set($this->Tag->input_id(), false, $items, $this->Form->get($details, $this->Tag->id(), $this->Tag->default(), $this->Tag->post_prefix()), false, false, $multicol);

       	}

	}

	public function get_raw($post=false, $Item=false)
    {
        if ($post===false) {
            $post = $_POST;
        }

        $id = $this->Tag->id();
        if (isset($post[$id])) {

            $this->raw_item = $post[$id];
            return $this->raw_item;
        }

        return null;
    }

	public function find_templates()
	{
		$app_templates = $this->get_dir_contents(__DIR__.'/templates/shop/emails');
        $local_templates = $this->get_dir_contents(PERCH_TEMPLATE_PATH.'/shop/emails');

        $templates = array_merge($app_templates, $local_templates);
        sort($templates);
        return $templates;
	}

	public function get_dir_contents($dir, $prefix='')
    {
        $Perch = Perch::fetch();

        $a = array();
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if(substr($file, 0, 1) != '.' && !preg_match($Perch->ignore_pattern, $file)) {
                        if (is_dir($dir.DIRECTORY_SEPARATOR.$file)) {
                        	$sub = $this->get_dir_contents($dir.DIRECTORY_SEPARATOR.$file, $file.DIRECTORY_SEPARATOR);
                        	if (PerchUtil::count($sub)) {
                        	 	$a = array_merge($a, $sub);
                        	}
                        }else{
                        	if (PerchUtil::file_extension($file)=='html' && substr($file, 0, 1)!=='_' && $file!=='email.html') {
                        		$a[] = ['value'=>$prefix.$file, 'label'=>PerchUtil::filename($prefix.$file)];
                        	}
                        }
                    }
                }
                closedir($dh);
            }
        }
        return $a;
    }
}

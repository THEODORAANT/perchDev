<?php


class PerchEvents_FieldType extends PerchFieldType
{
	public function get_value($details=array(), $id=null)
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

class PerchEvents_FieldType_API_Lookup extends PerchEvents_FieldType
{
	protected $class;

	public function render_inputs($details=array())
	{
		$API  = new PerchAPI(1.0, 'perch_events');
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
		$API  = new PerchAPI(1.0, 'perch_events');
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
		
		$API   = new PerchAPI(1.0, 'perch_events');
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
	                    $out[] = array('key'=>$id.'.'.$key, 'value'=>trim($val));
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
		
		$API   = new PerchAPI(1.0, 'perch_events');
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
	                if (!is_array($val) && strpos($key, 'perch_')===false && strpos($key, 'DynamicFields')===false) {
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

        $API   = new PerchAPI(1.0, 'perch_events');
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

class PerchEvents_FieldType_bool extends PerchEvents_FieldType
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




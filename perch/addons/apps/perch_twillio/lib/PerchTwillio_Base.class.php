<?php

class PerchTwillio_Base extends PerchAPI_Base implements JsonSerializable
{
	protected $pk_is_int = true;

	protected $date_fields = [];

	public $deleted_date_column = null;
	public $suppress_events     = false;

	public function __sleep()
	{
		$this->db = null;
		return ['details', 'api', 'table', 'pk', 'factory_classname'];

	}

	public function __wakeup()
	{
		$this->db 	 = PerchDB::fetch();
	}

	//public function jsonSerialize()
	public function jsonSerialize():mixed
	{
		return $this->to_array_for_api();
	}

	public function delete()
	{
		if (is_null($this->deleted_date_column)) {
			return parent::delete();
		}
		
		$this->update([
			$this->deleted_date_column => gmdate('Y-m-d H:i:s'),
			]);

		return true;
	}


	public function update($data)
	{
		if (isset($this->duplicate_fields) && count($this->duplicate_fields)) {
			$dynamic_field_col = str_replace('ID', 'DynamicFields', $this->pk);
			if (isset($data[$dynamic_field_col])) {
				$dynamic_fields    = PerchUtil::json_safe_decode($data[$dynamic_field_col], true);

				foreach($this->duplicate_fields as $target=>$source) {

					$urlify = false;
					if ($source[0]==='*') {
						$source = str_replace('*', '', $source);
						$urlify = true;
					}

					if (isset($dynamic_fields[$source])) {

						if (is_array($dynamic_fields[$source])) {
							$dynamic_fields[$source] = $this->_distil($source, $dynamic_fields);
						}

						if ($urlify) {
							$data[$target] = PerchUtil::urlify($dynamic_fields[$source]);
						}else{
							$data[$target] = $dynamic_fields[$source];
						}

					}
				}
			}
		}

		$r = parent::update($data);

		return $r;
	}

	public function intelliupdate($data)
	{

		$dynamic_field_col = str_replace('ID', 'DynamicFields', $this->pk);
		$dynamic_fields    = PerchUtil::json_safe_decode($this->details[$dynamic_field_col], true);

		if (is_array($dynamic_fields)) {
			$dynamic_fields	= array_merge($dynamic_fields, $data);	
		} else {
			$dynamic_fields = $data;
		}

		return $this->update([
			$dynamic_field_col => PerchUtil::json_safe_encode($dynamic_fields)
			]);
	}

	public function get($val)
	{
		$dynamic_field_col = str_replace('ID', 'DynamicFields', $this->pk);
		$dynamic_fields    = PerchUtil::json_safe_decode($this->details[$dynamic_field_col], true);

		if (isset($dynamic_fields[$val])) return $dynamic_fields[$val];

		return false;
	}

	public function title()
	{
		$col = str_replace('ID', 'Title', $this->pk);
		return $this->$col();
	}

	public function get_factory()
	{
		return new $this->factory_classname($this->api);
	}

	public function update_locally($data)
	{
		return $this->update($data);
	}

	private function _distil($field, $details)
	{
		if (isset($details[$field])) {
			if (is_array($details[$field])) {
				$opts = ['id', 'key', 'code', 'slug'];
				foreach($opts as $opt) {
					if (isset($details[$field]['data'][$opt])) {
						return $details[$field]['data'][$opt];
					}
				}
			}
			return $details[$field];
		}

		return null;
	}

	protected function flatten_array($prefix, array $array) 
	{
    	$prefix = trim($prefix, '_');
	    $flattened_array = array();
	    array_walk($array, function($a, $key) use (&$flattened_array, $prefix) {
	    	if (is_array($a)) {
	    		$next = $this->flatten_array($prefix.'_'.$key, $a);
	    		$flattened_array = array_merge($flattened_array, $next);
	    	}else{
	    		$flattened_array[$prefix.'_'.$key] = $a;
	    	}
	    });
	    return $flattened_array;
	}

}

<?php

class PerchShop_Factory extends PerchAPI_Factory
{
	protected $namespace = 'shop';
	protected $master_template = false;
	protected $deleted_date_column = null;

	public function create($data)
	{
		if (isset($this->created_date_column)) {
			$data[$this->created_date_column] = gmdate('Y-m-d H:i:s');
		}
		$Result = parent::create($data);
		if (is_object($Result)) {
			$Result->update($data);
		}
		return $Result;
	}

	public function intellicreate($data)
	{
		$dynamic_field_col = str_replace('ID', 'DynamicFields', $this->pk);

		return $this->create([
			$dynamic_field_col => PerchUtil::json_safe_encode($data)
			]);
	}

	protected function standard_restrictions()
    {
    	$c = new $this->singular_classname([]);
        if (!is_null($c->deleted_date_column)) {
			return ' AND '.$c->deleted_date_column.' IS NULL ';
		}

		return '';
    }

    public function standard_where_callback(PerchQuery $Query)
    {
    	if ($this->deleted_date_column) {
    		 $Query->where[] = $this->deleted_date_column . ' IS NULL';
		}
        return $Query;
    }


}
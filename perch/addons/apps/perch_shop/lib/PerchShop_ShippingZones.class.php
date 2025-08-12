<?php

class PerchShop_ShippingZones extends PerchShop_Factory
{
    protected $table               = 'shop_shipping_zones';
    protected $pk                  = 'zoneID';
    protected $singular_classname  = 'PerchShop_ShippingZone';

    public $static_fields          = ['zoneTitle', 'zoneIsDefault'];

    protected $namespace           = 'shop';
    protected $event_prefix        = 'shop.shipping_zone';

    protected $default_sort_column = 'zoneTitle';
	protected $created_date_column = 'zoneCreated';

	public function find_matching($countryID=null)
	{
		$sql = 'SELECT z.* FROM '.$this->table.' z LEFT JOIN '.PERCH_DB_PREFIX.'shop_shipping_zone_countries c ON z.zoneID=c.zoneID
				WHERE z.zoneActive=1 AND z.zoneDeleted IS NULL 
					AND (c.countryID='.$this->db->pdb((int)$countryID) .' OR zoneIsDefault=1)
				ORDER BY zoneIsDefault ASC LIMIT 1';

		return $this->return_instance($this->db->get_row($sql));
	}

	public function find_set($ids)
	{
		$ids = array_map("intval", $ids);
		$sql = 'SELECT * FROM '.$this->table.' WHERE zoneDeleted IS NULL AND zoneActive=1 AND zoneID IN ('.$this->db->implode_for_sql_in($ids).')';
		return $this->return_instances($this->db->get_rows($sql));
	}
}
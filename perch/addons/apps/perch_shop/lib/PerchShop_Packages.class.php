<?php

class PerchShop_Packages extends PerchShop_Factory
{
    public $api_method         = 'packages';
    public $api_list_method    = 'packages';
    public $singular_classname = 'PerchShop_Package';
    public $static_fields      = ['customerID', 'month', 'status'];
    public $remote_fields      = ['customerID', 'month', 'status'];

    protected $table               = 'shop_packages';
    protected $pk                  = 'packageID';
    protected $default_sort_column = 'packageID';

    protected $event_prefix = 'shop.package';

    public function get_for_customer($customerID)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE customerID=' . $this->db->pdb((int)$customerID);
        return $this->return_instances($this->db->get_rows($sql));
    }
}


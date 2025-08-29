<?php

class PerchShop_PackageItems extends PerchShop_Factory
{
    public $api_method         = 'packages';
    public $api_list_method    = 'packages';
    public $singular_classname = 'PerchShop_PackageItem';
    public $static_fields      = ['packageID', 'productID', 'variantID', 'qty'];

    protected $table               = 'shop_package_items';
    protected $pk                  = 'itemID';
    protected $index_table         = false;
    protected $default_sort_column = 'itemID';

    protected $event_prefix = 'shop.packageitem';

    public function get_for_package($packageID)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE packageID=' . $this->db->pdb((int)$packageID);
        return $this->return_instances($this->db->get_rows($sql));
    }
}


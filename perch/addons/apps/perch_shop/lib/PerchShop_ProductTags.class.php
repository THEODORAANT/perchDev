<?php

class PerchShop_ProductTags extends PerchAPI_Factory
{
    protected $table               = 'shop_product_tags';
    protected $pk                  = 'prodtagID';
    protected $singular_classname  = 'PerchShop_ProductTag';

    protected $index_table         = false;
    protected $namespace           = 'tag';

    protected $event_prefix        = 'shop.producttag';

    protected $default_sort_column = 'tagOrder';

	public $static_fields          = ['prodtagID', 'tagID', 'productID', 'tagTitle', 'tagSlug', 'resourceID', 'tagDynamicFields', 'tagOrder'];

	public function get_tag_ids($productID)
	{
		$sql = 'SELECT prodtagID FROM '.$this->table.' WHERE productID='.$this->db->pdb((int)$productID).' AND tagDeleted IS NULL';
		return $this->db->get_rows_flat($sql);
	}

	public function get_for_product($productID)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE productID='.$this->db->pdb((int)$productID).' AND tagDeleted IS NULL ORDER BY tagOrder ASC';
		return $this->return_instances($this->db->get_rows($sql));
	}

	public function get_array_for_editing($productID)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE productID='.$this->db->pdb((int)$productID).' AND tagDeleted IS NULL ORDER BY tagOrder ASC';
		$rows = $this->db->get_rows($sql);

		if (PerchUtil::count($rows)) {
			$out = ['tags'=>[]];
			foreach($rows as $row) {
				$tmp = PerchUtil::json_safe_decode($row['tagDynamicFields'], true);
				$tmp['id'] = $row[$this->pk];
				$out['tags'][] = $tmp;
			}
			return $out;
		}
		return ['tags'=>false];
	}
}
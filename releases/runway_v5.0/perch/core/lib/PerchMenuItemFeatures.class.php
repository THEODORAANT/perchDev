<?php

class PerchMenuItemFeatures extends PerchFactory
{
	protected $singular_classname  = 'PerchMenuItemFeature';
	protected $table               = 'menu_item_features';
	protected $pk                  = 'featureID';
	protected $default_sort_column = 'featureOrder';

	public $static_fields = ['parentID', 'featureType', 'featureOrder', 'featureTitle', 'featureValue', 'featurePersists', 'featureActive', 'privID', 'userID'];

	public function get_top_level()
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE featureType='.$this->db->pdb('app').'  ORDER BY featureOrder ASC';
		return $this->return_instances($this->db->get_rows($sql));

	}

	public function get_for_parent($parentID)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE parentID='.$this->db->pdb($parentID).' AND featureInternal=0 ORDER BY featureOrder ASC';
		return $this->return_instances($this->db->get_rows($sql));
	}

	public function get_by_title($featureTitle)
    {
    		$sql = 'SELECT * FROM '.$this->table.' WHERE featureTitle='.$this->db->pdb($featureTitle).'  ORDER BY featureOrder ASC';
    		return  $this->db->get_row($sql);//$this->return_instances($this->db->get_row($sql));

   }

}





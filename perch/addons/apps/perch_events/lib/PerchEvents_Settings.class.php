<?php
class PerchEvents_Settings extends PerchEvents_Factory
{
	public $api_method             = 'settings';
	public $api_list_method        = 'settings';
    protected $table  = 'events_settings';
    protected $pk     = 'settingID';
	public $singular_classname     = 'PerchEvents_Setting';


    	public function find_by_slug($settingSlug)
    	{
    		$sql = 'SELECT * FROM '.$this->table.' WHERE settingSlug='.$this->db->pdb($settingSlug);

    		return $this->return_instance($this->db->get_row($sql));
    	}

    }


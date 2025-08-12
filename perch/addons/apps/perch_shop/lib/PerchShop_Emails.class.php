<?php

class PerchShop_Emails extends PerchShop_Factory
{
	public $api_method             = 'emails';
	public $api_list_method        = 'emails';
	public $singular_classname     = 'PerchShop_Email';
	public $static_fields          = ['emailTitle', 'emailCreated', 'emailStatus', 'emailTemplate', 'emailFor', 'emailRecipient'];
	public $remote_fields          = ['name', 'slug', 'subject', 'enabled'];

	protected $table               = 'shop_emails';
	protected $pk                  = 'emailID';
	protected $index_table         = 'shop_index';
	protected $master_template	   = 'shop/emails/email.html';

	protected $default_sort_column = 'emailStatus';
	protected $created_date_column = 'emailCreated';

	protected $runtime_restrictions = [
		[
			'field'          => 'status',
			'values'         => ['1'],
			'negative_match' => false,
			'match'          => 'all',
			'fuzzy'			 => false
		]
	];

	protected $event_prefix = 'shop.email';

	public function find_for_status($statusID)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE emailStatus='.$this->db->pdb((int)$statusID).' AND emailActive=1';
		return $this->return_instance($this->db->get_row($sql));
	}

	public function get_for_status($statusID)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE emailStatus='.$this->db->pdb((int)$statusID).' AND emailActive=1';
		return $this->return_instances($this->db->get_rows($sql));
	}
}

<?php

class PerchShop_Sales extends PerchShop_Factory
{
	public $singular_classname     = 'PerchShop_Sale';
	public $static_fields          = ['saleTitle', 'saleCreated', 'saleUpdated', 'saleDeleted', 'saleActive'];
	public $remote_fields          = ['title', 'from', 'to', 'status', 'priority'];
	
	protected $table               = 'shop_sales';
	protected $pk                  = 'saleID';

	protected $master_template	   = 'shop/sales/sale.html';
	
	protected $default_sort_column = 'saleTitle';
	protected $created_date_column = 'saleCreated';
	protected $deleted_date_column = 'saleDeleted';

	protected $event_prefix = 'shop.sale';

	protected $runtime_restrictions = [
		[
			'field'          => 'status',
			'values'         => ['1'],
			'negative_match' => false,
			'match'          => 'all',
			'fuzzy'			 => false
		]
	];

	public function get_currently_active()
	{
		$now = gmdate('Y-m-d H:i:00');
		$sql = 'SELECT * FROM '.$this->table.'
				WHERE saleFrom<='.$this->db->pdb($now).' AND saleTo>'.$this->db->pdb($now).' AND saleActive=1 AND saleDeleted IS NULL
				ORDER BY saleOrder ASC';
		return $this->return_instances($this->db->get_rows($sql));
	}
}



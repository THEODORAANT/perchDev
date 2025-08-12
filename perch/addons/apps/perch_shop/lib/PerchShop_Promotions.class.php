<?php

class PerchShop_Promotions extends PerchShop_Factory
{
	public $api_method             = 'promotions/cart';
	public $api_list_method        = 'promotions/cart';
	public $singular_classname     = 'PerchShop_Promotion';
	public $static_fields          = ['promoTitle', 'promoCreated', 'promoUpdated', 'promoDeleted', 'promoActive'];
	public $remote_fields          = ['title', 'description', 'discount_code', 'from', 'to', 'max_uses', 'customer_uses', 'customer_group', 'status', 'terminating', 'persistent', 'action', 'priority', 'amount', 'max_quantity', 'trigger_quantity', 'apply_to_shipping', 'shipping_methods', 'match_items', 'match_rules'];
	
	protected $table               = 'shop_promotions';
	protected $pk                  = 'promoID';
	protected $index_table         = 'shop_index';
	protected $master_template	   = 'shop/promotions/promotion.html';
	
	protected $default_sort_column = 'promoTitle';
	protected $created_date_column = 'promoCreated';
	protected $deleted_date_column = 'promoDeleted';

	protected $event_prefix = 'shop.promotion';

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
				WHERE promoFrom<='.$this->db->pdb($now).' AND promoTo>'.$this->db->pdb($now).' AND promoActive=1 AND promoDeleted IS NULL
				ORDER BY promoOrder ASC';
		return $this->return_instances($this->db->get_rows($sql));
	}
}



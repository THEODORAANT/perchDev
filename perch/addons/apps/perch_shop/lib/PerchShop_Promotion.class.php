<?php

class PerchShop_Promotion extends PerchShop_Base
{
	protected $factory_classname = 'PerchShop_Promotions';
	protected $table             = 'shop_promotions';
	protected $pk                = 'promoID';
	protected $index_table       = 'shop_index';

	protected $modified_date_column = 'promoUpdated';

	protected $event_prefix = 'shop.promotion';

	protected $duplicate_fields  = [
									'promoTitle' => 'title', 
									'promoActive' => 'active',
									'promoFrom' => 'from',
									'promoTo' => 'to',
								   ];

	public function get_use_count($customerID=null)
	{
		$sql = 'SELECT COUNT(*) FROM '.PERCH_DB_PREFIX.'shop_order_promotions
				WHERE promoID='.$this->db->pdb((int)$this->id());

		if ($customerID!==null) {
			$sql .= ' AND customerID='.$this->db->pdb((int)$customerID);
		}

		return $this->db->get_count($sql);
	}

}
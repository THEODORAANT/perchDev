<?php

class PerchShop_OrderStatuses extends PerchShop_Factory
{
	public $singular_classname     = 'PerchShop_OrderStatus';
	public $static_fields          = ['statusID', 'statusKey', 'statusTitle', 'statusEditable', 'statusIndex', 'statusActive'];

	protected $table               = 'shop_order_statuses';
	protected $pk                  = 'statusID';
	protected $master_template	   = 'shop/orders/status.html';

	protected $default_sort_column = 'statusIndex';
	protected $created_date_column = 'statusCreated';

	public static $statuses 	 	= [];

	protected $event_prefix = 'shop.orderstatus';

	public function find_by_key($key)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE statusKey='.$this->db->pdb($key).' AND statusActive=1 AND statusDeleted IS NULL';
		return $this->return_instance($this->db->get_row($sql));
	}

	public function get_select_options()
	{
		$statuses = $this->all();

		$out = [];

		if (PerchUtil::count($statuses)) {
			foreach($statuses as $Status) {
				$out[] = [
					'label' => $Status->statusTitle(),
					'value' => $Status->statusKey(),
				];
			}
		}

		return $out;
	}

	public function get_list()
	{
		$this->_load_statuses();
		return PerchShop_OrderStatuses::$statuses;
	}

	public function get_status_and_above($key='paid')
	{
		$this->_load_statuses();
		$out = [];

		$statuses = PerchShop_OrderStatuses::$statuses;

		if (PerchUtil::count($statuses)) {
			$keep = false;	
			foreach($statuses as $status) {
				if ($keep || $status==$key) {
					$keep = true;
					$out[] = $status;
					continue;
				}
			}
		}

		return $out;
	}

	private function _load_statuses()
	{
		if (count(PerchShop_OrderStatuses::$statuses)===0) {
			$sql = 'SELECT statusKey FROM '.$this->table.' WHERE statusDeleted IS NULL ORDER BY statusIndex ASC';
			PerchShop_OrderStatuses::$statuses = $this->db->get_rows_flat($sql);
		}
	}
}


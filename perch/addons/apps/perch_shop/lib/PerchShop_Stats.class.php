<?php

class PerchShop_Stats extends PerchShop_Factory
{
	public $api_method             = 'statistics';
	public $api_list_method        = 'statistics';
	public $singular_classname     = 'PerchShop_Stat';
	public $static_fields          = [];
	public $remote_fields          = [];

	protected $table               = 'shop_stats';
	protected $pk                  = 'statID';
	protected $index_table         = 'shop_admin_index';
	protected $master_template	   = 'shop/stats/stat.html';

	protected $default_sort_column = '';
	protected $created_date_column = '';

	public function get_store_stats($timeframe='30days')
	{
		$data = [
			'from' => $timeframe,
		];

		#$Cache = PerchShop_Cache::fetch();
		#$cache_key = 'admin.stats.store.'.$timeframe;

		#if ($Cache->exists($cache_key)) {
		#	return $Cache->get($cache_key);
		#}

		try {
			$out = [];

			$Orders = new PerchShop_Orders($this->api);
			$out['orders'] = $Orders->get_dashboard_widget();
			$out['revenue'] = $Orders->get_revenue_dashboard_widget();
			
			#$Cache->set($cache_key, $out);

			return $out;
		} catch (Exception $e) {
			PerchUtil::debug($e->getMessage(), 'error');
		}
	}

	public function render_widget($type, $data)
	{
		#PerchUtil::mark($type);

		PerchSystem::set_var('orders_app_path', $this->api->app_path('perch_shop_orders'));
		PerchSystem::set_var('shop_app_path', $this->api->app_path('perch_shop'));
		PerchSystem::set_var('products_app_path', $this->api->app_path('perch_shop_products'));

		switch($type) {

			case 'orders':
				$Template = $this->api->get('Template');
				$Template->set('shop/stats/orders.html', 'shop');
				return $Template->render_group($data['items']);
				break;

			case 'revenue':
				$Currencies = new PerchShop_Currencies($this->api);
				$ReportingCurrency = $Currencies->get_reporting_currency();
				if ($ReportingCurrency) {
					PerchSystem::set_vars($ReportingCurrency->to_array());
					$Template = $this->api->get('Template');
					$Template->set('shop/stats/revenue.html', 'shop');
					return $Template->render_group($data['items']);	
				}else{
					PerchUtil::debug("Reporting Currency not set.", 'error');
					return false;
				}
				
				break;

		}
	}

}
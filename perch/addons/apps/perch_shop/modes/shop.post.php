<?php
	
	echo $HTML->title_panel([
		'heading' => $Lang->get('Shop dashboard'),
	], $CurrentUser);

if ($first_run) {

	function do_importable($status='todo', $html_message='OK', $action_url='')
	{
		$action_msg='Info';

		if (substr($action_url, 0, 1) == '#') {
			$action_url = 'https://docs.grabaperch.com/addons/shop/getting-started/' . $action_url;
		}

		switch($status) {
			case 'todo':
				$class = 'warning';
				$icon  = PerchUI::icon('core/alert');
				break;

			case 'success':
				$class = 'success';
				$icon  = PerchUI::icon('core/circle-check');
				break;

			default: 
				$class = $status;
				break;
		}

		echo '<li class="progress-item progress-'.$class.'">'.$icon.' '.$html_message.($action_msg?'<a class="button button-small action-'.$class.'" href="'.PerchUtil::html($action_url, true).'">'.$action_msg.'</a>':'').'</li>';
	}

	echo '<div class="inner">';
	echo '<ul class="progress-list">';

	$todos_found = 0;

	/* config/apps.php */
		$file = file_get_contents(PerchUtil::file_path(PERCH_PATH.'/config/apps.php'));
		$members_pos = strpos($file, 'perch_members');
		$shop_pos    = strpos($file, 'perch_shop');

		$status = 'todo';
		if ($members_pos && $shop_pos) $status = 'success';
		
		do_importable($status, $Lang->get('Add %sperch_members%s and %sperch_shop%s to your %sconfig/apps.php%s file.', '<code>','</code>','<code>','</code>','<code>','</code>'), '#appsconfig');

		if ($members_pos > $shop_pos) {
			do_importable('warning', $Lang->get('Make sure %sperch_members%s appears before %sperch_shop%s to your %sconfig/apps.php%s file.', '<code>','</code>','<code>','</code>','<code>','</code>'), '#appsconfig');
		}

		if ($status == 'todo') $todos_found++;
	/* --------------------------------- */

	
	/* categories */
		$status = 'todo';
		$Sets = new PerchCategories_Sets($API);
		$Set  = $Sets->get_one_by('setSlug', 'products');
		if ($Set) {
			$status = 'success';
		}

		do_importable($status, $Lang->get('%sCreate a category set%s for your products.', '<a class="go progress-link" href="'.PERCH_LOGINPATH.'/core/apps/categories/sets/edit/">', '</a>'), '#categories');

		if ($status == 'todo') $todos_found++;
	/* --------------------------------- */

	
	/* currency */
		$status = 'todo';
		$Currencies = new PerchShop_Currencies($API);
		$active = $Currencies->get_active();

		if (PerchUtil::count($active)) {
			$status = 'success';
		}
		do_importable($status, $Lang->get('%sEnable at least one currency%s to sell in.', '<a class="go progress-link" href="'.$API->app_path('perch_shop').'/currencies/">', '</a>'), '#currency');

		if ($status == 'todo') $todos_found++;
	/* --------------------------------- */


	/* settings */
		$status = 'todo';
		$Settings = PerchSettings::fetch();
		if ($Settings->get('perch_shop_default_currency')->val()) {
			$status = 'success';
		}
		do_importable($status, $Lang->get('%sConfigure settings%s for currencies and how prices are entered.', '<a class="go progress-link" href="'.PERCH_LOGINPATH.'/core/settings/#perch_shop">', '</a>'), '#tax');

		if ($status == 'todo') $todos_found++;
	/* --------------------------------- */

	/* home tax location */
		$status = 'todo';
		$Locations = new PerchShop_TaxLocations($API);
		$loc = $Locations->get_one_by('locationIsHome', '1');
		if ($loc) {
			$status = 'success';
		}
		do_importable($status, $Lang->get('%sCreate a home tax location%s for tax calculation purposes.', '<a class="go progress-link" href="'.$API->app_path('perch_shop').'/tax/locations/">', '</a>'), '#tax');

		if ($status == 'todo') $todos_found++;
	/* --------------------------------- */

	/* default tax location */
		$status = 'todo';
		$Locations = new PerchShop_TaxLocations($API);
		$loc = $Locations->get_one_by('locationIsDefault', '1');
		if ($loc) {
			$status = 'success';
		}
		do_importable($status, $Lang->get('%sCreate a default tax location%s for tax calculation purposes.', '<a class="go progress-link" href="'.$API->app_path('perch_shop').'/tax/locations/">', '</a>'), '#tax');

		if ($status == 'todo') $todos_found++;
	/* --------------------------------- */


	/* tax group */
		$status = 'todo';
		$Groups = new PerchShop_TaxGroups($API);
		$groups = $Groups->all($Paging);
		if (PerchUtil::count($groups)) {
			$status = 'success';
		}
		do_importable($status, $Lang->get('%sCreate at least one tax group%s to assign your products and shipping to.', '<a class="go progress-link" href="'.$API->app_path('perch_shop').'/tax/groups/edit/">', '</a>'), '#tax');

		if ($status == 'todo') $todos_found++;
	/* --------------------------------- */

	/* shipping zone */
		$status = 'todo';
		$Zones = new PerchShop_ShippingZones($API);
		$groups = $Zones->all($Paging);
		if (PerchUtil::count($groups)) {
			$status = 'success';
		}
		do_importable($status, $Lang->get('%sCreate at least one shipping zone%s to define where in the world you deliver to.', '<a class="go progress-link" href="'.$API->app_path('perch_shop').'/shippings/zones/edit/">', '</a>'), '#shipping');

		if ($status == 'todo') $todos_found++;
	/* --------------------------------- */


	/* shipping method */
		$status = 'todo';
		$Method = new PerchShop_Shippings($API);
		$groups = $Method->all($Paging);
		if (PerchUtil::count($groups)) {
			$status = 'success';
		}
		do_importable($status, $Lang->get('%sCreate at least one shipping method%s by which products can be shipped.', '<a class="go progress-link" href="'.$API->app_path('perch_shop').'/shippings/edit/">', '</a>'), '#shipping');

		if ($status == 'todo') $todos_found++;
	/* --------------------------------- */

	/* brands */
		$status = 'todo';
		$Brands = new PerchShop_Brands($API);
		$brands = $Brands->all($Paging);
		if (PerchUtil::count($brands)) {
			$status = 'success';
		}
		do_importable($status, $Lang->get('%sCreate one or more brands%s to assign products to.', '<a class="go progress-link" href="'.$API->app_path('perch_shop_products').'/brands/edit/">', '</a>'), '#brands');

		if ($status == 'todo') $todos_found++;
	/* --------------------------------- */

	/* products */
		if ($todos_found==0) {
			$status = 'success';	
			do_importable($status, $Lang->get('All done! %sAdd some products!%s', '<a class="go progress-link" href="'.$API->app_path('perch_shop_products').'/">', '</a>'), 'https://docs.grabaperch.com/addons/shop/products/');
		}
		
		
	/* --------------------------------- */



	echo '</ul>';
	echo '</div>';


}else{
?>
<div id="dashboard" class="dashboard inline-dash">
<?php
	if (PerchUtil::count($stats)) {

		$order = $default_widget_order;
		$configured_order = $Settings->get('perch_shop_dashboard_order')->val();
		if ($configured_order && !empty($configured_order)) {
			$order = $configured_order;
		}

		$order = explode(',', $order);
		PerchUtil::debug($order);

		$found_items = 0;


		foreach($order as $widget_id) {
			foreach($stats as $widget=>$data) {
				if ($widget==$widget_id) {
					if (isset($data['items']) && is_array($data['items']) ) {
						$found_items += count($data['items']);
					}
					echo $Stats->render_widget($widget, $data);
				}
			}
		}

		// output any without a configured order
		foreach($stats as $stat) {
			foreach($stat as $widget=>$data) {
				if (!in_array($widget, $order)) {
					if (isset($data['items']) && count($data['items'])) {
						$found_items += count($data['items']);
					}
					echo $Stats->render_widget($widget, $data);
				}
			}
		}

		

	}
?>
</div>
<?php 

	if ($found_items == 0) {
			echo $HTML->warning_message('Once you have some orders, they will show up here!');
		}


} // first_run

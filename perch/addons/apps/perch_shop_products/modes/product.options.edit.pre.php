<?php
	
	$Products     = new PerchShop_Products($API);
	$Options      = new PerchShop_Options($API);
	$OptionValues = new PerchShop_OptionValues($API);

	if (!isset($_GET['id'])) exit;

	$message = false;

	$productID = (int)PerchUtil::get('id');
	$Product = $Products->find($productID);

	$Form = $API->get('Form');

	if ($Form->submitted()) {
		$postvars = ['opts'];
		$data = $Form->receive($postvars);

		if (isset($data['opts'])) {
			$Product->set_options($data['opts']);
		}else{
			// no modifiers? delete them.
			$Product->set_options([]);
		}

		$vals = $Form->find_items('vals_');
		$new_values = [];
		if (PerchUtil::count($vals)) {
			foreach($vals as $optionID=>$values) {
				if (PerchUtil::count($values)) {
					foreach($values as $valueID) {
						$Product->set_option_values($optionID, $valueID);
						$new_values[] = [
							'optionID' => $optionID,
							'valueID' => $valueID,
							];
					}
				}
			}
		}
		$Product->set_option_values($new_values);

	}

	$options 		= $Options->get_checkbox_options();
	$option_values  = $Options->get_checkbox_values($productID);

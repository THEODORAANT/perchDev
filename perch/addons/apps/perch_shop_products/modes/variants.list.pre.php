<?php
	$Paging = $API->get('Paging');
	$Paging->set_per_page(48);

	$message = '';

	$productID = (int)PerchUtil::get('id');

	$Products   = new PerchShop_Products($API);
	$Product    = $Products->find($productID);

	$Form = $API->get('Form');

	if ($Form->submitted()) {
		$Product->generate_variants();
	}

	$message = $HTML->warning_message('You should generate variants after making any changes to the options. %s', $Form->submit('btnSubmit', $Lang->get('Generate variants'), 'button button-small action-warning', false));




	$variants  = $Products->get_by('parentID', $productID, 'productOrder', $Paging);


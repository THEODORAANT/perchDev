<?php

	$Form = $API->get('Form');

	$l1 = $Lang->get('Export');
	$l2 = $Lang->get('Between');
	$l3 = $Lang->get('and');
	$l4 = $Lang->get('Status');
	


	$startdate = date('Y-m-01');
	$enddate   = date('Y-m-31');

	$OrdersExport = new PerchShop_OrdersExport($API);
	$Statuses 	  = new PerchShop_OrderStatuses($API);



	$opts   = array();
    $opts[] = $Lang->get('Orders').'|orders';
    $opts[] = $Lang->get('Order items').'|orderitems';
    $opts[] = $Lang->get('Customer addresses').'|addresses';
    $export_opts =  implode(',', $opts);

    $opts =  [];
    $statuses = $Statuses->all();
    if (PerchUtil::count($statuses)) {
    	foreach($statuses as $Status) {
    		$opts[] = $Status->statusTitle().'|'.$Status->statusKey();
    	}
    }

    $status_opts =  implode(',', $opts);

	$default_fields = '<perch:shop id="export" type="select" label="'.$l1.'" options="'.$export_opts.'" required="true" />
						<perch:shop id="start" type="date" label="'.$l2.'" default="'.$startdate.'" />
						<perch:shop id="end" type="date" label="'.$l3.'" default="'.$enddate.'" />
						<perch:shop id="status" type="select" label="'.$l4.'" options="'.$status_opts.'" required="true" default="paid" />';

	if (defined('PERCH_SHOP_ASSISTANT') && PERCH_SHOP_ASSISTANT) {
		$l5 = $Lang->get('Format');

		$opts   = array();
	    $opts[] = $Lang->get('Default').'|default';
	    $opts[] = $Lang->get('Xero').'|xero';
	    $format_opts =  implode(',', $opts);

		$default_fields .= '<perch:shop id="format" type="select" label="'.$l5.'" options="'.$format_opts.'" />';
	}


	if (false && PERCH_RUNWAY) {

		$l8 = $Lang->get('Save to');

		$bucket_opts = '';

		$buckets = PerchResourceBuckets::get_all_remote();

		$opts = array();
		$opts[] = $Lang->get('Download directly').'|download';

		if (PerchUtil::count($buckets)) {
			foreach($buckets as $Bucket) {
				if ($Bucket) $opts[] = ucwords($Bucket->get_name()).'|'.$Bucket->get_name();
			}
		}

		$bucket_opts = implode(',',	$opts);

		$default_fields .= '<perch:shop id="save" type="select" label="'.$l8.'" options="'.$bucket_opts.'" required="true" />';
	}else{
		$default_fields .= '<perch:shop id="save" type="editcontrol" value="download" default="download" edit-control="true" />';
	}
	
	$message = false;
	$details = array();

	$Template   = $API->get('Template');
	$Template->set_from_string($default_fields, 'shop');

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);
    $Form->set_required_fields_from_template($Template, $details);

    if ($Form->submitted()) {		
    	
    	$data = $Form->get_posted_content($Template, $OrdersExport);

    	//PerchUtil::debug($data,'success');

    	$OrdersExport->populate($data);
    	$OrdersExport->export();

    } 

    if (PerchDB::$driver!='PDO') {
    	$message = $HTML->failure_message('Export requires the PHP PDO library for connecting to your database.');
    }
<?php

class PerchMailChimp_List extends PerchAPI_Base
{
	protected $factory_classname = 'PerchMailChimp_Lists';
	protected $table             = 'mailchimp_lists';
	protected $pk                = 'listID';

	protected $modified_date_column = 'listUpdated';
}
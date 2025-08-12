<?php

class PerchEmailOctopus_List extends PerchAPI_Base
{
	protected $factory_classname = 'PerchEmailOctopus_Lists';
	protected $table             = 'emailoctopus_lists';
	protected $pk                = 'listID';

	protected $modified_date_column = 'listUpdated';
}

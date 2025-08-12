<?php

class PerchUserLogs extends PerchFactory
{
	protected $singular_classname = 'PerchUserLog';
    protected $table    = 'user_log';
    protected $pk       = 'logID';
    protected $default_sort_column = 'logTime';
    protected $created_date_column = 'logCreated';

    static $user_logs = array();


}

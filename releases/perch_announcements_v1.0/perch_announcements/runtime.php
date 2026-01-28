<?php

	//include(__DIR__.'/fieldtypes.php');

    spl_autoload_register(function($class_name){
        if (strpos($class_name, 'PerchAnnouncements')===0) {
            include(PERCH_PATH.'/addons/apps/perch_announcements/lib/'.$class_name.'.class.php');
            return true;
        }

        return false;
    });
    function perch_by_announcement($id, $return=false)
                {
                    $id = rtrim($id, '/');

                    $opts = array(

                        'template' => 'announcement.html'
                        );

                    if (is_numeric($id)) {
                        $opts['_id'] = intval($id);
                    }

                    $r = perch_announcement($opts, $return);
                    if ($return) return $r;
                    echo $r;
                }

    function perch_announcements_custom($opts=false, $return=false)
    {
        return perch_announcement($opts, $return);
    }

    function perch_announcement($opts=false, $return=false)
    {
        $default_opts = array(
            'skip-template'        => false,
            'split-items'          => false,
            'filter'               => false,
            'paginate'             => true,
            'template'             => false,
        );

        if (is_array($opts)) {
            $opts = array_merge($default_opts, $opts);
        }else{
            $opts = $default_opts;
        }

        if (isset($opts['data'])) PerchSystem::set_vars($opts['data']);

        if ($opts['skip-template'] || $opts['split-items']) $return = true;

        $API  = new PerchAPI(1.0, 'perch_announcements');

           $Announcements = new PerchAnnouncements_Announcements($API);



        if (isset($opts['pagination_var'])) $opts['pagination-var'] = $opts['pagination_var'];

        $r = $Announcements->get_custom($opts);

    	if ($return) return $r;

    	echo $r;
    }

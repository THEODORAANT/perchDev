<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	//include(__DIR__.'/fieldtypes.php');

    if (!function_exists('perch_members_init')) {
        die('Please ensure that the Members app is installed and appears before the Events app in your config/apps.php file.');
    }
    spl_autoload_register(function($class_name){
        if (strpos($class_name, 'PerchTwillio')===0) {
            include(PERCH_PATH.'/addons/apps/perch_twillio/lib/'.$class_name.'.class.php');
            return true;
        }

        return false;
    });


function perch_twillio_is_customerphone_registered()
                {
                   	$API  = new PerchAPI(1.0, 'perch_twillio');
                           		$Twillio_Runtime = PerchTwillio_Runtime::fetch();
                           		return  $Twillio_Runtime->is_customerphone_registered();
                }
     function perch_twillio_customer_verified()
        {
           	$API  = new PerchAPI(1.0, 'perch_twillio');
                   		$Twillio_Runtime = PerchTwillio_Runtime::fetch();
                   		return  $Twillio_Runtime->is_customer_verified();
        }
    function perch_twillio_form_handler($SubmittedForm)
        {

        		$API  = new PerchAPI(1.0, 'perch_twillio');
        		$Twillio_Runtime = PerchTwillio_Runtime::fetch();

        		switch($SubmittedForm->formID) {

                    case 'verify':
                        $Twillio_Runtime->verified_customer($SubmittedForm);
                        break;
                    case 'register':
                        $Twillio_Runtime->register_customer_from_form($SubmittedForm);
                        break;
                     case 'confirm_phone':
                        $Twillio_Runtime->update_customer_from_form($SubmittedForm);
                        break;
        		}



            $Perch = Perch::fetch();
            $errors = $Perch->get_form_errors($SubmittedForm->formID);
            if ($errors) PerchUtil::debug($errors);
        }
	function verify_customer_from_form($opts=array(), $return=false)
	{
		$API  = new PerchAPI(1.0, 'perch_twillio');

        $defaults = array();
        $defaults['template']        = 'messages/verify_customer.html';

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }

        $Template = $API->get('Template');
       $Template->set($opts['template'], 'twillio');
        $html = $Template->render(array());
        $html = $Template->apply_runtime_post_processing($html);

        if ($return) return $html;
        echo $html;

	}

    function perch_twillio_customer_confirmPhone_form($opts=array(), $return=false)
    {
        $API  = new PerchAPI(1.0, 'perch_twillio');

        $defaults = [];
        $defaults['template'] = 'messages/customer_confirm_phone.html';

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }

        $Twillio_Runtime = PerchTwillio_Runtime::fetch();

       // PerchSystem::set_var('country_list', PerchShop_Countries::get_list_options());

        $Session = PerchMembers_Session::fetch();

        $data = $Session->to_array();
         $data["return_url"]=$opts["return_url"];
        $data = array_merge($data, $Twillio_Runtime->get_customer_details());

        $Template = $API->get('Template');
        $Template->set($opts['template'], 'twillio');

        $html = $Template->render($data);
        $html = $Template->apply_runtime_post_processing($html, $data);

        if ($return) return $html;
        echo $html;

    }

function perch_twillio_registration_form($opts=array(), $return=false)
	{
		$API  = new PerchAPI(1.0, 'perch_twillio');

        $defaults = [];
        $defaults['template'] = 'messages/customer_create.html';

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }


        $Template = $API->get('Template');
        $Template->set($opts['template'], 'twillio');
        $html = $Template->render(array());
        $html = $Template->apply_runtime_post_processing($html);

        if ($return) return $html;
        echo $html;

	}

    function perch_twillio_login_form($template=null, $return=false)
    {

        if (is_null($template)) {
              $template = '~perch_twillio/templates/messages/customer_login.html';
        }


        return perch_member_form($template, $return);
    }
  include(__DIR__.'/events.php');
    ?>

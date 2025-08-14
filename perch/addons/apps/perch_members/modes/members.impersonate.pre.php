<?php

    $Members = new PerchMembers_Members($API);
    $Auth    = new PerchMembers_Auth($API);
    $HTML    = $API->get('HTML');

    $message = false;

    if (PerchRequest::get('end')) {
        $Auth->log_out();
        $message = $HTML->success_message('Impersonation ended. <a href="'.$API->app_path().'/">Return to admin</a>');
    } else {

        if (!$CurrentUser->has_priv('perch_members.impersonate')) {
            PerchUtil::redirect($API->app_path());
        }

        $memberID = PerchRequest::get('id');

        if ($memberID) {
            $Member = $Members->find($memberID);
        }

        if (!is_object($Member)) {
            PerchUtil::redirect($API->app_path());
        }

        $Auth->refresh_session_data($Member);

        $log_line = sprintf('[%s] Admin %d impersonated member %d'."\n", date('Y-m-d H:i:s'), $CurrentUser->id(), $Member->id());
        $log_file = PERCH_PATH.'/addons/apps/perch_members/impersonate.log';
        file_put_contents($log_file, $log_line, FILE_APPEND);

        PerchUtil::redirect('/shop/index.php');
    }


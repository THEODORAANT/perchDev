<?php

class PerchTwillio_Message extends PerchAPI_Base
{
    protected $table  = 'twillio_messages';
    protected $pk     = 'messageID';

    private $tmp_url_vars = array();


    public function update($data)
    {

        $PerchTwillio_Messages = new PerchTwillio_Messages();


        // Update the event itself
        parent::update($data);


 		return true;
    }

    public function delete()
    {
        parent::delete();

    }

    public function date()
    {
        return date('Y-m-d', strtotime($this->messageDateTime()));
    }


    private function substitute_url_vars($matches)
    {
        $url_vars = $this->tmp_url_vars;
        if (isset($url_vars[$matches[1]])){
            return $url_vars[$matches[1]];
        }
    }

}

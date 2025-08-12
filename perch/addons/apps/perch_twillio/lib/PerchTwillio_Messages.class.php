<?php
use Twilio\Rest\Client;

class PerchTwillio_Messages  extends PerchTwillio_Factory
{
    protected $table     = 'twillio_messages';
	protected $pk        = 'messageID';
	protected $singular_classname = 'PerchTwillio_Message';

	protected $default_sort_column = 'messageDateTime';
    protected $created_date_column = 'messageDateTime';

	public $static_fields   = array('messageText',  'messageDateTime','sendDateTime');



    public static function check_phone($phone)
    {    $API  = new PerchAPI(1.0, 'perch_twillio');
       // $first_character = mb_substr($phone, 0, 1);
        $minDigits = 9;
        $maxDigits = 14;
        if(!preg_match('/^[0-9]{'.$minDigits.','.$maxDigits.'}\z/', $phone)){
        return false;
        }
          return true;
   }
    /**
     * get the list of events with a date of today or greater to display int he admin area.
     */
    public function all($Paging=false, $future=true)
    {

        if ($Paging && $Paging->enabled()) {
            $sql = $Paging->select_sql();
        }else{
            $sql = 'SELECT';
        }

        $sql .= ' *
                FROM '.$this->table;

     /*   if ($future) {
           // $sql .= ' WHERE messageDateTime>='.$this->db->pdb(date('Y-m-d 00:00:00'));
          $sql .= ' WHERE messageDateTime>='.$this->db->pdb(date('Y-m-d 00:00:00')).' OR messageDateTime>='.$this->db->pdb(date('Y-m-d 00:00:00'));

        }else{
            $sql .= ' WHERE messageDateTime<='.$this->db->pdb(date('Y-m-d 00:00:00'));
        }*/

        $sql .= ' ORDER BY '.$this->default_sort_column;

        if (!$future) {
            $sql  .= ' DESC';
        }


        if ($Paging && $Paging->enabled()) {
            $sql .=  ' '.$Paging->limit_sql();
        }

        $results = $this->db->get_rows($sql);

        if ($Paging && $Paging->enabled()) {
            $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
        }

        return $this->return_instances($results);
    }
	public static function register_member_login($Event)
    	{
    		$TwillioRuntime = PerchTwillio_Runtime::fetch();
    		$TwillioRuntime->register_member_login($Event);
    	}

   public function sendWhatsAppWithTwillio($body,$to)
{

	$TwilioAPI = $this->get_api_instance();


try{


  $message = $TwilioAPI->messages
          ->create("whatsapp:+35799838711",//.$to, // to
            array(
              "from" => "whatsapp:".PERCH_TWILLIO_FROM,
           // "body" => "Your appointment is coming up on July 21 at 3PM"
              "body" => $body
            )
          );
        sleep(10);
        $latest_message = $TwilioAPI->messages($message->sid)->fetch();

 // echo "result";
 //   print_r($latest_message);


            return $latest_message->status;
            }catch(Exception $e) {
            return "serverfailed";
            }
}

	public function sendWithTwillio($body,$to)
	{


	$TwilioAPI = $this->get_api_instance();


try{

$message = $TwilioAPI->messages->create(
             // Where to send a text message (your cell phone?)
             $to,//'+35799838711',
             array(
                 'from' => PERCH_TWILLIO_FROM,
                 'body' =>$body
             )
         );


     //   sleep(10);
        $latest_message = $TwilioAPI->messages($message->sid)->fetch();

 // echo "result";
   // print_r($latest_message);


            return $latest_message->status;
            }catch(Exception $e) {
            return "serverfailed";
            }
	}

	}
    ?>

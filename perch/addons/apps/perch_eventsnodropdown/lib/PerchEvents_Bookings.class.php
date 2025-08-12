<?php

class PerchEvents_Bookings extends PerchEvents_Factory
{
    protected $table     = 'events_bookings';
	protected $pk        = 'bookingID';
	protected $singular_classname = 'PerchEvents_Booking';

	protected $default_sort_column = 'bookingDate';

    //public $static_fields   = array('bookingID', 'dayID', 'slotID', 'eventID', 'bookingDate', 'time', 'status');

    public $static_fields   =array('date','time','status');

    /**
     * Find a category by its categorySlug
     *
     * @param string $id
     * @return void
     * @author Theodora
     */
    public function find_by_id($id)
    {
        $sql    = 'SELECT *
                    FROM ' . $this->table . '
                    WHERE bookingID='. $this->db->pdb($id) .'
                    LIMIT 1';

        $result = $this->db->get_row($sql);

        if (is_array($result)) {
            return new $this->singular_classname($result);
        }

        return false;
    }

   public function all($Paging = false){
       $sql = 'SELECT b.*,e.eventTitle
   	            FROM '.$this->table.' b, '.PERCH_DB_PREFIX.'events e
   	            WHERE b.eventID=e.eventID'  ;
   	    $rows   = $this->db->get_rows($sql);

   	    return $this->return_instances($rows);

   }

	public function get_for_event($eventID)
	{
	    $sql = 'SELECT b.*
	            FROM '.$this->table.' b, '.PERCH_DB_PREFIX.'events e
	            WHERE b.eventID=e.eventID
	                AND e.eventID='.$this->db->pdb($eventID);
	    $rows   = $this->db->get_rows($sql);

	    return $this->return_instances($rows);
	}


}


?>

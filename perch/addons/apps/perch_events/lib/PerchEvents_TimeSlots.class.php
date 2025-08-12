<?php

class PerchEvents_TimeSlots extends PerchAPI_Factory
{
    protected $table     = 'events_timeslots';
    protected $daystable     = 'events_days';
	protected $pk        = 'slotID';
	protected $singular_classname = 'PerchEvents_TimeSlot';

	protected $default_sort_column = 'startDate';

    public $static_fields   = array('startDate', 'endDate', 'slot_duration');


    /**
     * Find a category by its categorySlug
     *
     * @param string $slug
     * @return void
     * @author Drew McLellan
     */
    public function find_by_slotID($slotID)
    {
        $sql    = 'SELECT *
                    FROM ' . $this->table . '
                    WHERE slotID='. $this->db->pdb($slotID) .'
                    LIMIT 1';

        $result = $this->db->get_row($sql);

        if (is_array($result)) {
            return new $this->singular_classname($result);
        }

        return false;


    }

        /**
         * Find a all slots
         *
         * @param string $slug
         * @return void
         * @author Drew McLellan
         */
        public function find_all()
        {
           /* $sql    = 'SELECT *
                        FROM ' . $this->table . ' as t, '.PERCH_DB_PREFIX.'events_timeslots_perday as d
                        WHERE t.slotID=d.slotID  GROUP BY t.slotID ';*/

            $sql    = 'SELECT *
                        FROM ' . $this->table ;
             $rows   = $this->db->get_rows($sql);

             $content = array();

             if (PerchUtil::count($rows)) {

                    foreach($rows as $day) $content[] = $day;
              }
           return $content;

      	    //return $this->return_instances($rows);


        }

    public function get_days()
    {
    	    $sql = 'SELECT *
    	            FROM '.PERCH_DB_PREFIX.$this->daystable.' ORDER BY dayID asc';


    	    $rows   = $this->db->get_rows($sql);

    	    		$content = array();

                    if (PerchUtil::count($rows)) {
                        foreach($rows as $day) $content[] = $day;
                    }
        return $content;

    }

    	/**
    	* takes the event slot and inserts it as a new row in the database.
    	*/
        public function create($data)
        {


            if (isset($data['day_ids']) && is_array($data['day_ids'])) {
                $day_ids = $data['day_ids'];
            }else{
                $day_ids = false;
            }

            unset($data['day_ids']);
            if (isset($data['startDate_hour']) && isset($data['startDate_minute'])) {

             $starttime = strtotime($data['startDate_hour'] . ':' . $data['startDate_minute'] . ':00');
             $data['startDate']=date('H:i:s', $starttime);
              unset($data['startDate_hour']);
              unset($data['startDate_minute']);
             }
             if (isset($data['endDate_hour']) && isset($data['endDate_minute'])) {
              $endtime = strtotime($data['endDate_hour'] . ':' . $data['endDate_minute'] . ':00');
               $data['endDate']=date('H:i:s', $endtime);
               unset($data['endDate_hour']);
               unset($data['endDate_minute']);
             }


            $slotID = $this->db->insert($this->table, $data);


    		if ($slotID) {
    			if(is_array($day_ids)) {
    				for($i=0; $i<sizeOf($day_ids); $i++) {
    				    $tmp = array();
    				    $tmp['slotID'] = $slotID;
    				    $tmp['dayID'] = $day_ids[$i];
    				    $this->db->insert(PERCH_DB_PREFIX.'events_timeslots_perday', $tmp);
    				}
    			}

                return $this->find($slotID);
    		}
            return false;
    	}



    /*
        Get a single slot by its ID
    */
    public function find($slotID) {
		$sql = 'SELECT * FROM '.PERCH_DB_PREFIX.'events_timeslots WHERE slotID = '.$this->db->pdb($slotID);

		$row = $this->db->get_row($sql);

		if(is_array($row)) {
			$sql = 'SELECT dayID FROM '.PERCH_DB_PREFIX.'events_timeslots_perday WHERE slotID = '.$this->db->pdb($slotID);
			$result = $this->db->get_rows($sql);
			$a = array();
			if(is_array($result)) {
				foreach($result as $day_row) {
					$a[] = $day_row['dayID'];
				}
			}
			$row['day_ids'] = $a;
		}

		return $this->return_instance($row);
	}


}


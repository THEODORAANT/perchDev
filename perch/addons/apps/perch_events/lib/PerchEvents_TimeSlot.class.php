<?php

class PerchEvents_TimeSlot extends PerchAPI_Base
{
   protected $table  = 'events_timeslots';
   protected $pk     = 'slotID';

  private $tmp_url_vars = array();


  public function update($data)
  {

           $PerchEvents_TimeSlots = new PerchEvents_TimeSlots();

            if (isset($data['day_ids'])) {
              $dayIDs = $data['day_ids'];
               unset($data['day_ids']);
             }else{
                $dayIDs = false;
            }

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

            parent::update($data);

           // Delete existing days
           $this->db->delete(PERCH_DB_PREFIX.'events_timeslots_perday', $this->pk, $this->id());

           // Add new days
           if (is_array($dayIDs)) {
               for($i=0; $i<sizeOf($dayIDs); $i++) {
                    $tmp = array();
                    $tmp['slotID'] = $this->id();
                    $tmp['dayID'] = $dayIDs[$i];
                    $this->db->insert(PERCH_DB_PREFIX.'events_timeslots_perday', $tmp);
               }
           	}
        return true;
     }

     public function delete()
     {
        parent::delete();
       $this->db->delete(PERCH_DB_PREFIX.'events_timeslots_perday', $this->pk, $this->id());
     }




 }

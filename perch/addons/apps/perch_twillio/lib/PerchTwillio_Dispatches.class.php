<?php

class PerchTwillio_Dispatches  extends PerchTwillio_Factory
{
    protected $table     = 'twillio_dispatches';
	protected $pk        = 'dispatchID';
	protected $singular_classname = 'PerchTwillio_Dispatch';

	protected $default_sort_column = 'dispatchDateTime';
    protected $created_date_column = 'dispatchDateTime';

	public $static_fields   = array( 'messageID','status', 'dispatchDateTime','customersfile');


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

        if ($future) {
           // $sql .= ' WHERE eventDateTime>='.$this->db->pdb(date('Y-m-d 00:00:00'));
          $sql .= ' WHERE dispatchDateTime>='.$this->db->pdb(date('Y-m-d 00:00:00')).' OR dispatchDateTime>='.$this->db->pdb(date('Y-m-d 00:00:00'));

        }else{
            $sql .= ' WHERE dispatchDateTime<='.$this->db->pdb(date('Y-m-d 00:00:00'));
        }

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




	}
    ?>

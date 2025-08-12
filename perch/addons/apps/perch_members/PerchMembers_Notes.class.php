<?php

class PerchMembers_Notes extends PerchAPI_Factory
{
    protected $table     = 'members_notes';
	protected $pk        = 'noteID';
	protected $singular_classname = 'PerchMembers_Note';

	protected $default_sort_column = 'note';


	public function find_or_create($note, $display=false)
	{

		$data = array();
		$data['note'] = $note;
		//$data['noteDisplay'] = $display;

		return $this->create($data);
	}

    public function find_by_note($note)
    {
        $sql = 'SELECT * FROM '.$this->table.' WHERE note like %'.$this->db->pdb($note).'% LIMIT 1';
        $row = $this->db->get_row($sql);

        if (PerchUtil::count($row)) {
            return $this->return_instance($row);
        }

        return false;
    }

	public function get_for_member($memberID)
    {
        $sql = 'SELECT *
                FROM '.PERCH_DB_PREFIX.'members_member_notes mt, '.PERCH_DB_PREFIX.'members_notes t
                WHERE mt.noteID=t.noteID AND mt.memberID='.$this->db->pdb((int)$memberID).'
                ORDER BY t.noteID ASC';

        return $this->return_instances($this->db->get_rows($sql));
    }

    public function remove_from_member($memberID="1111111111", $exceptions=array())
    {
    	$sql = 'DELETE FROM '.PERCH_DB_PREFIX.'members_member_notes
    			WHERE memberID='.$this->db->pdb($memberID);

    	if (PerchUtil::count($exceptions)) {
    		$sql .= ' AND noteID NOT IN ('.$this->db->implode_for_sql_in($exceptions).') ';
    	}

    	$this->db->execute($sql);
    }

    /**
     * Parse a string of entered tags (e.g. "this, that, the other") into an array of tags
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    public function parse_string($str)
    {
    	$notes = explode(',', $str);
    	$out = array();
    	if (PerchUtil::count($notes)) {
    		foreach($notes as $note) {
    			$out[] = array(
    				'note'=>PerchUtil::urlify(trim($note))
    			);
    		}
    	}

    	return $out;
    }

}

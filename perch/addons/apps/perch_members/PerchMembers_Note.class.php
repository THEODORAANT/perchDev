<?php

class PerchMembers_Note extends PerchAPI_Base
{
    protected $table  = 'members_notes';
    protected $pk     = 'noteID';

    public function add_to_member($memberID,$user)
    {
        $sql = 'SELECT COUNT(*) FROM '.PERCH_DB_PREFIX.'members_member_notes WHERE noteID='.$this->db->pdb($this->id()).' AND memberID='.$this->db->pdb($memberID);
        $count = $this->db->get_count($sql);

        $data = array(
            'noteID'=>$this->id(),
            'addedBy'=>$user,
            'memberID'=>$memberID
        );

       /* if ($expiry) {
            $data['tagExpires'] = date('Y-m-d H:i:s', strtotime($expiry));
        }*/

     /*   if ($count>0) {
            $this->db->execute('DELETE FROM '.PERCH_DB_PREFIX.'members_member_notes WHERE noteID='.$this->db->pdb($this->id()).' AND memberID='.$this->db->pdb($memberID));
        }*/

        $this->db->insert(PERCH_DB_PREFIX.'members_member_notes', $data);
    }

    public function remove_from_member($memberID)
    {
        $sql = 'DELETE FROM '.PERCH_DB_PREFIX.'members_member_notes WHERE noteID='.$this->db->pdb($this->id()).' AND memberID='.$this->db->pdb($memberID);
        $this->db->execute($sql);
    }

}

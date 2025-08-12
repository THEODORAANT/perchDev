<?php

class PerchMembers_Documents extends PerchAPI_Factory
{
    protected $table     = 'members_documents';
	protected $pk        = 'documentID';
	protected $singular_classname = 'PerchMembers_Document';

	protected $default_sort_column = 'date';
	public $static_fields = array('documentID', 'memberID', 'documentName', 'documenUploadDate');
    public     $default_fields = '
    				<perch:members type="file" id="documentFile" required="true" label="Email" listing="true" order="98" />
                    <perch:members type="date" id="documenUploadDate" required="true" label="Joined" listing="true" format="d F Y" order="99" />
                    ';

   	public function get_document($memberID,$documentID)
       {
           $sql = 'SELECT d.*
                   FROM  '.PERCH_DB_PREFIX.'members_documents d
                   WHERE d.documentID='.$this->db->pdb((int)$documentID).' AND d.memberID='.$this->db->pdb((int)$memberID).'
                   LIMIT 1';

           return $this->db->get_row($sql);
       }


    public function delete_passed_files(){
        $sql = 'SELECT d.*
                    FROM  '.PERCH_DB_PREFIX.'members_documents d
                    WHERE DATEDIFF( CURDATE(),  d.documenUploadDate ) > 15 AND documentDeleted="0"
                    ORDER BY d.documenUploadDate';

        $rows = $this->db->get_rows($sql);

        $target_dir = __DIR__."/documents/";
        if (PerchUtil::count($rows)) {
          foreach($rows as $row) {
          $filename= $target_dir.$row['documentName'];
          if (!unlink($filename)) {
               $log  ="$filename cannot be deleted due to an error";
           }
           else {
                $log  ="$filename has been deleted";
            }
              $log  .= implode("-",$row);

               $doc = array(
                  'documentDeleted'=>'1'
                );
                $updatedata=$row;
                $pk =$row['documentID'];
                //echo  $pk;
                $updatedata[ 'documentDeleted']='1';
                $r = $this->db->update($this->table, $updatedata, $this->pk, $pk );
               // print_r( $r);


            $log_filename = $_SERVER['DOCUMENT_ROOT']."/logs";
            if (!file_exists($log_filename))
            {
                // create directory/folder uploads.
               mkdir($log_filename, 0777, true);
             }
              $log_file_data = $log_filename.'/log_' . date('d-M-Y') . '.log';
               file_put_contents( $log_file_data , $log, FILE_APPEND);
           }
        }
        return true;
    }

	public function get_for_member($memberID)
    {
        $sql = 'SELECT d.*
                FROM  '.PERCH_DB_PREFIX.'members_documents d
                WHERE d.memberID='.$this->db->pdb((int)$memberID).'
                ORDER BY d.documenUploadDate DESC';

        return $this->return_instances($this->db->get_rows($sql));
    }

	public function upload($file,$memberID)
	{

      	$target_dir = __DIR__."/documents/";
      	$filef =  $file['name'];
      	$path = pathinfo($filef);
      	$filename = $path['filename'];
      	$ext = $path['extension'];
      	$temp_name = $file['tmp_name'];
      	$path_filename_ext = $target_dir.$filename.".".$ext;

      // Check if file already exists
      if (file_exists($path_filename_ext)) {
       echo "Sorry, file already exists.";
       }else{
       move_uploaded_file($temp_name,$path_filename_ext);
       //echo "Congratulations! File Uploaded Successfully.";
       //echo  $HTML->success_message('Congratulations! File Uploaded Successfully');
       }
		// Tag wasn't found, so create a new one and return it.
        //'documentID', 'memberID', 'documentName', 'documenUploadDate');
		$data = array();
		$data['memberID'] =$memberID;
		$data['documentName'] =$filename.".".$ext;
		$data['documenUploadDate'] = date('Y-m-d H:i:s');
        $this->create($data);
		}


}

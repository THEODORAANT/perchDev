<?php

class PerchContent_Languages extends PerchFactory
 {
    protected $singular_classname  = 'PerchContent_Language';
    protected $table               = 'content_languages';
    protected $pk                  = 'languageID';


    public function find_all(){
    $sql = 'SELECT *
       	            FROM '.$this->table ;
   $rows   = $this->db->get_rows($sql);

   return $this->return_instances($rows);

    }
/**
	* takes the event data and inserts it as a new row in the database.
	*/
    public function create_update($data)
    {

     if (isset($data['web_languages']) && is_array($data['web_languages'])) {
                $web_languages = $data['web_languages'];
            }else{
                $web_languages = false;
            }

	    	$sql = 'DELETE FROM '.PERCH_DB_PREFIX.'content_languages ';
	    	$this->db->execute($sql);
            if(is_array($web_languages)) {
            				foreach($web_languages as $lang=>$name)  {
            				    $tmp = array();
            				    $tmp['lang'] = $name;
            				    $tmp['name'] = $name;
                                $tmp['active'] = 1;
            				 // echo "create";print_r($tmp);
                                	 parent::create($tmp);
            				}

           }



    }

    }
    ?>

<?php


class PerchAnnouncements_Announcements  extends PerchAnnouncements_Factory
{
    protected $table     = 'announcements';
	protected $pk        = 'announcementID';
	protected $singular_classname = 'PerchAnnouncements_Announcement';

	protected $default_sort_column = 'announcementCreatedDate';
    protected $created_date_column = 'announcementCreatedDate';

	public $static_fields   = array(  'announcementCreatedDate');


/**
    	* takes the event data and inserts it as a new row in the database.
    	*/
        public function create($data)
        {
          /*  if(isset($data['announcementContent'])) {
            	$data['announcementHTML'] = $this->text_to_html($data['announcementContent']);
            }else{
            	$data['announcementDescHTML'] = false;
            }*/




            $announcementID = $this->db->insert($this->table, $data);



                return $this->find($announcementID);

    	}

    	 private function _standard_pre_template_callback($opts)
            {

                return function($items) use ($opts) {
                    if (isset($opts['include-meta'])) {
                        $domain = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
                        if (PerchUtil::count($items)) {
                            foreach($items as &$item) {
                                $item['domain'] = $domain;
                            }
                        }
                    }
                    return $items;
                };
            }
    	    private function _standard_where_callback($opts)
            {


                $db = $this->db;

                return function(PerchQuery $Query) use ($opts, $db) {


                    // blog
                    if (isset($opts['announcementID'])) {
                        $Query->where[] = ' announcementID='.(int)$opts['announcementID'].' ';
                    }







                    return $Query;

                };


            }

     public function get_custom($opts)
       {
           $announcements = array();
           $Announcement = false;
           $single_mode = false;
           $where = array();
           $order = array();
           $limit = '';


           // find specific _id
   	    if (isset($opts['_id'])) {
   	        $single_mode = true;
   	        $Announcement = $this->find($opts['_id']);
   	    }else{

   	        // if not picking an _id, check for a filter
   	        if (isset($opts['filter']) && is_array( $opts['filter']) ) {



   	            $keys = $opts['filter'];
   	            foreach($keys  as $key=> $kvalue){
   	            $raw_value = $kvalue;



   	            $match = 'eq';
   	            if(is_array( $raw_value)){
   	         //   $value = $this->db->pdb($kvalue);
   	               $match = 'between';
   	            }else{
   	            $value = $this->db->pdb($kvalue);
   	            }

                   switch ($match) {
                       case 'eq':
                       case 'is':
                       case 'exact':
                           $where[] = $key.'='.$value;
                           break;
                       case 'neq':
                       case 'ne':
                       case 'not':
                           $where[] = $key.'!='.$value;
                           break;
                       case 'gt':
                           $where[] = $key.'>'.$value;
                           break;
                       case 'gte':
                           $where[] = $key.'>='.$value;
                           break;
                       case 'lt':
                           $where[] = $key.'<'.$value;
                           break;
                       case 'lte':
                           $where[] = $key.'<='.$value;
                           break;
                       case 'contains':
                           $v = str_replace('/', '\/', $raw_value);
                           $where[] = $key." REGEXP '[[:<:]]'.$v.'[[:>:]]'";
                           break;
                       case 'regex':
                       case 'regexp':
                           $v = str_replace('/', '\/', $raw_value);
                           $where[] = $key." REGEXP '".$v."'";
                           break;
                       case 'between':
                       case 'betwixt':
                           $vals  = $raw_value;//explode(',', $raw_value);
                           if (PerchUtil::count($vals)==2) {
                               $where[] = $key.'>'.trim($this->db->pdb($vals[0]));
                               $where[] = $key.'<'.trim($this->db->pdb($vals[1]));
                           }
                           break;
                       case 'eqbetween':
                       case 'eqbetwixt':
                           $vals  = explode(',', $raw_value);
                           if (PerchUtil::count($vals)==2) {
                               $where[] = $key.'>='.trim($this->db->pdb($vals[0]));
                               $where[] = $key.'<='.trim($this->db->pdb($vals[1]));
                           }
                           break;
                       case 'in':
                       case 'within':
                           $vals  = explode(',', $raw_value);
                           $tmp = array();
                           if (PerchUtil::count($vals)) {
                               foreach($vals as $value) {
                                   if ($item[$key]==trim($value)) {
                                       $tmp[] = $item;
                                       break;
                                   }
                               }
                               $where[] = $key.' IN '.$this->implode_for_sql_in($tmp);

                           }
                           break;
                           }
                   }
   	        }
   	    }

   	    // sort
   	    if (isset($opts['sort'])) {
   	        $desc = false;
   	        if (isset($opts['sort-order']) && $opts['sort-order']=='DESC') {
   	            $desc = true;
   	        }else{
   	            $desc = false;
   	        }
   	        $order[] = $opts['sort'].' '.($desc ? 'DESC' : 'ASC');
   	    }

   	    if (isset($opts['sort-order']) && $opts['sort-order']=='RAND') {
               $order[] = 'RAND()';
           }

   	    // limit
   	    if (isset($opts['count'])) {
   	        $count = (int) $opts['count'];

   	        if (isset($opts['start'])) {
                   $start = (((int) $opts['start'])-1). ',';
   	        }else{
   	            $start = '';
   	        }

   	        $limit = $start.$count;
   	    }

   	    if ($single_mode){
   	        $announcements = array($Announcement);
   	    }else{

       	    $sql = 'SELECT DISTINCT e.*  FROM '.$this->table.' e ';


       	    if (count($where)) {
       	        $sql .= ' WHERE ' . implode(' AND ', $where);
       	    }

       	    if (count($order)) {
       	        $sql .= ' ORDER BY '.implode(', ', $order);
       	    }

       	    if ($limit!='') {
       	        $sql .= ' LIMIT '.$limit;
       	    }


       	    $rows    = $this->db->get_rows($sql);

       	    $announcements  = $this->return_instances($rows);





           }


           if (isset($opts['skip-template']) && $opts['skip-template']==true) {

               if ($single_mode) return $Announcement;

               $out = array();
               if (PerchUtil::count($announcements)) {
                   foreach($announcements as $Announcement) {
                       $out[] = $Announcement->to_array();
                   }
               }

               return $out;
   	    }


   	    // template
   	    if (isset($opts['template'])) {

               $template = 'announcements/'.str_replace('announcements/', '', $opts['template']);
   	    }else{
   	        $template = 'announcements/announcement.html';
   	    }

   	    $Template = $this->api->get("Template");
   	    $Template->set($template, 'announcements');

           if (PerchUtil::count($announcements)) {
               $html = $Template->render_group($announcements, true);
           }else{
               $Template->use_noresults();
               $html = $Template->render(array());
           }


   	    return $html;
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





	}
    ?>

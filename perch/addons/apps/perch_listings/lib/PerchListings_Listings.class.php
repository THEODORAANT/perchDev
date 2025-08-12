<?php

class PerchListings_Listings  extends PerchListings_Factory
{
    protected $table     = 'listings_listings';
	protected $pk        = 'listingID';
	protected $singular_classname = 'PerchListings_Listing';

	protected $default_sort_column = 'listingCreated';
    protected $created_date_column = 'listingCreated';

	public $static_fields   = array('listingTitle', 'listingDescription' , 'listingPrice', 'listingSize','listingLocation' ,'listingDescRaw', 'listingDescHTML', 'listingCreated','cat_ids');
   /*
        Get a single listing by its ID
    */
    public function find($listingID) {
		$sql = 'SELECT * FROM '.PERCH_DB_PREFIX.'listings_listings WHERE listingID = '.$this->db->pdb($listingID);

		$row = $this->db->get_row($sql);

		if(is_array($row)) {
			$sql = 'SELECT categoryID FROM '.PERCH_DB_PREFIX.'listings_to_categories WHERE listingID = '.$this->db->pdb($listingID);
			$result = $this->db->get_rows($sql);
			$a = array();
			if(is_array($result)) {
				foreach($result as $cat_row) {
					$a[] = $cat_row['categoryID'];
				}
			}
			$row['cat_ids'] = $a;


		}

		return $this->return_instance($row);
	}
	  private function implode_for_sql_in($rows)
        {
            foreach($rows as &$item) {
                $item = $this->db->pdb($item);
            }

            return implode(', ', $rows);
        }
/*
        Get a single listing by its Slug
    */
    public function find_by_slug($listingSlug)
    {

		$sql = 'SELECT * FROM '.PERCH_DB_PREFIX.'listings_listings WHERE listingSlug = '.$this->db->pdb($listingSlug);

		$row = $this->db->get_row($sql);

		if(is_array($row)) {
			$sql = 'SELECT categoryID FROM '.PERCH_DB_PREFIX.'listings_to_categories WHERE listingID = '.$this->db->pdb($row['listingID']);
			$result = $this->db->get_rows($sql);
			$a = array();
			if(is_array($result)) {
				foreach($result as $cat_row) {
					$a[] = $cat_row['categoryID'];
				}
			}
			$row['cat_ids'] = $a;
		}

		return $this->return_instance($row);

    }


    	/**
    	* takes the event data and inserts it as a new row in the database.
    	*/
        public function create($data)
        {
          /*  if(isset($data['listingDescription'])) {
            	$data['listingDescHTML'] = $this->text_to_html($data['listingDescription']);
            }else{
            	$data['listingDescHTML'] = false;
            }*/

            if (isset($data['listingTitle'])) {
                $data['listingSlug'] = PerchUtil::urlify(date('Y m d', strtotime($data['listingCreated'])). ' ' . $data['listingTitle']);
            }

            if (isset($data['cat_ids']) && is_array($data['cat_ids'])) {
                $cat_ids = $data['cat_ids'];
            }else{
                $cat_ids = false;
            }

            unset($data['cat_ids']);

            $listingID = $this->db->insert($this->table, $data);

    		if ($listingID) {
    			if(is_array($cat_ids)) {
    				for($i=0; $i<sizeOf($cat_ids); $i++) {
    				    $tmp = array();
    				    $tmp['listingID'] = $listingID;
    				    $tmp['categoryID'] = $cat_ids[$i];
    				    $this->db->insert(PERCH_DB_PREFIX.'listings_to_categories', $tmp);
    				}
    			}


                return $this->find($listingID);
    		}
            return false;
    	}
  public function get_custom($opts)
    {
        $listings = array();
        $Listing = false;
        $single_mode = false;
        $where = array();
        $order = array();
        $limit = '';


        // find specific _id
	    if (isset($opts['_id'])) {
	        $single_mode = true;
	        $Listing = $this->find($opts['_id']);
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
	        $listings = array($Listing);
	    }else{
	     $API  = new PerchAPI(1.0, 'perch_listings');
	      $Settings = $API->get('Settings');

             $lcurrency=$Settings->get('perch_listings_price_currency')->val();

    	    $sql = 'SELECT DISTINCT e.*,"'.$lcurrency.'" as listingCurrency   FROM '.$this->table.' e ';

    	    // categories
    	    if (isset($opts['category'])) {
    	        $cats = $opts['category'];
    	        if (!is_array($cats)) $cats = array($cats);


    	        if (is_array($cats)) {
            	    $sql = 'SELECT DISTINCT e.*
            	            FROM '.$this->table.' e, '.PERCH_DB_PREFIX.'listings_to_categories e2c, '.PERCH_DB_PREFIX.'listings_categories c ';
            	    $where[] =  'e.listingID=e2c.listingID AND e2c.categoryID=c.categoryID AND categorySlug IN ('.$this->implode_for_sql_in($cats).') ';
            	}
    	    }

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

    	    $listings  = $this->return_instances($rows);





        }


        if (isset($opts['skip-template']) && $opts['skip-template']==true) {

            if ($single_mode) return $Listing;

            $out = array();
            if (PerchUtil::count($listings)) {
                foreach($listings as $Listing) {
                    $out[] = $Listing->to_array();
                }
            }

            return $out;
	    }

	    //euro 8364
	    //pound 8356
	    //usd
	    $currenciessymbol=array("EUR"=>"&#8364;","GBP"=>"&#8356;","USD"=>"$");

       PerchSystem::set_var('currencysymbol', $currenciessymbol[$lcurrency]);

	    // template
	    if (isset($opts['template'])) {

            $template = 'listings/'.str_replace('listings/', '', $opts['template']);
	    }else{
	        $template = 'listings/listing.html';
	    }

	    $Template = $this->api->get("Template");
	    $Template->set($template, 'listings');

        if (PerchUtil::count($listings)) {
            $html = $Template->render_group($listings, true);
        }else{
            $Template->use_noresults();
            $html = $Template->render(array());
        }


	    return $html;
    }

     private function _listing_slug_to_id($listingID)
        {
            if (!$listingID) return 1;

            if (PERCH_RUNWAY) {
                $sql = 'SELECT blogID FROM '.PERCH_DB_PREFIX.'listings_listings WHERE listingsSlug='.$this->db->pdb($listingID).' LIMIT 1';
                $result = (int)$this->db->get_value($sql);
                if ($result > 0) return $result;
                return 1;
            }

            return 1;
        }
    private function _standard_where_callback($opts)
    {


        $db = $this->db;

        return function(PerchQuery $Query) use ($opts, $db) {


            // listing
            if (isset($opts['listingID'])) {
                $listingID = $this->_listing_slug_to_id($opts['listingID']);
                $Query->where[] = ' listingID='.(int)$listingID.' ';
            }



            return $Query;

        };


    }


    /**
     * gets the listing by category
     * @param varchar $slug
     */
    public function get_by_category_slug($slug)
    {

        $opts = array(
            'category' => $slug,
            'return-objects' => true,
            );

        return $this->get_filtered_listing($opts, $this->_standard_where_callback($opts));

    }

    /**
     * get the list of Listings with a date of today or greater to display int he admin area.
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

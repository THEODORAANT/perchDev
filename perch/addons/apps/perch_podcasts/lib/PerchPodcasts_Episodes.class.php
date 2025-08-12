<?php

class PerchPodcasts_Episodes extends PerchAPI_Factory
{
    protected $table     = 'podcasts_episodes';
	protected $pk        = 'episodeID';
	protected $singular_classname = 'PerchPodcasts_Episode';
	
	protected $default_sort_column = 'episodeDate DESC, 1=1 ';
    protected $created_date_column = 'episodeDate';
	
	public $static_fields   = array('episodeID', 'showID', 'episodeNumber', 'episodeTitle', 'episodeSlug', 'episodeDate', 'episodeDuration', 'episodeFile', 'episodeFileSize', 'episodeFileType', 'episodeDynamicFields', 'episodeStatus');


	public function get_next_episode_number_for_show($showID)
	{
		$sql = 'SELECT MAX(episodeNumber) FROM '.$this->table.' WHERE showID='.$showID;
		$num = $this->db->get_value($sql);
		$num++;
		return $num;
	}

	public function import_from_remote_rss($showID, $rss_url)
	{
		include(__DIR__.'/../SimplePie_autoloader.php');

		$SimplePie = new SimplePie();
		$SimplePie->set_feed_url($rss_url);
		$SimplePie->set_cache_location(PERCH_RESFILEPATH);
		$success = $SimplePie->init();
		$SimplePie->handle_content_type();

		if ($success) {

			$out = array();

			$Shows = new PerchPodcasts_Shows($this->api);
			$Show = $Shows->find($showID);

			$Template = $this->api->get('Template');
			$Template->set('podcasts/episode.html', 'podcasts');
			$tags     = $Template->find_all_tags_and_repeaters();

			foreach ($SimplePie->get_items() as $item) {
				$data = array();
				$data['showID'] = $showID;
				$data['episodeTitle'] = $item->get_title();

				if (preg_match('#^([0-9]+):\s#', $data['episodeTitle'], $matches)) {
					$data['episodeNumber'] = $matches[1];
					$data['episodeTitle'] = str_replace($matches[0], '', $data['episodeTitle']);
				}


				$data['episodeSlug'] = PerchUtil::urlify($data['episodeTitle']);
				$data['episodeDate'] = $item->get_date('Y-m-d H:i:s');
				
				$enclosure = $item->get_enclosure();
				if ($enclosure) {
					$data['episodeFileSize'] = $enclosure->length;
					$data['episodeFileType'] = $enclosure->type;
					$data['episodeFile'] 	 = $enclosure->link;
					$data['episodeDuration'] = $enclosure->duration;
					
				}


				
				 
				$data['episodeStatus'] = 'Published';

				$df = array();
				$seen_tags = array();  
				foreach($tags as $tag) {
					if (!in_array($tag->id(), $seen_tags)) {
						$FieldType = PerchFieldTypes::get($tag->type(), false, $tag);

						$namespace = '';
						if ($tag->import_from()) {
							if (strpos($tag->import_from(), ':')) {
								$parts = explode(':', $tag->import_from());
								switch($parts[0]) {
									case 'itunes':
										$namespace = SIMPLEPIE_NAMESPACE_ITUNES;
										break;
								}
								$import_field = $parts[1];
							}
						}else{
							$import_field = $tag->id();
						}

						$field_data = $item->get_item_tags($namespace, $import_field);
						if ($field_data) {

							$var = $FieldType->get_raw(array($tag->id() => $field_data[0]['data'] ));
							$df[$tag->id()] = $var;

						}

						
						$seen_tags[] = $tag->id();
					}
				}


				$data['episodeDynamicFields'] = PerchUtil::json_safe_encode($df);


				$trackedURL = false;
		        $default_url = $Show->get_option('statsURL');
		        if ($default_url) {
		        	$show = $Show->to_array();
		            $trackedURL = preg_replace_callback('/{([A-Za-z0-9_\-]+)}/', function($matches) use ($data, $show){
		                if (isset($data[$matches[1]])){
		                    return $data[$matches[1]];
		                }
		                if (isset($show[$matches[1]])){
                        	return $show[$matches[1]];
                    	}
		            }, $default_url);
		        }

		        if ($trackedURL) $data['episodeTrackedURL'] = $trackedURL;



				$out[] = $data;
			}

			if (PerchUtil::count($out)) {
				$out = PerchUtil::array_sort($out, 'episodeDate');
				$i = 1;
				foreach($out as $item) {
					if (!isset($item['episodeNumber'])) {
						$item['episodeNumber'] = $i;
					}

					$i++;
					$this->create($item);
				}
			}
		}

	}

	public function get_custom($Show=false, $opts=array())
    {

        $sql = 'SELECT * FROM '.$this->table.' WHERE episodeStatus='.$this->db->pdb('Published');
        $sql .= ' AND episodeDate<='.$this->db->pdb(date('Y-m-d H:i:s'));

        if ($Show) {
        	$sql .= ' AND showID='.$this->db->pdb($Show->id()).' ';
        }

        $sql .= ' ORDER BY '.$this->default_sort_column .'';
        $rows   = $this->db->get_rows($sql);
        $objects = $this->return_instances($rows);
        $content = array();
        if (PerchUtil::count($objects)) {
            foreach($objects as $Object) $content[] = $Object->to_array();
        }
        
        // find specific _id
	    if (isset($opts['_id'])) {
	        if (PerchUtil::count($content)) {
	            $out = array();
	            foreach($content as $item) {
	                if (isset($item['_id']) && $item['_id']==$opts['_id']) {
	                    $out[] = $item;
	                    break;
	                }
	            }
	            $content = $out;
	        }   
	    }else{
	        // if not picking an _id, check for a filter
	        if (isset($opts['filter']) && isset($opts['value'])) {
	            if (PerchUtil::count($content)) {	                
    	            $out = array();
    	            $key = $opts['filter'];
    	            $val = $opts['value'];
    	            $match = isset($opts['match']) ? $opts['match'] : 'eq';
    	            foreach($content as $item) {
                        if (!isset($item[$key])) $item[$key] = false;
    	                if (isset($item[$key])) {
    	                    switch ($match) {
                                case 'eq': 
                                case 'is': 
                                case 'exact': 
                                    if ($item[$key]==$val) $out[] = $item;
                                    break;
                                case 'neq': 
                                case 'ne': 
                                case 'not': 
                                    if ($item[$key]!=$val) $out[] = $item;
                                    break;
                                case 'gt':
                                    if ($item[$key]>$val) $out[] = $item;
                                    break;
                                case 'gte':
                                    if ($item[$key]>=$val) $out[] = $item;
                                    break;
                                case 'lt':
                                    if ($item[$key]<$val) $out[] = $item;
                                    break;
                                case 'lte':
                                    if ($item[$key]<=$val) $out[] = $item;
                                    break;
                                case 'contains':
                                    $value = str_replace('/', '\/', $val);
                                    if (preg_match('/\b'.$value.'\b/i', $item[$key])) $out[] = $item;
                                    break;
                                case 'regex':
                                case 'regexp':
                                    if (preg_match($val, $item[$key])) $out[] = $item;
                                    break;
                                case 'between':
                                case 'betwixt':
                                    $vals  = explode(',', $val);
                                    if (PerchUtil::count($vals)==2) {
                                        if ($item[$key]>trim($vals[0]) && $item[$key]<trim($vals[1])) $out[] = $item;
                                    }
                                    break;
                                case 'eqbetween':
                                case 'eqbetwixt':
                                    $vals  = explode(',', $val);
                                    if (PerchUtil::count($vals)==2) {
                                        if ($item[$key]>=trim($vals[0]) && $item[$key]<=trim($vals[1])) $out[] = $item;
                                    }
                                    break;
                                case 'in':
                                case 'within':
                                    $vals  = explode(',', $val);
                                    if (PerchUtil::count($vals)) {
                                        foreach($vals as $value) {
                                            if ($item[$key]==trim($value)) {
                                                $out[] = $item;
                                                break;
                                            }
                                        }
                                    }
                                    break;

    	                    }
    	                }
    	            }
    	            $content = $out;
    	        }
	        }
	    }
    
	    // sort
	    if (isset($opts['sort'])) {
	        if (isset($opts['sort-order']) && $opts['sort-order']=='DESC') {
	            $desc = true;
	        }else{
	            $desc = false;
	        }
	        $content = PerchUtil::array_sort($content, $opts['sort'], $desc);
	    }
    
	    if (isset($opts['sort-order']) && $opts['sort-order']=='RAND') {
            shuffle($content);
        }
    
        // Pagination
        if (isset($opts['paginate'])) {
            if (isset($opts['pagination-var'])) {
                $Paging = new PerchPaging($opts['pagination-var']);
            }else{
                $Paging = new PerchPaging();
            }
            
            $Paging->set_per_page(isset($opts['count'])?(int)$opts['count']:10);
            
            $opts['count'] = $Paging->per_page();
            $opts['start'] = $Paging->lower_bound()+1;
            
            $Paging->set_total(PerchUtil::count($content));
        }else{
            $Paging = false;
        }
                
        // limit
	    if (isset($opts['count']) || isset($opts['start'])) {

            // count
	        if (isset($opts['count'])) {
	            $count = (int) $opts['count'];
	        }else{
	            $count = PerchUtil::count($content);
	        }
            
	        // start
	        if (isset($opts['start'])) {
	            if ($opts['start'] === 'RAND') {
	                $start = rand(0, PerchUtil::count($content)-1);
	            }else{
	                $start = ((int) $opts['start'])-1; 
	            }
	        }else{
	            $start = 0;
	        }

	        // loop through
	        $out = array();
	        for($i=$start; $i<($start+$count); $i++) {
	            if (isset($content[$i])) {
	                $out[] = $content[$i];
	            }else{
	                break;
	            }
	        }
	        $content = $out;
	    }
        
	    // Paging to template
        if (is_object($Paging) && $Paging->enabled()) {
            $paging_array = $Paging->to_array($opts);
            // merge in paging vars
	        foreach($content as &$item) {
	            foreach($paging_array as $key=>$val) {
	                $item[$key] = $val;
	            }
	        }
        }

        if ($opts['skip-template']) {
        	return $content;
        }

        $Template = $this->api->get("Template");
	    $Template->set('podcasts/'.$opts['template'], 'podcasts');

        if (PerchUtil::count($content)) {
            $html = $Template->render_group($content, true);
        }else{
            $Template->use_noresults();
            $html = $Template->render(array());
        }
        
        return $html;
    }


    public function find_by_slug($showID, $slug)
    {
        $sql    = 'SELECT * FROM ' . $this->table . ' WHERE showID='. $this->db->pdb($showID) .' AND episodeSlug='.$this->db->pdb($slug).' AND episodeDate<='.$this->db->pdb(date('Y-m-d H:i:s')).' LIMIT 1';
        $result = $this->db->get_row($sql);
        
        if (is_array($result)) {
            return new $this->singular_classname($result);
        }
        
        return false;
    }

    public function find_by_number($showID, $number)
    {
        $sql    = 'SELECT * FROM ' . $this->table . ' WHERE showID='. $this->db->pdb($showID) .' AND episodeNumber='.$this->db->pdb((int)$number).' AND episodeDate<='.$this->db->pdb(date('Y-m-d H:i:s')).' LIMIT 1';
        $result = $this->db->get_row($sql);
        
        if (is_array($result)) {
            return new $this->singular_classname($result);
        }
        
        return false;
    }


    public function get_count_for_show($showID)
    {
    	$sql = 'SELECT COUNT(*) FROM ' . $this->table . ' WHERE showID='. $this->db->pdb($showID) .' AND episodeDate<='.$this->db->pdb(date('Y-m-d H:i:s'));
        return $this->db->get_count($sql);
    }

}

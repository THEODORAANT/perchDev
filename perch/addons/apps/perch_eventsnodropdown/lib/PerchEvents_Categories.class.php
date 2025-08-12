<?php

class PerchEvents_Categories extends PerchAPI_Factory
{
    protected $table     = 'events_categories';
	protected $pk        = 'categoryID';
	protected $singular_classname = 'PerchEvents_Category';
	
	protected $default_sort_column = 'categoryTitle';

    public $static_fields   = array('categoryTitle', 'categorySlug', 'categoryPostCount', 'categoryFuturePostCount');
	
	
    /**
     * Find a category by its categorySlug
     *
     * @param string $slug 
     * @return void
     * @author Drew McLellan
     */
    public function find_by_slug($slug)
    {
        $sql    = 'SELECT * 
                    FROM ' . $this->table . '
                    WHERE categorySlug='. $this->db->pdb($slug) .'
                    LIMIT 1';
                    
        $result = $this->db->get_row($sql);
        
        if (is_array($result)) {
            return new $this->singular_classname($result);
        }
        
        return false;
    }
    
	public function get_for_event($eventID)
	{
	    $sql = 'SELECT c.*
	            FROM '.$this->table.' c, '.PERCH_DB_PREFIX.'events_to_categories e2c
	            WHERE c.categoryID=e2c.categoryID
	                AND e2c.eventID='.$this->db->pdb($eventID);
	    $rows   = $this->db->get_rows($sql);
	    
	    return $this->return_instances($rows);
	}

	public function get_custom($opts)
	{
		$API = new PerchAPI(1.0, 'perch_events');

		if ($opts['past-events']) {
			$count_field = 'categoryEventCount';
		}else{
            $count_field = 'categoryFutureEventCount';
		}

		if ($opts['include-empty']) {
			$sql = 'SELECT *, categoryID AS _id, '.$count_field.' as qty FROM '.$this->table.' ORDER BY categoryTitle ASC';
		}else{
			$sql = 'SELECT *, categoryID AS _id, '.$count_field.' as qty FROM '.$this->table.' WHERE categoryEventCount>0 ORDER BY categoryTitle ASC';
		}

		$rows   = $this->db->get_rows($sql);
		$cats 	= $this->return_instances($rows);

		$content = array();

        if (PerchUtil::count($cats)) {
            foreach($cats as $Cat) $content[] = $Cat->to_array();    
        }
		
		
		if (isset($opts['filter']) && (isset($opts['value']) || is_array($opts['filter']))) {
            if (PerchUtil::count($content)) {
                $out = array();

                // if it's not a multi-filter, make it look like one to unify what we're working with
                if (!is_array($opts['filter']) && isset($opts['value'])) {
                    $filters = array(
                                    array(
                                        'filter'=>$opts['filter'],
                                        'value'=>$opts['value'],
                                        'match'=>(isset($opts['match']) ? $opts['match'] : 'eq')
                                    )
                                );
                    $filter_mode = 'AND';
                }else{
                    $filters = $opts['filter'];
                    $filter_mode = 'AND';

                    if (isset($opts['match']) && strtolower($opts['match'])=='or') {
                        $filter_mode = 'OR';
                    }
                }

                $filter_content = $content;

                foreach($filters as $filter) {                       

                    $key = $filter['filter'];
                    $val = $filter['value'];
                    $match = isset($filter['match']) ? $filter['match'] : 'eq';
                    foreach($filter_content as $item) {

                        // If 'AND' mode, remove the item, as we only want it if it's added by this filter too.
                        // ninja code.
                        if ($filter_mode=='AND' && isset($out[$item['_id']])) {
                            unset($out[$item['_id']]);
                        }
                        if (!isset($item[$key])) $item[$key] = false;
                        if (isset($item[$key])) {
                            $this_item = $this->_resolve_to_value($item[$key]);

                            switch ($match) {
                                case 'eq': 
                                case 'is': 
                                case 'exact': 
                                    if ($this_item==$val) $out[$item['_id']] = $item;
                                    break;
                                case 'neq': 
                                case 'ne': 
                                case 'not': 
                                    if ($this_item!=$val) $out[$item['_id']] = $item;
                                    break;
                                case 'gt':
                                    if ($this_item>$val) $out[$item['_id']] = $item;
                                    break;
                                case 'gte':
                                    if ($this_item>=$val) $out[$item['_id']] = $item;
                                    break;
                                case 'lt':
                                    if ($this_item<$val) $out[$item['_id']] = $item;
                                    break;
                                case 'lte':
                                    if ($this_item<=$val) $out[$item['_id']] = $item;
                                    break;
                                case 'contains':
                                    $value = str_replace('/', '\/', $val);
                                    if (preg_match('/\b'.$value.'\b/i', $this_item)) $out[$item['_id']] = $item;
                                    break;
                                case 'regex':
                                case 'regexp':
                                    if (preg_match($val, $this_item)) $out[$item['_id']] = $item;
                                    break;
                                case 'between':
                                case 'betwixt':
                                    $vals  = explode(',', $val);
                                    if (PerchUtil::count($vals)==2) {
                                        if ($this_item>trim($vals[0]) && $this_item<trim($vals[1])) $out[$item['_id']] = $item;
                                    }
                                    break;
                                case 'eqbetween':
                                case 'eqbetwixt':
                                    $vals  = explode(',', $val);
                                    if (PerchUtil::count($vals)==2) {
                                        if ($this_item>=trim($vals[0]) && $this_item<=trim($vals[1])) $out[$item['_id']] = $item;
                                    }
                                    break;
                                case 'in':
                                case 'within':
                                    $vals  = explode(',', $val);
                                    if (PerchUtil::count($vals)) {
                                        foreach($vals as $value) {
                                            if ($this_item==trim($value)) {
                                                $out[$item['_id']] = $item;
                                                break;
                                            }
                                        }
                                    }
                                    break;

                            }
                        }
                    }

                    // if 'AND' mode, run the next filter against the already filtered list
                    if ($filter_mode == 'AND') {
                        $filter_content = $out;                        
                    }else{
                        $filter_content = $content;
                    }
                }


                $content = $out;
            }
        }

		// reindex array
        $new_content = array();
        foreach($content as $c) $new_content[] = $c;
        $content = $new_content;
    
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

        	$Paging = $API->get('Paging');

            if (isset($opts['pagination-var'])) {
                $Paging->set_qs_param($opts['pagination-var']);
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
              
        
        if (isset($opts['skip-template']) && $opts['skip-template']==true) {
            if (isset($opts['raw']) && $opts['raw']==true) {
                if (PerchUtil::count($content)) {
                    foreach($content as &$item) {
                        if (PerchUtil::count($item)) {
                            foreach($item as &$field) {
                                if (is_array($field) && isset($field['raw'])) {
                                    $field = $field['raw'];
                                }
                            }
                        }
                    }
                }
                return $content; 
            }
        }
    
        
        // template
        $Template = $API->get('Template');
        $Template->set('events/'.$opts['template'], 'events');



        // post process       
        $tags   = $Template->find_all_tags_and_repeaters('events');
        $processed_vars = array();
        $used_items = array();
        foreach($content as $item) {
            $tmp = $item;
            if (PerchUtil::count($tags)) {
                foreach($tags as $Tag) {
                    if (isset($item[$Tag->id()])) {    
                        $used_items[] = $item;
                    }
                }
            }
            if ($tmp) $processed_vars[] = $tmp;
        }

        
        
        
        // Paging to template
        if (is_object($Paging) && $Paging->enabled()) {
            $paging_array = $Paging->to_array($opts);
            // merge in paging vars
            foreach($processed_vars as &$item) {
                foreach($paging_array as $key=>$val) {
                    $item[$key] = $val;
                }
            }
        }
        
        if (PerchUtil::count($processed_vars)) {
            $html = $Template->render_group($processed_vars, true);

        }else{
            $Template->use_noresults();
            $html = $Template->render(array());
        }

        
        if (isset($opts['skip-template']) && $opts['skip-template']==true) {
            $out = array();

            if (PerchUtil::count($processed_vars)) {
                foreach($processed_vars as &$item) {
                    if (PerchUtil::count($item)) {
                        foreach($item as &$field) {
                            if (is_array($field) && isset($field['processed'])) {
                                $field = $field['processed'];
                            }
                            if (is_array($field) && isset($field['_default'])) {
                                $field = $field['_default'];
                            }
                        }
                    }
                }
            }

            for($i=0; $i<PerchUtil::count($content); $i++) {
                $out[] = array_merge($content[$i], $processed_vars[$i]);
            }

            if (isset($opts['return-html'])&& $opts['return-html']==true) $out['html'] = $html;

            return $out;
        }
        
        return $html;

	}

	public function update_event_counts()
    {
    	$sql = 'SELECT c.categoryID, COUNT(p2c.eventID) AS qty
                FROM '.PERCH_DB_PREFIX.'events_categories c, '.PERCH_DB_PREFIX.'events_to_categories p2c, '.PERCH_DB_PREFIX.'events p
                WHERE p2c.categoryID=c.categoryID AND p2c.eventID=p.eventID
                    AND p.eventDateTime>='.$this->db->pdb(date('Y-m-d H:i:00')).' 
                GROUP BY c.categoryID
                ORDER BY c.categoryTitle ASC';
        $rows = $this->db->get_rows($sql);

        if (PerchUtil::count($rows)) {
        	foreach($rows as $row) {
        		$sql = 'UPDATE '.PERCH_DB_PREFIX.'events_categories SET categoryFutureEventCount='.$this->db->pdb($row['qty']).' WHERE categoryID='.$this->db->pdb($row['categoryID']).' LIMIT 1';
        		$this->db->execute($sql);
        	}
        }

        $sql = 'SELECT c.categoryID, COUNT(p2c.eventID) AS qty
                FROM '.PERCH_DB_PREFIX.'events_categories c, '.PERCH_DB_PREFIX.'events_to_categories p2c, '.PERCH_DB_PREFIX.'events p
                WHERE p2c.categoryID=c.categoryID AND p2c.eventID=p.eventID
                GROUP BY c.categoryID
                ORDER BY c.categoryTitle ASC';
        $rows = $this->db->get_rows($sql);

        if (PerchUtil::count($rows)) {
        	foreach($rows as $row) {
        		$sql = 'UPDATE '.PERCH_DB_PREFIX.'events_categories SET categoryEventCount='.$this->db->pdb($row['qty']).' WHERE categoryID='.$this->db->pdb($row['categoryID']).' LIMIT 1';
        		$this->db->execute($sql);
        	}
        }
    }
    
}


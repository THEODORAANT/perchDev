<?php

class PerchEvents_Event extends PerchAPI_Base
{
    protected $table  = 'events';
    protected $pk     = 'eventID';
    protected $deleted_date_column = null;
    private $tmp_url_vars = array();


    public function update($data)
    {

        $PerchEvents_Events = new PerchEvents_Events();
        
        if(isset($data['eventDescRaw'])) {
        	$data['eventDescHTML'] = $PerchEvents_Events->text_to_html($data['eventDescRaw']);
        }else{
        	$data['eventDescHTML'] = false;
        }
        
        if (isset($data['eventTitle'])) {
            $data['eventSlug'] = PerchUtil::urlify(date('Y m d', strtotime($data['eventDateTime'])). ' ' . $data['eventTitle']);
        }

        if (isset($data['cat_ids'])) {
            $catIDs = $data['cat_ids'];
            unset($data['cat_ids']);
        }else{
            $catIDs = false;
        }
        $slots= $data['slots'];
        unset($data['slots']);
        // Update the event itself
        parent::update($data);

        // Delete existing categories
        $this->db->delete(PERCH_DB_PREFIX.'events_to_categories', $this->pk, $this->id());

 		// Add new categories
 		if (is_array($catIDs)) {
 			for($i=0; $i<sizeOf($catIDs); $i++) {
 			    $tmp = array();
 			    $tmp['eventID'] = $this->id();
 			    $tmp['categoryID'] = $catIDs[$i];
 			    $this->db->insert(PERCH_DB_PREFIX.'events_to_categories', $tmp);
 			}
 		}
          $this->db->delete(PERCH_DB_PREFIX.'events_to_timeslots', $this->pk, $this->id());
          $tmp2['eventID'] = $this->id();
 	      $tmp2['slotID'] = $slots;
 		  $this->db->insert(PERCH_DB_PREFIX.'events_to_timeslots', $tmp2);
 		return true;
    }
    
    public function delete()
    {
        parent::delete();
        $this->db->delete(PERCH_DB_PREFIX.'events_to_categories', $this->pk, $this->id());
    }
    
    public function date()
    {
        return date('Y-m-d', strtotime($this->eventDateTime()));
    }

     public function enddate()
     {
         return date('Y-m-d', strtotime($this->eventEndDateTime()));
     }

    public function to_array($template_ids=false)
    {
        $out = parent::to_array();
        
        $Categories = new PerchEvents_Categories();
        $cats   = $Categories->get_for_event($this->id());

        $out['category_slugs'] = '';
        $out['category_names'] = '';
        $out['category_colors'] = '';

        if (PerchUtil::count($cats)) {
            $slugs = array();
            $names = array();
            $colors = array();
            foreach($cats as $Category) {
                $slugs[] = $Category->categorySlug();
                $names[] = $Category->categoryTitle();

                $category_data = $Category->to_array();

                if (isset($category_data['color']) && $category_data['color'] !== '') {
                    $colors[] = $category_data['color'];
                    $out['category_color_'.$Category->categorySlug()] = $category_data['color'];
                }

                // for template
                $out[$Category->categorySlug()] = true;
            }

            $out['category_slugs'] = implode(' ', $slugs);
            $out['category_names'] = implode(', ', $names);

            if (PerchUtil::count($colors)) {
                $out['category_colors'] = implode(' ', $colors);
                $out['category_color'] = reset($colors);
            }
        }

        if (PerchUtil::count($template_ids) && in_array('eventURL', $template_ids)) {
            $Settings = PerchSettings::fetch();
            $url_template = $Settings->get('perch_events_detail_url')->val();
            $this->tmp_url_vars = $out;
            $out['eventURL'] = preg_replace_callback('/{([A-Za-z0-9_\-]+)}/', array($this, "substitute_url_vars"), $url_template);
            $this->tmp_url_vars = false;
        }
        
        if (isset($out['eventDynamicFields']) && $out['eventDynamicFields'] != '') {
            $dynamic_fields = PerchUtil::json_safe_decode($out['eventDynamicFields'], true);
            if (PerchUtil::count($dynamic_fields)) {
                foreach($dynamic_fields as $key=>$value) {
                    $out['perch_'.$key] = $value;
                }
            }
            $out = array_merge($dynamic_fields, $out);
        }
        
        return $out;
    }

    private function substitute_url_vars($matches)
    {
        $url_vars = $this->tmp_url_vars;
        if (isset($url_vars[$matches[1]])){
            return $url_vars[$matches[1]];
        }
    }

}

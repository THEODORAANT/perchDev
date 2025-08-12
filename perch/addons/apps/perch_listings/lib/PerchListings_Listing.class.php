<?php

class PerchListings_Listing extends PerchAPI_Base
{
    protected $table  = 'listings_listings';
    protected $pk     = 'listingID';

    private $tmp_url_vars = array();
 public function __call($method, $arguments)
	{
		if (isset($this->details[$method])) {
			return $this->details[$method];
		}else{



            // look in dynamic fields
            $dynamic_fields = PerchUtil::json_safe_decode($this->listingDynamicFields(), true);
            if (isset($dynamic_fields[$method])) {
                return $dynamic_fields[$method];
            }

            // try database
		    PerchUtil::debug('Looking up missing property ' . $method, 'notice');
		    if (isset($this->details[$this->pk])){
		        $sql    = 'SELECT ' . $method . ' FROM ' . $this->table . ' WHERE ' . $this->pk . '='. $this->db->pdb($this->details[$this->pk]);
		        $this->details[$method] = $this->db->get_value($sql);
		        return $this->details[$method];
		    }
		}

		return false;
	}

    public function update($data)
    {

      //  $PerchListings_Listing = new PerchListings_Listing();


        if (isset($data['listingTitle'])) {
            $data['listingSlug'] = PerchUtil::urlify(date('Y m d', strtotime($data['listingCreated'])). ' ' . $data['listingTitle']);
        }

        if (isset($data['cat_ids'])) {
            $catIDs = $data['cat_ids'];
            unset($data['cat_ids']);
        }else{
            $catIDs = false;
        }

         // Merge fields
if (isset($this->duplicate_fields) && count($this->duplicate_fields)) {
			$dynamic_field_col = str_replace('ID', 'DynamicFields', $this->pk);
			if (isset($data[$dynamic_field_col])) {
				$dynamic_fields    = PerchUtil::json_safe_decode($data[$dynamic_field_col], true);

				foreach($this->duplicate_fields as $target=>$source) {

					$urlify = false;
					if ($source[0]==='*') {
						$source = str_replace('*', '', $source);
						$urlify = true;
					}

					if (isset($dynamic_fields[$source])) {

						if (is_array($dynamic_fields[$source])) {
							$dynamic_fields[$source] = $this->_distil($source, $dynamic_fields);
						}

						if ($urlify) {
							$data[$target] = PerchUtil::urlify($dynamic_fields[$source]);
						}else{
							$data[$target] = $dynamic_fields[$source];
						}

					}
				}
			}
		}
        // Update the event itself
        parent::update($data);
// Delete existing categories
        $this->db->delete(PERCH_DB_PREFIX.'listings_to_categories', $this->pk, $this->id());

 		// Add new categories
 		if (is_array($catIDs)) {
 			for($i=0; $i<sizeOf($catIDs); $i++) {
 			    $tmp = array();
 			    $tmp['listingID'] = $this->id();
 			    $tmp['categoryID'] = $catIDs[$i];
 			    $this->db->insert(PERCH_DB_PREFIX.'listings_to_categories', $tmp);
 			}
 		}

 		return true;
    }

    public function delete()
    {
        parent::delete();

    }

    public function date()
    {
        return date('Y-m-d', strtotime($this->listingCreated()));
    }

  public function to_array($template_ids=false)
    {
        $out = parent::to_array();

        $Categories = new PerchListings_Categories();
        $cats   = $Categories->get_for_listing($this->id());

        $out['category_slugs'] = '';
        $out['category_names'] = '';

        if (PerchUtil::count($cats)) {
            $slugs = array();
            $names = array();
            foreach($cats as $Category) {
                $slugs[] = $Category->categorySlug();
                $names[] = $Category->categoryTitle();

                // for template
                $out[$Category->categorySlug()] = true;
            }

            $out['category_slugs'] = implode(' ', $slugs);
            $out['category_names'] = implode(', ', $names);
        }

        if (PerchUtil::count($template_ids) && in_array('listingURL', $template_ids)) {
            $Settings = PerchSettings::fetch();
            $url_template = $Settings->get('perch_listings_detail_url')->val();
            $this->tmp_url_vars = $out;
            $out['listingURL'] = preg_replace_callback('/{([A-Za-z0-9_\-]+)}/', array($this, "substitute_url_vars"), $url_template);
            $this->tmp_url_vars = false;
        }

        if (isset($out['listingDynamicFields']) && $out['listingDynamicFields'] != '') {

            $dynamic_fields = PerchUtil::json_safe_decode($out['listingDynamicFields'], true);

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

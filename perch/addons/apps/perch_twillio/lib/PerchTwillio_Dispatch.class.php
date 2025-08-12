<?php

class PerchTwillio_Dispatch extends PerchAPI_Base
{
    protected $table  = 'twillio_dispatches';
    protected $pk     = 'dispatchID';

    private $tmp_url_vars = array();


    public function update($data)
    {

        $PerchTwillio_Dispatches = new PerchTwillio_Dispatches();


        // Update the event itself
        parent::update($data);


 		return true;
    }

    public function delete()
    {
        parent::delete();

    }

    public function date()
    {
        return date('Y-m-d', strtotime($this->dispatchDateTime()));
    }


  /*  public function to_array($template_ids=false)
    {
        $out = parent::to_array();

        $Categories = new PerchEvents_Categories();
        $cats   = $Categories->get_for_event($this->id());

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
    }*/

    private function substitute_url_vars($matches)
    {
        $url_vars = $this->tmp_url_vars;
        if (isset($url_vars[$matches[1]])){
            return $url_vars[$matches[1]];
        }
    }

}

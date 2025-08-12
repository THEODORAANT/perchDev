<?php

class PerchMenuItemFeature extends PerchBase
{
    protected $table  = 'menu_item_features';
    protected $pk     = 'featureID';

    public function get_link()
    {
        switch($this->details['featureType']) {

            case 'app':
                return $this->resolve_app_path($this->details['featureValue']);
                break;

            case 'menu':
                return '';
                break;

            case 'link':
                return $this->details['featureValue'];
                break;

        }
    }

    public function is_permitted($CurrentUser, $apps)
    {

        if ($this->details['featureType'] == 'app' && !is_numeric($this->details['featureValue'])) {
            if (!in_array($this->details['featureValue'], $apps)) {
                return false;
            }
        }


        if (!$this->privID()) {
            return true;
        }

        if ($CurrentUser->has_priv($this->privKey())) {
            return true;
        }

        return false;
    }

   public function featureSetActive($active)
    {
       $data = array();

        $data['featureID'] = $this->featureID();
        if($active){
         $data['featureActive'] = 0;
        }else{
             $data['featureActive'] = 1;
        }


      if (count($data)) $this->update($data);


    }

    public function update_tree_position($parentID=false, $order=false)
    {
        $data = array();

        if ($parentID) {
            $data['parentID'] = $parentID;
        }else{
            $data['parentID'] = $this->parentID();
        }

        if ($order) {
            $data['featureOrder'] = $order;
        }else{
            $data['featureOrder'] = $this->find_next_child_order($data['parentID']);
        }

        if (count($data)) $this->update($data);
    }



    private function resolve_app_path($appSlug)
    {
        $core_apps = ['content', 'assets', 'categories'];

        if (PERCH_RUNWAY && is_numeric($appSlug)) {
            return PERCH_LOGINPATH.'/core/apps/content/collections/?id='.$appSlug;
        }

        if (in_array($appSlug, $core_apps)) {
            return PERCH_LOGINPATH.'/core/apps/'.str_replace('perch_', '', $appSlug).'/';
        }

        return PERCH_LOGINPATH.'/addons/apps/'.$appSlug.'/';
    }
}

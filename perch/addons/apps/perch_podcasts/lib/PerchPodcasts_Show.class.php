<?php

class PerchPodcasts_Show extends PerchAPI_Base
{
    protected $table  = 'podcasts';
    protected $pk     = 'showID';


    public function delete()
    {
        $Episodes = new PerchPodcasts_Episodes();
        $eps = $Episodes->get_by('showID', $this->id());
        if (PerchUtil::count($eps)) {
            foreach($eps as $Ep) {
                $Ep->delete();
            }
        }
        parent::delete();
    }

    public function get_options()
    {
        $opts = $this->showOptions();
        if ($opts!='') {
            return PerchUtil::json_safe_decode($opts, true);
        }

        return array();
    }

    public function get_option($key)
    {
        $opts = $this->get_options();
        if (isset($opts[$key])){
            return $opts[$key];
        }

        return false;
    }

    public function set_options($options)
    {
        $old_options = $this->get_options();
        if (is_array($options)) {
            $new_options = array_merge($old_options, $options);
            $data = array('showOptions'=>PerchUtil::json_safe_encode($new_options));
            $this->update($data);
            return true;
        }
        return false;
    }

    public function get_next_episode_number()
    {
        $Episodes = new PerchPodcasts_Episodes();
        return $Episodes->get_next_episode_number_for_show($this->id());
    }

    public function update_episode_count()
    {
        $Episodes = new PerchPodcasts_Episodes();
        
        $data = array(
            'showEpisodeCount'=>$Episodes->get_count_for_show($this->id()),
            );
        $this->update($data);
    }


    public function to_array($template_ids=false)
    {
        $out = parent::to_array();
              
        if ($out['showDynamicFields'] != '') {
            $dynamic_fields = PerchUtil::json_safe_decode($out['showDynamicFields'], true);
            if (PerchUtil::count($dynamic_fields)) {
                foreach($dynamic_fields as $key=>$value) {
                    $out['perch_'.$key] = $value;
                }
            }
            $out = array_merge($dynamic_fields, $out);
        }
        
        return $out;
    }

    public function report_on($key)
    {
        $table = PERCH_DB_PREFIX.'podcasts_downloads';

        switch($key) {

            case 'total_plays':
                $sql = 'SELECT COUNT(*) FROM '.$table.' WHERE showID='.$this->db->pdb($this->id());
                return $this->db->get_count($sql);
                break;

            case 'average_plays':
                $sql = 'SELECT COUNT(*) FROM '.$table.' WHERE showID='.$this->db->pdb($this->id());
                $show_count = (int)$this->showEpisodeCount();
                if ($show_count) {
                    return ceil($this->db->get_count($sql) / (int)$this->showEpisodeCount());    
                }
                return ceil($this->db->get_count($sql));
                break;

            case 'play_spark':
                $sql = 'SELECT CONCAT(YEAR(downloadDateTime), LPAD(MONTH(downloadDateTime), 2, \'0\')) AS m, COUNT(*) AS qty
                        FROM '.$table.' 
                        WHERE showID='.$this->db->pdb($this->id()).'
                        GROUP BY showID, m
                        ORDER BY m ASC';
                $rows = $this->db->get_rows($sql);
                $out = array();
                if (PerchUtil::count($rows)) {
                    foreach($rows as $row) $out[] = $row['qty'];
                }
                return $out;
                break;

        }
    }
}


<?php

class PerchPodcasts_Episode extends PerchAPI_Base
{
    protected $table  = 'podcasts_episodes';
    protected $pk     = 'episodeID';


    public function duration_hms()
    {
        $duration = (isset($this->details['episodeDuration']) ? (int)$this->details['episodeDuration'] : false);

        if ($duration) {
            return gmdate("H:i:s", $duration);    
        }
        return '';
    }

    public function html5AudioTag()
    {
        $s = '<audio controls>';
        $s .= '<source src="'.$this->episodeFile().'" type="'.$this->episodeFileType().'" />';
        $s .='</audio>';
        return $s;
    }

    public function track_play($env)
    {
        $ua = '';

        if (isset($env['HTTP_USER_AGENT'])) {
            $ua = $env['HTTP_USER_AGENT'];
        }

        $data = array(
            'downloadDateTime' => date('Y-m-d H:i:s'),
            'showID' => $this->showID(),
            'episodeID' => $this->id(),
            'downloadUA' => $ua,
            );        

        $this->db->insert(PERCH_DB_PREFIX.'podcasts_downloads', $data);

    }


    public function to_array($template_ids=false)
    {
        $out = parent::to_array();
              
        $out['episodeDurationHMS'] = $this->duration_hms();

        if ($out['episodeDynamicFields'] != '') {
            $dynamic_fields = PerchUtil::json_safe_decode($out['episodeDynamicFields'], true);
            if (PerchUtil::count($dynamic_fields)) {
                foreach($dynamic_fields as $key=>$value) {
                    $out['perch_'.$key] = $value;
                }
            }
            $out = array_merge($dynamic_fields, $out);
        }
        
        return $out;
    }


    public function report_on($key, $opts=array())
    {
        $table = PERCH_DB_PREFIX.'podcasts_downloads';

        switch($key) {

            case 'total_plays':
                $sql = 'SELECT COUNT(*) FROM '.$table.' WHERE showID='.$this->db->pdb($this->showID()).' AND episodeID='.$this->db->pdb($this->id());
                return $this->db->get_count($sql);
                break;

            case 'play_spark':
                if (isset($opts['latest']) && $opts['latest']) {
                    $sql = 'SELECT CONCAT(YEAR(downloadDateTime), LPAD(MONTH(downloadDateTime), 2, \'0\'), LPAD(DAY(downloadDateTime), 2, \'0\')) AS m, COUNT(*) AS qty
                        FROM '.$table.' 
                        WHERE showID='.$this->db->pdb($this->showID()).' AND episodeID='.$this->db->pdb($this->id()).'
                        GROUP BY showID, m
                        ORDER BY m ASC';
                }else{
                    $sql = 'SELECT CONCAT(YEAR(downloadDateTime), LPAD(MONTH(downloadDateTime), 2, \'0\')) AS m, COUNT(*) AS qty
                        FROM '.$table.' 
                        WHERE showID='.$this->db->pdb($this->showID()).' AND episodeID='.$this->db->pdb($this->id()).'
                        GROUP BY showID, m
                        ORDER BY m ASC';
                }
                
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

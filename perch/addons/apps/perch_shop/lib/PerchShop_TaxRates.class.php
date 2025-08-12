<?php

class PerchShop_TaxRates extends PerchShop_Factory
{
    protected $table               = 'shop_tax_rates';
    protected $pk                  = 'rateID';
    protected $singular_classname  = 'PerchShop_TaxRate';

    protected $namespace           = 'shop';
    protected $event_prefix        = 'shop.tax';

    protected $default_sort_column = 'rateID';

    private static $cached_rates = [];

    public function get_rate_ids_for_location($locationID)
    {
        $sql = 'SELECT rateID FROM '.$this->table.' WHERE locationID='.$this->db->pdb((int)$locationID);
        return $this->db->get_rows_flat($sql);
    }

    public function get_edit_values($locationID)
    {
        if (isset(PerchShop_TaxRates::$cached_rates[$locationID])) {
            return PerchShop_TaxRates::$cached_rates[$locationID];
        }

        $sql = 'SELECT rateID AS id, rateTitle AS title, rateValue AS rate, locationID
                FROM '.$this->table.' WHERE rateDeleted IS NULL 
                ORDER BY rateValue DESC';
        $rows = $this->db->get_rows($sql);

        if (PerchUtil::count($rows)) {
            foreach($rows as $row) {
                PerchShop_TaxRates::$cached_rates[$row['locationID']][] = $row;
            }
        }

        if (isset(PerchShop_TaxRates::$cached_rates[$locationID])) {
            return PerchShop_TaxRates::$cached_rates[$locationID];
        }

        // fallback (original, non-cached version)
        $sql = 'SELECT rateID AS id, rateTitle AS title, rateValue AS rate
                FROM '.$this->table.' WHERE locationID='.$this->db->pdb((int)$locationID).' AND rateDeleted IS NULL 
                ORDER BY rateValue DESC';
        return $this->db->get_rows($sql);
    }

    public function get_edit_opts_for_location($locationID)
    {
        $sql = 'SELECT rateID, rateTitle
                FROM '.$this->table.' WHERE locationID='.$this->db->pdb((int)$locationID).' AND rateDeleted IS NULL 
                ORDER BY rateValue DESC';
        $rows = $this->db->get_rows($sql);

        if (PerchUtil::count($rows)) {
            $opts = [];
            $opts[] = ['label'=>'', 'value'=>''];
            foreach($rows as $row) {
                $opts[] = ['label'=>$row['rateTitle'], 'value'=>$row['rateID']];
            }
            return $opts;
        }
        return [['label'=>'-', 'value'=>'0']];
    }

    public function add_or_update($data)
    {
        $table = PERCH_DB_PREFIX.'shop_tax_group_rates';

    	$sql = 'DELETE FROM '.$table.' WHERE groupID='.$this->db->pdb($data['groupID']).' AND locationID='.$this->db->pdb($data['locationID']);
    	$this->db->execute($sql);
    	$this->db->insert($table, $data);
    }

    // Used by cart
    public function get_rate_for_location($groupID, $locationID)
    {
        $table = PERCH_DB_PREFIX.'shop_tax_group_rates';

        $sql = 'SELECT r.rateValue FROM '.$table.' gr, '.$this->table.' r 
                WHERE gr.rateID=r.rateID AND gr.groupID='.$this->db->pdb($groupID).' AND gr.locationID='.$this->db->pdb($locationID);
        $result = $this->db->get_value($sql);

        if ($result) return floatval($result);

        return 0;
    }

    // Used in editing
	public function get_type_for_location($groupID, $locationID)
	{
        $table = PERCH_DB_PREFIX.'shop_tax_group_rates';

		$sql = 'SELECT rateID FROM '.$table.' WHERE groupID='.$this->db->pdb($groupID).' AND locationID='.$this->db->pdb($locationID);
		return $this->db->get_value($sql);
	}
}
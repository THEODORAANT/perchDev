<?php

class PerchShop_ProductTag extends PerchAPI_Base
{
    protected $table        = 'shop_product_tags';
    protected $pk           = 'prodtagID';
    
    protected $index_table  = false;
    protected $event_prefix = 'shop.producttag';

    public function delete_from_product($productID)
    {
    	$sql = 'DELETE FROM '.$this->table.' WHERE productID='.$this->db->pdb($productID) .' AND tagID='.$this->db->pdb($this->tagID());
    	$this->db->execute($sql);
    }

}
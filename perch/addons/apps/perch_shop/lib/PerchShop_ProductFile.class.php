<?php

class PerchShop_ProductFile extends PerchAPI_Base
{
    protected $table        = 'shop_product_files';
    protected $pk           = 'fileID';
    
    protected $index_table  = 'shop_index';
    protected $event_prefix = 'shop.productfile';


    public function file_size()
    {
        $size = $this->resourceFileSize();

        if ($size < 1048576) {
            $size = round($size/1024, 0).'<span class="unit">KB</span>';
        } else {
            $size = round($size/1024/1024, 0).'<span class="unit">MB</span>';
        }

        return $size;
    }

}
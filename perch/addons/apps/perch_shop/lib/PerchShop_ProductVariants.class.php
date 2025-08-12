<?php

class PerchShop_ProductVariants extends PerchShop_Products
{
	protected $runtime_restrictions = [
		[
			'field'          => 'status',
			'values'         => ['1'],
			'negative_match' => false,
			'match'          => 'all',
			'fuzzy'			 => false
		],
		[
			'field'          => 'parentID',
			'values'         => [''],
			'negative_match' => true,
			'match'          => 'all',
			'fuzzy'			 => false
		],
	];


	public function standard_where_callback(PerchQuery $Query)
    {
    	$Query->where[] = $this->deleted_date_column . ' IS NULL';		
        return $Query;
    }
}
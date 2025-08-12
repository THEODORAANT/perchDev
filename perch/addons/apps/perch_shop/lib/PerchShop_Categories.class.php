<?php

class PerchShop_Categories extends PerchShop_Factory
{
	public $api_method         = 'categories';
	public $api_list_method    = 'categories';
	public $singular_classname = 'PerchShop_Category';
	public $static_fields          = ['categoryTitle', 'categoryCreated'];
	public $remote_fields          = ['title', 'slug', 'status', 'description', 'parent'];
	
	protected $table               = 'shop_categories';
	protected $pk                  = 'categoryID';
	protected $index_table         = 'shop_index';
	protected $master_template	   = 'shop/categories/category.html';
	
	protected $default_sort_column = 'categoryTitle';
	protected $created_date_column = 'categoryCreated';



	public static function on_update($Event) 
	{
		PerchUtil::debug('Updated category callback!', 'success');

		$PerchCategory = $Event->subject;

		if (is_object($PerchCategory)) {
			$watched_set_id = PerchShop_Settings::get('category_set');
			if ($PerchCategory->setID() == $watched_set_id) {

				$API = new PerchAPI(1.0, 'perch_shop');
    			$Categories = new PerchShop_Categories($API);

    			$Category = $Categories->find($Categories->find_shop_id($PerchCategory->id()));

    			if (!$Category) {
    				return PerchShop_Categories::on_create($Event);
    			}

				
				$fields = [
    				'title'  => $PerchCategory->catTitle(),
    				'slug'   => $PerchCategory->catSlug(),
    				'description' => $PerchCategory->catTitle(),
    				'status' => '1',
    				'parent' => '0',
    			];

    			if ($PerchCategory->catParentID() > 0) {
		    		$fields['parent'] = $Categories->find_shop_id($PerchCategory->catParentID());
		    	}

		    	$Category = $Categories->find($Categories->find_shop_id($PerchCategory->id()));

		    	if ($Category) {
		    		$Category->update([
		    			'categoryTitle' => $PerchCategory->catTitle(),
						'categoryDynamicFields' => PerchUtil::json_safe_encode($fields),
		    			]);
		    	}
		    

			}
		}

	}

	public static function on_create($Event) 
	{
		$PerchCategory = $Event->subject;

		$API = new PerchAPI(1.0, 'perch_shop');
    	$Categories = new PerchShop_Categories($API);

		$fields = [
    				'title'  => $PerchCategory->catTitle(),
    				'slug'   => $PerchCategory->catSlug(),
    				'description' => $PerchCategory->catTitle(),
    				'status' => '1',
    				'parent' => null,
    			];

    	if ($PerchCategory->catParentID() > 0) {
    		$fields['parent'] = $Categories->find_shop_id($PerchCategory->catParentID());
    	}
   		   	

    	return $Categories->create([
    		'categoryTitle' => $PerchCategory->catTitle(),
    		'nativeCatID'   => $PerchCategory->id(),
    		'categoryDynamicFields' => PerchUtil::json_safe_encode($fields),
    	]);

	}

	public function find_shop_id($nativeCatID)
	{
		return $this->db->get_value($sql = 'SELECT categoryID FROM '.$this->table.' WHERE nativeCatID='.$this->db->pdb((int)$nativeCatID).' LIMIT 1');
	}

	public function find_category_path_from_shop_id($shopID)
	{
		$sql = 'SELECT c.catPath 
				FROM '.PERCH_DB_PREFIX.'categories c, '.PERCH_DB_PREFIX.'shop_categories m
				WHERE m.nativeCatID=c.catID
					AND m.categoryID='.$this->db->pdb($shopID).'
				LIMIT 1';
		return $this->db->get_value($sql);
	}

}
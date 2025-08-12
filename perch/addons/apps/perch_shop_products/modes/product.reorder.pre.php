<?php
	$Products = new PerchShop_Products($API);

  $products = $Products->all();
	$Form = new PerchForm('order');
		if ($Form->posted() && $Form->validate()) {
         if (isset($_POST['orders']) && $_POST['orders']!='') {
    	        $products = explode('&', $_POST['orders']);

    	        $sort_orders = array();
    	        	             if (PerchUtil::count($products)) {
                                	            foreach($products as $str) {

                                	             if (trim($str)!='') {
                                                                	                    $parts = explode('=', $str);
                                                                	                    $productID = str_replace(array('product[',']'), '', $parts[0]);
                                                                	                    $parentID = $parts[1];
                                                                	                      if ($parentID == 'root') $parentID = '0';

                                                                                                        	                    if (!isset($sort_orders[$parentID])) {
                                                                                                        	                        $sort_orders[$parentID] = 1;
                                                                                                        	                    }else{
                                                                                                        	                        $sort_orders[$parentID]++;
                                                                                                        	                    }

                	                    $order = $sort_orders[$parentID];

                	                    $Product = $Products->find($productID);
                	                    if (is_object($Product)) {
                	                        $Product->update_tree_position($parentID, $order);
                	                    }
                                                                	                    }

                                	            }
                                	            }

	    $Alert->set('success', PerchLang::get('Products orders successfully updated.'));
		PerchUtil::redirect(PERCH_LOGINPATH.'/addons/apps/perch_shop_products');

    	        }
		}



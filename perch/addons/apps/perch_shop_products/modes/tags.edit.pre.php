<?php

	$productID = PerchUtil::get('id');

	$Products   = new PerchShop_Products($API);
	$Product    = $Products->find($productID);

	$ProductTags	= new PerchShop_ProductTags($API);
	
	$edit_mode  	= false;
	$Option    		= false;
	$shop_id  		= false;
	$message		= false;
	$details 		= false;

	if (PerchUtil::get('id')) {
		$edit_mode         = true;
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/products/tags.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {

		$data = $Form->get_posted_content($Template, $Products, $Product);
		
		PerchUtil::debug($data);

		
		$tags = [];
		$created = false;

		if (isset($data['productDynamicFields'])) {
			$dyn = PerchUtil::json_safe_decode($data['productDynamicFields'], true);
			if (isset($dyn['tags'])) {
				$tags = $dyn['tags'];
				unset($dyn['tags']);
				$data['productDynamicFields'] = PerchUtil::json_safe_encode($dyn);
			}
		}


		$existing_tag_ids = $ProductTags->get_tag_ids($Product->id());

		if (PerchUtil::count($tags)) {

			$i = 1;

			
			#PerchUtil::debug($existing_tag_ids, 'success');

			// TAGS
			$MemberTags = new PerchMembers_Tags($API);

			foreach($tags as $tag) {
				PerchUtil::debug($tag, 'notice');

				// look up tag ID form members.
				$MemberTag = $MemberTags->find_or_create($tag['tag']);

				if ($MemberTag) {

					$tag_data = [
						'productID'        => $Product->id(),
						'tagID'            => $MemberTag->id(),
						'tagExpiry'        => $tag['expiry']['_default'],
						'tagOrder'         => $i,
						'tagDynamicFields' => PerchUtil::json_safe_encode($tag),
					];

					if ($tag['id']!='') {

						// remove this ID from the known IDs list
						$existing_tag_ids = array_diff($existing_tag_ids, [(int)$tag['id']]);

						$ProductTag = $ProductTags->find($tag['id']);
						$ProductTag->update($tag_data);
					}else{
						$ProductTag = $ProductTags->create($tag_data);
					}

					$i++;
				}

			}

			// delete tags
			if (PerchUtil::count($existing_tag_ids)) {
				// if there are any IDs left in this array then they've been deleted.
				foreach($existing_tag_ids as $id) {
					$ProductTag = $ProductTags->find($id);
					if ($ProductTag) $ProductTag->delete_from_product($Product->id());
				}
			}
			

		    $message = $HTML->success_message('Your product tags have been successfully edited.');
		}else{

			// delete tags
			if (PerchUtil::count($existing_tag_ids)) {
				// if there are any IDs left in this array then they've been deleted.
				foreach($existing_tag_ids as $id) {
					$ProductTag = $ProductTags->find($id);
					if ($ProductTag) $ProductTag->delete_from_product($Product->id());
				}
			}

			$message = $HTML->success_message('Your product tags have been successfully edited.');

		    ##$message = $HTML->failure_message('Sorry, that update was not successful.');
		}

		
	}


	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your product tags have been successfully created.');
	}


	
	$details = $ProductTags->get_array_for_editing($Product->id());
	
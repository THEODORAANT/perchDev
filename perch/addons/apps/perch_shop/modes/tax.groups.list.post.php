<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all tax groups'),
        'button'  => [
            'text' => $Lang->get('Add tax group'),
            'link' => $API->app_nav().'/tax/groups/edit/',
            'icon' => 'core/plus',
            'priv' => 'perch_shop.taxgroups.create',
        ],
    ], $CurrentUser);

	/* ----------------------------------------- SMART BAR ----------------------------------------- */
       include('_tax_smartbar.php');
	/* ----------------------------------------- /SMART BAR ----------------------------------------- */


    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'Title',
            'value'     => 'groupTitle',
            'sort'      => 'groupTitle',
            'edit_link' => 'groups/edit',
            'priv'      => 'perch_shop.taxgroups.edit',
        ]);

    $Listing->add_col([
            'title'     => 'Slug',
            'value'     => 'groupSlug',
            'sort'      => 'groupSlug',
        ]);
    
    $Listing->add_delete_action([
            'priv'   => 'perch_shop.taxgroups.delete',
            'inline' => true,
            'path'   => 'groups/delete',
        ]);

    echo $Listing->render($groups);

<?php include (PERCH_PATH.'/core/inc/sidebar_start.php'); ?>
<p><?php //echo PerchLang::get(''); ?></p>
<?php include (PERCH_PATH.'/core/inc/sidebar_end.php'); ?>
<?php include (PERCH_PATH.'/core/inc/main_start.php'); ?>
<?php include ('_subnav.php'); ?>

    <?php if ($CurrentUser->has_priv('perch_shop.taxbands.create')) { ?>
    <a class="add button" href="<?php echo PerchUtil::html($API->app_path('perch_shop').'/taxbands/edit'); ?>"><?php echo $Lang->get('Add tax band'); ?></a>
    <?php } // perch_shop.taxbands.create ?>
    
    <h1><?php echo $Lang->get('Listing all tax bands'); ?></h1>

	<?php
	/* ----------------------------------------- SMART BAR ----------------------------------------- */
       
	/* ----------------------------------------- /SMART BAR ----------------------------------------- */
    $Alert->output();

    echo $HTML->listing($taxbands, 
    		array('Title'), 
    		array('taxbandTitle'), 
            array(
                    'edit'   => 'edit',
                    'delete' => 'delete',
                ),
            array(
                'user'   => $CurrentUser,
                'edit'   => 'perch_shop.taxbands.edit',
                'delete' => 'perch_shop.taxbands.delete',
                )
            );

    echo $HTML->paging($Paging);
    ?>

<?php include (PERCH_PATH.'/core/inc/main_end.php'); ?>
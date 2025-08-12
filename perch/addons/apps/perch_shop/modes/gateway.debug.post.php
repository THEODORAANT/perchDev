<?php include (PERCH_PATH.'/core/inc/sidebar_start.php'); ?>
<p><?php //echo PerchLang::get(''); ?></p>
<?php include (PERCH_PATH.'/core/inc/sidebar_end.php'); ?>
<?php include (PERCH_PATH.'/core/inc/main_start.php'); ?>
<?php include ('_subnav.php'); ?>


    <h1><?php echo $Lang->get('Gateway info'); ?></h1>

	<?php
	/* ----------------------------------------- SMART BAR ----------------------------------------- */

	/* ----------------------------------------- /SMART BAR ----------------------------------------- */
    $Alert->output();

    if (isset($result)) {
        echo '<pre>';
        print_r($result);
        echo '</pre>';
    }


    ?>

<?php include (PERCH_PATH.'/core/inc/main_end.php'); ?>
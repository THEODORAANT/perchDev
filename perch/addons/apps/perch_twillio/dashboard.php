<?php


    $API   = new PerchAPI(1.0, 'perch_twillio');
    $Lang  = $API->get('Lang');

    $Paging = $API->get('Paging');
    $Paging->set_per_page(10);

    $Messages = new PerchTwillio_Messages($API);
    $messages = $Messages->all($Paging);

?>
<div class="widget">
	<h2>
		<?php echo $Lang->get('Messages'); ?>
		<a href="<?php echo PerchUtil::html(PERCH_LOGINPATH.'/addons/apps/perch_twillio/edit/'); ?>" class="add button"><?php echo $Lang->get('Add Message'); ?></a>
	</h2>
	<div class="bd">
		<?php
			if (PerchUtil::count($messages)) {
				echo '<ul>';
				foreach($messages as $Message) {
					echo '<li>';
						echo '<a href="'.PerchUtil::html(PERCH_LOGINPATH.'/addons/apps/perch_twillio/edit/?id='.$Message->id()).'">';
							echo PerchUtil::html($Message->messageText());
							echo '<span class="note">'.PerchUtil::html(date('d M Y, h:i s', strtotime($Message->messageDateTime()))).'</span>';
						echo '</a>';
					echo '</li>';
				}
				echo '</ul>';
			}
		?>
	</div>
</div>

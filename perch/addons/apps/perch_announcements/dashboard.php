<?php


    $API   = new PerchAPI(1.0, 'perch_announcements');
    $Lang  = $API->get('Lang');

    $Paging = $API->get('Paging');
    $Paging->set_per_page(10);

    $Announcements = new PerchAnnouncements_Announcements($API);
    $announcements = $Announcements->all($Paging);

?>
<div class="widget">
	<h2>
		<?php echo $Lang->get('Announcements'); ?>
		<a href="<?php echo PerchUtil::html(PERCH_LOGINPATH.'/addons/apps/perch_announcements/edit/'); ?>" class="add button"><?php echo $Lang->get('Add Announcement'); ?></a>
	</h2>
	<div class="bd">
		<?php
			if (PerchUtil::count($announcements)) {
				echo '<ul>';
				foreach($announcements as $Announcement) {
					echo '<li>';
						echo '<a href="'.PerchUtil::html(PERCH_LOGINPATH.'/addons/apps/perch_announcements/edit/?id='.$Announcement->id()).'">';
							echo PerchUtil::html($Announcement->announcementContent());
							echo '<span class="note">'.PerchUtil::html(date('d M Y, h:i s', strtotime($Announcement->announcementCreatedDate()))).'</span>';
						echo '</a>';
					echo '</li>';
				}
				echo '</ul>';
			}
		?>
	</div>
</div>

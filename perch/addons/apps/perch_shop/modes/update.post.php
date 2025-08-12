<div class="inner">
<div style="update-box">
	<div class="hd">
	    <h1>Shop Software Update</h1>
	</div>

	<div class="bd">
	    <ul class="progress-list">
	<?php
	    echo '<li class="progress-item progress-success">'.PerchUI::icon('core/circle-check').' Updated to version '.PERCH_SHOP_VERSION.'.</li>';

	?>
		</ul>
	</div>
	<div class="submit">
		<a href="<?php echo $Perch->get_page(true); ?>" class="button button-simple action-success">Continue</a>
	</div>
</div>
</div>
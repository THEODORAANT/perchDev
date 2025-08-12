<?php
	include('../runtime/runtime.php');
	$s = filter_input(INPUT_GET, 's', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	if ($s) {
		echo PerchUtil::urlify($s);
	}

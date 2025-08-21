<?php

// Register the package processing task to run daily
include __DIR__.'/scripts/process_packages.php';

PerchScheduledTasks::register_task('perch_shop', 'process_packages', 1440, 'perch_shop_process_packages');


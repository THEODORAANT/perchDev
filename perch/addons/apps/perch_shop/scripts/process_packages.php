<?php

include __DIR__.'/../../../../runtime.php';

$API = new PerchAPI(1, 'perch_shop');
$DB  = PerchDB::fetch();

// Fetch active packages
$packages = $DB->get_rows('SELECT * FROM shop_packages WHERE active=1');

$Runtime = PerchShop_Runtime::fetch();

if (is_array($packages)) {
    foreach ($packages as $package) {
        $id          = (int)$package['packageID'];
        $billingType = $package['billingType'] ?? 'monthly';

        if ($billingType === 'prepaid') {
            // Already paid? skip
            if (empty($package['paidAt'])) {
                // Take payment once at creation
                perch_shop_checkout('manual');
                $Order = $Runtime->get_active_order();
                if ($Order) {
                    $sql = 'UPDATE shop_packages SET orderID='.$DB->pdb($Order->id()).', paidAt=NOW(), status="paid" WHERE packageID='.$DB->pdb($id);
                    $DB->execute($sql);
                }
            }
            // Skip further billing
            continue;
        }

        // Monthly billing
        $nextBilling = $package['nextBilling'] ?? null;
        if ($nextBilling === null || strtotime($nextBilling) <= time()) {
            perch_shop_checkout('manual');
            $Order = $Runtime->get_active_order();
            if ($Order) {
                $sql = 'UPDATE shop_packages SET orderID='.$DB->pdb($Order->id()).', nextBilling=DATE_ADD(CURDATE(), INTERVAL 1 MONTH) WHERE packageID='.$DB->pdb($id);
                $DB->execute($sql);
            }
        }
    }

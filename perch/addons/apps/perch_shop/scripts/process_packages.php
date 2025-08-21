<?php

/**
 * Process monthly packages and convert them into orders.
 *
 * Loads all packages scheduled for the current month, adds their items to a
 * cart using the PerchShop runtime and attempts checkout. The package record is
 * updated to reflect success or failure and confirmation emails are optionally
 * sent using existing order email templates.
 *
 * @param string $last_run Timestamp of last execution provided by scheduler.
 *
 * @return array Result information for the scheduler log.
 */
function perch_shop_process_packages($last_run = null)
{
    $API = new PerchAPI(1.0, 'perch_shop');
    $DB  = $API->get('DB');

    // Ensure we have a session for cart operations
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $Runtime = PerchShop_Runtime::fetch();

    $start = date('Y-m-01');
    $end   = date('Y-m-t');

    $sql = 'SELECT * FROM '.PERCH_DB_PREFIX.'shop_packages '
         . 'WHERE packageStatus='.$DB->pdb('pending')
         . ' AND packageDate BETWEEN '.$DB->pdb($start)
         . ' AND '.$DB->pdb($end);

    $packages = $DB->get_rows($sql);
    $processed = 0;

    if (PerchUtil::count($packages)) {
        foreach ($packages as $package) {
            // reset cart for this package
            $Runtime->reset_after_logout();

            $item_sql = 'SELECT productID, itemQty FROM '
                     . PERCH_DB_PREFIX.'shop_package_items '
                     . 'WHERE packageID='.$DB->pdb($package['packageID']);
            $items = $DB->get_rows($item_sql);

            if (PerchUtil::count($items)) {
                foreach ($items as $item) {
                    $Runtime->add_to_cart($item['productID'], (int)$item['itemQty']);
                }

                // Attempt checkout using the manual gateway
                $Runtime->checkout('manual');
                $Order = $Runtime->Order;

                if ($Order && $Order->id()) {
                    // Update package as processed and store order ID
                    $DB->update(
                        PERCH_DB_PREFIX.'shop_packages',
                        ['packageStatus' => 'processed', 'orderID' => $Order->id()],
                        'packageID',
                        $package['packageID']
                    );

                    // Send confirmation emails for this order status
                    $Emails = new PerchShop_Emails($API);
                    $emails = $Emails->get_for_status($Order->orderStatus());
                    if (PerchUtil::count($emails)) {
                        foreach ($emails as $Email) {
                            $Order->send_order_email($Email);
                        }
                    }

                    $processed++;
                } else {
                    // Failed checkout
                    $DB->update(
                        PERCH_DB_PREFIX.'shop_packages',
                        ['packageStatus' => 'failed'],
                        'packageID',
                        $package['packageID']
                    );
                }
            } else {
                // No items found for package
                $DB->update(
                    PERCH_DB_PREFIX.'shop_packages',
                    ['packageStatus' => 'failed'],
                    'packageID',
                    $package['packageID']
                );
            }

            // Clear cart/session for next package
            $Runtime->reset_after_logout();
        }
    }

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    $message = $processed
        ? $processed.' packages processed.'
        : 'No packages to process.';

    return [
        'result'  => 'OK',
        'message' => $message,
    ];
}


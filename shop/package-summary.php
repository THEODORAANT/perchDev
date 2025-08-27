<?php include('../perch/runtime.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantities = perch_post('qty');
    $removals = perch_post('remove');
    if (is_array($quantities)) {
        foreach ($quantities as $itemID => $qty) {
            perch_shop_package_update_item($itemID, (int)$qty);
        }
    }
    if (is_array($removals)) {
        foreach ($removals as $itemID => $doRemove) {
            if ($doRemove) {
                perch_shop_package_remove_item($itemID);
            }
        }
    }
    if (perch_post('action') === 'checkout') {
        try {
            $package = perch_shop_create_package([]);
            if ($package) {
                perch_shop_update_package_status('confirmed');
                PerchUtil::redirect('checkout.php');
            }
        } catch (Exception $e) {
            // fall through to error redirect
        }
        PerchUtil::redirect('package-builder.php?error=package_exists');
    }
}
?>
<form method="post">
    <?php perch_shop_package_contents([
        'template' => 'package-summary/summary.html',
    ]); ?>
    <button type="submit" name="action" value="update">Update package</button>
    <button type="submit" name="action" value="checkout">Proceed to checkout</button>
</form>

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
}
?>
<form method="post">
    <?php perch_shop_package_contents([
        'template' => 'package-summary/summary.html',
    ]); ?>
    <button type="submit">Update package</button>
</form>
<p><a href="checkout.php">Proceed to checkout</a></p>

<?php include('../perch/runtime.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $variants = perch_post('variant');
    $quantities = perch_post('qty');
    if (is_array($variants) && is_array($quantities)) {
        foreach ($variants as $idx => $variantID) {
            $qty = isset($quantities[$idx]) ? (int)$quantities[$idx] : 0;
            if ($qty > 0 && $variantID) {
                perch_shop_package_add_item($variantID, $qty);
            }
        }
    }
    PerchUtil::redirect('package-summary.php');
}
?>
<form method="post">
    <?php perch_shop_products([
        'template' => 'package-builder/product.html',
        'variants' => true,
    ]); ?>
    <button type="submit">Save package</button>
</form>

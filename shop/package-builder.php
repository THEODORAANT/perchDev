<?php include('../perch/runtime.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $months   = (int) perch_post('months');
    $variants = perch_post('variant');
    if ($months && is_array($variants)) {
        for ($i = 0; $i < $months; $i++) {
            $variantID = $variants[$i] ?? null;
            if ($variantID) {
                perch_shop_package_add_item($variantID, 1);
            }
        }
    }
    PerchUtil::redirect('package-summary.php');
}
?>
<form method="post">
    <label for="months">Months</label>
    <select id="months" name="months">
        <option value="4">4</option>
        <option value="6">6</option>
        <option value="12">12</option>
    </select>

    <?php for ($i = 1; $i <= 12; $i++): ?>
    <div class="package-month">
        <label>Month <?php echo $i; ?></label>
        <select name="variant[]">
            <?php perch_shop_products([
                'template' => 'package-builder/variant-options.html',
                'variants'  => true,
            ]); ?>
        </select>
    </div>
    <?php endfor; ?>

    <button type="submit">Save package</button>
</form>

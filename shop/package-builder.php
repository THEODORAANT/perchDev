<?php
include('../perch/runtime.php');

/* -----------------------------------------------------------
   package-builder.php
   -----------------------------------------------------------
   - Server-side only (no JS)
   - Persists a draft package across submits via PHP session
   - Works with Perch Shop product templates
   ----------------------------------------------------------- */

if (session_status() === PHP_SESSION_NONE) session_start();

/* ---------- Helpers ---------- */
function uuid_like() { return bin2hex(random_bytes(8)); }
function clamp_months($v){ $m = max(1, (int)$v); return min($m, 36); }
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

/* ---------- Load existing draft ---------- */
$draft = $_SESSION['draft_package'] ?? null;
if(isset($_GET["UNSET"])){
 unset($_SESSION['draft_package']);

}
/* ---------- Handle POST ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* Finalize / complete package */
    if (isset($_POST['complete_package'])) {
        // TODO: Persist $draft to DB / create order / etc. before clearing
       $new_package=[
                                     'uuid'      => $_SESSION['draft_package']['id'],
                                     'created' => date('Y-m-d H:i:s'),
                                     'months' => $_SESSION['draft_package']['months'],
                                     'status'  => $_SESSION['draft_package']['status'],
                                     'billing_type' => $_SESSION['draft_package']['billing'],
                                     // optionally store user/customer identifier here
                                 ];
       $package = perch_shop_create_package($new_package);
                    if ($package) {

                    $_SESSION['perch_shop_package_id']= $_SESSION['draft_package']['id'];
                    perch_shop_add_package_item($package->id(), $_SESSION['draft_package']['selections']);
                          unset($_SESSION['draft_package']);
                            //$draft = null;

                    }

        // Optional: redirect to a confirmation screen
      //header('Location: /order/package-summary'); exit;
    } else {
        // Create/attach a draft ID
        $posted_id = isset($_POST['package_id']) ? (string)$_POST['package_id'] : null;

        if (!$draft || !$posted_id || ($posted_id !== ($draft['id'] ?? ''))) {
            $draft = [
                'id'      => $posted_id ?: uuid_like(),
                'created' => time(),
                'status'  => 'in_progress',
                // optionally store user/customer identifier here
            ];
        }

        // Remember months (from any submit)
        if (isset($_POST['months'])) {
            $draft['months'] = clamp_months($_POST['months']);
        } elseif (!isset($draft['months'])) {
            $draft['months'] = 1;
        }

        // Remember billing type
        if (isset($_POST['billing_type'])) {
            $billing = ($_POST['billing_type'] === 'monthly') ? 'monthly' : 'prepaid';
            $draft['billing'] = $billing;
        } elseif (!isset($draft['billing'])) {
            $draft['billing'] = 'prepaid';
        }

        /* --------- SELECTION CAPTURE – ADAPT IF NEEDED ---------
           If your Perch item template posts:
              selections[<month>][dose]
              selections[<month>][qty]
              selections[<month>][product_id]
           …this will capture them into the session.
           ------------------------------------------------------ */
        if (isset($_POST['selections']) && is_array($_POST['selections'])) {
            foreach ($_POST['selections'] as $m => $row) {
                $m = (int)$m;
                if ($m < 1) continue;

                // Merge/overwrite that month’s selection
                $draft['selections'][$m] = [
                    'product_id'       => isset($row['dose']) ? (string)$row['dose'] : null,
                    'qty'        => isset($row['qty'])  ? max(1, (int)$row['qty']) : 1,
                    'packageID'=>$posted_id
                   // 'product_id' => isset($row['product_id']) ? (string)$row['product_id'] : null,
                ];
            }
        }

        $_SESSION['draft_package'] = $draft;
    }
}

/* ---------- Working values for rendering ---------- */
$packageId = $draft['id']     ?? uuid_like();
$months    = $draft['months'] ?? 1;
$billing   = $draft['billing'] ?? 'prepaid';
$selections = $draft['selections'] ?? [];

/* ---------- Make vars available to Perch templates ---------- */
if (function_exists('PerchSystem::set_var')) {
    PerchSystem::set_var('package_id', $packageId);
    PerchSystem::set_var('months',    $months);
    PerchSystem::set_var('billing_type', $billing);
}

/* ---------- (Optional) Read a months change from GET, or show a selector above ---------- */
if (isset($_GET['months'])) {
    $months = clamp_months($_GET['months']);
    if (!isset($draft)) $draft = ['id' => $packageId, 'created' => time(), 'status' => 'in_progress'];
    $draft['months'] = $months;
    $_SESSION['draft_package'] = $draft;
    if (function_exists('PerchSystem::set_var')) {
        PerchSystem::set_var('months', $months);
    }
}

/* ---------- Start of page output ---------- */

    perch_layout('getStarted/header', [
        'page_title' => perch_page_title(true),
    ]);
?>

  <style>
    .wrap { max-width: 920px; margin: 2rem auto; padding: 0 1rem; }
    .row { margin-bottom: 1.25rem; }
    .package-month { border: 1px solid #ddd; padding: 1rem; border-radius: .5rem; margin-bottom: 1rem; }
    .actions { display: flex; gap: .5rem; }
    .btn { display: inline-block; padding: .6rem 1rem; border: 1px solid #333; background:#fff; cursor: pointer; border-radius: .375rem; }
    .btn-primary { background: #0d6efd; color: #fff; border-color: #0d6efd; }
    .muted { color: #666; font-size: .9rem; }
    .selections { background: #fafafa; padding: .75rem; border-radius: .5rem; border: 1px dashed #ccc; }
  </style>

<div class="wrap">

  <h1>Build your package</h1>
  <p class="muted">Package ID: <strong><?= h($packageId) ?></strong></p>
  <p class="muted">Billing: <strong><?= h($billing) ?></strong></p>

  <!-- Quick months selector (posts to this same file) -->
  <form method="post" class="row" style="display:flex; align-items:center; gap:.5rem;">
    <input type="hidden" name="package_id" value="<?= h($packageId) ?>">
    <label for="months">Months:</label>
    <select  class="form-control mb-4 dose_dropdown" id="months" name="months">
      <?php foreach ([1,3,6,12] as $opt): ?>
        <option value="<?= $opt ?>" <?= ($months == $opt ? 'selected' : '') ?>><?= $opt ?></option>
      <?php endforeach; ?>
    </select>
    <label for="billing_type">Billing:</label>
    <select class="form-control mb-4" id="billing_type" name="billing_type">
      <option value="prepaid" <?= ($billing === 'prepaid' ? 'selected' : '') ?>>Prepaid</option>
      <option value="monthly" <?= ($billing === 'monthly' ? 'selected' : '') ?>>Monthly</option>
    </select>
    <button class="btn" type="submit" name="save_options" value="1">Set</button>
  </form>

  <!-- Per-month product pickers
       NOTE: Do NOT wrap these in another <form> if your Perch product
             template already renders its own <perch:form> (as you shared).
             Each month’s "Add to package" will POST back here with the
             hidden package_id/months provided by that template.
  -->
  <?php for ($i = 1; $i <= $months; $i++): ?>
    <div class="package-month">
      <h3>Month <?= (int)$i ?></h3>
      <?php
        // Allow template to know current month + draft identifiers

        // Render your Perch product block (your template should include
        // hidden inputs for package_id and months, and name fields like
        // selections[<month>][...])
        if (function_exists('perch_shop_products')) {
                    PerchSystem::set_var('month', $i);
                    PerchSystem::set_var('package_id', $packageId);
                    PerchSystem::set_var('months', $months);
                    PerchSystem::set_var('billing_type', $billing);

            perch_shop_products([
                'category' => 'products/weight-loss',
                'template' => 'products/package-builder/variant-options'
            ]);
        } else {
            echo '<p class="muted">perch_shop_products() not available in this environment.</p>';
        }
      ?>

      <?php if (!empty($selections[$i])): ?>
        <div class="selections">
          <strong>Saved for month <?= (int)$i ?>:</strong>
          <div>Dose: <?= h($selections[$i]['dose'] ?? '-') ?></div>
          <div>Qty: <?= h($selections[$i]['qty'] ?? '1') ?></div>
          <div>Product ID: <?= h($selections[$i]['product_id'] ?? '-') ?></div>
        </div>
      <?php endif; ?>
    </div>
  <?php endfor; ?>

  <!-- Finalize / Complete -->
  <form method="post" class="actions">
    <input type="hidden" name="package_id" value="<?= h($packageId) ?>">
    <input type="hidden" name="months"     value="<?= (int)$months ?>">
    <input type="hidden" name="billing_type" value="<?= h($billing) ?>">
    <button class="btn" type="submit" name="save_package" value="1">Save (keep editing)</button>
    <button class="btn btn-primary" type="submit" name="complete_package" value="1">Complete package</button>
  </form>

</div>
        <?php
      perch_layout('getStarted/footer');?>


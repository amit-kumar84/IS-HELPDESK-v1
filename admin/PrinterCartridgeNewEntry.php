
<?php
function esc($value)
{
  return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$flashMessage = '';
$flashType = 'info';
$model = '';
$make = '';
$cartridgeNo = '';
$ttlPagePrint = '';
$rate = '';
$partNumber = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sub'])) {
  $model = trim($_POST['model'] ?? '');
  $make = trim($_POST['make'] ?? '');
  $cartridgeNo = trim($_POST['cartridge_no'] ?? '');
  $ttlPagePrint = trim($_POST['ttl_page_prnt'] ?? '');
  $rate = trim($_POST['rate'] ?? '');
  $partNumber = trim($_POST['part_number'] ?? '');

  if ($model === '' || $make === '' || $cartridgeNo === '' || $rate === '' || $partNumber === '') {
    $flashMessage = 'Fill in all required fields before saving the printer-cartridge record.';
    $flashType = 'danger';
  } else {
    $checkStmt = mysqli_prepare($link, "SELECT 1 FROM `printer_cartridge_list` WHERE `model` = ? LIMIT 1");
    mysqli_stmt_bind_param($checkStmt, 's', $model);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if (mysqli_stmt_num_rows($checkStmt) > 0) {
      $flashMessage = 'Printer model already exists. Use the edit tab to change its details.';
      $flashType = 'danger';
    } else {
      $insertStmt = mysqli_prepare($link, "INSERT INTO `printer_cartridge_list`(`model`, `make`, `cartridge_no`, `ttl_page_prnt`, `rate`, `part_no`) VALUES (?, ?, ?, ?, ?, ?)");
      mysqli_stmt_bind_param($insertStmt, 'ssssss', $model, $make, $cartridgeNo, $ttlPagePrint, $rate, $partNumber);

      if (mysqli_stmt_execute($insertStmt)) {
        $flashMessage = 'Printer-cartridge has been added successfully.';
        $flashType = 'success';
        $model = $make = $cartridgeNo = $ttlPagePrint = $rate = $partNumber = '';
      } else {
        $flashMessage = 'Unknown error while saving the printer-cartridge record.';
        $flashType = 'danger';
      }
    }
  }
}

$printerStats = mysqli_query($link, "SELECT COUNT(*) AS total_rows, COUNT(DISTINCT `cartridge_no`) AS unique_cartridges, COUNT(DISTINCT `make`) AS unique_makes FROM `printer_cartridge_list`");
$printerStatsRow = mysqli_fetch_assoc($printerStats) ?: [
  'total_rows' => 0,
  'unique_cartridges' => 0,
  'unique_makes' => 0,
];
?>

<div class="content printer-cart-page">
  <div class="page-head printer-cart-hero">
    <div class="ic"><i class="fa-solid fa-print"></i></div>
    <div class="printer-cart-copy">
      <div class="printer-cart-crumb">Cartridges / Add Printer</div>
      <h2>Add Printer-Cartridge</h2>
      <div class="sub">Create the printer model mapping that links a device to its cartridge, page yield, and pricing details.</div>
    </div>
    <div class="printer-cart-metrics">
      <div class="printer-metric">
        <span>Printer mappings</span>
        <strong><?= number_format((int)$printerStatsRow['total_rows']) ?></strong>
      </div>
      <div class="printer-metric">
        <span>Cartridge types</span>
        <strong><?= number_format((int)$printerStatsRow['unique_cartridges']) ?></strong>
      </div>
      <div class="printer-metric">
        <span>Brands / makes</span>
        <strong><?= number_format((int)$printerStatsRow['unique_makes']) ?></strong>
      </div>
    </div>
  </div>

  <?php if ($flashMessage !== ''): ?>
    <div class="alert alert-<?= esc($flashType) ?> printer-cart-flash"><?= esc($flashMessage) ?></div>
  <?php endif; ?>

  <div class="printer-cart-layout">
    <div class="card printer-cart-form-card">
      <div class="card-title"><i class="fa-solid fa-circle-plus"></i> Printer Mapping Form</div>
      <p class="text-muted">Enter the printer details once. This tab keeps the cartridge registry consistent for requests, stock usage, and reports.</p>

      <form name="add_prntr_cartg" method="post" class="printer-cart-form">
        <div class="form-grid">
          <div class="form-row">
            <label for="model">Model <span class="req">*</span></label>
            <input id="model" name="model" type="text" value="<?= esc($model) ?>" placeholder="Printer model" required>
          </div>

          <div class="form-row">
            <label for="make">Make <span class="req">*</span></label>
            <input id="make" name="make" type="text" value="<?= esc($make) ?>" placeholder="Printer make" required>
          </div>

          <div class="form-row">
            <label for="cartridge_no">Cartridge Number <span class="req">*</span></label>
            <input id="cartridge_no" name="cartridge_no" type="text" value="<?= esc($cartridgeNo) ?>" placeholder="Cartridge number" required>
          </div>

          <div class="form-row">
            <label for="ttl_page_prnt">Total Page's Print</label>
            <input id="ttl_page_prnt" name="ttl_page_prnt" type="text" value="<?= esc($ttlPagePrint) ?>" placeholder="Expected page yield">
          </div>

          <div class="form-row">
            <label for="rate">Rate <span class="req">*</span></label>
            <input id="rate" name="rate" type="text" value="<?= esc($rate) ?>" placeholder="Printer rate" required>
          </div>

          <div class="form-row">
            <label for="part_number">Part Number <span class="req">*</span></label>
            <input id="part_number" name="part_number" type="text" maxlength="12" value="<?= esc($partNumber) ?>" placeholder="Part number" required>
          </div>
        </div>

        <div class="printer-cart-form-help">Use the exact model name already printed on the device so requests and issue records match cleanly later.</div>

        <div class="flex-end printer-cart-actions">
          <button type="submit" name="sub" id="butt" class="btn btn-navy printer-cart-submit"><i class="fa-solid fa-sparkles"></i> Save Printer-Cartridge</button>
        </div>
      </form>
    </div>

    <div class="card printer-cart-side-card">
      <div class="card-title"><i class="fa-solid fa-wand-magic-sparkles"></i> Registry Notes</div>
      <div class="printer-cart-checklist">
        <div class="printer-tip">
          <strong>Model identity</strong>
          <span>Each model should be unique so cartridge requests can resolve to one printer definition.</span>
        </div>
        <div class="printer-tip">
          <strong>Cartridge mapping</strong>
          <span>Link the correct cartridge number to keep stock deduction and reporting accurate.</span>
        </div>
        <div class="printer-tip">
          <strong>Usage info</strong>
          <span>Page yield, rate, and part number make the registry more useful for support and procurement.</span>
        </div>
      </div>

      <div class="printer-mini-card">
        <div class="printer-mini-title">Polished printer catalog</div>
        <div class="printer-mini-text">This view is designed to feel faster and cleaner while still matching the government-style dashboard shell.</div>
      </div>
    </div>
  </div>
</div>
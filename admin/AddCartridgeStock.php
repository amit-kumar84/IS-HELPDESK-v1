

<?php
function esc($value)
{
  return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$flashMessage = '';
$flashType = 'info';
$cartridge = '';
$color = 'BLACK';
$sl_no = '';
$ttl_page_prnt = '';
$rate = '';
$part_number = '';
$recvd_QTY = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sub'])) {
  $cartridge = trim($_POST['cartridge'] ?? '');
  $color = trim($_POST['color'] ?? 'BLACK');
  $sl_no = trim($_POST['sl_no'] ?? '');
  $ttl_page_prnt = trim($_POST['ttl_page_prnt'] ?? '');
  $rate = trim($_POST['rate'] ?? '');
  $part_number = trim($_POST['part_number'] ?? '');
  $recvd_QTY = trim($_POST['recvd_QTY'] ?? '');
  $receivedAmount = (int)$recvd_QTY;

  if ($cartridge === '' || $sl_no === '' || $rate === '' || $part_number === '' || $receivedAmount <= 0) {
    $flashMessage = 'Fill all required fields with a valid received quantity before saving the cartridge.';
    $flashType = 'danger';
  } else {
    $checkStmt = mysqli_prepare($link, "SELECT 1 FROM `cartridge_stock_list` WHERE `cartridge_no` = ? LIMIT 1");
    mysqli_stmt_bind_param($checkStmt, 's', $cartridge);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if (mysqli_stmt_num_rows($checkStmt) > 0) {
      $flashMessage = 'Cartridge is already added. Update the existing record instead.';
      $flashType = 'danger';
    } else {
      date_default_timezone_set('Asia/Kolkata');
      $stock_add_dt = date('Y-m-d');
      $lastIssueQty = 0;
      $lastIssueNo = '';
      $lastIssueDate = '';
      $insertStmt = mysqli_prepare($link, "INSERT INTO `cartridge_stock_list`(`cartridge_no`, `color`, `cartridge_serial_no`, `no_of_page`, `cost_of_toner`, `part_no`, `stock_qty`, `last_received_qty`, `last_received_date`, `last_issue_qty`, `last_issue_no`, `last_issue_date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      mysqli_stmt_bind_param($insertStmt, 'ssssssiisiss', $cartridge, $color, $sl_no, $ttl_page_prnt, $rate, $part_number, $receivedAmount, $receivedAmount, $stock_add_dt, $lastIssueQty, $lastIssueNo, $lastIssueDate);

      if (mysqli_stmt_execute($insertStmt)) {
        $flashMessage = 'New cartridge has been added successfully.';
        $flashType = 'success';
        $cartridge = $sl_no = $ttl_page_prnt = $rate = $part_number = $recvd_QTY = '';
        $color = 'BLACK';
      } else {
        $flashMessage = 'Unknown error while adding the cartridge: ' . mysqli_stmt_error($insertStmt);
        $flashType = 'danger';
      }
    }
  }
}

$cartridgeStats = mysqli_query($link, "SELECT COUNT(*) AS total_rows, COUNT(DISTINCT `cartridge_no`) AS unique_cartridges, COUNT(DISTINCT `color`) AS unique_colors, COALESCE(SUM(`stock_qty`),0) AS total_stock FROM `cartridge_stock_list`");
$cartridgeStatsRow = mysqli_fetch_assoc($cartridgeStats) ?: [
  'total_rows' => 0,
  'unique_cartridges' => 0,
  'unique_colors' => 0,
  'total_stock' => 0,
];
?>

<div class="content cartridge-create-page">
  <div class="page-head cartridge-create-hero">
    <div class="ic"><i class="fa-solid fa-box-open"></i></div>
    <div class="cartridge-create-copy">
      <div class="cartridge-create-crumb">Cartridges / Add Stock</div>
      <h2>Add New Cartridge</h2>
      <div class="sub">Register a fresh cartridge entry with its color, serial number, print yield, rate, and starting quantity in one polished flow.</div>
    </div>
    <div class="cartridge-create-metrics">
      <div class="cartridge-create-metric">
        <span>Records</span>
        <strong><?= number_format((int)$cartridgeStatsRow['total_rows']) ?></strong>
      </div>
      <div class="cartridge-create-metric">
        <span>Cartridge types</span>
        <strong><?= number_format((int)$cartridgeStatsRow['unique_cartridges']) ?></strong>
      </div>
      <div class="cartridge-create-metric">
        <span>Total stock</span>
        <strong><?= number_format((int)$cartridgeStatsRow['total_stock']) ?></strong>
      </div>
      <div class="cartridge-create-metric">
        <span>Color variants</span>
        <strong><?= number_format((int)$cartridgeStatsRow['unique_colors']) ?></strong>
      </div>
    </div>
  </div>

  <?php if ($flashMessage !== ''): ?>
    <div class="alert alert-<?= esc($flashType) ?> cartridge-create-flash"><?= esc($flashMessage) ?></div>
  <?php endif; ?>

  <div class="cartridge-create-layout">
    <div class="card cartridge-create-form-card">
      <div class="card-title"><i class="fa-solid fa-circle-plus"></i> Cartridge Registration Form</div>
      <p class="text-muted">Enter the cartridge details once. This tab captures the stock record that powers issue tracking, reports, and inventory updates.</p>

      <form name="add_prntr_cartg" method="post" class="cartridge-create-form">
        <div class="form-grid">
          <div class="form-row">
            <label for="cartridge">Cartridge <span class="req">*</span></label>
            <input id="cartridge" name="cartridge" type="text" value="<?= esc($cartridge) ?>" placeholder="Cartridge name or code" required>
          </div>

          <div class="form-row">
            <label for="color">Color <span class="req">*</span></label>
            <select id="color" name="color" required>
              <option value="BLACK" <?= $color === 'BLACK' ? 'selected' : '' ?>>BLACK</option>
              <option value="YELLOW" <?= $color === 'YELLOW' ? 'selected' : '' ?>>YELLOW</option>
              <option value="CYAN" <?= $color === 'CYAN' ? 'selected' : '' ?>>CYAN</option>
              <option value="MAGENTA" <?= $color === 'MAGENTA' ? 'selected' : '' ?>>MAGENTA</option>
            </select>
          </div>

          <div class="form-row">
            <label for="sl_no">Serial No <span class="req">*</span></label>
            <input id="sl_no" name="sl_no" type="text" value="<?= esc($sl_no) ?>" placeholder="Cartridge serial number" required>
          </div>

          <div class="form-row">
            <label for="ttl_page_prnt">Total Page's Print</label>
            <input id="ttl_page_prnt" name="ttl_page_prnt" type="text" value="<?= esc($ttl_page_prnt) ?>" placeholder="Expected page yield">
          </div>

          <div class="form-row">
            <label for="rate">Rate <span class="req">*</span></label>
            <input id="rate" name="rate" type="text" value="<?= esc($rate) ?>" placeholder="Cartridge rate" required>
          </div>

          <div class="form-row">
            <label for="part_number">Part Number <span class="req">*</span></label>
            <input id="part_number" name="part_number" type="text" maxlength="12" value="<?= esc($part_number) ?>" placeholder="Part number" required>
          </div>

          <div class="form-row">
            <label for="recvd_QTY">Received Quantity <span class="req">*</span></label>
            <input id="recvd_QTY" name="recvd_QTY" type="number" min="1" step="1" value="<?= esc($recvd_QTY) ?>" placeholder="Starting stock quantity" required>
          </div>
        </div>

        <div class="cartridge-create-form-help">The current date is stamped automatically, so the new cartridge record is ready for issue, stock, and reporting workflows right after save.</div>

        <div class="flex-end cartridge-create-actions">
          <button type="submit" name="sub" id="butt" class="btn btn-navy cartridge-create-submit"><i class="fa-solid fa-sparkles"></i> Save Cartridge</button>
        </div>
      </form>
    </div>

    <div class="card cartridge-create-side-card">
      <div class="card-title"><i class="fa-solid fa-wand-magic-sparkles"></i> Quick Flow</div>
      <div class="cartridge-create-checklist">
        <div class="cartridge-create-tip">
          <strong>Step 1</strong>
          <span>Enter the cartridge name or code exactly as it should appear in the stock register.</span>
        </div>
        <div class="cartridge-create-tip">
          <strong>Step 2</strong>
          <span>Fill the serial, page yield, rate, part number, and the received quantity together.</span>
        </div>
        <div class="cartridge-create-tip">
          <strong>Step 3</strong>
          <span>Save the form and the new cartridge becomes available for inventory and issue tracking.</span>
        </div>
      </div>

      <div class="cartridge-create-mini-card">
        <div class="cartridge-create-mini-title">Color-rich workflow</div>
        <div class="cartridge-create-mini-text">The refreshed layout uses soft glass panels, animated gradients, and clearer spacing so the cartridge section feels more refined and faster to use.</div>
      </div>
    </div>
  </div>
</div>
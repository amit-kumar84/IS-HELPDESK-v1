
<?php
function esc($value)
{
	return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$flashMessage = '';
$flashType = 'info';
$selectedCartridge = '';
$selectedColor = 'BLACK';
$receivedQty = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cartridge'], $_POST['color'], $_POST['stck_recvd'])) {
	$selectedCartridge = trim($_POST['cartridge'] ?? '');
	$selectedColor = trim($_POST['color'] ?? 'BLACK');
	$receivedQty = trim($_POST['stck_recvd'] ?? '');
	$receivedAmount = (int)$receivedQty;

	if ($selectedCartridge === '' || $receivedAmount <= 0) {
		$flashMessage = 'Select a cartridge and enter a valid received quantity.';
		$flashType = 'danger';
	} else {
		date_default_timezone_set('Asia/Kolkata');
		$rcvd_dt = date('Y-m-d');

		$stmt = mysqli_prepare($link, "SELECT COALESCE(`stock_qty`,0) AS stock_qty FROM `cartridge_stock_list` WHERE `cartridge_no` = ? AND `color` = ? LIMIT 1");
		mysqli_stmt_bind_param($stmt, 'ss', $selectedCartridge, $selectedColor);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt, $currentQtyDb);
		$currentStockRow = mysqli_stmt_fetch($stmt);

		if ($currentStockRow) {
			$currentQty = (int)$currentQtyDb;
			$newQty = $currentQty + $receivedAmount;

			$updateStmt = mysqli_prepare($link, "UPDATE `cartridge_stock_list` SET `stock_qty` = ?, `last_received_qty` = ?, `last_received_date` = ? WHERE `cartridge_no` = ? AND `color` = ?");
			mysqli_stmt_bind_param($updateStmt, 'iisss', $newQty, $receivedAmount, $rcvd_dt, $selectedCartridge, $selectedColor);

			if (mysqli_stmt_execute($updateStmt)) {
				$affectedRows = mysqli_stmt_affected_rows($updateStmt);
				if ($affectedRows > 0) {
					$flashMessage = 'Cartridge stock has been updated successfully.';
					$flashType = 'success';
					$receivedQty = '';
				} else {
					$flashMessage = 'No stock values changed. Check the selected cartridge and color or enter a different received quantity.';
					$flashType = 'warning';
				}
			} else {
				$flashMessage = 'Unknown error while updating cartridge stock.';
				$flashType = 'danger';
			}
		} else {
			$flashMessage = 'No matching cartridge and color combination was found.';
			$flashType = 'danger';
		}
	}
}

$stockSummary = mysqli_query($link, "SELECT COUNT(*) AS cartridge_count, COALESCE(SUM(`stock_qty`),0) AS total_qty, COALESCE(SUM(CASE WHEN `stock_qty` <= 2 THEN 1 ELSE 0 END),0) AS low_stock_count, COALESCE(SUM(CASE WHEN `stock_qty` = 0 THEN 1 ELSE 0 END),0) AS zero_stock_count FROM `cartridge_stock_list`");
$stockSummaryRow = mysqli_fetch_assoc($stockSummary) ?: [
	'cartridge_count' => 0,
	'total_qty' => 0,
	'low_stock_count' => 0,
	'zero_stock_count' => 0,
];

$cartridgeList = mysqli_query($link, "SELECT DISTINCT(`cartridge_no`) AS cartridge_no FROM `cartridge_stock_list` ORDER BY `cartridge_no`");
$colorOptions = ['BLACK', 'YELLOW', 'CYAN', 'MAGENTA'];
?>

<div class="content cartridge-stock-page">
	<div class="page-head cartridge-stock-hero">
		<div class="ic"><i class="fa-solid fa-boxes-stacked"></i></div>
		<div class="cartridge-stock-hero-copy">
			<div class="cartridge-stock-crumb">Cartridges / Update Stock</div>
			<h2>Receive Cartridge Stock</h2>
			<div class="sub">Add incoming stock to an existing cartridge and keep the last received details current.</div>
		</div>
		<div class="cartridge-stock-metrics">
			<div class="cartridge-metric">
				<span>Total cartridges</span>
				<strong><?= number_format((int)$stockSummaryRow['cartridge_count']) ?></strong>
			</div>
			<div class="cartridge-metric">
				<span>Total stock</span>
				<strong><?= number_format((int)$stockSummaryRow['total_qty']) ?></strong>
			</div>
			<div class="cartridge-metric">
				<span>Low stock</span>
				<strong><?= number_format((int)$stockSummaryRow['low_stock_count']) ?></strong>
			</div>
			<div class="cartridge-metric">
				<span>Zero stock</span>
				<strong><?= number_format((int)$stockSummaryRow['zero_stock_count']) ?></strong>
			</div>
		</div>
	</div>

	<?php if ($flashMessage !== ''): ?>
		<div class="alert alert-<?= esc($flashType) ?> cartridge-flash"><?= esc($flashMessage) ?></div>
	<?php endif; ?>

	<div class="cartridge-stock-layout">
		<div class="card cartridge-form-card">
			<div class="card-title"><i class="fa-solid fa-circle-plus"></i> Stock Entry</div>
			<p class="text-muted">Pick the cartridge, choose the color, and enter the new received quantity. The system adds it to the existing stock automatically.</p>

			<form name="add_prntr_cartg" method="post" class="cartridge-stock-form">
				<div class="form-grid">
					<div class="form-row">
						<label for="cartridge">Cartridge <span class="req">*</span></label>
						<select id="cartridge" name="cartridge" required>
							<option value="" disabled <?= $selectedCartridge === '' ? 'selected' : '' ?>>Select cartridge</option>
							<?php while ($master_cartridge_data_arr = mysqli_fetch_array($cartridgeList)): ?>
								<?php $cartridgeNo = $master_cartridge_data_arr['cartridge_no']; ?>
								<option value="<?= esc($cartridgeNo) ?>" <?= $selectedCartridge === $cartridgeNo ? 'selected' : '' ?>><?= esc($cartridgeNo) ?></option>
							<?php endwhile; ?>
						</select>
					</div>

					<div class="form-row">
						<label for="color">Color <span class="req">*</span></label>
						<select id="color" name="color" required>
							<?php foreach ($colorOptions as $colorOption): ?>
								<option value="<?= esc($colorOption) ?>" <?= $selectedColor === $colorOption ? 'selected' : '' ?>><?= esc($colorOption) ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="form-row">
						<label for="stck_recvd">Stock Received <span class="req">*</span></label>
						<input id="stck_recvd" name="stck_recvd" type="number" min="1" step="1" value="<?= esc($receivedQty) ?>" placeholder="Enter received quantity" required>
					</div>
				</div>

				<div class="cartridge-form-help">The last received date is set automatically to today in the Asia/Kolkata time zone.</div>

				<div class="flex-end cartridge-form-actions">
					<button type="submit" name="sub" id="butt" class="btn btn-navy cartridge-submit"><i class="fa-solid fa-sparkles"></i> Update Stock</button>
				</div>
			</form>
		</div>

		<div class="card cartridge-side-card">
			<div class="card-title"><i class="fa-solid fa-wand-magic-sparkles"></i> Quick Flow</div>
			<div class="cartridge-checklist">
				<div class="cartridge-tip">
					<strong>Step 1</strong>
					<span>Select the cartridge and color that has physically arrived.</span>
				</div>
				<div class="cartridge-tip">
					<strong>Step 2</strong>
					<span>Enter the received quantity only. The current stock is added in the background.</span>
				</div>
				<div class="cartridge-tip">
					<strong>Step 3</strong>
					<span>Save the entry and the last received date will be refreshed automatically.</span>
				</div>
			</div>

			<div class="cartridge-mini-card">
				<div class="cartridge-mini-title">Polished stock tracking</div>
				<div class="cartridge-mini-text">This view is tuned for quick desk-side updates, clear feedback, and a cleaner workflow on every cartridge receipt.</div>
			</div>
		</div>
	</div>
</div>
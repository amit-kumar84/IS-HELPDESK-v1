<?php
function esc($value)
{
	return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$flashMessage = '';
$flashType = 'info';
$selectedCartridge = trim($_POST['cartridge_search'] ?? '');
$selectedColor = trim($_POST['color_search'] ?? 'BLACK');
$cartridgeModelRow = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
	if ($selectedCartridge !== '') {
		$searchStmt = mysqli_prepare($link, "SELECT * FROM `cartridge_stock_list` WHERE `cartridge_no` = ? AND `color` = ? LIMIT 1");
		mysqli_stmt_bind_param($searchStmt, 'ss', $selectedCartridge, $selectedColor);
		mysqli_stmt_execute($searchStmt);
		$searchResult = mysqli_stmt_get_result($searchStmt);
		$cartridgeModelRow = mysqli_fetch_assoc($searchResult) ?: null;

		if (!$cartridgeModelRow) {
			$flashMessage = 'No record found for the selected cartridge and color combination.';
			$flashType = 'danger';
		}
	} else {
		$flashMessage = 'Choose a cartridge before searching.';
		$flashType = 'danger';
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subm'])) {
	$selectedCartridge = trim($_POST['cartridge'] ?? $selectedCartridge);
	$selectedColor = trim($_POST['color'] ?? $selectedColor);
	$sl_no = trim($_POST['sl_no'] ?? '');
	$ttl_page_prnt = trim($_POST['ttl_page_prnt'] ?? '');
	$rate = trim($_POST['rate'] ?? '');
	$part_number = trim($_POST['part_number'] ?? '');

	if ($selectedCartridge === '' || $selectedColor === '') {
		$flashMessage = 'Load a cartridge record before updating it.';
		$flashType = 'danger';
	} elseif ($sl_no === '' || $rate === '' || $part_number === '') {
		$flashMessage = 'Fill in the required fields before saving the update.';
		$flashType = 'danger';
	} else {
		$updateStmt = mysqli_prepare($link, "UPDATE `cartridge_stock_list` SET `cartridge_serial_no` = ?, `no_of_page` = ?, `cost_of_toner` = ?, `part_no` = ? WHERE `cartridge_no` = ? AND `color` = ?");
		mysqli_stmt_bind_param($updateStmt, 'ssssss', $sl_no, $ttl_page_prnt, $rate, $part_number, $selectedCartridge, $selectedColor);

		if (mysqli_stmt_execute($updateStmt)) {
			$flashMessage = 'Cartridge details updated successfully.';
			$flashType = 'success';
			$reloadStmt = mysqli_prepare($link, "SELECT * FROM `cartridge_stock_list` WHERE `cartridge_no` = ? AND `color` = ? LIMIT 1");
			mysqli_stmt_bind_param($reloadStmt, 'ss', $selectedCartridge, $selectedColor);
			mysqli_stmt_execute($reloadStmt);
			$reloadResult = mysqli_stmt_get_result($reloadStmt);
			$cartridgeModelRow = mysqli_fetch_assoc($reloadResult) ?: null;
		} else {
			$flashMessage = 'Unknown error while updating the cartridge record.';
			$flashType = 'danger';
		}
	}
}

if (!$cartridgeModelRow && $selectedCartridge !== '') {
	$seedStmt = mysqli_prepare($link, "SELECT * FROM `cartridge_stock_list` WHERE `cartridge_no` = ? AND `color` = ? LIMIT 1");
	mysqli_stmt_bind_param($seedStmt, 'ss', $selectedCartridge, $selectedColor);
	mysqli_stmt_execute($seedStmt);
	$seedResult = mysqli_stmt_get_result($seedStmt);
	$cartridgeModelRow = mysqli_fetch_assoc($seedResult) ?: null;
}

$cartridgeStats = mysqli_query($link, "SELECT COUNT(*) AS total_rows, COUNT(DISTINCT `cartridge_no`) AS unique_cartridges, COUNT(DISTINCT `color`) AS unique_colors, COALESCE(SUM(`stock_qty`),0) AS total_stock FROM `cartridge_stock_list`");
$cartridgeStatsRow = mysqli_fetch_assoc($cartridgeStats) ?: [
	'total_rows' => 0,
	'unique_cartridges' => 0,
	'unique_colors' => 0,
	'total_stock' => 0,
];

$masterCartridgeModels = mysqli_query($link, "SELECT DISTINCT `cartridge_no` FROM `cartridge_stock_list` ORDER BY `cartridge_no`");
$colorOptions = ['BLACK', 'YELLOW', 'CYAN', 'MAGENTA'];
?>

<div class="content cartridge-edit-page">
	<div class="page-head cartridge-edit-hero">
		<div class="ic"><i class="fa-solid fa-pen-to-square"></i></div>
		<div class="cartridge-edit-copy">
			<div class="cartridge-edit-crumb">Cartridges / Edit Stock</div>
			<h2>Edit Cartridge Details</h2>
			<div class="sub">Search a cartridge record, load it into a polished editor, and update the stock details with a cleaner workflow.</div>
		</div>
		<div class="cartridge-edit-metrics">
			<div class="cartridge-edit-metric">
				<span>Records</span>
				<strong><?= number_format((int)$cartridgeStatsRow['total_rows']) ?></strong>
			</div>
			<div class="cartridge-edit-metric">
				<span>Cartridge types</span>
				<strong><?= number_format((int)$cartridgeStatsRow['unique_cartridges']) ?></strong>
			</div>
			<div class="cartridge-edit-metric">
				<span>Total stock</span>
				<strong><?= number_format((int)$cartridgeStatsRow['total_stock']) ?></strong>
			</div>
			<div class="cartridge-edit-metric">
				<span>Color variants</span>
				<strong><?= number_format((int)$cartridgeStatsRow['unique_colors']) ?></strong>
			</div>
		</div>
	</div>

	<?php if ($flashMessage !== ''): ?>
		<div class="alert alert-<?= esc($flashType) ?> cartridge-edit-flash"><?= esc($flashMessage) ?></div>
	<?php endif; ?>

	<div class="cartridge-edit-layout">
		<div class="card cartridge-edit-search-card">
			<div class="card-title"><i class="fa-solid fa-magnifying-glass"></i> Load Cartridge Record</div>
			<p class="text-muted">Pick the cartridge and color to bring the matching record into the editor.</p>
			<form id="form1" name="form1" method="post" class="cartridge-edit-search-form">
				<div class="form-grid">
					<div class="form-row">
						<label for="cartridge_search">Cartridge <span class="req">*</span></label>
						<select id="cartridge_search" name="cartridge_search" required>
							<option value="" disabled <?= $selectedCartridge === '' ? 'selected' : '' ?>>Select cartridge</option>
							<?php while ($cartridgeModelDataArr = mysqli_fetch_array($masterCartridgeModels)): ?>
								<?php $cartridgeOption = $cartridgeModelDataArr['cartridge_no']; ?>
								<option value="<?= esc($cartridgeOption) ?>" <?= $selectedCartridge === $cartridgeOption ? 'selected' : '' ?>><?= esc($cartridgeOption) ?></option>
							<?php endwhile; ?>
						</select>
					</div>
					<div class="form-row">
						<label for="color_search">Color <span class="req">*</span></label>
						<select id="color_search" name="color_search" required>
							<?php foreach ($colorOptions as $colorOption): ?>
								<option value="<?= esc($colorOption) ?>" <?= $selectedColor === $colorOption ? 'selected' : '' ?>><?= esc($colorOption) ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="flex-end cartridge-edit-actions">
					<button type="submit" name="search" id="butt" class="btn btn-navy cartridge-edit-search-btn"><i class="fa-solid fa-magnifying-glass"></i> Load Record</button>
				</div>
			</form>
			<div class="cartridge-edit-search-note">Search first, then update the loaded cartridge in the edit card.</div>

			<div class="card cartridge-edit-loaded-card">
				<div class="card-title"><i class="fa-solid fa-pen"></i> Update Cartridge Details</div>
				<p class="text-muted">When the record is loaded below, update the fields and save the revised cartridge information.</p>

				<?php if ($cartridgeModelRow): ?>
					<form name="add_prntr_cartg" method="post" class="cartridge-edit-form">
						<input type="hidden" name="cartridge" value="<?= esc($cartridgeModelRow['cartridge_no']) ?>">
						<input type="hidden" name="color" value="<?= esc($cartridgeModelRow['color']) ?>">
						<div class="form-grid">
							<div class="form-row">
								<label for="cartridge_display">Cartridge <span class="req">*</span></label>
								<input id="cartridge_display" type="text" value="<?= esc($cartridgeModelRow['cartridge_no']) ?>" readonly>
							</div>
							<div class="form-row">
								<label for="color_display">Color <span class="req">*</span></label>
								<input id="color_display" type="text" value="<?= esc($cartridgeModelRow['color']) ?>" readonly>
							</div>
							<div class="form-row">
								<label for="sl_no">Serial No <span class="req">*</span></label>
								<input id="sl_no" name="sl_no" type="text" value="<?= esc($cartridgeModelRow['cartridge_serial_no']) ?>" placeholder="Serial number" required>
							</div>
							<div class="form-row">
								<label for="ttl_page_prnt">Total Page's Print</label>
								<input id="ttl_page_prnt" name="ttl_page_prnt" type="text" value="<?= esc($cartridgeModelRow['no_of_page']) ?>" placeholder="Expected page yield">
							</div>
							<div class="form-row">
								<label for="rate">Rate <span class="req">*</span></label>
								<input id="rate" name="rate" type="text" value="<?= esc($cartridgeModelRow['cost_of_toner']) ?>" placeholder="Cartridge rate" required>
							</div>
							<div class="form-row">
								<label for="part_number">Part Number <span class="req">*</span></label>
								<input id="part_number" name="part_number" type="text" maxlength="12" value="<?= esc($cartridgeModelRow['part_no']) ?>" placeholder="Part number" required>
							</div>
						</div>

						<div class="cartridge-edit-form-help">The update keeps the cartridge identity fixed while refreshing the operational details used elsewhere in the portal.</div>

						<div class="flex-end cartridge-edit-form-actions">
							<button type="submit" id="butt" name="subm" class="btn btn-navy cartridge-edit-submit"><i class="fa-solid fa-sparkles"></i> Update Cartridge</button>
						</div>
					</form>
				<?php else: ?>
					<div class="cartridge-edit-empty">
						<div class="cartridge-edit-empty-title">No cartridge loaded yet</div>
						<div class="cartridge-edit-empty-text">Use the search panel above to load an existing cartridge record into the editor.</div>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="card cartridge-edit-side-card">
			<div class="card-title"><i class="fa-solid fa-wand-magic-sparkles"></i> Edit Guidance</div>
			<div class="cartridge-edit-checklist">
				<div class="cartridge-edit-tip">
					<strong>Loaded identity</strong>
					<span>Cartridge and color stay fixed so the update targets the exact stock record.</span>
				</div>
				<div class="cartridge-edit-tip">
					<strong>Editable fields</strong>
					<span>Serial number, page yield, rate, and part number can be refreshed as needed.</span>
				</div>
				<div class="cartridge-edit-tip">
					<strong>Cleaner flow</strong>
					<span>The new layout keeps the edit process readable, polished, and responsive.</span>
				</div>
			</div>

			<div class="cartridge-edit-mini-card">
				<div class="cartridge-edit-mini-title">Polished edit workspace</div>
				<div class="cartridge-edit-mini-text">The updated surface mirrors the new cartridge add tab, so both flows feel consistent and modern.</div>
			</div>
		</div>
	</div>
</div>

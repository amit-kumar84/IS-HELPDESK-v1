<?php
function esc($value)
{
	return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$flashMessage = '';
$flashType = 'info';
$searchModel = trim($_POST['model_search'] ?? $_POST['original_model'] ?? '');
$selectedPrinter = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
	if ($searchModel !== '') {
		$searchStmt = mysqli_prepare($link, "SELECT * FROM `printer_cartridge_list` WHERE `model` = ? LIMIT 1");
		mysqli_stmt_bind_param($searchStmt, 's', $searchModel);
		mysqli_stmt_execute($searchStmt);
		$searchResult = mysqli_stmt_get_result($searchStmt);
		$selectedPrinter = mysqli_fetch_assoc($searchResult) ?: null;

		if (!$selectedPrinter) {
			$flashMessage = 'No printer-cartridge record was found for the selected model.';
			$flashType = 'danger';
		}
	} else {
		$flashMessage = 'Choose a printer model before searching.';
		$flashType = 'danger';
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subm'])) {
	$model = trim($_POST['model'] ?? '');
	$originalModel = trim($_POST['original_model'] ?? $model);
	$make = trim($_POST['make'] ?? '');
	$cartridgeNo = trim($_POST['cartridge_no'] ?? '');
	$ttlPagePrint = trim($_POST['ttl_page_prnt'] ?? '');
	$rate = trim($_POST['rate'] ?? '');
	$partNumber = trim($_POST['part_number'] ?? '');

	$searchModel = $originalModel;

	if ($model === '' || $make === '' || $cartridgeNo === '' || $rate === '' || $partNumber === '') {
		$flashMessage = 'Fill in all required fields before updating the printer-cartridge record.';
		$flashType = 'danger';
	} else {
		$updateStmt = mysqli_prepare($link, "UPDATE `printer_cartridge_list` SET `make` = ?, `cartridge_no` = ?, `ttl_page_prnt` = ?, `rate` = ?, `part_no` = ? WHERE `model` = ?");
		mysqli_stmt_bind_param($updateStmt, 'ssssss', $make, $cartridgeNo, $ttlPagePrint, $rate, $partNumber, $originalModel);

		if (mysqli_stmt_execute($updateStmt)) {
			$flashMessage = 'Printer-cartridge details updated successfully.';
			$flashType = 'success';
			$searchModel = $model;

			$refreshStmt = mysqli_prepare($link, "SELECT * FROM `printer_cartridge_list` WHERE `model` = ? LIMIT 1");
			mysqli_stmt_bind_param($refreshStmt, 's', $originalModel);
			mysqli_stmt_execute($refreshStmt);
			$refreshResult = mysqli_stmt_get_result($refreshStmt);
			$selectedPrinter = mysqli_fetch_assoc($refreshResult) ?: [
				'model' => $model,
				'make' => $make,
				'cartridge_no' => $cartridgeNo,
				'ttl_page_prnt' => $ttlPagePrint,
				'rate' => $rate,
				'part_no' => $partNumber,
			];
		} else {
			$flashMessage = 'Unknown error while updating the printer-cartridge record.';
			$flashType = 'danger';
		}
	}
}

if (!$selectedPrinter && $searchModel !== '') {
	$seedStmt = mysqli_prepare($link, "SELECT * FROM `printer_cartridge_list` WHERE `model` = ? LIMIT 1");
	mysqli_stmt_bind_param($seedStmt, 's', $searchModel);
	mysqli_stmt_execute($seedStmt);
	$seedResult = mysqli_stmt_get_result($seedStmt);
	$selectedPrinter = mysqli_fetch_assoc($seedResult) ?: null;
}

$printerStats = mysqli_query($link, "SELECT COUNT(*) AS total_rows, COUNT(DISTINCT `cartridge_no`) AS unique_cartridges, COUNT(DISTINCT `make`) AS unique_makes FROM `printer_cartridge_list`");
$printerStatsRow = mysqli_fetch_assoc($printerStats) ?: [
	'total_rows' => 0,
	'unique_cartridges' => 0,
	'unique_makes' => 0,
];

$masterPrinterModels = mysqli_query($link, "SELECT `model` FROM `printer_cartridge_list` ORDER BY `model`");
?>

<div class="content printer-edit-page">
	<div class="page-head printer-edit-hero">
		<div class="ic"><i class="fa-solid fa-pen-to-square"></i></div>
		<div class="printer-edit-copy">
			<div class="printer-edit-crumb">Cartridges / Edit Printer</div>
			<h2>Edit Printer-Cartridge</h2>
			<div class="sub">Search an existing printer model, inspect the current mapping, and update the cartridge reference with a cleaner workflow.</div>
		</div>
		<div class="printer-edit-metrics">
			<div class="printer-edit-metric">
				<span>Printer mappings</span>
				<strong><?= number_format((int)$printerStatsRow['total_rows']) ?></strong>
			</div>
			<div class="printer-edit-metric">
				<span>Cartridge types</span>
				<strong><?= number_format((int)$printerStatsRow['unique_cartridges']) ?></strong>
			</div>
			<div class="printer-edit-metric">
				<span>Brands / makes</span>
				<strong><?= number_format((int)$printerStatsRow['unique_makes']) ?></strong>
			</div>
		</div>
	</div>

	<?php if ($flashMessage !== ''): ?>
		<div class="alert alert-<?= esc($flashType) ?> printer-edit-flash"><?= esc($flashMessage) ?></div>
	<?php endif; ?>

	<div class="printer-edit-layout">
		<div class="card printer-edit-search-card">
			<div class="card-title"><i class="fa-solid fa-magnifying-glass"></i> Find Printer Model</div>
			<p class="text-muted">Pick a printer model to load its current configuration into the editor.</p>
			<form id="form1" name="form1" method="post" class="printer-edit-search-form">
				<div class="form-row">
					<label for="model_search">Printer Model</label>
					<select id="model_search" name="model_search" required>
						<option value="" disabled <?= $searchModel === '' ? 'selected' : '' ?>>Select printer model</option>
						<?php while ($printer_model_data_arr = mysqli_fetch_array($masterPrinterModels)): ?>
							<?php $printerModelName = $printer_model_data_arr['model']; ?>
							<option value="<?= esc($printerModelName) ?>" <?= $searchModel === $printerModelName ? 'selected' : '' ?>><?= esc($printerModelName) ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="flex-end printer-edit-actions">
					<button type="submit" name="search" id="butt" class="btn btn-navy printer-edit-search-btn"><i class="fa-solid fa-magnifying-glass"></i> Load Record</button>
				</div>
			</form>
			<div class="printer-edit-search-note">Search first, then update the loaded mapping in the edit card.</div>

			<div class="card printer-edit-loaded-card">
				<div class="card-title"><i class="fa-solid fa-pen"></i> Update Printer-Cartridge Details</div>
				<p class="text-muted">The current values are shown below. Edit the details and save to update the printer registry.</p>

				<?php if ($selectedPrinter): ?>
					<form name="print_cartdge_form" method="post" class="printer-edit-form">
						<input type="hidden" name="original_model" value="<?= esc($selectedPrinter['model']) ?>">
						<div class="form-grid">
							<div class="form-row">
								<label for="model">Model <span class="req">*</span></label>
								<input id="model" name="model" type="text" value="<?= esc($selectedPrinter['model']) ?>" readonly>
							</div>
							<div class="form-row">
								<label for="make">Make <span class="req">*</span></label>
								<input id="make" name="make" type="text" value="<?= esc($selectedPrinter['make']) ?>" placeholder="Printer make" required>
							</div>
							<div class="form-row">
								<label for="cartridge_no">Cartridge Number <span class="req">*</span></label>
								<input id="cartridge_no" name="cartridge_no" type="text" value="<?= esc($selectedPrinter['cartridge_no']) ?>" placeholder="Cartridge number" required>
							</div>
							<div class="form-row">
								<label for="ttl_page_prnt">Total Page's Print</label>
								<input id="ttl_page_prnt" name="ttl_page_prnt" type="text" value="<?= esc($selectedPrinter['ttl_page_prnt']) ?>" placeholder="Expected page yield">
							</div>
							<div class="form-row">
								<label for="rate">Rate <span class="req">*</span></label>
								<input id="rate" name="rate" type="text" value="<?= esc($selectedPrinter['rate']) ?>" placeholder="Printer rate" required>
							</div>
							<div class="form-row">
								<label for="part_number">Part Number <span class="req">*</span></label>
								<input id="part_number" name="part_number" type="text" maxlength="12" value="<?= esc($selectedPrinter['part_no']) ?>" placeholder="Part number" required>
							</div>
						</div>

						<div class="printer-edit-form-help">Saving updates the printer mapping used by cartridge request and stock tabs.</div>

						<div class="flex-end printer-edit-form-actions">
							<button type="submit" name="subm" id="butt" class="btn btn-navy printer-edit-submit"><i class="fa-solid fa-sparkles"></i> Update Printer-Cartridge</button>
						</div>
					</form>
				<?php else: ?>
					<div class="printer-edit-empty">
						<div class="printer-edit-empty-title">No printer loaded yet</div>
						<div class="printer-edit-empty-text">Use the search panel above to load an existing printer-cartridge record into the editor.</div>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="card printer-edit-side-card">
			<div class="card-title"><i class="fa-solid fa-wand-magic-sparkles"></i> Edit Guidance</div>
			<div class="printer-edit-checklist">
				<div class="printer-edit-tip">
					<strong>Readonly identity</strong>
					<span>The model remains the record key, so your edits stay tied to the original printer entry.</span>
				</div>
				<div class="printer-edit-tip">
					<strong>Updated mapping</strong>
					<span>Change the cartridge number, page yield, rate, or part number as required.</span>
				</div>
				<div class="printer-edit-tip">
					<strong>Cleaner tracking</strong>
					<span>This layout keeps the edit flow readable on desktop and still responsive on smaller screens.</span>
				</div>
			</div>

			<div class="printer-edit-mini-card">
				<div class="printer-edit-mini-title">Polished edit workspace</div>
				<div class="printer-edit-mini-text">A simpler surface for maintaining printer mappings without the clutter of the old table form.</div>
			</div>
	</div>
</div>
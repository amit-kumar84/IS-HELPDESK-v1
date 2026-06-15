<?php
date_default_timezone_set('Asia/Kolkata');

$uid = $sid ?? (function_exists('current_user_id') ? current_user_id() : ($_SESSION['sid'] ?? ''));

function cartridge_status_class($status) {
	$s = strtolower(trim((string)$status));
	if ($s === 'pending') return 'pending';
	if ($s === 'issued') return 'attend';
	if ($s === 'received') return 'solved';
	if ($s === 'completed') return 'closed';
	return 'danger';
}

function cartridge_status_icon($status) {
	$s = strtolower(trim((string)$status));
	if ($s === 'pending') return 'fa-hourglass-half';
	if ($s === 'issued') return 'fa-cube';
	if ($s === 'received') return 'fa-hand-holding';
	if ($s === 'completed') return 'fa-circle-check';
	return 'fa-triangle-exclamation';
}

$allOrders = array();
$pendingCount = 0;
$completedCount = 0;

if (!empty($uid)) {
	$qAll = mysqli_prepare($link, "SELECT * FROM request_master WHERE staff_no = ? ORDER BY substring(request_no,2,6) DESC, substring(request_no,7,12) DESC");
	mysqli_stmt_bind_param($qAll, 's', $uid);
	mysqli_stmt_execute($qAll);
	$rAll = mysqli_stmt_get_result($qAll);
	while ($row = mysqli_fetch_assoc($rAll)) {
		$allOrders[] = $row;
		$s = strtolower(trim($row['Status'] ?? ''));
		if ($s === 'pending') $pendingCount++;
		if ($s === 'completed' || $s === 'received' || $s === 'issued') $completedCount++;
	}
}
?>

<section class="my-cartridges-page">
	<div class="my-cartridges-hero">
		<div class="my-cartridges-copy">
			<div class="my-cartridges-kicker"><i class="fa-solid fa-droplet"></i> Activity / Cartridges</div>
			<h2>My Cartridge Orders</h2>
			<p>Track your cartridge requests, monitor delivery status, and manage printer supplies.</p>
			<div class="my-cartridges-points">
				<div class="my-cartridges-point"><i class="fa-solid fa-truck"></i><span>Real-time tracking</span></div>
				<div class="my-cartridges-point"><i class="fa-solid fa-cubes"></i><span>Stock availability</span></div>
				<div class="my-cartridges-point"><i class="fa-solid fa-clock"></i><span>Delivery status</span></div>
			</div>
		</div>
		<div class="my-cartridges-summary-card">
			<div class="my-cartridges-stat">
				<span>Total Orders</span>
				<strong><?php echo count($allOrders); ?></strong>
			</div>
			<div class="my-cartridges-stat">
				<span>Pending</span>
				<strong><?php echo $pendingCount; ?></strong>
			</div>
			<div class="my-cartridges-stat">
				<span>Completed / Received</span>
				<strong><?php echo $completedCount; ?></strong>
			</div>
			<div class="my-cartridges-note"><i class="fa-solid fa-circle-info"></i> Contact support if an order exceeds 5 business days.</div>
		</div>
	</div>

	<section class="my-cartridges-card">
		<div class="my-cartridges-card-head">
			<div>
				<div class="section-tag"><i class="fa-solid fa-list-ul"></i> All Orders</div>
				<h3>Cartridge Requests</h3>
				<p>Complete history of your cartridge supply orders.</p>
			</div>
			<div class="my-cartridges-badge">Visible: <span id="cartridgeVisibleCount"><?php echo count($allOrders); ?></span> / <?php echo count($allOrders); ?></div>
		</div>

		<div class="my-cartridges-legend">
			<span class="legend-item"><i class="fa-solid fa-hourglass-half"></i> Pending</span>
			<span class="legend-item"><i class="fa-solid fa-cube"></i> Issued</span>
			<span class="legend-item"><i class="fa-solid fa-hand-holding"></i> Received</span>
			<span class="legend-item"><i class="fa-solid fa-circle-check"></i> Completed</span>
		</div>

		<div class="my-cartridges-toolbar">
			<div class="toolbar-field search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" id="cartridgeSearch" placeholder="Search request, printer, cartridge..."></div>
			<div class="toolbar-field filter"><i class="fa-solid fa-filter"></i>
				<select id="cartridgeStatusFilter">
					<option value="">All Status</option>
					<option value="pending">Pending</option>
					<option value="issued">Issued</option>
					<option value="received">Received</option>
					<option value="completed">Completed</option>
				</select>
			</div>
		</div>

		<div class="table-wrap my-cartridges-table-wrap">
			<table id="allOrdersTable">
				<thead>
					<tr>
						<th>Request Number</th>
						<th>Request Date</th>
						<th>PC Number</th>
						<th>Printer</th>
						<th>Color</th>
						<th>Cartridge</th>
						<th>Issue Date</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php if (count($allOrders) === 0): ?>
						<tr><td colspan="8" class="my-cartridges-empty">No cartridge orders found.</td></tr>
					<?php else: ?>
						<?php foreach ($allOrders as $order): ?>
							<tr data-status="<?php echo strtolower(trim($order['Status'])); ?>">
								<td><?php echo htmlspecialchars($order['request_no']); ?></td>
								<td><?php echo htmlspecialchars($order['request_date']); ?></td>
								<td><?php echo htmlspecialchars($order['pc_no'] ?: '-'); ?></td>
								<td class="ta-left"><?php echo htmlspecialchars($order['printer_name'] ?: '-'); ?></td>
								<td><?php echo htmlspecialchars($order['color'] ?: '-'); ?></td>
								<td><?php echo htmlspecialchars($order['cartridge_no'] ?: '-'); ?></td>
								<td><?php echo htmlspecialchars($order['issue_date'] ?: '-'); ?></td>
								<td><span class="badge <?php echo cartridge_status_class($order['Status']); ?>"><i class="fa-solid <?php echo cartridge_status_icon($order['Status']); ?>"></i> <?php echo htmlspecialchars($order['Status']); ?></span></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</section>
</section>

<script>
	(function(){
		var table = document.getElementById('allOrdersTable');
		var search = document.getElementById('cartridgeSearch');
		var status = document.getElementById('cartridgeStatusFilter');
		var visibleCount = document.getElementById('cartridgeVisibleCount');
		if (!table || !search || !status || !visibleCount) return;

		var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
		function applyFilters(){
			var q = (search.value || '').toLowerCase().trim();
			var st = (status.value || '').toLowerCase().trim();
			var count = 0;

			rows.forEach(function(row){
				if (row.querySelector('.my-cartridges-empty')) return;
				var txt = (row.textContent || '').toLowerCase();
				var rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
				var okText = q === '' || txt.indexOf(q) !== -1;
				var okStatus = st === '' || rowStatus === st;
				var show = okText && okStatus;
				row.style.display = show ? '' : 'none';
				if (show) count++;
			});
			visibleCount.textContent = count;
		}

		search.addEventListener('input', applyFilters);
		status.addEventListener('change', applyFilters);
	})();
</script>
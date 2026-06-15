
<?php
date_default_timezone_set('Asia/Kolkata');

$uid = $sid ?? (function_exists('current_user_id') ? current_user_id() : ($_SESSION['sid'] ?? ''));
$noticeType = '';
$noticeMessage = '';

function status_class($status) {
	$s = strtolower(trim((string)$status));
	if ($s === 'pending') return 'pending';
	if ($s === 'attend') return 'attend';
	if ($s === 'solved') return 'solved';
	if ($s === 'closed') return 'closed';
	return 'danger';
}

function status_icon($status) {
	$s = strtolower(trim((string)$status));
	if ($s === 'pending') return 'fa-hourglass-half';
	if ($s === 'attend') return 'fa-user-gear';
	if ($s === 'solved') return 'fa-circle-check';
	if ($s === 'closed') return 'fa-lock';
	return 'fa-triangle-exclamation';
}

if (isset($_POST['reopen_ticket']) && !empty($uid)) {
	$ticketToReopen = trim($_POST['reopen_ticket']);
	$st = mysqli_prepare($link, "SELECT status FROM complain_register WHERE t_no = ? AND Staff_no = ? LIMIT 1");
	mysqli_stmt_bind_param($st, 'ss', $ticketToReopen, $uid);
	mysqli_stmt_execute($st);
	$ticketRow = mysqli_fetch_assoc(mysqli_stmt_get_result($st)) ?: array();

	if (!$ticketRow) {
		$noticeType = 'error';
		$noticeMessage = 'Ticket not found for your account.';
	} else if (strtolower(trim($ticketRow['status'])) === 'pending') {
		$noticeType = 'info';
		$noticeMessage = 'This ticket is already pending.';
	} else {
		$up = mysqli_prepare($link, "UPDATE complain_register SET status='Pending' WHERE t_no = ? AND Staff_no = ?");
		mysqli_stmt_bind_param($up, 'ss', $ticketToReopen, $uid);
		if (mysqli_stmt_execute($up)) {
			$noticeType = 'success';
			$noticeMessage = 'Your complaint has been reopened successfully.';
		} else {
			$noticeType = 'error';
			$noticeMessage = 'Unable to reopen the ticket right now.';
		}
	}
}

$yr = substr(date('Y'), 2);
$mnth = date('m');
$dte = date('d');
$dateLike = $yr . $mnth . $dte;

$todayCalls = array();
$allCalls = array();

if (!empty($uid)) {
	$qToday = mysqli_prepare($link, "SELECT * FROM complain_register WHERE Staff_no = ? AND t_no LIKE ? ORDER BY substring(t_no,1,5) DESC, substring(t_no,8,12) DESC");
	$todayLike = '%' . $dateLike . '%';
	mysqli_stmt_bind_param($qToday, 'ss', $uid, $todayLike);
	mysqli_stmt_execute($qToday);
	$rToday = mysqli_stmt_get_result($qToday);
	while ($row = mysqli_fetch_assoc($rToday)) {
		$todayCalls[] = $row;
	}

	$qAll = mysqli_prepare($link, "SELECT * FROM complain_register WHERE Staff_no = ? ORDER BY substring(t_no,1,5) DESC, substring(t_no,8,12) DESC");
	mysqli_stmt_bind_param($qAll, 's', $uid);
	mysqli_stmt_execute($qAll);
	$rAll = mysqli_stmt_get_result($qAll);
	while ($row = mysqli_fetch_assoc($rAll)) {
		$allCalls[] = $row;
	}
}

$pendingCount = 0;
$resolvedCount = 0;
foreach ($allCalls as $call) {
	$s = strtolower(trim($call['status'] ?? ''));
	if ($s === 'pending') $pendingCount++;
	if ($s === 'solved' || $s === 'closed') $resolvedCount++;
}
?>

<section class="my-complaints-page">
	<div class="my-complaints-hero">
		<div class="my-complaints-copy">
			<div class="my-complaints-kicker"><i class="fa-solid fa-clipboard-list"></i> Activity / Complaints</div>
			<h2>My Complaints</h2>
			<p>Track your tickets, see current status, and reopen resolved calls when the issue repeats.</p>
			<div class="my-complaints-points">
				<div class="my-complaints-point"><i class="fa-solid fa-bolt"></i><span>Live status view</span></div>
				<div class="my-complaints-point"><i class="fa-solid fa-rotate-left"></i><span>One-click reopen</span></div>
				<div class="my-complaints-point"><i class="fa-solid fa-user-gear"></i><span>Engineer visibility</span></div>
			</div>
		</div>
		<div class="my-complaints-summary-card">
			<div class="my-complaints-stat">
				<span>Total Tickets</span>
				<strong><?php echo count($allCalls); ?></strong>
			</div>
			<div class="my-complaints-stat">
				<span>Pending</span>
				<strong><?php echo $pendingCount; ?></strong>
			</div>
			<div class="my-complaints-stat">
				<span>Resolved / Closed</span>
				<strong><?php echo $resolvedCount; ?></strong>
			</div>
			<div class="my-complaints-note"><i class="fa-solid fa-circle-info"></i> Keep ticket number ready while speaking with support.</div>
		</div>
	</div>

	<section class="my-complaints-card">
		<div class="my-complaints-card-head">
			<div>
				<div class="section-tag"><i class="fa-solid fa-calendar-day"></i> Today</div>
				<h3>Today's Calls</h3>
				<p>Complaints generated for today. Reopen is available for non-pending tickets only.</p>
			</div>
			<div class="my-complaints-badge"><?php echo date('d-m-Y'); ?></div>
		</div>
		<div class="my-complaints-legend">
			<span class="legend-item"><i class="fa-solid fa-hourglass-half"></i> Pending</span>
			<span class="legend-item"><i class="fa-solid fa-user-gear"></i> Attend</span>
			<span class="legend-item"><i class="fa-solid fa-circle-check"></i> Solved</span>
			<span class="legend-item"><i class="fa-solid fa-lock"></i> Closed</span>
		</div>

		<div class="table-wrap my-complaints-table-wrap">
			<table>
				<thead>
					<tr>
						<th>Ticket Number</th>
						<th>Call DateTime</th>
						<th>Problem</th>
						<th>Engineer</th>
						<th>Solution</th>
						<th>Solution DateTime</th>
						<th>Status</th>
						<th>ReOpen</th>
					</tr>
				</thead>
				<tbody>
					<?php if (count($todayCalls) === 0): ?>
						<tr><td colspan="8" class="my-complaints-empty">No complaints logged today.</td></tr>
					<?php else: ?>
						<?php foreach ($todayCalls as $call): ?>
							<tr>
								<td><?php echo htmlspecialchars($call['t_no']); ?></td>
								<td><?php echo htmlspecialchars($call['r_DateTime']); ?></td>
								<td class="ta-left"><?php echo htmlspecialchars($call['problem']); ?></td>
								<td><?php echo htmlspecialchars($call['support_engg'] ?: '-'); ?></td>
								<td class="ta-left"><?php echo htmlspecialchars($call['solution'] ?: '-'); ?></td>
								<td><?php echo htmlspecialchars($call['s_DateTime'] ?: '-'); ?></td>
								<td><span class="badge <?php echo status_class($call['status']); ?>"><i class="fa-solid <?php echo status_icon($call['status']); ?>"></i> <?php echo htmlspecialchars($call['status']); ?></span></td>
								<td>
									<?php if (strtolower(trim($call['status'])) !== 'pending'): ?>
										<form method="POST" class="reopen-form">
											<input type="hidden" name="reopen_ticket" value="<?php echo htmlspecialchars($call['t_no']); ?>">
											<button type="submit" class="reopen-btn"><i class="fa-solid fa-rotate-left"></i> Reopen</button>
										</form>
									<?php else: ?>
										<span class="reopen-disabled">Pending</span>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</section>

	<section class="my-complaints-card">
		<div class="my-complaints-card-head">
			<div>
				<div class="section-tag"><i class="fa-solid fa-database"></i> History</div>
				<h3>All Calls</h3>
				<p>Complete complaint history for your staff account.</p>
			</div>
			<div class="my-complaints-badge">Visible: <span id="historyVisibleCount"><?php echo count($allCalls); ?></span> / <?php echo count($allCalls); ?></div>
		</div>

		<div class="my-complaints-toolbar">
			<div class="toolbar-field search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" id="complaintSearch" placeholder="Search ticket, problem, asset, engineer..."></div>
			<div class="toolbar-field filter"><i class="fa-solid fa-filter"></i>
				<select id="complaintStatusFilter">
					<option value="">All Status</option>
					<option value="pending">Pending</option>
					<option value="attend">Attend</option>
					<option value="solved">Solved</option>
					<option value="closed">Closed</option>
				</select>
			</div>
		</div>

		<div class="table-wrap my-complaints-table-wrap">
			<table id="allCallsTable">
				<thead>
					<tr>
						<th>Ticket Number</th>
						<th>Call DateTime</th>
						<th>Problem</th>
						<th>Asset ID</th>
						<th>Engineer</th>
						<th>Solution</th>
						<th>Solution DateTime</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php if (count($allCalls) === 0): ?>
						<tr><td colspan="8" class="my-complaints-empty">No complaint history found.</td></tr>
					<?php else: ?>
						<?php foreach ($allCalls as $call): ?>
							<tr data-status="<?php echo strtolower(trim($call['status'])); ?>">
								<td><?php echo htmlspecialchars($call['t_no']); ?></td>
								<td><?php echo htmlspecialchars($call['r_DateTime']); ?></td>
								<td class="ta-left"><?php echo htmlspecialchars($call['problem']); ?></td>
								<td><?php echo htmlspecialchars($call['pc_no'] ?: '-'); ?></td>
								<td><?php echo htmlspecialchars($call['support_engg'] ?: '-'); ?></td>
								<td class="ta-left"><?php echo htmlspecialchars($call['solution'] ?: '-'); ?></td>
								<td><?php echo htmlspecialchars($call['s_DateTime'] ?: '-'); ?></td>
								<td><span class="badge <?php echo status_class($call['status']); ?>"><i class="fa-solid <?php echo status_icon($call['status']); ?>"></i> <?php echo htmlspecialchars($call['status']); ?></span></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</section>
</section>

<?php if ($noticeMessage !== ''): ?>
<div class="submit-success-screen compact-modal" id="complaintNoticeModal">
	<div class="submit-success-card">
		<div class="success-badge"><i class="fa-solid <?php echo $noticeType === 'success' ? 'fa-check' : ($noticeType === 'info' ? 'fa-circle-info' : 'fa-triangle-exclamation'); ?>"></i></div>
		<div class="success-eyebrow"><?php echo $noticeType === 'success' ? 'Ticket Updated' : ($noticeType === 'info' ? 'No Changes' : 'Action Failed'); ?></div>
		<h2><?php echo $noticeType === 'success' ? 'Complaint reopened' : ($noticeType === 'info' ? 'Already pending' : 'Could not reopen'); ?></h2>
		<p><?php echo htmlspecialchars($noticeMessage); ?></p>
		<div class="success-actions">
			<button type="button" class="success-btn" id="complaintNoticeOk">OK</button>
		</div>
	</div>
</div>
<script>
	(function(){
		var modal = document.getElementById('complaintNoticeModal');
		var okBtn = document.getElementById('complaintNoticeOk');
		if (!modal || !okBtn) return;
		okBtn.addEventListener('click', function(){ modal.style.display = 'none'; });
	})();
</script>
<?php endif; ?>

<script>
	(function(){
		var table = document.getElementById('allCallsTable');
		var search = document.getElementById('complaintSearch');
		var status = document.getElementById('complaintStatusFilter');
		var visibleCount = document.getElementById('historyVisibleCount');
		if (!table || !search || !status || !visibleCount) return;

		var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
		function applyFilters(){
			var q = (search.value || '').toLowerCase().trim();
			var st = (status.value || '').toLowerCase().trim();
			var count = 0;

			rows.forEach(function(row){
				if (row.querySelector('.my-complaints-empty')) return;
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
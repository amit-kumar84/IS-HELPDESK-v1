<?php
$query_sel_4_count = mysqli_query($link, "SELECT * FROM `complain_register`");
$totalCalls = mysqli_num_rows($query_sel_4_count);

$query_sel_closed_Calls = mysqli_query($link, "SELECT * FROM `complain_register` WHERE `status`='Closed'");
$closedCalls = mysqli_num_rows($query_sel_closed_Calls);

$query_sel_pending_Calls = mysqli_query($link, "SELECT * FROM `complain_register` WHERE `status`='Pending'");
$pendingCalls = mysqli_num_rows($query_sel_pending_Calls);

$query_sel_attend_Calls = mysqli_query($link, "SELECT * FROM `complain_register` WHERE `status`='Attend'");
$attendCalls = mysqli_num_rows($query_sel_attend_Calls);

$query_sel_solved_Calls = mysqli_query($link, "SELECT * FROM `complain_register` WHERE `status`='Solved'");
$solvedCalls = mysqli_num_rows($query_sel_solved_Calls);

date_default_timezone_set('Asia/Kolkata');
$dateLike = substr(date('Y'), 2) . date('m') . date('d');

$todayTotal = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `complain_register` WHERE `t_no` LIKE '%" . $dateLike . "%'"));
$todayClosed = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `complain_register` WHERE `status`='Closed' AND `t_no` LIKE '%" . $dateLike . "%'"));
$todayPending = $pendingCalls;
$todayAttend = $attendCalls;
$todaySolved = $solvedCalls;

$openCalls = $pendingCalls + $attendCalls + $solvedCalls;
$todayOpen = $todayPending + $todayAttend + $todaySolved;
$todayBarMax = max($todayOpen, $todayPending, $todayAttend, $todaySolved, $todayClosed, 1);
?>

<style>
.report-wrap {
	position: relative;
	overflow: hidden;
	padding: 22px;
	border-radius: 22px;
	background:
		radial-gradient(circle at top left, rgba(245,158,11,.18), transparent 30%),
		radial-gradient(circle at top right, rgba(34,197,94,.18), transparent 28%),
		linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
	border: 1px solid rgba(148,163,184,.18);
	box-shadow: 0 18px 40px -28px rgba(15,23,42,.38);
}
.report-wrap::before,
.report-wrap::after {
	content: '';
	position: absolute;
	border-radius: 50%;
	pointer-events: none;
	animation: reportFloat 9s ease-in-out infinite;
}
.report-wrap::before {
	width: 240px; height: 240px; top: -90px; right: -70px;
	background: radial-gradient(circle, rgba(59,130,246,.18) 0%, rgba(59,130,246,0) 70%);
}
.report-wrap::after {
	width: 180px; height: 180px; left: -70px; bottom: -70px;
	background: radial-gradient(circle, rgba(16,185,129,.16) 0%, rgba(16,185,129,0) 72%);
	animation-delay: -3.5s;
}
@keyframes reportFloat {
	0%, 100% { transform: translate3d(0,0,0) scale(1); opacity: .78; }
	50% { transform: translate3d(10px,-12px,0) scale(1.08); opacity: 1; }
}
.report-head {
	position: relative;
	z-index: 1;
	display: flex;
	justify-content: space-between;
	gap: 16px;
	flex-wrap: wrap;
	align-items: flex-start;
	margin-bottom: 18px;
}
.report-head .title-pill {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	padding: 7px 12px;
	border-radius: 999px;
	background: linear-gradient(135deg, rgba(59,130,246,.12), rgba(245,158,11,.12));
	border: 1px solid rgba(59,130,246,.16);
	color: #1e3a8a;
	font-size: 11.5px;
	font-weight: 800;
	letter-spacing: .5px;
	text-transform: uppercase;
}
.report-head h2 {
	margin: 10px 0 6px;
	font-size: 28px;
	color: #0a1f44;
	letter-spacing: -.4px;
}
.report-head p {
	margin: 0;
	color: #475569;
	font-size: 13px;
	line-height: 1.6;
	max-width: 760px;
}
.report-meta {
	min-width: 210px;
	padding: 16px 18px;
	border-radius: 18px;
	background: linear-gradient(135deg, #0a1f44, #1e3a8a 50%, #2563eb);
	color: #fff;
	box-shadow: 0 18px 35px -20px rgba(37,99,235,.58);
}
.report-meta span {
	display: block;
	font-size: 11px;
	text-transform: uppercase;
	letter-spacing: .5px;
	opacity: .82;
}
.report-meta strong {
	display: block;
	margin: 6px 0 4px;
	font-size: 32px;
	line-height: 1;
}
.report-meta small {
	display: block;
	font-size: 12px;
	opacity: .88;
}
.report-grid {
	position: relative;
	z-index: 1;
	display: grid;
	grid-template-columns: repeat(4, minmax(0, 1fr));
	gap: 12px;
	margin-bottom: 16px;
}
.report-card {
	position: relative;
	overflow: hidden;
	padding: 16px;
	border-radius: 18px;
	color: #0a1f44;
	background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,250,252,.96));
	border: 1px solid rgba(148,163,184,.18);
	box-shadow: 0 14px 30px -24px rgba(15,23,42,.38);
}
.report-card::before {
	content: '';
	position: absolute;
	inset: 0;
	background: linear-gradient(135deg, rgba(255,255,255,.45), transparent 40%, rgba(255,255,255,.15));
	pointer-events: none;
}
.report-card .label {
	position: relative;
	z-index: 1;
	display: flex;
	align-items: center;
	gap: 8px;
	font-size: 11px;
	font-weight: 800;
	letter-spacing: .5px;
	text-transform: uppercase;
	color: #64748b;
}
.report-card .value {
	position: relative;
	z-index: 1;
	display: block;
	margin-top: 10px;
	font-size: 28px;
	font-weight: 900;
	letter-spacing: -.4px;
	color: #0a1f44;
}
.report-card .note {
	position: relative;
	z-index: 1;
	margin-top: 6px;
	font-size: 12px;
	color: #64748b;
	line-height: 1.4;
}
.report-card .spark {
	position: absolute;
	right: 14px;
	top: 14px;
	width: 38px;
	height: 38px;
	border-radius: 12px;
	display: flex;
	align-items: center;
	justify-content: center;
	color: #fff;
	font-size: 14px;
	box-shadow: inset 0 1px 0 rgba(255,255,255,.18);
}
.report-card.total .spark { background: linear-gradient(135deg, #1e3a8a, #2563eb); }
.report-card.closed .spark { background: linear-gradient(135deg, #64748b, #0f172a); }
.report-card.pending .spark { background: linear-gradient(135deg, #f59e0b, #fb7185); }
.report-card.attend .spark { background: linear-gradient(135deg, #7c3aed, #06b6d4); }
.report-card.solved .spark { background: linear-gradient(135deg, #10b981, #22c55e); }
.report-card.today-total { background: linear-gradient(180deg, rgba(59,130,246,.08), rgba(255,255,255,.98)); }
.report-card.today-open { background: linear-gradient(180deg, rgba(245,158,11,.08), rgba(255,255,255,.98)); }
.report-card.today-closed { background: linear-gradient(180deg, rgba(16,185,129,.08), rgba(255,255,255,.98)); }
.report-card.today-solved { background: linear-gradient(180deg, rgba(124,58,237,.08), rgba(255,255,255,.98)); }
.report-bars {
	position: relative;
	z-index: 1;
	display: grid;
	gap: 14px;
	margin-top: 8px;
}
.report-bar {
	display: grid;
	grid-template-columns: 120px 1fr 60px;
	gap: 10px;
	align-items: center;
	font-size: 12.5px;
	color: #334155;
}
.report-bar .track {
	height: 12px;
	border-radius: 999px;
	background: #e2e8f0;
	overflow: hidden;
}
.report-bar .fill {
	height: 100%;
	border-radius: inherit;
	background: linear-gradient(90deg, #1e3a8a, #2563eb, #0ea5e9);
	box-shadow: 0 6px 18px -8px rgba(37,99,235,.55);
	width: 0;
	animation: fillBar 1.2s ease-out forwards;
}
.report-bar.pending .fill { background: linear-gradient(90deg, #f97316, #f59e0b, #fb7185); }
.report-bar.attend .fill { background: linear-gradient(90deg, #7c3aed, #06b6d4); }
.report-bar.solved .fill { background: linear-gradient(90deg, #10b981, #22c55e); }
.report-bar.closed .fill { background: linear-gradient(90deg, #475569, #0f172a); }
.report-bar span:last-child {
	text-align: right;
	font-weight: 800;
	color: #0a1f44;
	font-variant-numeric: tabular-nums;
}
@keyframes fillBar {
	from { width: 0; }
}
@media (max-width: 1100px) {
	.report-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
	.report-wrap { padding: 16px; }
	.report-head h2 { font-size: 22px; }
	.report-grid { grid-template-columns: 1fr; }
	.report-bar { grid-template-columns: 1fr; gap: 6px; }
	.report-bar span:last-child { text-align: left; }
}
</style>

<div class="report-wrap">
	<div class="report-head">
		<div>
			<div class="title-pill"><i class="fa-solid fa-chart-pie"></i> Overall Status</div>
			<h2>Overall Call Report</h2>
			<p>Use this dashboard to read the ticket mix at a glance. The cards and progress bars make the distribution more readable, colorful, and easier to scan than the old text block.</p>
		</div>
		<div class="report-meta">
			<span>Total calls</span>
			<strong><?= number_format($totalCalls) ?></strong>
			<small><?= number_format($openCalls) ?> open, <?= number_format($closedCalls) ?> closed</small>
		</div>
	</div>

	<div class="report-grid">
		<div class="report-card total">
			<div class="spark"><i class="fa-solid fa-ticket"></i></div>
			<div class="label"><i class="fa-solid fa-layer-group"></i> Total calls</div>
			<span class="value"><?= number_format($totalCalls) ?></span>
			<div class="note">All tickets currently in the system.</div>
		</div>
		<div class="report-card closed">
			<div class="spark"><i class="fa-solid fa-lock"></i></div>
			<div class="label"><i class="fa-solid fa-circle-check"></i> Closed</div>
			<span class="value"><?= number_format($closedCalls) ?></span>
			<div class="note">Completed and archived tickets.</div>
		</div>
		<div class="report-card pending">
			<div class="spark"><i class="fa-solid fa-hourglass-half"></i></div>
			<div class="label"><i class="fa-solid fa-triangle-exclamation"></i> Pending</div>
			<span class="value"><?= number_format($pendingCalls) ?></span>
			<div class="note">Waiting for assignment or action.</div>
		</div>
		<div class="report-card attend">
			<div class="spark"><i class="fa-solid fa-spinner"></i></div>
			<div class="label"><i class="fa-solid fa-gears"></i> In progress</div>
			<span class="value"><?= number_format($attendCalls) ?></span>
			<div class="note">Tickets actively being worked on.</div>
		</div>
	</div>

	<div class="report-grid" style="grid-template-columns: 1.1fr .9fr">
		<div class="report-card solved" style="grid-column:auto">
			<div class="spark"><i class="fa-solid fa-circle-check"></i></div>
			<div class="label"><i class="fa-solid fa-star"></i> Solved</div>
			<span class="value"><?= number_format($solvedCalls) ?></span>
			<div class="note">Solved tickets ready for closure.</div>
		</div>
		<div class="report-card today-total">
			<div class="label"><i class="fa-solid fa-calendar-day"></i> Today</div>
			<div class="report-bars">
				<div class="report-bar total"><span>Total open</span><div class="track"><div class="fill" style="width:<?= $todayOpen > 0 ? 100 : 0 ?>%"></div></div><span><?= number_format($todayOpen) ?></span></div>
				<div class="report-bar pending"><span>Pending</span><div class="track"><div class="fill" style="width:<?= $todayPending > 0 ? max(6, round(($todayPending / $todayBarMax) * 100)) : 0 ?>%"></div></div><span><?= number_format($todayPending) ?></span></div>
				<div class="report-bar attend"><span>In progress</span><div class="track"><div class="fill" style="width:<?= $todayAttend > 0 ? max(6, round(($todayAttend / $todayBarMax) * 100)) : 0 ?>%"></div></div><span><?= number_format($todayAttend) ?></span></div>
				<div class="report-bar solved"><span>Solved</span><div class="track"><div class="fill" style="width:<?= $todaySolved > 0 ? max(6, round(($todaySolved / $todayBarMax) * 100)) : 0 ?>%"></div></div><span><?= number_format($todaySolved) ?></span></div>
				<div class="report-bar closed"><span>Today closed</span><div class="track"><div class="fill" style="width:<?= $todayTotal > 0 ? max(6, round(($todayClosed / $todayTotal) * 100)) : 0 ?>%"></div></div><span><?= number_format($todayClosed) ?></span></div>
			</div>
		</div>
	</div>
</div>
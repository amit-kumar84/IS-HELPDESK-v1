<?php
	$ticket_no = $_SESSION['token'] ?? '';
?>

<section class="submit-success-screen">
	<div class="submit-success-card">
		<div class="success-badge">
			<i class="fa-solid fa-check"></i>
		</div>
		<div class="success-eyebrow">Ticket Submitted</div>
		<h2>Successfully recorded</h2>
		<p>Your request is now in the support queue. Save the reference ID below for follow-up or future enquiry.</p>
		<div class="success-ticket-wrap">
			<span>Reference ID</span>
			<strong><?php echo htmlspecialchars($ticket_no); ?></strong>
		</div>
		<div class="success-actions">
			<a class="success-btn" href="home.php?UserTab=DashBoard">OK</a>
		</div>
	</div>
</section>
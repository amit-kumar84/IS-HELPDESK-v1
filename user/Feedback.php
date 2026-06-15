
<?php
$closeNoticeType = null;
$closeNoticeMessage = null;
$closeSuccessTicket = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ticket_no'])) {
    $ticket_no = $_POST['ticket_no'];
    $solution = $_POST['solution'] ?? '';
    $support_engg = $_POST['support_engg'] ?? '';
    $status = $_POST['status'] ?? '';
    
    if (!empty($solution) && !empty($support_engg) && !empty($status)) {
        date_default_timezone_set('Asia/Kolkata');
        $complt_dt = date('d-m-Y h:i:s A');
        
        // Use prepared statement for security
        $stmt = mysqli_prepare($link, "UPDATE `complain_register` SET `support_engg`=?, `solution`=?, `status`=?, `s_DateTime`=? WHERE `t_no`=? AND `Staff_no`=?");
        mysqli_stmt_bind_param($stmt, 'ssssss', $support_engg, $solution, $status, $complt_dt, $ticket_no, $sid);
        
        if (mysqli_stmt_execute($stmt)) {
            $closeNoticeType = 'success';
            $closeNoticeMessage = 'Complaint closed successfully!';
            $closeSuccessTicket = $ticket_no;
        } else {
            $closeNoticeType = 'error';
            $closeNoticeMessage = 'Error updating complaint. Please try again.';
        }
        mysqli_stmt_close($stmt);
    } else {
        $closeNoticeType = 'error';
        $closeNoticeMessage = 'All fields are required.';
    }
}

// Fetch pending complaints
$sel = mysqli_query($link, "SELECT * FROM `complain_register` WHERE `Staff_no`='$sid' AND `status`='Pending' ORDER BY substring(t_no,8,13) DESC");
$total_row_count = mysqli_num_rows($sel);

// Fetch support engineers
$suppot_engg_data_fetch = mysqli_query($link, "SELECT * FROM `s_engg_login` WHERE `status`='0' AND `presence`='P' ORDER BY engg_name ASC");
$engineers = [];
while ($eng = mysqli_fetch_array($suppot_engg_data_fetch)) {
    $engineers[] = $eng["engg_name"];
}
?>

<!-- Success Modal -->
<?php if ($closeNoticeType === 'success'): ?>
<div class="submit-success-screen" id="closeSuccessModal">
    <div class="success-card">
        <div class="success-badge">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="success-eyebrow">Success</div>
        <div class="success-title">Complaint Closed</div>
        <div class="success-message">Ticket <strong>#<?php echo htmlspecialchars($closeSuccessTicket); ?></strong> has been closed and marked in the system.</div>
        <div class="success-actions">
            <button type="button" class="success-btn" id="closeSuccessBtn">
                <i class="fa-solid fa-check"></i> Confirmed
            </button>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var closeBtn = document.getElementById('closeSuccessBtn');
    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            window.location.href = window.location.pathname + '?UserTab=CloseComplain';
        });
    }
});
</script>
<?php elseif ($closeNoticeType === 'error'): ?>
<div class="submit-success-screen" id="closeErrorModal">
    <div class="success-card error-card">
        <div class="success-badge error-badge">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="success-eyebrow">Error</div>
        <div class="success-title"><?php echo htmlspecialchars($closeNoticeMessage); ?></div>
        <div class="success-actions">
            <button type="button" class="success-btn" id="closeErrorBtn">
                <i class="fa-solid fa-redo"></i> Dismiss
            </button>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var dismissBtn = document.getElementById('closeErrorBtn');
    if (dismissBtn) {
        dismissBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var modal = document.getElementById('closeErrorModal');
            if (modal) {
                modal.style.display = 'none';
            }
        });
    }
});
</script>
<?php endif; ?>

<!-- Hero Section -->
<div class="close-complaint-hero">
    <div>
        <div class="hero-badge">Service / Close</div>
        <h1>Close Complaint</h1>
        <p>Review pending complaints and close them with solution details, assigned engineer, and final status.</p>
        <ul class="hero-points">
            <li><i class="fa-solid fa-circle-check"></i> Mark tickets as Solved or Attend</li>
            <li><i class="fa-solid fa-user-gear"></i> Assign support engineer</li>
            <li><i class="fa-solid fa-notepad"></i> Document solution</li>
        </ul>
    </div>
    <div class="close-complaint-stats">
        <div class="stat-block">
            <div class="stat-number"><?php echo $total_row_count; ?></div>
            <div class="stat-label">Pending</div>
        </div>
    </div>
</div>

<!-- Tickets List -->
<div class="close-complaint-section">
    <?php if ($total_row_count > 0): ?>
        <?php while ($arr = mysqli_fetch_array($sel)): ?>
            <form method="POST" action="" class="close-complaint-card">
                <input type="hidden" name="ticket_no" value="<?php echo htmlspecialchars($arr['t_no']); ?>">
                
                <div class="close-complaint-header">
                    <div class="close-ticket-info">
                        <div class="close-ticket-number"><?php echo htmlspecialchars($arr['t_no']); ?></div>
                        <div class="close-ticket-datetime"><i class="fa-solid fa-calendar"></i> <?php echo htmlspecialchars($arr['r_DateTime']); ?></div>
                    </div>
                </div>
                
                <div class="close-complaint-body">
                    <div class="close-field-group">
                        <label class="close-field-label"><i class="fa-solid fa-file-alt"></i> Problem Description</label>
                        <div class="close-problem-text"><?php echo htmlspecialchars($arr['problem']); ?></div>
                    </div>
                    
                    <div class="close-field-row">
                        <div class="close-field-group">
                            <label class="close-field-label" for="solution_<?php echo $arr['t_no']; ?>"><i class="fa-solid fa-lightbulb"></i> Solution</label>
                            <textarea class="close-field-input" id="solution_<?php echo $arr['t_no']; ?>" name="solution" placeholder="Enter solution details..." required></textarea>
                        </div>
                    </div>
                    
                    <div class="close-field-row">
                        <div class="close-field-group">
                            <label class="close-field-label" for="support_engg_<?php echo $arr['t_no']; ?>"><i class="fa-solid fa-user-gear"></i> Support Engineer</label>
                            <select class="close-field-input" id="support_engg_<?php echo $arr['t_no']; ?>" name="support_engg" required>
                                <option value="">Select engineer</option>
                                <option value="Self"><i class="fa-solid fa-user"></i> Self</option>
                                <?php foreach ($engineers as $eng): ?>
                                    <option value="<?php echo htmlspecialchars($eng); ?>"><?php echo htmlspecialchars($eng); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="close-field-group">
                            <label class="close-field-label" for="status_<?php echo $arr['t_no']; ?>"><i class="fa-solid fa-flag-checkered"></i> Status</label>
                            <select class="close-field-input" id="status_<?php echo $arr['t_no']; ?>" name="status" required>
                                <option value="">Select status</option>
                                <option value="Solved"><i class="fa-solid fa-circle-check"></i> Solved</option>
                                <option value="Attend"><i class="fa-solid fa-user-gear"></i> Attend</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="close-complaint-footer">
                    <button type="submit" class="close-submit-btn">
                        <i class="fa-solid fa-paper-plane"></i> Submit & Close
                    </button>
                </div>
            </form>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="close-complaint-empty">
            <i class="fa-solid fa-inbox"></i>
            <div class="empty-title">No Pending Complaints</div>
            <div class="empty-message">All your complaints have been closed. Great work!</div>
        </div>
    <?php endif; ?>
</div>
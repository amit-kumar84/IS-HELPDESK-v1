<?php
/**
 * Renders the 3-column "info row" (Forms Download | Suggestion | Latest News)
 * + bottom Notice Board, just like the old portal.
 *
 * Requires: $link (mysqli), $sid (user id), current_user_id(), current_role()
 *
 * Optional: pass $hide_suggestion=true to skip the suggestion column
 * (e.g. on admin dashboard where admins consume suggestions instead).
 */

// Handle suggestion submission
if (isset($_POST['sub_suggest'])) {
    $msg     = trim($_POST['suggest_msg'] ?? '');
    $subj    = trim($_POST['suggest_subject'] ?? 'सुझाव');
    if ($msg !== '' && current_user_id() !== '') {
        $uid   = current_user_id();
        $role  = current_role();
        $uname = $_SESSION['user_name'] ?? $uid;
        $dept  = '';
        if ($role === 'user') {
            $s = mysqli_prepare($link, "SELECT deptt FROM emp_details WHERE staffid=? LIMIT 1");
            mysqli_stmt_bind_param($s, 's', $uid); mysqli_stmt_execute($s);
            $r = mysqli_fetch_assoc(mysqli_stmt_get_result($s));
            $dept = $r['deptt'] ?? '';
        } elseif ($role === 'Engineer') {
            $s = mysqli_prepare($link, "SELECT support_field FROM s_engg_login WHERE enggid=? LIMIT 1");
            mysqli_stmt_bind_param($s, 's', $uid); mysqli_stmt_execute($s);
            $r = mysqli_fetch_assoc(mysqli_stmt_get_result($s));
            $dept = $r['support_field'] ?? '';
        }
        $ins = mysqli_prepare($link, "INSERT INTO suggestions (user_staff_id,user_name,user_dept,user_role,subject,message) VALUES (?,?,?,?,?,?)");
        mysqli_stmt_bind_param($ins, 'ssssss', $uid, $uname, $dept, $role, $subj, $msg);
        mysqli_stmt_execute($ins);
        flash_set('success', 'धन्यवाद! आपका सुझाव सफलतापूर्वक भेज दिया गया है। Thank you — your suggestion has been received.');
        $redir = $_SERVER['REQUEST_URI'];
        header('Location: ' . $redir); exit;
    }
}

$news_rows   = @mysqli_query($link, "SELECT * FROM news_items WHERE is_active=1 ORDER BY sort_order ASC, id DESC");
$form_rows   = @mysqli_query($link, "SELECT * FROM form_downloads WHERE is_active=1 ORDER BY sort_order ASC, id ASC");
$notice_rows = @mysqli_query($link, "SELECT * FROM notice_board WHERE is_active=1 ORDER BY sort_order ASC, id ASC");

// Announcement: first NEW news item (safe — survives missing table)
$__ann_q = @mysqli_query($link, "SELECT title, link FROM news_items WHERE is_active=1 AND is_new=1 ORDER BY sort_order ASC LIMIT 1");
$ann     = $__ann_q ? mysqli_fetch_assoc($__ann_q) : null;
$hide_suggestion = $hide_suggestion ?? false;
?>

<?php if ($ann): ?>
<div class="announcement-bar">
    <span class="lbl">Update / Announcement</span>
    <div class="scroll"><span><span class="new-flag">NEW</span> * <?= e($ann['title']) ?> &nbsp;&nbsp;&nbsp; <span class="new-flag">NEW</span> * <?= e($ann['title']) ?></span></div>
</div>
<?php endif; ?>

<div class="info-grid">
    <!-- Forms Download -->
    <div class="info-card forms">
        <div class="ic-head"><i class="fa-solid fa-cloud-arrow-down"></i> प्रपत्र डाउनलोड &middot; Forms Download</div>
        <div class="ic-body">
            <ul>
            <?php while ($form_rows && ($f = mysqli_fetch_assoc($form_rows))):
                $exists = is_file($f['file_path']);
                $href = $exists ? $f['file_path'] : '#';
                $extraAttrs = $exists ? 'target="_blank" rel="noopener" download' : 'onclick="alert(\'Form file not yet uploaded by admin.\');return false"';
            ?>
                <li>
                    <i class="fa-solid <?= e($f['icon']) ?> lead"></i>
                    <a href="<?= e($href) ?>" <?= $extraAttrs ?>><?= e($f['title']) ?></a>
                </li>
            <?php endwhile; ?>
            </ul>
        </div>
    </div>

    <?php if (!$hide_suggestion && logged_in()): ?>
    <!-- Suggestion (सुझाव) -->
    <div class="info-card suggest">
        <div class="ic-head"><i class="fa-solid fa-lightbulb"></i> सुझाव &middot; Suggestion Box</div>
        <div class="ic-body">
            <form method="post" data-testid="suggestion-form">
                <input type="text" name="suggest_subject" placeholder="विषय / Subject (optional)" style="margin-bottom:8px" maxlength="180">
                <textarea name="suggest_msg" rows="5" placeholder="अपना सुझाव यहाँ लिखें... / Write your suggestion here..." required minlength="5" maxlength="1000" data-testid="suggestion-message"></textarea>
                <div class="flex-end mt-2">
                    <button type="submit" name="sub_suggest" class="btn btn-sm btn-success" data-testid="suggestion-submit"><i class="fa-solid fa-paper-plane"></i> भेजें &middot; Send</button>
                </div>
            </form>
            <div style="font-size:11px;color:#64748b;margin-top:6px"><i class="fa-solid fa-circle-info"></i> आपका सुझाव आपके नाम और विभाग के साथ प्रशासक को भेजा जायेगा।</div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Latest News -->
    <div class="info-card news">
        <div class="ic-head"><i class="fa-solid fa-bullhorn"></i> ताज़ा समाचार &middot; Latest News</div>
        <div class="ic-body">
            <ul>
            <?php while ($news_rows && ($n = mysqli_fetch_assoc($news_rows))): ?>
                <li>
                    <i class="fa-solid fa-circle lead" style="font-size:6px;margin-top:7px"></i>
                    <div>
                        <?php if ($n['is_new']): ?><span class="new-pill">NEW</span><?php endif; ?>
                        <?php if (!empty($n['link'])): ?>
                            <?php 
                            $is_pdf = strpos($n['link'], 'forms/') === 0 || stripos($n['link'], '.pdf') !== false || stripos($n['link'], '.doc') !== false;
                            $is_doc = stripos($n['link'], '.doc') !== false;
                            $icon_class = $is_pdf ? ($is_doc ? 'fa-file-word' : 'fa-file-pdf') : 'fa-link';
                            $icon_color = $is_pdf ? ($is_doc ? '#2563eb' : '#dc2626') : '#0284c7';
                            ?>
                            <a href="<?= e($n['link']) ?>" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:6px;color:#1e40af;text-decoration:none;transition:all .3s ease;border-bottom:2px solid transparent;cursor:pointer;font-weight:500;padding:2px 4px;border-radius:4px">
                                <i class="fa-solid <?= $icon_class ?>" style="font-size:12px;color:<?= $icon_color ?>;transition:.3s ease"></i>
                                <?= e($n['title']) ?>
                            </a>
                        <?php else: ?>
                            <span style="color:#334155;font-weight:500"><?= e($n['title']) ?></span>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endwhile; ?>
            </ul>
        </div>
    </div>
</div>

<!-- Notice Board (सूचना पट्ट) -->
<div class="notice-strip">
    <div class="ns-title"><i class="fa-solid fa-bullhorn"></i> सूचना पट्ट &middot; Notice Board</div>
    <div class="ns-items">
    <?php while ($notice_rows && ($nb = mysqli_fetch_assoc($notice_rows))):
        $exists = !empty($nb['link']) && (preg_match('#^https?://#', $nb['link']) || is_file($nb['link']));
    ?>
        <a href="<?= e($nb['link'] ?: '#') ?>" <?= $exists ? 'target="_blank" rel="noopener"' : 'onclick="alert(\'Document not yet uploaded.\');return false"' ?>>
            <i class="fa-solid <?= e($nb['icon']) ?>"></i> <?= e($nb['title']) ?>
        </a>
    <?php endwhile; ?>
    </div>
</div>

<?php
/** Admin: Suggestions inbox */
require_once 'includes/photo.php';

// Mark read / delete / reply
if (isset($_POST['mark_read'])) {
    $id = (int)$_POST['mark_read'];
    mysqli_query($link, "UPDATE suggestions SET is_read=1 WHERE id=$id");
    flash_set('success', 'Suggestion marked as read.');
    header('Location: Admin_Home.php?AdminTab=Suggestions'); exit;
}
if (isset($_POST['delete_sugg'])) {
    $id = (int)$_POST['delete_sugg'];
    mysqli_query($link, "DELETE FROM suggestions WHERE id=$id");
    flash_set('success', 'Suggestion deleted.');
    header('Location: Admin_Home.php?AdminTab=Suggestions'); exit;
}
if (isset($_POST['reply_id'])) {
    $id  = (int)$_POST['reply_id'];
    $rep = trim($_POST['reply_text'] ?? '');
    $stmt = mysqli_prepare($link, "UPDATE suggestions SET admin_reply=?, is_read=1 WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'si', $rep, $id);
    mysqli_stmt_execute($stmt);
    flash_set('success', 'Reply saved.');
    header('Location: Admin_Home.php?AdminTab=Suggestions'); exit;
}

$filter = $_GET['f'] ?? 'unread';
$where  = $filter === 'read'    ? 'WHERE is_read=1' :
         ($filter === 'all'     ? '' : 'WHERE is_read=0');
$rows   = mysqli_query($link, "SELECT * FROM suggestions $where ORDER BY created_at DESC");
$unread = (int) mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM suggestions WHERE is_read=0"))[0];
$total  = (int) mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) FROM suggestions"))[0];
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid fa-lightbulb"></i></div>
    <div>
        <h2>सुझाव बॉक्स &middot; User Suggestions</h2>
        <div class="sub">Messages submitted by employees and engineers through the suggestion box.</div>
    </div>
    <div class="actions">
        <span class="badge danger" style="font-size:11px;padding:5px 10px"><i class="fa-solid fa-envelope"></i> &nbsp;<?= $unread ?> Unread</span>
        <span class="badge active" style="font-size:11px;padding:5px 10px"><i class="fa-solid fa-inbox"></i> &nbsp;<?= $total ?> Total</span>
    </div>
</div>

<div class="card" style="padding:10px 14px">
    <div class="flex" style="gap:6px;flex-wrap:wrap">
        <?php foreach (['unread'=>'Unread ('.$unread.')','read'=>'Read','all'=>'All ('.$total.')'] as $k=>$lbl): ?>
            <a class="btn btn-sm <?= $filter===$k ? '' : 'btn-secondary' ?>" href="Admin_Home.php?AdminTab=Suggestions&f=<?= $k ?>"><?= e($lbl) ?></a>
        <?php endforeach; ?>
    </div>
</div>

<?php if (mysqli_num_rows($rows) === 0): ?>
    <div class="card text-center" style="padding:40px;color:#94a3b8"><i class="fa-solid fa-inbox" style="font-size:32px;display:block;margin-bottom:8px"></i> कोई सुझाव नहीं है &middot; No suggestions yet.</div>
<?php else: ?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(420px,1fr));gap:14px">
<?php while ($s = mysqli_fetch_assoc($rows)): ?>
    <div class="card" style="margin-bottom:0;<?= $s['is_read']==0 ? 'border-left:3px solid #ea7600' : '' ?>">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
            <?= render_avatar($s['user_staff_id'], $s['user_name'], 44, $s['user_role']==='Engineer' ? 'images/engineers' : 'Pictures') ?>
            <div style="flex:1;min-width:0">
                <div style="font-weight:700;font-size:13.5px;color:#0a1f44"><?= e($s['user_name']) ?></div>
                <div style="font-size:11px;color:#64748b">
                    <i class="fa-solid fa-id-card"></i> <?= e($s['user_staff_id']) ?>
                    <?php if ($s['user_dept']): ?>&middot; <?= e($s['user_dept']) ?><?php endif; ?>
                    &middot; <span class="badge <?= $s['user_role']==='Admin'?'attend':($s['user_role']==='Engineer'?'solved':'active') ?>" style="font-size:9.5px"><?= e($s['user_role']) ?></span>
                </div>
            </div>
            <?php if ($s['is_read']==0): ?><span class="badge danger" style="font-size:9.5px">NEW</span><?php endif; ?>
        </div>
        <?php if (!empty($s['subject'])): ?>
            <div style="font-weight:600;font-size:13px;color:#1e3a8a;margin-bottom:4px"><i class="fa-solid fa-tag"></i> <?= e($s['subject']) ?></div>
        <?php endif; ?>
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:10px;font-size:12.5px;color:#0f172a;white-space:pre-wrap"><?= e($s['message']) ?></div>
        <?php if (!empty($s['admin_reply'])): ?>
            <div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:8px;padding:10px;font-size:12px;color:#166534;margin-top:8px"><b><i class="fa-solid fa-reply"></i> Admin reply:</b><br><?= nl2br(e($s['admin_reply'])) ?></div>
        <?php endif; ?>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;font-size:11px;color:#94a3b8">
            <span><i class="fa-regular fa-clock"></i> <?= e(date('d M Y, H:i', strtotime($s['created_at']))) ?></span>
            <div style="display:flex;gap:5px">
                <?php if ($s['is_read']==0): ?>
                    <form method="post" style="display:inline"><button name="mark_read" value="<?= (int)$s['id'] ?>" class="btn btn-xs btn-secondary" title="Mark as read"><i class="fa-solid fa-check"></i></button></form>
                <?php endif; ?>
                <button class="btn btn-xs btn-secondary" onclick="document.getElementById('rf<?= (int)$s['id'] ?>').style.display='block'"><i class="fa-solid fa-reply"></i> Reply</button>
                <form method="post" style="display:inline" onsubmit="return confirm('Delete this suggestion?')"><button name="delete_sugg" value="<?= (int)$s['id'] ?>" class="btn btn-xs btn-danger" title="Delete"><i class="fa-solid fa-trash"></i></button></form>
            </div>
        </div>
        <form id="rf<?= (int)$s['id'] ?>" method="post" style="display:none;margin-top:8px;border-top:1px dashed #e2e8f0;padding-top:8px">
            <input type="hidden" name="reply_id" value="<?= (int)$s['id'] ?>">
            <textarea name="reply_text" rows="2" placeholder="Your reply..." required minlength="2"><?= e($s['admin_reply']) ?></textarea>
            <div class="flex-end mt-2"><button type="submit" class="btn btn-xs btn-success">Save Reply</button></div>
        </form>
    </div>
<?php endwhile; ?>
</div>
<?php endif; ?>

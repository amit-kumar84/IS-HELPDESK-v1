<?php
/**
 * Admin · Content Manager
 *
 * Single-screen CRUD for the three landing-page widgets:
 *   • news_items       — Latest News (right column)
 *   • notice_board     — Notice Board (bottom)
 *   • form_downloads   — Forms Download (left column)
 *
 * Routed from Admin_Home.php via ?AdminTab=ContentManager&section=news|notice|form
 */

$section = $_GET['section'] ?? 'news';
if (!in_array($section, ['news','notice','form'], true)) $section = 'news';

$tableMap = [
    'news'   => 'news_items',
    'notice' => 'notice_board',
    'form'   => 'form_downloads',
];
$labelMap = [
    'news'   => ['Latest News',     'fa-newspaper',         'newsroom announcements shown on every dashboard'],
    'notice' => ['Notice Board',    'fa-bullhorn',          'static notices pinned on every dashboard'],
    'form'   => ['Forms Download',  'fa-cloud-arrow-down',  'downloadable PDF / DOC forms'],
];
$tbl = $tableMap[$section];

/* ---------- HANDLE POST ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['act'] ?? '';
    $id  = (int)($_POST['id'] ?? 0);

    if ($act === 'delete' && $id > 0) {
        $stmt = mysqli_prepare($link, "DELETE FROM `$tbl` WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        flash_set('success', 'Item deleted.');
    }
    elseif ($act === 'toggle' && $id > 0) {
        mysqli_query($link, "UPDATE `$tbl` SET is_active = 1 - is_active WHERE id = $id");
        flash_set('success', 'Visibility updated.');
    }
    elseif ($act === 'save') {
        $title = trim($_POST['title'] ?? '');
        $sort  = (int)($_POST['sort_order'] ?? 0);
        $act_v = isset($_POST['is_active']) ? 1 : 0;

        if ($title === '') {
            flash_set('danger', 'Title is required.');
        } else {
            if ($section === 'news') {
                $link_v = trim($_POST['link'] ?? '#');
                $isnew  = isset($_POST['is_new']) ? 1 : 0;
                if ($id > 0) {
                    $stmt = mysqli_prepare($link, "UPDATE news_items SET title=?, link=?, is_new=?, is_active=?, sort_order=? WHERE id=?");
                    mysqli_stmt_bind_param($stmt, 'ssiiii', $title, $link_v, $isnew, $act_v, $sort, $id);
                } else {
                    $stmt = mysqli_prepare($link, "INSERT INTO news_items (title, link, is_new, is_active, sort_order) VALUES (?,?,?,?,?)");
                    mysqli_stmt_bind_param($stmt, 'ssiii', $title, $link_v, $isnew, $act_v, $sort);
                }
                mysqli_stmt_execute($stmt);
            }
            elseif ($section === 'notice') {
                $body = trim($_POST['body'] ?? '');
                $icon = trim($_POST['icon'] ?? 'fa-bullhorn');
                if ($id > 0) {
                    $stmt = mysqli_prepare($link, "UPDATE notice_board SET title=?, body=?, icon=?, is_active=?, sort_order=? WHERE id=?");
                    mysqli_stmt_bind_param($stmt, 'sssiii', $title, $body, $icon, $act_v, $sort, $id);
                } else {
                    $stmt = mysqli_prepare($link, "INSERT INTO notice_board (title, body, icon, is_active, sort_order) VALUES (?,?,?,?,?)");
                    mysqli_stmt_bind_param($stmt, 'sssii', $title, $body, $icon, $act_v, $sort);
                }
                mysqli_stmt_execute($stmt);
            }
            elseif ($section === 'form') {
                $file = trim($_POST['file_path'] ?? '');
                $icon = trim($_POST['icon'] ?? 'fa-file-pdf');
                if ($id > 0) {
                    $stmt = mysqli_prepare($link, "UPDATE form_downloads SET title=?, file_path=?, icon=?, is_active=?, sort_order=? WHERE id=?");
                    mysqli_stmt_bind_param($stmt, 'sssiii', $title, $file, $icon, $act_v, $sort, $id);
                } else {
                    $stmt = mysqli_prepare($link, "INSERT INTO form_downloads (title, file_path, icon, is_active, sort_order) VALUES (?,?,?,?,?)");
                    mysqli_stmt_bind_param($stmt, 'sssii', $title, $file, $icon, $act_v, $sort);
                }
                mysqli_stmt_execute($stmt);
            }
            flash_set('success', $id > 0 ? 'Item updated.' : 'Item added.');
        }
    }
    header('Location: Admin_Home.php?AdminTab=ContentManager&section=' . $section);
    exit;
}

/* ---------- Load row for editing ---------- */
$editing = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $r = mysqli_query($link, "SELECT * FROM `$tbl` WHERE id = $eid");
    $editing = mysqli_fetch_assoc($r) ?: null;
}

/* ---------- List rows ---------- */
$rows = mysqli_query($link, "SELECT * FROM `$tbl` ORDER BY sort_order ASC, id DESC");
[$L_title, $L_icon, $L_desc] = $labelMap[$section];
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid <?= e($L_icon) ?>"></i></div>
    <div>
        <h2>Content Manager · <?= e($L_title) ?></h2>
        <div class="sub">Manage the <?= e($L_desc) ?>. Changes appear instantly on every dashboard.</div>
    </div>
</div>

<!-- Section tabs -->
<div class="card" style="padding:6px;display:inline-flex;gap:4px;margin-bottom:14px" data-testid="cms-tabs">
    <?php foreach ($labelMap as $k => $lbl): ?>
        <a class="btn btn-sm <?= $section===$k ? '' : 'btn-secondary' ?>"
           href="Admin_Home.php?AdminTab=ContentManager&section=<?= $k ?>"
           data-testid="cms-tab-<?= $k ?>"
           style="<?= $section===$k ? '' : 'background:#f1f5f9;color:#0a1f44' ?>">
            <i class="fa-solid <?= e($lbl[1]) ?>"></i> &nbsp;<?= e($lbl[0]) ?>
        </a>
    <?php endforeach; ?>
</div>

<div style="display:grid;grid-template-columns:1fr 380px;gap:18px;align-items:start">
    <!-- Existing items -->
    <div class="card">
        <div class="card-title"><i class="fa-solid fa-list"></i> Existing items</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th>Title</th>
                    <?php if ($section === 'news'): ?><th style="width:80px">NEW?</th><?php endif; ?>
                    <?php if ($section === 'form'): ?><th>File</th><?php endif; ?>
                    <th style="width:80px">Order</th>
                    <th style="width:90px">Active</th>
                    <th style="width:160px">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($rows) === 0): ?>
                <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:24px">No items yet — use the form on the right to add one.</td></tr>
            <?php endif; ?>
            <?php while ($r = mysqli_fetch_assoc($rows)): ?>
                <tr data-testid="cms-row-<?= (int)$r['id'] ?>">
                    <td><?= (int)$r['id'] ?></td>
                    <td><b><?= e($r['title']) ?></b>
                        <?php if ($section==='notice' && !empty($r['body'])): ?>
                            <div style="color:#64748b;font-size:11.5px;margin-top:2px"><?= e(mb_substr($r['body'],0,90)) ?><?= mb_strlen($r['body'])>90?'…':'' ?></div>
                        <?php endif; ?>
                    </td>
                    <?php if ($section === 'news'): ?>
                        <td><?= !empty($r['is_new']) ? '<span class="pill" style="background:#fee2e2;color:#b91c1c">NEW</span>' : '—' ?></td>
                    <?php endif; ?>
                    <?php if ($section === 'form'): ?>
                        <td style="font-size:11.5px;color:#475569;word-break:break-all"><?= e($r['file_path']) ?></td>
                    <?php endif; ?>
                    <td><?= (int)$r['sort_order'] ?></td>
                    <td>
                        <form method="post" style="display:inline">
                            <input type="hidden" name="act" value="toggle">
                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                            <button class="btn btn-xs <?= $r['is_active'] ? '' : 'btn-secondary' ?>"
                                    data-testid="cms-toggle-<?= (int)$r['id'] ?>"
                                    title="Click to toggle">
                                <i class="fa-solid <?= $r['is_active'] ? 'fa-eye' : 'fa-eye-slash' ?>"></i>
                                <?= $r['is_active'] ? 'Visible' : 'Hidden' ?>
                            </button>
                        </form>
                    </td>
                    <td>
                        <a class="btn btn-xs btn-secondary"
                           href="Admin_Home.php?AdminTab=ContentManager&section=<?= $section ?>&edit=<?= (int)$r['id'] ?>"
                           data-testid="cms-edit-<?= (int)$r['id'] ?>"
                           style="background:#dbeafe;color:#1e3a8a">
                            <i class="fa-solid fa-pen"></i> Edit
                        </a>
                        <form method="post" style="display:inline"
                              onsubmit="return confirm('Delete this item permanently?');">
                            <input type="hidden" name="act" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                            <button class="btn btn-xs"
                                    data-testid="cms-delete-<?= (int)$r['id'] ?>"
                                    style="background:#fee2e2;color:#b91c1c">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add / edit form -->
    <div class="card">
        <div class="card-title">
            <i class="fa-solid <?= $editing ? 'fa-pen-to-square' : 'fa-plus' ?>"></i>
            <?= $editing ? 'Edit item #' . (int)$editing['id'] : 'Add new item' ?>
        </div>
        <form method="post" data-testid="cms-form">
            <input type="hidden" name="act" value="save">
            <input type="hidden" name="id"  value="<?= (int)($editing['id'] ?? 0) ?>">

            <div class="form-row">
                <label>Title <span style="color:#dc2626">*</span></label>
                <input type="text" name="title" required
                       value="<?= e($editing['title'] ?? '') ?>"
                       data-testid="cms-input-title">
            </div>

            <?php if ($section === 'news'): ?>
                <div class="form-row">
                    <label>Link (URL)</label>
                    <input type="text" name="link" placeholder="https://… or #"
                           value="<?= e($editing['link'] ?? '#') ?>"
                           data-testid="cms-input-link">
                </div>
                <div class="form-row" style="display:flex;align-items:center;gap:8px">
                    <input type="checkbox" name="is_new" id="cms_isnew"
                           <?= !empty($editing['is_new']) ? 'checked' : '' ?>
                           data-testid="cms-input-isnew">
                    <label for="cms_isnew" style="margin:0">Show NEW flag</label>
                </div>
            <?php endif; ?>

            <?php if ($section === 'notice'): ?>
                <div class="form-row">
                    <label>Body</label>
                    <textarea name="body" rows="4" data-testid="cms-input-body"
                              style="width:100%"><?= e($editing['body'] ?? '') ?></textarea>
                </div>
                <div class="form-row">
                    <label>Icon (Font Awesome name)</label>
                    <input type="text" name="icon"
                           placeholder="fa-bullhorn / fa-server / fa-shield"
                           value="<?= e($editing['icon'] ?? 'fa-bullhorn') ?>"
                           data-testid="cms-input-icon">
                </div>
            <?php endif; ?>

            <?php if ($section === 'form'): ?>
                <div class="form-row">
                    <label>File path (relative to project root)</label>
                    <input type="text" name="file_path"
                           placeholder="forms/cartridge.pdf"
                           value="<?= e($editing['file_path'] ?? '') ?>"
                           data-testid="cms-input-file">
                </div>
                <div class="form-row">
                    <label>Icon (Font Awesome name)</label>
                    <input type="text" name="icon"
                           placeholder="fa-file-pdf"
                           value="<?= e($editing['icon'] ?? 'fa-file-pdf') ?>"
                           data-testid="cms-input-icon">
                </div>
            <?php endif; ?>

            <div class="form-row">
                <label>Sort order (lower shows first)</label>
                <input type="number" name="sort_order" min="0" step="1"
                       value="<?= (int)($editing['sort_order'] ?? 10) ?>"
                       data-testid="cms-input-sort">
            </div>
            <div class="form-row" style="display:flex;align-items:center;gap:8px">
                <input type="checkbox" name="is_active" id="cms_active"
                       <?= !$editing || !empty($editing['is_active']) ? 'checked' : '' ?>
                       data-testid="cms-input-active">
                <label for="cms_active" style="margin:0">Visible on dashboards</label>
            </div>

            <div style="display:flex;gap:8px;margin-top:14px">
                <button class="btn" type="submit" data-testid="cms-submit">
                    <i class="fa-solid fa-save"></i> <?= $editing ? 'Update' : 'Add Item' ?>
                </button>
                <?php if ($editing): ?>
                    <a class="btn btn-secondary"
                       href="Admin_Home.php?AdminTab=ContentManager&section=<?= $section ?>"
                       style="background:#f1f5f9;color:#0a1f44">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

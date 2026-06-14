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
                $file_type = trim($_POST['form_type'] ?? 'url');
                $link_v = '';
                $upload_error = '';
                if ($file_type === 'file') {
                    if (isset($_FILES['form_file']) && $_FILES['form_file']['error'] === UPLOAD_ERR_OK) {
                        $upload_dir = dirname(__FILE__) . '/../forms/';
                        if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true)) {
                            $upload_error = 'Could not create upload directory. Check server permissions.';
                        }
                        if (empty($upload_error)) {
                            $file_name = basename($_FILES['form_file']['name']);
                            $file_name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file_name);
                            $file_name = time() . '_' . $file_name;
                            $file_path = $upload_dir . $file_name;
                            if (move_uploaded_file($_FILES['form_file']['tmp_name'], $file_path)) {
                                $link_v = 'forms/' . $file_name;
                                flash_set('success', 'File uploaded successfully!');
                            } else {
                                $upload_error = 'Failed to move uploaded file. Check directory permissions.';
                            }
                        }
                    } elseif ($id > 0 && isset($_POST['file_path_existing']) && !empty($_POST['file_path_existing'])) {
                        $link_v = trim($_POST['file_path_existing']);
                    } else {
                        $upload_error = 'Please select a file to upload for Latest News.';
                    }
                } else {
                    $link_v = trim($_POST['link'] ?? '');
                    if ($link_v === '') {
                        $upload_error = 'Please enter a valid URL.';
                    }
                }
                $isnew  = isset($_POST['is_new']) ? 1 : 0;
                if (!empty($upload_error)) {
                    flash_set('danger', $upload_error);
                } else {
                    if ($id > 0) {
                        $stmt = mysqli_prepare($link, "UPDATE news_items SET title=?, link=?, is_new=?, is_active=?, sort_order=? WHERE id=?");
                        mysqli_stmt_bind_param($stmt, 'ssiiii', $title, $link_v, $isnew, $act_v, $sort, $id);
                    } else {
                        $stmt = mysqli_prepare($link, "INSERT INTO news_items (title, link, is_new, is_active, sort_order) VALUES (?,?,?,?,?)");
                        mysqli_stmt_bind_param($stmt, 'ssiii', $title, $link_v, $isnew, $act_v, $sort);
                    }
                    mysqli_stmt_execute($stmt);
                }
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
                $file_type = trim($_POST['form_type'] ?? 'file'); // 'url' or 'file' - default to file
                $file_v    = '';
                $upload_error = '';
                
                if ($file_type === 'file') {
                    // Handle file upload mode
                    if (isset($_FILES['form_file']) && $_FILES['form_file']['error'] === UPLOAD_ERR_OK) {
                        // File was selected and uploaded
                        $upload_dir = dirname(__FILE__) . '/../forms/';
                        if (!is_dir($upload_dir)) {
                            if (!mkdir($upload_dir, 0755, true)) {
                                $upload_error = 'Could not create upload directory. Check server permissions.';
                            }
                        }
                        
                        if (empty($upload_error)) {
                            $file_name = basename($_FILES['form_file']['name']);
                            $file_name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file_name);
                            // Add timestamp to avoid conflicts
                            $file_name = time() . '_' . $file_name;
                            $file_path = $upload_dir . $file_name;
                            
                            if (move_uploaded_file($_FILES['form_file']['tmp_name'], $file_path)) {
                                $file_v = 'forms/' . $file_name;
                                flash_set('success', 'File uploaded successfully!');
                            } else {
                                $upload_error = 'Failed to move uploaded file. Check directory permissions (needs 755).';
                            }
                        }
                    } elseif (isset($_FILES['form_file']) && $_FILES['form_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                        // File was selected but error occurred
                        $err_codes = [
                            UPLOAD_ERR_INI_SIZE => 'File too large (exceeds php.ini limit)',
                            UPLOAD_ERR_FORM_SIZE => 'File too large (exceeds form limit)',
                            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                            UPLOAD_ERR_NO_TMP_DIR => 'No temporary directory',
                            UPLOAD_ERR_CANT_WRITE => 'Cannot write to directory',
                            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
                        ];
                        $upload_error = 'Upload error: ' . ($err_codes[$_FILES['form_file']['error']] ?? 'Unknown error #' . $_FILES['form_file']['error']);
                    } elseif ($id > 0 && isset($_POST['file_path_existing']) && !empty($_POST['file_path_existing'])) {
                        // Editing: keep existing file if no new file uploaded
                        $file_v = trim($_POST['file_path_existing']);
                    } else {
                        // New form or editing without file = error
                        $upload_error = 'Please select a PDF file to upload.';
                    }
                } elseif ($file_type === 'url') {
                    // Handle URL mode
                    $file_v = trim($_POST['form_url'] ?? '');
                    if (empty($file_v)) {
                        $upload_error = 'Please enter a URL.';
                    }
                }
                
                if (!empty($upload_error)) {
                    flash_set('danger', $upload_error);
                } elseif (!empty($file_v)) {
                    $icon = trim($_POST['icon'] ?? 'fa-file-pdf');
                    if ($id > 0) {
                        $stmt = mysqli_prepare($link, "UPDATE form_downloads SET title=?, file_path=?, icon=?, is_active=?, sort_order=? WHERE id=?");
                        mysqli_stmt_bind_param($stmt, 'sssiii', $title, $file_v, $icon, $act_v, $sort, $id);
                    } else {
                        $stmt = mysqli_prepare($link, "INSERT INTO form_downloads (title, file_path, icon, is_active, sort_order) VALUES (?,?,?,?,?)");
                        mysqli_stmt_bind_param($stmt, 'sssii', $title, $file_v, $icon, $act_v, $sort);
                    }
                    if (mysqli_stmt_execute($stmt)) {
                        if (empty($file_v)) flash_set('success', 'Form saved successfully!');
                    } else {
                        flash_set('danger', 'Database error: ' . mysqli_error($link));
                    }
                } else {
                    flash_set('danger', 'No file or URL provided.');
                }
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
$panelOpen = (bool)$editing;
?>

<div class="page-head">
    <div class="ic"><i class="fa-solid <?= e($L_icon) ?>"></i></div>
    <div>
        <h2>Content Manager · <?= e($L_title) ?></h2>
        <div class="sub">Manage the <?= e($L_desc) ?>. Changes appear instantly on every dashboard.</div>
    </div>
    <div class="page-head actions">
        <button class="btn btn-accent btn-sm" id="cms-open-panel" type="button">
            <i class="fa-solid fa-plus"></i> Add new item
        </button>
    </div>
</div>

<div class="cms-action-bar">
    <div class="cms-tabs" data-testid="cms-tabs">
        <?php foreach ($labelMap as $k => $lbl): ?>
            <a class="btn btn-sm <?= $section===$k ? '' : 'btn-secondary' ?> <?= $section===$k ? 'active-tab' : '' ?>"
               href="Admin_Home.php?AdminTab=ContentManager&section=<?= $k ?>"
               data-testid="cms-tab-<?= $k ?>">
                <i class="fa-solid <?= e($lbl[1]) ?>"></i>&nbsp;<?= e($lbl[0]) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<div class="cms-grid">
    <!-- Existing items -->
    <div class="card cms-list-card">
        <div class="card-title"><i class="fa-solid fa-list"></i> Existing items</div>
        <div class="table-wrap">
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
</div>

<div class="cms-modal-backdrop <?= $panelOpen ? 'visible' : '' ?>" id="cms-backdrop"></div>

<div class="cms-panel <?= $panelOpen ? 'open' : '' ?>" id="cms-panel">
    <div class="cms-panel-header">
        <div>
            <div class="panel-badge" id="cms-panel-badge">
                <i class="fa-solid <?= $section === 'news' ? 'fa-newspaper' : ($section === 'notice' ? 'fa-bullhorn' : 'fa-cloud-arrow-down') ?>"></i>
                <?= $editing ? 'Edit mode' : 'Create new' ?>
            </div>
            <h3 id="cms-panel-title"><?= $editing ? 'Edit item #' . (int)$editing['id'] : 'Add a new content item' ?></h3>
            <p id="cms-panel-subtitle" style="opacity:.9">Fill the form below and save to publish instantly.</p>
        </div>
        <button class="btn btn-secondary btn-sm" id="cms-close-panel" type="button" style="border-radius:10px;padding:8px 12px;font-size:12px;transition:all .3s ease">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <form method="post" enctype="multipart/form-data" data-testid="cms-form" class="cms-form" id="cms-content-form">            <input type="hidden" name="act" value="save">
            <input type="hidden" name="id"  value="<?= (int)($editing['id'] ?? 0) ?>" id="cms-hidden-id">
            
            <div class="form-row">
                <label><i class="fa-solid fa-heading" style="color:#2563eb;font-size:13px"></i> Title <span class="req" style="color:#dc2626">*</span></label>
                <input type="text" name="title" required placeholder="Enter a descriptive title..."
                       value="<?= e($editing['title'] ?? '') ?>"
                       data-testid="cms-input-title">
            </div>

            <?php if ($section === 'news'): ?>
                <div class="form-row" style="display:flex;gap:16px;flex-wrap:wrap;align-items:center;padding:12px 14px;background:linear-gradient(135deg,rgba(14,165,233,.08),rgba(59,130,246,.08));border:1.5px solid rgba(59,130,246,.2);border-radius:12px">
                    <div style="flex:1;min-width:220px">
                        <label style="display:flex;align-items:center;gap:6px;margin-bottom:10px"><i class="fa-solid fa-link" style="color:#0284c7;font-size:13px"></i> Content Type</label>
                        <div class="form-row inline" style="margin:0;gap:12px">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:8px 12px;border-radius:8px;background:rgba(255,255,255,.5);transition:all .3s ease;border:1px solid transparent">
                                <input type="radio" name="form_type" value="url"
                                       <?= empty($editing) || (strpos($editing['link'] ?? '', 'forms/') !== 0) ? 'checked' : '' ?>
                                       data-testid="cms-input-type-url">
                                <i class="fa-solid fa-globe" style="color:#0284c7"></i> Web Link
                            </label>
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:8px 12px;border-radius:8px;background:rgba(255,255,255,.5);transition:all .3s ease;border:1px solid transparent">
                                <input type="radio" name="form_type" value="file"
                                       <?= !empty($editing) && strpos($editing['link'] ?? '', 'forms/') === 0 ? 'checked' : '' ?>
                                       data-testid="cms-input-type-file">
                                <i class="fa-solid fa-file-pdf" style="color:#dc2626"></i> PDF File
                            </label>
                        </div>
                    </div>
                </div>

                <div id="news_url_input" class="form-row" style="display:<?= empty($editing) || (strpos($editing['link'] ?? '', 'forms/') !== 0) ? 'block' : 'none' ?>">
                    <label><i class="fa-solid fa-globe" style="color:#0284c7;font-size:13px"></i> Website URL</label>
                    <input type="text" name="link" placeholder="https://example.com"
                           value="<?= e((strpos($editing['link'] ?? '', 'forms/') !== 0) ? ($editing['link'] ?? '') : '') ?>"
                           data-testid="cms-input-link">
                </div>

                <div id="news_file_input" class="form-row" style="display:<?= !empty($editing) && strpos($editing['link'] ?? '', 'forms/') === 0 ? 'block' : 'none' ?>">
                    <label><i class="fa-solid fa-file-pdf" style="color:#dc2626;font-size:13px"></i> Upload PDF</label>
                    <input type="file" name="form_file" accept=".pdf,.doc,.docx" style="border-color:rgba(220,38,38,.3);background:linear-gradient(135deg,rgba(254,226,226,.3),rgba(255,237,213,.2)) !important"
                           data-testid="cms-input-pdf">
                    <?php if (!empty($editing) && strpos($editing['link'] ?? '', 'forms/') === 0): ?>
                        <div style="font-size:12px;color:#7c2d12;margin-top:10px;padding:10px;background:linear-gradient(135deg,#fef3c7,#fecdd3);border-radius:8px;border-left:3px solid #dc2626">
                            <strong style="color:#991b1b"><i class="fa-solid fa-file-check"></i> Current:</strong> <?= e(basename($editing['link'])) ?>
                            <br><span style="font-size:11px;opacity:.8">Upload a new file to replace</span>
                            <input type="hidden" name="file_path_existing" value="<?= e($editing['link']) ?>">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-row" style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:linear-gradient(135deg,rgba(239,68,68,.06),rgba(249,115,22,.06));border:1.5px solid rgba(239,68,68,.15);border-radius:10px">
                    <input type="checkbox" name="is_new" id="cms_isnew" style="width:20px;height:20px;accent-color:#dc2626"
                           <?= !empty($editing['is_new']) ? 'checked' : '' ?>
                           data-testid="cms-input-isnew">
                    <label for="cms_isnew" style="margin:0;cursor:pointer;font-weight:500"><i class="fa-solid fa-star" style="color:#dc2626"></i> Show "NEW" Badge</label>
                </div>
            <?php endif; ?>

            <?php if ($section === 'notice'): ?>
                <div class="form-row">
                    <label><i class="fa-solid fa-message" style="color:#8b5cf6;font-size:13px"></i> Message Content</label>
                    <textarea name="body" rows="4" placeholder="Enter your notice message here..." data-testid="cms-input-body"
                              style="width:100%"><?= e($editing['body'] ?? '') ?></textarea>
                </div>
                <div class="form-row">
                    <label><i class="fa-solid fa-icons" style="color:#6366f1;font-size:13px"></i> Icon (Font Awesome)</label>
                    <input type="text" name="icon"
                           placeholder="e.g., fa-bullhorn / fa-server / fa-shield / fa-exclamation-triangle"
                           value="<?= e($editing['icon'] ?? 'fa-bullhorn') ?>"
                           data-testid="cms-input-icon">
                    <p style="font-size:11px;color:#64748b;margin:6px 0 0;"><i class="fa-solid fa-lightbulb"></i> Search Font Awesome icons at <code style="background:#f1f5f9;padding:2px 6px;border-radius:4px">fontawesome.com</code></p>
                </div>
            <?php endif; ?>

            <?php if ($section === 'form'): ?>
                <div class="form-row" style="display:flex;gap:12px;align-items:center;margin-bottom:12px;padding:12px 14px;background:linear-gradient(135deg,rgba(34,197,94,.08),rgba(74,222,128,.08));border:1.5px solid rgba(34,197,94,.2);border-radius:12px">
                    <label style="margin:0;font-weight:600;display:flex;align-items:center;gap:6px"><i class="fa-solid fa-folder-open" style="color:#16a34a;font-size:13px"></i> Form Type:</label>
                    <div style="display:flex;gap:12px;flex-wrap:wrap">
                        <label style="display:flex;align-items:center;gap:8px;margin:0;cursor:pointer;padding:8px 12px;border-radius:8px;background:rgba(255,255,255,.5);transition:all .3s ease;border:1px solid transparent">
                            <input type="radio" name="form_type" value="file" 
                                   <?= empty($editing) || (strpos($editing['file_path'] ?? '', 'http') !== 0) ? 'checked' : '' ?>
                                   onchange="document.getElementById('form_file_input').style.display='block'; document.getElementById('form_url_input').style.display='none'">
                            <i class="fa-solid fa-file-arrow-down" style="color:#16a34a"></i> Local PDF
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;margin:0;cursor:pointer;padding:8px 12px;border-radius:8px;background:rgba(255,255,255,.5);transition:all .3s ease;border:1px solid transparent">
                            <input type="radio" name="form_type" value="url"
                                   <?= !empty($editing) && strpos($editing['file_path'] ?? '', 'http') === 0 ? 'checked' : '' ?>
                                   onchange="document.getElementById('form_file_input').style.display='none'; document.getElementById('form_url_input').style.display='block'">
                            <i class="fa-solid fa-link" style="color:#2563eb"></i> External URL
                        </label>
                    </div>
                </div>

                <div id="form_file_input" class="form-row" style="display:<?= empty($editing) || (strpos($editing['file_path'] ?? '', 'http') !== 0) ? 'block' : 'none' ?>">
                    <label><i class="fa-solid fa-file-pdf" style="color:#dc2626;font-size:13px"></i> Upload PDF/DOC <span style="color:#dc2626">*</span></label>
                    <input type="file" name="form_file" accept=".pdf,.doc,.docx" style="border-color:rgba(34,197,94,.3);background:linear-gradient(135deg,rgba(220,252,231,.3),rgba(236,253,245,.2)) !important"
                           data-testid="cms-input-pdf">
                    <?php if (!empty($editing) && !empty($editing['file_path']) && strpos($editing['file_path'], 'http') !== 0): ?>
                        <div style="font-size:12px;color:#15803d;margin-top:10px;padding:10px;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-radius:8px;border-left:3px solid #16a34a">
                            <strong style="color:#166534"><i class="fa-solid fa-file-check"></i> Current:</strong> <?= e(basename($editing['file_path'])) ?>
                            <br><span style="font-size:11px;opacity:.8">Upload a new file to replace</span>
                            <input type="hidden" name="file_path_existing" value="<?= e($editing['file_path']) ?>">
                        </div>
                    <?php endif; ?>
                </div>

                <div id="form_url_input" class="form-row" style="display:<?= !empty($editing) && strpos($editing['file_path'] ?? '', 'http') === 0 ? 'block' : 'none' ?>">
                    <label><i class="fa-solid fa-globe" style="color:#2563eb;font-size:13px"></i> External URL <span style="color:#dc2626">*</span></label>
                    <input type="url" name="form_url"
                           placeholder="https://example.com/form.pdf"
                           value="<?= (strpos($editing['file_path'] ?? '', 'http') === 0) ? e($editing['file_path']) : '' ?>"
                           data-testid="cms-input-url">
                </div>

                <div class="form-row">
                    <label><i class="fa-solid fa-icons" style="color:#6366f1;font-size:13px"></i> Icon (Font Awesome)</label>
                    <input type="text" name="icon"
                           placeholder="e.g., fa-file-pdf / fa-file-word / fa-link"
                           value="<?= e($editing['icon'] ?? 'fa-file-pdf') ?>"
                           data-testid="cms-input-icon">
                </div>
            <?php endif; ?>

            <div class="form-row" style="display:flex;gap:12px;align-items:flex-end">
                <div style="flex:1">
                    <label><i class="fa-solid fa-arrow-down-1-9" style="color:#6366f1;font-size:13px"></i> Sort Order</label>
                    <input type="number" name="sort_order" min="0" step="1"
                           value="<?= (int)($editing['sort_order'] ?? 10) ?>"
                           data-testid="cms-input-sort" style="width:100%">
                    <p style="font-size:11px;color:#64748b;margin:4px 0 0"><i class="fa-solid fa-circle-info"></i> Lower numbers appear first</p>
                </div>
            </div>

            <div class="form-row" style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:linear-gradient(135deg,rgba(59,130,246,.06),rgba(34,197,94,.06));border:1.5px solid rgba(59,130,246,.15);border-radius:10px">
                <input type="checkbox" name="is_active" id="cms_active" style="width:20px;height:20px"
                       <?= !$editing || !empty($editing['is_active']) ? 'checked' : '' ?>
                       data-testid="cms-input-active">
                <label for="cms_active" style="margin:0;cursor:pointer;font-weight:500"><i class="fa-solid fa-eye" style="color:#2563eb"></i> Visible on Dashboard</label>
            </div>

            <div style="display:flex;gap:10px;margin-top:18px;padding-top:14px;border-top:1px solid rgba(59,130,246,.15)">
                <button class="btn" type="submit" data-testid="cms-submit" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);color:white;box-shadow:0 6px 20px rgba(37,99,235,.3);flex:1;font-weight:600;border:none;padding:11px 16px;border-radius:10px;transition:all .3s ease">
                    <i class="fa-solid fa-save"></i> <?= $editing ? 'Update Item' : 'Create Item' ?>
                </button>
                <?php if ($editing): ?>
                    <a class="btn btn-secondary" id="cms-cancel-link"
                       href="Admin_Home.php?AdminTab=ContentManager&section=<?= $section ?>"
                       style="background:linear-gradient(135deg,#f1f5f9,#e2e8f0);color:#0f172a;flex-shrink:0;border:1px solid #cbd5e1;font-weight:600;padding:11px 16px;border-radius:10px;transition:all .3s ease">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
(function(){
    var openBtn = document.getElementById('cms-open-panel');
    var closeBtn = document.getElementById('cms-close-panel');
    var panel = document.getElementById('cms-panel');
    var backdrop = document.getElementById('cms-backdrop');
    function showPanel(){
        if (!panel || !backdrop) return;
        panel.classList.add('open');
        backdrop.classList.add('visible');
    }
    function hidePanel(){
        if (!panel || !backdrop) return;
        panel.classList.remove('open');
        backdrop.classList.remove('visible');
    }
    var panelBadge = document.getElementById('cms-panel-badge');
    var panelTitle = document.getElementById('cms-panel-title');
    var panelSubtitle = document.getElementById('cms-panel-subtitle');
    var hiddenId = document.getElementById('cms-hidden-id');
    var cancelLink = document.getElementById('cms-cancel-link');
    function resetPanelToAdd(){
        if (panelBadge) panelBadge.textContent = 'Create new';
        if (panelTitle) panelTitle.textContent = 'Add a new content item';
        if (panelSubtitle) panelSubtitle.textContent = 'Fill the form below and save to publish instantly.';
        if (hiddenId) hiddenId.value = '0';
        if (cancelLink) cancelLink.style.display = 'none';
        var titleInput = document.querySelector('input[name="title"]');
        if (titleInput) titleInput.value = '';
        var linkInput = document.querySelector('input[name="link"]');
        if (linkInput) linkInput.value = '';
        var bodyInput = document.querySelector('textarea[name="body"]');
        if (bodyInput) bodyInput.value = '';
        var iconInput = document.querySelector('input[name="icon"]');
        if (iconInput) iconInput.value = iconInput.getAttribute('placeholder') || '';
        var formUrlInput = document.querySelector('input[name="form_url"]');
        if (formUrlInput) formUrlInput.value = '';
        var fileInputField = document.querySelector('input[name="form_file"]');
        if (fileInputField) fileInputField.value = '';
        var isNewCheckbox = document.querySelector('input[name="is_new"]');
        if (isNewCheckbox) isNewCheckbox.checked = false;
        var activeCheckbox = document.querySelector('input[name="is_active"]');
        if (activeCheckbox) activeCheckbox.checked = true;
        var sortOrderInput = document.querySelector('input[name="sort_order"]');
        if (sortOrderInput) sortOrderInput.value = '10';
        var filePathExisting = document.querySelector('input[name="file_path_existing"]');
        if (filePathExisting) filePathExisting.value = '';
        var submitButton = document.querySelector('[data-testid="cms-submit"]');
        if (submitButton) submitButton.innerHTML = '<i class="fa-solid fa-save"></i> Add Item';
        if (fileRadio) fileRadio.checked = true;
        if (urlRadio) urlRadio.checked = false;
        if (window.history && window.history.replaceState) {
            var newUrl = new URL(window.location.href);
            newUrl.searchParams.delete('edit');
            window.history.replaceState({}, document.title, newUrl.pathname + newUrl.search);
        }
        toggleFormType();
    }
    if (openBtn) {
        openBtn.addEventListener('click', function(){
            var currentUrl = new URL(window.location.href);
            if (currentUrl.searchParams.has('edit')) {
                currentUrl.searchParams.delete('edit');
                currentUrl.hash = 'openAdd';
                window.location.href = currentUrl.toString();
                return;
            }
            resetPanelToAdd();
            showPanel();
        });
    }
    if (closeBtn) {
        closeBtn.addEventListener('click', function(){
            hidePanel();
        });
    }
    if (backdrop) {
        backdrop.addEventListener('click', function(){
            hidePanel();
        });
    }
    if (window.location.hash === '#openAdd') {
        resetPanelToAdd();
        showPanel();
        if (window.history && window.history.replaceState) {
            var cleanUrl = new URL(window.location.href);
            cleanUrl.hash = '';
            window.history.replaceState({}, document.title, cleanUrl.toString());
        }
    }
    var fileRadio = document.querySelector('input[name="form_type"][value="file"]');
    var urlRadio  = document.querySelector('input[name="form_type"][value="url"]');
    var formFileInput = document.getElementById('form_file_input');
    var formUrlInput  = document.getElementById('form_url_input');
    var newsFileInput = document.getElementById('news_file_input');
    var newsUrlInput  = document.getElementById('news_url_input');
    function toggleFormType(){
        if (!fileRadio || !urlRadio) return;
        var showUrl = urlRadio.checked;
        if (formFileInput && formUrlInput) {
            formFileInput.style.display = showUrl ? 'none' : 'block';
            formUrlInput.style.display = showUrl ? 'block' : 'none';
        }
        if (newsFileInput && newsUrlInput) {
            newsFileInput.style.display = showUrl ? 'none' : 'block';
            newsUrlInput.style.display = showUrl ? 'block' : 'none';
        }
    }
    if (fileRadio) fileRadio.addEventListener('change', toggleFormType);
    if (urlRadio) urlRadio.addEventListener('change', toggleFormType);
    toggleFormType();
})();
</script>

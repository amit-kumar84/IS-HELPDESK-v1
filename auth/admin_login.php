<?php
/**
 * Legacy admin login — REMOVED.
 *
 * The "Administrator" role has been replaced by **ISKot Admin** (which
 * authenticates against the `iskotadmin_login` table). Any direct hits
 * on this file are silently forwarded to the new ISKot Admin login page.
 */
header('Location: ../index.php?login_as=ISKotAdmin');
exit;

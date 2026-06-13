# Test Credentials — IS-HELPDESK

> Seeded accounts for end-to-end testing in the preview container.

| Role            | Login URL                                | Login ID  | Password    | Notes                                              |
| --------------- | ---------------------------------------- | --------- | ----------- | -------------------------------------------------- |
| **Super Admin** | `index.php?login_as=ISKotAdmin`          | `iskot`   | `bel@123`   | The single Super Admin — only one who sees the **Manage Admins** tab. Can add / update / change passwords / remove other admins. |
| ISKot Admin     | `index.php?login_as=ISKotAdmin`          | `amit`    | `amit@123`  | Sample regular admin created via the Manage Admins UI for testing. |
| Employee        | `index.php?login_as=User`                | `206990`  | `test123`   | Real staff row (SARITA SETHI); password set to MD5('test123') for testing. |
| Engineer        | `index.php?login_as=Engineer`            | `620151`  | `test123`   | Real engineer row (PRADEEP RANA); password set to MD5('test123') for testing. |

> ⚠️ The legacy **Administrator** (admin_login) flow has been **removed** from
> the landing page. The admin-side dashboard is now exclusively driven by the
> `ISKot Admin` role and the `iskotadmin_login` table. Within that role, the
> only account flagged as **Super Admin** is the hard-wired `iskot` user.

## Database

- Engine: MariaDB 10.11 (XAMPP)
- Host: `127.0.0.1`
- DB:   `hardware_master`
- User: `root` (XAMPP default) — or `app` / `apppass` in the preview container.
- Pass: *(empty — default XAMPP)*

## SQL to re-seed test passwords after re-importing `hardware_master.sql`

```sql
-- Super Admin
UPDATE iskotadmin_login SET adminpass = MD5('bel@123') WHERE adminid = 'iskot';

-- Sample regular admin (only useful if you want a pre-created non-super admin)
INSERT IGNORE INTO iskotadmin_login (adminName, adminid, adminpass)
VALUES ('AMIT KUMAR', 'amit', MD5('amit@123'));

-- Sample Employee / Engineer
UPDATE emp_details   SET staffpass = MD5('test123') WHERE staffid = '206990';
UPDATE s_engg_login  SET enggpass  = MD5('test123') WHERE enggid  = '620151';
```

## Notes for the next agent

- Real production ISKot Admin rows (e.g. `208006`, `652373`, `209488`,
  `210319`) keep their pre-existing passwords — only the `iskot` row is
  forcibly reset to `bel@123` so the Super Admin tools are accessible
  out-of-the-box.
- The **Super Admin** check is hard-wired on `adminid === 'iskot'` (see
  `SUPER_ADMIN_ID()` and `is_super_admin()` in `includes/auth.php`). To
  designate a different account as Super Admin, change the return value
  of `SUPER_ADMIN_ID()`.
- Migrating all auth from MD5 to `password_hash()` / bcrypt remains in
  the backlog (P1).

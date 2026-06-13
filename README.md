# IS-HELPDESK · BEL Kotdwar IT Service Desk

A role-based PHP / MySQL helpdesk portal for the IT support team at BEL
Kotdwar (Bharat Electronics Limited – Government of India, Ministry of
Defence PSU).

**Developer / Author:** Amit Kumar
**License:** MIT (see `LICENSE`)
**Copyright:** © 2026 Amit Kumar — All Rights Reserved
**Stack:** PHP 8.x, MySQL / MariaDB, vanilla CSS + Font Awesome (XAMPP friendly)

---

## 1. Folder structure

```
IS-HELPDESK/
│
├── index.php                  Landing + login dispatcher (role chooser)
├── Admin_Home.php             Admin dashboard router (?AdminTab=…)
├── Engineer_home.php          Engineer dashboard router (?EngineerTab=…)
├── home.php                   User dashboard router (?UserTab=…)
├── logout.php                 Logout / session destroy
├── connection.php             MySQL connection (env-driven, XAMPP defaults)
├── sanitizeReq.php            Tiny input sanitiser
├── hardware_master.sql        Full DB schema + seed data
├── LICENSE                    MIT license
├── favicon.ico
│
├── auth/                      ──  Login forms (included by index.php)
│   ├── admin_login.php
│   ├── engineer_login.php
│   ├── user_login.php
│   ├── Appren_login.php
│   └── ISKotAdminLogin.php
│
├── admin/                     ──  Administrator-only feature pages
│   ├── admin_dashboard.php
│   ├── AddNEwUser.php, ManageUsers.php, EditUser.php, BulkImportUsers.php …
│   ├── Add_Engineer.php, Engineer_List.php, Edit_Engineer_Details.php …
│   ├── PrinterCartridgeNewEntry.php, AddCartridgeStock.php …
│   ├── NewHardwareEntry.php, ChangeHardwareDetails.php …
│   └── AdminChangePassword.php, Suggestions.php, …
│
├── engineer/                  ──  Engineer-only feature pages
│   ├── engineer_dashboard.php
│   ├── PendingCalls.php, AttendCalls.php, SolvedCalls.php, ClosedCalls.php
│   ├── Presence_Engineer.php, verifiedAssetList.php
│   ├── user_contact_update.php
│   └── E_ChangePassword.php
│
├── user/                      ──  Employee (user) feature pages
│   ├── userdashboard.php
│   ├── RequestForm.php, cartridgeForm.php, Feedback.php
│   ├── Enquiry.php, CartridgeUserEnquiry.php
│   ├── ConfirmRequest.php, NDAagreement.php
│   ├── ChangePassword.php, User_Password_Change.php
│
├── shared/                    ──  Pages used by multiple roles
│   ├── AllCalls.php, CallReport.php, ReportCalls.php, ViewCalls.php
│   ├── Hardware_Details.php, PC_Printer_Details.php …
│   ├── CallGenerate.php, CartridgeRequest.php, CartridgeRequestGenerate.php
│   ├── CartridgeStock.php, cartridgeIssue.php, cartridgeWiseIssueList.php
│   ├── HardwareIssueToUser.php, addToMS.php, …
│   ├── SearchEmployee.php, asset_wise_report.php, user_wise_report.php
│   └── footer.php             (legacy footer kept for older static pages)
│
├── includes/                  ──  Shared layout & helpers (auto-included)
│   ├── auth.php                  session, role guard, escape helpers
│   ├── header.php                <head> + sidebar opener
│   ├── topbar.php                page heading + crumbs + clock
│   ├── footer_app.php            closing layout + global footer (Amit Kumar)
│   ├── gov_header.php            GoI / BEL banner with live clock
│   ├── sidebar_admin.php
│   ├── sidebar_engineer.php
│   ├── sidebar_user.php
│   ├── change_password_form.php
│   ├── info_widgets.php
│   ├── ticket_action.php
│   └── photo.php
│
├── assets/css/app.css         New modern stylesheet (used by all dashboards)
├── css/style.css              Legacy stylesheet (used only by older pages)
├── images/                    Logos and static assets
└── Pictures/                  User profile pictures (uploadable)
```

## 2. Quick start (XAMPP / local PHP)

1. Copy the project into `htdocs/IS-HELPDESK/`.
2. Start Apache + MySQL from the XAMPP control panel.
3. In phpMyAdmin, create a database called `hardware_master` and import
   `hardware_master.sql`.
4. (Optional) Set environment variables to override DB defaults:
   `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`.
5. Browse to `http://localhost/IS-HELPDESK/`.

## 3. Default test credentials

| Role          | ID            | Password   |
| ------------- | ------------- | ---------- |
| Administrator | `Admin`       | `Admin@123` |
| Employee      | `206990`      | `test123`  |
| Engineer      | `620151`      | `test123`  |

(The seed `hardware_master.sql` ships real staff/engineer rows; the two
`test123` rows above are set manually for development convenience.)

## 4. Adding a new feature page

* Drop the file into the matching folder (`admin/`, `engineer/`, `user/`
  or `shared/`).
* Add a new `case` entry to the appropriate router
  (`Admin_Home.php`, `Engineer_home.php` or `home.php`) pointing at the
  new path, e.g. `case 'MyNewPage': include 'admin/MyNewPage.php'; break;`
* Add a link in the corresponding sidebar
  (`includes/sidebar_*.php`).

## 5. Credits

```
Developed by  :  Amit Kumar
GitHub        :  https://github.com/amit-kumar84/IS-HELPDESK
Copyright     :  © 2026 Amit Kumar
Licence       :  MIT
```

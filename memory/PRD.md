# PRD — IS-HELPDESK · BEL Kotdwar IT Service Desk

## Original Problem Statement
> fix this app ui not showing propurly and remove unasssry filess and foleds
> make weell structurs folder for saprate code like admin , user, enginer,
> css, etc and give copright / devloper/ licenss name is - Amit Kumar
>
> Repo: https://github.com/amit-kumar84/IS-HELPDESK
> Stack: PHP + XAMPP + MySQL

## Architecture
- **Frontend / Backend**: PHP 8.x (server-rendered pages)
- **Database**: MySQL / MariaDB (DB name: `hardware_master`)
- **Web server**: built-in `php -S` on port 3000 (supervisor-managed) for the
  preview container; XAMPP/Apache on user's local machine.
- **Layout**: shared `includes/` (header, sidebar per role, topbar, footer)
- **Styling**: `assets/css/app.css` (new) + `css/style.css` (legacy)

## User personas
1. **Employee** (Staff #) — registers complaints, requests cartridges,
   views own assets and ticket status.
2. **Engineer** — attends tickets, manages hardware, updates status,
   verifies assets.
3. **Administrator** — manages users, engineers, assets, cartridges,
   reads suggestions, sees system-wide reports.

## Core requirements (static)
- Role-based login (Employee / Engineer / Administrator).
- Ticket lifecycle: Pending → Attend → Solved → Closed.
- Cartridge request → approval → issue tracking.
- Hardware inventory (PCs, laptops, VDIs, printers, CCTV).
- Reports: asset-wise, user-wise, section-wise, date-wise.
- News / Notice / Forms widget, Suggestion box.

## What's been implemented (this session — 11 Jun 2026)
- **Sidebar hide/show toggle (Admin + Engineer + User)** — every dashboard
  topbar now has a dark `≡` button. On desktop (>900 px) it collapses /
  restores the sidebar with a smooth transition, on mobile it slides the
  drawer in / out. State is persisted in `localStorage`
  (`iskot_sidebar = collapsed|expanded`) so the choice survives reloads.
- **Admin Content Manager (P2 from previous backlog)** — new page at
  `Admin_Home.php?AdminTab=ContentManager` with three CRUD tabs:
    * `Latest News` (news_items)  · title + link + NEW flag + sort + active
    * `Notice Board` (notice_board) · title + body + FA icon + sort + active
    * `Forms Download` (form_downloads) · title + file path + FA icon + sort + active
  Each tab supports Add / Edit / Delete and one-click Visibility toggle.
  Linked from the sidebar under a new "Content Manager" group with three
  sub-items. All changes appear instantly on every user / engineer / admin
  dashboard widget.
- **Database brought up**: imported `hardware_master.sql` into MariaDB,
  created missing tables (`suggestions`, `news_items`, `form_downloads`,
  `notice_board`) and seeded sample data so the dashboards render.
- **UI fix**: "all pages not showing properly" was due to missing DB +
  missing tables → fixed; landing, admin, engineer, user dashboards now
  render end-to-end (verified via curl + screenshots).
- **Folder restructure** (the main ask):
  - `auth/` — login forms
  - `admin/` — administrator-only pages (~35 files)
  - `engineer/` — engineer-only pages (~9 files)
  - `user/` — employee-only pages (~10 files)
  - `shared/` — pages used by multiple roles (~35 files)
  - `includes/` — layout & helpers (unchanged)
  - `assets/css/`, `css/`, `images/`, `Pictures/` — static assets
  - Updated 96 file moves + all `include`, `require`, `href`, `action`,
    `Location:` references via an automated script (`/tmp/restructure.py`).
- **Cleanup**: deleted 23 unused folders (de/, es/, fr/, hu/, it/, jp/,
  pl/, pt_br/, ro/, ru/, tr/, ur/, zh_cn/, zh_tw/, attsystem/,
  punch_system/, iskotMedicalServices/, Download/, update/, docs/,
  stylesheets/, javascripts/, video/) and 9 legacy files (404.html,
  bdayDisplayOLD.php, survey_questions2.php, index.html, faq.html,
  howto*.html, web.config). Repo size dropped sharply.
- **Copyright / Developer / License = Amit Kumar**:
  - `includes/footer_app.php` (footer on every dashboard page)
  - `index.php` landing-page footer
  - `shared/footer.php` (legacy footer)
  - `<meta name="author">`, `<meta name="copyright">` added in
    `includes/header.php` and `index.php`
  - New `LICENSE` (MIT, © 2026 Amit Kumar)
  - New `README.md` documenting structure + credits
- **Smoke tested** all 3 role logins and 18+ sub-tabs — every page
  returns HTTP 200 with zero PHP errors / warnings.

## Backlog (P1 / P2)
- P1 — password hashing migration from MD5 to `password_hash()` / bcrypt.
- P1 — CSRF tokens on every POST form.
- P2 — separate ticket priority levels + SLA timers.
- P2 — email/SMS notification on ticket events.
- P2 — REST API layer (so a future React/mobile front-end can attach).
- P2 — admin panel to manage news/notice/form rows (currently SQL only).

## 2026-Jan · Offline assets · Topbar enhancement · Tirangawave banner

### Fully offline (intranet-ready)
- Font Awesome 6.5.1 free downloaded to `assets/fa/all.min.css` +
  `assets/fa/webfonts/` (1.1 MB total) — all icons render with **no
  internet** required.
- Inter (400/500/600/700/800), Sora (400/600/700/800) and JetBrains
  Mono (500/700) TTFs downloaded to `assets/fonts/` (2 MB total) and
  declared via a single `assets/fa/fonts.css` `@font-face` bundle.
- Every page (`index.php`, `includes/header.php`, `live_board.php`)
  now references **only local paths** — `cdnjs.cloudflare.com`,
  `fonts.googleapis.com` and `fonts.gstatic.com` references have
  been deleted from the codebase.
- Verified by reloading the dashboard / landing page / live board /
  manage admins page with **all external requests blocked**: every
  icon and font still renders.

### Tirangawave background on the floating banner
- The floating banner's tricolour now uses the **same `tirangaWave`
  animation** as the BEL top header — a soft saffron→white→green
  drift over a deep-navy base, plus a second slower counter-drift
  layer for depth. Result is coherent with the rest of the app and
  far more readable than the previous solid stripes.

### Enhanced topbar
- `.app-topbar` background is now a white base with a continuous
  `tirangaWave` overlay (matching the banner / BEL header) and a
  crisp 3-px saffron-white-green strip pinned to the top edge.
- Page title gets a 4-px vertical tiranga "spine" before the text,
  bold 800-weight Inter for stronger PSU branding.
- Clock pill upgraded to a saffron/green dual-tinted chip with a
  subtle inset highlight; menu-toggle now lifts on hover.

### 2026-Jan · Super Admin · Manage Admins · Tiranga Banner

### Super Admin & Manage Admins (latest)
- The single account `iskot` (default password `bel@123`) is the **Super
  Admin** — hard-wired via `SUPER_ADMIN_ID()` and `is_super_admin()`
  helpers in `includes/auth.php`.
- New **"Manage Admins"** page (`admin/ManageAdmins.php`) — visible in
  the sidebar **only to the Super Admin** and protected by
  `require_super_admin()` on direct URL access. Other admins see a
  branded "Restricted" screen.
- The Super Admin can **add** a new ISKot Admin, **rename** an
  existing admin, **reset** any admin's password, and **remove**
  any admin — except the Super Admin itself (which is "Protected").
- Sidebar shows a golden **SUPER ADMIN · iskot** band when the Super
  Admin is signed in. Topbar role-label flips from "ISKot
  Administrator" to "Super Admin".

### Sub-Admin login REMOVED from landing page
- The landing page (`index.php`) now exposes exactly three role tabs —
  **Employee / Engineer / ISKot Admin** — no more "Administrator"
  selector. `?login_as=SubAdmin` / `?login_as=Admin` are folded into
  `?login_as=ISKotAdmin` for backwards compat.

### Tiranga (Indian Tricolour) animated banner background
- The floating Live Banner now uses a real **saffron / white / green**
  vertical tricolour for its base, overlaid with a deep-navy darkening
  layer (so the stat cards stay readable), a horizontal patriotic
  shimmer that sweeps every 12 s, and a slowly rotating Ashok-Chakra
  decorative ring on the right edge. Pure CSS — no extra assets.

### ISKot Admin migration (replaces the legacy "Admin" login)
- Admin-side dashboard authenticates against `iskotadmin_login`.
- `auth/admin_login.php` is a redirect stub → `index.php?login_as=ISKotAdmin`.
- `auth/ISKotAdminLogin.php` uses the modern login form UI.
- `Admin_Home.php` role-gates to `ISKotAdmin` (legacy `Admin` session
  still accepted) and reads both `AdminTab` / `ISKotAdminTab` params.
- `ISKotAdminHome.php` at root is a one-liner alias of `Admin_Home.php`.
- `flash_set()` / `flash_get()` helpers live in `includes/auth.php`.

### All legacy ISKotAdminHome tabs inherited
Sidebar now contains every drawer item from the old `ISKotAdminHome`
plus everything the modern dashboard already had: Tickets (Section /
Date / Change / Remove), Cartridges (Edit / Update Stock / Remove
Req.), Hardware (Total Asset / Verified / Storage / CCTV / ISKOT /
Write-off / Standby / Software / Internet / OS / Asset No / Update
No / Remove / Write-off / RFID), User Detail (Update Details /
Contact / Reset Password / Remove), Engineers, Communications,
Others (Sanitization, Backup Timing).

### Live Board (pop-out) + Auto-refresh banner
- `live_board.php` — standalone resizable pop-out window with a live
  ticking clock + auto-refresh every 60 s + animated star-engineer
  ID card. Toolbar: full / strip / dock / theme / close. Strip mode
  is the single-row marquee for big-screen strips.
- `live_board_data.php` — auth-gated JSON endpoint polled every 60 s.
- Pop-out launcher added to the sidebar with a `NEW` badge.

## Next actions for the user
1. Pull the latest code into your local XAMPP `htdocs/IS-HELPDESK/`.
2. Re-import `hardware_master.sql` (only if you want the new tables).
3. **Reset the Super Admin password** so you can log in:
   ```sql
   UPDATE iskotadmin_login SET adminpass = MD5('bel@123') WHERE adminid = 'iskot';
   ```
4. Browse to `http://localhost/IS-HELPDESK/` → click **ISKot Admin**
   → log in with `iskot / bel@123`.
5. You'll see the golden **SUPER ADMIN: iskot** band and a
   **"Manage Admins"** link in the sidebar. Use it to add / rename /
   reset-password / remove other admins.
6. Sign in as any other admin — the Manage Admins link is hidden and
   direct URL access shows a branded "Restricted" screen.
7. From the sidebar **"Open Live Board"** → resizable pop-out window
   opens. Numbers auto-refresh every 60 s. Banner uses the new
   animated **tiranga** background.


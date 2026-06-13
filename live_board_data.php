<?php
/**
 * Live Board JSON endpoint.
 *
 * Returns ALL enabled banner cards + the current star engineer as JSON,
 * so the live board / floating banner can auto-refresh its numbers
 * every 60 s via AJAX without a full page reload.
 *
 *  GET /live_board_data.php
 *  → {
 *      "ts": "2026-01-30 12:43:08",
 *      "date": "Friday, 30 Jan 2026",
 *      "cards": [
 *         {"key":"total_calls","label":"Total Tickets",...,"today":4,"total":15321,"icon":"fa-ticket","color_from":"#1e3a8a","color_to":"#3b82f6"},
 *         ...
 *      ],
 *      "star": {"name":"...", "enggid":"...", "company":"...", "support_field":"...", "joining_date":"...", "photo":"Pictures/620149.jpg", "count":7, "scope":"today"}
 *   }
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/connection.php';

// Optional: require any authenticated role so non-logged-in tab can't poll.
if (!logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'auth_required']);
    exit;
}

$today = date('d-m-Y');

$STATS = [
    'total_calls'      => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM complain_register"),
        'today' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE r_DateTime LIKE '$today%'"),
    ],
    'total_unassigned' => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status IN ('Pending','Attend')"),
        'today' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Pending'"),
        'subtext' => 'Unassigned · Open',
    ],
    'total_attend'     => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Attend'"),
        'today' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Attend' AND s_DateTime LIKE '$today%'"),
    ],
    'total_solved'     => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Solved'"),
        'today' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Solved' AND s_DateTime LIKE '$today%'"),
    ],
    'total_closed'     => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Closed'"),
        'today' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Closed' AND s_DateTime LIKE '$today%'"),
    ],
    'today_incoming'   => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE r_DateTime LIKE '$today%'"),
        'today' => null,
    ],
    'today_attend'     => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Attend' AND s_DateTime LIKE '$today%'"),
        'today' => null,
    ],
    'today_solved'     => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Solved' AND s_DateTime LIKE '$today%'"),
        'today' => null,
    ],
    'today_closed'     => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM complain_register WHERE status='Closed' AND s_DateTime LIKE '$today%'"),
        'today' => null,
    ],
    'active_engineers' => [
        'total' => safe_count($link, "SELECT COUNT(*) FROM s_engg_login"),
        'today' => safe_count($link, "SELECT COUNT(*) FROM s_engg_login WHERE status='0' AND presence='P'"),
        'today_label' => 'Today Active',
        'total_label' => 'Total Engineers',
        'subtext' => 'Active / Inactive',
    ],
];

// Pull enabled cards (and label / colour / icon from admin settings)
$cards = [];
$cq = @mysqli_query($link, "SELECT * FROM banner_settings WHERE is_enabled=1 ORDER BY sort_order ASC");
if ($cq) {
    while ($r = mysqli_fetch_assoc($cq)) {
        if ($r['setting_key'] === 'star_engineer') continue; // star rendered separately
        $stat = $STATS[$r['setting_key']] ?? null;
        if (!$stat) continue;
        $cards[] = [
            'key'        => $r['setting_key'],
            'label'      => $r['label'],
            'icon'       => $r['icon'],
            'color_from' => $r['color_from'],
            'color_to'   => $r['color_to'],
            'total'      => (int)$stat['total'],
            'today'      => $stat['today'] === null ? null : (int)$stat['today'],
            'subtext'    => $stat['subtext'] ?? null,
        ];
    }
}

// Is the star engineer card enabled?
$starEnabled = (bool) safe_count($link, "SELECT COUNT(*) FROM banner_settings WHERE setting_key='star_engineer' AND is_enabled=1");

$star = null;
if ($starEnabled) {
    $starQ = @mysqli_query($link,
        "SELECT support_engg AS name, COUNT(*) AS c
         FROM complain_register
         WHERE status IN ('Solved','Closed')
           AND DATE(s_DateTime) = CURDATE()
           AND support_engg <> ''
         GROUP BY support_engg
         ORDER BY c DESC LIMIT 1");
    $scope = 'today';
    if (!($starQ && mysqli_num_rows($starQ) > 0)) {
        $starQ = @mysqli_query($link,
            "SELECT support_engg AS name, COUNT(*) AS c
             FROM complain_register
             WHERE status IN ('Solved','Closed') AND support_engg <> ''
             GROUP BY support_engg
             ORDER BY c DESC LIMIT 1");
        $scope = 'all-time';
    }
    if ($starQ && ($row = mysqli_fetch_assoc($starQ))) {
        // Pull engineer detail (id, company, field, joining)
        $detail = ['enggid'=>'', 'company'=>'', 'support_field'=>'', 'joining_date'=>'', 'presence'=>''];
        $eq = @mysqli_query($link, "SELECT enggid, company, support_field, joining_date, presence
                                    FROM s_engg_login
                                    WHERE engg_name='" . mysqli_real_escape_string($link, $row['name']) . "'
                                    LIMIT 1");
        if ($eq && ($er = mysqli_fetch_assoc($eq))) $detail = $er;

        $photo = '';
        foreach ([
            'images/engineers/' . $detail['enggid'] . '.JPG',
            'images/engineers/' . $detail['enggid'] . '.jpg',
            'images/engineers/' . $detail['enggid'] . '.png',
            'Pictures/' . $detail['enggid'] . '.JPG',
            'Pictures/' . $detail['enggid'] . '.jpg',
            'Pictures/' . $detail['enggid'] . '.png',
        ] as $cand) {
            if (file_exists($cand)) { $photo = $cand; break; }
        }

        $star = [
            'name'          => $row['name'],
            'count'         => (int)$row['c'],
            'scope'         => $scope,
            'enggid'        => (string)$detail['enggid'],
            'company'       => $detail['company'],
            'support_field' => $detail['support_field'],
            'joining_date'  => $detail['joining_date'],
            'presence'      => $detail['presence'],
            'photo'         => $photo,
        ];
    }
}

echo json_encode([
    'ts'     => date('Y-m-d H:i:s'),
    'date'   => date('l, d M Y'),
    'cards'  => $cards,
    'star'   => $star,
]);

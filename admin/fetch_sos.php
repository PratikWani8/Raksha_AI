<?php
include("../config/db.php");

if (!isset($_SESSION['admin'])) {
    exit;
}

/* Fetch SOS data */

$query = "

SELECT 
    s.sos_id,
    u.name,
    u.phone,
    s.location,
    s.message,
    s.sent_at,
    'Registered' AS type

FROM emergency_sos s
JOIN users u ON s.user_id = u.user_id


UNION ALL


SELECT 
    n.sos_id,
    NULL AS name,
    NULL AS phone,
    n.location,
    n.message,
    n.sent_at,
    'Guest' AS type

FROM non_reg_sos n


ORDER BY sent_at DESC
";

$res = $conn->query($query);

$data = [];

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
}

header("Content-Type: application/json");
echo json_encode($data);
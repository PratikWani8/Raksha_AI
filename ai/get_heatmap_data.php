<?php
include("../config/db.php");

$result = $conn->query("
SELECT latitude as lat, longitude as lng, risk_score as score, incident_count as count
FROM heatmap_data
");

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);
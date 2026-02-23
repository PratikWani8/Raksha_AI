<?php
include("../config/db.php");
$conn->set_charset("utf8mb4");
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch SOS locations
$query = "
SELECT location FROM emergency_sos WHERE location IS NOT NULL
UNION ALL
SELECT location FROM non_reg_sos WHERE location IS NOT NULL
";

$result = $conn->query($query);

$locations = [];

while ($row = $result->fetch_assoc()) {

    if (!empty($row['location']) && strpos($row['location'], ',') !== false) {

        list($lat, $lng) = explode(',', $row['location']);

        $lat = round(floatval($lat), 3);
        $lng = round(floatval($lng), 3);

        $key = $lat . "," . $lng;

        if (!isset($locations[$key])) {
            $locations[$key] = [
                "lat" => $lat,
                "lng" => $lng,
                "count" => 0
            ];
        }

        $locations[$key]['count']++;
    }
}

// Create dataset
$data = [];

foreach ($locations as $loc) {

    $risk = min(1, $loc['count'] / 5);

    $data[] = [
        "lat" => $loc['lat'],
        "lng" => $loc['lng'],
        "score" => $risk,
        "count" => $loc['count']
    ];
}

// Save heatmap data
$user_id = $_SESSION['user_id'];

foreach ($data as $row) {

    $stmt = $conn->prepare("
        INSERT INTO heatmap_data (user_id, latitude, longitude, risk_score, incident_count)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            incident_count = VALUES(incident_count),
            risk_score = LEAST(1, VALUES(incident_count) / 5)
    ");

    if ($stmt) {
        $stmt->bind_param("iddii",
            $user_id,
            $row['lat'],
            $row['lng'],
            $row['score'],
            $row['count']
        );
        $stmt->execute();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>AI Powered Heatmap</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css"/>
<link rel="icon" href="../assets/favicon.jpg" type="image/x-icon" />
<link rel="stylesheet" href="../style.css">
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
<style> 
#map { 
    height: 500px;
    margin: 20px; 
    border-radius: 12px; 
    } 

#loading {
  text-align: center;
  font-size: 18px;
  padding: 10px;
}
</style>
</head>

<body>

<header>
 <div class="header-container">
  <h2> AI Powered Heatmap</h2> 
</div> 
</header>

<div class="card">
<p>Real-time AI danger prediction using SOS data</p>
</div>

<div id="loading">Loading AI model...</div>
<div id="map"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js"></script>

<script>

// Map Setup
var map = L.map('map').setView([18.5204, 73.8567], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap'
}).addTo(map);

let heatmapData = <?php echo json_encode($data); ?>;

let heatData = heatmapData.map(p => [p.lat, p.lng, p.score]);

L.heatLayer(heatData, {
  radius: 25,
  blur: 20,
  gradient: {
    0.0: 'green',
    0.5: 'yellow',
    1.0: 'red'
  }
}).addTo(map);

function speakWarning(){
  const msg = new SpeechSynthesisUtterance("Warning. Dangerous area detected.");
  msg.lang = "en-US";
  speechSynthesis.speak(msg);
}

function getDistance(lat1, lon1, lat2, lon2) {
  const R = 6371;
  const dLat = (lat2 - lat1) * Math.PI/180;
  const dLon = (lon2 - lon1) * Math.PI/180;

  const a =
    Math.sin(dLat/2)**2 +
    Math.cos(lat1*Math.PI/180) *
    Math.cos(lat2*Math.PI/180) *
    Math.sin(dLon/2)**2;

  return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
}

// TensorFlow AI Model
let model;

async function createModel(){

  const model = tf.sequential();

  model.add(tf.layers.dense({
    units: 16,
    inputShape: [3],
    activation: 'relu'
  }));

  model.add(tf.layers.dense({ units: 8, activation: 'relu' }));

  model.add(tf.layers.dense({
    units: 1,
    activation: 'sigmoid'
  }));

  model.compile({
    optimizer: 'adam',
    loss: 'meanSquaredError'
  });

  return model;
}

// Train Model
async function trainModel(model){

  const inputs = heatmapData.map(p => [p.lat, p.lng, p.count]);
  const labels = heatmapData.map(p => p.score);

  const xs = tf.tensor2d(inputs);
  const ys = tf.tensor2d(labels, [labels.length, 1]);

  await model.fit(xs, ys, {
    epochs: 40,
    batchSize: 8,
    shuffle: true
  });

  console.log("AI trained");
}

// Predict Risk
function predictRisk(lat, lng, count){

  if(!model) return 0;

  const input = tf.tensor2d([[lat, lng, count]]);
  const pred = model.predict(input);
  return pred.dataSync()[0];
}

// Risk Detection
let lastAlert = 0;

function checkUserRisk(lat, lng){

  let now = Date.now();
  if(now - lastAlert < 30000) return;

  let predicted = predictRisk(lat, lng, 3);

  console.log("Predicted Risk:", predicted);

  if(predicted > 0.6){
    lastAlert = now;
    alert("🚨 AI Warning: High Risk Area");
    document.body.style.background = "#ffe6e6";
    speakWarning();
  } else {
    document.body.style.background = "#f4f6ff";
  }
}

// User Tracking
let userMarker;

if(navigator.geolocation){

  navigator.geolocation.watchPosition(function(pos){

    let lat = pos.coords.latitude;
    let lng = pos.coords.longitude;

    if(userMarker){
      userMarker.setLatLng([lat, lng]);
    } else {
      userMarker = L.marker([lat, lng]).addTo(map)
        .bindPopup("📍 You are here");
    }

    map.setView([lat, lng], 13);

    checkUserRisk(lat, lng);

  },
  function(err){
    console.log(err);
  },
  { enableHighAccuracy: true });

}

// Initialize AI
async function initAI(){

  model = await createModel();
  await trainModel(model);
  document.getElementById("loading").style.display = "none";
}

initAI();

</script>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Safest Route Navigation</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css"/>
<link rel="stylesheet" href="../style.css">
<link rel="icon" href="../assets/favicon.jpg" type="image/x-icon" />
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>

<style>
#map {
  height: 500px;
  margin: 20px;
  border-radius: 12px;
}

.controls {
    width: 50%;
    margin: 20px auto;
}
</style>
</head>

<body>

<header>
<div class="header-container">
<h2>🛣 Safest Route Navigation</h2>
</div>
</header>

<div class="card">
<input type="text" id="start" placeholder="Enter Start Location">
<input type="text" id="end" placeholder="Enter Destination">
<button onclick="findRoute()">Find Route</button>
</div>

<div id="map"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js"></script>

<script>

// ================= MAP =================
var map = L.map('map').setView([18.5204, 73.8567], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution:'© OpenStreetMap'
}).addTo(map);

// ================= DATA =================
let dangerZones = [];

// ================= LOAD DATA =================
fetch("get_heatmap_data.php")
.then(res => res.json())
.then(data => {

  dangerZones = data;

  drawHeatmap();
  drawDangerCircles();
  initAI();

});

// ================= HEATMAP =================
function drawHeatmap(){

  let heatPoints = dangerZones.map(p => [
    parseFloat(p.lat),
    parseFloat(p.lng),
    parseFloat(p.score)
  ]);

  L.heatLayer(heatPoints, {
    radius: 25,
    blur: 20,
    gradient: {
      0.0: 'green',
      0.5: 'yellow',
      1.0: 'red'
    }
  }).addTo(map);
}

// ================= CIRCLES =================
function drawDangerCircles(){

  dangerZones.forEach(p => {

    let color = "green";

    if(p.score > 0.6) color = "red";
    else if(p.score > 0.3) color = "orange";

    L.circle([p.lat, p.lng], {
      radius: 100,
      color: color,
      fillOpacity: 0.3
    }).addTo(map);

  });
}

// ================= AI MODEL =================
let model;

async function initAI(){

  if(dangerZones.length < 5){
    console.log("Not enough data");
    return;
  }

  model = tf.sequential();

  model.add(tf.layers.dense({units:16, inputShape:[3], activation:'relu'}));
  model.add(tf.layers.dense({units:8, activation:'relu'}));
  model.add(tf.layers.dense({units:1, activation:'sigmoid'}));

  model.compile({optimizer:'adam', loss:'meanSquaredError'});

  let inputs = dangerZones.map(p => [
    parseFloat(p.lat),
    parseFloat(p.lng),
    parseFloat(p.count)
  ]);

  let labels = dangerZones.map(p => parseFloat(p.score));

  const xs = tf.tensor2d(inputs, [inputs.length,3]);
  const ys = tf.tensor2d(labels, [labels.length,1]);

  await model.fit(xs, ys, {epochs:30});

  console.log("AI Ready");
}

// ================= PREDICT =================
function predictRisk(lat, lng){

  if(!model) return 0;

  let input = tf.tensor2d([[lat, lng, 3]]);
  let pred = model.predict(input);

  return pred.dataSync()[0];
}

// ================= CHECK SAFE =================
function isRouteSafe(coords){

  for(let i=0;i<coords.length;i+=5){

    let risk = predictRisk(coords[i].lat, coords[i].lng);

    if(risk > 0.6){
      return false;
    }
  }

  return true;
}

// ================= FIND DANGER =================
function findDangerPoint(coords){

  for(let i=0;i<coords.length;i+=5){

    let lat = coords[i].lat;
    let lng = coords[i].lng;

    let risk = predictRisk(lat, lng);

    if(risk > 0.6){
      return {lat, lng};
    }
  }

  return null;
}

// ================= DETOUR =================
function createDetour(point){

  return L.latLng(
    point.lat + 0.005,
    point.lng + 0.005
  );
}

// ================= GEOCODE =================
async function geocode(place){

  let res = await fetch("geocode.php?place=" + encodeURIComponent(place));
  let data = await res.json();

  if(!data || data.length === 0){
    alert("Location not found");
    return null;
  }

  return L.latLng(data[0].lat, data[0].lon);
}

// ================= ROUTING =================
let control;

function getRoute(start, end, isReroute=false, detour=null){

  if(control) map.removeControl(control);

  let waypoints = [start];

  if(detour) waypoints.push(detour);

  waypoints.push(end);

  control = L.Routing.control({
    waypoints: waypoints,
    routeWhileDragging: false,
    show: false
  }).addTo(map);

  control.on('routesfound', function(e){

    let coords = e.routes[0].coordinates;

    let danger = findDangerPoint(coords);

    if(danger && !isReroute){

      alert("⚠ Dangerous route! Finding safer path...");

      let detourPoint = createDetour(danger);

      getRoute(start, end, true, detourPoint);

    } else {

      if(isRouteSafe(coords)){
        alert("✅ Safe Route Found");
        document.body.style.background = "#e8f5e9";
      } else {
        alert("⚠ No fully safe route");
        document.body.style.background = "#ffe6e6";
      }

    }

  });
}

// ================= FIND ROUTE =================
async function findRoute(){

  let start = document.getElementById("start").value;
  let end = document.getElementById("end").value;

  if(!start || !end){
    alert("Enter locations");
    return;
  }

  let s = await geocode(start);
  let e = await geocode(end);

  if(!s || !e) return;

  getRoute(s, e);
}

</script>

</body>
</html>
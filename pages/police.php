<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Nearby Police Stations - Raksha</title>

<!-- META TAGS -->
<meta name="title" content="Raksha - Women Safety & Emergency Protection System">
<meta name="description" content="Raksha is a smart women safety platform for SOS alerts, emergency support, live location sharing, and nearby police assistance. Stay safe, stay empowered.">

<meta name="keywords" content="women safety, SOS alert system, emergency help for women, Raksha safety app, women security platform">

<meta name="author" content="Raksha Team">
<meta name="robots" content="index, follow">

<meta property="og:type" content="website">
<meta property="og:title" content="Raksha - Women Safety & Emergency Protection System">
<meta property="og:description" content="Smart platform for women's safety with instant SOS alerts, live tracking, and police support.">

<meta name="theme-color" content="#e91e63">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="../index.css" />

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
<link rel="icon" href="../assets/favicon.jpg" type="image/x-icon" />

<style>
#map {
  width: 100%;
  height: 400px;
  border-radius: 12px;
  margin-top: 15px;
}
</style>
</head>

<body>

<div class="top-banner">
 <p>🚨 Emergency Helpline: 112 | Women Helpline: 181 | Need Help Urgently?</p>
    <a href="../user/non_reg_sos.php">
    <button>Get Help</button>
    </a>
</div>

<header>
<div class="nav-container">
  <div class="logo">Raksha</div>

  <nav>
    <a href="../index.php">Home</a>
    <a href="safety.php">Safety Tips</a>
    <a href="police.php" class="active">Nearby Police</a>
    <a href="../auth/register.php">User</a>
    <a href="../admin/admin_login.php">Admin</a>
  </nav>

  <a href="../auth/register.php">
    <button class="start-btn">Start Protection</button>
  </a>
</div>
</header>

<section class="hero">

<div class="hero-left">

  <div class="badge">
    🚓 Find Police Stations Near You
  </div>

  <h1>
    Nearby <span>Police Help</span><br>
    Anytime You Need
  </h1>

  <p>
    Allow location access to find nearby police stations
    for quick assistance and safety.
  </p>

  <a href="#map-section">
    <button class="primary-btn" onclick="getLocation()">
      Find Nearby Police ➞
    </button>
  </a>

</div>

<div class="hero-right">
  <img src="../assets/police.png" alt="Police phone illustration">
</div>

</section>

<section class="map-section" id="map-section">

<h2>📍 Police Stations Near You</h2>

<p>Click the button to allow location access.</p>

<button class="loc-btn" onclick="getLocation()">
  Enable Location
</button>

<div id="map"></div>

</section>

 <!-- Footer -->
<footer style="text-align:center; padding:15px; color:#666; background:white; margin-top:30px;">
  © <?php echo date("Y"); ?> Raksha - Women Safety System | Designed for Safety • Security • Empowerment for Women | All Rights Reserved.
</footer>


<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

<script>

let map;
let userLat, userLng;
let routeControl;
let nearestStation = null;

const userIcon = L.icon({

  iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png",

  shadowUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",

  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41]

});


const policeIcon = L.icon({

  iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png",

  shadowUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",

  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41]

});


// GET LOCATION

function getLocation() {

  if (navigator.geolocation) {

    navigator.geolocation.getCurrentPosition(
      showMap,
      showError
    );

  } else {

    alert("Geolocation not supported.");

  }
}


// SHOW MAP

function showMap(position) {

  userLat = position.coords.latitude;
  userLng = position.coords.longitude;

  if (map) {
    map.remove();
  }


  map = L.map("map").setView([userLat, userLng], 14);


  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {

    attribution:
      '&copy; OpenStreetMap contributors',

  }).addTo(map);


  // User Marker (GREEN)
  L.marker(
    [userLat, userLng],
    { icon: userIcon }
  )
  .addTo(map)
  .bindPopup("You are here")
  .openPopup();


  getPoliceStations(userLat, userLng);

}


// DISTANCE FUNCTION

function getDistance(lat1, lon1, lat2, lon2) {

  const R = 6371;

  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLon = (lon2 - lon1) * Math.PI / 180;

  const a =
    Math.sin(dLat/2) * Math.sin(dLat/2) +
    Math.cos(lat1*Math.PI/180) *
    Math.cos(lat2*Math.PI/180) *
    Math.sin(dLon/2) *
    Math.sin(dLon/2);

  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

  return R * c;

}


// LOAD POLICE STATIONS

function getPoliceStations(lat, lng) {

  const radius = 10000; // 10 km


  const query = `
    [out:json];
    node
      [amenity=police]
      (around:${radius},${lat},${lng});
    out;
  `;


  const url =
    "https://overpass-api.de/api/interpreter?data=" +
    encodeURIComponent(query);


  fetch(url)

    .then(res => res.json())

    .then(data => {

      if (!data.elements || data.elements.length === 0) {

        alert("No police stations found nearby.");
        return;

      }


      let minDistance = Infinity;
      nearestStation = null;

      data.elements.forEach(place => {

        const policeLat = place.lat;
        const policeLng = place.lon;

        const name =
          place.tags.name || "Police Station";


        const distance = getDistance(
          userLat, userLng,
          policeLat, policeLng
        );


        // Find nearest
        if (distance < minDistance) {

          minDistance = distance;

          nearestStation = {
            lat: policeLat,
            lng: policeLng,
            name: name
          };

        }


        // Police Marker (RED)
        L.marker(
          [policeLat, policeLng],
          { icon: policeIcon }
        )

        .addTo(map)

        .bindPopup(`
          <b>${name}</b><br>
          Distance: ${distance.toFixed(2)} km<br>
          <button onclick="showRoute(${policeLat}, ${policeLng})">
            Navigate Here
          </button>
        `);

      });


      // Auto Route to Nearest
      if (nearestStation) {

        alert(
          "Nearest Police Station: " +
          nearestStation.name +
          " (" + minDistance.toFixed(2) + " km)"
        );


        showRoute(
          nearestStation.lat,
          nearestStation.lng
        );

      }

    })

    .catch(err => {

      alert("Error loading police stations.");
      console.error(err);

    });

}


// SHOW ROUTE

function showRoute(destLat, destLng) {

  if (routeControl) {

    map.removeControl(routeControl);

  }


  routeControl = L.Routing.control({

    waypoints: [

      L.latLng(userLat, userLng),
      L.latLng(destLat, destLng)

    ],

    routeWhileDragging: true,
    show: true

  }).addTo(map);

}


// ERROR HANDLING

function showError(error) {

  switch(error.code) {

    case error.PERMISSION_DENIED:
      alert("Location permission denied.");
      break;

    case error.POSITION_UNAVAILABLE:
      alert("Location unavailable.");
      break;

    case error.TIMEOUT:
      alert("Request timeout.");
      break;

    default:
      alert("Unknown error.");

  }

}

</script>

</body>
</html>
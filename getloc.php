<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Get My Location</title>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>
<div id="map" style="height: 400px;"></div>
<button id="getLocationBtn">Get My Location</button>

<script>
var map = L.map('map').setView([51.505, -0.09], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
}).addTo(map);

var marker;

document.getElementById('getLocationBtn').addEventListener('click', function() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var latitude = position.coords.latitude.toFixed(6);
      var longitude = position.coords.longitude.toFixed(6);
      
      if (marker) {
        map.removeLayer(marker);
      }
      
      marker = L.marker([latitude, longitude]).addTo(map)
        .bindPopup('Your Location: ' + latitude + ', ' + longitude)
        .openPopup();
    }, function(error) {
      alert('Error getting location: ' + error.message);
    });
  } else {
    alert('Geolocation is not supported by your browser.');
  }
});
</script>
</body>
</html>

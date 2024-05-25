<?php
if(isset($_GET['lat']) && isset($_GET['lng'])) {
  $latitude = $_GET['lat'];
  $longitude = $_GET['lng'];
  
  // Construct Google Maps link
  $googleMapsLink = "https://www.google.com/maps?q={$latitude},{$longitude}";
  
  // Redirect to Google Maps
  header("Location: $googleMapsLink");
  exit;
} else {
  echo "Error: Coordinates not provided.";
}
?>

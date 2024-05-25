<?php
// Initialize session or include authentication mechanism
// Include database connection
// Initialize connection to MySQL database

// Assuming you have a users table with user_id, latitude, and longitude columns
$user_id = $_SESSION['user_id']; // Assuming user is authenticated and session is set

// Get latitude and longitude from POST request
$data = json_decode(file_get_contents('php://input'), true);
$latitude = $data['latitude'];
$longitude = $data['longitude'];

// Insert location into database
$sql = "INSERT INTO user_locations (user_id, latitude, longitude) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id, $latitude, $longitude]);

// Return success response if needed
?>
<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['SESSION_EMAIL'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Get user ID from session
$userId = $_SESSION['SESSION_ID'];

// Function to fetch user's location using JavaScript Geolocation API
function getUserLocation($userId) {
    echo '<script>
    navigator.geolocation.getCurrentPosition(function(position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;
        var locationData = {
            user_id: ' . $userId . ',
            latitude: latitude,
            longitude: longitude
        };
        fetch("update_location.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(locationData)
        }).then(function(response) {
            return response.json();
        }).then(function(data) {
            console.log("Location data sent:", data);
        }).catch(function(error) {
            console.error("Error sending location data:", error);
        });
    });
    </script>';
}

// Call the function to fetch user's location
getUserLocation($userId);


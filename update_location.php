<?php
session_start();
include 'config.php';

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the raw POST data
    $postData = file_get_contents("php://input");
    
    // Check if data is not empty
    if (!empty($postData)) {
        // Decode the JSON data into an associative array
        $locationData = json_decode($postData, true);

        // Validate and sanitize the input data (you may need to add more validation as needed)
        $userId = isset($locationData['user_id']) ? intval($locationData['user_id']) : 0;
        $latitude = isset($locationData['latitude']) ? floatval($locationData['latitude']) : 0.0;
        $longitude = isset($locationData['longitude']) ? floatval($locationData['longitude']) : 0.0;

        // Check if user ID, latitude, and longitude are valid
        if ($userId > 0 && $latitude != 0.0 && $longitude != 0.0) {
            // Update the user's location in the database
            $query = "UPDATE users SET latitude = $latitude, longitude = $longitude WHERE id = $userId";
            $result = mysqli_query($conn, $query);

            if ($result) {
                // Location updated successfully
                echo json_encode(['success' => true, 'message' => 'Location updated successfully.']);
            } else {
                // Error updating location
                echo json_encode(['success' => false, 'message' => 'Error updating location.']);
            }
        } else {
            // Invalid data received
            echo json_encode(['success' => false, 'message' => 'Invalid data received.']);
        }
    } else {
        // No data received
        echo json_encode(['success' => false, 'message' => 'No data received.']);
    }
} else {
    // Invalid request method
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>

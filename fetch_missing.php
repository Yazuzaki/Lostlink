<?php
session_start();

// Include your database connection file (e.g., config.php)
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['SESSION_EMAIL'])) {
    header("Location: login.php");
    exit();
}

// Fetch all items from the reported_missing table
$sql = "SELECT * FROM reported_missing";
$result = mysqli_query($conn, $sql);

// Initialize an array to hold all items
$items = array();

if ($result && mysqli_num_rows($result) > 0) {
    // Loop through each row in the result set
    while ($row = mysqli_fetch_assoc($result)) {
        // Add each item to the $items array
        $items[] = array(
            'item_id' => $row['item_id'],
            'user_id' => $row['user_id'],
            'user_name' => $row['user_name'],
            'item_name' => $row['item_name'],
            'item_image' => $row['item_image'],
            'item_description' => $row['item_description'],
            'last_seen' => $row['last_seen'],
            'posted_date' => $row['posted_date'],
            'qrcode_image' => $row['qrcode_image']
        );
    }

    // Prepare the response array
    $response = array(
        'success' => true,
        'items' => $items
    );
} else {
    // No items found in the database
    $response = array('success' => false, 'message' => 'No items found.');
}

// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($response);


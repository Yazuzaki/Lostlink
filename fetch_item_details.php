<?php
session_start();

// Include your database connection file (e.g., config.php)
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['SESSION_EMAIL'])) {
    header("Location: login.php");
    exit();
}

// Check if the item_id is set and is a valid integer
if (isset($_POST['item_id']) && filter_var($_POST['item_id'], FILTER_VALIDATE_INT)) {
    $itemId = $_POST['item_id'];
    
    // Fetch item details from the database based on the item_id
    $sql = "SELECT * FROM registered_items WHERE item_id='$itemId'"; // Assuming 'id' is the primary key
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) == 1) {
        $item = mysqli_fetch_assoc($result);
        
        // Prepare the item details as JSON for AJAX response
        $response = array(
            'success' => true,
            'item' => array(
                'user_name' => $item['user_name'],
                'item_name' => $item['item_name'],
                'item_image' => $item['item_image'],
                'item_description' => $item['item_description'],
                'last_seen' => $item['last_seen'],
                'posted_date' => $item['posted_date']
            )
        );
    } else {
        // Item not found in the database
        $response = array('success' => false, 'message' => 'Item not found.');
    }
} else {
    // Invalid or missing item_id parameter
    $response = array('success' => false, 'message' => 'Invalid item ID.');
}

// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Debugging: Print the response for debugging
// var_dump($response);
?>

<?php
// Include your database connection file (e.g., config.php)
include 'config.php';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are present in the POST request
    if (isset($_POST['item_id']) && isset($_POST['item_name']) && isset($_POST['item_description'])) {
        // Sanitize and validate input
        $itemId = mysqli_real_escape_string($conn, $_POST['item_id']);
        $itemName = mysqli_real_escape_string($conn, $_POST['item_name']);
        $itemDescription = mysqli_real_escape_string($conn, $_POST['item_description']);

        // Update the item details in the registered_items table
        $updateSqlRegisteredItems = "UPDATE registered_items SET item_name='$itemName', item_description='$itemDescription' WHERE item_id='$itemId'";
        $updateResultRegisteredItems = mysqli_query($conn, $updateSqlRegisteredItems);

        // Update the corresponding item details in the reported_missing table
        $updateSqlReportedMissing = "UPDATE reported_missing SET item_name='$itemName', item_description='$itemDescription' WHERE item_id='$itemId'";
        $updateResultReportedMissing = mysqli_query($conn, $updateSqlReportedMissing);

        if ($updateResultRegisteredItems && $updateResultReportedMissing) {
            // Update successful
            echo "success";
        } else {
            // Update failed
            echo "error";
        }
    } else {
        // Required fields are missing
        echo "error";
    }
} else {
    // Invalid request method
    echo "error";
}


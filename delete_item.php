<?php
// Include your database connection file
include 'config.php';

// Check if item ID is provided in the POST request
if (isset($_POST['item_id'])) {
    // Sanitize the input to prevent SQL injection
    $itemId = mysqli_real_escape_string($conn, $_POST['item_id']);

    // Perform the deletion operation in the registered_items table
    $deleteSqlRegisteredItems = "DELETE FROM registered_items WHERE item_id='$itemId'";
    $deleteResultRegisteredItems = mysqli_query($conn, $deleteSqlRegisteredItems);

    // Perform the deletion operation in the reported_missing table for the corresponding item
    $deleteSqlReportedMissing = "DELETE FROM reported_missing WHERE item_id='$itemId'";
    $deleteResultReportedMissing = mysqli_query($conn, $deleteSqlReportedMissing);

    if ($deleteResultRegisteredItems && $deleteResultReportedMissing) {
        // Deletion successful
        echo "success";
    } else {
        // Deletion failed
        echo "error";
    }
} else {
    // If item ID is not provided, handle the error
    echo "error";
}
?>

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['SESSION_EMAIL'])) {
  http_response_code(401); // Unauthorized
  exit();
}

// Include your database connection file (e.g., config.php)
include 'config.php';

// Get the item ID from the POST request
$itemId = isset($_POST['item_id']) ? $_POST['item_id'] : null;

// Check if item ID is provided
if ($itemId) {
  // Check if the item is marked as missing in the registered_items table
  $checkSql = "SELECT * FROM registered_items WHERE item_id='$itemId' AND is_missing='Missing'";
  $checkResult = mysqli_query($conn, $checkSql);
  if ($checkResult && mysqli_num_rows($checkResult) > 0) {
    // Delete the item from the reported_missing table
    $deleteSql = "DELETE FROM reported_missing WHERE item_id='$itemId'";
    $deleteResult = mysqli_query($conn, $deleteSql);

    if ($deleteResult) {
      // Update the status of the item as found in the registered_items table
      $updateSql = "UPDATE registered_items SET is_missing='Found' WHERE item_id='$itemId'";
      $updateResult = mysqli_query($conn, $updateSql);

      if ($updateResult) {
        http_response_code(200); // Success
        exit();
      } else {
        http_response_code(500); // Internal Server Error
        exit();
      }
    } else {
      http_response_code(500); // Internal Server Error
      exit();
    }
  } else {
    http_response_code(404); // Item not found or not marked as missing
    exit();
  }
} else {
  http_response_code(400); // Bad Request
  exit();
}
?>

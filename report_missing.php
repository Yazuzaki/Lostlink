<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['SESSION_EMAIL'])) {
  http_response_code(401); // Unauthorized
  exit();
}

// Include your database connection file (e.g., config.php)
include 'config.php';

// Get the item ID and Last Seen from the POST request
$itemId = isset($_POST['item_id']) ? $_POST['item_id'] : null;
$lastSeen = isset($_POST['last_seen']) ? $_POST['last_seen'] : null;

// Check if item ID and Last Seen are provided
if ($itemId && $lastSeen) {
  // Check if the item is already marked as missing in the registered_items table
  $checkSql = "SELECT * FROM registered_items WHERE item_id='$itemId' AND is_missing='Missing'";
  $checkResult = mysqli_query($conn, $checkSql);
  if ($checkResult && mysqli_num_rows($checkResult) > 0) {
    http_response_code(409); // Item already reported as missing
    exit(json_encode(['message' => 'Item already reported as missing']));
  }

  // Fetch item details from the database
  $itemSql = "SELECT * FROM registered_items WHERE item_id='$itemId'";
  $itemResult = mysqli_query($conn, $itemSql);
  if ($itemResult && mysqli_num_rows($itemResult) == 1) {
    $item = mysqli_fetch_assoc($itemResult);

    // Update the status of the item as missing in the registered_items table
    $updateSql = "UPDATE registered_items SET is_missing='Missing' WHERE item_id='$itemId'";
    $updateResult = mysqli_query($conn, $updateSql);

    if ($updateResult) {
      // Insert the item data into the reported_missing table
      $insertSql = "INSERT INTO reported_missing (item_id, user_id, user_name, item_name, item_image, item_description, last_seen, qrcode_image)
      VALUES ('{$item['item_id']}','{$item['user_id']}', '{$item['user_name']}', '{$item['item_name']}', '{$item['item_image']}', '{$item['item_description']}', '{$lastSeen}', '{$item['qrcode_image']}')";
      $insertResult = mysqli_query($conn, $insertSql);

      if ($insertResult) {
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
    http_response_code(404); // Item not found
    exit();
  }
} else {
  http_response_code(400); // Bad Request
  exit();
}

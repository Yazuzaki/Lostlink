<?php
// Include your database connection file (e.g., config.php)
include 'config.php';

// Get the search query from the POST request
$searchQuery = isset($_POST['search_query']) ? $_POST['search_query'] : '';

// Get missing items from the database based on the search query
$missingItemsSql = "SELECT * FROM reported_missing WHERE item_name LIKE '%$searchQuery%'";
$missingItemsResult = mysqli_query($conn, $missingItemsSql);
$missingItemsList = [];
if ($missingItemsResult && mysqli_num_rows($missingItemsResult) > 0) {
  while ($missingItemRow = mysqli_fetch_assoc($missingItemsResult)) {
    $missingItemsList[] = $missingItemRow;
  }
}

// Prepare the response data
$response = [
  'success' => true,
  'items' => $missingItemsList,
];

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);

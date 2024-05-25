<?php
// Include necessary files and initialize database connection
include 'config.php'; // Contains database connection details
require 'vendor/autoload.php'; // Include the QR code library

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// Start session
session_start();

// Function to generate QR code with given data and image
function generateQRCode($conn, $data, $image)
{
    $options = new QROptions([
        'version' => 5,
        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel' => QRCode::ECC_H, // Corrected constant for error correction level
        'imageBase64' => false, // Set to true if you want to output the image as base64
    ]);

    $qr = new QRCode($options);
    $qrImage = $qr->render($data); // Save QR code image

    // Save the image in the database
    $imageData = file_get_contents($image['tmp_name']);
    $imageBase64 = base64_encode($imageData);

    // Insert data into database
    $insertQuery = mysqli_prepare($conn, "INSERT INTO lost_found_items (owner_name, lost_item, item_details, item_image) 
                                         VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($insertQuery, 'ssss', $ownerName, $lostItem, $itemDetails, $imageBase64);

    $ownerName = mysqli_real_escape_string($conn, $_POST['owner_name']);
    $lostItem = mysqli_real_escape_string($conn, $_POST['lost_item']);
    $itemDetails = mysqli_real_escape_string($conn, $_POST['item_details']);

    if (mysqli_stmt_execute($insertQuery)) {
        $msg = "Item reported successfully!";
    } else {
        $msg = "Error reporting item.";
    }

    // Store QR image in session for later use
    $_SESSION['qr_image'] = $qrImage;
}

if (isset($_POST['submit'])) {
    // Check if database connection is established
    if (!isset($conn)) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Retrieve form data
    $ownerName = mysqli_real_escape_string($conn, $_POST['owner_name']);
    $lostItem = mysqli_real_escape_string($conn, $_POST['lost_item']);
    $itemDetails = mysqli_real_escape_string($conn, $_POST['item_details']);
    
    // Check if an image was uploaded
    if(isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
        $itemImage = $_FILES['item_image'];
        
        // Generate QR code data (Owner details + Lost item details)
        $qrData = "Owner: $ownerName\nLost Item: $lostItem\nDetails: $itemDetails";
    
        // Generate QR code and save it
        generateQRCode($conn, $qrData, $itemImage);
    
        // Notify the owner of the lost item (this is just an example, adjust as needed)
        $ownerNotification = "Your lost item has been found!";
        
        // Display owner notification (you can use this message to send an email or other notification)
        echo $ownerNotification;
    } else {
        // Handle image upload error
        echo "Error uploading image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Report Lost Item</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <h2>Report Lost Item</h2>
    <form method="post" action="" enctype="multipart/form-data">
        <label for="owner_name">Owner Name:</label>
        <input type="text" id="owner_name" name="owner_name" required><br><br>

        <label for="lost_item">Lost Item:</label>
        <input type="text" id="lost_item" name="lost_item" required><br><br>

        <label for="item_details">Item Details:</label><br>
        <textarea id="item_details" name="item_details" rows="4" cols="50" required></textarea><br><br>

        <label for="item_image">Item Image:</label>
        <input type="file" id="item_image" name="item_image" accept="image/*" required><br><br>

        <button type="submit" name="submit">Submit</button>
    </form>

    <?php
    if (!empty($_SESSION['qr_image'])) {
        echo '<h3>QR Code:</h3>';
        echo '<img src="data:image/png;base64,' . base64_encode($_SESSION['qr_image']) . '" />';
    }
    ?>

    <script>
        $(document).ready(function () {
            // Trigger location permission request on form submission
            $('form').submit(function (event) {
                event.preventDefault();
                requestLocationPermission();
            });
        });

        // Function to request location permission
        function requestLocationPermission() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        // Function to handle location data
        function showPosition(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;

            $.ajax({
                type: 'POST',
                url: 'process_location.php',
                data: {
                    latitude: latitude,
                    longitude: longitude,
                    qr_data: '<?php echo addslashes($qrData); ?>'
                },
                success: function (response) {
                    alert(response); // Display server response
                }
            });

        }
    </script>
</body>

</html>

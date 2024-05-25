<?php
// Include necessary files and initialize database connection
include 'config.php'; // Contains database connection details
require 'vendor/autoload.php'; // Include the QR code library

use chillerlan\QRCode\QRCode;

// Start session
session_start();

// Function to generate QR code with given data
function generateQRCode($data) {
    $qr = new QRCode;
    $qr->text($data);
    $qr->render('qr/code.png'); // Save QR code image
}

if (isset($_POST['latitude'], $_POST['longitude'], $_POST['qr_data'])) {
    // Retrieve location data and QR code data
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $qrData = $_POST['qr_data'];

    // Generate QR code and save it
    generateQRCode($qrData);

    // Notify the owner of the lost item (this is just an example, adjust as needed)
    $ownerNotification = "Your lost item has been found! Location: Latitude $latitude, Longitude $longitude";
    
    // Display owner notification (you can use this message to send an email or other notification)
    echo $ownerNotification;

    // Clear the QR data from session after processing
    unset($_SESSION['qr_data']);
} else {
    // Handle invalid request
    echo "Invalid request.";
}
?>

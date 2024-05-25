<?php
session_start();
include 'config.php';

if (isset($_POST['receiverId'])) {
    $receiverId = $_POST['receiverId'];

    // Fetch conversation from the database
    $query = "SELECT * FROM private_messages WHERE (sender_id = '$userId' AND receiver_id = '$receiverId') OR (sender_id = '$receiverId' AND receiver_id = '$userId') ORDER BY timestamp ASC";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $senderName = ($row['sender_id'] == $userId) ? 'You' : $userName;
            $message = $row['message'];
            $timestamp = formatDate($row['timestamp']);
            echo "<p><strong>$senderName:</strong> $message <span>($timestamp)</span></p>";
        }
    } else {
        echo "No messages found.";
    }
} else {
    echo "Receiver ID not provided.";
}
?>

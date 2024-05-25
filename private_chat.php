<?php
session_start();
include 'config.php';

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['SESSION_EMAIL'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details from the database based on the logged-in email
$email = $_SESSION['SESSION_EMAIL'];
$sql = "SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $userId = $row['id']; // Get user ID
    $userName = $row['name']; // Get user name
} else {
    // Redirect to login if user data is not found
    header("Location: login.php");
    exit();
}

// Function to format timestamp
function formatDate($timestamp)
{
    return date('Y-m-d H:i:s', strtotime($timestamp));
}

if (isset($_POST['submit'])) {
    $receiverId = $_POST['receiver_id'];
    $message = $_POST['message'];

    // Check if receiverId exists in the users table
    $checkReceiverQuery = "SELECT id FROM users WHERE id = '$receiverId'";
    $receiverResult = mysqli_query($conn, $checkReceiverQuery);

    if ($receiverResult && mysqli_num_rows($receiverResult) == 1) {
        // Insert private message into the database
        $query = "INSERT INTO private_messages (sender_id, receiver_id, message, timestamp) VALUES ('$userId', '$receiverId', '$message', NOW())";
        $run = $conn->query($query);

        if ($run) {
            // Notify user about successful message sent
            echo "<embed loop='false' src='discord-notification.wav' hidden='true' autoplay='true'/>";
        }
    } else {
        // Handle case where receiver does not exist
        echo "Error: Receiver not found.";
    }
}

// Fetch user list for displaying names to start conversations
$userQuery = "SELECT id, name FROM users WHERE id != '$userId'"; // Exclude the current user
$userResult = mysqli_query($conn, $userQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Your HTML head content -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            // Function to handle user selection and fetch conversation
            $('.select-user').click(function (e) {
                e.preventDefault();
                var receiverId = $(this).data('receiverid');

                // Redirect to private chat page with receiver ID
                window.location.href = 'private_chat.php?receiver_id=' + receiverId;
            });
        });
    </script>
</head>

<body>
    <!-- Your HTML body content -->
    <h1>Start a Conversation</h1>
    <ul>
        <?php
        while ($userRow = mysqli_fetch_assoc($userResult)) {
            echo "<li><a href='#' data-receiverid='{$userRow['id']}' class='select-user'>{$userRow['name']}</a></li>";
        }
        ?>
    </ul>
</body>

</html>

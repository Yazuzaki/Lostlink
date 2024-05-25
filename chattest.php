<?php
session_start();
include 'config.php';
// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['SESSION_EMAIL']))  {
  header("Location: login.php");
  exit();
}
// Fetch user details from the database based on the logged-in email
$email = $_SESSION['SESSION_EMAIL'];
$sql = "SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);
  $userId = $row['user_id']; // Get user ID
  $userName = $row['name']; // Get user name
} else {
  // Redirect to login if user data is not found
  header("Location: login.php");
  exit();
}



function formatDate($timestamp) {
    return date('Y-m-d H:i:s', strtotime($timestamp));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>::Message::</title>
    <link rel="stylesheet" href="chat.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Function to load chat messages
            function loadChat() {
                $.ajax({
                    url: 'get_chat.php',
                    type: 'GET',
                    success: function(response) {
                        $('#chat').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                });
            }

            // Load chat messages initially
            loadChat();

            // Function to send message
            $('#chat-form').submit(function(event) {
                event.preventDefault(); // Prevent default form submission

                // Get input values
                var message = $('#message-write').val();

                // AJAX to send message to server
                $.ajax({
                    url: 'send_message.php',
                    type: 'POST',
                    data: { message: message },
                    success: function(response) {
                        // Clear input after successful submission
                        $('#message-write').val('');
                        // Reload chat messages after sending
                        loadChat();
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                });
            });

            // Function to auto-refresh chat messages every 3 seconds
            setInterval(loadChat, 1000);
        });
    </script>
</head>
<body>

<div class="page">
    <div class="display-box">
        <div id="chat"></div>
    </div>
    <div class="form">
        <form action="" method="post">
        <span><?php echo $userName; ?></span><br>
            <label for="message">Write something:</label><br>
            <textarea name="message" id="message-write" cols="30" rows="3"></textarea><br>
            <input type="submit" name="submit" value="Send">
        </form>
        <?php
        if (isset($_POST['submit'])) {
            $name = $userName;
            $message = $_POST['message'];

            $query = "INSERT INTO tbl_chat (name, message) VALUES ('$name','$message')";
            $run = $conn->query($query);
           
        }
        ?>
    </div>
</div>
    
</body>
</html>
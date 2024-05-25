<?php
include 'config.php';
$msg = "";

if (isset($_GET['code'])) {
    $verificationCode = $_GET['code'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE code='{$verificationCode}'");

    if (mysqli_num_rows($query) > 0) {
        $updateQuery = mysqli_query($conn, "UPDATE users SET code='verified' WHERE code='{$verificationCode}'");

        if ($updateQuery) {
            $msg = "<div class='alert alert-success'>Account verification has been <span class='success'> successfully completed</span>.</div>";
        } else {
            $msg = "<div class='alert alert-danger' >Failed to complete account verification. Please try again later.</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'><span class='failed'>Invalid verification code. </span>Please check the link or try again.</div>";
    }
} else {
    header("Location: index.php");
    die();  
}
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Account Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <meta name="keywords" content="Account Verification" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Actor&display=swap" rel="stylesheet">
    <style>
        .failed{
            color:red;
        }
        .success{
            color:green;
        }
        .alert {
            text-align: center; /* Center text horizontally */
            font-size: 25px;
            width: 100%;
        }

        .alert span {
            font-weight: bold;
        }

        .alert-success {
            color:#001B299C; /* Adjust color for success message */
        }

        .alert-danger {
            color:#001B299C;
        }

        body {
            font-family: "Poppins", sans-serif;
            margin: 0; /* Remove default margin */
            padding: 0; /* Remove default padding */
        }

        h2 {
            font-size: 40px;
            text-align: center;
            color: #6200EE;
        }

        .left_grid_info {
            display: flex;
            justify-content: center; /* Center items horizontally */
            align-items: center; /* Center items vertically */
        }

        .left_grid_info img {
            max-width: 150px; /* Adjust maximum width of images */
            height: auto;
            margin: 0 10px; /* Add some spacing between the images */
        }

        /* Navbar styles */
        .navbar {
            background-color: #6200EE; /* Set background color */
            padding: 30px; /* Add padding to the navbar */
        }

        /* Style the links within the navbar */
        .navbar a {
            color: #fff;
            text-decoration: none; /* Remove underline from links */
            margin-right: 20px; /* Add some spacing between links */
        }

        /* Style hover effect for links */
        .navbar a:hover {
            text-decoration: underline; /* Add underline on hover */
        }

        .p-center {
            text-align: center;
        }

        .container {
            max-width: 600px; /* Adjust max-width as needed */
            margin: 0 auto; /* Center the container horizontally */
            display: flex;
            justify-content: center; /* Center items horizontally */
            align-items: center; /* Center items vertically */
            height: 80vh; /* Set height to fill the viewport vertically */
            padding: 0 20px; /* Add padding to the sides */
        }

        a {
            color: #6200EE;
            text-decoration: none;
            font-weight: 600;
        }
           /* Media query for mobile devices */
           @media (max-width: 768px) {
            h2 {
                font-size: 28px; /* Decrease font size for smaller screens */
            }

            .left_grid_info img {
                max-width: 150px; /* Decrease image size for smaller screens */
            }

            .container {
                max-width: 100%; /* Adjust container width for smaller screens */
            }
            .alert{
                font-size:17px;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <div class="navbar">
    </div>

    <section class="w3l-mockup-form">
        <div class="container">
            <div class="workinghny-form-grid">
                <div class="main-mockup">
                    <div class="w3l_form align-self">
                        <div class="left_grid_info">
                            <!-- Add your images here -->
                            <img src="img2.png" alt="Image 1">
                            <img src="img3.png" alt="Image 2">
                        </div>
                    </div>
                    <div class="content-wthree">
                        <h2>Account Verification</h2>
                        <?php echo $msg; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>

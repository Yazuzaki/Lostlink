<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lost Link</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #fff;
    }
    .welcome-container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .lead {
      font-family: "Poppins", sans-serif;
      font-weight: 400;
      font-size: 18px;
      line-height: 21px;
      color: #001B299C;
    }
    h1 {
      margin-top: 1rem;
      font-family: "Poppins", sans-serif;
      font-weight: 600;
      font-size: 24px;
      color: #001B29CC;
    }
    .login-btn{
      font-family: "Poppins", sans-serif;
      padding: 10px 110px 10px 110px;
      font-size: 14px;
      background-color:  #6200EE;
      color: #fff;
      border: none;
      cursor: pointer;
      border-radius: 25px 25px;
      transition: 200ms ease-in-out;
      text-decoration: none; /* Remove underline */
    }
    .register-btn{
      font-family: "Poppins", sans-serif;
      padding: 9px 100px 9px 100px;
      font-size: 14px;
      background-color: #fff;
      color:  #6200EE;
      border: 1px solid #6200EE;
      cursor: pointer;
      border-radius: 25px 25px;
      transition: 200ms ease-in-out;
      text-decoration: none; /* Remove underline */
    }

    /* Remove blue color on click */
    .login-btn:focus, .register-btn:focus {
      outline: none;
      box-shadow: none;
    }

    /* Remove underline on hover */
    .login-btn:hover, .register-btn:hover {
      text-decoration: none;
    }

  </style>
</head>
<body>
  <div class="welcome-container">
    <div class="text-center">
      <img src="img1.png" alt="">
      <h1>Lost Something?</h1>
      <?php
      // Start or resume the session
      session_start();

      // Check if the user is logged in
    // Check if the user is logged in
    if (isset($_SESSION['SESSION_EMAIL'])) {
      // If logged in, redirect to homepage.php
      header("Location: homepage.php");
      exit();
    } else {
        // If not logged in, show the default message
       
        echo '<p class="lead">Create an ad of your lost item and let your friends know</p>';
        echo '<div class="mt-4">';
        echo '<div class="mb-3">';
        echo '<a href="login.php" class= "login-btn">Login</a>';
        echo '</div>';
        echo '<div>';
        echo '<a href="register.php" class= "register-btn">Register</a>';
        echo '</div>';
        echo '</div>';
      }
      ?>
    </div>
  </div>

  <!-- Bootstrap JS (optional) -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

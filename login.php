<?php
session_start();

if (isset($_SESSION['SESSION_EMAIL'])) {
  header("Location: main-page.php"); // Redirect if already logged in
  exit();
}

include 'config.php'; // Assuming this file contains your database connection code

$msg = ""; // Initialize the message variable

if (isset($_POST['submit'])) {
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = mysqli_real_escape_string($conn, md5($_POST['password']));

  $sql = "SELECT * FROM users WHERE email='{$email}' AND password='{$password}'";
  $result = mysqli_query($conn, $sql);

  if ($result && mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);

    if (strpos($row['code'], '') !== false) {
      $_SESSION['SESSION_EMAIL'] = $email;
      if ($row['is_admin']) {
        header("Location: admin.php"); // Redirect to admin page if user is admin
        exit(); // Exit after redirection
      } else {
        // successfully logged in
        // creating the SESSION
        $_SESSION['username'] = $row['username'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['user_id'] = $row['user_id'];
        // Check if there's a redirect URL
        if (isset($_GET['redirect'])) {
          $redirectUrl = urldecode($_GET['redirect']);
          // Redirect the user to the URL they were trying to access before login
          header("Location: " . $redirectUrl);
          exit;
        } else {
          // If no redirect URL, redirect to the default page (e.g., home page)
          header("Location: main-page.php");
          exit;
        }
      }
    } else {
      $msg = "<div class='alert alert-error'>First verify your account and try again.</div>";
    }
  } else {
    $msg = "<div class='alert alert-error'>Email or password do not match.</div>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Actor&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">
  <title>Lost Link</title>
  <style>
    .container {
      max-width: 400px;
      margin: 50px auto;
      text-align: left;
      /* Align header to the left */
      padding-left: 20px;
      /* Add padding to the left for better alignment */
      padding-right: 20px;
      position: relative;
      /* Make container position relative */
    }

    h1 {
      margin-bottom: 30px;
      font-family: "Poppins", sans-serif;
      font-weight: 500;
    }

    .email-input,
    .password-input {
      position: relative;
      margin-bottom: 20px;
    }

    .email-input input,
    .password-input input {
      width: 100%;
      padding: 10px 0;
      border: none;
      border-bottom: 1px solid #000;
      outline: none;
      font-family: "Poppins", sans-serif;
      transition: border-bottom 0.3s ease;
      /* Added transition for smooth effect */
    }

    .email-input input:focus,
    .password-input input:focus {
      border-bottom: 2px solid #001B299C;
      /* Adjusted border-bottom thickness for focus state */
    }

    .email-input label,
    .password-input label {
      position: absolute;
      top: 0;
      left: 0;
      padding: 10px 0;
      /* Shorter placeholder length */
      color: #001B299C;
      pointer-events: none;
      transition: .3s;
      font-family: "Poppins", sans-serif;
      font-size: 12px;
      /* Adjusted font size for shorter placeholders */
    }

    .email-input input:focus+label,
    .email-input input:not(:placeholder-shown)+label,
    .password-input input:focus+label,
    .password-input input:not(:placeholder-shown)+label {
      transform: translateY(-20px);
      font-size: 14px;
      color: #001B299C;
    }

    button {
      font-family: "Poppins", sans-serif;
      padding: 10px 100px 10px 100px;
      font-size: 16px;
      background-color: #6200EE;
      color: #fff;
      border: none;
      cursor: pointer;
      border-radius: 25px 25px;
      transition: 200ms ease-in-out;
      margin: 4rem auto 0;
      /* Center the button horizontally */
      display: block;
      /* Ensure the button occupies full width */
    }

    button:hover {
      background-color: #001B299C;
    }

    .forgot-password {
      position: absolute;
      top: 100%;
      right: 0;
      font-family: "Poppins", sans-serif;
      font-size: 12px;
      color: #4252E5;
      text-decoration: none;
      margin-top: 5px;
    }

    #back-icon {
      margin-top: 10px;
    }

    .create-container {
      text-align: center;
      font-family: "Poppins", sans-serif;
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      margin-bottom: 2rem;
    }

    .create-container a {
      font-size: 12px;
      text-decoration: none;
      color: #6200EE;

    }

    .create-container p {
      font-size: 14px;
    }
  </style>
</head>

<body>
  <a href="welcome.php"><img src="back.png" alt="" id="back-icon"></a>
  <div class="container">
    <h1>Sign In</h1>
    <form action="" method="post">
      <div class="email-input">
        <input type="email" id="email" name="email" placeholder=" " required />
        <label for="email">Email</label>
      </div>
      <div class="password-input">
        <input type="password" id="password" name="password" placeholder=" " required />
        <label for="password">Password</label>
        <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
      </div>
      <button type="submit" name="submit">Sign In</button>
    </form>
  </div>
  <div class="create-container">
    <p>Don't have an account yet?</p>
    <a href="register.php">Create one here</a>
  </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
<script>
  <?php
  if (!empty($msg)) {
    echo "var message = '" . addslashes($msg) . "';";
    if (strpos($msg, 'error') !== false) {
      // Display error message in red
      echo "iziToast.show({
      title: '',
      message: message,
      color: 'red',
      position: 'topCenter',
      timeout: 5000,
      transitionIn: 'fadeInDown',
      close: true, // Include the close button inside the box
      progressBarColor: 'rgb(0, 255, 184)' // Custom progress bar color
    });";
    } else {
      // Display success message in green
      echo "iziToast.show({
      title: '',
      message: message,
      color: 'green',
      position: 'topCenter',
      timeout: 5000,
      transitionIn: 'fadeInDown',
      close: true, // Include the close button inside the box
      progressBarColor: 'rgb(0, 255, 184)' // Custom progress bar color
    });";
    }
  }
  ?>





</script>

</html>
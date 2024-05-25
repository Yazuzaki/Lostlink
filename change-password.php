<?php
$msg = "";

include 'config.php';

if (isset($_GET['reset'])) {
    if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE code='{$_GET['reset']}'")) > 0) {
        if (isset($_POST['submit'])) {
            $password = mysqli_real_escape_string($conn, md5($_POST['password']));
            $confirm_password = mysqli_real_escape_string($conn, md5($_POST['confirm-password']));

            if ($password === $confirm_password) {
                $query = mysqli_query($conn, "UPDATE users SET password='{$password}', code='' WHERE code='{$_GET['reset']}'");

                if ($query) {
                    // Update code to 'verified'
                    $updateCodeQuery = mysqli_query($conn, "UPDATE users SET code='verified' WHERE code='{$_GET['reset']}'");
                    $msg = "<div class='alert alert-success'>Password reset successful'.</div>";
                    
                    if ($updateCodeQuery) {
                        header("Location: index.php");
                    } else {
                        $msg = "<div class='alert alert-error'>Error updating code to 'verified'.</div>";
                    }
                } else {
                    $msg = "<div class='alert alert-error'>Error updating password.</div>";
                }
            } else {
                $msg = "<div class='alert alert-error'>Password and Confirm Password do not match.</div>";
            }
        }
    } else {
        $msg = "<div class='alert alert-error'>Reset Link does not match.</div>";
    }
} else {
    header("Location: forgot-password.php");
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Login Form</title>
    <!-- Meta tag Keywords -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <meta name="keywords" content="Login Form" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">
    <script src="https://kit.fontawesome.com/af562a2a63.js" crossorigin="anonymous"></script>
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
      font-weight: 600;
    }

    .email-input,
    .password-input,
    .name-input,
    .confirm-password-input {
      position: relative;
      margin-bottom: 20px;
    }

    .email-input input,
    .password-input input,
    .name-input input,
    .confirm-password-input input {
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
    .password-input input:focus,
    .name-input input:focus,
    .confirm-password-input input:focus {
      border-bottom: 2px solid #001B299C;
      /* Adjusted border-bottom thickness for focus state */
    }

    .email-input label,
    .password-input label,
    .name-input label,
    .confirm-password-input label {
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
    .password-input input:not(:placeholder-shown)+label,
    .name-input input:focus+label,
    .name-input input:not(:placeholder-shown)+label,
    .confirm-password-input input:focus+label,
    .confirm-password-input input:not(:placeholder-shown)+label {
      transform: translateY(-20px);
      font-size: 14px;
      color: #001B299C;
    }

    button {
      font-family: "Poppins", sans-serif;
      padding: 10px 100px 10px 100px;
      font-size: 12px;
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

    p{
        text-align:center;
        font-family: "Poppins", sans-serif;

    }
    .p-icons{
        font-size: 12px;
        font-weight:600;

    }
    .p-text{
        font-size:14px;
        text-align:justify;
        color: #001B299C;
        margin-bottom:2rem;

    }
    span{
        color: #6200EE;
        font-weight:600;
    }
    a{
        text-decoration:none;
        font-weight:600;
        color: #4252E5;

    }

    #back-icon {
      margin-top: 10px;
    }
    </style>
</head>

<body>

    <!-- form section start -->
    <section class="w3l-mockup-form">
    <a href="login.php"><img src="back.png" alt="" id="back-icon"></a>

        <div class="container">
            <!-- /form -->
            <div class="workinghny-form-grid">
                <div class="main-mockup">
                    <div class="w3l_form align-self">
                        <div class="left_grid_info">
                        </div>
                    </div>
                    <div class="content-wthree">
                        <h1>Change Password</h1>
                        <form action="" method="post">            
                            <div class="password-input">
                                <input type="password" class="password" name="password" id="password" placeholder=" " required />
                                <label for="password">Enter Your Password</label>
                            </div>
                            <div class="confirm-password-input">
                                <input type="password" class="confirm-password" name="confirm-password" id="confirm-password" placeholder=" "
                                required />
                                <label for="confirm-password">Please Confirm Your Password</label>
                            </div>
                            <button name="submit" class="btn" type="submit">Change Password</button>
                        </form>
                        <div class="social-icons">
                            <p class="p-icons" >Back to! <a href="login.php">Login</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- //form -->
        </div>
    </section>
    <!-- //form section start -->
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
      close: false, // Include the close button inside the box
      closeOnClick: true,
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
      close: false, // Include the close button inside the box
      closeOnClick: true,
      progressBarColor: 'rgb(0, 255, 184)' // Custom progress bar color
    });";
  }
}
?>





</script>
</html>
<!-- Code by Brave Coder - https://youtube.com/BraveCoder -->

<?php

session_start();
if (isset($_SESSION['SESSION_EMAIL'])) {
    header("Location: welcome.php");
    die();
}

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

include 'config.php';
$msg = "";

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $code = mysqli_real_escape_string($conn, md5(rand()));

    if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE email='{$email}'")) > 0) {
        $query = mysqli_query($conn, "UPDATE users SET code='{$code}' WHERE email='{$email}'");

        if ($query) {        
            echo "<div style='display: none;'>";
            //Create an instance; passing `true` enables exceptions
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = 'lostlinkapp@gmail.com';                     //SMTP username
                $mail->Password   = 'kpujprdcjsrmcbdz';                               //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                //Recipients
                $mail->setFrom('lostlinkapp@gmail.com');
                $mail->addAddress($email);

                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Lost Link Forgot Password';                          // Email subject
                
                // Stylish email body with improved design and icons
                $mail->Body = '
                    <div style="background-color: #6200EE; padding: 20px; font-family: Arial, sans-serif; color: #fff; border-radius: 10px;">
                        <nav style="text-align: center;">
                            <h2 style="margin: 0; font-size: 24px;">LostLink Forgot Password</h2>
                        </nav>
                        <div style="background-color: #f4f4f4; padding: 20px; margin-top: 20px; border-radius: 10px;">
                            <p style="font-size: 16px; font-weight:bold; color: #333;">Dear User,</p>
                            <p style="font-size: 16px; color: #333;">We recently received a request to reset the password for your account. If you did not make this request, please ignore this message and no further action is required.</p>
                            <p style="font-size: 16px; color: #333;">To reset your password, please click on the following link:</p>
                            <div style=" margin-top: 20px;">
                                <a href="http://localhost/LostLink.github.io/change-password.php?reset='.$code.'" style="background-color: #6200EE; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 16px;">
                                    <i style="margin-right: 5px;" class="fas fa-unlock-alt"></i><b>Reset Password<b/>
                                </a>
                            </div>
                            <p style="font-size: 16px; color: #333; margin-top: 20px;">You will be redirected to a page where you can create a new password. Please ensure that your new password is unique and difficult for others to guess.</p>
                            <p style="font-size: 16px; color: #333;">If you have any difficulties or questions, please do not hesitate to contact our customer support team.</p>
                            <p style="font-size: 16px; color: #333;">Best Regards,<br> <span style="font-weight:bold; color: green;">The LostLink Team<span/></p>
                        </div>
                    </div>
                ';
                
                $mail->send();  // Send the email
                
                echo 'Message has been sent';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
            echo "</div>";        
            $msg = "<div class='alert alert-success'>We've send a verification link on your email address.</div>";
        }
    } else {
        $msg = "<div class='alert alert-error'>$email - This email address do not found.</div>";
    }
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
      font-weight: 600;
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
                        <h1>Forgot Password</h1>
                        <p class="p-text"> <span>Lost your password? </span>Please enter your email address. You will receive a link to create a new password via email. </p>
                        <form action="" method="post">
                            <div class="email-input">
                                <input type="email" id="email" name="email" placeholder=" " required />
                                <label for="email">Enter Your Email</label>
                                <button name="submit" class="btn" type="submit">Send Reset Link</button>
                            </div>
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
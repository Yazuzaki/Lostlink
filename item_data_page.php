<?php
session_start(); // Start the session

// Include your database connection file
include_once 'config.php'; // Update this with the correct file path

// Check if the user is logged in and get their email
if (isset($_SESSION['SESSION_EMAIL'])) {
  $loggedInEmail = $_SESSION['SESSION_EMAIL'];

  // You can use $loggedInEmail to fetch additional user details or perform actions based on the logged-in user
  // echo "Logged-in User Email: " . $loggedInEmail;
} else {
  // Handle the case when the user is not logged in
  // echo "User is not logged in.";
}
// Fetch item details from the URL parameters
if (isset($_GET['itemData'])) {
  $itemData = $_GET['itemData'];

  // Extract QR data from the item data
  $qrData = substr($itemData, strpos($itemData, "QR Data: ") + 9);

  // Query to fetch item details based on qr_data
  $sql = "SELECT * FROM registered_items WHERE qr_data = ?";
  
  // Prepare the statement
  $stmt = $conn->prepare($sql);
  
  if ($stmt) {
    // Bind the parameter
    $stmt->bind_param("s", $qrData);
    
    // Execute the statement
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();
    
    // Check if result exists and fetch the item data
    if ($result && $result->num_rows > 0) {
      $item = $result->fetch_assoc();

      // Fetch the item images and split them into an array
      $images = explode(',', $item['item_image']);

      // Extract other item details
      $user_id = $item['user_id'];
      $itemName = $item['item_name'];
      $itemstatus = $item['is_missing'];
      $itemDescription = $item['item_description'];
      $userName = $item['user_name'];

      // Now fetch the last seen information from reported_missing table
      $sqlLastSeen = "SELECT last_seen FROM reported_missing WHERE item_id = ?";
      $stmtLastSeen = $conn->prepare($sqlLastSeen);

      if ($stmtLastSeen) {
        // Bind the parameter
        $stmtLastSeen->bind_param("i", $item['item_id']);

        // Execute the statement
        $stmtLastSeen->execute();

        // Get the result
        $resultLastSeen = $stmtLastSeen->get_result();

        // Check if result exists and fetch the last seen data
        if ($resultLastSeen && $resultLastSeen->num_rows > 0) {
          $reported = $resultLastSeen->fetch_assoc();
          $lastSeen = $reported['last_seen'];
        } else {
          $lastSeen = "Not reported missing";
        }

        // Close the statement
        $stmtLastSeen->close();
      } else {
        echo "Error executing the last seen query."; // Debugging line
      }

      // Now you can use these variables to display item details in your HTML
    } else {
      echo "Item not found."; // Debugging line
    }

    // Close the statement
    $stmt->close();
  } else {
    echo "Error executing the query."; // Debugging line
  }
} else {
  echo "Item data parameter missing."; // Debugging line
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Page</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izimodal/1.6.0/css/iziModal.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


  <style>
    body {
      font-family: "Poppins", sans-serif;
    }

    /* Hide scrollbar for webkit browsers (Chrome, Safari) */
    ::-webkit-scrollbar {
      display: none;
    }

    /* Hide scrollbar for Firefox */
    html {
      scrollbar-width: none;
    }


    .left-icon {
      margin-left: 2.5rem;
    }

    .right-icon {
      margin-right: 2.5rem;
    }

    /* Custom rounded box */
    .rounded-box {
      background-color: #6200EA;
      border-radius: 10px;
      padding: 20px;
      color: #fff;
      margin-bottom: 20px;
      margin-top: -1rem;
    }

    h1 {
      font-weight: 600;
    }

    p {
      font-family: "Poppins", sans-serif;
      font-weight: 100;
      font-style: normal;
      color: #e1def9;
    }

    .button-container {
      display: flex;
      align-items: center;
    }

    /* Added margin to the left of the button */
    .button-container button {
      margin-left: auto;
    }

    @media (min-width: 768px) {
      .button-container {
        display: flex;
        justify-content: center;
        margin-top: 1rem;
        /* Adjust margin for better spacing */
      }

      h1 {
        font-size: 20px;
        margin-right: 1rem;
      }

      .button-container button {
        margin-left: 0;
      }

      p {
        margin-top: 1.5rem;
      }
    }

    #back-icon {
      margin-top: 10px;
    }

    #more-icon {
      margin-top: 5px;
      margin-bottom: -1.6rem;
    }

    /* CSS to control the aspect ratio of the image */
    .image-wrapper {
      position: relative;
      width: 100%;
      height: 0;
      padding-top: 56.25%;
      /* 16:9 aspect ratio (height:width) */
      overflow: hidden;
    }

    .image-wrapper img {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: contain;
      /* Maintain aspect ratio without cropping */
      object-position: center;
      /* Center the image within the container */
    }



    /* Edit button */
    .edit-btn {
      position: absolute;
      bottom: 10px;
      right: 10px;
      z-index: 1;
      /* Ensure it's above the image */
    }

    /* CSS for the modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.9);
    }

    .modal-content {
      margin: auto;
      display: block;
      width: 80%;
      max-width: 700px;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }


    #close {
      color: white;
      position: absolute;
      top: 15px;
      right: 35px;
      font-size: 40px;
      font-weight: bold;
      transition: 0.3s;
    }

    #close:hover,
    #close:focus {
      color: #bbb;
      text-decoration: none;
      cursor: pointer;
    }

    .card-title {
      font-size: 25px;
      font-weight: bold;
      margin-top: -0.8rem;
      margin-bottom: 1.7rem;
      letter-spacing: 0.5px;
    }

    .item-detail-card {
      border: none;
    }

    .item-status {
      color: #33363896;
      font-size: 11px;
      font-weight: bold;

    }

    .item-description-title {
      color: #33363896;
      font-size: 11.5px;
      letter-spacing: 1px;
      font-weight: bold;


    }

    .item-description-text {
      color: #33363896;
      font-size: 15px;
      margin-top: -0.6rem;
      margin-bottom: 1.5rem;
    }

    .last-seen-title {
      color: #33363896;
      font-size: 11.5px;
      letter-spacing: 1px;
      font-weight: bold;
    }

    .location-icon {
      color: #6200EE;
      margin-right: 0.5rem;
      font-weight: 900;
    }

    .last-seen-text {
      color: #33363896;
      font-size: 15px;
      margin-top: -0.6rem;
      margin-bottom: 1.5rem;
    }

    .user-title {
      color: #33363896;
      font-size: 11.5px;
      letter-spacing: 1px;
      font-weight: bold;
    }

    .person-icon {
      color: #6200EE;
      margin-right: 0.5rem;
      font-weight: 900;
    }

    button {
      font-family: "Poppins", sans-serif;
      padding: 10px 55px 10px 55px;
      font-size: 13px;
      font-weight: bold;
      background-color: #6200EE;
      letter-spacing: 1px;
      color: #fff;
      border: none;
      cursor: pointer;
      border-radius: 25px 25px;
      transition: 200ms ease-in-out;
      margin-top: 1.5rem;
    }

    button:hover {
      box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.3);

    }

    .owner-name {
      color: #33363896;
      font-weight: bold;
      margin-top: -0.6rem;
      margin-bottom: -0.2rem;
    }


    .view-qr-link {
      text-decoration: none;
      color: #6200EE;
      font-weight: bold;
      font-size: 12px;
      text-align: center;
    }

    .owner-id {
      color: #33363896;
      font-size: 11px;
      margin-top: -0.3rem;
    }

    .modal-exit-btn {
      margin: 1rem;
    }

    .modal-item-Name {
  margin-bottom: 2rem;
  margin-top: -0.5rem; 
  overflow-wrap: break-word; /* Allow long words to break and wrap */
}

.modal-item-Name h2 {
  font-size: 15px;
  font-weight: bold;
  margin-left:0.7rem;
  margin-right:0.7rem;
  color: #33363896;
}


    .qr-img-container img {
      width: 250px;
      height: 250px;
      margin-top:1rem
    }

    .exit-btn {
      box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.3);
    }

    .download-qr-btn {
      text-decoration: none;
      color: #6200EE;
    }

    .text-end {
      text-align: end;
    }

    /* Add CSS styles to change background color on hover and click */
    .dropdown-item.edit-item:hover,
    .dropdown-item.edit-item:focus,
    .dropdown-item.edit-item:active,
    .dropdown-item.delete-item:hover,
    .dropdown-item.delete-item:focus,
    .dropdown-item.delete-item:active {
      background-color: #6200EE !important;
      color: #fff;
    }

    .edit-form {
      margin-top: 2rem;
      padding: 1rem:
    }
    #closeEditModal{
      font-size:25px;
    }
    /* Center the "Save changes" button */
.modal-footer button {
  margin: 0 auto; /* Center the button horizontally */
}
  /* Define the custom focus ring color */
  .form-control:focus {
    outline: none !important;
        box-shadow: none !important;
        border-color: #6200EE;

  }
  .no-btn{
    padding:10px 50px 10px 50px;
    font-weight:bold;
    border-radius:25px 25px;
    color:white;
    margin:0.5rem;


  }
  .yes-btn{
    color:white;
    background-color:#6200EE;
    padding:10px 50px 10px 50px;
    font-weight:bold;
    border-radius:25px 25px;
    margin:0.5rem;

  }

  
  .delete-p{
    font-family: "Poppins", sans-serif;
    color:black;
    font-size:12px;
    color: #33363896;
  }
 
  @media (max-width: 380px){
    .btn-secondary{
    font-size:11px;
  }
  .btn-danger{
    font-size:11px;
  }
  }
  .email-btn:hover{
    box-shadow:none;
    outline:none !important;
    border:none; 
    color:green;
  }
  .msg-btn:hover{
    box-shadow:none;
    outline:none !important;
    border:none; 
    color:green;
  }
  .msg-btn{
    color:#6200EA;
  }
  .email-btn{
    color:#6200EA;

  }
  



  </style>
</head>

<body>
  <a href="main-page.php"><img src="back.png" alt="" id="back-icon"></a>

  <div class="container">
    <div class="row justify-content-md-center">
      <div class="col-md-6 item-box">
        <div class="dropdown text-end">
          <ul class="dropdown-menu" aria-labelledby="moreDropdown">
            <!-- Add event listeners to open the edit and delete modals -->
            <li><a class="dropdown-item edit-item" href="#" onclick="openEditModal()">Edit</a></li>
            <li><a class="dropdown-item delete-item" href="#" onclick="openDeleteModal()">Delete</a></li>
          </ul>
        </div>



        <div class="card mb-1 mt-4">
  <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <?php
      // Split the item images by comma
      $images = explode(',', $item['item_image']);
      $active = 'active'; // Set the active class for the first image
      foreach ($images as $image) {
        echo '<div class="carousel-item ' . $active . '">';
        echo '<div class="image-wrapper">';
        echo '<img src="' . $image . '" class="card-img-top" alt="Item Image">';
        echo '</div>';
        echo '</div>';
        $active = ''; // Remove active class for subsequent images
      }
      ?>
    </div>
    <!-- Previous and Next buttons -->
    <button class="carousel-control-prev visually-hidden" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next visually-hidden" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
</div>

        <!-- Details Card -->
        <div class="card mb-4 item-detail-card">
          <div class="card-body">
            <p class="item-status"><?php echo $itemstatus;?></p>
            <h5 class="card-title"><?php echo $itemName; ?></h5>
            <p class="item-description-title">Description</p>
            <p class="card-text item-description-text"><?php echo $itemDescription; ?></p>
            <p class="last-seen-title"><img src="location_on.png" alt="" class="location-icon"></i>Last Seen</p>
            <p class="card-text last-seen-text"></i><?php echo $lastSeen; ?></p>
            <p class="user-title"><img src="person.png" alt="" class="person-icon">Owner</p>
            <p class="card-text owner-name"><?php echo $userName; ?></p>
            <p class="card-text owner-id"><small>User ID: # <?php echo $user_id ?></small></p>
          </div>
          <div class="message-owner-btn-container text-center">
          <button onclick="openMessageModal()">Message Owner</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
  // Function to open the message modal
function openMessageModal() {
  var modal = document.getElementById("messageModal");
  modal.style.display = "block";
}

// Function to close the message modal
function closeMessageModal() {
  var modal = document.getElementById("messageModal");
  modal.style.display = "none";
}

// Function to close the message modal
function closeMessageModal() {
  var modal = document.getElementById("messageModal");
  modal.style.display = "none";
}

// Function to send the selected message type
function sendMessage(type) {
  // Get the owner's username
  var ownerUsername = "<?php echo $userName; ?>";

  if (type === 'email') {
    // Send email logic
    console.log('Sending email...');
  } else if (type === 'message') {
    // Check if the user is logged in
    var isLoggedIn = <?php echo isset($_SESSION['SESSION_EMAIL']) ? 'true' : 'false'; ?>;

    // If not logged in, prompt the user to login or signup
    if (!isLoggedIn) {
      var confirmLogin = confirm("Please login or signup first to send a chat message.");
      if (confirmLogin) {
        // Redirect to the login page with the redirect URL after successful login
        var redirectUrl = 'http://localhost/LostLink.github.io/chat.php?user=' + ownerUsername;
        window.location.href = 'http://localhost/LostLink.github.io/login.php?redirect=' + encodeURIComponent(redirectUrl);
      }
    } else {
      // Redirect logged-in users to the chat page
      var redirectUrl = 'http://localhost/LostLink.github.io/chat.php?user=' + ownerUsername;
      window.location.href = redirectUrl;
    }
  }
}



</script>

<!-- Contact modal -->
<div class="modal" id="messageModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-bottom">
    <div class="modal-content">
    <div class="modal-header" style="background:#6200EA;">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick=closeMessageModal()></button>
      </div>
      <div class="modal-body">
        <div class="text-center">
          <h2>Contact Owner</h2>
          <p class="delete-p" style="font-size:14px;">Please select how you want to contact the owner</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn email-btn " onclick="sendMessage('email')">Email</button>
        <button type="button" class="btn msg-btn" onclick="sendMessage('message')">Message</button>
      </div>
    </div>
  </div>
</div>





  <!-- edit modal -->
  <div class="modal" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-bottom">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><b>Edit Item Details</b></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick=closeEditModal()></button>
      </div>
      <div class="modal-body">
        <!-- Input fields for editing item details -->
        <div class="mb-3">
          <label for="itemName" class="form-label">Item Name</label>
          <input type="text" class="form-control" id="itemName" value="<?php echo $item['item_name']; ?>">
        </div>
        <div class="mb-3">
          <label for="itemDescription" class="form-label">Item Description</label>
          <textarea class="form-control" id="itemDescription" rows="3"><?php echo $item['item_description']; ?></textarea>
        </div>
        <div class="mb-3">
          <label for="lastSeen" class="form-label">Item Name</label>
          <input type="text" class="form-control" id="lastSeen" value="<?php echo $item['last_seen']; ?>">
        </div>
      </div>
      <div class="modal-footer text-center">
        <button type="button" class="">Save changes</button>
      </div>
    </div>
  </div>
</div>






  <div id="deleteModal" class="modal">
    <div class="modal-content">
      <span id="closeDeleteModal" class="close" onclick="closeDeleteModal()">&times;</span>
      <p>Are you sure you want to delete this item?</p>
      <div class="button-container">
        <button onclick="deleteItem()">Yes</button>
        <button onclick="closeDeleteModal()">No</button>
      </div>
    </div>
  </div>

  <!-- QR Modal HTML -->
  <div id="qrModal" class="modal">
    <div class="modal-content">
      <span class="text-end" id="close" onclick="closeModal()">&times;</span>
      <!-- Display the dynamically generated QR code image -->
      <div class="qr-img-container text-center">
        <?php
        // Fetch the QR code image path from the database based on item ID
        $itemId = $_GET['item_id']; // Assuming you have the item ID in the URL parameter
        $sql = "SELECT qrcode_image FROM registered_items WHERE item_id = $itemId";
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
          $row = mysqli_fetch_assoc($result);
          $qrcodeImagePath = $row['qrcode_image'];
          // Display the QR code image
          echo '<img src="' . $qrcodeImagePath . '" alt="QR Code" width="200">';
        } else {
          // Handle error if QR code image is not found
          echo 'QR Code not found';
        }
        ?>
      </div>
      <div class="modal-item-Name text-center">
        <h2><?php echo $item['item_name']; ?></h2>
      </div>
      <div class="modal-exit-btn text-center">
      <a href="<?php echo $qrcodeImagePath; ?>" download="<?php echo $qrcodeImagePath; ?>.png" class="download-qr-btn">Download QR Code</a><br> <!-- Make the QR code downloadable with the default QR code name -->
        <button class="exit-btn" onclick="closeModal()">Exit</button>
      </div>
    </div>
  </div>



<!-- 
  <div class="navbar">
    <a href="user-page.php" class="active" onclick="changeImage('user')"><img src="fi-sr-user.png" alt=""
        class="left-icon" id="user-icon"></a>
    <a href="main-page.php" onclick="changeImage('home')"><img src="fi-rr-home.png" alt="" class="middle-icon"
        id="home-icon"></a>
    <a href="notif-page.php" onclick="changeImage('bell')"><img src="fi-rr-bell.png" alt="" class="right-icon"
        id="bell-icon"></a>
  </div> -->

  <!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0-alpha1/js/bootstrap.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/izimodal/1.6.0/js/iziModal.min.js"></script>
  <script>
    // Function to open the modal
    function openModal() {
      var modal = document.getElementById("qrModal");
      modal.style.display = "block";
    }

    // Function to close the modal
    function closeModal() {
      var modal = document.getElementById("qrModal");
      modal.style.display = "none";
    }
    // Function to open the edit modal
    function openEditModal() {
      var modal = document.getElementById("editModal");
      modal.style.display = "block";
    }

    // Function to close the edit modal
    function closeEditModal() {
      var modal = document.getElementById("editModal");
      modal.style.display = "none";
    }

    // Function to open the delete modal
    function openDeleteModal() {
      var modal = document.getElementById("deleteModal");
      modal.style.display = "block";
    }

    // Function to close the delete modal
    function closeDeleteModal() {
      var modal = document.getElementById("deleteModal");
      modal.style.display = "none";
    }

     // Function to handle the delete operation
     function deleteItem() {
      // Get the item ID
      var itemId = "<?php echo $itemId; ?>";

       $(document).ready(function() {
    // Activate the carousel
    $('#imageCarousel').carousel();
  });

      // Send an AJAX request to delete_item.php
      $.ajax({
        url: "delete_item.php",
        type: "POST",
        data: { item_id: itemId },
        success: function(response) {
          // Check if deletion was successful
          if (response == "success") {
            // Redirect to user page or perform any other action as needed
            window.location.href = "user-page.php";
          } else {
            // Handle error
            alert("Failed to delete item. Please try again.");
          }
        }
      });
    }
  </script>


</body>

</html>
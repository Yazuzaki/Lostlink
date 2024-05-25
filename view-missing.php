<?php
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['SESSION_EMAIL'])) {
  header("Location: login.php");
  exit();
}

// Include your database connection file (e.g., config.php)
include 'config.php';

// Check if item ID is provided in the URL
if (isset($_GET['item_id']) && filter_var($_GET['item_id'], FILTER_VALIDATE_INT)) {
  $itemId = $_GET['item_id'];

  // Fetch item details from the database based on the item ID
  $itemSql = "SELECT * FROM reported_missing WHERE item_id='$itemId'";
  $itemResult = mysqli_query($conn, $itemSql);
  if ($itemResult && mysqli_num_rows($itemResult) == 1) {
    $item = mysqli_fetch_assoc($itemResult);
  } else {
    // Redirect if item not found
    header("Location: user-page.php");
    exit();
  }
} else {
  // Redirect if item ID is not provided in the URL or is invalid
  header("Location: user-page.php");
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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Actor&display=swap" rel="stylesheet">
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

    /* Navbar styles */
    .navbar {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      background-color: #fff;
      color: #fff;
      padding: 22px 0;
      text-align: center;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.7);
      /* Added box-shadow */
    }

    .navbar a {
      color: #fff;
      text-decoration: none;
      margin: 0 10px;
      margin-bottom: 0.5rem;
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

    .item-box {
      overflow-y: auto;
      max-height: calc(100vh - 110px);
      /* Adjust the value as needed */
      /* 260px is the estimated height of other elements on the page */
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
      overflow-wrap: break-word;
      /* Allow long words to break and wrap */
    }

    .modal-item-Name h2 {
      font-size: 15px;
      font-weight: bold;
      margin-left: 0.7rem;
      margin-right: 0.7rem;
      color: #33363896;
    }


    .qr-img-container img {
      width: 250px;
      height: 250px;
      margin-top: 1rem
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

    #closeEditModal {
      font-size: 25px;
    }

    /* Center the "Save changes" button */
    .modal-footer button {
      margin: 0 auto;
      /* Center the button horizontally */
    }

    /* Define the custom focus ring color */
    .form-control:focus {
      outline: none !important;
      box-shadow: none !important;
      border-color: #6200EE;

    }

    .no-btn {
      padding: 10px 50px 10px 50px;
      font-weight: bold;
      border-radius: 25px 25px;
      color: white;
      margin: 0.5rem;


    }

    .yes-btn {
      color: white;
      background-color: #6200EE;
      padding: 10px 50px 10px 50px;
      font-weight: bold;
      border-radius: 25px 25px;
      margin: 0.5rem;

    }


    .delete-p {
      font-family: "Poppins", sans-serif;
      color: black;
      font-size: 12px;
      color: #33363896;
    }

    @media (max-width: 368px) {
      .btn-secondary {
        font-size: 11px;
      }

      .btn-danger {
        font-size: 11px;
      }

      .yes-btn {
        padding: 10px 40px 10px 40px;
      }

      .no-btn {
        padding: 10px 40px 10px 40px;
      }
    }
  </style>
</head>

<body>
  <a href="main-page.php"><img src="back.png" alt="" id="back-icon"></a>

  <div class="container">
    <div class="row justify-content-md-center">
      <div class="col-md-6 item-box">
        <!-- <div class="dropdown text-end">
          <a href="#" role="button" id="moreDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="more_vert.png" alt="" id="more-icon">
          </a>
          <ul class="dropdown-menu" aria-labelledby="moreDropdown">
            Add event listeners to open the edit and delete modals
            <li><a class="dropdown-item edit-item" href="#" onclick="openEditModal()">Edit</a></li>
            <li><a class="dropdown-item delete-item" href="#" onclick="openDeleteModal()">Delete</a></li>
          </ul>
        </div> -->



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
            <button class="carousel-control-prev visually-hidden" type="button" data-bs-target="#imageCarousel"
              data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next visually-hidden" type="button" data-bs-target="#imageCarousel"
              data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>
        </div>

        <!-- Link to trigger the modal -->
      
        <!-- Details Card -->
        <div class="card mb-4 item-detail-card">
          <div class="card-body">
            <p class="item-status">Missing</p>
            <h5 class="card-title"><?php echo $item['item_name']; ?></h5>
            <p class="item-description-title">Description</p>
            <p class="card-text item-description-text"><?php echo $item['item_description']; ?></p>
            <p class="last-seen-title"><img src="location_on.png" alt="" class="location-icon"></i>Last Seen</p>
            <p class="card-text last-seen-text"></i><?php echo $item['last_seen']; ?></p>
            <p class="user-title"><img src="person.png" alt="" class="person-icon">Owner</p>
            <p class="card-text owner-name"><?php echo $item['user_name'];; ?></p>
            <p class="card-text owner-id"><small>User ID: # <?php echo $item["user_id"]; ?></small></p>
          </div>
          <div class="report-btn-container text-center">
          <button class="report-missing-btn" id="reportMissingBtn">Contact Owner</button>
            <script>
  // Function to redirect to the chat page with the owner's user ID
  function redirectToChat() {
    // Get the owner's user ID
    var ownerId = "<?php echo $item['user_name']; ?>";
    
    // Construct the chat page URL with the user ID as a query parameter
    var chatUrl = "http://localhost/LostLink.github.io/chat.php?user=" + ownerId;
    
    // Redirect to the chat page
    window.location.href = chatUrl;
  }

  // Add event listener to the "Contact Owner" button
  document.getElementById("reportMissingBtn").addEventListener("click", redirectToChat);
</script>
          </div>
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
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
            onclick=closeEditModal()></button>
        </div>
        <div class="modal-body">
          <!-- Input fields for editing item details -->
          <div class="mb-3">
            <label for="itemName" class="form-label">Item Name</label>
            <input type="text" class="form-control" id="itemName" value="<?php echo $item['item_name']; ?>">
          </div>
          <div class="mb-3">
            <label for="itemDescription" class="form-label">Item Description</label>
            <textarea class="form-control" id="itemDescription"
              rows="3"><?php echo $item['item_description']; ?></textarea>
          </div>
          <div class="mb-3">
            <label for="lastSeen" class="form-label">Item Name</label>
            <input type="text" class="form-control" id="lastSeen" value="<?php echo $item['last_seen']; ?>">
          </div>
        </div>
        <div class="modal-footer text-center">
          <button type="button" class="save-changes-btn" id="saveChangesBtn">Save changes</button>
        </div>

      </div>
    </div>
  </div>


  <!-- Delete Modal -->
  <div class="modal" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-bottom">
      <div class="modal-content">
        <div class="modal-body">
          <div class="text-center">
            <h5>Are you sure? </h5>
            <p class="delete-p">Do you really want to delete this item? This process cannot be undone.</p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn yes-btn btn-secondary" onclick="deleteItem()">Yes</button>
          <button type="button" class="btn no-btn btn-danger" onclick="closeDeleteModal()">No</button>
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
        <a href="<?php echo $qrcodeImagePath; ?>" download="<?php echo $qrcodeImagePath; ?>.png"
          class="download-qr-btn">Download QR Code</a><br>
        <!-- Make the QR code downloadable with the default QR code name -->
        <button class="exit-btn" onclick="closeModal()">Exit</button>
      </div>
    </div>
  </div>

  <div class="navbar">
    <a href="user-page.php" onclick="changeImage('user')"><img src="fi-rr-user.png" alt="" class="left-icon" id="user-icon"></a>
    <a href="main-page.php" class="active" onclick="changeImage('home')"><img src="fi-sr-home.png" alt="" class="middle-icon" id="home-icon"></a>
    <a href="home.php" onclick="changeImage('bell')"><img src="fi-rr-bell.png" alt="" class="right-icon" id="bell-icon"></a>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
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

      // Send an AJAX request to delete_item.php
      $.ajax({
        url: "delete_item.php",
        type: "POST",
        data: { item_id: itemId },
        success: function (response) {
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

    // Function to handle saving changes to item details
    function saveChanges() {
      // Get the updated values from the input fields
      var itemName = document.getElementById("itemName").value;
      var itemDescription = document.getElementById("itemDescription").value;
      var lastSeen = document.getElementById("lastSeen").value;
      var itemId = "<?php echo $itemId; ?>"; // Get the item ID

      // Send an AJAX request to update_item.php
      $.ajax({
        url: "update_item.php",
        type: "POST",
        data: {
          item_id: itemId,
          item_name: itemName,
          item_description: itemDescription,
          last_seen: lastSeen
        },
        success: function (response) {
          // Check if update was successful
          if (response == "success") {
            // Redirect to user page or perform any other action as needed
            location.reload();
          } else {
            // Handle error
            alert("Failed to update item details. Please try again.");
          }
        }
      });
    }

    // Add event listener to the "Save Changes" button
    document.getElementById("saveChangesBtn").addEventListener("click", saveChanges);

  </script>


</body>

</html>
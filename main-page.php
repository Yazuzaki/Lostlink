<?php
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['SESSION_EMAIL'])) {
  header("Location: login.php");
  exit();
}

// Include your database connection file (e.g., config.php)
include 'config.php';

// Fetch user details from the database based on the logged-in email
$email = $_SESSION['SESSION_EMAIL'];
$sql = "SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);
  $welcome_message = "Welcome, " . $row['name'] . "!";
} else {
  // Redirect to login if user data is not found
  header("Location: login.php");
  exit();
}

// Get missing items from the database
$missingItemsSql = "SELECT * FROM reported_missing";
$missingItemsResult = mysqli_query($conn, $missingItemsSql);
$missingItemsList = [];
if ($missingItemsResult && mysqli_num_rows($missingItemsResult) > 0) {
  while ($missingItemRow = mysqli_fetch_assoc($missingItemsResult)) {
    $missingItemsList[] = $missingItemRow;
  }
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
  overflow: hidden; /* Add this line to make the page not scrollable */
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
      padding: 24px 0;
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
      box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.3);

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

    /* Minimalist search input styles */
    .search-column {
      background-color: #f2f2f2;
      border-radius: 10px;
      padding: 10px;
      margin-top: 20px;
      /* Added margin */
    }

    .search-input {
      width: 100%;
      padding: 10px 40px 10px 10px;
      border: none;
      border-radius: 5px;
      outline: none;
      background-color: transparent;
      color: #333;
    }

    .search-icon {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #666;
    }

    .added-items-box {
      background: #FFFFFF;
      margin-top: 0.2rem;
      margin-left: 0.5rem;
      margin-right: 0.5rem;
      box-shadow: 1px 1px 8px rgba(0, 0, 0, 0.3);
      /* Added box-shadow */
    }

    .registered-items-text {
      color: #00141FCC;
      margin-top: 0.6rem;
      font-size: 24px;

    }

    @media (max-width: 348px) {
      .view-item-btn {
        font-size: 12px;
        padding: ;
      }
    }

    .product-name {
      color: #000;
      font-weight: 600;
      font-size: 20px;
      width: 150px;
      /* Adjust width as needed */
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }


    small {
      color: #333638CC;
      font-weight: bold;
      font-size: 12px;

    }

    .item-posted-date {
      color: #333638CC;
    }

    a {
      font-family: "Poppins", sans-serif;
      text-decoration: underline;
      border-radius: 25px 25px;
      font-size: 13px;
      font-weight: 600;
      color: #6200EE;
      background: none;
      border: none;
      cursor: pointer;
    }

    /* Add this CSS at the end of your <style> block */
    .registered-items-container {
      overflow-y: auto;
      max-height: calc(100vh - 230px);
      /* Adjust the value as needed */
      /* 260px is the estimated height of other elements on the page */
    }
    .btn {
  color: black;
  border: none;
}
.fa-search{
  color:#6200EE;
  font-size:20px;
}


.form-control {
  outline: none !important;
  box-shadow: none !important;
  border: none;
  border-bottom: 1px solid black; /* Default underline color */
}

.form-control:focus {
  border-bottom: 3px solid #6200EE; /* Underline color when input is focused */
}

  </style>
</head>

<body>

  <div class="container mt-5">
  <div class="input-group mb-3">
  <input type="text" class="form-control" id="searchInput" placeholder="Search Item Name" aria-label="Recipient's username" aria-describedby="button-addon2">
  <button class="btn" type="button" id="button-addon2"><i class="fas fa-search"></i>
</button>
<script>
  $(document).ready(function () {
  $('#searchInput').on('input', function () {
    var searchQuery = $(this).val(); // Get the search query from the input field

    $.ajax({
      url: 'search_items.php',
      type: 'POST',
      data: { search_query: searchQuery },
      dataType: 'json',
      success: function (response) {
  if (response.success) {
    // Update the displayed items with the filtered list
    $('.registered-items-container').empty(); // Clear existing items
    response.items.forEach(function (item) {
      // Create a Date object from the posted_date
      var date = new Date(item.posted_date);
      // Format the date using JavaScript's toLocaleString() or any custom formatter
      var formattedDate = date.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
      });

      // Append each item to the container
      var itemHtml = '<div class="rounded-box added-items-box">' +
        '<div class="product-detail d-flex align-items-center justify-content-between">' +
        '<div class="product-info">' +
        '<small>' + formattedDate + '</small>' +
        '<h2 class="product-name">' + item.item_name.substring(0, 15) + '</h2>' +
        '<a href="view-missing.php?item_id=' + item.item_id + '" class="view-item-btn">View Item</a>' +
        '</div>' +
        '<div class="product-image">' +
        '<img src="' + item.item_image + '" alt="Product Image" style="width: 90px; height: 90px; object-fit: cover;">' +
        '</div>' +
        '</div>' +
        '</div>';
      $('.registered-items-container').append(itemHtml);
    });
  } else {
    // Handle no items found or other errors
    console.error(response.message);
  }
},
      error: function (xhr, status, error) {
        console.error(error);
      }
    });
  });
});

  </script>
</div>
   <!-- Second Rounded box -->
<div class="row justify-content-center">
  <div class="col-md-6">
    <h1 class="registered-items-text">Missing Items</h1>
    <!-- Wrap the container with a div and apply styles -->
    <div class="registered-items-container">
      <!-- Loop through each item in $missingItemsList -->
      <?php foreach ($missingItemsList as $missingItem): ?>
        <div class="rounded-box added-items-box">
          <div class="product-detail d-flex align-items-center justify-content-between">
            <div class="product-info">
            <small><?php echo date('M j, Y, h:i a', strtotime($missingItem['posted_date'])); ?></small>
              <h2 class="product-name"><?php echo substr($missingItem['item_name'], 0, 15); ?></h2>
              <a href="view-missing.php?item_id=<?php echo $missingItem['item_id']; ?>" class="view-item-btn">View Item</a>
            </div>
            <div class="product-image">
              <?php
              // Split the item images by comma
              $images = explode(',', $missingItem['item_image']);
              if (!empty($images)) {
                // Output only the first image
                echo '<img src="' . $images[0] . '" alt="Product Image" style="width: 90px; height: 90px; object-fit: cover;">';
              } else {
                // Handle case where no image is available
                echo 'No Image Available';
              }
              ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      <!-- End of item loop -->
    </div>
  </div>
</div>




    <div id="itemModal">
      <div id="modalContent">
        <h2 id="modalUserName"></h2>
        <h2 id="modalItemName"></h2>
        <img id="modalItemImage" src="" alt="Product Image">
        <h2 id="modalItemDescription"></h2>
        <h2 id="modalLastSeen"></h2>
        <h2 id="modalPostedDate"></h2>
      </div>
    </div>

    <script>
      $(document).ready(function () {
        // Initialize iziModal
        $("#itemModal").iziModal({
          headerColor: '#6200EA', // Optional: customize header color
          width: 600, // Optional: set modal width
          padding: 20 // Optional: adjust padding
        });

        // Add event listener to View Item buttons
        $(".view-item-btn").click(function () {
          // Get the item ID
          var itemId = $(this).data('item-id');

          // AJAX request to fetch item details
          $.ajax({
            url: 'fetch_missing.php',
            type: 'POST',
            data: { item_id: itemId },
            dataType: 'json',
            success: function (response) {
              if (response.success) {
                // Populate modal with item details
                $("#modalUserName").text(response.item.user_name);
                $("#modalItemName").text(response.item.item_name);
                $("#modalItemImage").attr('src', response.item.item_image);
                $("#modalItemDescription").text(response.item.item_description);
                $("#modalLastSeen").text(response.item.last_seen);
                $("#modalPostedDate").text(response.item.posted_date);

                // Open the modal
                $("#itemModal").iziModal('open');
              } else {
                // Show error message if item not found or invalid ID
                console.error(response.message);
                // You can add code here to show an error message to the user
              }
            },
            error: function (xhr, status, error) {
              console.error(error);
              // You can add code here to handle AJAX errors
            }
          });
        });
      });

      $(document).ready(function() {
  $('#searchInput').on('focus', function() {
    $(this).css('border-bottom', '1px solid #6200EE');
  });
  $('#searchInput').on('blur', function() {
    $(this).css('border-bottom', '1px solid black');
  });
});

    </script>



  </div>


 <!-- Navbar -->
 <div class="navbar">
    <a href="user-page.php" onclick="changeImage('user')"><img src="fi-rr-user.png" alt="" class="left-icon" id="user-icon"></a>
    <a href="main-page.php" class="active" onclick="changeImage('home')"><img src="fi-sr-home.png" alt="" class="middle-icon" id="home-icon"></a>
    <a href="home.php" onclick="changeImage('bell')"><img src="fi-rr-bell.png" alt="" class="right-icon" id="bell-icon"></a>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  <script src="change-img.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/izimodal/1.6.0/js/iziModal.min.js"></script>
  <script>
    function goToPostingPage() {
      window.location.href = 'posting-page.php'; // Change 'posting-page.php' to the actual URL of your posting page
    }


  </script>

</body>

</html>
<?php
session_start();

# check if the user is logged in
if (isset($_SESSION['SESSION_EMAIL'])) {
    # database connection file
    include '../db.conn.php';

	function getUserByEmail($email, $conn) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    
        // Fetch the user
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $user;
    }
    

    // Get the user_id using the logged-in user's email
    $user_id = getUserByEmail($_SESSION['SESSION_EMAIL'], $conn);

    # check if the key is submitted
    if (isset($_POST['key'])) {
        # creating simple search algorithm :) 
        $key = "%" . $_POST['key'] . "%";

        $sql = "SELECT * FROM users
                WHERE username
                LIKE ? OR name LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$key, $key]);

        if ($stmt->rowCount() > 0) {
            $users = $stmt->fetchAll();

            foreach ($users as $user) {
                // Check if the 'user_id' key exists in the current user array
                if (isset($user['user_id']) && $user['user_id'] != $user_id) {
?>
                <li class="list-group-item">
                    <a href="chat.php?user=<?= $user['username'] ?>"
                       class="d-flex justify-content-between align-items-center p-2">
                        <div class="d-flex align-items-center">
                            <img src="uploads/<?= $user['p_p'] ?>" class="w-10 rounded-circle">
                            <h3 class="fs-xs m-2">
                                <?= $user['name'] ?>
                            </h3>
                        </div>
                    </a>
                </li>
<?php
                }
            }
        } else { ?>
            <div class="alert alert-info text-center">
                <i class="fa fa-user-times d-block fs-big"></i>
                The user "<?= htmlspecialchars($_POST['key']) ?>" is not found.
            </div>
<?php
        }
    }

} else {
    header("Location: ../../index.php");
    exit;
}
?>

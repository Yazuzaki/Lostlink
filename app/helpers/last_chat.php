<?php 

function lastChat($id_1, $id_2, $conn){
    $sql = "SELECT * FROM chats
            WHERE (from_id=:id1 AND to_id=:id2)
            OR    (to_id=:id1 AND from_id=:id2)
            ORDER BY chat_id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['id1' => $id_1, 'id2' => $id_2]);

    $chat = $stmt->fetch();
    // Sanitize the message content to prevent HTML rendering issues
    $message = htmlspecialchars($chat['message'] ?? '');

    // Shorten the message if it's longer than 10 characters
    if (strlen($message) > 25) {
        $message = substr($message, 0, 25) . '...';
    }

    return $message;
}


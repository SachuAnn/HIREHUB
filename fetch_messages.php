<?php
require_once __DIR__ . '/../config.php';
session_start();

if (!isset($_GET['user1']) || !isset($_GET['user2'])) {
    exit("Missing user ids");
}

$user1 = (int)$_GET['user1']; 
$user2 = (int)$_GET['user2'];

$query = "
    SELECT * FROM messages
    WHERE (sender_id = ? AND receiver_id = ?)
       OR (sender_id = ? AND receiver_id = ?)
    ORDER BY sent_at ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("iiii", $user1, $user2, $user2, $user1);
$stmt->execute();
$res = $stmt->get_result();

$messages = [];
while ($row = $res->fetch_assoc()) {
    $messages[] = $row;
}

header("Content-Type: application/json");
echo json_encode($messages);
?>

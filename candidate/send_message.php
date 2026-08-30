<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['candidate_id'])) exit();

$sender = $_SESSION['candidate_id'];
$receiver = $_POST['receiver'];
$message = trim($_POST['message']);

if ($message !== "") {
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, sent_at)
                            VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $sender, $receiver, $message);
    $stmt->execute();
}
?>

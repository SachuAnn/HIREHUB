<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['user_role'])) exit;

$logged_in_id =
    ($_SESSION['user_role'] === 'candidate') ? $_SESSION['candidate_id'] :
    (($_SESSION['user_role'] === 'employer') ? $_SESSION['employer_id'] : 0);

$other_id = $_GET['other_id'] ?? 0;

$stmt = $conn->prepare("
    SELECT * FROM messages
    WHERE (sender_id=? AND receiver_id=?)
       OR (sender_id=? AND receiver_id=?)
    ORDER BY sent_at ASC
");
$stmt->bind_param("iiii", $logged_in_id, $other_id, $other_id, $logged_in_id);
$stmt->execute();
$res = $stmt->get_result();

while ($msg = $res->fetch_assoc()) {
    $class = ($msg['sender_id'] == $logged_in_id) ? 'me' : 'other';
    echo "<div class='msg $class'>" . nl2br(htmlspecialchars($msg['message'])) . "</div>";
}

$stmt->close();
?>

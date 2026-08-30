<?php
session_start();
require_once "config.php";

// Candidate must be logged in
if (!isset($_SESSION['candidate_id'])) {
    die("Unauthorized access.");
}

$candidate_id = $_SESSION['candidate_id'];

// Validate employer_id
if (!isset($_GET['employer_id']) || !is_numeric($_GET['employer_id'])) {
    die("Invalid chat user.");
}

$employer_id = (int)$_GET['employer_id'];

// Send new message
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $msg = trim($_POST['message']);
    if ($msg != "") {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, sent_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $candidate_id, $employer_id, $msg);
        $stmt->execute();
    }
}

// Get employer name
$ename = $conn->prepare("SELECT name FROM users WHERE user_id=?");
$ename->bind_param("i", $employer_id);
$ename->execute();
$employer = $ename->get_result()->fetch_assoc();

// Get chat history
$chat = $conn->prepare("
    SELECT * FROM messages
    WHERE sender_id=? AND receiver_id=?
    ORDER BY sent_at ASC
");
$chat->bind_param("ii", $candidate_id, $employer_id);
$chat->execute();
$messages = $chat->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<title>Chat</title>
<style>
body { 
    font-family: 'Poppins', sans-serif;
    background: #f3f6ff;
}
.chat-box {
    max-width: 600px;
    margin: 40px auto;
    background: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.msg {
    padding: 12px;
    background: #e8f0ff;
    margin-bottom: 12px;
    border-radius: 8px;
}
textarea {
    width: 100%; padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
}
button {
    background: #0078ff;
    color: white;
    padding: 10px 18px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-top: 10px;
}
button:hover {
    background: #005fcc;
}
</style>
</head>
<body>

<div class="chat-box">
    <h2>Chat with <?= htmlspecialchars($employer['name']) ?></h2>

    <?php while($m = $messages->fetch_assoc()): ?>
        <div class="msg">
            <?= htmlspecialchars($m['message']) ?><br>
            <small><?= $m['sent_at'] ?></small>
        </div>
    <?php endwhile; ?>

    <form method="post">
        <textarea name="message" rows="3" required placeholder="Type your message..."></textarea>
        <button type="submit">Send</button>
    </form>
</div>

</body>
</html>

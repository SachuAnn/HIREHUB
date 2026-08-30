<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['candidate_id'])) {
    header("Location: ../login.php");
    exit();
}

$candidate_id = $_SESSION['candidate_id'];
$employer_id = $_GET['employer_id'] ?? 0;

if (!$employer_id) die("Invalid Employer");

$empQ = $conn->prepare("SELECT name FROM users WHERE user_id=?");
$empQ->bind_param("i", $employer_id);
$empQ->execute();
$emp = $empQ->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Chat with Employer</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins','Segoe UI',sans-serif;}

.video-bg{
  position:fixed;top:0;left:0;width:100%;height:100%;
  object-fit:cover;z-index:-3;
}

body::before{
  content:"";position:fixed;inset:0;
  background:linear-gradient(-45deg,rgba(79,140,255,0.6),
           rgba(111,231,221,0.6),rgba(102,126,234,0.6),rgba(118,75,162,0.6));
  background-size:400% 400%;
  animation:gradientMove 12s ease infinite;
  z-index:-2;
}
body::after{
  content:"";position:fixed;inset:0;
  background:rgba(0,0,0,0.45);z-index:-1;
}

@keyframes gradientMove{0%{background-position:0% 50%;}
50%{background-position:100% 50%;}100%{background-position:0% 50%;}}

.chat-container{
  width:60%;margin:70px auto;padding:20px;
  background:rgba(255,255,255,0.9);border-radius:18px;
  box-shadow:0 8px 24px rgba(0,0,0,0.25);
}

.chat-box{
  height:420px;overflow-y:auto;padding:15px;
  background:#fff;border-radius:12px;
}

.msg{
  margin:10px 0;padding:10px 14px;border-radius:10px;
  max-width:70%;font-size:15px;
}

.me{background:#d1eaff;margin-left:auto;color:#003d6b;}
.them{background:#f0f0f0;margin-right:auto;color:#222;}

form{margin-top:15px;display:flex;gap:10px;}
input[type=text]{
  flex:1;padding:12px;border-radius:10px;border:1px solid #ccc;
}

button{
  background:#0078ff;color:#fff;border:none;padding:12px 20px;
  border-radius:10px;cursor:pointer;font-weight:600;
}
button:hover{background:#0060d4;}
</style>
</head>
<body>

<video autoplay muted loop class="video-bg">
  <source src="../background.mp4" type="video/mp4">
</video>

<div class="chat-container">
  <h2>Chat with <?= htmlspecialchars($emp['name']) ?></h2>

  <div id="chatBox" class="chat-box"></div>

  <form id="chatForm">
    <input type="hidden" id="sender" value="<?= $candidate_id ?>">
    <input type="hidden" id="receiver" value="<?= $employer_id ?>">
    <input type="text" id="message" placeholder="Type message..." required>
    <button type="submit">Send</button>
  </form>
</div>

<script>
function loadMessages(){
    let u1 = <?= $candidate_id ?>;
    let u2 = <?= $employer_id ?>;

    fetch(`../common/fetch_messages.php?user1=${u1}&user2=${u2}`)
    .then(res => res.json())
    .then(data => {
        let box = document.getElementById("chatBox");
        box.innerHTML = "";
        data.forEach(m => {
            let div = document.createElement("div");
            div.className = "msg " + (m.sender_id == u1 ? "me" : "them");
            div.textContent = m.message;
            box.appendChild(div);
        });
        box.scrollTop = box.scrollHeight;
    });
}
setInterval(loadMessages, 1000);
loadMessages();

document.getElementById("chatForm").onsubmit = function(e){
    e.preventDefault();
    fetch("send_message.php", {
        method:"POST",
        body:new FormData(this)
    });
    document.getElementById("message").value = "";
};
</script>

</body>
</html>

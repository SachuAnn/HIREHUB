<?php
session_start();

$host = "localhost";
$user = "root";
$pass = ""; 
$dbname = "job_portal";
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT admin_id, password FROM admin WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($aid, $dbpass);
        $stmt->fetch();
        if ($password === $dbpass) {
            $_SESSION['admin_id'] = $aid;
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $msg = "Incorrect password";
        }
    } else {
        $msg = "Admin username not found";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - HireHub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    * {
      margin: 0; padding: 0; box-sizing: border-box;
      font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      height: 100vh;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #333;
    }

    /* ===== Background Gradient + Video ===== */
    video.bg-video {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      object-fit: cover;
      z-index: -3;
    }

    body::before {
      content: "";
      position: fixed;
      inset: 0;
      background: linear-gradient(-45deg, rgba(79,140,255,0.6), rgba(111,231,221,0.6), rgba(102,126,234,0.6), rgba(118,75,162,0.6));
      background-size: 400% 400%;
      animation: gradientMove 12s ease infinite;
      z-index: -2;
    }

    body::after {
      content: "";
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.45);
      z-index: -1;
    }

    @keyframes gradientMove {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* ===== Login Card ===== */
    .login-box {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 16px;
      padding: 40px 36px;
      width: 360px;
      text-align: center;
      box-shadow: 0 8px 32px rgba(0,0,0,0.15);
      backdrop-filter: blur(10px);
      animation: fadeSlideUp 1s ease;
      z-index: 5;
    }

    .login-box h2 {
      color: #0078ff;
      font-size: 1.8rem;
      margin-bottom: 30px;
      font-weight: 700;
      letter-spacing: 0.5px;
    }

    input {
      width: 100%;
      padding: 12px 14px;
      margin: 10px 0;
      border: 1.4px solid #cbd5e1;
      border-radius: 10px;
      font-size: 1rem;
      outline: none;
      transition: 0.2s ease;
    }

    input:focus {
      border-color: #0078ff;
      box-shadow: 0 0 5px rgba(0,120,255,0.3);
    }

    button {
      width: 100%;
      background: #0078ff;
      color: white;
      border: none;
      padding: 13px 0;
      border-radius: 40px;
      font-weight: 600;
      font-size: 1.1rem;
      margin-top: 14px;
      cursor: pointer;
      transition: 0.3s ease;
    }

    button:hover {
      background: #005fcc;
    }

    .msg {
      margin: 12px 0;
      color: crimson;
      font-weight: 500;
    }

    a {
      color: #0078ff;
      text-decoration: none;
      transition: 0.2s ease;
    }

    a:hover {
      text-decoration: underline;
    }

    @keyframes fadeSlideUp {
      0% { opacity: 0; transform: translateY(30px); }
      100% { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

<video autoplay muted loop playsinline class="bg-video">
  <source src="../background.mp4" type="video/mp4">
</video>

<div class="login-box">
  <h2>Admin Login</h2>
  <form method="post" action="">
    <input type="text" name="username" placeholder="Admin Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
    <?php if($msg): ?>
      <div class="msg"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
  </form>
  <div style="margin-top: 14px;">
    <a href="../index.php">← Back to Landing Page</a>
  </div>
</div>

</body>
</html>

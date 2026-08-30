<?php
session_start();
if (!isset($_SESSION['admin_id'])) { 
    header("Location: admin_login.php"); 
    exit; 
}

$host = "localhost"; 
$user = "root"; 
$pass = ""; 
$dbname = "job_portal";
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Connection failed");

// --- Report deletion ---
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $rid = intval($_GET['delete']);
    $conn->query("DELETE FROM spam_reports WHERE report_id=$rid");
    header("Location: spam_reports.php");
    exit();
}

$result = $conn->query("
    SELECT s.*, u.name AS reporter_name
    FROM spam_reports s
    JOIN users u ON s.reporter_id = u.user_id
    ORDER BY s.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Spam Reports - HireHub Admin</title>
<style>
* {margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', 'Segoe UI', sans-serif;}

body {
  color: #fff;
  overflow-x: hidden;
}

/* ==== Video Background ==== */
video.bg-video {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  object-fit: cover;
  z-index: -3;
}

/* ==== Gradient Overlay ==== */
body::before {
  content: "";
  position: fixed;
  inset: 0;
  background: linear-gradient(-45deg, rgba(79,140,255,0.6), rgba(111,231,221,0.6), rgba(102,126,234,0.6), rgba(118,75,162,0.6));
  background-size: 400% 400%;
  animation: gradientMove 12s ease infinite;
  z-index: -2;
}

/* ==== Dark Overlay ==== */
body::after {
  content: "";
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  z-index: -1;
}

@keyframes gradientMove {
  0% {background-position: 0% 50%;}
  50% {background-position: 100% 50%;}
  100% {background-position: 0% 50%;}
}

/* ==== Sidebar ==== */
.sidebar {
  width: 240px;
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(12px);
  position: fixed;
  top: 0; left: 0;
  height: 100%;
  padding-top: 30px;
  color: white;
  z-index: 20;
}

.sidebar h2 {
  text-align: center;
  color: #fff;
  margin-bottom: 40px;
  font-size: 1.6rem;
  font-weight: 700;
}

.sidebar a {
  display: block;
  padding: 12px 20px;
  color: white;
  text-decoration: none;
  margin: 8px 0;
  font-weight: 500;
  transition: background 0.3s ease, transform 0.2s ease;
  border-radius: 8px;
}

.sidebar a:hover {
  background: #0078ff;
  transform: translateX(5px);
}

.sidebar a.active {
  background: #0078ff;
}

/* ==== Content ==== */
.content {
  margin-left: 260px;
  padding: 50px;
  min-height: 100vh;
}

h1 {
  color: #fff;
  font-size: 2.2rem;
  margin-bottom: 20px;
  animation: fadeSlideUp 1s ease forwards;
}

.manage-box {
  background: rgba(255,255,255,0.95);
  border-radius: 16px;
  padding: 30px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.15);
  color: #333;
  animation: fadeSlideUp 1s ease forwards;
}

h2 {
  color: #0078ff;
  margin-bottom: 20px;
  text-align: center;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}

th, td {
  border: 1px solid #e2e7ed;
  padding: 10px 12px;
  text-align: left;
}

th {
  background: #e7f2ff;
  color: #0078ff;
}

a.delete {
  color: crimson;
  font-weight: 600;
  text-decoration: none;
}

a.delete:hover {
  text-decoration: underline;
}

@keyframes fadeSlideUp {
  0% {opacity: 0; transform: translateY(30px);}
  100% {opacity: 1; transform: translateY(0);}
}

@media (max-width: 860px) {
  .sidebar {width: 200px;}
  .content {margin-left: 210px; padding: 30px;}
}
</style>
</head>
<body>

<!-- Video Background -->
<video autoplay muted loop playsinline class="bg-video">
  <source src="../background.mp4" type="video/mp4">
</video>

<!-- Sidebar -->
<div class="sidebar">
  <h2>HireHub Admin</h2>
  <a href="admin_dashboard.php">🏠 Dashboard</a>
  <a href="manage_users.php">👥 Manage Users</a>
  <a href="spam_reports.php" class="active">🚨 Spam Reports</a>
  <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="content">
  <h1>Spam Reports</h1>
  <div class="manage-box">
    <h2>Reported Spam Activities</h2>
    <table>
      <tr>
        <th>ID</th>
        <th>Reporter</th>
        <th>Reason</th>
        <th>Created At</th>
        <th>Action</th>
      </tr>

      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row["report_id"]) ?></td>
        <td><?= htmlspecialchars($row["reporter_name"]) ?></td>
        <td><?= htmlspecialchars($row["reason"]) ?></td>
        <td><?= htmlspecialchars($row["created_at"]) ?></td>
        <td><a href="?delete=<?= $row["report_id"] ?>" class="delete" onclick="return confirm('Delete this report?')">Delete</a></td>
      </tr>
      <?php endwhile; ?>

    </table>
  </div>
</div>

</body>
</html>

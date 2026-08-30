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
if ($conn->connect_error) die("Database connection failed");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Dashboard - HireHub</title>

<style>
  * {margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;}

  body {
    color: #333;
    overflow-x: hidden;
    background: #f9fbfd;
  }

  /* ===== Background Video ===== */
  video.bg-video {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    z-index: -3;
  }

  /* Gradient Overlay Animation */
  body::before {
    content: "";
    position: fixed;
    inset: 0;
    background: linear-gradient(-45deg, rgba(79,140,255,0.6), rgba(111,231,221,0.6), rgba(102,126,234,0.6), rgba(118,75,162,0.6));
    background-size: 400% 400%;
    animation: gradientMove 12s ease infinite;
    z-index: -2;
  }

  /* Dark Overlay */
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

  /* ===== SIDEBAR ===== */
  .sidebar {
    width: 240px;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(12px);
    position: fixed;
    top: 0;
    left: 0;
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

  /* ===== MAIN CONTENT ===== */
  .content {
    margin-left: 260px;
    padding: 50px;
    min-height: 100vh;
    position: relative;
  }

  h1 {
    font-size: 2.4rem;
    font-weight: 700;
    color: white;
    margin-bottom: 10px;
    animation: fadeSlideUp 1s ease forwards;
  }

  p {
    font-size: 1.1rem;
    color: #f0f0f0;
    max-width: 650px;
    margin-bottom: 30px;
    animation: fadeSlideUp 1s ease forwards;
    animation-delay: 0.3s;
  }

  /* ===== QUICK LINKS ===== */
  .quick-links {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 40px;
    animation: fadeSlideUp 1s ease forwards;
    animation-delay: 0.4s;
  }

  .quick-links a {
    background: white;
    color: #0078ff;
    padding: 12px 26px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
  }

  .quick-links a:hover {
    background: #e3f0ff;
    transform: translateY(-3px);
  }

  .dashboard-box {
    background: rgba(255,255,255,0.95);
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    padding: 25px;
    backdrop-filter: blur(8px);
    color: #333;
  }

  .dashboard-box h2 {
    color: #0078ff;
    margin-bottom: 15px;
  }

  @keyframes fadeSlideUp {
    0% { opacity: 0; transform: translateY(30px); }
    100% { opacity: 1; transform: translateY(0); }
  }

  @media (max-width: 860px) {
    .sidebar { width: 200px; }
    .content { margin-left: 210px; padding: 30px; }
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
  <a href="spam_reports.php">🚨 Spam Reports</a>
  <a href="logout.php">🚪 Logout</a>
</div>

<!-- Content -->
<div class="content">
  <h1>Welcome, Admin 👋</h1>
  <p>Monitor, manage, and maintain the HireHub platform efficiently from your dashboard.</p>

  <div class="quick-links">
    <a href="manage_users.php">Manage Users</a>
    <a href="spam_reports.php">View Spam Reports</a>
  </div>

  <div class="dashboard-box">
    <h2>Dashboard Overview</h2>
    <p>This section can be expanded later to show key statistics, such as the number of registered users, job posts, or reports received. Use the links above to get started.</p>
  </div>
</div>

</body>
</html>

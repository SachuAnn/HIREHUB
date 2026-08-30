<?php 
session_start();
include '../config.php';

// Ensure candidate is logged in
if (!isset($_SESSION['candidate_id']) || $_SESSION['user_role'] !== 'candidate') {
    header("Location: ../login.php");
    exit();
}

$candidate_id = (int) $_SESSION['candidate_id'];

// Fetch jobs list for candidate
$query = "SELECT job_id, title, description, location, salary, created_at 
          FROM jobs ORDER BY job_id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Candidate Dashboard - HireHub</title>

<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    overflow-x: hidden;
    background: #f9fbfd;
  }

  /* ===== SIDEBAR ===== */
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
    transition: 0.3s ease;
    border-radius: 8px;
  }

  .sidebar a:hover { background: #0078ff; transform: translateX(5px); }

  /* ===== CONTENT ===== */
  .content {
    margin-left: 260px;
    padding: 40px;
    position: relative;
    min-height: 100vh;
    z-index: 5;
  }

  /* ===== VIDEO BG + GRADIENT ===== */
  .video-bg {
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
    background: linear-gradient(-45deg,
      rgba(79,140,255,0.6),
      rgba(111,231,221,0.6),
      rgba(102,126,234,0.6),
      rgba(118,75,162,0.6)
    );
    background-size: 400% 400%;
    animation: gradientMove 12s ease infinite;
    z-index: -2;
  }

  body::after {
    content: "";
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: -1;
  }

  @keyframes gradientMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  /* DASHBOARD TEXT */
  h1 {
    font-size: 2.4rem;
    font-weight: 700;
    color: white;
    margin-bottom: 10px;
  }

  p.lead {
    font-size: 1.05rem;
    color: #f0f0f0;
    margin-bottom: 18px;
  }

  /* QUICK ACTION BUTTONS */
  .quick-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 30px;
  }

  .quick-actions a {
    background: white;
    color: #0078ff;
    padding: 12px 20px;
    border-radius: 26px;
    text-decoration: none;
    font-weight: 600;
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
  }

  .quick-actions a:hover {
    background: #eaf3ff;
    transform: translateY(-3px);
  }

  /* FULL-WIDTH JOB TABLE */
  .job-table {
    background: rgba(255,255,255,0.95);
    border-radius: 12px;
    padding: 18px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    backdrop-filter: blur(6px);
    width: 100%;
  }

  .job-table h2 { color:#0078ff; margin-bottom:12px; }

  table { width:100%; border-collapse: collapse; }
  th, td { padding:10px 12px; border-bottom:1px solid #e8edf6; text-align:left; }
  th { background:#eef5ff; color:#0078ff; font-weight:600; }

  @media (max-width: 980px) {
    .sidebar { width:200px; }
    .content { margin-left:210px; padding:20px; }
  }
</style>
</head>
<body>

<!-- VIDEO BACKGROUND -->
<video autoplay muted loop playsinline class="video-bg">
  <source src="../background.mp4" type="video/mp4">
</video>

<!-- SIDEBAR -->
<div class="sidebar">
  <h2>HireHub</h2>
  <a href="candidate_dashboard.php">🏠 Dashboard</a>
  <a href="my_applications.php">📝 My Applications</a>
  <a href="search_jobs.php">🔍 Search Jobs</a>
  <a href="update_profile.php">👤 Update Profile</a>
  <a href="apply_job.php">📩 Apply Job</a>
  <a href="upload_resume.php">📄 Upload Resume</a>
  <a href="report_spam.php">🚨 Report Spam</a>
  <a href="logout.php">🚪 Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="content">
  <h1>Welcome, <?= htmlspecialchars($_SESSION['candidate_name']); ?> 👋</h1>
  <p class="lead">Find your next career opportunity.</p>

  <div class="quick-actions">
    <a href="apply_job.php">Apply Job</a>
    <a href="my_applications.php">View Applications</a>
    <a href="search_jobs.php">Search Jobs</a>
    <a href="update_profile.php">Update Profile</a>
    <a href="upload_resume.php">Upload Resume</a>
  </div>

  <!-- FULL WIDTH JOB LIST -->
  <div class="job-table">
    <h2>Available Jobs</h2>
    <table>
      <tr>
        <th>Title</th>
        <th>Description</th>
        <th>Location</th>
        <th>Salary</th>
        <th>Posted On</th>
        <th>Action</th>
      </tr>

      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($job = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($job['title']); ?></td>
          <td><?= htmlspecialchars(substr($job['description'], 0, 50)); ?>...</td>
          <td><?= htmlspecialchars($job['location']); ?></td>
          <td><?= htmlspecialchars($job['salary']); ?></td>
          <td><?= htmlspecialchars($job['created_at']); ?></td>
          <td><a href="apply_job.php?job_id=<?= (int)$job['job_id'] ?>">Apply</a></td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="6" style="text-align:center;">No jobs available at the moment.</td></tr>
      <?php endif; ?>

    </table>
  </div>

</div>

</body>
</html>

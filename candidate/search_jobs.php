<?php
session_start();
if (!isset($_SESSION['candidate_id']) || $_SESSION['user_role'] !== 'candidate') {
    header("Location: ../login.php");
    exit;
}

require_once '../config.php';

// Safe input fetch and trim
$keyword = trim($_GET['keyword'] ?? '');

$sql = "
    SELECT job_id, title AS job_title, 
           (SELECT company_name FROM employer_profiles WHERE employer_id = jobs.employer_id) AS company, 
           location, created_at AS posted_at
    FROM jobs 
    WHERE title LIKE ? 
       OR EXISTS (SELECT 1 FROM employer_profiles WHERE employer_id = jobs.employer_id AND company_name LIKE ?)
       OR location LIKE ? 
    ORDER BY created_at DESC
";

$stmt = $conn->prepare($sql);
$kw = "%$keyword%";
$stmt->bind_param("sss", $kw, $kw, $kw);
$stmt->execute();
$stmt->bind_result($id, $title, $company, $loc, $posted);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Search Jobs - HireHub</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

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
      padding: 40px;
      position: relative;
      min-height: 100vh;
      z-index: 5;
    }

    /* ===== VIDEO BACKGROUND ===== */
    .video-bg {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
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

    /* ===== SEARCH FORM ===== */
    .search-container {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      padding: 30px;
      margin-bottom: 30px;
      backdrop-filter: blur(8px);
      animation: fadeSlideUp 1s ease;
    }

    .search-container h2 {
      color: #0078ff;
      margin-bottom: 20px;
    }

    .search-container form {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    input[type="text"] {
      flex: 1;
      min-width: 250px;
      padding: 10px 14px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 1rem;
    }

    button {
      background: #0078ff;
      border: none;
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
      transition: 0.3s ease;
    }

    button:hover {
      background: #005fcc;
    }

    /* ===== JOB TABLE ===== */
    .job-table {
      background: rgba(255,255,255,0.95);
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      overflow-x: auto;
      padding: 20px;
      backdrop-filter: blur(8px);
      animation: fadeSlideUp 1s ease;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px 14px;
      border-bottom: 1px solid #e0e0e0;
      text-align: left;
      color: #333;
    }

    th {
      background: #eef5ff;
      color: #0078ff;
      font-weight: 600;
    }

    td a {
      color: #0078ff;
      text-decoration: none;
      font-weight: 600;
    }

    td a:hover {
      text-decoration: underline;
    }

    @keyframes fadeSlideUp {
      0% { opacity: 0; transform: translateY(30px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 860px) {
      .sidebar { width: 200px; }
      .content { margin-left: 210px; padding: 20px; }
      table { font-size: 0.85rem; }
    }
  </style>
</head>

<body>

<!-- Video Background -->
<video autoplay muted loop playsinline class="video-bg">
  <source src="../background.mp4" type="video/mp4">
</video>

<!-- Sidebar -->
<div class="sidebar">
  <h2>HireHub</h2>
  <a href="candidate_dashboard.php">🏠 Dashboard</a>
  <a href="my_applications.php">📝 My Applications</a>
  <a href="search_jobs.php">🔍 Search Jobs</a>
  <a href="update_profile.php" >👤 Update Profile</a>
  <a href="apply_job.php">📩 Apply Job</a>
  <a href="upload_resume.php">📄 Upload Resume</a>
  <a href="report_spam.php">🚨 Add Spam Report</a>
  <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="content">
  <div class="search-container">
    <h2>Search Jobs</h2>
    <form method="get">
      <input type="text" name="keyword" placeholder="Job title, company, or location" value="<?php echo htmlspecialchars($keyword ?? ''); ?>">
      <button type="submit">Search</button>
    </form>
  </div>

  <div class="job-table">
    <table>
      <tr>
        <th>Title</th>
        <th>Company</th>
        <th>Location</th>
        <th>Posted On</th>
        <th>Action</th>
      </tr>
      <?php
      $found = false;
      while ($stmt->fetch()):
          $found = true; ?>
          <tr>
            <td><?php echo htmlspecialchars($title ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($company ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($loc ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($posted ?? ''); ?></td>
            <td><a href="apply_job.php?job_id=<?php echo urlencode($id); ?>">Apply</a></td>
          </tr>
      <?php endwhile;
      if (!$found): ?>
        <tr><td colspan="5">No jobs found for the given keyword.</td></tr>
      <?php endif; ?>
    </table>
  </div>
</div>

<?php $stmt->close(); $conn->close(); ?>
</body>
</html>

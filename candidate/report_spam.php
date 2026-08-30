<?php
session_start();
require_once '../config.php';

// Ensure candidate is logged in
if (!isset($_SESSION['candidate_id']) || $_SESSION['user_role'] !== 'candidate') {
    header("Location: ../login.php");
    exit();
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $candidate_id = $_SESSION['candidate_id'];
    $reason = trim($_POST['reason']);

    // If you want job ID or employer ID, capture here:
    $reported_job_id = 0; 
    $reported_user_id = 0;

    if (!empty($reason)) {
        $stmt = $conn->prepare("
            INSERT INTO spam_reports (reporter_id, reported_job_id, reported_user_id, reason, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("iiis", $candidate_id, $reported_job_id, $reported_user_id, $reason);

        if ($stmt->execute()) {
            $msg = "✅ Spam report submitted successfully!";
        } else {
            $msg = "❌ Something went wrong. Please try again.";
        }

        $stmt->close();
    } else {
        $msg = "⚠️ Please enter spam details before submitting.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Report Spam - HireHub</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
/* ---- SAME UI STYLE AS YOUR FEEDBACK PAGE ---- */
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
  font-family: 'Poppins', sans-serif;
  color: #333;
  overflow-x: hidden;
}

/* Sidebar */
.sidebar {
  width: 240px;
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(12px);
  position: fixed;
  height: 100%;
  padding-top: 30px;
  color: white;
  z-index: 20;
}
.sidebar h2 { text-align: center; margin-bottom: 40px; font-size: 1.6rem; }
.sidebar a {
  display: block; padding: 12px 20px; color: white;
  text-decoration: none; margin: 8px 0; border-radius: 8px;
}
.sidebar a:hover, .sidebar a.active { background: #0078ff; }

/* Background video */
.video-bg {
  position: fixed; width: 100%; height: 100%;
  object-fit: cover; z-index: -3;
}

/* Gradient overlay */
body::before {
  content: ""; position: fixed; inset: 0;
  background: linear-gradient(-45deg, rgba(79,140,255,0.6), rgba(111,231,221,0.6), rgba(102,126,234,0.6), rgba(118,75,162,0.6));
  background-size: 400% 400%;
  animation: gradientMove 12s ease infinite;
  z-index: -2;
}

/* Dark overlay */
body::after {
  content: ""; position: fixed; inset: 0;
  background: rgba(0, 0, 0, 0.45); z-index: -1;
}

@keyframes gradientMove {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

/* Content */
.content { margin-left: 260px; padding: 50px; min-height: 100vh; }

.report-box {
  background: rgba(255,255,255,0.95);
  padding: 40px; margin: 80px auto;
  max-width: 700px; border-radius: 16px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}

.report-box h2 {
  color: #0078ff; text-align: center;
  margin-bottom: 20px;
}

textarea {
  width: 100%; padding: 12px; min-height: 150px;
  border: 1px solid #ccc; border-radius: 10px; resize: vertical;
}

button {
  background: #ff3b3b; color: white;
  border: none; padding: 12px 25px;
  width: 100%; font-size: 1rem; font-weight: 600;
  border-radius: 30px; cursor: pointer;
  margin-top: 20px;
}

button:hover { background: #d32f2f; }

.msg {
  margin-top: 20px; padding: 12px;
  border-radius: 8px; text-align: center;
  font-weight: 600;
}

.success { background: #e7f9ed; color: #2e7d32; }
.error { background: #fdecea; color: #c62828; }
</style>
</head>

<body>

<video autoplay muted loop class="video-bg">
  <source src="../background.mp4" type="video/mp4">
</video>

<!-- Sidebar -->
<div class="sidebar">
  <h2>HireHub</h2>
  <a href="candidate_dashboard.php">🏠 Dashboard</a>
  <a href="my_applications.php">📝 My Applications</a>
  <a href="search_jobs.php">🔍 Search Jobs</a>
  <a href="update_profile.php">👤 Update Profile</a>
  <a href="apply_job.php">📩 Apply Job</a>
  <a href="upload_resume.php">📄 Upload Resume</a>
  <a href="report_spam.php">🚨 Add Spam Report</a>
  <a href="logout.php">🚪 Logout</a>
</div>
<!-- Main Content -->
<div class="content">
  <div class="report-box">
    <h2>Report Spam / Fraud</h2>

    <?php if ($msg): ?>
      <div class="msg <?= strpos($msg, '✅') !== false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($msg) ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <label>Describe the spam incident</label>
      <textarea name="reason" placeholder="Describe the spam incident, fake job offer, suspicious employer, fraudulent activity..." required></textarea>
      <button type="submit">Submit Report</button>
    </form>
  </div>
</div>

</body>
</html>

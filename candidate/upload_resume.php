<?php
session_start();

if (!isset($_SESSION['candidate_id'])) {
    header("Location: ../candidate_login.php");
    exit;
}

require_once "../config.php";

$candidate_id = $_SESSION['candidate_id'];
$msg = '';
$resume_path = '';

// ✅ Fetch existing resume if available
$stmt = $conn->prepare("SELECT resume FROM candidate_profiles WHERE candidate_id = ?");
$stmt->bind_param("i", $candidate_id);
$stmt->execute();
$stmt->bind_result($resume_path);
$stmt->fetch();
$stmt->close();

// ✅ Handle Resume Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['resume'])) {
    $target_dir = __DIR__ . '/resumes/';

    // Create directory if not exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $original_name = basename($_FILES["resume"]["name"]);
    $filetype = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    $unique_name = uniqid() . "_" . $original_name;
    $target_file = $target_dir . $unique_name;
    $db_path = 'candidate/resumes/' . $unique_name; // ✅ relative path for web access

    if ($filetype !== "pdf") {
        $msg = "❌ Only PDF files are allowed!";
    } elseif (move_uploaded_file($_FILES["resume"]["tmp_name"], $target_file)) {
        // ✅ Save new resume path
        $stmt = $conn->prepare("UPDATE candidate_profiles SET resume=? WHERE candidate_id=?");
        $stmt->bind_param("si", $db_path, $candidate_id);
        if ($stmt->execute()) {
            $msg = "✅ Resume uploaded successfully!";
            $resume_path = $db_path;
        } else {
            $msg = "❌ Database error while saving resume.";
        }
        $stmt->close();
    } else {
        $msg = "❌ Failed to upload resume.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Upload Resume - HireHub</title>
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
    padding: 50px;
    position: relative;
    min-height: 100vh;
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

  /* ===== UPLOAD FORM ===== */
  .upload-box {
    background: rgba(255,255,255,0.95);
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    max-width: 600px;
    margin: 80px auto;
    padding: 40px;
    text-align: center;
    backdrop-filter: blur(8px);
    animation: fadeSlideUp 1s ease forwards;
  }

  .upload-box h2 {
    color: #0078ff;
    margin-bottom: 20px;
  }

  .upload-box input[type="file"] {
    display: block;
    margin: 20px auto;
    padding: 10px;
    border: 2px dashed #0078ff;
    border-radius: 10px;
    width: 80%;
    background: #f9fbfd;
    cursor: pointer;
  }

  .upload-box button {
    background: #0078ff;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 25px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 10px;
  }

  .upload-box button:hover {
    background: #005fcc;
    transform: translateY(-2px);
  }

  .msg {
    margin-top: 20px;
    font-weight: 500;
    color: #333;
    padding: 10px;
    border-radius: 8px;
  }

  .msg.success { background: #e6f7ee; color: #2e7d32; }
  .msg.error { background: #fdecea; color: #c62828; }

  /* Resume Preview */
  .resume-view {
    margin-top: 25px;
    text-align: center;
  }

  .resume-view a {
    display: inline-block;
    background: #00b894;
    color: white;
    padding: 10px 18px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
  }

  .resume-view a:hover {
    background: #019870;
  }

  @keyframes fadeSlideUp {
    0% { opacity: 0; transform: translateY(30px); }
    100% { opacity: 1; transform: translateY(0); }
  }

  @media (max-width: 860px) {
    .sidebar { width: 200px; }
    .content { margin-left: 210px; padding: 20px; }
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
  <a href="search_jobs.php"class="active">🔍 Search Jobs</a>
  <a href="update_profile.php">👤 Update Profile</a>
  <a href="apply_job.php">📩 Apply Job</a>
  <a href="upload_resume.php">📄 Upload Resume</a>
  <a href="report_spam.php">🚨 Add Spam Report</a>
  <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="content">
  <div class="upload-box">
    <h2>Upload Resume (PDF Only)</h2>
    <form method="post" enctype="multipart/form-data">
      <input type="file" name="resume" accept=".pdf" required>
      <button type="submit">Upload Resume</button>
    </form>

    <?php if ($msg): ?>
      <div class="msg <?= strpos($msg, '✅') !== false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($msg) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($resume_path)): ?>
      <div class="resume-view">
        <a href="../<?= htmlspecialchars($resume_path) ?>" target="_blank">📄 View Uploaded Resume</a>
      </div>
    <?php endif; ?>
  </div>
</div>

</body>
</html>

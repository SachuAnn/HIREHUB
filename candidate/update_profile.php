<?php
session_start();
require_once '../config.php';

// Ensure candidate is logged in
if (!isset($_SESSION['candidate_id']) || $_SESSION['user_role'] !== 'candidate') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['candidate_id'];
$msg = '';

// Fetch candidate profile
$stmt = $conn->prepare("
    SELECT c.candidate_id, u.name, u.email, c.skills, c.location, c.experience, c.age
    FROM users u
    JOIN candidate_profiles c ON u.user_id = c.user_id
    WHERE u.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($candidate_id, $name, $email, $skills, $location, $experience, $age);
$found = $stmt->fetch();
$stmt->close();

// If profile doesn't exist, create a blank one
if (!$found) {
    $cstmt = $conn->prepare("
        INSERT INTO candidate_profiles (user_id, age, resume, skills, experience, location)
        VALUES (?, 18, '', '', '', '')
    ");
    $cstmt->bind_param("i", $user_id);
    $cstmt->execute();
    $cstmt->close();

    $stmt = $conn->prepare("
        SELECT c.candidate_id, u.name, u.email, c.skills, c.location, c.experience, c.age
        FROM users u
        JOIN candidate_profiles c ON u.user_id = c.user_id
        WHERE u.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($candidate_id, $name, $email, $skills, $location, $experience, $age);
    $stmt->fetch();
    $stmt->close();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = $_POST['name'] ?? $name;
    $new_email = $_POST['email'] ?? $email;
    $new_skills = $_POST['skills'] ?? $skills;
    $new_location = $_POST['location'] ?? $location;
    $new_experience = $_POST['experience'] ?? $experience;
    $new_age = $_POST['age'] ?? $age;

    $conn->begin_transaction();

    $stmt1 = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE user_id = ?");
    $stmt1->bind_param("ssi", $new_name, $new_email, $user_id);
    $success1 = $stmt1->execute();
    $stmt1->close();

    $stmt2 = $conn->prepare("
        UPDATE candidate_profiles 
        SET skills = ?, location = ?, experience = ?, age = ?
        WHERE user_id = ?
    ");
    $stmt2->bind_param("ssssi", $new_skills, $new_location, $new_experience, $new_age, $user_id);
    $success2 = $stmt2->execute();
    $stmt2->close();

    if ($success1 && $success2) {
        $conn->commit();
        $msg = "✅ Profile updated successfully!";
        $name = $new_name;
        $email = $new_email;
        $skills = $new_skills;
        $location = $new_location;
        $experience = $new_experience;
        $age = $new_age;
    } else {
        $conn->rollback();
        $msg = "❌ Failed to update profile.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update Profile - HireHub</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  * {
    margin: 0; padding: 0; box-sizing: border-box;
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

  /* ===== VIDEO BACKGROUND ===== */
  .video-bg {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    z-index: -3;
  }

  /* Gradient Overlay */
  body::before {
    content: "";
    position: fixed; inset: 0;
    background: linear-gradient(-45deg, rgba(79,140,255,0.6), rgba(111,231,221,0.6), rgba(102,126,234,0.6), rgba(118,75,162,0.6));
    background-size: 400% 400%;
    animation: gradientMove 12s ease infinite;
    z-index: -2;
  }

  /* Dark Overlay */
  body::after {
    content: "";
    position: fixed; inset: 0;
    background: rgba(0, 0, 0, 0.45);
    z-index: -1;
  }

  @keyframes gradientMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  /* ===== MAIN CONTENT ===== */
  .content {
    margin-left: 260px;
    padding: 50px;
    position: relative;
    min-height: 100vh;
  }

  .profile-box {
    background: rgba(255,255,255,0.95);
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    max-width: 700px;
    margin: 60px auto;
    padding: 40px;
    backdrop-filter: blur(8px);
    animation: fadeSlideUp 1s ease forwards;
  }

  .profile-box h2 {
    color: #0078ff;
    margin-bottom: 20px;
    text-align: center;
  }

  label {
    display: block;
    margin-top: 15px;
    font-weight: 500;
    color: #333;
  }

  input[type="text"], input[type="email"], input[type="number"], textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    margin-top: 6px;
    font-size: 1rem;
  }

  textarea {
    resize: vertical;
    min-height: 70px;
  }

  button {
    background: #0078ff;
    color: white;
    border: none;
    padding: 12px 28px;
    border-radius: 30px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 20px;
  }

  button:hover {
    background: #005fcc;
    transform: translateY(-2px);
  }

  .msg {
    margin-top: 20px;
    padding: 12px;
    border-radius: 8px;
    text-align: center;
    font-weight: 500;
  }

  .msg.success { background: #e6f7ee; color: #2e7d32; }
  .msg.error { background: #fdecea; color: #c62828; }

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
  <a href="search_jobs.php">🔍 Search Jobs</a>
  <a href="update_profile.php">👤 Update Profile</a>
  <a href="apply_job.php">📩 Apply Job</a>
  <a href="upload_resume.php">📄 Upload Resume</a>
  <a href="report_spam.php">🚨 Add Spam Report</a>
  <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="content">
  <div class="profile-box">
    <h2>Update Your Profile</h2>

    <?php if ($msg): ?>
      <div class="msg <?= strpos($msg, '✅') !== false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($msg) ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <label>Name</label>
      <input type="text" name="name" value="<?= htmlspecialchars($name ?? '') ?>" required>

      <label>Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>

      <label>Skills</label>
      <textarea name="skills"><?= htmlspecialchars($skills ?? '') ?></textarea>

      <label>Location</label>
      <input type="text" name="location" value="<?= htmlspecialchars($location ?? '') ?>">

      <label>Experience</label>
      <textarea name="experience"><?= htmlspecialchars($experience ?? '') ?></textarea>

      <label>Age</label>
      <input type="number" name="age" value="<?= htmlspecialchars($age ?? '') ?>">

      <button type="submit">Update Profile</button>
    </form>
  </div>
</div>

</body>
</html>

<?php
// /candidate/view_reviews_candidate.php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['candidate_id']) || ($_SESSION['user_role'] ?? '') !== 'candidate') {
    header("Location: ../login.php");
    exit();
}

$candidate_id = (int)$_SESSION['candidate_id'];

$q = $conn->prepare("
    SELECT r.*, j.title AS job_title, u.name AS employer_name
    FROM reviews r
    LEFT JOIN jobs j ON r.job_id = j.job_id
    LEFT JOIN users u ON r.employer_id = u.user_id
    WHERE r.candidate_id = ?
    ORDER BY r.created_at DESC
");
$q->bind_param("i", $candidate_id);
$q->execute();
$res = $q->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Reviews — HireHub</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Poppins',sans-serif;overflow-x:hidden}.video-bg{position:fixed;top:0;left:0;width:100%;height:100%;object-fit:cover;z-index:-3}body::before{content:"";position:fixed;inset:0;background:linear-gradient(-45deg, rgba(79,140,255,0.6), rgba(111,231,221,0.6), rgba(102,126,234,0.6), rgba(118,75,162,0.6));background-size:400% 400%;animation:gradientMove 12s ease infinite;z-index:-2}body::after{content:"";position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:-1}@keyframes gradientMove{0%{background-position:0%50%}50%{background-position:100%50%}100%{background-position:0%50%}} .sidebar{width:240px;background:rgba(0,0,0,0.7);backdrop-filter:blur(12px);position:fixed;top:0;left:0;height:100%;padding-top:30px;color:white;z-index:20} .sidebar a{display:block;padding:12px 20px;color:white;text-decoration:none;margin:8px 0} .content{margin-left:260px;padding:40px;color:white} .box{background:rgba(255,255,255,0.95);padding:24px;border-radius:12px;max-width:1000px;margin:auto;color:#0a2540} table{width:100%;border-collapse:collapse} th{background:#eef5ff;color:#0078ff;padding:12px;text-align:left} td{padding:12px;border-bottom:1px solid #e6e9ef}
</style>
</head>
<body>
<video autoplay muted loop class="video-bg"><source src="../background.mp4" type="video/mp4"></video>
<div class="sidebar">
  <h2>HireHub</h2>
  <a href="candidate_dashboard.php">🏠 Dashboard</a>
  <a href="my_applications.php">📝 My Applications</a>
  <a class="active" href="view_reviews_candidate.php">⭐ My Reviews</a>
  <a href="logout.php">🚪 Logout</a>
</div>

<div class="content">
  <h1 style="color:white;margin-bottom:12px">Your Reviews</h1>
  <div class="box">
    <?php if ($res->num_rows === 0): ?>
      <p>No reviews submitted yet.</p>
    <?php else: ?>
      <table>
        <tr><th>Job</th><th>Employer</th><th>Rating</th><th>Comment</th><th>Posted On</th></tr>
        <?php while($r = $res->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($r['job_title'] ?? '—') ?></td>
            <td><?= htmlspecialchars($r['employer_name'] ?? '—') ?></td>
            <td><?= intval($r['rating']) ?> ⭐</td>
            <td><?= htmlspecialchars($r['comment']) ?></td>
            <td><?= htmlspecialchars($r['created_at']) ?></td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>

<?php
session_start();
require_once __DIR__ . '/../config.php';

// Validate session
if (!isset($_SESSION['candidate_id']) || $_SESSION['user_role'] !== 'candidate') {
    header("Location: ../login.php");
    exit();
}

$candidate_id = (int) $_SESSION['candidate_id'];

// Fetch applications
$query = "
    SELECT 
        a.application_id, a.status, a.applied_at,
        a.job_id,
        j.title, j.location, j.employer_id,
        u.name AS employer_name
    FROM applications a
    JOIN jobs j ON a.job_id = j.job_id
    JOIN users u ON j.employer_id = u.user_id
    WHERE a.candidate_id = ?
    ORDER BY a.applied_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $candidate_id);
$stmt->execute();
$applicationsResult = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>My Applications - HireHub</title>

<style>
/* SAME STYLE YOU USED */
*{margin:0;padding:0;box-sizing:border-box;}
body{
  font-family:'Poppins',sans-serif;background:#f9fbfd;overflow-x:hidden;
}
.video-bg{
  position:fixed;top:0;left:0;width:100%;height:100%;object-fit:cover;z-index:-3;
}
body::before{
  content:"";position:fixed;inset:0;
  background:linear-gradient(-45deg,rgba(79,140,255,0.6),
  rgba(111,231,221,0.6),rgba(102,126,234,0.6),rgba(118,75,162,0.6));
  background-size:400%400%;animation:gradientMove 12s ease infinite;z-index:-2;
}
@keyframes gradientMove{
  0%{background-position:0% 50%;}
  50%{background-position:100% 50%;}
  100%{background-position:0% 50%;}
}
body::after{
  content:"";position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:-1;
}
.sidebar{
  width:240px;background:rgba(0,0,0,0.7);backdrop-filter:blur(12px);
  position:fixed;height:100%;padding-top:30px;color:white;z-index:20;
}
.sidebar h2{text-align:center;color:white;margin-bottom:40px;font-size:1.6rem;}
.sidebar a{
  display:block;padding:12px 20px;margin:8px 0;color:white;
  text-decoration:none;border-radius:8px;transition:0.3s;
}
.sidebar a:hover,.sidebar a.active{background:#0078ff;transform:translateX(5px);}
.content{margin-left:260px;padding:50px;}
.applications-box{
  background:rgba(255,255,255,0.95);padding:40px;border-radius:16px;
  max-width:1100px;margin:auto;box-shadow:0 8px 24px rgba(0,0,0,0.1);
}
table{
  width:100%;border-collapse:collapse;margin-top:20px;background:white;
  border-radius:10px;overflow:hidden;
}
th{
  background:#0078ff;color:white;padding:12px;font-size:15px;
}
td{
  padding:12px;border-bottom:1px solid #eee;font-size:14px;color:#333;
}
.status{padding:6px 10px;border-radius:6px;font-weight:bold;}
.pending{background:#fef3c7;color:#92400e;}
.accepted{background:#dcfce7;color:#166534;}
.rejected{background:#fee2e2;color:#991b1b;}

.chat-btn{
  background:#22c55e;padding:7px 14px;border-radius:8px;color:white;
  font-weight:600;text-decoration:none;display:inline-block;margin-bottom:6px;
}
.chat-btn:hover{background:#16a34a;}

.review-btn{
  background:#f97316;padding:7px 14px;border-radius:8px;color:white;
  font-weight:600;text-decoration:none;display:inline-block;
}
.review-btn:hover{background:#ea580c;}
</style>
</head>

<body>

<!-- Background Video -->
<video autoplay muted loop playsinline class="video-bg">
  <source src="../background.mp4" type="video/mp4">
</video>

<!-- Sidebar -->
<div class="sidebar">
  <h2>HireHub</h2>
  <a href="candidate_dashboard.php">🏠 Dashboard</a>
  <a href="my_applications.php" class="active">📝 My Applications</a>
  <a href="search_jobs.php">🔍 Search Jobs</a>
  <a href="update_profile.php">👤 Update Profile</a>
  <a href="apply_job.php">📩 Apply Job</a>
  <a href="upload_resume.php">📄 Upload Resume</a>
  <a href="report_spam.php">🚨 Report Spam</a>
  <a href="logout.php">🚪 Logout</a>
</div>

<div class="content">
  <div class="applications-box">

    <h2>My Applications</h2>

    <table>
      <tr>
        <th>Job Title</th>
        <th>Employer</th>
        <th>Location</th>
        <th>Status</th>
        <th>Applied At</th>
        <th>Actions</th>
      </tr>

      <?php if ($applicationsResult->num_rows > 0): ?>
        <?php while ($app = $applicationsResult->fetch_assoc()): ?>

          <?php
            $cls = ($app['status'] == 'shortlisted' || $app['status'] == 'accepted') ? 'accepted' :
                   (($app['status'] == 'rejected') ? 'rejected' : 'pending');
          ?>

          <tr>
            <td><?= htmlspecialchars($app['title']) ?></td>
            <td><?= htmlspecialchars($app['employer_name']) ?></td>
            <td><?= htmlspecialchars($app['location']) ?></td>
            <td><span class="status <?= $cls ?>"><?= htmlspecialchars($app['status']) ?></span></td>
            <td><?= date("d M Y, H:i", strtotime($app['applied_at'])) ?></td>

            <td>
              <!-- Chat Button -->
              <a href="../chat.php?candidate_id=<?= $candidate_id ?>&employer_id=<?= $app['employer_id'] ?>"
                 class="chat-btn">Send Message</a><br>

              <!-- Review Button -->
              <a href="submit_review.php?application_id=<?= $app['application_id'] ?>&job_id=<?= $app['job_id'] ?>&employer_id=<?= $app['employer_id'] ?>"
                 class="review-btn">Add Review</a>
            </td>

          </tr>

        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" style="text-align:center; padding:20px; color:#555;">
            You haven't applied for any jobs yet.
          </td>
        </tr>
      <?php endif; ?>

    </table>

  </div>
</div>

</body>
</html>

<?php $stmt->close(); ?>

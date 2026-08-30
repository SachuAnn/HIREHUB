<?php
// /candidate/submit_review.php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['candidate_id']) || ($_SESSION['user_role'] ?? '') !== 'candidate') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['job_id']) || !is_numeric($_GET['job_id'])) {
    die("Invalid job.");
}

$job_id = (int)$_GET['job_id'];
$candidate_id = (int)$_SESSION['candidate_id'];

// verify candidate applied to this job and fetch employer
$check = $conn->prepare("
    SELECT j.title, j.employer_id
    FROM jobs j
    JOIN applications a ON a.job_id = j.job_id
    WHERE j.job_id = ? AND a.candidate_id = ?
    LIMIT 1
");
$check->bind_param("ii", $job_id, $candidate_id);
$check->execute();
$jobRes = $check->get_result();
if ($jobRes->num_rows === 0) {
    die("You must apply to the job before writing a review.");
}
$job = $jobRes->fetch_assoc();
$job_title = $job['title'];
$employer_id = (int)$job['employer_id'];
$check->close();

// prevent duplicate review for same job
$chk = $conn->prepare("SELECT 1 FROM reviews WHERE job_id = ? AND candidate_id = ? LIMIT 1");
$chk->bind_param("ii", $job_id, $candidate_id);
$chk->execute();
$already = (bool) $chk->get_result()->fetch_assoc();
$chk->close();

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$already) {
    $rating = intval($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if ($rating < 1 || $rating > 5) {
        $msg = "Please choose a rating (1-5).";
    } elseif ($comment === '') {
        $msg = "Please write a comment.";
    } else {
        $ins = $conn->prepare("INSERT INTO reviews (employer_id, candidate_id, job_id, rating, comment, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $ins->bind_param("iiiis", $employer_id, $candidate_id, $job_id, $rating, $comment);
        if ($ins->execute()) {
            $msg = "✅ Review submitted successfully.";
            $already = true;
        } else {
            $msg = "❌ Unable to save review. Try again.";
        }
        $ins->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Write Review — HireHub</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Poppins',sans-serif;overflow-x:hidden}
.video-bg{position:fixed;top:0;left:0;width:100%;height:100%;object-fit:cover;z-index:-3}
body::before{content:"";position:fixed;inset:0;background:linear-gradient(-45deg, rgba(79,140,255,0.6), rgba(111,231,221,0.6), rgba(102,126,234,0.6), rgba(118,75,162,0.6));background-size:400% 400%;animation:gradientMove 12s ease infinite;z-index:-2}
body::after{content:"";position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:-1}
@keyframes gradientMove{0%{background-position:0%50%}50%{background-position:100%50%}100%{background-position:0%50%}}
.sidebar{width:240px;background:rgba(0,0,0,0.7);backdrop-filter:blur(12px);position:fixed;top:0;left:0;height:100%;padding-top:30px;color:white;z-index:20}
.sidebar a{display:block;padding:12px 20px;color:white;text-decoration:none;margin:8px 0}
.content{margin-left:260px;padding:40px;color:white;min-height:100vh}
.box{background:rgba(255,255,255,0.95);padding:28px;border-radius:14px;max-width:800px;margin:40px auto;color:#0a2540}
h1{color:#0078ff;margin-bottom:12px}
label{display:block;margin-top:12px;font-weight:600}
.star-rating{direction:rtl;display:inline-flex;gap:6px;font-size:36px;cursor:pointer}
.star-rating input{display:none}
.star-rating label{color:#ccc;transition:0.2s}
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label{color:gold}
textarea{width:100%;min-height:120px;padding:10px;border-radius:8px;border:1px solid #cfd7e2}
.btn{background:#0078ff;color:#fff;padding:10px 16px;border-radius:8px;border:none;margin-top:12px;cursor:pointer}
.msg{margin-top:12px;padding:10px;border-radius:8px}
.msg.success{background:#e6ffe6;color:#2e7d32}
.msg.error{background:#fdecea;color:#c62828}
</style>
</head>
<body>

<video autoplay muted loop playsinline class="video-bg"><source src="../background.mp4" type="video/mp4"></video>

<div class="sidebar">
  <h2>HireHub</h2>
  <a href="candidate_dashboard.php">🏠 Dashboard</a>
  <a href="my_applications.php">📝 My Applications</a>
  <a href="view_reviews_candidate.php">⭐ My Reviews</a>
  <a href="logout.php">🚪 Logout</a>
</div>

<div class="content">
  <div class="box">
    <h1>Review Employer — <?= htmlspecialchars($job_title) ?></h1>

    <?php if ($msg): ?>
      <div class="msg <?= strpos($msg,'✅')!==false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($msg) ?>
      </div>
    <?php endif; ?>

    <?php if ($already): ?>
      <p>You already submitted a review for this job. <a href="view_reviews_candidate.php">View your reviews</a></p>
    <?php else: ?>
      <form method="post">
        <label>Your Rating</label>
        <div class="star-rating" title="Select rating">
          <input type="radio" name="rating" id="r5" value="5"><label for="r5">★</label>
          <input type="radio" name="rating" id="r4" value="4"><label for="r4">★</label>
          <input type="radio" name="rating" id="r3" value="3"><label for="r3">★</label>
          <input type="radio" name="rating" id="r2" value="2"><label for="r2">★</label>
          <input type="radio" name="rating" id="r1" value="1"><label for="r1">★</label>
        </div>

        <label>Your Review</label>
        <textarea name="comment" required placeholder="Write about your experience with this employer..."></textarea>

        <button class="btn" type="submit">Submit Review</button>
      </form>
    <?php endif; ?>
  </div>
</div>

</body>
</html>

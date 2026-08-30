<?php
session_start();
require_once __DIR__ . '/../config.php';

// Ensure candidate logged in
if (!isset($_SESSION['candidate_id']) || $_SESSION['user_role'] !== 'candidate') {
    header("Location: ../login.php");
    exit();
}

$candidate_id = $_SESSION['candidate_id'];

// Check job
if (!isset($_GET['job_id'])) {
    header("Location: candidate_dashboard.php");
    exit();
}

$job_id = intval($_GET['job_id']);

// Check if already applied
$check = $conn->prepare("SELECT application_id FROM applications WHERE candidate_id=? AND job_id=?");
$check->bind_param("ii", $candidate_id, $job_id);
$check->execute();
$r = $check->get_result();

if ($r->num_rows > 0) {
    $message = "You have already applied for this job.";
    $status = "error";
} else {

    // Insert application
    $insert = $conn->prepare("
        INSERT INTO applications (job_id, candidate_id, status, applied_at)
        VALUES (?, ?, 'Pending', NOW())
    ");
    $insert->bind_param("ii", $job_id, $candidate_id);

    if ($insert->execute()) {
        $message = "Application submitted successfully!";
        $status = "success";
    } else {
        $message = "Error submitting application.";
        $status = "error";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Apply Job - HireHub</title>
<style>
* {margin:0; padding:0; box-sizing:border-box;}
body, html {height:100%; font-family:'Poppins',sans-serif; color:#fff;}

.video-bg {
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    object-fit:cover;
    z-index:-3;
    filter:brightness(0.45);
}

body::before {
    content:"";
    position:fixed; inset:0;
    background:linear-gradient(-45deg,
        rgba(79,140,255,0.6),
        rgba(111,231,221,0.6),
        rgba(102,126,234,0.6),
        rgba(118,75,162,0.6)
    );
    background-size:400% 400%;
    animation:grad 12s ease infinite;
    z-index:-2;
}

@keyframes grad {
    0%{background-position:0% 50%;}
    50%{background-position:100% 50%;}
    100%{background-position:0% 50%;}
}

.container {
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.card {
    background:rgba(255,255,255,0.1);
    backdrop-filter:blur(15px);
    padding:40px;
    width:420px;
    border-radius:20px;
    text-align:center;
    box-shadow:0 8px 24px rgba(0,0,0,0.3);
}

.card h2 {
    font-size:26px;
    margin-bottom:10px;
}

.message {
    padding:15px;
    border-radius:10px;
    margin-top:15px;
    font-size:16px;
}

.success {background:rgba(0,255,0,0.2); border:1px solid #00ff9d;}
.error {background:rgba(255,0,0,0.2); border:1px solid #ff5b5b;}

.btn {
    display:inline-block;
    margin-top:25px;
    padding:12px 25px;
    background:#4f46e5;
    color:#fff;
    text-decoration:none;
    border-radius:10px;
}
.btn:hover {background:#4238e0;}
</style>
</head>

<body>

<video autoplay muted loop class="video-bg">
    <source src="../background.mp4" type="video/mp4">
</video>

<div class="container">
    <div class="card">
        <h2><?= ($status=="success") ? "Application Sent!" : "Application Error" ?></h2>

        <div class="message <?= $status ?>">
            <?= htmlspecialchars($message) ?>
        </div>

        <a href="candidate_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</div>

</body>
</html>

<?php
ob_start();
session_start();
include 'config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['user_type'];

    if (!empty($email) && !empty($password) && !empty($role)) {
        $stmt = $conn->prepare("SELECT user_id, name, email, password, role FROM users WHERE email = ? AND role = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("ss", $email, $role);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    if (strtolower($user['role']) === "employer") {
                        $_SESSION['employer_id']   = $user['user_id'];
                        $_SESSION['employer_name'] = $user['name'];
                        $_SESSION['user_role']     = 'employer';
                        header("Location: /HIREHUB/employer/employer_dashboard.php");
                        exit();
                    } elseif (strtolower($user['role']) === "candidate") {
                        $_SESSION['candidate_id']   = $user['user_id'];
                        $_SESSION['candidate_name'] = $user['name'];
                        $_SESSION['user_role']      = 'candidate';
                        header("Location: /HIREHUB/candidate/candidate_dashboard.php");
                        exit();
                    } else {
                        $error = "⚠️ Unknown role detected.";
                    }
                } else {
                    $error = "❌ Invalid password.";
                }
            } else {
                $error = "❌ No account found with this email and role.";
            }
            $stmt->close();
        } else {
            $error = "❌ Database query failed: " . $conn->error;
        }
    } else {
        $error = "❌ All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HireHub - Login</title>
<style>
body {
    margin:0;
    padding:0;
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color:#222;
}

/* --- Video Background --- */
header {
    position: relative;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    overflow: hidden;
}

header video {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: -3;
}

/* Moving gradient overlay */
header::before {
    content:"";
    position: absolute;
    inset: 0;
    background: linear-gradient(-45deg, rgba(79,140,255,0.6), rgba(111,231,221,0.6), rgba(102,126,234,0.6), rgba(118,75,162,0.6));
    background-size: 400% 400%;
    animation: gradientMove 12s ease infinite;
    z-index: -2;
}

@keyframes gradientMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Dark overlay for clarity */
header::after {
    content:"";
    position:absolute;
    inset:0;
    background: rgba(0,0,0,0.4);
    z-index:-1;
}

/* --- Login Card --- */
.login-container {
    position: relative;
    z-index: 1;
    width:100%;
    max-width:400px;
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(10px);
    padding:40px 50px;
    border-radius: 18px;
    box-shadow:0 8px 30px rgba(0,0,0,0.25);
    animation: fadeSlideUp 1s ease forwards;
    margin: auto;
}

/* Animation */
@keyframes fadeSlideUp {
    0% { opacity: 0; transform: translateY(40px);}
    100% { opacity: 1; transform: translateY(0);}
}

.login-container h2 {
    text-align:center;
    color:#0a2540;
    margin-bottom:12px;
}

.muted { text-align:center; color:#555; margin-bottom:24px; }

.input-group { margin-bottom:18px; text-align:left; }
.input-group label { display:block; margin-bottom:6px; font-weight:600; color:#333; }
.input-group input, .input-group select {
    width:100%;
    padding:12px;
    border-radius:8px;
    border:1px solid #cfd7e2;
    font-size:1rem;
}

.input-group input:focus, .input-group select:focus {
    border-color:#4f8cff;
    box-shadow:0 0 0 3px rgba(79,140,255,0.2);
    outline:none;
}

.btn-primary {
    width:100%;
    padding:14px;
    border:none;
    border-radius:30px;
    font-weight:bold;
    font-size:1rem;
    background:#4f8cff;
    color:white;
    cursor:pointer;
    transition: background 0.3s, transform 0.2s;
}

.btn-primary:hover {
    background:#3a74e3;
    transform:translateY(-2px);
}

.error {
    color:#c2005f;
    background:#ffeef6;
    border:1px solid #f3c0db;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
}

.footer-links { text-align:center; margin-top:12px; }
.footer-links a { color:#4f8cff; text-decoration:none; }
.footer-links a:hover { text-decoration:underline; }

@media (max-width:480px) {
    .login-container { padding:30px 24px; margin-top:50px; }
}
</style>
</head>
<body>
<header>
<video autoplay muted loop playsinline>
    <source src="assets/bg-video.mp4" type="video/mp4">
</video>
<div class="login-container">
    <h2>Login</h2>
    <p class="muted">Access your HireHub account</p>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="Enter your email" required>
        </div>
        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>
        </div>
        <div class="input-group">
            <label>User Type</label>
            <select name="user_type" required>
                <option value="">-- Select User Type --</option>
                <option value="candidate">Candidate</option>
                <option value="employer">Employer</option>
            </select>
        </div>
        <button type="submit" class="btn-primary">Login</button>
    </form>
    <div style="text-align:center; margin-top:15px;">
  <a href="index.php" 
     style="display:inline-block; padding:10px 20px; background:#484a4f; 
            color:white; text-decoration:none; border-radius:6px; 
            font-weight:500; transition:0.3s;">
    ⬅️ Back to Home
  </a>
</div>


    <div class="footer-links">
        <p>Don't have an account? <a href="/HIREHUB/register.php">Register</a></p>
    </div>
</div>
</header>
</body>
</html>
<?php ob_end_flush(); ?>

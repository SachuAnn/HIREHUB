<?php
include 'config.php';
include 'header.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Directory for resume uploads
    $uploadDir = "resumes/";

    // Create folder if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';

    // -------------------------------
    // VALIDATION (MODIFIED)
    // -------------------------------
    if ($name === '' || $email === '' || $password === '' || $confirm === '' || $role === '') {
        $errors[] = "All fields are required.";
    } 
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    } 
    elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $email)) {
        $errors[] = "Only valid Gmail addresses are allowed (example: username@gmail.com).";
    }
    elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    } 
    elseif (!in_array($role, ['candidate', 'employer'])) {
        $errors[] = "Invalid role selected.";
    } 
    else {

        // Check for duplicate email
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->fetch_assoc()) {
            $errors[] = "An account with that email already exists.";
        } else {

            // Insert new user
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $insert->bind_param("ssss", $name, $email, $hash, $role);

            if ($insert->execute()) {
                $new_user_id = $insert->insert_id;

                // ----------------------------
                // CANDIDATE REGISTRATION
                // ----------------------------
                if ($role === 'candidate') {

                    // Resume upload handling
                    $resume_path = "";
                    if (isset($_FILES['resume_file']) && $_FILES['resume_file']['error'] === 0) {
                        $filename = time() . "_" . basename($_FILES['resume_file']['name']);
                        $filePath = $uploadDir . $filename;

                        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                        $allowed = ['pdf', 'doc', 'docx'];

                        if (in_array($ext, $allowed)) {
                            if (move_uploaded_file($_FILES['resume_file']['tmp_name'], $filePath)) {
                                $resume_path = $filePath;
                            } else {
                                $errors[] = "Failed to upload resume.";
                            }
                        } else {
                            $errors[] = "Invalid resume format. Only PDF, DOC, DOCX allowed.";
                        }
                    }

                    // Insert candidate profile
                    $age = intval($_POST['age'] ?? 18);
                    $skills = $_POST['skills'] ?? '';
                    $experience = $_POST['experience'] ?? '';
                    $location = $_POST['location'] ?? '';

                    $cstmt = $conn->prepare("INSERT INTO candidate_profiles 
                        (user_id, age, resume, skills, experience, location)
                        VALUES (?, ?, ?, ?, ?, ?)");
                    $cstmt->bind_param("isssss", $new_user_id, $age, $resume_path, $skills, $experience, $location);
                    $cstmt->execute();
                    $cstmt->close();

                } 
                // ----------------------------
                // EMPLOYER REGISTRATION
                // ----------------------------
                else {
                    $company_name = $_POST['company_name'] ?? '';
                    $industry = $_POST['industry'] ?? '';
                    $elocation = $_POST['employer_location'] ?? '';
                    $website = $_POST['website'] ?? '';

                    $estmt = $conn->prepare("INSERT INTO employer_profiles 
                        (user_id, company_name, industry, location, website)
                        VALUES (?, ?, ?, ?, ?)");
                    $estmt->bind_param("issss", $new_user_id, $company_name, $industry, $elocation, $website);
                    $estmt->execute();
                    $estmt->close();
                }

                $success = "Registration successful! You can now <a href='login.php'>Login</a>.";
            } else {
                $errors[] = "Registration failed. Please try again.";
            }

            $insert->close();
        }

        $stmt->close();
    }
}
?>

<style>
/* --- Background video setup --- */
.video-bg {
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  object-fit: cover;
  z-index: -2;
}

.overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 30, 80, 0.45);
  z-index: -1;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  margin: 0;
  padding: 0;
  color: #222;
}

.main-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  padding: 30px;
}

.card {
  background: rgba(255, 255, 255, 0.95);
  padding: 40px 50px;
  border-radius: 18px;
  max-width: 540px;
  width: 100%;
  box-shadow: 0 8px 30px rgba(0,0,0,0.25);
}

.card h2 {
  text-align: center;
  margin-bottom: 8px;
}

.muted {
  text-align: center;
  margin-bottom: 24px;
}

.form-row { margin-bottom: 16px; }

label {
  display: block;
  margin-bottom: 6px;
  font-weight: 600;
}

input, select, textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #cfd7e2;
  border-radius: 8px;
  font-size: 1rem;
}

.btn-primary {
  background: #4f8cff;
  color: white;
  padding: 14px;
  border: none;
  border-radius: 8px;
  width: 100%;
  cursor: pointer;
}
.btn-primary:hover { background: #3a74e3; }

.alert { padding: 12px; border-radius: 8px; }
.alert-error { background: #ffeef6; color: #c2005f; }
.alert-success { background: #e6ffe6; color: #2e7d32; }
</style>

<!-- Background Video -->
<video autoplay muted loop class="video-bg">
  <source src="assets/bg-video.mp4" type="video/mp4">
</video>
<div class="overlay"></div>

<!-- Registration Form -->
<div class="main-container">
  <div class="card">
    <h2>Create your HireHub Account</h2>
    <p class="muted">Register as a Candidate or Employer</p>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <?php foreach ($errors as $err): ?>
          <div><?php echo $err; ?></div>
        <?php endforeach; ?>
      </div>
    <?php elseif ($success): ?>
      <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="post" action="" enctype="multipart/form-data">
      
      <div class="form-row">
        <label>Full Name</label>
        <input type="text" name="name" required>
      </div>

      <div class="form-row">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>

      <div class="form-row">
        <label>I am a</label>
        <select name="role" id="role" onchange="toggleRoleFields()" required>
          <option value="">-- Select Role --</option>
          <option value="candidate">Candidate</option>
          <option value="employer">Employer</option>
        </select>
      </div>

      <!-- Candidate Fields -->
      <div id="candidateFields" style="display:none;">
        <div class="form-row">
          <label>Age</label>
          <input type="number" name="age" value="18">
        </div>

        <div class="form-row">
          <label>Skills</label>
          <input type="text" name="skills">
        </div>

        <div class="form-row">
          <label>Experience</label>
          <input type="text" name="experience">
        </div>

        <div class="form-row">
          <label>Location</label>
          <input type="text" name="location">
        </div>

        <div class="form-row">
          <label>Upload Resume (PDF, DOC, DOCX)</label>
          <input type="file" name="resume_file" accept=".pdf,.doc,.docx">
        </div>
      </div>

      <!-- Employer Fields -->
      <div id="employerFields" style="display:none;">
        <div class="form-row">
          <label>Company Name</label>
          <input type="text" name="company_name">
        </div>

        <div class="form-row">
          <label>Industry</label>
          <input type="text" name="industry">
        </div>

        <div class="form-row">
          <label>Company Location</label>
          <input type="text" name="employer_location">
        </div>

        <div class="form-row">
          <label>Website</label>
          <input type="text" name="website">
        </div>
      </div>

      <div class="form-row">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>

      <div class="form-row">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>
      </div>

      <button class="btn-primary" type="submit">Create Account</button>

      <p style="text-align:center; margin-top:12px;">
        <a href="login.php">Already have an account? Login</a>
      </p>

    </form>
  </div>
</div>

<script>
function toggleRoleFields() {
  let role = document.getElementById("role").value;
  document.getElementById("candidateFields").style.display = role === "candidate" ? "block" : "none";
  document.getElementById("employerFields").style.display = role === "employer" ? "block" : "none";
}
</script>

<?php include 'footer.php'; ?>

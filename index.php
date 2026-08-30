<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>HireHub - Welcome</title>

<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f9fbfd;
    color: #333;
    overflow-x: hidden;
  }

  /* ===== NAVIGATION ===== */
  nav {
    background: rgba(255, 255, 255, 0.85);
    padding: 16px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 100;
    backdrop-filter: blur(10px);
  }

  /* ===== LOGO / HEADING ===== */
  .logo {
    font-size: 1.6rem;
    font-weight: 700;
    color: #0078ff;
    letter-spacing: 1px;
    text-decoration: none;
  }

  .logo span {
    color: #00c8c8;
  }

  /* ===== NAV LINKS ===== */
  .nav-links {
    display: flex;
    align-items: center;
    gap: 30px;
  }

  nav a {
    color: #333;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease, background 0.3s ease;
    padding: 8px 18px;
    border-radius: 8px;
  }

  nav a:hover {
    color: #0078ff;
    background: #eef5ff;
  }

  /* ===== Dropdown ===== */
  .dropdown {
    position: relative;
  }

  .dropbtn {
    background-color: #0078ff;
    color: white;
    padding: 10px 26px;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.3s ease;
  }

  .dropbtn:hover {
    background-color: #005ed6;
  }

  .dropdown-content {
    display: none;
    position: absolute;
    background-color: white;
    min-width: 180px;
    box-shadow: 0px 8px 20px rgba(0,0,0,0.12);
    border-radius: 10px;
    margin-top: 10px;
    right: 0;
    z-index: 200;
    overflow: hidden;
  }

  .dropdown-content a {
    color: #333;
    padding: 12px 20px;
    text-decoration: none;
    display: block;
    font-weight: 500;
    transition: background 0.3s ease, color 0.3s ease;
  }

  .dropdown-content a:hover {
    background-color: #0078ff;
    color: #fff;
  }

  /* Keeps dropdown open on hover */
  .dropdown:hover .dropdown-content,
  .dropdown:focus-within .dropdown-content {
    display: block;
  }

  /* ===== HERO SECTION ===== */
  header {
    position: relative;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    color: white;
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

  header::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(-45deg, rgba(79,140,255,0.7), rgba(111,231,221,0.7), rgba(102,126,234,0.7), rgba(118,75,162,0.7));
    background-size: 400% 400%;
    animation: gradientMove 12s ease infinite;
    z-index: -2;
  }

  @keyframes gradientMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  header::after {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.4);
    z-index: -1;
  }

  header h1 {
    font-size: 3.6rem;
    font-weight: 700;
    margin-bottom: 0.4em;
    letter-spacing: -1px;
    animation: fadeSlideUp 1s ease forwards;
  }

  header p {
    font-size: 1.25rem;
    font-weight: 400;
    max-width: 600px;
    margin-bottom: 2em;
    line-height: 1.5;
    opacity: 0;
    animation: fadeSlideUp 1s ease forwards;
    animation-delay: 0.4s;
  }

  @keyframes fadeSlideUp {
    0% { opacity: 0; transform: translateY(40px); }
    100% { opacity: 1; transform: translateY(0); }
  }

  header a.button {
    background: white;
    color: #0078ff;
    text-decoration: none;
    padding: 15px 42px;
    border-radius: 40px;
    font-weight: bold;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    opacity: 0;
    animation: fadeSlideUp 1s ease forwards;
    animation-delay: 0.8s;
  }

  header a.button:hover {
    background: #e3f0ff;
    transform: translateY(-3px);
  }

  /* ===== INFO SECTION ===== */
  .container {
    max-width: 1100px;
    margin: 80px auto;
    padding: 0 24px;
    display: flex;
    flex-wrap: wrap;
    gap: 40px;
    justify-content: center;
  }

  .info {
    background: white;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    text-align: left;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .info:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.12);
  }

  .info h2 {
    color: #0078ff;
    font-size: 2.2rem;
    margin-bottom: 18px;
  }

  .info p {
    font-size: 1.1rem;
    line-height: 1.7;
    color: #555;
  }

  @media (max-width: 860px) {
    nav {
      flex-direction: column;
      gap: 12px;
    }
    header h1 { font-size: 2.4rem; }
    header p { font-size: 1rem; padding: 0 20px; }
  }
</style>
</head>

<body>

<nav>
  <a href="#" class="logo">HIRE<span>HUB</span></a>
  <div class="nav-links">
    <a href="register.php">Register</a>
    <div class="dropdown">
      <button class="dropbtn">Login ▼</button>
      <div class="dropdown-content">
        <a href="login.php?type=user">User Login</a>
        <a href="admin/admin_login.php">Admin Login</a>
      </div>
    </div>
  </div>
</nav>

<header>
  <video autoplay muted loop playsinline>
    <source src="background.mp4" type="video/mp4">
  </video>

  <h1>Welcome to HireHub</h1>
  <p>Your Dream Job Awaits — Connect with Top Employers and Candidates Today!</p>
  <a href="register.php" class="button">Get Started</a>
</header>

<div class="container">
  <div class="info">
    <h2>Connecting Talent and Opportunities</h2>
    <p>
      HireHub is your trusted platform for seamless recruitment. 
      Find the right job, meet your career goals, or connect with outstanding talent. 
      Our portal is designed for job seekers and employers alike — intuitive, professional, and efficient.
    </p>
    <p>
      Join thousands who use HireHub for job searching, applications, and employer solutions. 
      Get started by registering or logging in today!
    </p>
  </div>
</div>

</body>
</html>

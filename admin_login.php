<?php
session_start(); // Start the session at the very top
$error_message = "";

// 1. Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 2. Check if the username and password are correct
    if ($username === 'admin' && $password === 'admin123') { // Your password
        // SUCCESS!
        $_SESSION['loggedin'] = true; // Remember the user is logged in
        // Use absolute path for redirect
        // *** FIX: Redirect to clean URL ***
        header("Location: dashboard");
        exit;
    } else {
        // FAILED
        $error_message = "Wrong username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - College Digital Notice Board</title>
  <link rel="stylesheet" href="/notice/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>

  <header class="main-header">
    <img src="/notice/logo.png" alt="College Logo" class="logo">
    <h1>College Digital Notice Board - Computer Department</h1>
    <button id="dark-mode-toggle" title="Toggle dark mode"></button>
  </header>

  <main>
    <form class="login-card" action="/notice/admin_login.php" method="POST">
      <h2>Admin Login</h2>
      
      <?php if (!empty($error_message)): ?>
          <p class="login-error"><?php echo $error_message; ?></p>
      <?php endif; ?>

      <label for="username">Username:</label>
      <input type="text" id="username" name="username" placeholder="Enter your username" required>

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" placeholder="Enter your password" required>

      <button type="submit" class="btn btn-primary">Login</button>
    </form>
  </main>

  <footer class="main-footer">
    <p>© 2025 College Digital Notice Board - All Rights Reserved</p>
  </footer>

  <script src="/notice/darkmode.js"></script>
</body>
</html>
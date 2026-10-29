<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$notice_id = $_GET['id'] ?? 0; 
$notice_title = "";
$notice_content = "";
$current_cat = "General"; // Default
$is_checked = ""; 

if ($notice_id > 0) {
    include 'config.php';
    $conn = new mysqli($servername, $username, $password, $dbname, 3307); 

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        $stmt = $conn->prepare("SELECT title, content, is_pinned FROM notices WHERE id = ?");
        $stmt->bind_param("i", $notice_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $notice_title = $row['title'];
            $is_checked = ($row['is_pinned'] == 1) ? 'checked' : ''; 
            
            // --- LOGIC TO EXTRACT CATEGORY ---
            $full_content = $row['content'];
            $notice_content = $full_content;
            
            // Check for [Category] at the start
            if (preg_match('/^\[(.*?)\]/', $full_content, $matches)) {
                $current_cat = $matches[1]; // Found category (e.g., "Exams")
                // Remove [Category] from the content box
                $notice_content = trim(str_replace("[$current_cat]", "", $full_content));
            }
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Edit Notice - College Digital Notice Board</title>
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
    <div class="home-button-container">
        <a href="dashboard" class="btn btn-primary">Back to Dashboard</a>
    </div>

    <form class="form-card" action="api.php" method="POST">
      <h2>Edit Notice (ID: <?php echo $notice_id; ?>)</h2>
      
      <label for="title">Notice Title:</label>
      <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($notice_title); ?>" required>

      <label for="category">Category:</label>
      <select id="category" name="category">
          <option value="General" <?php if($current_cat == 'General') echo 'selected'; ?>>General</option>
          <option value="Exams" <?php if($current_cat == 'Exams') echo 'selected'; ?>>Exams</option>
          <option value="Events" <?php if($current_cat == 'Events') echo 'selected'; ?>>Events</option>
          <option value="Holidays" <?php if($current_cat == 'Holidays') echo 'selected'; ?>>Holidays</option>
      </select>

      <label for="message">Notice Content:</label>
      <textarea id="message" name="message" required><?php echo htmlspecialchars($notice_content); ?></textarea>

      <div class="checkbox-container">
        <input type="checkbox" id="is_pinned" name="is_pinned" value="1" <?php echo $is_checked; ?>>
        <label for="is_pinned">Mark as Important (Pin to Top)</label>
      </div>

      <input type="hidden" name="action" value="update"> 
      <input type="hidden" name="notice_id" value="<?php echo $notice_id; ?>"> 

      <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
  </main>

  <footer class="main-footer">
    <p>© 2025 College Digital Notice Board - All Rights Reserved</p>
  </footer>

  <script src="/notice/darkmode.js"></script>
</body>
</html>
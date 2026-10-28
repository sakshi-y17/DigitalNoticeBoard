<?php
// --- SESSION GUARD ---
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

include 'config.php'; 
$conn = new mysqli($servername, $username, $password, $dbname, 3307); // Port 3307

// --- ANALYTICS QUERIES ---
$total_notices = $conn->query("SELECT COUNT(*) as count FROM notices")->fetch_assoc()['count'];
$total_pinned = $conn->query("SELECT COUNT(*) as count FROM notices WHERE is_pinned = 1")->fetch_assoc()['count'];
$total_today = $conn->query("SELECT COUNT(*) as count FROM notices WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - PCCOER</title>
  <link rel="stylesheet" href="/notice/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>

  <header class="main-header">
    <img src="/notice/logo.png" alt="PCCOER Logo" class="logo">
    <h1>Pimpri Chinchwad College of Engineering and Research, Ravet - Computer Department</h1>
    <button id="dark-mode-toggle" title="Toggle dark mode"></button>
  </header>

  <main>
    <div class="home-button-container" style="justify-content: space-between; display: flex;">
        <a href="/notice/" target="_blank" class="btn" style="background: #28a745; border-color: #28a745; color: white;">
            👁️ Live Preview
        </a>
        <button onclick="secureLogout()" class="btn" style="background: #d9534f; border-color: #d9534f; color: white;">
            🔒 Secure Logout
        </button>
    </div>

    <div class="analytics-container">
        <div class="stat-card">
            <h3>Total Notices</h3>
            <p><?php echo $total_notices; ?></p>
        </div>
        <div class="stat-card">
            <h3>Pinned</h3>
            <p><?php echo $total_pinned; ?></p>
        </div>
        <div class="stat-card">
            <h3>Posted Today</h3>
            <p><?php echo $total_today; ?></p>
        </div>
    </div>

    <form class="form-card" action="api.php" method="POST">
      <h2>Add New Notice</h2>
      
      <label for="title">Notice Title:</label>
      <input type="text" id="title" name="title" placeholder="e.g., Prelims timetable" required>

      <label for="category">Category:</label>
      <select id="category" name="category">
          <option value="General">General</option>
          <option value="Exams">Exams</option>
          <option value="Events">Events</option>
          <option value="Holidays">Holidays</option>
      </select>

      <label for="message">Notice Content:</label>
      <textarea id="message" name="message" placeholder="Enter notice details here..." required></textarea>
      
      <div id="char-counter-container">
          Characters: <span id="char-count">0</span> / 1000
      </div>

      <div class="checkbox-container">
        <input type="checkbox" id="is_pinned" name="is_pinned" value="1">
        <label for="is_pinned">Mark as Important (Pin to Top)</label>
      </div>

      <input type="hidden" name="action" value="add">
      <button type="submit" class="btn btn-primary">Add Notice</button>
    </form>

    <form action="admin_dashboard.php" method="GET" style="margin-bottom: 20px; display: flex; gap: 10px; max-width: 700px; margin: 30px auto 20px;">  
      <input type="text" name="search" placeholder="Search notices..." style="padding: 10px; flex: 1; border: 1px solid #ccc; border-radius: 4px;" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">  
      <button type="submit" class="btn btn-primary">Search</button>
      <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
          <a href="admin_dashboard.php" class="btn" style="background: #d9534f; color: white; border:none;">Clear</a>
      <?php endif; ?>
    </form>

    <div class="notice-table-container">
        <h2>Existing Notices</h2>
        <table class="notice-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Content</th> <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($conn->connect_error) {
                    echo "<tr><td colspan='5'>Connection Failed</td></tr>";
                } else {
                    $search = $_GET['search'] ?? '';
                    $sql = "SELECT id, title, content, created_at, is_pinned FROM notices";
                    
                    if (!empty($search)) {
                        $search_term = "%" . $conn->real_escape_string($search) . "%";
                        $sql .= " WHERE title LIKE '$search_term' OR content LIKE '$search_term'";
                    }
                    $sql .= " ORDER BY is_pinned DESC, id DESC";
                    
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $row_class = $row['is_pinned'] == 1 ? 'pinned-row' : '';
                            
                            // --- SMART BADGE LOGIC ---
                            $full_content = $row['content'];
                            $clean_content = $full_content;
                            $cat_badge = "";

                            if (preg_match('/^\[(.*?)\]/', $full_content, $matches)) {
                                $cat_name = $matches[1];
                                $clean_content = trim(str_replace("[$cat_name]", "", $full_content));
                                
                                $badge_color = "cat-general";
                                if (stripos($cat_name, 'Exam') !== false) $badge_color = "cat-exam";
                                if (stripos($cat_name, 'Event') !== false) $badge_color = "cat-event";
                                if (stripos($cat_name, 'Holiday') !== false) $badge_color = "cat-holiday";

                                $cat_badge = "<span class='category-badge $badge_color' style='margin-bottom: 5px;'>$cat_name</span><br>";
                            }
                            // -------------------------

                            echo "<tr class='" . $row_class . "'>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                            
                            // Show Badge + Truncated Clean Content
                            echo "<td>" . $cat_badge . htmlspecialchars(substr($clean_content, 0, 60)) . "...</td>"; 
                            
                            echo "<td>" . htmlspecialchars(date('d-m-Y', strtotime($row['created_at']))) . "</td>";
                            echo "<td class='actions'>";
                            echo "<a href='edit_notice.php?id=" . $row['id'] . "' class='btn btn-primary'>Edit</a>";
                            echo "<form action='api.php' method='POST' style='display:inline;'>";
                            echo "<input type='hidden' name='action' value='delete'>";
                            echo "<input type='hidden' name='notice_id' value='" . $row['id'] . "'>";
                            echo "<button type='submit' class='btn btn-delete' onclick='return confirm(\"Delete?\");'>Delete</button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; padding: 20px;'>No notices found.</td></tr>";
                    }
                    $conn->close();
                }
                ?>
            </tbody>
        </table>
    </div>
  </main>

  <footer class="main-footer">
    <p>© 2025 PCCOER - All Rights Reserved</p>
  </footer>

  <div id="lockdown-overlay">
      <div class="lock-icon">🔒</div>
      <h2>Securing Dashboard...</h2>
      <p>Closing session & clearing cache</p>
  </div>

  <script>
    const messageTextarea = document.getElementById("message");
    const charCountSpan = document.getElementById("char-count");
    const charLimit = 1000;
    if(messageTextarea){
        messageTextarea.addEventListener("input", () => {
            charCountSpan.textContent = messageTextarea.value.length;
        });
    }
    function secureLogout() {
        document.getElementById('lockdown-overlay').style.display = 'flex'; 
        setTimeout(() => { window.location.href = 'logout.php'; }, 2000);
    }
  </script>
  <button id="back-to-top-btn" title="Go to top">▲</button>
  <script src="/notice/main.js"></script>
  <script src="/notice/darkmode.js"></script>
</body>
</html>
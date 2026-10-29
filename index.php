<?php
include 'config.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Portal - College Digital Notice Board</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>

  <header class="main-header">
    <img src="logo.png" alt="College Logo" class="logo">
    <h1>College Digital Notice Board - Computer Department</h1>
    <button id="dark-mode-toggle" title="Toggle dark mode"></button>
  </header>

  <main>
    
    <?php
    // 1. Connect specifically for the ticker
    $conn_ticker = new mysqli($servername, $username, $password, $dbname, 3307);
    
    // 2. Fetch ONLY Pinned notices
    $sql_ticker = "SELECT title FROM notices WHERE is_pinned = 1 ORDER BY created_at DESC";
    $result_ticker = $conn_ticker->query($sql_ticker);

    if ($result_ticker && $result_ticker->num_rows > 0) {
        echo '<div class="ticker-wrap"><div class="ticker">';
        while ($row = $result_ticker->fetch_assoc()) {
            echo '<div class="ticker-item">📢 ' . htmlspecialchars($row['title']) . '</div>';
        }
        echo '</div></div>';
    }
    $conn_ticker->close();
    ?>
    <div class="notice-list-container">
      
      <div class="home-button-container" style="margin-bottom: 15px;">
          <form action="index.php" method="GET" class="search-container" style="display:flex; justify-content:center; margin: 0 auto; max-width: 800px; width: 100%;">
    
             <input type="text" id="search-input" name="search" placeholder="Search for notices..." 
             style="flex-grow: 1; padding: 15px; border: 2px solid #e9ecef; border-right: none; border-radius: 30px 0 0 30px; outline: none; font-family: 'Lato', sans-serif; font-size: 1.1rem;" 
             autocomplete="off" 
             value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            
    <button type="submit" class="btn btn-primary" 
            style="border-radius: 0 30px 30px 0; margin: 0; padding: 0 35px; border: 2px solid #003366; font-size: 1.1rem; display: flex; align-items: center;">
        Search
    </button>

            </form>
      </div>

      <div class="home-button-container" style="margin-bottom: 30px; display: flex; justify-content: center; gap: 10px; flex-wrap: wrap;">
          <a href="index.php" class="btn btn-primary">All Notices</a>
          <a href="index.php?category=Exams" class="btn btn-primary">Exams</a>
          <a href="index.php?category=Events" class="btn btn-primary">Events</a>
          <a href="index.php?category=Holidays" class="btn btn-primary">Holidays</a>
      </div>

      <h2 class="page-title">Latest Announcements 📢</h2>

      <div id="notice-container">
          <?php
          $conn = new mysqli($servername, $username, $password, $dbname, 3307);

          if ($conn->connect_error) {
              echo "<div class='notice-card'><h3>Database Connection Failed</h3><p>" . $conn->connect_error . "</p></div>";
          } else {
              
              $search_term = $_GET['search'] ?? '';
              $category = $_GET['category'] ?? '';
              
              if (!empty($search_term)) {
                  echo "<div style='text-align: center; margin-bottom: 20px; color: #555;'>";
                  echo "Showing results for: <strong>" . htmlspecialchars($search_term) . "</strong> ";
                  echo "<a href='index.php' style='color: #d9534f; text-decoration: none; margin-left: 10px; font-weight:bold;'>✖ Clear Search</a>";
                  echo "</div>";
              }

              $sql = "SELECT id, title, content, created_at, is_pinned FROM notices";
              $params = [];
              $types = "";
              $where_clauses = [];

              if (!empty($category)) {
                  $where_clauses[] = "content LIKE ?";
                  $params[] = "[" . $category . "]%";
                  $types .= "s";
              }
              if (!empty($search_term)) {
                  $where_clauses[] = "(title LIKE ? OR content LIKE ?)";
                  $like_term = "%" . $search_term . "%";
                  $params[] = $like_term;
                  $params[] = $like_term;
                  $types .= "ss";
              }
              if (!empty($where_clauses)) {
                  $sql .= " WHERE " . implode(" AND ", $where_clauses);
              }
              
              $sql .= " ORDER BY is_pinned DESC, created_at DESC";

              $stmt = $conn->prepare($sql);
              if (!empty($params)) {
                  $stmt->bind_param($types, ...$params);
              }
              $stmt->execute();
              $result = $stmt->get_result();

              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      $card_class = $row['is_pinned'] == 1 ? 'notice-card pinned' : 'notice-card';
                      
                      // --- SMART CATEGORY LOGIC START ---
                      $full_content = $row['content'];
                      $cat_badge = ""; // Default empty
                      $clean_content = $full_content; // Default content

                      // Check if content starts with [Something]
                      if (preg_match('/^\[(.*?)\]/', $full_content, $matches)) {
                          $cat_name = $matches[1]; // Get text inside []
                          $clean_content = trim(str_replace("[$cat_name]", "", $full_content)); // Remove it from text
                          
                          // Assign Color Class based on name
                          $badge_color_class = "cat-general"; // Default
                          if (stripos($cat_name, 'Exam') !== false) $badge_color_class = "cat-exam";
                          if (stripos($cat_name, 'Event') !== false) $badge_color_class = "cat-event";
                          if (stripos($cat_name, 'Holiday') !== false) $badge_color_class = "cat-holiday";

                          // Create the HTML Badge
                          $cat_badge = "<span class='category-badge $badge_color_class'>$cat_name</span>";
                      }
                      // --- SMART CATEGORY LOGIC END ---


                      echo "<div class='" . $card_class . "'>";
                      
                      // Date Logic
                      $post_date = new DateTime($row['created_at']);
                      $now = new DateTime();
                      $diff = $now->diff($post_date);
                      $hours = ($diff->days * 24) + $diff->h;
                      $title = htmlspecialchars($row['title']);
                      
                      // Title Line
                      if ($hours < 1) {
                          echo "<h2>" . $title . " <span class='pulse-badge'>JUST NOW</span></h2>";
                      } elseif ($diff->days < 1) {
                          echo "<h2>" . $title . " <span class='new-badge'>NEW!</span></h2>";
                      } else {
                          echo "<h2>" . $title . "</h2>";
                      }
                      
                      // Content Line (Using CLEAN content)
                      echo "<p class='notice-content'>" . htmlspecialchars($clean_content) . "</p>";
                      
                      // Footer Line (Date + Category Badge + Print)
                      echo "<div class='card-footer-line'>";
                          echo "<small>Posted: " . htmlspecialchars(date('d M, h:i A', strtotime($row['created_at']))) . "</small>";
                          echo $cat_badge; // Show the colored badge here
                          echo "<button onclick='printNotice(this)' class='print-btn'>🖨️ Print</button>";
                      echo "</div>";
                      
                      echo "</div>";
                  }
              } else {
                  echo "<div class='notice-card'><p>No announcements found.</p></div>";
              }
              $conn->close();
          }
          ?>
      </div> 
    </div>
  </main>

  <footer class="main-footer">
    <p>© 2025 College Digital Notice Board - All Rights Reserved</p>
  </footer>

  <button id="back-to-top-btn" title="Go to top">▲</button>
  <script>
    // 1. Back to Top Button Logic
    const topButton = document.getElementById("back-to-top-btn");
    if (topButton) {
        window.onscroll = function() {
            if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
                topButton.style.display = "block";
                topButton.style.opacity = "1";
            } else {
                topButton.style.opacity = "0";
                setTimeout(() => { 
                    if (document.body.scrollTop < 100 && document.documentElement.scrollTop < 100) {
                        topButton.style.display = "none";
                    }
                }, 300);
            }
        };
        topButton.addEventListener("click", () => {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        });
    }

    // 2. Print Notice Logic (The Fix)
    function printNotice(button) {
        // Find the card container
        const card = button.closest('.notice-card');
        
        // Add the 'printing' class to HIDE everything else
        card.classList.add('printing');
        
        // Open Print Dialog
        window.print();
        
        // Remove the class after 1 second so the page returns to normal
        setTimeout(() => {
            card.classList.remove('printing');
        }, 1000);
    }
  </script>
</body>
</html>
  <script src="darkmode.js"></script>
</body>
</html>
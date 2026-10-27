<?php
include 'config.php';
$conn = new mysqli($servername, $username, $password, $dbname, 3307);

if ($conn->connect_error) {
    echo "Connection Failed";
    exit;
}

$search_term = $_POST['query'] ?? '';

$sql = "SELECT id, title, content, created_at, is_pinned FROM notices";
$params = [];
$types = "";

if (!empty($search_term)) {
    $sql .= " WHERE title LIKE ? OR content LIKE ?";
    $like_term = "%" . $search_term . "%";
    $params[] = $like_term;
    $params[] = $like_term;
    $types .= "ss";
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
        
        // Date Logic
        $post_date = new DateTime($row['created_at']);
        $now = new DateTime();
        $diff = $now->diff($post_date);
        $hours = ($diff->days * 24) + $diff->h;
        $title = htmlspecialchars($row['title']);
        
        echo "<div class='" . $card_class . "'>";
        
        if ($hours < 1) {
            echo "<h2>" . $title . " <span class='pulse-badge'>JUST NOW</span></h2>";
        } elseif ($diff->days < 1) {
            echo "<h2>" . $title . " <span class='new-badge'>NEW!</span></h2>";
        } else {
            echo "<h2>" . $title . "</h2>";
        }
        
        // Note: 'notice-content' class is important for Read More script
        echo "<p class='notice-content'>" . htmlspecialchars($row['content']) . "</p>";
        echo "<small>Posted on: " . htmlspecialchars(date('d-m-Y g:i A', strtotime($row['created_at']))) . "</small>";
        echo "</div>";
    }
} else {
    echo "<div class='notice-card'><p>No notices found matching your search.</p></div>";
}
$conn->close();
?>
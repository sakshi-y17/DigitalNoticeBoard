<?php  
include 'config.php';  
$conn = new mysqli($servername, $username, $password, $dbname, 3307);  
if ($conn->connect_error) { echo "<tr><td colspan='5'>Connection Failed</td></tr>"; exit; }  
$search_term = $_POST['query'] ?? '';  
$sql = "SELECT id, title, content, created_at, is_pinned FROM notices";  
$params = []; $types = "";  
if (!empty($search_term)) {  
  $sql .= " WHERE title LIKE ? OR content LIKE ?";  
  $like_term = "%" . $search_term . "%";  
  $params = [$like_term, $like_term]; $types = "ss";  
}  
$sql .= " ORDER BY is_pinned DESC, id DESC";  
$stmt = $conn->prepare($sql);  
if (!empty($params)) { $stmt->bind_param($types, ...$params); }  
$stmt->execute(); $result = $stmt->get_result();  
if ($result->num_rows > 0) {  
  while ($row = $result->fetch_assoc()) {  
    $row_class = $row['is_pinned'] == 1 ? 'pinned-row' : '';  
    echo "<tr class='" . $row_class . "'>";  
    echo "<td>" . $row['id'] . "</td>";  
    echo "<td>" . htmlspecialchars($row['title']) . "</td>";  
    echo "<td>" . htmlspecialchars(substr($row['content'], 0, 50)) . "...</td>";   
    echo "<td>" . htmlspecialchars(date('d-m-Y', strtotime($row['created_at']))) . "</td>";  
    echo "<td class='actions'>";  
    echo "<a href='edit_notice.php?id=" . $row['id'] . "' class='btn btn-primary'>Edit</a>";  
    echo "<form action='api.php' method='POST' style='display:inline;'>";  
    echo "<input type='hidden' name='action' value='delete'>";  
    echo "<input type='hidden' name='notice_id' value='" . $row['id'] . "'>";  
    echo "<button type='submit' class='btn btn-delete' onclick='return confirm(\"Delete?\");'>Delete</button>";  
    echo "</form></td></tr>";  
  }  
} else {  
  echo "<tr><td colspan='5'>No notices found.</td></tr>";  
}  
$conn->close();  
?>  
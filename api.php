<?php
include 'config.php'; 

$conn = new mysqli($servername, $username, $password, $dbname, 3307);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // --- DELETE Action ---
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $notice_id = $_POST['notice_id'] ?? 0;
        $stmt = $conn->prepare("DELETE FROM notices WHERE id = ?");
        $stmt->bind_param("i", $notice_id);
        $stmt->execute();
        
        // *** FIX: Redirect to 'dashboard' ***
        header("Location: dashboard?status=deleted");
        exit();
    }

    // --- UPDATE Action ---
    else if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $notice_id = $_POST['notice_id'] ?? 0;
        $title = $_POST['title'] ?? '';
        $category = $_POST['category'] ?? 'General';
        $message = $_POST['message'] ?? '';
        $is_pinned = isset($_POST['is_pinned']) ? 1 : 0;
        
        $stmt = $conn->prepare("UPDATE notices SET title = ?, content = ?, is_pinned = ? WHERE id = ?");
        $full_description = "[" . $category . "] " . $message;
        $stmt->bind_param("ssii", $title, $full_description, $is_pinned, $notice_id);
        
        if ($stmt->execute()) {
            // *** FIX: Redirect to 'dashboard' ***
            header("Location: dashboard?status=updated");
            exit();
        } else {
            echo "<h1>Error updating notice: " . $stmt->error . "</h1>";
        }
    }

    // --- ADD Action ---
    else if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $title = $_POST['title'] ?? '';
        $category = $_POST['category'] ?? 'General';
        $message = $_POST['message'] ?? '';
        $is_pinned = isset($_POST['is_pinned']) ? 1 : 0;

        $stmt = $conn->prepare("INSERT INTO notices (title, content, is_pinned) VALUES (?, ?, ?)");
        $full_description = "[" . $category . "] " . $message;
        $stmt->bind_param("ssi", $title, $full_description, $is_pinned);

        if ($stmt->execute()) {
            // *** FIX: Redirect to 'dashboard' ***
            header("Location: dashboard?status=added");
            exit();
        } else {
            echo "<h1>Error adding notice: " . $stmt->error . "</h1>";
        }
    }
}
else {
    header("Location: ./");
}
$conn->close();
?>
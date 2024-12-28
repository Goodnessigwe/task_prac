<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'task_prac');
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    if ($_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "uploads/profile_images/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $image_name = basename($_FILES['profile_image']['name']);
        $target_path = $upload_dir . uniqid() . "_" . $image_name;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_path)) {
            // Update profile image in the database
            $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            $stmt->bind_param("si", $target_path, $user_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: dashboard.php");
    exit();
}
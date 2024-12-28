<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'task_prac');
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->query("DELETE FROM tasks WHERE user_id = $user_id AND status = 'done'");
    header("Location: history.php");
    exit();
}
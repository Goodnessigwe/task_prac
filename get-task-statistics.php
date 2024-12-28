<?php
require 'db_connect.php';
session_start(); // Start session to access logged-in user's data

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

// Get the logged-in user's ID
$userId = $_SESSION['user_id'];

// Initialize counts
$read = 0;
$processing = 0;
$done = 0;

try {
    // Query to count tasks with status 'read' for the logged-in user
    $queryRead = "SELECT COUNT(*) AS count FROM tasks WHERE status = 'read' AND user_id = ?";
    $stmtRead = $conn->prepare($queryRead);
    $stmtRead->bind_param("i", $userId);
    $stmtRead->execute();
    $resultRead = $stmtRead->get_result();
    $read = $resultRead->fetch_assoc()['count'];

    // Query to count tasks with status 'processing' for the logged-in user
    $queryProcessing = "SELECT COUNT(*) AS count FROM tasks WHERE status = 'processing' AND user_id = ?";
    $stmtProcessing = $conn->prepare($queryProcessing);
    $stmtProcessing->bind_param("i", $userId);
    $stmtProcessing->execute();
    $resultProcessing = $stmtProcessing->get_result();
    $processing = $resultProcessing->fetch_assoc()['count'];

    // Query to count tasks with status 'done' for the logged-in user
    $queryDone = "SELECT COUNT(*) AS count FROM tasks WHERE status = 'done' AND user_id = ?";
    $stmtDone = $conn->prepare($queryDone);
    $stmtDone->bind_param("i", $userId);
    $stmtDone->execute();
    $resultDone = $stmtDone->get_result();
    $done = $resultDone->fetch_assoc()['count'];

    // Return the data as JSON
    echo json_encode([
        'read' => $read,
        'processing' => $processing,
        'done' => $done,
    ]);
} catch (Exception $e) {
    // Handle exceptions and return an error
    echo json_encode([
        'error' => 'Failed to fetch task statistics: ' . $e->getMessage(),
    ]);
}
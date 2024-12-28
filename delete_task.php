<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'task_prac');
$user_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $task_id = intval($_GET['id']);
    $redirect_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard.php'; // Redirect to either dashboard or history

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Get the associated section ID for the task
        $result = $conn->query("SELECT section_id FROM tasks WHERE id = $task_id AND user_id = $user_id");
        $task = $result->fetch_assoc();

        if ($task) {
            $section_id = $task['section_id'];

            // Delete the task
            $conn->query("DELETE FROM tasks WHERE id = $task_id AND user_id = $user_id");

            // Check if the section has no other tasks
            $section_tasks = $conn->query("SELECT COUNT(*) AS task_count FROM tasks WHERE section_id = $section_id AND user_id = $user_id");
            $task_count = $section_tasks->fetch_assoc()['task_count'];

            if ($task_count == 0) {
                // Delete the section if no tasks are associated
                $conn->query("DELETE FROM sections WHERE id = $section_id AND user_id = $user_id");
            }

            // Commit the transaction
            $conn->commit();
        } else {
            throw new Exception("Task not found or unauthorized access.");
        }
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
        exit();
    }

    // Redirect to the originating page
    header("Location: $redirect_page");
    exit();
}
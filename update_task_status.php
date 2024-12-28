<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    $task_id = $data['task_id'] ?? null;
    $new_status = $data['status'] ?? null;

    // Validate inputs
    if ($task_id && in_array($new_status, ['read', 'done', 'processing'])) {
        // Determine if we need to set completed_date
        if ($new_status === 'done') {
            $query = "UPDATE tasks SET status = ?, completed_date = NOW() WHERE id = ?";
        } else {
            $query = "UPDATE tasks SET status = ?, completed_date = NULL WHERE id = ?";
        }

        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $new_status, $task_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No changes made.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid inputs.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
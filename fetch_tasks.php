<?php
include 'db_connect.php';
session_start();

$user_id = $_SESSION['user_id'];

// Fetch tasks by status
$tasks = $conn->query("SELECT tasks.*, sections.name AS section_name 
                       FROM tasks 
                       JOIN sections ON tasks.section_id = sections.id 
                       WHERE tasks.user_id = $user_id AND tasks.status = 'read'");
$processing_tasks = $conn->query("SELECT tasks.*, sections.name AS section_name 
                                  FROM tasks 
                                  JOIN sections ON tasks.section_id = sections.id 
                                  WHERE tasks.user_id = $user_id AND tasks.status = 'processing'");
$done_tasks = $conn->query("SELECT tasks.*, sections.name AS section_name 
                            FROM tasks 
                            JOIN sections ON tasks.section_id = sections.id 
                            WHERE tasks.user_id = $user_id AND tasks.status = 'done'");

// Generate HTML for each table
function generateTableRows($tasks)
{
    $html = '';
    while ($task = $tasks->fetch_assoc()) {
        $html .= "<tr>
                    <td>{$task['id']}</td>
                    <td>" . htmlspecialchars($task['section_name']) . "</td>
                    <td>" . htmlspecialchars($task['title']) . "</td>
                    <td>" . htmlspecialchars($task['description']) . "</td>
                    <td>" . htmlspecialchars($task['due_date']) . "</td>";
        if ($task['status'] === 'read') {
            $html .= "<td>
                        <button id='status-button-{$task['id']}' onclick=\"updateStatus({$task['id']}, 'done')\">Done</button>
                        <button id='status-button-{$task['id']}' onclick=\"updateStatus({$task['id']}, 'processing')\">Processing</button>
                      </td>";
        } elseif ($task['status'] === 'done') {
            $html .= "<td><button onclick=\"deleteTask({$task['id']})\">Delete</button></td>";
        }
        $html .= "</tr>";
    }
    return $html;
}

echo json_encode([
    'success' => true,
    'tasks_html' => generateTableRows($tasks),
    'processing_html' => generateTableRows($processing_tasks),
    'done_html' => generateTableRows($done_tasks)
]);
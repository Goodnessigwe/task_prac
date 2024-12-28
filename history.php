<?php
session_start();
require 'db_connect.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the logged-in user's profile image
$user_query = $conn->query("SELECT profile_image FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();

// Default image if no profile image is uploaded
$profile_image = $user['profile_image'] ? $user['profile_image'] : 'uploads/profile_images/default-profile.png';
// Fetch completed tasks
$completed_tasks = $conn->query("
    SELECT
         id,  
        title, 
        created_date, 
        due_date, 
        completed_date 
    FROM tasks 
    WHERE user_id = $user_id AND status = 'done' 
    ORDER BY completed_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Task Manager History</title>
<link rel="stylesheet" href="css/styles.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body {
    font-family: Arial, sans-serif;
    background-color: #0d0c22;
    color: white;
    /* margin: 0;
    padding: 20px; */
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

th,
td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #444;
}

th {
    background-color: #252540;
    color: white;
}

tr:hover {
    background-color: #333;
}

h1 {
    margin-bottom: 20px;
}
</style>
</head>

<body>

    <header class="header">

        <?php
        // Fetch the user's profile image from the database
        $user_query = $conn->query("SELECT profile_image FROM users WHERE id = $user_id");
        $user = $user_query->fetch_assoc();

        $profile_image = $user['profile_image'] ?? 'uploads/profile_images/default-profile.png'; // Use default if none exists
        ?>
        <div class="header-left">
            <h1 class="app-title">Task Manager History</h1>
        </div>
        <div class="header-right">
            <!-- Main Profile Image -->
            <div class="main-profile">
                <img id="imagePreviewMain" src="<?= htmlspecialchars($profile_image) ?>" alt="Main Profile Image">
            </div>

        </div>
    </header>

    <?php
    // Get the current page name
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>
    <nav class="navbar">
        <button class="dashboard-btn <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
            <a href="dashboard.php">Dashboard</a>
        </button>
        <button class="add-task-btn <?= $current_page == 'create_task.php' ? 'active' : '' ?>">
            <a href="create_task.php">Add New Task</a>
        </button>
        <button class="history-btn <?= $current_page == 'history.php' ? 'active' : '' ?>">
            <a href="history.php">History</a>
        </button>
        <button class="logout-btn <?= $current_page == 'logout.php' ? 'active' : '' ?>">
            <a href="logout.php">Logout</a>
        </button>
    </nav>

    <main class="dashboard">

        <section class="task-tables">

            <body>
                <h1>Completed Tasks History</h1>
                <!-- Clear All Button -->
                <form action="clear_all_tasks.php" method="POST" style="margin-bottom: 20px;">
                    <button type="submit" class="action-btn btn-clear-all">Clear All</button>
                </form>


                <table>

                    <thead>
                        <tr>
                            <th>Task Name</th>
                            <th>Created Date</th>
                            <th>Due Date</th>
                            <th>Completed Date</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($completed_tasks->num_rows > 0): ?>
                        <?php while ($task = $completed_tasks->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($task['title']) ?></td>
                            <td>
                                <?= htmlspecialchars(
                                            $task['created_date']
                                                ? date("Y-m-d H:i:s", strtotime($task['created_date']))
                                                : "N/A"
                                        ) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars(
                                            $task['due_date']
                                                ? date("Y-m-d", strtotime($task['due_date']))
                                                : "N/A"
                                        ) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars(
                                            $task['completed_date']
                                                ? date("Y-m-d H:i:s", strtotime($task['completed_date']))
                                                : "N/A"
                                        ) ?>
                            </td>
                            <td>
                                <!-- Delete Button for Each Task -->

                                <a href="delete_task.php?id=<?= htmlspecialchars($task['id']) ?>&page=history.php"
                                    class="action-btn btn-delete">Delete</a>

                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="4">No completed tasks found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>

        </section>
    </main>

    <script>
    // Delete a single task
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', () => {
            const taskId = button.getAttribute('data-id');

            fetch(`delete_task.php?id=${taskId}`, {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`task-${taskId}`).remove();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));

        });
    });

    // Clear all tasks
    document.getElementById('clearAllButton').addEventListener('click', () => {
        if (confirm('Are you sure you want to delete all tasks?')) {
            fetch('delete_all_tasks.php', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector('tbody').innerHTML =
                            '<tr><td colspan="5">No completed tasks found.</td></tr>';
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });
    </script>

</body>

</html>
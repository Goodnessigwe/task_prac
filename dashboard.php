<?php
session_start();
require 'db_connect.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

//To display the count of tasks in each category (read, processing, done), fetch the counts in PHP:
$readCount = $conn->query("SELECT COUNT(*) AS count FROM tasks WHERE status = 'read' AND user_id = $user_id")->fetch_assoc()['count'];
$processingCount = $conn->query("SELECT COUNT(*) AS count FROM tasks WHERE status = 'processing' AND user_id = $user_id")->fetch_assoc()['count'];
$doneCount = $conn->query("SELECT COUNT(*) AS count FROM tasks WHERE status = 'done' AND user_id = $user_id")->fetch_assoc()['count'];

// Fetch the logged-in user's profile image
$user_query = $conn->query("SELECT profile_image FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();

// Default image if no profile image is uploaded
$profile_image = $user['profile_image'] ? $user['profile_image'] : 'uploads/profile_images/default-profile.png';


// Fetch sections
$sections = $conn->query("SELECT * FROM sections WHERE user_id = $user_id");

// Fetch tasks
$tasks = $conn->query("SELECT tasks.*, sections.name AS section_name 
                       FROM tasks 
                       JOIN sections ON tasks.section_id = sections.id 
                       WHERE tasks.user_id = $user_id AND tasks.status = 'read' ");

// Fetch tasks by status
$read_tasks = $conn->query("SELECT tasks.*, sections.name AS section_name 
                            FROM tasks 
                            JOIN sections ON tasks.section_id = sections.id 
                            WHERE tasks.status = 'read' AND tasks.user_id = $user_id");
$done_tasks = $conn->query("SELECT tasks.*, sections.name AS section_name 
                            FROM tasks 
                            JOIN sections ON tasks.section_id = sections.id 
                            WHERE tasks.status = 'done' AND tasks.user_id = $user_id");
$processing_tasks = $conn->query("SELECT tasks.*, sections.name AS section_name 
                                  FROM tasks 
                                  JOIN sections ON tasks.section_id = sections.id 
                                  WHERE tasks.status = 'processing' AND tasks.user_id = $user_id");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <h1 class="app-title">Task Manager Dashboard</h1>
            <p class="greeting">Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Guest') ?>!</p>
        </div>
        <div class="header-right">
            <!-- Main Profile Image -->
            <div class="main-profile">
                <img id="imagePreviewMain" src="<?= htmlspecialchars($profile_image) ?>" alt="Main Profile Image">
            </div>

            <!-- Always-visible File Input -->
            <div class="upload-controls">
                <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                    <!-- Always-visible File Label -->
                    <label for="profile_image" class="custom-file-label">Select Image</label>
                    <input type="file" name="profile_image" id="profile_image" accept="image/*"
                        onchange="previewImage(event)">

                    <!-- Conditionally Visible Upload Button -->
                    <div class="preview-box" id="previewBox" style="display: none;">
                        <img id="imagePreviewBox" alt="Preview Image">
                        <button type="submit" class="upload-btn">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </header>

    <?php
    // Get the current page name
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>
    <nav class="navbar">
        <button onclick="location.href='dashboard.php'"
            class="dashboard-btn <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
            Dashboard
        </button>

        <button onclick="location.href='create_task.php'"
            class="add-task-btn <?= $current_page == 'create_task.php' ? 'active' : '' ?>">
            Add New Task
        </button>
        <button onclick="location.href='history.php'"
            class="history-btn <?= $current_page == 'history.php' ? 'active' : '' ?>">
            View history
        </button>
        <button onclick="location.href='logout.php'"
            class="logout-btn <?= $current_page == 'logout.php' ? 'active' : '' ?>">
            logout
        </button>
    </nav>

    <main class="dashboard">


        <!-- Chart Section -->
        <section class="statistics-section">
            <div class="chart-container">
                <canvas id="doughnutChart"></canvas>
            </div>
        </section>

        <section class="task-tables">
            <div class="task-table">
                <h2>Read Tasks</h2>
                <table id="viewTasksTable">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>Section Name</th>
                            <th>Task Name</th>
                            <th>Task Description</th>
                            <th>Deadline</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($tasks->num_rows > 0): ?>
                            <?php while ($task = $tasks->fetch_assoc()): ?>
                                <tr id="task-<?= $task['id'] ?>">
                                    <td><?= htmlspecialchars($task['id']) ?></td>
                                    <td><?= htmlspecialchars($task['section_name']) ?></td>
                                    <td><?= htmlspecialchars($task['title']) ?></td>
                                    <td><?= htmlspecialchars($task['description']) ?></td>
                                    <td><?= htmlspecialchars($task['due_date']) ?></td>
                                    <td>
                                        <a href="edit_task.php?id=<?= $task['id'] ?>" class="action-btn btn-edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_task.php?id=<?= $task['id'] ?>&page=dashboard.php"
                                            class="action-btn btn-delete">
                                            <i class="fa fa-trash"></i></a>
                                        </a>
                                        <button class="action-btn btn-done" onclick="moveToDone(<?= $task['id'] ?>)">
                                            <i class="fa fa-check"></i>
                                        </button>
                                        <button class="action-btn btn-processing"
                                            onclick="moveToProcessing(<?= $task['id'] ?>)">
                                            <i class="fa fa-chalkboard"></i>
                                        </button>
                                    </td>

                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No tasks found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="task-table">
                <h2>Processing Tasks</h2>
                <table id="processingTasksTable">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>Section Name</th>
                            <th>Task Name</th>
                            <th>Task Description</th>
                            <th>Deadline</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($task = $processing_tasks->fetch_assoc()): ?>
                            <tr id="processing-task-<?= $task['id'] ?>">
                                <td><?= htmlspecialchars($task['id']) ?></td>
                                <td><?= htmlspecialchars($task['section_name']) ?></td>
                                <td><?= htmlspecialchars($task['title']) ?></td>
                                <td><?= htmlspecialchars($task['description']) ?></td>
                                <td><?= htmlspecialchars($task['due_date']) ?></td>
                                <td>
                                    <button class="action-btn btn-done" onclick="moveToDone(<?= $task['id'] ?>)">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="task-table">
                <h2>Done Tasks</h2>
                <table id="doneTasksTable">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>Section Name</th>
                            <th>Task Name</th>
                            <th>Task Description</th>
                            <th>Deadline</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($task = $done_tasks->fetch_assoc()): ?>
                            <tr id="done-task-<?= $task['id'] ?>">
                                <td><?= htmlspecialchars($task['id']) ?></td>
                                <td><?= htmlspecialchars($task['section_name']) ?></td>
                                <td><?= htmlspecialchars($task['title']) ?></td>
                                <td><?= htmlspecialchars($task['description']) ?></td>
                                <td><?= htmlspecialchars($task['due_date']) ?></td>
                                <td>
                                    <a href="delete_task.php?id=<?= $task['id'] ?>&page=dashboard.php"
                                        class="action-btn btn-delete">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
        // Fetch dynamic data from the server
        async function fetchTaskStatistics() {
            try {
                const response = await fetch('get-task-statistics.php'); // Replace with your PHP script URL
                const data = await response.json();
                if (data.error) {
                    console.error(data.error);
                    return null;
                }
                return data;
            } catch (error) {
                console.error("Error fetching task statistics:", error);
                return null;
            }
        }

        // Initialize Chart.js after fetching data
        async function initializeChart() {
            const taskStats = await fetchTaskStatistics();

            if (!taskStats) {
                console.error("Failed to fetch task statistics.");
                return;
            }

            // Extract counts from the dynamic data
            const {
                read,
                processing,
                done
            } = taskStats;


            // Create the chart
            const ctx = document.getElementById('doughnutChart').getContext('2d');
            const doughnutChart = new Chart(ctx, {
                type: 'doughnut', // Doughnut chart type
                data: {
                    labels: ['Read', 'Processing', 'Done'], // Data labels
                    datasets: [{
                        label: 'Task Statistics',
                        data: [read, processing, done], // Dynamic counts
                        backgroundColor: [
                            'rgba(75, 192, 192, 1)', // Read
                            'rgba(255, 206, 86, 1)', // Processing
                            'rgba(153, 102, 255, 1)' // Done
                        ],
                        borderColor: [
                            'rgba(255, 255, 255, 1)', // White border for contrast
                            'rgba(255, 255, 255, 1)',
                            'rgba(255, 255, 255, 1)'
                        ],
                        borderWidth: 2 // Set border width for distinction
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top', // Position the legend at the top
                            labels: {
                                color: 'white', // White text in the legend
                                font: {
                                    size: 14 // Optional: Adjust font size
                                }
                            }
                        },
                        tooltip: {
                            bodyColor: 'white', // White text in tooltips
                            titleColor: 'white', // Optional: White title text
                            backgroundColor: 'rgba(0, 0, 0, 0.7)', // Optional: Black tooltip background
                            borderColor: '#FFFFFF',
                            borderWidth: 1
                        }
                    }
                }
            });

        }
        // Call the function to initialize the chart
        initializeChart();

        function previewImage(event) {
            const reader = new FileReader();
            const previewBox = document.getElementById('previewBox');
            const imagePreviewBox = document.getElementById('imagePreviewBox');

            reader.onload = function() {
                imagePreviewBox.src = reader.result; // Display the selected image
                previewBox.style.display = 'flex'; // Show the preview box with the Upload button
            };

            if (event.target.files && event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]); // Read the selected file
            } else {
                previewBox.style.display = 'none'; // Hide the preview box if no file is selected
            }
        }

        function moveToDone(taskId) {
            // Check if the row exists in the View or Processing table
            let taskRow = document.querySelector(`#task-${taskId}`) || document.querySelector(`#processing-task-${taskId}`);
            if (!taskRow) return; // Exit if the task row doesn't exist

            // Clone the row
            const doneTableBody = document.querySelector("#doneTasksTable tbody");
            const newRow = taskRow.cloneNode(true);

            // Update the row's ID for the "Done" table
            newRow.id = `done-task-${taskId}`;

            // Remove unnecessary buttons
            const actionsCell = newRow.querySelector("td:last-child");
            actionsCell.innerHTML = '<a href="delete_task.php?id=' + taskId + '">Delete</a>';

            // Append the row to the Done table
            doneTableBody.appendChild(newRow);

            // Remove the original row
            taskRow.remove();

            // Update the database status
            updateStatusInDatabase(taskId, "done");
        }

        function moveToProcessing(taskId) {
            const taskRow = document.querySelector(`#task-${taskId}`);
            if (!taskRow) return;

            // Clone the row
            const processingTableBody = document.querySelector("#processingTasksTable tbody");
            const newRow = taskRow.cloneNode(true);

            // Update the row's ID for the "Processing" table
            newRow.id = `processing-task-${taskId}`;

            // Update buttons
            const actionsCell = newRow.querySelector("td:last-child");
            actionsCell.innerHTML = '<button onclick="moveToDone(' + taskId + ')">Done</button>';

            // Append the row to the Processing table
            processingTableBody.appendChild(newRow);

            // Remove the original row
            taskRow.remove();

            // Update the database status
            updateStatusInDatabase(taskId, "processing");
        }

        function updateStatusInDatabase(taskId, status) {
            fetch("update_task_status.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        task_id: taskId,
                        status: status
                    }),
                })
                .then((response) => response.json())
                .then((data) => {
                    if (!data.success) {
                        console.error(data.message);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                });
        }
    </script>

</body>

</html>
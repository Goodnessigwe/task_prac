<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $task_id = $_GET['id'];

    // Fetch the task details
    $stmt = $conn->prepare("SELECT tasks.*, sections.name AS section_name FROM tasks 
                            LEFT JOIN sections ON tasks.section_id = sections.id 
                            WHERE tasks.id = ? AND tasks.user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();

    if ($task) {
        $title = $task['title'];
        $description = $task['description'];
        $due_date = $task['due_date'];
        $section_name = $task['section_name']; // Existing section name
    } else {
        header("Location: dashboard.php"); // Redirect if task not found
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $due_date = $_POST['due_date'];
        $section_name = trim($_POST['section_name']);

        // Validation
        $errors = [];
        if (empty($title)) {
            $errors[] = "Task title is required.";
        }
        if (empty($due_date)) {
            $errors[] = "Due date is required.";
        }
        if (empty($section_name)) {
            $errors[] = "Section name is required.";
        }

        if (empty($errors)) {

            // Retrieve the section ID associated with the task
            $section_id = $task['section_id']; // Use section_id from the fetched task

            // Update the section name
            $stmt = $conn->prepare("UPDATE sections SET name = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("sii", $section_name, $section_id, $user_id);
            $stmt->execute();
        }

        // Update the task details
        $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, due_date = ?, section_id = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sssiii", $title, $description, $due_date, $section_id, $task_id, $user_id);
        $stmt->execute();

        // Redirect to the dashboard upon successful update
        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Edit Task</title>
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
            <h1 class="app-title">Update Task</h1>
        </div>
        <div class="header-right">
            <!-- Main Profile Image -->
            <div class="main-profile">
                <img id="imagePreviewMain" src="<?= htmlspecialchars($profile_image) ?>" alt="Main Profile Image">
            </div>
        </div>
    </header>

    <nav class="navbar">
        <button onclick="location.href='dashboard.php'" class="dashboard-btn">
            Dashboard
        </button>
    </nav>

    <main class="dashboard">
        <div class="form-container">
            <?php if (!empty($errors)): ?>
                <div class="error-messages">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <label for="title">Task Title</label>
                <input type="text" name="title" id="title" value="<?= htmlspecialchars($title) ?>"
                    placeholder="Enter task title" required>

                <label for="description">Task Description</label>
                <textarea name="description" id="description"
                    placeholder="Enter task description"><?= htmlspecialchars($description) ?></textarea>

                <label for="due_date">Due Date</label>
                <input type="date" name="due_date" id="due_date" value="<?= htmlspecialchars($due_date) ?>" required>

                <label for="section_name">Section Name</label>
                <input type="text" name="section_name" id="section_name" value="<?= htmlspecialchars($section_name) ?>"
                    required>

                <button type="submit">Update Task</button>
            </form>
        </div>
    </main>
</body>

</html>
<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Initialize variables for validation and error handling
$errors = [];
$title = $description = $due_date = $section_id = $section_name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form inputs
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    $section_id = $_POST['section_id'];
    $section_name = trim($_POST['section_name']);

    // Validation
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
        // If section_name is not empty, create it
        if (!empty($section_name)) {
            $stmt = $conn->prepare("INSERT INTO sections (user_id, name) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $section_name);
            $stmt->execute();
            $section_id = $conn->insert_id; // Get the ID of the created section
        }

        // Insert the task
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, section_id, title, description, due_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $user_id, $section_id, $title, $description, $due_date);
        $stmt->execute();

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
    <title>Create Task</title>
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
            <h1 class="app-title">Create New Task </h1>
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
        <div class="form-container">

            <!-- Display Validation Errors -->
            <?php if (!empty($errors)): ?>
                <div class="error-messages">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
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


                <label for="section_name">Add Section Name</label>
                <input type="text" name="section_name" id="section_name" value="<?= htmlspecialchars($section_name) ?>"
                    placeholder="Enter section name" required>
                <button type="submit">Add Task</button>
            </form>

        </div>

    </main>

</body>

</html>
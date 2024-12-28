<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $default_image = 'uploads/profile_images/default-profile.png'; // Path to default image
    $profile_image_path = $default_image; // Set default image path initially

    // Check if an image was uploaded
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "uploads/profile_images/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $image_name = basename($_FILES['profile_image']['name']);
        $target_path = $upload_dir . uniqid() . "_" . $image_name;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_path)) {
            $profile_image_path = $target_path; // Override default image with uploaded image path
        }
    }

    // Save user data to the database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, profile_image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $profile_image_path);
    $stmt->execute();

    header("Location: login.php");
    exit();
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

        <div class="header-left">
            <h1 class="app-title">Task Manager App </h1>
        </div>
        <div class="header-right">
            <div class="main-profile">
                <a href="login.php">
                    <h2 class="app-title">Login</h2>
                </a>
            </div>

        </div>
    </header>

    <?php

    ?>
    <nav class="navbar">

    </nav>

    <main class="dashboard">
        <div class="form-container">
            </h2 style="color: #0d0c22">Signup Form</h2>
            <form method="POST" enctype="multipart/form-data">
                <label for="username">username</label>
                <input type="text" name="username" placeholder="Username" required>
                <label for="email">email</label>
                <input type="email" name="email" placeholder="Email" required>
                <label for="password">password</label>
                <input type="password" name="password" placeholder="Password" required>
                <label for="profile_image">Upload Profile Image:</label>
                <input type="file" name="profile_image" id="profile_image" accept="image/*">
                <button type="submit">Signup</button>
            </form>

        </div>

    </main>

</body>

</html>
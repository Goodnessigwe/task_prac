  <?php
    session_start();

    require 'db_connect.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id,username, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username']; // Add username to session

                // Debug: Confirm session variables are set
                echo "Session username: " . $_SESSION['username'] . "<br>";
                echo "Session user_id: " . $_SESSION['user_id'] . "<br>";

                header("Location: dashboard.php");
                exit();
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "User not found with this email.";
        }

        $stmt->close();
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
                  <a href="index.php">
                      <h2 class="app-title">Signup</h2>
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
              </h2 style="color: #0d0c22">Login Form</h2>
              <form method="POST">
                  <label for="email">email</label>
                  <input type="email" name="email" placeholder="Email Address" required>
                  <label for="password">password</label>
                  <input type="password" name="password" placeholder="Password" required>
                  <button type="submit">Login</button>
              </form>

          </div>

      </main>

  </body>

  </html>
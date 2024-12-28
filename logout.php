<?php
// Start the session
session_start();

// Destroy all session data
session_unset(); // Free all session variables
session_destroy(); // Destroy the session

// Redirect the user to the login page
header("Location: login.php");
exit(); // Ensure no further code is executed
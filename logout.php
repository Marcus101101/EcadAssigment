<?php
// Start the session
session_start();

// Destroy the session
session_destroy();

// Redirect to home page (index.php)
header("Location: index.php");
exit();
?>
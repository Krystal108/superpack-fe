<?php

session_start();
session_unset();  // Unset all session variables
session_destroy(); // Destroy the session
header('Location: /?page=welcome'); // Redirect to the login page (or your desired page)
exit();
?>

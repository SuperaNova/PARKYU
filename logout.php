<?php
// Initialize the session
session_start();
 
// Unset all of the session variables
$_SESSION = array();
 
// Destroy the session.
if (session_destroy()) {
    // If session destroy was successful, redirect to login page
    header("location: login.php?logout=success");
    exit;
} else {
    // If there was an issue destroying the session, 
    // you could redirect with an error or log the error.
    // For simplicity, we'll still redirect to login but you might want to handle this.
    header("location: login.php?logout=error");
    exit;
}
?> 
<?php
session_start();
include 'connect.php'; // Your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        header("location: login.php?error=Please enter both username and password");
        exit;
    }

    // Prepare SQL statement to prevent SQL injection
    $sql = "SELECT userID, username, password, usertype FROM tbluser WHERE username = ? AND isActive = 1";

    if ($stmt = mysqli_prepare($connection, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = $username;

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $userID, $db_username, $hashed_password, $usertype);
                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, start a new session
                        $_SESSION["loggedin"] = true;
                        $_SESSION["userID"] = $userID;
                        $_SESSION["username"] = $db_username;
                        $_SESSION["usertype"] = $usertype;

                        // Redirect user to dashboard page
                        header("location: dashboard.php");
                        exit;
                    } else {
                        // Password is not valid
                        header("location: login.php?error=Invalid username or password");
                        exit;
                    }
                }
            } else {
                // Username doesn't exist
                header("location: login.php?error=Invalid username or password");
                exit;
            }
        } else {
            // SQL execution error
            header("location: login.php?error=Oops! Something went wrong. Please try again later.");
            exit;
        }
        mysqli_stmt_close($stmt);
    } else {
        // SQL prepare error
        header("location: login.php?error=Database error. Please try again later.");
        exit;
    }
    mysqli_close($connection);
} else {
    // If not a POST request, redirect to login page or show an error
    header("location: login.php");
    exit;
}
?> 
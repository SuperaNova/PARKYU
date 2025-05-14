<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ParkU</title>
    <link rel="stylesheet" href="design.css">
</head>

<body class="login-page-body">

    <div class="auth-top-bar">
        <a href="Index.php" class="auth-logo-link">
            <img src="res/logo.png" alt="ParkU Logo - Home" />
        </a>
        <!-- Potential placeholder for other top-bar content -->
    </div>

    <div class="login-container">
        <h2>Login to ParkU</h2>
        <form method="POST" action="login_process.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Login</button>
        </form>
        <p class="register-link" style="text-align: center; margin-top: 20px;">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
        <?php 
        if (isset($_GET['error'])) {
            echo '<p class="error-message" style="color: red; text-align: center; margin-top: 10px;">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        ?>
    </div>

</body>

</html>

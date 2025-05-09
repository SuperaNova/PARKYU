<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ParkU</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <div class="login-container">
        <h2>Login to ParkU</h2>
        <form onsubmit="return validateLogin()">
            <label for="username">Username</label>
            <input type="text" id="username" placeholder="Enter your username" required>

            <label for="password">Password</label>
            <input type="password" id="password" placeholder="Enter your password" required>

            <button type="submit">Login</button>
        </form>
        <p id="error-message"></p>
    </div>

    <script src="script.js"></script>

</body>

</html>

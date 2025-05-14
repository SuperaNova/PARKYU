<?php
session_start();
include 'connect.php';

// Optional: Admin authentication check
// if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
//    header("Location: login.php");
//    exit();
// }

$users = [];
$userQuery = "SELECT userID, firstName, lastName, username FROM tbluser ORDER BY lastName, firstName";
$userResult = mysqli_query($connection, $userQuery);
if ($userResult) {
    while ($row = mysqli_fetch_assoc($userResult)) {
        $users[] = $row;
    }
}

$message = '';
$message_type = '';

if (isset($_POST['btnRegisterVehicle'])) {
    $selectedUserID = $_POST['selected_user_id'] ?? null;
    $licensePlate = trim($_POST['license_plate']);
    $vehicleType = $_POST['vehicle_type_enum'] ?? null; // Changed from vehicle_type to match new ENUM field
    $vehicleMake = trim($_POST['vehicle_make']);
    $vehicleModel = trim($_POST['vehicle_model']);
    $vehicleColor = trim($_POST['vehicle_color']);
    // $vehicleCategory is removed as vehicleType ENUM handles this.

    // $isCar and $isMotorcycle are removed as they are not in the new schema

    if (empty($selectedUserID) || empty($licensePlate) || empty($vehicleType) || empty($vehicleMake) || empty($vehicleModel) || empty($vehicleColor) ) { // Added new fields to check
        $message = "All fields are required, including user selection.";
        $message_type = "error";
    } elseif (!preg_match('/^[A-Z0-9\-]+$/i', $licensePlate)) { // Allow lowercase for easier input, can be uppercased before save if needed
        $message = "Invalid license plate format. Use uppercase letters, numbers, and hyphens only.";
        $message_type = "error";
    } else {
        // Convert license plate to uppercase for consistency before saving, if desired
        // $licensePlate = strtoupper($licensePlate);

        // Columns based on dbparkyu_schema_only.sql: userID, licensePlate, vehicleType, vehicleMake, vehicleModel, vehicleColor
        $insertQuery = "INSERT INTO vehicle_register (userID, licensePlate, vehicleType, vehicleMake, vehicleModel, vehicleColor) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection, $insertQuery);
        mysqli_stmt_bind_param($stmt, "isssss", $selectedUserID, $licensePlate, $vehicleType, $vehicleMake, $vehicleModel, $vehicleColor);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "Vehicle '{$licensePlate}' registered successfully for user ID {$selectedUserID}!";
            $message_type = "success";
            // Optionally clear form or redirect
            // header("Location: dashboard.php?tab=someVehicleListTab"); exit;
        } else {
            $message = "Database Error: Could not register vehicle. " . mysqli_error($connection);
            $message_type = "error";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Vehicle - ParkU Admin</title>
    <link rel="stylesheet" href="design.css">
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Using similar styles to adduser.php/addsticker.php for consistency */
        .admin-form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-width: 700px;
            margin: 40px auto;
        }
        .admin-form-container h2 {
            color: #333;
            text-align: center;
            margin-bottom: 25px;
            font-size: 1.8em;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }
        .form-group input[type="text"],
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 1em;
        }
        .form-group input:focus, .form-group select:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .btn-submit-vehicle {
            background-color: #17a2b8; /* Teal for vehicle actions */
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            transition: background-color 0.3s ease;
            display: block;
            width: 100%;
            margin-top: 20px;
        }
        .btn-submit-vehicle:hover {
            background-color: #138496;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            font-size: 1em;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body class="dashboard-page-body">

<header class="dashboard-header">
    <h1>ParkU Vehicle Registration</h1>
</header>

<nav class="dashboard-nav">
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="adduser.php">Add User</a></li>
        <li class="active-nav-item"><a href="vehicleregister.php">Register Vehicle</a></li>
        <li><a href="addsticker.php">Add Sticker</a></li>
        <li><a href="Index.php">Main Site</a></li>
        <li><a href="logout.php" onclick="confirmLogout(event)">Logout</a></li>
    </ul>
</nav>

<div class="admin-form-container">
    <h2>Register New Vehicle</h2>

    <?php if (!empty($message)): ?>
        <div class="message <?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="post" action="vehicleregister.php">
        <div class="form-group">
            <label for="selected_user_id">Select User:</label>
            <select name="selected_user_id" id="selected_user_id" required>
                <option value="">-- Select User --</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo htmlspecialchars($user['userID']); ?>">
                        <?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName'] . ' (' . $user['username'] . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="license_plate">License Plate:</label>
            <input type="text" id="license_plate" name="license_plate" placeholder="e.g., ABC-123 or AB123CD" required>
        </div>
        <div class="form-group">
            <label for="vehicle_type_enum">Vehicle Type:</label>
            <select name="vehicle_type_enum" id="vehicle_type_enum" required>
                <option value="">-- Select Type --</option>
                <option value="Car">Car</option>
                <option value="Motorcycle">Motorcycle</option>
                <option value="Van">Van</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div class="form-group">
            <label for="vehicle_make">Vehicle Make (Brand):</label>
            <input type="text" id="vehicle_make" name="vehicle_make" placeholder="e.g., Toyota, Honda, Yamaha" required>
        </div>
        <div class="form-group">
            <label for="vehicle_model">Vehicle Model:</label>
            <input type="text" id="vehicle_model" name="vehicle_model" placeholder="e.g., Corolla, Civic, NMAX" required>
        </div>
        <div class="form-group">
            <label for="vehicle_color">Vehicle Color:</label>
            <input type="text" id="vehicle_color" name="vehicle_color" placeholder="e.g., Red, Black, Silver" required>
        </div>

        <button type="submit" name="btnRegisterVehicle" class="btn-submit-vehicle">Register Vehicle</button>
    </form>
</div>

<footer class="dashboard-footer">
    © 2025 ParkU | Group ParkU | BSCS - 2nd Year
</footer>

<script>
function confirmLogout(event) {
    if (!confirm("Are you sure you want to logout?")) {
        event.preventDefault();
    }
}
</script>

</body>
</html>

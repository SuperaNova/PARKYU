<?php
session_start();
include 'connect.php';
// include 'readrecords.php'; // Removed as it's not directly used here

$message = '';
$message_type = '';
$userData = null;
$userType = ''; // student, faculty, staff
$userID = null;

// Determine user type based on existing info (similar to dashboard)
function determineUserType($userInfo) {
    if (!empty($userInfo['studentInfoID'])) return 'student';
    if (!empty($userInfo['facultyInfoID'])) return 'faculty';
    if (!empty($userInfo['staffInfoID'])) return 'staff';
    return '';
}

// Fetch user data for the form
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $userID = $_GET['id'];

    // Prepare a query to get all necessary user details
    // This query joins tbluser with tblstudent, tblfaculty, and tblstaff
    // Using LEFT JOINs to ensure we get user data even if role-specific data is missing (though unlikely for existing users)
    $stmt = $connection->prepare("
        SELECT 
            u.userID, u.username, u.firstName, u.lastName, u.email, u.userType AS dbUserType,
            s.studentInfoID, s.program, s.yearLevel,
            f.facultyInfoID, f.department,
            st.staffInfoID, st.office
        FROM tbluser u
        LEFT JOIN tblstudent s ON u.userID = s.userID
        LEFT JOIN tblfaculty f ON u.userID = f.userID
        LEFT JOIN tblstaff st ON u.userID = st.userID
        WHERE u.userID = ?
    ");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $userData = $result->fetch_assoc();
        // Determine user type from the fetched data or the dbUserType column
        if (!empty($userData['dbUserType'])) {
            $userType = $userData['dbUserType'];
        } else {
             // Fallback if dbUserType is not set (legacy data perhaps)
            if (!empty($userData['studentInfoID'])) $userType = 'Student';
            elseif (!empty($userData['facultyInfoID'])) $userType = 'Faculty';
            elseif (!empty($userData['staffInfoID'])) $userType = 'Staff';
        }
    } else {
        $message = "User not found.";
        $message_type = "error";
    }
    $stmt->close();
} else if (!isset($_GET['id'])) {
    // To prevent loading the page without an ID - though the form won't populate.
    // Or redirect: header('Location: dashboard.php'); exit;
}


// Handle Form Submission for Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $userID = filter_input(INPUT_POST, 'userID', FILTER_VALIDATE_INT);
    if (!$userID) {
        $message = "Invalid User ID for update.";
        $message_type = "error";
        // exit or show error, do not proceed
    } else {
        // Re-fetch user data to ensure we're working with the correct user type if it was manipulated
        // This is also a good security measure
        $stmt_check = $connection->prepare("SELECT userType FROM tbluser WHERE userID = ?");
        $stmt_check->bind_param("i", $userID);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows === 1) {
            $currentUserData = $result_check->fetch_assoc();
            $userType = $currentUserData['userType']; // Get the authoritative userType from DB
        } else {
            $message = "User not found for update. Cannot proceed.";
            $message_type = "error";
            // exit or show error
            $userID = null; // Prevent further processing
        }
        $stmt_check->close();

        if ($userID) { // Proceed only if userID is valid and user exists
            $username = trim($_POST['username']);
            $firstName = trim($_POST['firstName']);
            $lastName = trim($_POST['lastName']);
            $email = trim($_POST['email']); // Added email
            $newPassword = $_POST['password']; // Password can be empty

            // Basic validation
            if (empty($username) || empty($firstName) || empty($lastName) || empty($email)) {
                $message = "Username, First Name, Last Name, and Email cannot be empty.";
                $message_type = "error";
            } else {
                $connection->begin_transaction();
                try {
                    // Update tbluser
                    if (!empty($newPassword)) {
                        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                        $stmtUser = $connection->prepare("UPDATE tbluser SET username = ?, firstName = ?, lastName = ?, email = ?, password = ? WHERE userID = ?");
                        $stmtUser->bind_param("sssssi", $username, $firstName, $lastName, $email, $hashedPassword, $userID);
                    } else {
                        $stmtUser = $connection->prepare("UPDATE tbluser SET username = ?, firstName = ?, lastName = ?, email = ? WHERE userID = ?");
                        $stmtUser->bind_param("ssssi", $username, $firstName, $lastName, $email, $userID);
                    }
                    $stmtUser->execute();

                    // Update role-specific table
                    if ($userType === 'Student') {
                        $program = trim($_POST['program']);
                        $yearLevel = trim($_POST['yearLevel']);
                        if (empty($program) || empty($yearLevel)) {
                            throw new Exception("Program and Year Level are required for students.");
                        }
                        $stmtRole = $connection->prepare("UPDATE tblstudent SET program = ?, yearLevel = ? WHERE userID = ?");
                        $stmtRole->bind_param("ssi", $program, $yearLevel, $userID);
                        $stmtRole->execute();
                    } elseif ($userType === 'Faculty') {
                        $department = trim($_POST['department']);
                        if (empty($department)) {
                            throw new Exception("Department is required for faculty.");
                        }
                        $stmtRole = $connection->prepare("UPDATE tblfaculty SET department = ? WHERE userID = ?");
                        $stmtRole->bind_param("si", $department, $userID);
                        $stmtRole->execute();
                    } elseif ($userType === 'Staff') {
                        $office = trim($_POST['office']);
                        if (empty($office)) {
                            throw new Exception("Office is required for staff.");
                        }
                        $stmtRole = $connection->prepare("UPDATE tblstaff SET office = ? WHERE userID = ?");
                        $stmtRole->bind_param("si", $office, $userID);
                        $stmtRole->execute();
                    }

                    $connection->commit();
                    $message = "User details updated successfully!";
                    $message_type = "success";

                    // Refresh userData after update for the form display
                    $stmt_refresh = $connection->prepare("
                        SELECT 
                            u.userID, u.username, u.firstName, u.lastName, u.email, u.userType AS dbUserType,
                            s.studentInfoID, s.program, s.yearLevel,
                            f.facultyInfoID, f.department,
                            st.staffInfoID, st.office
                        FROM tbluser u
                        LEFT JOIN tblstudent s ON u.userID = s.userID
                        LEFT JOIN tblfaculty f ON u.userID = f.userID
                        LEFT JOIN tblstaff st ON u.userID = st.userID
                        WHERE u.userID = ?
                    ");
                    $stmt_refresh->bind_param("i", $userID);
                    $stmt_refresh->execute();
                    $result_refresh = $stmt_refresh->get_result();
                    $userData = $result_refresh->fetch_assoc();
                    // Re-determine userType from refreshed data (though it shouldn't change here)
                    if (!empty($userData['dbUserType'])) {
                        $userType = $userData['dbUserType'];
                    } else {
                        if (!empty($userData['studentInfoID'])) $userType = 'Student';
                        elseif (!empty($userData['facultyInfoID'])) $userType = 'Faculty';
                        elseif (!empty($userData['staffInfoID'])) $userType = 'Staff';
                    }
                    $stmt_refresh->close();


                } catch (Exception $e) {
                    $connection->rollback();
                    $message = "Error updating user: " . $e->getMessage();
                    $message_type = "error";
                }
            }
        }
    }
}

if (!$userData && isset($_GET['id'])) { // If ID was set but user not found after operations
    $message = "User with ID {$userID} not found or could not be loaded.";
    $message_type = "error";
    // To prevent form from showing with no data
    $userID = null; 
} else if (!isset($_GET['id'])) {
    $message = "No User ID provided. Please select a user to update from the dashboard.";
    $message_type = "info";
    $userID = null; 
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User - ParkU Admin</title>
    <link rel="stylesheet" href="design.css">
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="dashboard-page-body">

<header class="dashboard-header">
    <h1>Vehicle Management System Dashboard</h1>
</header>

<nav class="dashboard-nav">
    <ul>
        <li class="active-nav-item"><a href="dashboard.php">Dashboard</a></li>
        <li><a href="adduser.php">Add User</a></li>
        <li><a href="vehicleregister.php">Register Vehicle</a></li>
        <li><a href="addsticker.php">Add Sticker</a></li>
        <li><a href="Index.php">Main Site</a></li>
        <li><a href="logout.php" onclick="confirmLogout(event)">Logout</a></li>
    </ul>
</nav>

<main class="dashboard-main-content-full">
    <div class="form-container admin-form-container">
        <h2>Update User Details (ID: <?php echo htmlspecialchars($userID ?? 'N/A'); ?>)</h2>
        <?php if (!empty($message)): ?>
            <p class="message <?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if ($userID && $userData): // Only show form if user data is loaded ?>
        <form action="update_user.php?id=<?php echo htmlspecialchars($userID); ?>" method="POST" id="updateUserForm">
            <input type="hidden" name="userID" value="<?php echo htmlspecialchars($userData['userID']); ?>">
            
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userData['username'] ?? ''); ?>" required pattern="[a-zA-Z0-9_]{3,20}" title="Username must be 3-20 characters and can contain letters, numbers, and underscores.">

            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($userData['firstName'] ?? ''); ?>" required pattern="[A-Za-z\s'-]+" title="First name can contain letters, spaces, hyphens, and apostrophes.">

            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($userData['lastName'] ?? ''); ?>" required pattern="[A-Za-z\s'-]+" title="Last name can contain letters, spaces, hyphens, and apostrophes.">
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>

            <label for="password">New Password (leave blank to keep current):</label>
            <input type="password" id="password" name="password" pattern=".{8,}" title="Password must be at least 8 characters long.">

            <label for="userType">User Type:</label>
            <input type="text" id="userTypeDisplay" name="userTypeDisplay" value="<?php echo htmlspecialchars(ucfirst($userType)); ?>" readonly disabled class="readonly-input">
            <!-- Hidden input to pass actual userType if needed, but it's mainly determined server-side -->
            <input type="hidden" name="user_type_fixed" value="<?php echo htmlspecialchars($userType); ?>">


            <!-- Student Fields -->
            <div id="studentFields" style="display: <?php echo ($userType === 'Student') ? 'block' : 'none'; ?>;">
                <label for="program">Program:</label>
                <input type="text" id="program" name="program" value="<?php echo htmlspecialchars($userData['program'] ?? ''); ?>" <?php echo ($userType === 'Student') ? 'required' : ''; ?>>
                <label for="yearLevel">Year Level:</label>
                <input type="text" id="yearLevel" name="yearLevel" value="<?php echo htmlspecialchars($userData['yearLevel'] ?? ''); ?>" <?php echo ($userType === 'Student') ? 'required' : ''; ?>>
            </div>

            <!-- Faculty Fields -->
            <div id="facultyFields" style="display: <?php echo ($userType === 'Faculty') ? 'block' : 'none'; ?>;">
                <label for="department">Department:</label>
                <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($userData['department'] ?? ''); ?>" <?php echo ($userType === 'Faculty') ? 'required' : ''; ?>>
            </div>

            <!-- Staff Fields -->
            <div id="staffFields" style="display: <?php echo ($userType === 'Staff') ? 'block' : 'none'; ?>;">
                <label for="office">Office:</label>
                <input type="text" id="office" name="office" value="<?php echo htmlspecialchars($userData['office'] ?? ''); ?>" <?php echo ($userType === 'Staff') ? 'required' : ''; ?>>
            </div>

            <button type="submit" name="update_user" class="btn">Update User</button>
        </form>
        <?php elseif (!$userID && !isset($_GET['id'])): // Message when no ID is provided initially ?>
            <p class="message info">Please select a user from the dashboard to update.</p>
        <?php endif; ?>
    </div>
</main>

<footer class="dashboard-footer">
    © 2025 ParkU | Group ParkU | BSCS - 2nd Year
</footer>

<script>
// No dynamic field switching needed here as user type is fixed for an existing user.
// The PHP logic will display the correct fields based on $userType.
// However, if we wanted to allow changing user type (more complex), then JS would be needed.
function confirmLogout(event) {
    if (!confirm("Are you sure you want to logout?")) {
        event.preventDefault();
    }
}
</script>

</body>
</html> 
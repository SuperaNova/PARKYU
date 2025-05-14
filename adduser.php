<?php
session_start(); // Ensure session is started for potential auth checks
include 'connect.php';

// Optional: Add authentication check if this page should be admin-only
// if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) { // Or however you track admin status
//    header("Location: login.php");
//    exit();
// }

$message = '';
$message_type = '';

if (isset($_POST['btnSubmitUser'])) {
    $fname = trim($_POST['txtfirstname']);
    $lname = trim($_POST['txtlastname']);
    $gender = $_POST['txtgender'];
    $email = trim($_POST['txtemail']); // Added email field
    $utype = $_POST['txtusertype'];
    $uname = trim($_POST['txtusername']);
    $pword = $_POST['txtpassword'];
    
    $prog = null;
    $yearlevel = null;
    $department = null;
    $office = null;

    if ($utype == 'student') {
        $prog = $_POST['txtprogram'];
        $yearlevel = $_POST['txtyearlevel'];
    } elseif ($utype == 'faculty') {
        $department = $_POST['txtdepartment'];
    } elseif ($utype == 'staff') {
        $office = $_POST['txtoffice'];
    }

    // Validation
    if (empty($fname) || empty($lname) || empty($gender) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($utype) || empty($uname) || empty($pword) ||
        ($utype == 'student' && (empty($prog) || empty($yearlevel))) ||
        ($utype == 'faculty' && empty($department)) ||
        ($utype == 'staff' && empty($office)))
    {
        $message = "Please fill in all required fields with valid data.";
        $message_type = "error";
    } elseif (strlen($uname) < 4 || strlen($pword) < 6) {
        $message = "Username must be at least 4 characters and password at least 6 characters.";
        $message_type = "error";
    } else {
        $checkQuery = "SELECT username FROM tbluser WHERE username = ? OR email = ?";
        $checkStmt = mysqli_prepare($connection, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "ss", $uname, $email);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);

        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            $message = "Username or Email already exists. Please choose another.";
            $message_type = "error";
        } else {
            $hashedpw = password_hash($pword, PASSWORD_DEFAULT);

            $sql1 = "INSERT INTO tbluser (firstName, lastName, gender, email, usertype, username, password)
                     VALUES (?, ?, ?, ?, ?, ?, ?)"; // Added email to query
            $stmt1 = mysqli_prepare($connection, $sql1);
            mysqli_stmt_bind_param($stmt1, "sssssss", $fname, $lname, $gender, $email, $utype, $uname, $hashedpw); // Added 's' for email

            if (mysqli_stmt_execute($stmt1)) {
                $last_id = mysqli_insert_id($connection);
                $user_created_successfully = true;

                if ($utype == 'student') {
                    $sql2 = "INSERT INTO tblstudent (program, yearLevel, userID) VALUES (?, ?, ?)";
                    $stmt2 = mysqli_prepare($connection, $sql2);
                    mysqli_stmt_bind_param($stmt2, "sii", $prog, $yearlevel, $last_id);
                    if (!mysqli_stmt_execute($stmt2)) {
                        $message = "User account created, but failed to save student specific data: " . mysqli_error($connection);
                        $message_type = "error";
                        $user_created_successfully = false; 
                    }
                } elseif ($utype == 'faculty') {
                    $sql_faculty = "INSERT INTO tblfaculty (department, userID) VALUES (?, ?)";
                    $stmt_faculty = mysqli_prepare($connection, $sql_faculty);
                    mysqli_stmt_bind_param($stmt_faculty, "si", $department, $last_id);
                    if (!mysqli_stmt_execute($stmt_faculty)) {
                        $message = "User account created, but failed to save faculty specific data: " . mysqli_error($connection);
                        $message_type = "error";
                        $user_created_successfully = false;
                    }
                } elseif ($utype == 'staff') {
                    $sql_staff = "INSERT INTO tblstaff (office, userID) VALUES (?, ?)";
                    $stmt_staff = mysqli_prepare($connection, $sql_staff);
                    mysqli_stmt_bind_param($stmt_staff, "si", $office, $last_id);
                    if (!mysqli_stmt_execute($stmt_staff)) {
                        $message = "User account created, but failed to save staff specific data: " . mysqli_error($connection);
                        $message_type = "error";
                        $user_created_successfully = false;
                    }
                }

                if ($user_created_successfully && empty($message)) {
                    $message = "New user '{$uname}' created successfully!";
                    $message_type = "success";
                    // Optionally clear form or redirect
                    // header("Location: dashboard.php?tab=userTableContainer"); exit;
                }
            } else {
                $message = "Failed to save user data: " . mysqli_error($connection);
                $message_type = "error";
            }
        }
        mysqli_stmt_close($checkStmt);
        if(isset($stmt1)) mysqli_stmt_close($stmt1);
        // other stmt close if they were prepared
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User - ParkU Admin</title>
    <link rel="stylesheet" href="design.css">
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Using similar styles to addsticker.php for consistency */
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
            margin-bottom: 15px; /* Adjusted margin */
        }
        .form-group label {
            display: block;
            margin-bottom: 5px; /* Adjusted margin */
            font-weight: 600;
            color: #555;
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group select {
            width: 100%;
            padding: 10px; /* Adjusted padding */
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
        .btn-submit-user {
            background-color: #007bff; /* Blue for general submit */
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
        .btn-submit-user:hover {
            background-color: #0056b3;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            font-size: 1em; /* Adjusted font size */
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
        /* Styles for conditionally displayed field groups */
        #studentFields, #facultyFields, #staffFields {
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 4px;
            margin-top: 10px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body class="dashboard-page-body">

<header class="dashboard-header">
    <h1>ParkU User Management</h1>
</header>

<nav class="dashboard-nav">
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li class="active-nav-item"><a href="adduser.php">Add User</a></li>
        <li><a href="vehicleregister.php">Register Vehicle</a></li>
        <li><a href="addsticker.php">Add Sticker</a></li>
        <li><a href="Index.php">Main Site</a></li>
        <li><a href="logout.php" onclick="confirmLogout(event)">Logout</a></li>
    </ul>
</nav>

<div class="admin-form-container">
    <h2>Add New User</h2>

    <?php if (!empty($message)): ?>
        <div class="message <?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="post" action="adduser.php">
        <div class="form-group">
            <label for="txtfirstname">First Name:</label>
            <input type="text" id="txtfirstname" name="txtfirstname" placeholder="Enter first name" required pattern="[A-Za-z\s'-]+" title="Letters, spaces, hyphens, apostrophes allowed">
        </div>
        <div class="form-group">
            <label for="txtlastname">Last Name:</label>
            <input type="text" id="txtlastname" name="txtlastname" placeholder="Enter last name" required pattern="[A-Za-z\s'-]+" title="Letters, spaces, hyphens, apostrophes allowed">
        </div>
        <div class="form-group">
            <label for="txtgender">Gender:</label>
            <select name="txtgender" id="txtgender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
        <div class="form-group">
            <label for="txtemail">Email Address:</label>
            <input type="email" id="txtemail" name="txtemail" placeholder="Enter email address" required>
        </div>
        <div class="form-group">
            <label for="userType">User Type:</label>
            <select name="txtusertype" id="userType" required>
                <option value="">Select User Type</option>
                <option value="student">Student</option>
                <option value="faculty">Faculty</option>
                <option value="staff">Staff</option>
            </select>
        </div>
        <div class="form-group">
            <label for="txtusername">Username:</label>
            <input type="text" id="txtusername" name="txtusername" placeholder="Username (min. 4 chars)" required minlength="4">
        </div>
        <div class="form-group">
            <label for="txtpassword">Password:</label>
            <input type="password" id="txtpassword" name="txtpassword" placeholder="Password (min. 6 chars)" required minlength="6">
        </div>

        <div id="studentFields" style="display: none;">
            <h4>Student Details</h4>
            <div class="form-group">
                <label for="txtprogram">Program:</label>
                <select name="txtprogram" id="txtprogram">
                    <option value="">Select Program</option>
                    <option value="BSIT">BSIT</option>
                    <option value="BSCS">BSCS</option>
                    <option value="BSCE">BS Civil Engineering</option>
                    <option value="BSME">BS Mechanical Engineering</option>
                    <!-- Add other programs as needed -->
                </select>
            </div>
            <div class="form-group">
                <label for="txtyearlevel">Year Level:</label>
                <select name="txtyearlevel" id="txtyearlevel">
                    <option value="">Select Year Level</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                    <option value="5">5th Year (for specific programs)</option>
                </select>
            </div>
        </div>

        <div id="facultyFields" style="display: none;">
            <h4>Faculty Details</h4>
            <div class="form-group">
                <label for="txtdepartment">Department:</label>
                <select name="txtdepartment" id="txtdepartment">
                    <option value="">Select Department</option>
                    <option value="College of Engineering">College of Engineering</option>
                    <option value="College of CS & IT">College of CS & IT</option>
                    <option value="College of Education">College of Education</option>
                    <option value="College of Arts & Sciences">College of Arts & Sciences</option>
                    <!-- Add other departments as needed -->
                </select>
            </div>
        </div>

        <div id="staffFields" style="display: none;">
            <h4>Staff Details</h4>
            <div class="form-group">
                <label for="txtoffice">Office:</label>
                <select name="txtoffice" id="txtoffice">
                    <option value="">Select Office</option>
                    <option value="Registrar's Office">Registrar's Office</option>
                    <option value="Human Resources">Human Resources</option>
                    <option value="Accounting Office">Accounting Office</option>
                    <option value="IT Support">IT Support Office</option>
                    <!-- Add other offices as needed -->
                </select>
            </div>
        </div>

        <button type="submit" name="btnSubmitUser" class="btn-submit-user">Add User</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userTypeSelect = document.getElementById('userType');
    const studentFieldsDiv = document.getElementById('studentFields');
    const facultyFieldsDiv = document.getElementById('facultyFields');
    const staffFieldsDiv = document.getElementById('staffFields');

    function toggleFields() {
        const selectedType = userTypeSelect.value;

        studentFieldsDiv.style.display = 'none';
        facultyFieldsDiv.style.display = 'none';
        staffFieldsDiv.style.display = 'none';

        studentFieldsDiv.querySelectorAll('select, input').forEach(el => el.required = false);
        facultyFieldsDiv.querySelectorAll('select, input').forEach(el => el.required = false);
        staffFieldsDiv.querySelectorAll('select, input').forEach(el => el.required = false);

        if (selectedType === 'student') {
            studentFieldsDiv.style.display = 'block';
            studentFieldsDiv.querySelectorAll('select, input').forEach(el => el.required = true);
        } else if (selectedType === 'faculty') {
            facultyFieldsDiv.style.display = 'block';
            facultyFieldsDiv.querySelectorAll('select, input').forEach(el => el.required = true);
        } else if (selectedType === 'staff') {
            staffFieldsDiv.style.display = 'block';
            staffFieldsDiv.querySelectorAll('select, input').forEach(el => el.required = true);
        }
    }

    toggleFields(); // Initial call 
    userTypeSelect.addEventListener('change', toggleFields);
});

function confirmLogout(event) {
    if (!confirm("Are you sure you want to logout?")) {
        event.preventDefault();
    }
}
</script>

<footer class="dashboard-footer">
    © 2025 ParkU | Group ParkU | BSCS - 2nd Year
</footer>

</body>
</html>
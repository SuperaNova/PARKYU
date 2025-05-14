<?php    
    include 'connect.php';    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration - ParkU</title>
    <link rel="stylesheet" href="design.css">
</head>
<body class="register-page-body">

    <div class="auth-top-bar">
        <a href="Index.php" class="auth-logo-link">
            <img src="res/logo.png" alt="ParkU Logo - Home" />
        </a>
        <!-- Potential placeholder for other top-bar content -->
    </div>

<div class="registration-container">
    <h2>ParkU User Registration</h2>
    <form method="post" action="register.php">
        <input type="text" name="txtfirstname" placeholder="Firstname" required pattern="[A-Za-z\s'-]+" title="Letters, spaces, hyphens, apostrophes allowed">
        <input type="text" name="txtlastname" placeholder="Lastname" required pattern="[A-Za-z\s'-]+" title="Letters, spaces, hyphens, apostrophes allowed">
        <input type="email" name="txtemail" placeholder="Email Address" required>

        <select name="txtgender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <select name="txtusertype" id="userType" required>
            <option value="">Select User Type</option>
            <option value="student">Student</option>
            <option value="faculty">Faculty</option>
            <option value="staff">Staff</option>
        </select>

        <input type="text" name="txtusername" placeholder="Username (min. 4 chars)" required minlength="4">
        <input type="password" name="txtpassword" placeholder="Password (min. 6 chars)" required minlength="6">

        <div id="studentFields" style="display: none;">
            <select name="txtprogram" >
                <option value="">Select Program</option>
                <option value="bsit">BSIT</option>
                <option value="bscs">BSCS</option>
                <!-- Add other programs as needed -->
            </select>

            <select name="txtyearlevel" >
                <option value="">Select Year Level</option>
                <option value="1">1st Year</option>
                <option value="2">2nd Year</option>
                <option value="3">3rd Year</option>
                <option value="4">4th Year</option>
                 <!-- Add other year levels as needed -->
            </select>
        </div>

        <div id="facultyFields" style="display: none;">
            <select name="txtdepartment" >
                <option value="">Select Department</option>
                <option value="engineering">College of Engineering</option>
                <option value="csit">College of CS & IT</option>
                <option value="educ">College of Education</option>
                <!-- Add other departments as needed -->
            </select>
        </div>

        <div id="staffFields" style="display: none;">
            <select name="txtoffice" >
                <option value="">Select Office</option>
                <option value="registrar">Registrar's Office</option>
                <option value="hr">Human Resources</option>
                <option value="accounting">Accounting Office</option>
                <!-- Add other offices as needed -->
            </select>
        </div>

        <input type="submit" name="btnRegister" value="Register">
    </form>
    <p class="login-link" style="text-align: center; margin-top: 20px;">
        Already have an account? <a href="login.php">Login here</a>
    </p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userTypeSelect = document.getElementById('userType');
    const studentFieldsDiv = document.getElementById('studentFields');
    const facultyFieldsDiv = document.getElementById('facultyFields');
    const staffFieldsDiv = document.getElementById('staffFields');

    // Function to toggle field visibility
    function toggleFields() {
        const selectedType = userTypeSelect.value;

        // Hide all role-specific fields initially
        studentFieldsDiv.style.display = 'none';
        facultyFieldsDiv.style.display = 'none';
        staffFieldsDiv.style.display = 'none';

        // Make fields required or not required based on visibility
        // Student fields
        studentFieldsDiv.querySelectorAll('select').forEach(sel => sel.required = false);
        // Faculty fields
        facultyFieldsDiv.querySelectorAll('select').forEach(sel => sel.required = false);
        // Staff fields
        staffFieldsDiv.querySelectorAll('select').forEach(sel => sel.required = false);


        if (selectedType === 'student') {
            studentFieldsDiv.style.display = 'block';
            studentFieldsDiv.querySelectorAll('select').forEach(sel => sel.required = true);
        } else if (selectedType === 'faculty') {
            facultyFieldsDiv.style.display = 'block';
            facultyFieldsDiv.querySelectorAll('select').forEach(sel => sel.required = true);
        } else if (selectedType === 'staff') {
            staffFieldsDiv.style.display = 'block';
            staffFieldsDiv.querySelectorAll('select').forEach(sel => sel.required = true);
        }
    }

    // Initial call to set the correct fields based on pre-selection (if any)
    toggleFields();

    // Add event listener
    userTypeSelect.addEventListener('change', toggleFields);
});
</script>

</body>	
</html>

<?php
if (isset($_POST['btnRegister'])) {
    $fname = trim($_POST['txtfirstname']);
    $lname = trim($_POST['txtlastname']);
    $email = trim($_POST['txtemail']);
    $gender = $_POST['txtgender'];
    $utype = $_POST['txtusertype'];
    $uname = trim($_POST['txtusername']);
    $pword = $_POST['txtpassword'];
    
    // Initialize role-specific variables
    $prog = null;
    $yearlevel = null;
    $department = null;
    $office = null;

    // Get role-specific data based on usertype
    if ($utype == 'student') {
        $prog = $_POST['txtprogram'];
        $yearlevel = $_POST['txtyearlevel'];
    } elseif ($utype == 'faculty') {
        $department = $_POST['txtdepartment'];
    } elseif ($utype == 'staff') {
        $office = $_POST['txtoffice'];
    }

    if (empty($fname) || empty($lname) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($gender) || empty($utype) || empty($uname) || empty($pword) ||
        ($utype == 'student' && (empty($prog) || empty($yearlevel))) ||
        ($utype == 'faculty' && empty($department)) ||
        ($utype == 'staff' && empty($office))
    ) {
        echo "<script>alert('Please fill in all required fields with valid data, including a valid email.');</script>";
    } elseif (strlen($uname) < 4 || strlen($pword) < 6) {
        echo "<script>alert('Username must be at least 4 characters and password at least 6 characters.');</script>";
    } else {
        // Check if username or email already exists
        $checkQuery = "SELECT username FROM tbluser WHERE username = ? OR email = ?";
        $checkStmt = mysqli_prepare($connection, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "ss", $uname, $email);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);

        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            echo "<script>alert('Username or Email already exists. Please choose another.');</script>";
        } else {
            $hashedpw = password_hash($pword, PASSWORD_DEFAULT);

            $sql1 = "INSERT INTO tbluser (firstname, lastname, email, gender, usertype, username, password)
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt1 = mysqli_prepare($connection, $sql1);
            mysqli_stmt_bind_param($stmt1, "sssssss", $fname, $lname, $email, $gender, $utype, $uname, $hashedpw);

            if (mysqli_stmt_execute($stmt1)) {
                $last_id = mysqli_insert_id($connection);

                if ($utype == 'student') {
                    // Only insert into tblstudent if the usertype is student
                    $sql2 = "INSERT INTO tblstudent (program, yearlevel, userID) VALUES (?, ?, ?)";
                    $stmt2 = mysqli_prepare($connection, $sql2);
                    mysqli_stmt_bind_param($stmt2, "sii", $prog, $yearlevel, $last_id);

                    if (mysqli_stmt_execute($stmt2)) {
                        echo "<script>alert('New student record saved. Redirecting...');</script>";
                        echo "<script>window.location.href='dashboard.php';</script>";
                        exit;
                    } else {
                        // Log error or show a more specific error to the user
                        // For now, we can keep the generic student data error
                        echo "<script>alert('Failed to save student specific data. User account was created, but please complete your profile later.');</script>";
                        // Optionally, redirect to dashboard or a profile page if user account was created
                        // echo "<script>window.location.href='dashboard.php';</script>"; 
                        // exit;
                    }
                } elseif ($utype == 'faculty') {
                    // Insert into tblfaculty
                    $sql_faculty = "INSERT INTO tblfaculty (department, userID) VALUES (?, ?)";
                    $stmt_faculty = mysqli_prepare($connection, $sql_faculty);
                    mysqli_stmt_bind_param($stmt_faculty, "si", $department, $last_id);

                    if (mysqli_stmt_execute($stmt_faculty)) {
                        echo "<script>alert('New faculty record saved. Redirecting...');</script>";
                        echo "<script>window.location.href='dashboard.php';</script>";
                        exit;
                    } else {
                        echo "<script>alert('Failed to save faculty specific data. User account was created, but please complete your profile later.');</script>";
                    }
                } elseif ($utype == 'staff') {
                    // Insert into tblstaff
                    $sql_staff = "INSERT INTO tblstaff (office, userID) VALUES (?, ?)";
                    $stmt_staff = mysqli_prepare($connection, $sql_staff);
                    mysqli_stmt_bind_param($stmt_staff, "si", $office, $last_id);

                    if (mysqli_stmt_execute($stmt_staff)) {
                        echo "<script>alert('New staff record saved. Redirecting...');</script>";
                        echo "<script>window.location.href='dashboard.php';</script>";
                        exit;
                    } else {
                        echo "<script>alert('Failed to save staff specific data. User account was created, but please complete your profile later.');</script>";
                    }
                } else {
                    // Should not happen if form validation is correct, but good to have a fallback.
                    echo "<script>alert('User account created, but role-specific information was not applicable or failed. Redirecting...');</script>";
                    echo "<script>window.location.href='dashboard.php';</script>";
                    exit;
                }
            } else {
                echo "<script>alert('Failed to save user data.');</script>";
            }
        }
    }
}
?>
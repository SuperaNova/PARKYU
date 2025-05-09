<?php
include 'connect.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = $_POST['userID'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $userType = $_POST['userType'];

    // Insert into tbluser
    $query = "INSERT INTO tbluser (userID, firstName, lastName, email) VALUES ('$userID', '$firstName', '$lastName', '$email')";
    if (mysqli_query($connection, $query)) {
        // Insert into the corresponding table based on user type
        if ($userType == 'student') {
            $studentID = $_POST['studentID'];
            $program = $_POST['program'];
            $yearLevel = $_POST['yearLevel'];
            $studentQuery = "INSERT INTO tblstudent (userID, studentID, program, yearLevel) VALUES ('$userID', '$studentID', '$program', '$yearLevel')";
            mysqli_query($connection, $studentQuery);
        } elseif ($userType == 'staff') {
            $staffID = $_POST['staffID'];
            $office = $_POST['office'];
            $staffQuery = "INSERT INTO tblstaff (userID, staffID, office) VALUES ('$userID', '$staffID', '$office')";
            mysqli_query($connection, $staffQuery);
        } elseif ($userType == 'faculty') {
            $facultyID = $_POST['facultyID'];
            $department = $_POST['department'];
            $facultyQuery = "INSERT INTO tblfaculty (userID, facultyID, department) VALUES ('$userID', '$facultyID', '$department')";
            mysqli_query($connection, $facultyQuery);
        }
        echo "User added successfully!";
    } else {
        echo "Error: " . mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Your CSS styles here */
        body {
            font-family: 'Inconsolata', monospace;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        button {
            background-color: #6C2C2F;
            color: white;
            font-weight: bold;
        }
        button:hover {
            background-color: #A86D68;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Add New User</h2>
    <form method="POST">
        <label for="userID">User ID</label>
        <input type="text" id="userID" name="userID" required>

        <label for="firstName">First Name</label>
        <input type="text" id="firstName" name="firstName" required>

        <label for="lastName">Last Name</label>
        <input type="text" id="lastName" name="lastName" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="userType">User Type</label>
        <select id="userType" name="userType" required>
            <option value="">Select User Type</option>
            <option value="student">Student</option>
            <option value="staff">Staff</option>
            <option value="faculty">Faculty</option>
        </select>

        <div id="studentFields" class="dynamic-fields" style="display: none;">
            <label for="studentID">Student ID</label>
            <input type="text" id="studentID" name="studentID">

            <label for="program">Program</label>
            <input type="text" id="program" name="program">

            <label for="yearLevel">Year Level</label>
            <input type="text" id="yearLevel" name="yearLevel">
        </div>

        <div id="staffFields" class="dynamic-fields" style="display: none;">
            <label for="staffID">Staff ID</label>
            <input type="text" id="staffID" name="staffID">

            <label for="office">Office</label>
            <input type="text" id="office" name="office">
        </div>

        <div id="facultyFields" class="dynamic-fields" style="display: none;">
            <label for="facultyID">Faculty ID</label>
            <input type="text" id="facultyID" name="facultyID">

            <label for="department">Department</label>
            <input type="text" id="department" name="department">
        </div>

        <button type="submit">Add User</button>
    </form>
</div>

<script>
    // Dynamically show fields based on user type selection
    document.getElementById('userType').addEventListener('change', function() {
        var studentFields = document.getElementById('studentFields');
        var staffFields = document.getElementById('staffFields');
        var facultyFields = document.getElementById('facultyFields');

        studentFields.style.display = 'none';
        staffFields.style.display = 'none';
        facultyFields.style.display = 'none';

        if (this.value == 'student') {
            studentFields.style.display = 'block';
        } else if (this.value == 'staff') {
            staffFields.style.display = 'block';
        } else if (this.value == 'faculty') {
            facultyFields.style.display = 'block';
        }
    });
</script>

</body>
</html>

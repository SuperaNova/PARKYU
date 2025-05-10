<?php    
    include 'connect.php';    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ParkU - User Registration</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>

<div class="registration-container">
    <header>
        <div class="logo">
            <h1>ParkU</h1>
            <p>A Smart Parking Sticker System</p>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#contact">Contact Us</a></li>
            </ul>
        </nav>
    </header>

    <h2>ParkU User Registration Page</h2>
    <form method="post" onsubmit="return validateForm();">
        <input type="text" name="txtfirstname" placeholder="Firstname" required pattern="[A-Za-z]+" title="Only letters allowed">
        <input type="text" name="txtlastname" placeholder="Lastname" required pattern="[A-Za-z]+" title="Only letters allowed">

        <select name="txtgender" required>
            <option value="">Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <select name="txtusertype" required>
            <option value="">User Type</option>
            <option value="student">Student</option>
            <option value="employee">Employee</option>
        </select>

        <input type="text" name="txtusername" placeholder="Username" required minlength="4">
        <input type="password" name="txtpassword" placeholder="Password" required minlength="6">

        <select name="txtprogram" required>
            <option value="">Program</option>
            <option value="bsit">BSIT</option>
            <option value="bscs">BSCS</option>
        </select>

        <select name="txtyearlevel" required>
            <option value="">Year Level</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
        </select>

        <input type="submit" name="btnRegister" value="Register">
    </form>
</div>

<script>
function validateForm() {
    const uname = document.forms[0]["txtusername"].value;
    const pass = document.forms[0]["txtpassword"].value;

    if (uname.length < 4) {
        alert("Username must be at least 4 characters.");
        return false;
    }
    if (pass.length < 6) {
        alert("Password must be at least 6 characters.");
        return false;
    }
    return true;
}
</script>

</body>	
</html>

<?php
if (isset($_POST['btnRegister'])) {
    $fname = trim($_POST['txtfirstname']);
    $lname = trim($_POST['txtlastname']);
    $gender = $_POST['txtgender'];
    $utype = $_POST['txtusertype'];
    $uname = trim($_POST['txtusername']);
    $pword = $_POST['txtpassword'];
    $prog = $_POST['txtprogram'];
    $yearlevel = $_POST['txtyearlevel'];

    if (empty($fname) || empty($lname) || empty($gender) || empty($utype) || empty($uname) || empty($pword) || empty($prog) || empty($yearlevel)) {
        echo "<script>alert('Please fill in all fields.');</script>";
    } elseif (strlen($uname) < 4 || strlen($pword) < 6) {
        echo "<script>alert('Username must be at least 4 characters and password at least 6 characters.');</script>";
    } else {
        // Check if username already exists
        $checkQuery = "SELECT * FROM tbluser WHERE username = ?";
        $checkStmt = mysqli_prepare($connection, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "s", $uname);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);

        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            echo "<script>alert('Username already exists. Please choose another.');</script>";
        } else {
            $hashedpw = password_hash($pword, PASSWORD_DEFAULT);

            $sql1 = "INSERT INTO tbluser (firstname, lastname, gender, usertype, username, password)
                     VALUES (?, ?, ?, ?, ?, ?)";
            $stmt1 = mysqli_prepare($connection, $sql1);
            mysqli_stmt_bind_param($stmt1, "ssssss", $fname, $lname, $gender, $utype, $uname, $hashedpw);

            if (mysqli_stmt_execute($stmt1)) {
                $last_id = mysqli_insert_id($connection);

                $sql2 = "INSERT INTO tblstudent (program, yearlevel, uid) VALUES (?, ?, ?)";
                $stmt2 = mysqli_prepare($connection, $sql2);
                mysqli_stmt_bind_param($stmt2, "sii", $prog, $yearlevel, $last_id);

                if (mysqli_stmt_execute($stmt2)) {
                    echo "<script>alert('New record saved. Redirecting...');</script>";
                    echo "<script>window.location.href='dashboard.php';</script>";
                    exit;
                } else {
                    echo "<script>alert('Failed to save student data.');</script>";
                }
            } else {
                echo "<script>alert('Failed to save user data.');</script>";
            }
        }
    }
}
?>
<?php    
    include 'connect.php';    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <style>
:root {
  --white: #FFFFFF;
  --black: #000000;
  --text-color: #566B78;
  --maroon-accent: #6C2C2F;
  --background-general: #FEF3E2;
}

body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background: radial-gradient(circle, #F7A556 30%, #B1323A 80%);
}

.container {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  text-align: center;
}

.form_area {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  background-color: var(--background-general);
  height: auto;
  width: auto;
  padding: 20px;
  border: 2px solid var(--maroon-accent);
  border-radius: 20px;
  box-shadow: 3px 4px 0px 1px var(--text-color);
}

.title {
  color: var(--maroon-accent);
  font-weight: 900;
  font-size: 1.5em;
  margin-bottom: 15px;
}

.sub_title {
  font-weight: 600;
  margin: 5px 0;
  color: var(--text-color);
}

.form_group {
  display: flex;
  flex-direction: column;
  align-items: baseline;
  margin: 10px;
}

.form_style {
  outline: none;
  border: 2px solid var(--maroon-accent);
  box-shadow: 3px 4px 0px 1px var(--text-color);
  width: 100%;
  padding: 12px 10px;
  border-radius: 6px;
  font-size: 15px;
  background-color: var(--white);
  box-sizing: border-box; /* Ensures padding doesn’t affect width */
  appearance: show; /* Makes select elements behave more like input fields */
}

/* Additional styling for select to match input fields */
.form_style select {
  width: 100%;
  padding: 12px 10px;
  border: none;
  background-color: transparent;
}


.form_style:focus, .btn:focus {
  transform: translateY(4px);
  box-shadow: 1px 2px 0px 0px var(--text-color);
}

.btn {
  padding: 15px;
  margin: 25px 0px;
  width: 290px;
  font-size: 15px;
  background: var(--maroon-accent);
  color: var(--white);
  border-radius: 10px;
  font-weight: 800;
  box-shadow: 3px 3px 0px 0px var(--text-color);
  cursor: pointer;
  border: none;
}

.btn:hover {
  opacity: .9;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="form_area">
            <p class="title">SIGN UP</p>
            <form method="post">
                <div class="form_group">
                    <label class="sub_title">Firstname</label>
                    <input placeholder="Enter your first name" class="form_style" type="text" name="txtfirstname" required>
                </div>
                <div class="form_group">
                    <label class="sub_title">Lastname</label>
                    <input placeholder="Enter your last name" class="form_style" type="text" name="txtlastname" required>
                </div>
                <div class="form_group">
                    <label class="sub_title">Gender</label>
                    <select name="txtgender" class="form_style" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="form_group">
                    <label class="sub_title">User Type</label>
                    <select name="txtusertype" class="form_style" required>
                        <option value="">Select User Type</option>
                        <option value="student">Student</option>
                        <option value="employee">Employee</option>
                    </select>
                </div>
                <div class="form_group">
                    <label class="sub_title">Username</label>
                    <input placeholder="Choose a username" class="form_style" type="text" name="txtusername" required>
                </div>
                <div class="form_group">
                    <label class="sub_title">Password</label>
                    <input placeholder="Enter your password" class="form_style" type="password" name="txtpassword" required>
                </div>
                <div class="form_group">
                    <label class="sub_title">Program</label>
                    <select name="txtprogram" class="form_style" required>
                        <option value="">Select Program</option>
                        <option value="bsit">BSIT</option>
                        <option value="bscs">BSCS</option>
                    </select>
                </div>
                <div class="form_group">
                    <label class="sub_title">Year Level</label>
                    <select name="txtyearlevel" class="form_style" required>
                        <option value="">Select Year Level</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
                </div>
                <button type="submit" name="btnRegister" class="btn">REGISTER</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php    
if(isset($_POST['btnRegister'])) {        
    $fname = $_POST['txtfirstname'];        
    $lname = $_POST['txtlastname'];
    $gender = $_POST['txtgender'];
    $utype = $_POST['txtusertype'];
    $uname = $_POST['txtusername'];        
    $pword = $_POST['txtpassword'];    
    $hashedpw = password_hash($pword, PASSWORD_DEFAULT);
    
    $prog = $_POST['txtprogram'];        
    $yearlevel = $_POST['txtyearlevel'];        

    $sql1 = "INSERT INTO tbluser (firstname, lastname, gender, usertype, username, password) 
             VALUES ('$fname', '$lname', '$gender', '$utype', '$uname', '$hashedpw')";
    mysqli_query($connection, $sql1);
    
    $last_id = mysqli_insert_id($connection);
    
    if($utype == 'student') {
    	$sql2 = "INSERT INTO tblstudent (program, yearlevel, uid) 
             	VALUES ('$prog', '$yearlevel', '$last_id')";
    			mysqli_query($connection, $sql2);
    }
    echo "<script>
            alert('New record saved.');
            window.location.href = 'dashboard.php';
          </script>";
}
?>

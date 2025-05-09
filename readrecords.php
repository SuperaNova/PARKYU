<?php
    include 'connect.php';

    if (!$connection) {
        die('Could not connect: ' . mysqli_connect_error());
    }

    // Unified query to retrieve all users and their related records (students, staff, or faculty)
    $query = 'SELECT 
                tbluser.firstName, 
                tbluser.lastName, 
                tbluser.userID, 
                tbluser.username, 
                tbluser.usertype, 
                tbluser.gender, 
                tbluser.email, 
                tbluser.isActive,
                tblstudent.studentID, 
                tblstudent.program, 
                tblstudent.yearLevel,
                tblstaff.staffID, 
                tblstaff.office, 
                tblfaculty.facultyID, 
                tblfaculty.department 
              FROM tbluser
              LEFT JOIN tblstudent ON tbluser.userID = tblstudent.userID
              LEFT JOIN tblstaff ON tbluser.userID = tblstaff.userID
              LEFT JOIN tblfaculty ON tbluser.userID = tblfaculty.userID
              ORDER BY tbluser.userID ASC';

    $resultset = mysqli_query($connection, $query);

    if (!$resultset) {
        die('Query failed: ' . mysqli_error($connection));
    }

    // Optional: loop through the result to display (example)
    /*
    while ($row = mysqli_fetch_assoc($resultset)) {
        echo $row['firstName'] . ' ' . $row['lastName'] . '<br>';
    }
    */
?>

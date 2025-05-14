<?php
header('Content-Type: application/json');
include 'connect.php';

$userID = $_GET['userID'] ?? null;

if (!$userID) {
    echo json_encode([]);
    exit;
}

$vehicles = [];
// IMPORTANT ASSUMPTION: vehicle_register table has a `userID` column 
// that directly links to tbluser.userID.
// If it links via studentID to tblstudent, this query needs adjustment or will only work for students.
$query = "SELECT licensePlate, vehicleType, vehicleMake FROM vehicle_register WHERE userID = ? ORDER BY licensePlate";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $userID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $vehicles[] = $row;
}

mysqli_stmt_close($stmt);
mysqli_close($connection);

echo json_encode($vehicles);
?> 
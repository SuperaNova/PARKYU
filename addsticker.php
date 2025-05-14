<?php
session_start();
include 'connect.php';

// Redirect to login if not authenticated
// if (!isset($_SESSION['username'])) {
//     header("Location: login.php");
//     exit();
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmSticker'])) {
    $selectedUserID = $_POST['selected_user_id'] ?? null;
    $selectedLicensePlate = $_POST['selected_vehicle_lp'] ?? null;

    if (empty($selectedUserID) || empty($selectedLicensePlate)) {
        $message = "Error: User and vehicle must be selected.";
        $message_type = "error";
    } else {
        $connection->begin_transaction(); // Start transaction
        try {
            // Fetch vehicleType for the selected vehicle to determine stickerType server-side
            $vehicleInfoQuery = "SELECT vehicleType FROM vehicle_register WHERE userID = ? AND licensePlate = ?";
            $stmtVehicleInfo = mysqli_prepare($connection, $vehicleInfoQuery);
            mysqli_stmt_bind_param($stmtVehicleInfo, "is", $selectedUserID, $selectedLicensePlate);
            mysqli_stmt_execute($stmtVehicleInfo);
            $vehicleInfoResult = mysqli_stmt_get_result($stmtVehicleInfo);

            if (!($vehicleRow = mysqli_fetch_assoc($vehicleInfoResult))) {
                throw new Exception("Selected vehicle not found for the user or vehicle type could not be determined.");
            }
            mysqli_stmt_close($stmtVehicleInfo);
            
            $vehicleTypeFromDB = $vehicleRow['vehicleType'];
            $determinedStickerType = !empty($vehicleTypeFromDB) ? $vehicleTypeFromDB . " Sticker" : "General Sticker";

            $issueDate = date('Y-m-d');
            $todayForExpiry = new DateTime();
            $currentYearForExpiry = (int)$todayForExpiry->format('Y');
            $julyFirstThisYear = new DateTime($currentYearForExpiry . '-07-01');
            $expiryDate = ($todayForExpiry < $julyFirstThisYear) ? 
                          $julyFirstThisYear->format('Y-m-d') : 
                          (new DateTime(($currentYearForExpiry + 1) . '-07-01'))->format('Y-m-d');
            $isActive = 1;

            // Step 1: Insert into stickers table
            $insertStickerQuery = "INSERT INTO stickers (stickerType, issueDate, expiryDate, isActive) VALUES (?, ?, ?, ?)";
            $stmtInsertSticker = mysqli_prepare($connection, $insertStickerQuery);
            mysqli_stmt_bind_param($stmtInsertSticker, "sssi", $determinedStickerType, $issueDate, $expiryDate, $isActive);
            if (!mysqli_stmt_execute($stmtInsertSticker)) {
                throw new Exception("Database Error: Could not create sticker record. " . mysqli_error($connection));
            }
            $newStickerId = mysqli_insert_id($connection);
            mysqli_stmt_close($stmtInsertSticker);

            // Step 2: Update vehicle_register table with the new stickerID
            $updateVehicleQuery = "UPDATE vehicle_register SET stickerID = ? WHERE userID = ? AND licensePlate = ?";
            $stmtUpdateVehicle = mysqli_prepare($connection, $updateVehicleQuery);
            mysqli_stmt_bind_param($stmtUpdateVehicle, "iis", $newStickerId, $selectedUserID, $selectedLicensePlate);
            if (!mysqli_stmt_execute($stmtUpdateVehicle)) {
                // If this fails, we should ideally roll back the sticker insertion or handle it.
                throw new Exception("Database Error: Could not assign sticker to vehicle. " . mysqli_error($connection));
            }
            mysqli_stmt_close($stmtUpdateVehicle);

            $connection->commit(); // Commit transaction
            $message = "Success! Sticker #{$newStickerId} ({$determinedStickerType}) created and assigned to vehicle LP: {$selectedLicensePlate}. Expires: {$expiryDate}.";
            $message_type = "success";

        } catch (Exception $e) {
            $connection->rollback(); // Rollback transaction on error
            $message = $e->getMessage();
            $message_type = "error";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Sticker - ParkU</title>
    <link rel="stylesheet" href="design.css">
    <style>
        .sticker-form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-width: 700px;
            margin: 40px auto;
        }
        .sticker-form-container h2 {
            color: #333;
            text-align: center;
            margin-bottom: 25px;
            font-size: 1.8em;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        .form-group select, .form-group input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 1em;
        }
        .form-group select:focus, .form-group input[type="text"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .sticker-preview {
            border: 2px dashed #007bff;
            padding: 20px;
            margin-top: 25px;
            border-radius: 5px;
            background-color: #f8f9fa;
        }
        .sticker-preview h3 {
            margin-top: 0;
            color: #007bff;
            text-align: center;
            margin-bottom: 15px;
        }
        .sticker-preview p {
            margin: 8px 0;
            font-size: 1em;
            color: #333;
        }
        .sticker-preview p strong {
            color: #555;
        }
        .btn-confirm-sticker {
            background-color: #28a745;
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
        .btn-confirm-sticker:hover {
            background-color: #218838;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            font-size: 1.1em;
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
<body class="dashboard-page-body"> <!-- Or a more generic body class -->

<header class="dashboard-header">
    <h1>ParkU Sticker Management</h1>
</header>

<nav class="dashboard-nav">
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="adduser.php">Add User</a></li>
        <li><a href="vehicleregister.php">Register Vehicle</a></li>
        <li class="active-nav-item"><a href="addsticker.php">Add Sticker</a></li>
        <li><a href="Index.php">Main Site</a></li>
        <li><a href="logout.php" onclick="confirmLogout(event)">Logout</a></li>
    </ul>
</nav>

<div class="sticker-form-container">
    <h2>Create New Parking Sticker</h2>

    <?php if (!empty($message)): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="addsticker.php" id="addStickerForm">
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
            <label for="selected_vehicle_lp">Select Vehicle:</label>
            <select name="selected_vehicle_lp" id="selected_vehicle_lp" required disabled>
                <option value="">-- Select User First --</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="sticker_type_display">Sticker Type:</label>
            <input type="text" name="sticker_type_display" id="sticker_type_display" value="" readonly class="readonly-input">
            <!-- Hidden input to pass the determined sticker type if needed, though PHP will recalculate -->
            <input type="hidden" name="calculated_sticker_type" id="calculated_sticker_type" value="">
        </div>

        <div id="stickerPreviewUI" class="sticker-preview" style="display:none;">
            <h3>Sticker Preview</h3>
            <p><strong>Sticker Number:</strong> <span id="previewStickerNumber">Will be auto-generated</span></p>
            <p><strong>User:</strong> <span id="previewUser"></span></p>
            <p><strong>License Plate:</strong> <span id="previewLicensePlate"></span></p>
            <p><strong>Vehicle Details:</strong> <span id="previewVehicleDetails"></span></p>
            <p><strong>Sticker Type:</strong> <span id="previewStickerType">Annual Pass</span></p>
            <p><strong>Issue Date:</strong> <span id="previewIssueDate"></span></p>
            <p><strong>Expiry Date:</strong> <span id="previewExpiryDate"></span></p>
        </div>

        <button type="submit" name="confirmSticker" class="btn-confirm-sticker" id="btnConfirmSticker" disabled>Confirm and Create Sticker</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userSelect = document.getElementById('selected_user_id');
    const vehicleSelect = document.getElementById('selected_vehicle_lp');
    const stickerPreviewDiv = document.getElementById('stickerPreviewUI');
    const btnConfirm = document.getElementById('btnConfirmSticker');
    const stickerTypeDisplay = document.getElementById('sticker_type_display');
    const hiddenCalculatedStickerType = document.getElementById('calculated_sticker_type');

    userSelect.addEventListener('change', function() {
        const userId = this.value;
        vehicleSelect.innerHTML = '<option value="">-- Loading Vehicles --</option>';
        vehicleSelect.disabled = true;
        stickerPreviewDiv.style.display = 'none';
        btnConfirm.disabled = true;
        document.getElementById('previewUser').textContent = '';
        stickerTypeDisplay.value = '';
        hiddenCalculatedStickerType.value = '';


        if (userId) {
            document.getElementById('previewUser').textContent = this.options[this.selectedIndex].text;
            fetch('get_vehicles_for_user.php?userID=' + userId)
                .then(response => response.json())
                .then(data => {
                    vehicleSelect.innerHTML = '<option value="">-- Select Vehicle --</option>';
                    if (data.length > 0) {
                        data.forEach(vehicle => {
                            const option = document.createElement('option');
                            option.value = vehicle.licensePlate;
                            option.textContent = vehicle.licensePlate + ' (' + vehicle.vehicleMake + ' ' + vehicle.vehicleType + ')';
                            option.dataset.vehicleType = vehicle.vehicleType;
                            option.dataset.vehicleMake = vehicle.vehicleMake;
                            vehicleSelect.appendChild(option);
                        });
                        vehicleSelect.disabled = false;
                    } else {
                        vehicleSelect.innerHTML = '<option value="">-- No vehicles registered for this user --</option>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching vehicles:', error);
                    vehicleSelect.innerHTML = '<option value="">-- Error loading vehicles --</option>';
                });
        } else {
            vehicleSelect.innerHTML = '<option value="">-- Select User First --</option>';
        }
    });

    vehicleSelect.addEventListener('change', function() {
        if (this.value) {
            updateStickerPreview();
            stickerPreviewDiv.style.display = 'block';
            btnConfirm.disabled = false;
        } else {
            stickerPreviewDiv.style.display = 'none';
            btnConfirm.disabled = true;
            stickerTypeDisplay.value = '';
            hiddenCalculatedStickerType.value = '';
        }
    });

    function updateStickerPreview() {
        const selectedVehicleOption = vehicleSelect.options[vehicleSelect.selectedIndex];
        const vehicleType = selectedVehicleOption.dataset.vehicleType;
        const determinedStickerType = vehicleType ? vehicleType + " Sticker" : "Unknown Sticker";

        stickerTypeDisplay.value = determinedStickerType;
        hiddenCalculatedStickerType.value = determinedStickerType;

        document.getElementById('previewLicensePlate').textContent = selectedVehicleOption.value;
        document.getElementById('previewVehicleDetails').textContent = selectedVehicleOption.dataset.vehicleMake + ' ' + vehicleType;
        
        document.getElementById('previewStickerType').textContent = determinedStickerType;

        const today = new Date();
        const issueDateStr = today.toISOString().split('T')[0];
        document.getElementById('previewIssueDate').textContent = issueDateStr;

        let expiryDate = new Date();
        const currentYear = today.getFullYear();
        const julyFirstThisYear = new Date(currentYear, 6, 1);

        if (today < julyFirstThisYear) {
            expiryDate = julyFirstThisYear;
        } else {
            expiryDate = new Date(currentYear + 1, 6, 1);
        }
        const expiryDateStr = expiryDate.toISOString().split('T')[0];
        document.getElementById('previewExpiryDate').textContent = expiryDateStr;
    }
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
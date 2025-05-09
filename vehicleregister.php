<?php 
    include 'connect.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vehicle Registration</title>
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
            background-color: var(--background-general);
            padding: 20px;
            border: 2px solid var(--maroon-accent);
            border-radius: 20px;
            box-shadow: 3px 4px 0px 1px var(--text-color);
            width: 350px;
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
            box-sizing: border-box;
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
            <p class="title">VEHICLE REGISTRATION</p>
            <form method="post">
                <div class="form_group">
                    <label class="sub_title">License Plate</label>
                    <input class="form_style" type="text" name="license_plate" required>
                </div>
                <div class="form_group">
                    <label class="sub_title">Vehicle Type</label>
                    <input class="form_style" type="text" name="vehicle_type" required>
                </div>
                <div class="form_group">
                    <label class="sub_title">Brand</label>
                    <input class="form_style" type="text" name="brand" required>
                </div>
                <div class="form_group">
                    <label class="sub_title">Vehicle Category</label>
                    <select name="vehicle_category" class="form_style" required>
                        <option value="">Select Category</option>
                        <option value="car">Car</option>
                        <option value="motorcycle">Motorcycle</option>
                    </select>
                </div>
                <button type="submit" name="btnRegisterVehicle" class="btn">REGISTER VEHICLE</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php  
if (isset($_POST['btnRegisterVehicle'])) {
    $licensePlate = $_POST['license_plate'];
    $vehicleType = $_POST['vehicle_type'];
    $brand = $_POST['brand'];
    $vehicleCategory = $_POST['vehicle_category'];

    $isCar = $vehicleCategory === 'car' ? 1 : 0;
    $isMotorcycle = $vehicleCategory === 'motorcycle' ? 1 : 0;

    // Check if license plate already exists
    $checkStmt = $connection->prepare("SELECT licensePlate FROM vehicle_register WHERE licensePlate = ?");
    $checkStmt->bind_param("s", $licensePlate);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "<script>alert('License plate already exists!');</script>";
    } else {
        // Try to assign first available sticker
        $result = $connection->query("SELECT stickerID FROM stickers WHERE stickerID NOT IN (SELECT stickerID FROM vehicle_register) LIMIT 1");

        if ($row = $result->fetch_assoc()) {
            // Use existing available sticker
            $stickerID = $row['stickerID'];
        } else {
            // Create a new sticker if none available
            // Use the license plate from the form
            $userID = null;    // Optional: replace with logged-in user ID
            $stickerType = $vehicleCategory === 'car' ? 'CarSticker' : 'MotorcycleSticker';

            $stmtSticker = $connection->prepare("INSERT INTO stickers (licensePlate, userID, stickerType) VALUES (?, ?, ?)");
            $stmtSticker->bind_param("sis", $licensePlate, $userID, $stickerType);  // Correct the data type for licensePlate
            $stmtSticker->execute();
            $stickerID = $connection->insert_id;
        }

        // Register vehicle with assigned or newly created sticker
        $stmt = $connection->prepare("INSERT INTO vehicle_register (licensePlate, vehicleType, vehicleBrand, isMotorcycle, isCar, stickerID) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiii", $licensePlate, $vehicleType, $brand, $isMotorcycle, $isCar, $stickerID);
        $stmt->execute();

        echo "<script>
            alert('Vehicle registered and sticker assigned!');
            window.location.href = 'dashboard.php';
        </script>";
    }    
}
?>

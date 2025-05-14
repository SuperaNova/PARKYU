<?php    
    session_start(); // Ensure session is started
    include 'connect.php';
    // include 'readrecords.php'; // This will be called later, after potential delete action

    $delete_message = '';
    $delete_message_type = '';

    // Handle User Deletion
    if (isset($_GET['action']) && $_GET['action'] == 'delete_user' && isset($_GET['id'])) {
        $userIDToDelete = mysqli_real_escape_string($connection, $_GET['id']);
        if (filter_var($userIDToDelete, FILTER_VALIDATE_INT)) {
            // Before deleting from tbluser, you might need to handle related data if not perfectly cascaded
            // For example, stickers not directly linked to userID but through vehicle_register.
            // However, your schema for vehicle_register has ON DELETE CASCADE for userID, 
            // and vehicle_register.stickerID to stickers.stickerID is ON DELETE SET NULL.
            // This means deleting a user will delete their vehicles, and those vehicles' stickers will have their stickerID in vehicle_register set to NULL.
            // The stickers themselves in the `stickers` table might remain orphaned if not handled.

            // Let's assume for now that ON DELETE CASCADE on tbluser is sufficient for major dependent tables.
            // A more robust solution might involve explicitly deleting from `stickers` based on vehicles owned by the user if necessary.

            $deleteQuery = "DELETE FROM tbluser WHERE userID = ?";
            $stmt = mysqli_prepare($connection, $deleteQuery);
            mysqli_stmt_bind_param($stmt, "i", $userIDToDelete);
            
            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $delete_message = "User ID {$userIDToDelete} and their associated data have been deleted successfully.";
                    $delete_message_type = "success";
                } else {
                    $delete_message = "Error: User ID {$userIDToDelete} not found or already deleted.";
                    $delete_message_type = "error";
                }
            } else {
                $delete_message = "Database Error: Could not delete user. " . mysqli_error($connection);
                $delete_message_type = "error";
            }
            mysqli_stmt_close($stmt);
        } else {
            $delete_message = "Invalid User ID for deletion.";
            $delete_message_type = "error";
        }
        // It's good practice to redirect after a POST/action to prevent re-execution on refresh
        // For simplicity here, we will show a message and re-load the data. 
        // Consider a redirect: header("Location: dashboard.php?deletemsg=".urlencode($delete_message)); exit;
    }

    include 'readrecords.php'; // Now include and fetch records AFTER potential delete action

    // Store all rows in an array so we can reuse them
    $rows = [];
    while($row = $resultset->fetch_assoc()) {
        $rows[] = $row;
    }

    // Count the user types
    $studentCount = 0;
    $staffCount = 0;
    $facultyCount = 0;

    foreach($rows as $row) {
        if ($row['facultyInfoID']) {
            $facultyCount++;
        } elseif ($row['staffInfoID']) {
            $staffCount++;
        } elseif ($row['studentInfoID']) {
            $studentCount++;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - ParkU</title>
    <link rel="stylesheet" href="design.css">
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="dashboard-page-body">

<header class="dashboard-header">
    <h1>Vehicle Management System Dashboard</h1>
</header>

<?php if (!empty($delete_message)): ?>
    <div class="message <?php echo htmlspecialchars($delete_message_type); ?>" style="margin: 10px 20px; padding: 15px; text-align:center;">
        <?php echo htmlspecialchars($delete_message); ?>
    </div>
<?php endif; ?>

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

<div class="dashboard-layout-grid"> 
    <aside class="dashboard-sidebar">
        <ul>
            <li><button class="sidebar-nav-button" data-target="chartContainer">User Distribution (Bar)</button></li>
            <li><button class="sidebar-nav-button" data-target="pieChartContainer">User Distribution (Pie)</button></li>
            <li><button class="sidebar-nav-button active-nav-button" data-target="userTableContainer">List of Users</button></li>
            <li><button class="sidebar-nav-button" data-target="stickerInfoContainer">Sticker Information</button></li>
            <!-- Add more buttons here if new sections are added -->
        </ul>
    </aside>

    <main class="dashboard-main-content">
        <div id="chartContainer" class="content-section">
            <h2>User Type Distribution</h2>
            <canvas id="userTypeChart" width="400" height="200"></canvas>
            <script>
                var ctxBar = document.getElementById('userTypeChart').getContext('2d');
                var userTypeChart = new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: ['Student', 'Staff', 'Faculty'],
                        datasets: [{
                            label: 'Number of Users by Type',
                            data: [<?= $studentCount ?>, <?= $staffCount ?>, <?= $facultyCount ?>],
                            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                            borderColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        </div>

        <div id="pieChartContainer" class="content-section">
            <h2>User Type Distribution (Pie Chart)</h2>
            <canvas id="userTypePieChart"></canvas> <!-- Removed fixed width/height, CSS will handle -->
            <script>
                var ctxPie = document.getElementById('userTypePieChart').getContext('2d');
                var userTypePieChart = new Chart(ctxPie, {
                    type: 'pie',
                    data: {
                        labels: ['Student', 'Staff', 'Faculty'],
                        datasets: [{
                            label: 'Number of Users by Type',
                            data: [<?= $studentCount ?>, <?= $staffCount ?>, <?= $facultyCount ?>],
                            backgroundColor: ['#FF5733', '#33FF57', '#3357FF'],
                            borderColor: ['#FF5733', '#33FF57', '#3357FF'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true // Try true, then false if too big
                    }
                });
            </script>
        </div>

        <div id="userTableContainer" class="content-section active-content">
            <h2>List of Users</h2>
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>ID Number</th>
                        <th>Firstname</th>
                        <th>Lastname</th>
                        <th>Affiliation</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Ensure $connection is available or re-establish if necessary
                    // The query is defined in readrecords.php, which is included at the top
                    // We might need to re-run the query if the $resultset was exhausted by chart counts
                    // For simplicity, assuming $rows array is already populated from the top of the script
                    if (!empty($rows)) { // Use the $rows array populated at the top
                        foreach($rows as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['userID']); ?></td>
                                <td><?php echo htmlspecialchars($row['firstName']); ?></td>
                                <td><?php echo htmlspecialchars($row['lastName']); ?></td>
                                <td>
                                    <?php
                                    if (!empty($row['facultyInfoID'])) {
                                        echo htmlspecialchars($row['department']);
                                    } elseif (!empty($row['staffInfoID'])) {
                                        echo htmlspecialchars($row['office']);
                                    } elseif (!empty($row['studentInfoID'])) {
                                        echo htmlspecialchars($row['program']);
                                    } else {
                                        echo "N/A";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($row['facultyInfoID'])) {
                                        echo "Faculty";
                                    } elseif (!empty($row['staffInfoID'])) {
                                        echo "Staff";
                                    } elseif (!empty($row['studentInfoID'])) {
                                        echo "Student";
                                    } else {
                                        echo "Unknown";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <button class="btn" onclick="window.location.href='update_user.php?id=<?php echo htmlspecialchars($row['userID']); ?>'">UPDATE</button>
                                    <button class="btn btn-delete" onclick="confirmDeleteUser('<?php echo htmlspecialchars($row['userID']); ?>', '<?php echo htmlspecialchars(addslashes($row['username'])); ?>')">DELETE</button>
                                </td>
                            </tr>
                        <?php endforeach;
                    } else {
                        echo "<tr><td colspan='6'>No user data found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div id="stickerInfoContainer" class="content-section">
            <h2>Sticker Information</h2>
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Sticker ID</th>
                        <th>User Name</th>
                        <th>User Type</th>
                        <th>License Plate</th>
                        <th>Type</th>
                        <th>Issue Date</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query to join stickers with vehicle_register and tbluser to get user details
                    $stickerQuery = "SELECT 
                                        s.stickerID, 
                                        s.stickerType, 
                                        s.issueDate, 
                                        s.expiryDate, 
                                        s.isActive AS stickerIsActive, 
                                        vr.licensePlate, 
                                        u.firstName, 
                                        u.lastName, 
                                        u.usertype 
                                    FROM stickers s 
                                    LEFT JOIN vehicle_register vr ON s.stickerID = vr.stickerID 
                                    LEFT JOIN tbluser u ON vr.userID = u.userID 
                                    ORDER BY s.stickerID ASC";
                    $stickerResult = mysqli_query($connection, $stickerQuery);

                    if ($stickerResult && mysqli_num_rows($stickerResult) > 0) {
                        while($sticker = mysqli_fetch_assoc($stickerResult)):
                            $userName = !empty($sticker['firstName']) ? htmlspecialchars($sticker['firstName'] . ' ' . $sticker['lastName']) : 'N/A (Unassigned)';
                            $userTypeDisplay = !empty($sticker['usertype']) ? htmlspecialchars(ucfirst($sticker['usertype'])) : 'N/A';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sticker['stickerID']); ?></td>
                                <td><?php echo $userName; ?></td>
                                <td><?php echo $userTypeDisplay; ?></td>
                                <td><?php echo htmlspecialchars($sticker['licensePlate'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($sticker['stickerType']); ?></td>
                                <td><?php echo htmlspecialchars($sticker['issueDate']); ?></td>
                                <td><?php echo htmlspecialchars($sticker['expiryDate']); ?></td>
                                <td><?php echo $sticker['stickerIsActive'] ? 'Active' : 'Inactive'; ?></td>
                            </tr>
                        <?php endwhile; 
                    } else {
                        echo "<tr><td colspan='8'>No sticker data found or error fetching data.</td></tr>"; // Increased colspan
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
// New JavaScript for sidebar navigation
document.addEventListener('DOMContentLoaded', function() {
    const sidebarButtons = document.querySelectorAll('.dashboard-sidebar .sidebar-nav-button');
    const contentSections = document.querySelectorAll('.dashboard-main-content .content-section');

    sidebarButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');

            // Update button active states
            sidebarButtons.forEach(btn => btn.classList.remove('active-nav-button'));
            this.classList.add('active-nav-button');

            // Update content section visibility
            contentSections.forEach(section => {
                if (section.id === targetId) {
                    section.classList.add('active-content');
                } else {
                    section.classList.remove('active-content');
                }
            });
        });
    });

    // No need to explicitly click, default active classes are set in HTML now.
    // if (sidebarButtons.length > 0) {
    //     // Find the button for 'List of Users' and click it, or ensure it's active by default
    //     const defaultButton = Array.from(sidebarButtons).find(btn => btn.getAttribute('data-target') === 'userTableContainer');
    //     if (defaultButton) defaultButton.click(); 
    // }
});

function confirmDeleteUser(userId, username) {
    if (confirm(`Are you sure you want to delete user "${username}" (ID: ${userId})?\nThis action will delete the user and all their associated data (vehicles, stickers, etc.) and cannot be undone.`)) {
        window.location.href = 'dashboard.php?action=delete_user&id=' + userId;
    }
}

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

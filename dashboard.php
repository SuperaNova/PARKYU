<?php    
    include 'connect.php';
    include 'readrecords.php';

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
        if ($row['facultyID']) {
            $facultyCount++;
        } elseif ($row['staffID']) {
            $staffCount++;
        } elseif ($row['studentID']) {
            $studentCount++;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Function to toggle collapsible sections
        function toggleCollapsible(id) {
            var content = document.getElementById(id);
            if (content.style.display === "none") {
                content.style.display = "block";
            } else {
                content.style.display = "none";
            }
        }
    </script>
    <style>
        /* Your styles remain unchanged */
        :root {
            --white: #FFFFFF;
            --black: #000000;
            --text-color: #566B78;
            --maroon-accent: #6C2C2F;
            --background-general: #FEF3E2;
            --beige-light: #F9E7D1;
            --beige-dark: #E6D5BE;
            --grey-border: #A09D9A;
        }

        body {
            background: var(--background-general);
            color: var(--text-color);
            font-family: 'Inconsolata', monospace;
            font-size: 18px;
            line-height: 1.5;
            text-align: center;
        }

        h2 {
            color: var(--maroon-accent);
        }

        a {
            color: var(--black);
            text-decoration: none;
        }

        header {
            background: var(--maroon-accent);
            padding: 40px;
            text-align: center;
            color: var(--white);
            font-size: 2rem;
            font-weight: bold;
        }

        nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            background: var(--maroon-accent);
            padding: 10px;
            margin: 0;
        }

        nav ul li {
            padding: 10px 20px;
        }

        nav ul li a {
            color: var(--white);
            font-weight: bold;
        }

        nav ul li a:hover {
            color: var(--beige-dark);
        }

        .container {
            max-width: 50em;
            margin: 20px auto;
            padding: 20px;
            background: var(--beige-light);
            border-radius: 10px;
            border: 2px solid var(--maroon-accent);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: var(--beige-light);
            color: var(--text-color);
        }

        th {
            background: var(--maroon-accent);
            color: var(--white);
            padding: 12px;
            border: 2px solid var(--maroon-accent);
        }

        td {
            padding: 10px;
            border: 2px solid var(--grey-border);
        }

        tr:nth-child(even) {
            background: var(--beige-dark);
        }

        tr:nth-child(odd) {
            background: var(--beige-light);
        }

        tr:hover {
            background: #d1bca2;
        }

        .btn {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            border: none;
            cursor: pointer;
            padding: 12px 20px;
            border-radius: 8px;
            background: var(--maroon-accent);
            color: var(--white);
            box-shadow: 0 4px 0 var(--grey-border);
            transition: transform 0.1s ease, background 0.3s ease;
        }

        .btn:hover {
            background: var(--grey-border);
        }

        .btn:active {
            box-shadow: 0 2px 0 var(--grey-border);
            transform: translateY(2px);
        }

        footer {
            text-align: center;
            background: var(--maroon-accent);
            color: white;
            padding: 10px;
            margin-top: 20px;
            width: 100%;
        }

        .collapsible {
            background-color: var(--maroon-accent);
            color: white;
            cursor: pointer;
            padding: 10px;
            width: 100%;
            border: none;
            text-align: center;
            font-size: 1.2em;
            margin-top: 10px;
        }

        .active, .collapsible:hover {
            background-color: var(--beige-dark);
        }

        .content {
            padding: 0 18px;
            display: none;
            overflow: hidden;
            background-color: var(--beige-light);
        }
    </style>
</head>
<body>

<header>
    Vehicle Management System
</header>

<nav>
    <ul>
        <li><a href="adduser.php">Add User</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="container">
    <h2>User Type Distribution</h2>
    <button class="collapsible" onclick="toggleCollapsible('chartContainer')">Toggle Chart</button>
    <div id="chartContainer" class="content">
        <canvas id="userTypeChart" width="400" height="200"></canvas>
        <script>
            var ctx = document.getElementById('userTypeChart').getContext('2d');
            var userTypeChart = new Chart(ctx, {
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
</div>

<div class="container">
    <h2>User Type Distribution (Pie Chart)</h2>
    <button class="collapsible" onclick="toggleCollapsible('pieChartContainer')">Toggle Pie Chart</button>
    <div id="pieChartContainer" class="content">
        <canvas id="userTypePieChart" width="400" height="400"></canvas>
        <script>
            var ctx = document.getElementById('userTypePieChart').getContext('2d');
            var userTypePieChart = new Chart(ctx, {
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
                    responsive: true
                }
            });
        </script>
    </div>
</div>

<div class="container">
    <h2>List of Users</h2>
    <button class="collapsible" onclick="toggleCollapsible('userTableContainer')">Toggle User Table</button>
    <div id="userTableContainer" class="content">
        <table>
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
                // Reset the resultset since it was already used in chart rendering
                $resultset = mysqli_query($connection, $query);
                while($row = $resultset->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['userID']; ?></td>
                        <td><?php echo $row['firstName']; ?></td>
                        <td><?php echo $row['lastName']; ?></td>
                        <td>
                            <?php
                            if ($row['facultyID']) {
                                echo $row['department'];  // Display department for faculty
                            } elseif ($row['staffID']) {
                                echo $row['office'];  // Display office for staff
                            } elseif ($row['studentID']) {
                                echo $row['program'];  // Display program for student
                            } else {
                                echo "N/A";  // Fallback for users not in any category
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($row['facultyID']) {
                                echo "Faculty";
                            } elseif ($row['staffID']) {
                                echo "Staff";
                            } elseif ($row['studentID']) {
                                echo "Student";
                            } else {
                                echo "Unknown";
                            }
                            ?>
                        </td>
                        <td>
                            <button class="btn" onclick="window.location.href='update.php?id=<?php echo $row['userID']; ?>'">UPDATE</button>
                            <button class="btn" onclick="window.location.href='delete.php?id=<?php echo $row['userID']; ?>'">DELETE</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="container">
    <h2>Sticker Information</h2>
    <button class="collapsible" onclick="toggleCollapsible('stickerInfoContainer')">Toggle Sticker Info</button>
    <div id="stickerInfoContainer" class="content">
        <table>
            <thead>
                <tr>
                    <th>Sticker ID</th>
                    <th>Vehicle ID</th>
                    <th>User ID</th>
                    <th>Sticker Type</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query to fetch sticker information
                $stickerQuery = "SELECT stickerID, vehicleID, userID, stickerType FROM stickers";
                $stickerResult = mysqli_query($connection, $stickerQuery);

                // Loop through the stickers and display them in the table
                while($sticker = mysqli_fetch_assoc($stickerResult)): ?>
                    <tr>
                        <td><?php echo $sticker['stickerID']; ?></td>
                        <td><?php echo $sticker['vehicleID']; ?></td>
                        <td><?php echo $sticker['userID']; ?></td>
                        <td><?php echo $sticker['stickerType']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Function to toggle collapsible sections
    function toggleCollapsible(id) {
        var content = document.getElementById(id);
        if (content.style.display === "none") {
            content.style.display = "block";
        } else {
            content.style.display = "none";
        }
    }
</script>



<footer>
    Ivann James Paradero | BSCS - 2nd Year
</footer>

</body>
</html>

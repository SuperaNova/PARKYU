<?php    
    include 'connect.php';
    include 'readrecords.php';   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Variables */
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

        /* General Styles */
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

        /* Header */
        header {
            background: var(--maroon-accent);
            padding: 40px;
            text-align: center;
            color: var(--white);
            font-size: 2rem;
            font-weight: bold;
        }

        /* Navigation */
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

        /* Container */
        .container {
            max-width: 50em;
            margin: 20px auto;
            padding: 20px;
            background: var(--beige-light);
            border-radius: 10px;
            border: 2px solid var(--maroon-accent);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Table */
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
            background: #d1bca2; /* Slightly darker beige */
        }


        /* Buttons */
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

        /* Footer */
        footer {
            text-align: center;
            background: var(--maroon-accent);
            color: white;
            padding: 10px;
            margin-top: 20px;
            width: 100%;
        }
    </style>
</head>
<body>

<header>
    Student Management System
</header>

<nav>
    <ul>
        <li><a href="addrecord.php">Add Student</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="container">
    <h2>List of Students</h2>

    <table>
        <thead>
            <tr>
                <th>ID Number</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Program</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $resultset->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['uid']; ?></td>
                    <td><?php echo $row['firstname']; ?></td>
                    <td><?php echo $row['lastname']; ?></td>
                    <td><?php echo $row['program']; ?></td>
                    <td>
                        <button class="btn" onclick="window.location.href='update.php?id=<?php echo $row['uid']; ?>'">UPDATE</button>
                        <button class="btn" onclick="window.location.href='delete.php?id=<?php echo $row['uid']; ?>'">DELETE</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<footer>
    Jared Sheohn Acebes | BSCS - 2nd Year
</footer>

</body>
</html>

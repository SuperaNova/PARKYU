<?php
session_start(); // Start the session at the very beginning
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ParkU is a parking sticker system implemented at Cebu Institute of Technology University.">
    <title>ParkU - Parking Sticker System</title>
    <script src="design.js"></script>
    <link rel="stylesheet" href="design.css">
</head>
<body>

<nav role="navigation">
  <ul>
    <li><a href="#the-default-view">Home</a></li>
    <li><a href="#about-parku-content">About ParkU</a></li>
    <li><a href="#features">Features</a></li>
    <li><a href="#about-us-content">About Us</a></li>
    <li><a href="#contact-content">Contact Us</a></li>
  </ul>
  <div class="user-session-controls">
    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
        <a href="dashboard.php" class="profile-icon" title="Dashboard">●</a>
        <a href="logout.php" class="logout-link" title="Logout" onclick="confirmLogout(event)">Logout</a>
    <?php else: ?>
        <a href="login.php" class="profile-icon" title="Login/Register">●</a>
    <?php endif; ?>
  </div>
</nav>

<main role="main" class="app">
  
  <section id="about-parku-content">
    <header class="parku-details-header">
        <img src="res/logo.png" width="175" height="175" viewBox="0 0 150 150" />
      <h1>About ParkU</h1>
    </header>
    <div class="container">
      <p><b>ParkU</b> is a next-generation parking management system tailored for university students and staff. It's designed to provide a seamless and efficient parking experience on campus.</p>
      <p>Designed with user convenience at its core, ParkU streamlines the registration process. Returning users can quickly revalidate their parking credentials without re-entering personal details, which speeds up access and minimizes potential registration errors. This system also simplifies the process of replacing damaged or lost stickers, ensuring uninterrupted parking access.</p>
      <p>Security is paramount in ParkU. Advanced identification protocols ensure that only authorized vehicles can enter designated parking areas, protecting the campus environment from unauthorized access. Moreover, the system uses real-time data to optimize parking space management, preventing overcrowding and ensuring a smoother parking experience for everyone.</p>
      <p>ParkU revolutionizes campus mobility by merging ease-of-use, robust security, and efficient space management.</p>
    </div>
  </section>

  <section id="features">
    <header class="features-header">
        <img src="res/logo.png" width="175" height="175" viewBox="0 0 150 150" />
        <h1>Key Features</h1>
    </header>
    <div class="container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Feature</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Easy Sticker Registration</td>
                    <td>Quick and hassle-free sign-up for parking permits.</td>
                </tr>
                <tr>
                    <td>Real-time Parking Slot Availability</td>
                    <td>Check available parking spots in real-time.</td>
                </tr>
                <tr>
                    <td>Parking Duration Management</td>
                    <td>Monitor your parking duration and avoid overstaying.</td>
                </tr>
                <tr>
                    <td>Automatic Payment Integration</td>
                    <td>Seamlessly pay for parking fees using online payment options.</td>
                </tr>
            </tbody>
        </table>
    </div>
  </section>
  
  <section id="about-us-content">
    <header class="about-us-header">
      <img src="res/logo.png" width="175" height="175" viewBox="0 0 150 150" />
      <svg width="175" height="175" viewBox="0 0 150 150" xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <title>snes controller</title>
        <path d="M75 150c41.421356 0 75-33.578644 75-75 0-41.4213562-33.578644-75-75-75C33.5786438 0 0 33.5786438 0 75c0 41.421356 33.5786438 75 75 75zm39.333333-92.4444444v-1.3333334c0-.5555555-.444444-.6666666-.444444-.6666666-5.111111-2.6666667-10.111111-2.4444445-10.111111-2.4444445H93c-.555556 0-.555556.4444445-.555556.4444445v1H56.8888889v-1s0-.4444445-.5555556-.4444445H45.5555556s-5.1111112-.2222222-10.1111112 2.4444445c0 0-.4444444.1111111-.4444444.6666666v1.4444445C29 61.555556 25 68.222222 25 75.777778 25 87.555556 33.3333333 97 45.1111111 97c0 0 8.7777778.222222 12.9999999-4.333333 0 0 .222222-.333334 1.111111-.333334h31.333334s.444444-.111111.777777.333334C95.555556 97.444444 104.666667 97 104.666667 97c11.666666 0 20.111111-9.444444 20.111111-21.222222 0-7.666667-4.222222-14.444445-10.444445-18.2222224zM45.8888889 87.555556c-6.4444445 0-11.6666667-5.222223-11.6666667-11.666667 0-6.444445 5.2222222-11.777778 11.6666667-11.777778 6.4444444 0 11.6666671 5.222222 11.6666671 11.666667 0 6.444444-5.2222227 11.777778-11.6666671 11.777778zm57.6666671 7c-10.333334 0-18.666667-8.333334-18.666667-18.666667s8.333333-18.6666668 18.666667-18.6666668c10.333333 0 18.666666 8.3333338 18.666666 18.6666668 0 10.333333-8.333333 18.666667-18.666666 18.666667zm0-36.1111116c-9.555556 0-17.333334 7.7777776-17.333334 17.3333336 0 9.555555 7.777778 17.333333 17.333334 17.333333 9.555555 0 17.333333-7.777778 17.333333-17.333333 0-9.555556-7.777778-17.3333336-17.333333-17.3333336zm-13 20.4444446C88.777778 77 88.777778 73.888889 90.666667 72l9.444444-7.333333c2-1.777778 5-1.666667 6.888889.111111 1.777778 1.888889 1.777778 5 0 6.666666l-9.777778 7.666667C95 80.888889 92.333333 80.666667 90.555556 78.888889zM107 87.111111c-2.111111 1.777778-4.777778 1.555556-6.666667-.333333-1.888889-1.888889-1.777777-5 .111111-6.888889l9.444445-7.333333c2-1.777778 5-1.666667 6.888889.111111 1.777778 1.888889 1.777778 5 0 6.666666L107 87.111111zm-13.111111-7.888889c2.025044 0 3.666667-1.641622 3.666667-3.666666 0-2.025045-1.641623-3.666667-3.666667-3.666667s-3.666667 1.641622-3.666667 3.666667c0 2.025044 1.641623 3.666666 3.666667 3.666666zM103.444444 72c2.025045 0 3.666667-1.641623 3.666667-3.666667s-1.641622-3.666666-3.666667-3.666666c-2.025044 0-3.666666 1.641622-3.666666 3.666666C99.777778 70.358377 101.4194 72 103.444444 72zm.333334 15.111111c2.025044 0 3.666666-1.641622 3.666666-3.666667 0-2.025044-1.641622-3.666666-3.666666-3.666666-2.025044 0-3.666667 1.641622-3.666667 3.666666 0 2.025045 1.641623 3.666667 3.666667 3.666667zm9.555555-7.222222c2.025044 0 3.666667-1.641623 3.666667-3.666667s-1.641623-3.666666-3.666667-3.666666-3.666666 1.641622-3.666666 3.666666c0 2.025044 1.641622 3.666667 3.666666 3.666667zm-60.4444441-6.444445h-4.5555556v-4.777777c0-.444445-.3333333-.777778-.7777777-.777778h-3.6666667c-.4444445 0-.5555556.333333-.5555556.777778v4.777777h-4.6666666c-.4444445 0-.5555556.111112-.5555556.555556v3.666667c0 .444444.1111111.555555.5555556.555555h4.7777777v4.888889c0 .444445.1111112.777778.5555556.777778h3.6666667c.4444444 0 .7777777-.333333.7777777-.777778v-4.888889H53c.4444444 0 .7777778-.333333.7777778-.777778v-3.333333c-.1111111-.333333-.4444445-.666667-.8888889-.666667z" fill="#FFF" fill-rule="evenodd"/>
      </svg>
      <h1>About Us</h1>
    </header>
    <div class="container">
      <p>ParkU was created with the goal of improving parking efficiency and reducing the stress associated with finding parking spaces on a busy campus. Our system is designed to be easy to use, intuitive, and accessible for all CIT-U students, faculty, and visitors.</p>
      <p>Developed by: Jared Sheohn Acebes, Nathanael Jedd Del Castillo, Ivann James Paradero.</p>
    </div>
  </section>
  
  <section id="contact-content">
    <header class="contact-us-header">
      <img src="res/logo.png" width="175" height="175" viewBox="0 0 150 150" />
      <svg width="175" height="175" viewBox="0 0 150 150" xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <title>n64 controller</title>
        <path d="M75 150c41.421356 0 75-33.578644 75-75 0-41.4213562-33.578644-75-75-75C33.5786438 0 0 33.5786438 0 75c0 41.421356 33.5786438 75 75 75zM94.8140349 34.0802198l-1.4529804-3.278022S88.4025233 27 75.2004618 27h-.3967711c-13.2020615 0-18.1605927 3.8021978-18.1605927 3.8021978l-1.4540887 3.2769231s-21.4189927 1.965934-19.3043576 5.8989011c-12.1646933 3.932967-10.8424922 34.0857143-10.8424922 38.4120879 0 4.3263736 1.8508599 22.9417581 8.9905241 22.9417581s12.163585-27.1373625 12.163585-27.1373625 12.4284686-1.1802198 13.8825573 6.554945c1.4540887 7.7351649 4.9197407 42.2131865 14.7226482 42.2131865h.3967711c9.8029075 0 13.2685595-34.4780216 14.7226482-42.2131865 1.4540887-7.7351648 13.8825576-6.554945 13.8825576-6.554945s5.02392 27.1373625 12.163585 27.1373625c7.139664 0 8.990524-18.6153845 8.990524-22.9417581s1.325526-34.4791209-10.839168-38.410989c2.115744-3.9329671-19.3043571-5.8989011-19.3043571-5.8989011z" fill="#FFF" fill-rule="evenodd"/>
      </svg>
      <h1>Contact Us</h1>
    </header>
    <div class="container">
        <table class="styled-table">
            <tbody>
                <tr>
                    <td>Email:</td>
                    <td><a href="mailto:contact@parku.cit.edu">contact@parku.cit.edu</a></td>
                </tr>
                <tr>
                    <td>Phone:</td>
                    <td>+63 900 000 0000</td>
                </tr>
                <tr>
                    <td>Location:</td>
                    <td>Cebu Institute of Technology - University</td>
                </tr>
            </tbody>
        </table>
        <div style="margin-top: 20px;">
            <iframe 
                width="100%" 
                height="300" 
                style="border: 1px solid var(--text-color);" 
                src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBZMCLQ76i4YjDC5-op70Y380kPBYGV4U8&q=Cebu+Institute+of+Technology+University" allowfullscreen>
            </iframe>
        </div>
        <div class="connect-us-section" style="margin-top: 30px; text-align: center;">
            <h3>Connect With Us</h3>
            <ul class="social-icons">
                <li><a href="#" target="_blank" title="Facebook">F</a></li>
                <li><a href="#" target="_blank" title="Instagram">I</a></li>
                <li><a href="#" target="_blank" title="LinkedIn">L</a></li>
            </ul>
        </div>
    </div>
  </section>

  <section id="the-default-view">
    <div class="container">
        <h2>Welcome to ParkU!</h2>
        <p>ParkU is an innovative parking sticker system designed to streamline parking management at Cebu Institute of Technology University (CIT-U). It helps students, staff, and visitors park efficiently and ensures a smooth experience while on campus.</p>
        <p>Information Technology 1 Project</p>
        <div class="button-container-home">
            <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true): ?>
                <button class="account" onclick="location.href='register.php'">Register</button>
                <button class="account" onclick="location.href='login.php'">Login</button>
            <?php else: ?>
                <button class="account" onclick="location.href='dashboard.php'">Open Dashboard</button>
            <?php endif; ?>
        </div>
    </div>
  </section>
</main>

<footer>
  <p>&copy; 2025 ParkU | Group ParkU | BSCS - 2nd Year</p>
</footer>

</body>
</html>

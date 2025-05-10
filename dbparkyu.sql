-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2025 at 02:27 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbparkyu`
--

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `user_id` int(11) NOT NULL,
  `faculty_id` varchar(20) NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `user_id` int(11) NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `office` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stickers`
--

CREATE TABLE `stickers` (
  `stickerID` int(11) NOT NULL,
  `licensePlate` varchar(20) NOT NULL,
  `userID` int(11) NOT NULL,
  `stickerType` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblfaculty`
--

CREATE TABLE `tblfaculty` (
  `facultyID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `department` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblfaculty`
--

INSERT INTO `tblfaculty` (`facultyID`, `userID`, `department`) VALUES
(2323232, 1231233123, 'CSS');

-- --------------------------------------------------------

--
-- Table structure for table `tblstaff`
--

CREATE TABLE `tblstaff` (
  `staffID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `office` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblstaff`
--

INSERT INTO `tblstaff` (`staffID`, `userID`, `office`) VALUES
(23, 1231232, 'dswd');

-- --------------------------------------------------------

--
-- Table structure for table `tblstudent`
--

CREATE TABLE `tblstudent` (
  `studentID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `program` varchar(50) DEFAULT NULL,
  `yearLevel` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblstudent`
--

INSERT INTO `tblstudent` (`studentID`, `userID`, `program`, `yearLevel`) VALUES
(1, 2, 'bscs', 2),
(2123, 123123, 'bscs', 2),
(41231, 1231233412, 'bscs', 2),
(412312, 2147483647, 'bscs', 3);

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `userID` int(11) NOT NULL,
  `firstName` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `gender` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `usertype` varchar(10) DEFAULT NULL,
  `username` varchar(30) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`userID`, `firstName`, `lastName`, `gender`, `email`, `usertype`, `username`, `password`, `isActive`) VALUES
(1, 'Jared Sheohn', 'Acebes', 'Male', NULL, 'student', 'jares', '$2y$10$WjaiMFizK99kcSP2siK2ROY6y2cOapwIsuBXKV2fhhigY/Nimahjm', 1),
(2, 'Jared Sheohn', 'Acebes', 'Male', NULL, 'student', 'jsacebes', '$2y$10$QlxgH09v0lgW/z8CzPyLNuhBBLPTkSPqzy9UDUzSjPbJ92urvYzwa', 1),
(23, 'Tony', 'Stark', NULL, 'tony.stark@student.com', NULL, NULL, NULL, 1),
(24, 'Natasha', 'Romanoff', NULL, 'natasha.romanoff@staff.com', NULL, NULL, NULL, 1),
(25, 'Steve', 'Rogers', NULL, 'steve.rogers@faculty.com', NULL, NULL, NULL, 1),
(26, 'Thor', 'Odinson', NULL, 'thor.odinson@student.com', NULL, NULL, NULL, 1),
(27, 'Wanda', 'Maximoff', NULL, 'wanda.maximoff@staff.com', NULL, NULL, NULL, 1),
(28, 'Doctor', 'Strange', NULL, 'doctor.strange@faculty.com', NULL, NULL, NULL, 1),
(29, 'Bruce', 'Banner', NULL, 'bruce.banner@student.com', NULL, NULL, NULL, 1),
(30, 'T’Challa', 'Wakanda', NULL, 'tchalla.wakanda@staff.com', NULL, NULL, NULL, 1),
(31, 'Peter', 'Quill', NULL, 'peter.quill@faculty.com', NULL, NULL, NULL, 1),
(32, 'Carol', 'Danvers', NULL, 'carol.danvers@student.com', NULL, NULL, NULL, 1),
(33, 'Tony', 'Stark', NULL, 'tony.stark@student.com', NULL, NULL, NULL, 1),
(34, 'Thor', 'Odinson', NULL, 'thor.odinson@student.com', NULL, NULL, NULL, 1),
(35, 'Bruce', 'Banner', NULL, 'bruce.banner@student.com', NULL, NULL, NULL, 1),
(36, 'Carol', 'Danvers', NULL, 'carol.danvers@student.com', NULL, NULL, NULL, 1),
(37, 'T’Challa', 'Wakanda', NULL, 'tchalla.wakanda@student.com', NULL, NULL, NULL, 1),
(123123, 'Ivann James', 'Paradero', NULL, 'paradero730@gmail.com', NULL, NULL, NULL, 1),
(1231232, 'Ivann James', 'Paradero', NULL, 'paradero730@gmail.com', NULL, NULL, NULL, 1),
(1231233, 'Ivann James', 'Paradero', NULL, 'paradero730@gmail.com', NULL, NULL, NULL, 1),
(123123345, 'Ivann James', 'Paradero', NULL, 'paradero730@gmail.com', NULL, NULL, NULL, 1),
(1231233123, 'Ivann James', 'Paradero', NULL, 'paradero730@gmail.com', NULL, NULL, NULL, 1),
(1231233412, 'Ivann James', 'Paradero', NULL, 'paradero730@gmail.com', NULL, NULL, NULL, 1),
(2147483647, 'Ivann James', 'Paradero', NULL, 'paradero730@gmail.com', NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_register`
--

CREATE TABLE `vehicle_register` (
  `licensePlate` varchar(20) NOT NULL,
  `vehicleType` varchar(30) NOT NULL,
  `vehicleBrand` varchar(50) NOT NULL,
  `isMotorcycle` tinyint(1) DEFAULT 0,
  `isCar` tinyint(1) DEFAULT 0,
  `stickerID` int(11) NOT NULL,
  `studentID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `stickers`
--
ALTER TABLE `stickers`
  ADD PRIMARY KEY (`stickerID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `tblfaculty`
--
ALTER TABLE `tblfaculty`
  ADD PRIMARY KEY (`facultyID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `tblstaff`
--
ALTER TABLE `tblstaff`
  ADD PRIMARY KEY (`staffID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `tblstudent`
--
ALTER TABLE `tblstudent`
  ADD PRIMARY KEY (`studentID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`userID`);

--
-- Indexes for table `vehicle_register`
--
ALTER TABLE `vehicle_register`
  ADD PRIMARY KEY (`licensePlate`),
  ADD KEY `fk_sticker` (`stickerID`),
  ADD KEY `fk_student` (`studentID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `stickers`
--
ALTER TABLE `stickers`
  MODIFY `stickerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tblfaculty`
--
ALTER TABLE `tblfaculty`
  MODIFY `facultyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2323233;

--
-- AUTO_INCREMENT for table `tblstaff`
--
ALTER TABLE `tblstaff`
  MODIFY `staffID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tblstudent`
--
ALTER TABLE `tblstudent`
  MODIFY `studentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=412313;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2147483648;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `stickers`
--
ALTER TABLE `stickers`
  ADD CONSTRAINT `fk_stickers_user` FOREIGN KEY (`userID`) REFERENCES `tbluser` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `tblfaculty`
--
ALTER TABLE `tblfaculty`
  ADD CONSTRAINT `fk_tblfaculty_user` FOREIGN KEY (`userID`) REFERENCES `tbluser` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `tblstaff`
--
ALTER TABLE `tblstaff`
  ADD CONSTRAINT `fk_tblstaff_user` FOREIGN KEY (`userID`) REFERENCES `tbluser` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `tblstudent`
--
ALTER TABLE `tblstudent`
  ADD CONSTRAINT `fk_tblstudent_user` FOREIGN KEY (`userID`) REFERENCES `tbluser` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_register`
--
ALTER TABLE `vehicle_register`
  ADD CONSTRAINT `fk_vehicle_sticker` FOREIGN KEY (`stickerID`) REFERENCES `stickers` (`stickerID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vehicle_student` FOREIGN KEY (`studentID`) REFERENCES `tblstudent` (`studentID`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

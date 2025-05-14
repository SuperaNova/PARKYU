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

-- Drop existing tables if they exist to prevent conflicts
DROP TABLE IF EXISTS `vehicle_register`;
DROP TABLE IF EXISTS `stickers`;
DROP TABLE IF EXISTS `tblstudent`;
DROP TABLE IF EXISTS `tblfaculty`;
DROP TABLE IF EXISTS `tblstaff`;
DROP TABLE IF EXISTS `tbluser`;
DROP TABLE IF EXISTS `faculty`; -- Old redundant table
DROP TABLE IF EXISTS `staff`; -- Old redundant table

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--
CREATE TABLE `tbluser` (
  `userID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `firstName` VARCHAR(50) NOT NULL,
  `lastName` VARCHAR(50) NOT NULL,
  `gender` ENUM('Male', 'Female', 'Other') DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL UNIQUE,
  `usertype` ENUM('student', 'faculty', 'staff') NOT NULL,
  `username` VARCHAR(30) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL COMMENT 'Hashed password',
  `isActive` TINYINT(1) NOT NULL DEFAULT 1,
  `registrationDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblstudent`
--
CREATE TABLE `tblstudent` (
  `studentInfoID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `userID` INT UNSIGNED NOT NULL,
  `program` VARCHAR(50) DEFAULT NULL,
  `yearLevel` INT(1) DEFAULT NULL,
  PRIMARY KEY (`studentInfoID`),
  UNIQUE KEY `unique_userID` (`userID`),
  CONSTRAINT `fk_student_user` FOREIGN KEY (`userID`) REFERENCES `tbluser` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblfaculty`
--
CREATE TABLE `tblfaculty` (
  `facultyInfoID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `userID` INT UNSIGNED NOT NULL,
  `department` VARCHAR(100) DEFAULT NULL,
  `officeLocation` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`facultyInfoID`),
  UNIQUE KEY `unique_userID` (`userID`),
  CONSTRAINT `fk_faculty_user` FOREIGN KEY (`userID`) REFERENCES `tbluser` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblstaff`
--
CREATE TABLE `tblstaff` (
  `staffInfoID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `userID` INT UNSIGNED NOT NULL,
  `office` VARCHAR(100) DEFAULT NULL,
  `position` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`staffInfoID`),
  UNIQUE KEY `unique_userID` (`userID`),
  CONSTRAINT `fk_staff_user` FOREIGN KEY (`userID`) REFERENCES `tbluser` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stickers`
--
CREATE TABLE `stickers` (
  `stickerID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `stickerType` VARCHAR(50) NOT NULL COMMENT 'e.g., Student Car, Faculty Motorcycle',
  `issueDate` DATE NOT NULL,
  `expiryDate` DATE NOT NULL,
  `isActive` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`stickerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_register`
--
CREATE TABLE `vehicle_register` (
  `licensePlate` VARCHAR(20) NOT NULL,
  `userID` INT UNSIGNED NOT NULL COMMENT 'Owner of the vehicle',
  `stickerID` INT UNSIGNED NULL COMMENT 'Associated sticker, can be NULL if pending',
  `vehicleType` ENUM('Car', 'Motorcycle', 'Van', 'Other') NOT NULL,
  `vehicleMake` VARCHAR(50) DEFAULT NULL,
  `vehicleModel` VARCHAR(50) DEFAULT NULL,
  `vehicleColor` VARCHAR(30) DEFAULT NULL,
  `registrationDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isActive` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`licensePlate`),
  CONSTRAINT `fk_vehicle_user` FOREIGN KEY (`userID`) REFERENCES `tbluser` (`userID`) ON DELETE CASCADE,
  CONSTRAINT `fk_vehicle_sticker` FOREIGN KEY (`stickerID`) REFERENCES `stickers` (`stickerID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */; 
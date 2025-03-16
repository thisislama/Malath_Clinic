-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 16, 2025 at 08:32 PM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `malath`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `id` int(255) NOT NULL,
  `PatientID` int(255) NOT NULL,
  `DoctorID` int(255) NOT NULL,
  `date` date NOT NULL,
  `time` time(6) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `status` enum('Pending','Confirmed','Done') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`id`, `PatientID`, `DoctorID`, `date`, `time`, `reason`, `status`) VALUES
(401, 101, 301, '2024-03-20', '10:00:00.000000', 'Routine Checkup', 'Pending'),
(402, 102, 302, '2024-03-21', '11:30:00.000000', 'Headache Consultation', 'Confirmed'),
(403, 103, 303, '2024-03-22', '14:15:00.000000', 'Pediatric Follow-up', 'Done');

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `id` int(255) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `uniqueFileName` varchar(255) NOT NULL,
  `SpecialityID` int(255) NOT NULL,
  `emailAddress` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`id`, `firstName`, `lastName`, `uniqueFileName`, `SpecialityID`, `emailAddress`, `password`) VALUES
(301, 'John', 'Doe', 'img1.jpg', 201, 'johndoe@example.com', 'securepass'),
(302, 'Jane', 'Smith', 'img2.jpg', 202, 'janesmith@example.com', 'mypassword'),
(303, 'Emily', 'White', 'img3.jpg', 203, 'emilywhite@example.com', 'pass123');

-- --------------------------------------------------------

--
-- Table structure for table `medication`
--

CREATE TABLE `medication` (
  `id` int(255) NOT NULL,
  `MedicationName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `medication`
--

INSERT INTO `medication` (`id`, `MedicationName`) VALUES
(501, 'Paracetamol'),
(502, 'Ibuprofen'),
(503, 'Amoxicillin');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `id` int(255) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `Gender` varchar(255) NOT NULL,
  `DoB` date NOT NULL,
  `emailAddress` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`id`, `firstName`, `lastName`, `Gender`, `DoB`, `emailAddress`, `password`) VALUES
(101, 'Alice', 'Smith', 'Female', '1995-04-12', 'alice@example.com', 'password123'),
(102, 'Bob', 'Johnson', 'Male', '1990-06-25', 'bob@example.com', 'password456'),
(103, 'Charlie', 'Brown', 'Male', '1988-12-01', 'charlie@example.com', 'password789');

-- --------------------------------------------------------

--
-- Table structure for table `prescription`
--

CREATE TABLE `prescription` (
  `id` int(255) NOT NULL,
  `AppointmentID` int(255) NOT NULL,
  `MedicationID` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `prescription`
--

INSERT INTO `prescription` (`id`, `AppointmentID`, `MedicationID`) VALUES
(111, 401, 501),
(222, 402, 502),
(333, 403, 503);

-- --------------------------------------------------------

--
-- Table structure for table `speciality`
--

CREATE TABLE `speciality` (
  `id` int(255) NOT NULL,
  `speciality` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `speciality`
--

INSERT INTO `speciality` (`id`, `speciality`) VALUES
(201, 'Cardiology'),
(202, 'Neurology'),
(203, 'Pediatrics');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `DoctorID` (`DoctorID`),
  ADD KEY `PatientID` (`PatientID`);

--
-- Indexes for table `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `SpecialityID` (`SpecialityID`);

--
-- Indexes for table `medication`
--
ALTER TABLE `medication`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prescription`
--
ALTER TABLE `prescription`
  ADD PRIMARY KEY (`id`),
  ADD KEY `AppointmentID` (`AppointmentID`),
  ADD KEY `MedicationID` (`MedicationID`);

--
-- Indexes for table `speciality`
--
ALTER TABLE `speciality`
  ADD PRIMARY KEY (`id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`DoctorID`) REFERENCES `doctor` (`id`),
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`PatientID`) REFERENCES `patient` (`id`);

--
-- Constraints for table `doctor`
--
ALTER TABLE `doctor`
  ADD CONSTRAINT `SpecialityID` FOREIGN KEY (`SpecialityID`) REFERENCES `speciality` (`id`);

--
-- Constraints for table `prescription`
--
ALTER TABLE `prescription`
  ADD CONSTRAINT `prescription_ibfk_1` FOREIGN KEY (`AppointmentID`) REFERENCES `appointment` (`id`),
  ADD CONSTRAINT `prescription_ibfk_2` FOREIGN KEY (`MedicationID`) REFERENCES `medication` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

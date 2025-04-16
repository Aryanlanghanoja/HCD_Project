-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2025 at 10:49 AM
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
-- Database: `hcd_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `emp_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_no` varchar(20) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `profile_photo` longtext DEFAULT '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',
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `name`, `emp_id`, `password`, `phone_no`, `email`, `profile_photo`, `reset_token`, `token_expiry`) VALUES
(2, 'Aryan Langhanoja', '92200133030', '$2y$10$UtxZ2kKtWOcni1LZQ.w7G.Nk.e/TReuAlwxgSjCcj5QbhYmgrVK0y', '9404806497', 'aryanlanghanoja233@gmail.com', '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ywtujYZB/wA9D7VnzW0txP58bPFIPlDocHHpzwR9aiuY5bRpLq0YBfvTQsQFfHUgngNj8D3x1q7Y6jb3kCSxSDB7dwfQjsfauBq2p7dKqprzKW26YlftdwcdRhP/AImo/JXeHO55Om5jkj1x6fhWjPIqF9hG5jya53UtTVWNvbsxk/5aSIufLXu3vjn2GOecApRlJ2RtKcKceeWiJ7O4LaxKVb90yGMejFCOR+LsP+A1ovJWddQJaz6dFF/C7HH+yEYE/mR+dTPJX1eUJyw68mz4jN53xDl3HSPVWR6V3qtI9e1CB48pDZXqpI3WnyPVWV66oROacivG9WY3ooreaMYMso9WEkoormkjeLJ1kolRZSrZZJU5SRDhl+h/p0PfNFFc84RkrSWh0Qm4u6eotxfTvbi1uIi8jOmJYx8rKGBO4fw8A+x/HFSTRRStvZdsuMCRTtYfiP5UUVxUsHRpKUYrRvqddTF1ajjJvVdjPlvprqV9Pib97Eds0oHABAIP1wenr7ZInKQQJHbIVjiJ3yux/gXG4k988L/wL2oorheGp4XD1J01q7r8bHe8VVxdenGq7pJflckMzXNy11IpQbdkSMMFVzkk+hJx+Q75oeSiivXw9GFGmoQ2R5FerKrNzluyB5Kru9FFdkUckmVpHqtI9FFdMEYTZ//Z', '6afdf6c403d4d06556e63cfeae6bf7a5e1c49eaf7190a9eeaab5ee93dc5baba5d87b3dcb98aa925d1830021496f0fe1f87b1', '2025-03-23 18:33:23');

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

CREATE TABLE `batches` (
  `batch_id` int(11) NOT NULL,
  `batch_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batches`
--

INSERT INTO `batches` (`batch_id`, `batch_name`) VALUES
(1, 'A'),
(2, 'B'),
(3, 'C');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_name`) VALUES
(3, 'EK1'),
(4, 'EK2'),
(1, 'TK1'),
(2, 'TK2');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `exam_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `semester_id` int(11) DEFAULT NULL,
  `invigilator_id` int(11) DEFAULT NULL,
  `exam_date` date NOT NULL,
  `duration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_questions`
--

CREATE TABLE `exam_questions` (
  `exam_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_submissions`
--

CREATE TABLE `exam_submissions` (
  `exam_submission_id` int(11) NOT NULL,
  `enrollment_no` varchar(50) DEFAULT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `language` varchar(50) NOT NULL,
  `filepath` text NOT NULL,
  `status` enum('Pending','Accepted','Rejected') NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `score` float DEFAULT 0,
  `compiler_output` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `question_title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `testcase` text NOT NULL,
  `expected_output` text NOT NULL,
  `example_testcase_1` text NOT NULL,
  `example_outcome_1` text NOT NULL,
  `explanation_1` text NOT NULL,
  `example_testcase_2` text NOT NULL,
  `example_outcome_2` text NOT NULL,
  `explanation_2` text NOT NULL,
  `example_testcase_3` text NOT NULL,
  `example_outcome_3` text NOT NULL,
  `explanation_3` text NOT NULL,
  `tags` text NOT NULL,
  `difficulty` text NOT NULL,
  `constraints` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `question_title`, `description`, `testcase`, `expected_output`, `example_testcase_1`, `example_outcome_1`, `explanation_1`, `example_testcase_2`, `example_outcome_2`, `explanation_2`, `example_testcase_3`, `example_outcome_3`, `explanation_3`, `tags`, `difficulty`, `constraints`) VALUES
(1, '3sum', 'Given an integer array nums, return all the triplets [nums[i], nums[j], nums[k]] such that i != j, i != k, and j != k, and nums[i] + nums[j] + nums[k] == 0.\r\n\r\nNotice that the solution set must not contain duplicate triplets.', '3Sum.txt', '3Sum_op.txt', 'nums = [-1,0,1,2,-1,-4]', '[[-1,-1,2],[-1,0,1]]', 'nums[0] + nums[1] + nums[2] = (-1) + 0 + 1 = 0.\r\nnums[1] + nums[2] + nums[4] = 0 + 1 + (-1) = 0.\r\nnums[0] + nums[3] + nums[4] = (-1) + 2 + (-1) = 0.\r\nThe distinct triplets are [-1,0,1] and [-1,-1,2].\r\nNotice that the order of the output and the order of the triplets does not matter.', 'nums = [0,1,1]', '[]', 'The only possible triplet does not sum up to 0.', 'nums = [0,0,0]', '[[0,0,0]]', 'he only possible triplet sums up to', 'Array , Two Pointers , Sorting', 'Medium', '3 <= nums.length <= 3000 ,\r\n-105 <= nums[i] <= 105');

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `semester_id` int(11) NOT NULL,
  `semester_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`semester_id`, `semester_number`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `enrollment_no` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `gr_number` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_no` varchar(20) DEFAULT NULL,
  `semester_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `profile_photo` longtext DEFAULT '\'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\'',
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `submission_id` int(11) NOT NULL,
  `enrollment_no` varchar(50) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testcases`
--

CREATE TABLE `testcases` (
  `testcase_id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `input` text NOT NULL,
  `output` text NOT NULL,
  `explanation` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `emp_id` (`emp_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `batches`
--
ALTER TABLE `batches`
  ADD PRIMARY KEY (`batch_id`),
  ADD UNIQUE KEY `batch_name` (`batch_name`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD UNIQUE KEY `class_name` (`class_name`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`exam_id`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `semester_id` (`semester_id`),
  ADD KEY `invigilator_id` (`invigilator_id`),
  ADD KEY `idx_exam_id` (`exam_id`);

--
-- Indexes for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD PRIMARY KEY (`exam_id`,`question_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `exam_submissions`
--
ALTER TABLE `exam_submissions`
  ADD PRIMARY KEY (`exam_submission_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `idx_exam_submissions_enrollment_no` (`enrollment_no`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `idx_question_id` (`question_id`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`semester_id`),
  ADD UNIQUE KEY `semester_number` (`semester_number`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`enrollment_no`),
  ADD UNIQUE KEY `gr_number` (`gr_number`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `semester_id` (`semester_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `idx_enrollment_no` (`enrollment_no`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `idx_submissions_enrollment_no` (`enrollment_no`);

--
-- Indexes for table `testcases`
--
ALTER TABLE `testcases`
  ADD PRIMARY KEY (`testcase_id`),
  ADD KEY `question_id` (`question_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `batches`
--
ALTER TABLE `batches`
  MODIFY `batch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_submissions`
--
ALTER TABLE `exam_submissions`
  MODIFY `exam_submission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `semesters`
--
ALTER TABLE `semesters`
  MODIFY `semester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `testcases`
--
ALTER TABLE `testcases`
  MODIFY `testcase_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`batch_id`),
  ADD CONSTRAINT `exams_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`),
  ADD CONSTRAINT `exams_ibfk_3` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`semester_id`),
  ADD CONSTRAINT `exams_ibfk_4` FOREIGN KEY (`invigilator_id`) REFERENCES `admins` (`admin_id`);

--
-- Constraints for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD CONSTRAINT `exam_questions_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`),
  ADD CONSTRAINT `exam_questions_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`);

--
-- Constraints for table `exam_submissions`
--
ALTER TABLE `exam_submissions`
  ADD CONSTRAINT `exam_submissions_ibfk_1` FOREIGN KEY (`enrollment_no`) REFERENCES `students` (`enrollment_no`),
  ADD CONSTRAINT `exam_submissions_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`semester_id`),
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`),
  ADD CONSTRAINT `students_ibfk_3` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`batch_id`);

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`enrollment_no`) REFERENCES `students` (`enrollment_no`),
  ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`);

--
-- Constraints for table `testcases`
--
ALTER TABLE `testcases`
  ADD CONSTRAINT `testcases_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

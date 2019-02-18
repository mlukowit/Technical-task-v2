-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 17, 2019 at 08:02 PM
-- Server version: 5.7.23
-- PHP Version: 7.1.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `techtask`
--

-- --------------------------------------------------------

--
-- Table structure for table `questionsanswers`
--

CREATE TABLE `questionsanswers` (
  `IDprogressive` int(11) NOT NULL,
  `IDquiz` int(11) NOT NULL,
  `IDquestion` int(11) NOT NULL,
  `TextQuestion` text NOT NULL,
  `Answers` text NOT NULL,
  `CorrectOption` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `questionsanswers`
--

INSERT INTO `questionsanswers` (`IDprogressive`, `IDquiz`, `IDquestion`, `TextQuestion`, `Answers`, `CorrectOption`) VALUES
(1, 1, 1, 'Question 1 Quiz 1', 'A!&!B!&!c!&!d!&!e', 3),
(2, 1, 2, 'Question 2 Quiz 1', 'A!&!B!&!c', 1),
(3, 2, 1, 'Question 1 Quiz 2', 'A!&!B!&!c!&!d!&!e!&!f', 6),
(4, 2, 2, 'Question 2 Quiz 2', 'A!&!B', 0),
(5, 2, 3, 'Question 3 Quiz 2', 'A!&!B!&!c!&!D', 3),
(6, 1, 3, 'Question 3 Quiz 1', 'A!&!B!&!c!&!kj!&!ckbkg!&!chghjg!&!cggg!&!c3948394', 1),
(7, 2, 4, 'Question 4 Quiz 2', 'A!&!B', 2),
(8, 2, 5, 'Question 4 Quiz 2', 'A!&!B!&!4095ntrut!&!Bigrjg9u4!&!B\\l;fks', 3);

-- --------------------------------------------------------

--
-- Table structure for table `quizname`
--

CREATE TABLE `quizname` (
  `IDquiz` int(11) NOT NULL,
  `QuizName` text NOT NULL,
  `NumberOfQuest` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `quizname`
--

INSERT INTO `quizname` (`IDquiz`, `QuizName`, `NumberOfQuest`) VALUES
(1, 'Test Quiz 1', 3),
(2, 'Test Quiz 2', 5);

-- --------------------------------------------------------

--
-- Table structure for table `useranswers`
--

CREATE TABLE `useranswers` (
  `UserID` int(11) NOT NULL DEFAULT '0',
  `QuestionID` int(11) NOT NULL DEFAULT '0',
  `AnswerProgNumber` int(11) NOT NULL DEFAULT '0',
  `QuestionText` text NOT NULL,
  `AnswerText` text NOT NULL,
  `Timestamp` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `IDuserTable` int(11) NOT NULL,
  `UserName` text NOT NULL,
  `QuizID` int(11) NOT NULL,
  `QuizResults` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `questionsanswers`
--
ALTER TABLE `questionsanswers`
  ADD UNIQUE KEY `Idprogr` (`IDprogressive`);

--
-- Indexes for table `quizname`
--
ALTER TABLE `quizname`
  ADD PRIMARY KEY (`IDquiz`),
  ADD KEY `IDquiz` (`IDquiz`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`IDuserTable`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `questionsanswers`
--
ALTER TABLE `questionsanswers`
  MODIFY `IDprogressive` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `IDuserTable` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- College Digital Notice Board Database Schema
-- Database: digital_notice_board

CREATE DATABASE IF NOT EXISTS `digital_notice_board` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `digital_notice_board`;

-- Table structure for table `notices`
DROP TABLE IF EXISTS `notices`;
CREATE TABLE `notices` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `is_pinned` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample seed data for initial testing
INSERT INTO `notices` (`id`, `title`, `content`, `is_pinned`, `created_at`) VALUES
(1, 'In-Semester Examination Schedule', '[Exams] The In-Semester Examinations for SE, TE, and BE Computer Engineering will commence from next Monday. Detailed timetable has been displayed on the departmental notice board.', 1, NOW()),
(2, 'Annual Technical Symposium - Innovate 2025', '[Events] Department of Computer Engineering is organizing the Annual National Level Technical Symposium. Registrations are open for Coding, Hackathon, and Project Exhibition competitions.', 1, NOW()),
(3, 'Notice regarding Public Holiday', '[Holidays] The college will remain closed tomorrow on account of public holiday. Academic sessions will resume as per regular timetable on the following day.', 0, NOW()),
(4, 'Submission of Term-Work & Seminar Reports', '[General] All Final Year Computer Engineering students must submit their draft seminar reports and project phase-1 documentation to their respective guides by end of this week.', 0, NOW());

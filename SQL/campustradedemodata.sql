-- =========================================
-- DEMO DATA FOR CampusTrade
-- =========================================
-- =========================================
-- 20 demo accounts
-- =========================================

INSERT INTO `accounts` (`id`, `email`, `password`, `first_name`, `last_name`, `school_name`, `major`, `acad_role`, `city_state`, `created_at`, `must_change_password`, `reset_token_hash`, `reset_expires_at`) VALUES
(1, 'user01@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Alex', 'Lopez', 'Metro State University', 'Computer Science', 'Student', 'Saint Paul, MN', '2025-10-01 17:00:00', 0, NULL, NULL),
(2, 'user02@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Brian', 'Kim', 'University of Minnesota', 'Business', 'Alumni', 'Minneapolis, MN', '2025-10-02 17:00:00', 0, NULL, NULL),
(3, 'user03@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Carla', 'Singh', 'Augsburg University', 'Biology', 'Student', 'Bloomington, MN', '2025-10-03 17:00:00', 0, NULL, NULL),
(4, 'user04@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'David', 'Rivera', 'St. Thomas University', 'Psychology', 'Alumni', 'Maplewood, MN', '2025-10-04 17:00:00', 0, NULL, NULL),
(5, 'user05@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Emma', 'Johnson', 'Metro State University', 'Mathematics', 'Student', 'Roseville, MN', '2025-10-05 17:00:00', 0, NULL, NULL),
(6, 'user06@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Fatima', 'Patel', 'University of Minnesota', 'Accounting', 'Alumni', 'Saint Paul, MN', '2025-10-06 17:00:00', 0, NULL, NULL),
(7, 'user07@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'George', 'Nguyen', 'Augsburg University', 'Computer Science', 'Student', 'Minneapolis, MN', '2025-10-07 17:00:00', 0, NULL, NULL),
(8, 'user08@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Hannah', 'Baker', 'St. Thomas University', 'Business', 'Alumni', 'Bloomington, MN', '2025-10-08 17:00:00', 0, NULL, NULL),
(9, 'user09@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Ivan', 'Garcia', 'Metro State University', 'Biology', 'Student', 'Maplewood, MN', '2025-10-09 17:00:00', 0, NULL, NULL),
(10, 'user10@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Jade', 'Chen', 'University of Minnesota', 'Psychology', 'Alumni', 'Roseville, MN', '2025-10-10 17:00:00', 0, NULL, NULL),
(11, 'user11@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Kevin', 'Brown', 'Augsburg University', 'Mathematics', 'Student', 'Saint Paul, MN', '2025-10-11 17:00:00', 0, NULL, NULL),
(12, 'user12@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Lena', 'Ali', 'St. Thomas University', 'Accounting', 'Alumni', 'Minneapolis, MN', '2025-10-12 17:00:00', 0, NULL, NULL),
(13, 'user13@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Marco', 'Diaz', 'Metro State University', 'Computer Science', 'Student', 'Bloomington, MN', '2025-10-13 17:00:00', 0, NULL, NULL),
(14, 'user14@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Nina', 'Miller', 'University of Minnesota', 'Business', 'Alumni', 'Maplewood, MN', '2025-10-14 17:00:00', 0, NULL, NULL),
(15, 'user15@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Omar', 'Shah', 'Augsburg University', 'Biology', 'Student', 'Roseville, MN', '2025-10-15 17:00:00', 0, NULL, NULL),
(16, 'user16@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Priya', 'Khan', 'St. Thomas University', 'Psychology', 'Alumni', 'Saint Paul, MN', '2025-10-16 17:00:00', 0, NULL, NULL),
(17, 'user17@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Quinn', 'Young', 'Metro State University', 'Mathematics', 'Student', 'Minneapolis, MN', '2025-10-17 17:00:00', 0, NULL, NULL),
(18, 'user18@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Rosa', 'Hernandez', 'University of Minnesota', 'Accounting', 'Alumni', 'Bloomington, MN', '2025-10-18 17:00:00', 0, NULL, NULL),
(19, 'user19@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Sam', 'Wang', 'Augsburg University', 'Computer Science', 'Student', 'Maplewood, MN', '2025-10-19 17:00:00', 0, NULL, NULL),
(20, 'user20@demo.edu', '$2y$10$fRzUNkaB91wn5ZAqUgTJgetlZlP/zaDpYg1IbgVhBjMR5lGmCK5yC', 'Tara', 'Adams', 'St. Thomas University', 'Business', 'Alumni', 'Roseville, MN', '2025-10-20 17:00:00', 0, NULL, NULL),
(21, 'admin@go.minnstate.edu', '$2y$10$KzJEb..4sQw7saen3vz.ce5CXAYMvlYCANTBhADlwMEgvbEQWix9.', 'Admin', 'Account', 'Admin University', 'Admin', 'Admin', 'Roseville, MN', '2025-10-20 17:00:00', 0, NULL, NULL),
(22, 'user21@go.minnstate.edu', '$2y$10$DwLhcDEfbmvBK17aRgVGduE8XxxHqA76lol/7hxtvvetV1rTkQBNe', 'User', 'Account', 'User University', 'User', 'Student', 'Roseville, MN', '2025-10-20 17:00:00', 0, NULL, NULL);

-- =========================================
-- 20 demo book listings
-- =========================================

INSERT INTO `booklistings` (
  `id`,`seller_id`,`title`,`isbn`,`image_path`,
  `price`,`book_state`,`status`,`course_id`,`contact_info`,`created_at`
) VALUES
  (1, 1, 'Introduction to Psychology', '978000000100', 'Uploads/Books/book01.jpg', 10, 'New', 'Active', 'PSY101', 'user01@demo.edu', '2025-11-01 09:30:00'),
  (2, 2, 'Calculus Early Transcendentals', '978000000101', 'Uploads/Books/book02.jpg', 13, 'Used', 'Active', 'MATH150', 'user02@demo.edu', '2025-11-02 09:30:00'),
  (3, 3, 'Biology: Concepts and Investigations', '978000000102', 'Uploads/Books/book03.jpg', 16, 'Used', 'Active', 'BIO110', 'user03@demo.edu', '2025-11-03 09:30:00'),
  (4, 4, 'Microeconomics', '978000000103', 'Uploads/Books/book04.jpg', 19, 'New', 'Sold', 'ECON101', 'user04@demo.edu', '2025-11-04 09:30:00'),
  (5, 5, 'Macroeconomics', '978000000104', 'Uploads/Books/book05.jpg', 22, 'Used', 'Active', 'ECON102', 'user05@demo.edu', '2025-11-05 09:30:00'),
  (6, 6, 'Organic Chemistry', '978000000105', 'Uploads/Books/book06.jpg', 25, 'Used', 'Active', 'CHEM210', 'user06@demo.edu', '2025-11-06 09:30:00'),
  (7, 7, 'Physics for Scientists and Engineers', '978000000106', 'Uploads/Books/book07.jpg', 28, 'New', 'Sold', 'PHYS150', 'user07@demo.edu', '2025-11-07 09:30:00'),
  (8, 8, 'Intro to Algorithms', '978000000107', 'Uploads/Books/book08.jpg', 31, 'Used', 'Active', 'CS201', 'user08@demo.edu', '2025-11-08 09:30:00'),
  (9, 9, 'Data Structures in Java', '978000000108', 'Uploads/Books/book09.jpg', 34, 'Used', 'Active', 'CS220', 'user09@demo.edu', '2025-11-09 09:30:00'),
  (10, 10, 'Operating Systems Concepts', '978000000109', 'Uploads/Books/book10.jpg', 37, 'New', 'Sold', 'CS310', 'user10@demo.edu', '2025-11-10 09:30:00'),
  (11, 11, 'Database System Concepts', '978000000110', 'Uploads/Books/book11.jpg', 40, 'Used', 'Active', 'CS340', 'user11@demo.edu', '2025-11-11 09:30:00'),
  (12, 12, 'Digital Logic Design', '978000000111', 'Uploads/Books/book12.jpg', 43, 'Used', 'Active', 'EE200', 'user12@demo.edu', '2025-11-12 09:30:00'),
  (13, 13, 'Discrete Mathematics', '978000000112', 'Uploads/Books/book13.jpg', 46, 'New', 'Sold', 'MATH230', 'user13@demo.edu', '2025-11-13 09:30:00'),
  (14, 14, 'Linear Algebra and Its Applications', '978000000113', 'Uploads/Books/book14.jpg', 49, 'Used', 'Active', 'MATH240', 'user14@demo.edu', '2025-11-14 09:30:00'),
  (15, 15, 'Modern World History', '978000000114', 'Uploads/Books/book15.jpg', 52, 'Used', 'Active', 'HIST101', 'user15@demo.edu', '2025-11-15 09:30:00'),
  (16, 16, 'Marketing Principles', '978000000115', 'Uploads/Books/book16.jpg', 55, 'New', 'Sold', 'MKTG200', 'user16@demo.edu', '2025-11-16 09:30:00'),
  (17, 17, 'Financial Accounting', '978000000116', 'Uploads/Books/book17.jpg', 58, 'Used', 'Active', 'ACCT200', 'user17@demo.edu', '2025-11-17 09:30:00'),
  (18, 18, 'Statistics for Business', '978000000117', 'Uploads/Books/book18.jpg', 61, 'Used', 'Active', 'STAT201', 'user18@demo.edu', '2025-11-18 09:30:00'),
  (19, 19, 'Human Anatomy & Physiology', '978000000118', 'Uploads/Books/book19.jpg', 64, 'New', 'Sold', 'BIOL201', 'user19@demo.edu', '2025-11-19 09:30:00'),
  (20, 20, 'Software Engineering', '978000000119', 'Uploads/Books/book20.jpg', 67, 'Used', 'Active', 'CS350', 'user20@demo.edu', '2025-11-20 09:30:00');

-- =========================================
-- 20 demo tickets
-- =========================================

INSERT INTO `tickets` (`id`,`name`,`email`,`message`,`created_at`) VALUES
  (1, 'Alex Lopez', 'alex@demo.edu', 'Demo support ticket #1 for testing.', '2025-11-01 15:45:00'),
  (2, 'Brian Kim', 'brian@demo.edu', 'Demo support ticket #2 for testing.', '2025-11-02 15:45:00'),
  (3, 'Carla Singh', 'carla@demo.edu', 'Demo support ticket #3 for testing.', '2025-11-03 15:45:00'),
  (4, 'David Rivera', 'david@demo.edu', 'Demo support ticket #4 for testing.', '2025-11-04 15:45:00'),
  (5, 'Emma Johnson', 'emma@demo.edu', 'Demo support ticket #5 for testing.', '2025-11-05 15:45:00'),
  (6, 'Fatima Patel', 'fatima@demo.edu', 'Demo support ticket #6 for testing.', '2025-11-06 15:45:00'),
  (7, 'George Nguyen', 'george@demo.edu', 'Demo support ticket #7 for testing.', '2025-11-07 15:45:00'),
  (8, 'Hannah Baker', 'hannah@demo.edu', 'Demo support ticket #8 for testing.', '2025-11-08 15:45:00'),
  (9, 'Ivan Garcia', 'ivan@demo.edu', 'Demo support ticket #9 for testing.', '2025-11-09 15:45:00'),
  (10, 'Jade Chen', 'jade@demo.edu', 'Demo support ticket #10 for testing.', '2025-11-10 15:45:00'),
  (11, 'Kevin Brown', 'kevin@demo.edu', 'Demo support ticket #11 for testing.', '2025-11-11 15:45:00'),
  (12, 'Lena Ali', 'lena@demo.edu', 'Demo support ticket #12 for testing.', '2025-11-12 15:45:00'),
  (13, 'Marco Diaz', 'marco@demo.edu', 'Demo support ticket #13 for testing.', '2025-11-13 15:45:00'),
  (14, 'Nina Miller', 'nina@demo.edu', 'Demo support ticket #14 for testing.', '2025-11-14 15:45:00'),
  (15, 'Omar Shah', 'omar@demo.edu', 'Demo support ticket #15 for testing.', '2025-11-15 15:45:00'),
  (16, 'Priya Khan', 'priya@demo.edu', 'Demo support ticket #16 for testing.', '2025-11-16 15:45:00'),
  (17, 'Quinn Young', 'quinn@demo.edu', 'Demo support ticket #17 for testing.', '2025-11-17 15:45:00'),
  (18, 'Rosa Hernandez', 'rosa@demo.edu', 'Demo support ticket #18 for testing.', '2025-11-18 15:45:00'),
  (19, 'Sam Wang', 'sam@demo.edu', 'Demo support ticket #19 for testing.', '2025-11-19 15:45:00'),
  (20, 'Tara Adams', 'tara@demo.edu', 'Demo support ticket #20 for testing.', '2025-11-20 15:45:00');

-- =========================================
-- 20 demo feed posts
-- =========================================

INSERT INTO `posts` (
  `id`,`user_id`,`type`,`text`,`image_data`,`event_datetime`,`created_at`
) VALUES
  (1, 1, 'post', 'Demo post #1 on CampusTrade feed.', 'demo_image_data_base64', NULL, '2025-11-01 10:15:00'),
  (2, 2, 'post', 'Demo post #2 on CampusTrade feed.', NULL, NULL, '2025-11-02 10:15:00'),
  (3, 3, 'post', 'Demo post #3 on CampusTrade feed.', NULL, NULL, '2025-11-03 10:15:00'),
  (4, 4, 'post', 'Demo post #4 on CampusTrade feed.', 'demo_image_data_base64', NULL, '2025-11-04 10:15:00'),
  (5, 5, 'event', 'Demo event #5 on CampusTrade feed.', NULL, '2025-12-05 18:00:00', '2025-11-05 10:15:00'),
  (6, 6, 'post', 'Demo post #6 on CampusTrade feed.', NULL, NULL, '2025-11-06 10:15:00'),
  (7, 7, 'post', 'Demo post #7 on CampusTrade feed.', 'demo_image_data_base64', NULL, '2025-11-07 10:15:00'),
  (8, 8, 'post', 'Demo post #8 on CampusTrade feed.', NULL, NULL, '2025-11-08 10:15:00'),
  (9, 9, 'post', 'Demo post #9 on CampusTrade feed.', NULL, NULL, '2025-11-09 10:15:00'),
  (10, 10, 'event', 'Demo event #10 on CampusTrade feed.', 'demo_image_data_base64', '2025-12-10 18:00:00', '2025-11-10 10:15:00'),
  (11, 11, 'post', 'Demo post #11 on CampusTrade feed.', NULL, NULL, '2025-11-11 10:15:00'),
  (12, 12, 'post', 'Demo post #12 on CampusTrade feed.', NULL, NULL, '2025-11-12 10:15:00'),
  (13, 13, 'post', 'Demo post #13 on CampusTrade feed.', 'demo_image_data_base64', NULL, '2025-11-13 10:15:00'),
  (14, 14, 'post', 'Demo post #14 on CampusTrade feed.', NULL, NULL, '2025-11-14 10:15:00'),
  (15, 15, 'event', 'Demo event #15 on CampusTrade feed.', NULL, '2025-12-15 18:00:00', '2025-11-15 10:15:00'),
  (16, 16, 'post', 'Demo post #16 on CampusTrade feed.', NULL, NULL, '2025-11-16 10:15:00'),
  (17, 17, 'post', 'Demo post #17 on CampusTrade feed.', 'demo_image_data_base64', NULL, '2025-11-17 10:15:00'),
  (18, 18, 'post', 'Demo post #18 on CampusTrade feed.', NULL, NULL, '2025-11-18 10:15:00'),
  (19, 19, 'post', 'Demo post #19 on CampusTrade feed.', NULL, NULL, '2025-11-19 10:15:00'),
  (20, 20, 'event', 'Demo event #20 on CampusTrade feed.', 'demo_image_data_base64', '2025-12-20 18:00:00', '2025-11-20 10:15:00');

-- =========================================
-- Demo likes (NOT 20 per user/table, just some activity)
-- =========================================

INSERT INTO `post_likes` (`id`,`post_id`,`user_id`,`created_at`) VALUES
  (1, 1, 1, '2025-11-20 12:00:01'),
  (2, 1, 2, '2025-11-20 12:00:02'),
  (3, 2, 1, '2025-11-20 12:00:03'),
  (4, 2, 2, '2025-11-20 12:00:04'),
  (5, 3, 1, '2025-11-20 12:00:05'),
  (6, 3, 2, '2025-11-20 12:00:06'),
  (7, 4, 1, '2025-11-20 12:00:07'),
  (8, 4, 2, '2025-11-20 12:00:08'),
  (9, 5, 1, '2025-11-20 12:00:09'),
  (10, 5, 2, '2025-11-20 12:00:00'),
  (11, 6, 1, '2025-11-20 12:00:01'),
  (12, 6, 2, '2025-11-20 12:00:02'),
  (13, 7, 1, '2025-11-20 12:00:03'),
  (14, 7, 2, '2025-11-20 12:00:04'),
  (15, 8, 1, '2025-11-20 12:00:05'),
  (16, 8, 2, '2025-11-20 12:00:06'),
  (17, 9, 1, '2025-11-20 12:00:07'),
  (18, 9, 2, '2025-11-20 12:00:08'),
  (19, 10, 1, '2025-11-20 12:00:09'),
  (20, 10, 2, '2025-11-20 12:00:00');

-- =========================================
-- Demo comments (also just some activity)
-- =========================================

INSERT INTO `post_comments` (`id`,`post_id`,`user_id`,`comment_text`,`created_at`) VALUES
  (1, 1, 1, 'Nice post #1!', '2025-11-21 14:30:00'),
  (2, 1, 2, 'I agree with this #1.', '2025-11-21 14:35:00'),
  (3, 2, 1, 'Nice post #2!', '2025-11-21 14:30:00'),
  (4, 2, 2, 'I agree with this #2.', '2025-11-21 14:35:00'),
  (5, 3, 1, 'Nice post #3!', '2025-11-21 14:30:00'),
  (6, 3, 2, 'I agree with this #3.', '2025-11-21 14:35:00'),
  (7, 4, 1, 'Nice post #4!', '2025-11-21 14:30:00'),
  (8, 4, 2, 'I agree with this #4.', '2025-11-21 14:35:00'),
  (9, 5, 1, 'Nice post #5!', '2025-11-21 14:30:00'),
  (10, 5, 2, 'I agree with this #5.', '2025-11-21 14:35:00'),
  (11, 6, 1, 'Nice post #6!', '2025-11-21 14:30:00'),
  (12, 6, 2, 'I agree with this #6.', '2025-11-21 14:35:00'),
  (13, 7, 1, 'Nice post #7!', '2025-11-21 14:30:00'),
  (14, 7, 2, 'I agree with this #7.', '2025-11-21 14:35:00'),
  (15, 8, 1, 'Nice post #8!', '2025-11-21 14:30:00'),
  (16, 8, 2, 'I agree with this #8.', '2025-11-21 14:35:00'),
  (17, 9, 1, 'Nice post #9!', '2025-11-21 14:30:00'),
  (18, 9, 2, 'I agree with this #9.', '2025-11-21 14:35:00'),
  (19, 10, 1, 'Nice post #10!', '2025-11-21 14:30:00'),
  (20, 10, 2, 'I agree with this #10.', '2025-11-21 14:35:00');

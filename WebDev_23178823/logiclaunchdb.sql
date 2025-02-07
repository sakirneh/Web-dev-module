-- Group Members:
-- Harriet Rottan 23230096
-- Marius Tudorica 23147436
-- Henrikas Varanauskas 23178823


-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2025 at 09:15 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"; -- 0 can be manually added into auto-increment collumns
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `logiclaunchdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

-- Table includes columns for comment ID, course ID, user ID, coment text, approval status, admin ID, datetime of creation

-- The ID of comments exist so that we can manage comments via ID (approving, deleting)
-- Course ID exists because each comment must be made on course pages
-- User ID exists because a user needs to make the comment
-- comment text is the content of the comment
-- is_approved is for comment approval. this was added as a type of moderation. No offensive comment can be made public.
-- admin ID is for the admin approving the comment
-- created_at allows us to see the time in which the comment was made. this allows users to see history of course opinions.

-- no comment is approved automatically to stop the spread of harmful content and spam

CREATE TABLE `comments` (
  `id` int(11) NOT NULL, -- comment id
  `course_id` int(11) DEFAULT NULL, -- course id that user is commenting on
  `user_id` int(11) DEFAULT NULL, -- id of user commenting
  `comment_text` text DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0, -- unapproved by default
  `approved_by_admin_id` int(11) DEFAULT NULL, -- id of the admin that approved the comment
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `content`
--


-- id for content so that we can manage content if needed
-- course id exists because each content is linked to a course table, and this column tells us which one
-- text is for the actual content information. Several texts were used for CSS styling
-- video URL links to a youtube video related to the course. it provides users more content and is playable in the window. 
-- is_approved is to allow for correct content to be added to the page. This is in place to make sure the content is accurate and appropriate
-- admin ID is for the admin approving the comment
-- created_at allows us to see the time in which the content was made. this allows users to see how up to date the course's content is

-- originally, we included a resource_url column. We decided this part of the db was redundant and removed it. 

CREATE TABLE `content` (
  `id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL, -- id of course content is a part of
  `text_1` text DEFAULT NULL, -- multiple text rows were used to aid in CSS styling
  `text_2` text DEFAULT NULL,
  `text_3` text DEFAULT NULL,
  `text_4` text DEFAULT NULL,
  `text_5` text DEFAULT NULL,
  `text_6` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL, -- url to video
  `is_approved` tinyint(1) DEFAULT 0, -- content is no approved automatically, must be approved by admin
  `approved_by_admin_id` int(11) DEFAULT NULL, -- id of admin approving content
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `content`
--


-- data for the content table for all courses
INSERT INTO `content` (`id`, `course_id`, `text_1`, `text_2`, `text_3`, `text_4`, `text_5`, `text_6`, `video_url`, `is_approved`, `approved_by_admin_id`, `created_at`) VALUES
(1, 1, 'Python, created by Guido van Rossum in 1991, is currently the most widely-used programming language. It can be used for software development, server-side web development, data structures and algorithms, and system scripting. Python is capable of running on many different operating systems or platforms, such as Windows, Linux, Mac, and even Raspberry Pi. ', 'A key feature of Python is its human readability, its design and syntax being based off of the English language. This allows for significantly fewer lines of code than other programming languages. A significant part of Python is its formatting. Other languages may use curly brackets, for example, to define scope during coding, but Python instead uses indentation. Python also neglects the use of semicolons or brackets to finish commands in favour of nothing at all, as all programmers have to do is begin a new line.', 'Another key feature of Python is its versatility, allowing programmers to code using the procedural way, the functional way, or the object-oriented way. Versatility in tandem with its human readability is one of Python’s biggest benefits, allowing multiple ways of tackling the task at hand. In a group project, teams may have diverse backgrounds in regards to programming, but Python’s flexibility means that each of these developers may use techniques they excel at to benefit the project, all within the same programming language.', 'With such a large number of users comes a large array of frameworks and libraries covering many different areas, like web development or data science. The active community surrounding Python ensures constant new content and updates, with access to significant tools such as NumPY, and Pandas.', 'Unlike languages like Java or C++, Python code does not need to be compiled. This allows for faster iteration, and the ability to quickly change and test code. When a program runs and there is an error, Python halts execution at the line of error and provides a traceback so that developers can quickly diagnose and fix potential problems.', '', 'rfscVS0vtbw', 1, 1, '2025-01-19 00:42:49'),
(2, 2, 'In 1972, Dennis Ritchie created the C programming language at Bell Laboratories. It’s what’s known as a ‘general-purpose’ programming language, and remains popular amongst developers despite its age. Its popularity can be attributed to its continued use in relevant systems. For example, C was used to implement the Linux operating system. There are many other systems and software that were written in C, and so long as these systems still exist, C will be relevant. .', 'C is one of the most efficient programming languages and allows for low-level access to memory and system resources. It is often used for programming involving OS kernels and device drivers due to its ability to interact with the hardware. This stems from the fact that C can be compiled directly into machine code (binary). The language is relevant in industries with significant and important uses of machinery or hardware, such as medical devices and aerospace, thanks to C’s reliability and low-level capabilities.', 'C’s influence on modern programming is great, and it’s an educational resource for developers who would like to learn many programming languages. This is because many significant and popular languages, such as Java and Python, were undoubtedly influenced by C and its syntax. The language may not be as human readable as Python, but is instead extremely concise, avoiding complexity. This leads to less lines of code but more functionality. C also benefits from a standardised syntax, meaning that code can be written on one system and it will work similarly on another.', 'Learning C will allow developers to deepen their understanding of computers through interacting with memory and hardware. Memory must be manually handled through the use of pointers, allowing programmers to learn how memory is allocated and accessed. Another significant lesson that comes with programming in C is how to use debuggers. Higher level languages are often more forgiving in regards to errors, having features like garbage collectors, but C has no built-in debugging and developers must manage the debugger themselves. This allows for a better understanding of the code and how it complies, as well as enforcing more careful coding practices.', '', '', 'KJgsSFOSQv0', 1, 1, '2025-01-19 00:53:15'),
(3, 3, 'Created by Bjarne Stroutstrup, C++ was developed to be an extension to the C language in 1979. Similar to C, it gives developers significant control over the system and its hardware, and excels in creating high-performance applications. C++ supports low-level and high-level programming, used in large and complex systems such as video games, and high-performance applications. It is backwards compatible with C, meaning libraries and features from C can be used while incorporating modern features.', 'C++ introduced object oriented programming to C, a feature that was not originally built into the C language. Now, developers could create objects and use significant programming features such as inheritance and encapsulation. These additions allowed for much bigger systems to be created much easier , whereas C focuses on procedural programming and functional methods of developing.', 'The language compiles straight to machine code, meaning execution happens much faster. C++ also allows for interaction with memory and system hardware, making it a useful language for performance critical applications. With this and its compatibility with object oriented programming, C++ is a very powerful language with widespread use across the tech world. The language is also consistently updated, with its most recent update being in 2023.  Another great benefit of C++ is its cross-platform nature, running on Windows, MacOS, Linux, and many others. This results in C++ being a great language to use for portable applications.', 'The community for C++ is an active one, and due to its decades of use has seen a vast amount of libraries and frameworks for developers to implement. C++ already has a great Standard Template Library, including features for maps and algorithms, but there are also libraries and frameworks made outside of the STL such as Boost libraries that include tools for file systems, smart pointers, and much more. The community surrounding the language also allows for decades of documentation and tutorials, making it more beginner friendly than more obscure languages.', '', '', 'vLnPwxZdW4Y', 1, 1, '2025-01-19 01:06:45'),
(4, 4, 'Figma is a vector-based design tool that runs in the browser and is used for a multitude of different creative projects. For this course, Figma will be utilised for website design.', 'Web design allows developers to create a visual guidance for their web application or software before any programming. Knowing the colour scheme, layout and general look of a website is an important step in any sort of software development. Developers can decide what designs are the most accessible and user friendly through multiple plans and iterations. Using a tool like Figma makes this process significantly easier, as experimenting with web design through programming is much more tedious and time consuming. Visualising ideas before implementing them provides a clear vision of what must then be done through programming and doesn’t enforce the same technical constraints that experimenting through code.', 'Being a vector-based design tool means that Figma allows for the easy scalability of your designs. Vectors are resolution independent, meaning designers won’t have to worry about resolution while designing, unlike pixel-based design tools that have a fixed dimension. Figma also benefits in its feature allowing for real-time collaboration. Figma files can be accessed by several people, allowing for immediate feedback and a lack of miscommunication.', 'Figma is a cloud-based system, allowing users to access their designs from any devices they own without needing to download anything. It is also not operating system specific, allowing for use across many systems, including Linux. Learners benefit from the multitude of libraries and plugins that Figma provides, allowing you to branch out and add more features if you would prefer to design your website further than what the base tools of Figma provide. The active community surrounding Figma is always creating new plugins and resources, often for free, for others to use.', 'Those interested in web design can learn a lot from the use of Figma alone, but LogicLaunch’s course will offer key lessons and industry tips to get learners started.', '', 'ztHopE5Wnpc', 1, 1, '2025-01-19 01:14:34'),
(5, 5, 'Databases are a significant part of the technological world, found in social media, shopping sites, search engines and so many more places. SQL (Structured Query Language) was created in 1970 by Raymond Boyce and Donald Chamberlin, and is the most common and widely used way to interact with databases. A Relational Database Management System (RDBMS) is required for SQL to be able to interact with a database, with data stored into tables with columns and rows.', 'There are four methods that can be used to interact with data in a database, being Create, Read, Update, and Delete. ‘CRUD’ for short. Through SQL, programmers can create data through INSERT, read data through SELECT, update data through UPDATE, and delete data using DELETE. Almost every interactive software relies on CRUD to handle human input and display data, making SQL a relevant and important language for developers to learn due to its relevance in the modern technological world.', 'SQL’s syntax is straightforward and human readable, making it accessible for new learners. Despite its simplicity, SQL can handle complex queries, such as joining tables based on specific requirements, as well as simple data retrieval. SQL offers good versatility for database management, and it can be used for a variety of use cases like filtering, grouping, and sorting. It can also handle large volumes of data at once, all accessible through short and simple commands.', 'Being an industry standard for database management, it’s often a required skill for certain job fields that developers know SQL, such as back-end development and data analysts. Additionally, SQL’s capacity for cross-platform compatibility means that it can communicate with many different kinds of data systems. This flexibility allows software developers and even companies to maintain and scale their databases effectively.', 'SQL’s simplicity, versatility and relevance makes it a powerful tool for database management systems and a valuable asset for developers in a range of different fields.', '', 'jwCmIBJ8Jtc', 1, 1, '2025-01-19 01:27:20');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

-- ID exists so that courses can be managed by instructors and admins. (create, edit, remove, etc)
-- Title of the course lets users know what the course entails
-- description exists for a more in-depth explanation of the content. Allows users to gauge their interest.
-- instructor_id exists due to them creating the course. They also manage the course.
-- genre allows users to see where the course lies and if it alligns with their education plans.
-- created_at allows users to see when the course was created

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL, -- id of instructor the course belongs to
  `genre` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

-- data for all pre-made courses in the db
INSERT INTO `courses` (`id`, `title`, `description`, `instructor_id`, `genre`, `created_at`) VALUES
(1, 'Python For Beginners', 'Python is the most widely-used and one of the most versatile languages around today. With its number of users only increasing, it’s enlightening for any programmer to know the basics. With this course, you can get started on everything you need to know about Python going forward in your studies!', 2, 'Programming', '2025-01-19 00:28:34'),
(2, 'C For Beginners', 'C is an old but reliable programming language that is still widely used even today. C excels in its ability to communicate directly with the system and its hardware, and is known for its efficiency. With this course, you can get started on everything you need to know about the C programming language going forward in your studies!', 3, 'Programming', '2025-01-19 00:47:29'),
(3, 'C++ For Beginners', 'C++ was made as an extention to C, orignally named ’C with Classes’. C++ holds all the benefits that C does, even the same syntax, but with the added feature of built-in compatibility for object orientated programming. With this course, you can get started on everything you need to know about C++ going forward in your studies!', 4, 'Programming', '2025-01-19 01:03:27'),
(4, 'Figma and Website Design', 'Figma serves as a great design tool for web designers, allowing developers to plan their projects through simple and efficient visualisation tools. With this course, you can get started on everything you need to know about Figma and website design to develop your education!', 2, 'Website Design', '2025-01-19 01:11:24'),
(5, 'SQL And The Implementation Of It In Databases', 'Databases are a fudamental part of interactive websites and software, with SQL being an efficient and widely-used way of interacting with said databases. Through CRUD operations, users can learn how databases work and how to interact with them. With this course, you can get started on everything you need to know about using SQL to interact with databases to further your technical knowledge!', 3, 'Database Design and Development', '2025-01-19 01:23:44');

--
-- Table structure for table `enrollments`
--

-- this table includes a composite key for course_id and user_id so that users can enroll into courses only once.
-- enrolled_at allows users to see when students enroll into a course. instructors get to see interest history.

CREATE TABLE `enrollments` (
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrolled_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

-- id so that users can be managed (profile details)
-- first_name, last_name, email, and phone all exist for user details. this allows for signup and unique identification.
-- role exists to seperate the types of users and their permissions

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL -- allows for 3 types of users in one table rather than having a table for each type
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

-- data for all premade users in the db 

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `role`) VALUES -- users already in the system before 
(1, 'Admin', 'One', 'adminone@gmail.com', 'Admin123', 'Admin'),
(2, 'Jane', 'Jones', 'janejones@gmail.com', 'Jane123', 'Instructor'),
(3, 'John', 'Smith', 'johnsmith@gmail.com', 'John123', 'Instructor'),
(4, 'Linda', 'Davies', 'lindadavies@gmail.com', 'Linda123', 'Instructor');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `approved_by_admin_id` (`approved_by_admin_id`);

--
-- Indexes for table `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `approved_by_admin_id` (`approved_by_admin_id`);

--
-- Indexes for table `courses`
--

-- all contain a primary key for id

ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `instructor_id` (`instructor_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`user_id`,`course_id`), -- composite primary key for user_id and course_id
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--


--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content`
--
ALTER TABLE `content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

-- these constrains show the foreign keys of each table

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`approved_by_admin_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `content`
--
ALTER TABLE `content`
  ADD CONSTRAINT `content_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `content_ibfk_2` FOREIGN KEY (`approved_by_admin_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

COMMIT; -- commit all

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

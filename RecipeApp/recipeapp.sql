-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2025 at 10:35 PM
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
-- Database: `recipeapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Ingredients` text NOT NULL,
  `Instructions` text NOT NULL,
  `Picture` varchar(255) DEFAULT NULL,
  `Cuisine` varchar(100) NOT NULL,
  `User_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`ID`, `Name`, `Ingredients`, `Instructions`, `Picture`, `Cuisine`, `User_id`) VALUES
(1, 'Spaghetti Bolognese', 'Spaghetti, ground beef, tomato sauce, garlic, onion, olive oil, salt, pepper', 'Cook the spaghetti. In a separate pan, sauté garlic and onion in olive oil. Add the ground beef and cook until browned. Add the tomato sauce and let simmer for 15 minutes. Serve the sauce over the pasta.', 'https://images.pexels.com/photos/6287546/pexels-photo-6287546.jpeg?fm=jpg', 'Italian', 1),
(2, 'Chicken Curry', 'Chicken, curry powder, coconut milk, garlic, onion, ginger, tomatoes', 'Sauté onions, garlic, and ginger. Add chicken and cook until browned. Add curry powder, then pour in the coconut milk. Simmer for 20 minutes. Serve with rice.', 'https://www.allrecipes.com/thmb/FL-xnyAllLyHcKdkjUZkotVlHR8=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/46822-indian-chicken-curry-ii-DDMFS-4x3-39160aaa95674ee395b9d4609e3b0988.jpg', 'Indian', 2),
(3, 'Tacos', 'Ground beef, taco seasoning, tortillas, lettuce, cheese, salsa', 'Cook the ground beef with taco seasoning. Warm the tortillas. Assemble tacos with beef, lettuce, cheese, and salsa.', 'https://www.onceuponachef.com/images/2023/08/Beef-Tacos.jpg', 'Mexican', 3),
(4, 'Caesar Salad', 'Romaine lettuce, Caesar dressing, croutons, parmesan cheese', 'Toss the lettuce with Caesar dressing. Add croutons and parmesan cheese.', 'https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg?fm=jpg', 'American', 4),
(5, 'Vegetable Stir Fry', 'Broccoli, carrots, bell peppers, soy sauce, sesame oil, ginger, garlic', 'Stir-fry vegetables in sesame oil with ginger and garlic. Add soy sauce and cook until tender.', 'https://images.pexels.com/photos/302680/pexels-photo-302680.jpeg?fm=jpg', 'Chinese', 5),
(6, 'Pizza Margherita', 'Pizza dough, mozzarella, tomatoes, basil, olive oil', 'Roll out the pizza dough. Top with mozzarella, sliced tomatoes, and fresh basil. Drizzle with olive oil. Bake in the oven.', 'https://images.pexels.com/photos/4109084/pexels-photo-4109084.jpeg?fm=jpg', 'Italian', 1),
(7, 'Beef Stew', 'Beef, potatoes, carrots, onions, garlic, beef broth', 'Brown beef, then add onions and garlic. Add carrots, potatoes, and beef broth. Simmer for 1 hour until tender.', 'https://static01.nyt.com/images/2024/10/28/multimedia/beef-stew-mlfk/beef-stew-mlfk-mediumSquareAt3X.jpg', 'American', 6),
(8, 'Pasta Primavera', 'Pasta, zucchini, bell peppers, cherry tomatoes, olive oil, garlic, parmesan', 'Cook pasta. Sauté zucchini, peppers, and tomatoes in olive oil with garlic. Toss with pasta and top with parmesan.', 'https://images.pexels.com/photos/1279330/pexels-photo-1279330.jpeg?fm=jpg', 'Italian', 7),
(9, 'Chicken Alfredo', 'Chicken, fettuccine, Alfredo sauce, garlic, parmesan, butter', 'Cook chicken and fettuccine. In a pan, melt butter, add garlic, then pour in the Alfredo sauce. Toss with chicken and pasta.', 'https://images.pexels.com/photos/128430/pexels-photo-128430.jpeg?fm=jpg', 'Italian', 8),
(10, 'Fish Tacos', 'Fish fillets, tortillas, cabbage, lime, cilantro, crema', 'Cook fish fillets and break into pieces. Warm tortillas, then assemble tacos with fish, cabbage, cilantro, and crema.', 'https://images.pexels.com/photos/461198/pexels-photo-461198.jpeg?fm=jpg', 'Mexican', 9),
(11, 'Vegetable Soup', 'Carrots, celery, onions, potatoes, tomatoes, vegetable broth', 'Sauté onions, carrots, and celery. Add potatoes, tomatoes, and vegetable broth. Simmer for 30 minutes.', 'https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg?fm=jpg', 'American', 10),
(12, 'Chocolate Cake', 'Flour, sugar, cocoa powder, eggs, milk, butter, baking powder', 'Mix the dry ingredients. Add eggs, milk, and butter. Pour into a pan and bake at 350°F for 25 minutes.', 'https://images.pexels.com/photos/45202/chocolate-cake-dessert-food-45202.jpeg?fm=jpg', 'American', 11),
(13, 'Lentil Soup', 'Lentils, carrots, celery, onions, garlic, vegetable broth', 'Cook lentils with onions, garlic, carrots, and celery in vegetable broth. Simmer for 40 minutes.', 'https://images.pexels.com/photos/164077/lentils-dal.jpg?fm=jpg', 'Indian', 12),
(14, 'Grilled Cheese', 'Bread, butter, cheese', 'Butter the bread and grill with cheese in between until golden and melted.', 'https://images.pexels.com/photos/160071/pexels-photo-160071.jpeg?fm=jpg', 'American', 13),
(15, 'Peking Duck', 'Duck, hoisin sauce, pancakes, cucumber, green onion', 'Roast duck until crispy. Serve with hoisin sauce, pancakes, cucumber, and green onion.', 'https://images.pexels.com/photos/461198/pexels-photo-461198.jpeg?fm=jpg', 'Chinese', 14),
(16, 'Ramen', 'Ramen noodles, soy sauce, miso paste, eggs, scallions', 'Cook noodles. Make a broth with soy sauce and miso paste. Top noodles with boiled eggs and scallions.', 'https://images.pexels.com/photos/1279330/pexels-photo-1279330.jpeg?fm=jpg', 'Japanese', 15),
(19, 'Moussaka', 'Eggplant, ground beef, tomato sauce, béchamel sauce', 'Layer cooked eggplant with seasoned beef and tomato sauce. Top with béchamel and bake.', 'https://images.pexels.com/photos/1437267/pexels-photo-1437267.jpeg?fm=jpg', 'Greek', 18),
(20, 'Paella', 'Rice, saffron, chicken, seafood, peas, bell peppers', 'Cook rice with saffron. In a separate pan, cook chicken, seafood, and vegetables, then combine with rice.', 'https://images.pexels.com/photos/461198/pexels-photo-461198.jpeg?fm=jpg', 'Spanish', 19),
(21, 'Banh Mi', 'Baguette, pork, pickled vegetables, cilantro, mayo, sriracha', 'Fill baguette with cooked pork, pickled vegetables, cilantro, mayo, and sriracha.', 'https://images.pexels.com/photos/461198/pexels-photo-461198.jpeg?fm=jpg', 'Vietnamese', 20),
(22, 'asdasd', 'asdsda', 'asdads', '', 'All', 22),
(23, 'asdasd', 'asdsda', 'asdads', '', 'Moroccan', 22),
(24, 'addsa', 'dsasdad', 'dsad', '', 'All', 22),
(25, 'asddaasdda', 'asdads', 'asdsdad', '', 'All', 22),
(26, 'asda', 'dsda', 'asdasd', '', 'All', 22),
(27, 'cake', 'flour', 'sss', NULL, 'All', 22);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `ID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 0,
  `review_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`ID`, `user_id`, `recipe_id`, `rating`, `review_text`) VALUES
(1, 22, 2, 4, 'wowwwwwww'),
(2, 22, 2, 2, 'not bad');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `Fname` varchar(50) DEFAULT NULL,
  `Lname` varchar(50) DEFAULT NULL,
  `Email` varchar(320) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Pnumber` varchar(15) DEFAULT NULL,
  `username` varchar(32) NOT NULL,
  `ProfilePic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `Fname`, `Lname`, `Email`, `Password`, `Pnumber`, `username`, `ProfilePic`) VALUES
(1, 'John', 'Doe', 'john.doe@example.com', 'password123', '555-1234', 'johndoe', 'uploads/profile_pics/john_profile_pic.jpg'),
(2, 'Jane', 'Smith', 'jane.smith@example.com', 'securepass456', '555-5678', 'janesmith', 'uploads/profile_pics/jane_profile_pic.jpg'),
(3, 'Alice', 'Johnson', 'alice.johnson@example.com', 'mypassword789', '555-9876', 'alicej', 'uploads/profile_pics/alice_profile_pic.jpg'),
(4, 'Bob', 'Williams', 'bob.williams@example.com', 'pass1234', '555-6543', 'bobbyw', NULL),
(5, 'Charlie', 'Brown', 'charlie.brown@example.com', 'charliepass', '555-4321', 'charlie_b', 'uploads/profile_pics/charlie_profile_pic.jpg'),
(6, 'David', 'Taylor', 'david.taylor@example.com', 'taylorpass789', '555-5670', 'davidt', 'uploads/profile_pics/david_profile_pic.jpg'),
(7, 'Eva', 'Green', 'eva.green@example.com', 'evaSecure123', '555-9870', 'evagreen', NULL),
(8, 'Frank', 'Harris', 'frank.harris@example.com', 'password111', '555-4325', 'frankh', 'uploads/profile_pics/frank_profile_pic.jpg'),
(9, 'Grace', 'Lewis', 'grace.lewis@example.com', 'lewispassword22', '555-6540', 'gracel', 'uploads/profile_pics/grace_profile_pic.jpg'),
(10, 'Henry', 'Walker', 'henry.walker@example.com', 'walkpass456', '555-8765', 'henryw', NULL),
(11, 'Isla', 'Davis', 'isla.davis@example.com', 'davisPassword789', '555-3456', 'islad', 'uploads/profile_pics/isla_profile_pic.jpg'),
(12, 'Jack', 'Martinez', 'jack.martinez@example.com', 'jackPassword123', '555-2389', 'jackm', NULL),
(13, 'Kara', 'Roberts', 'kara.roberts@example.com', 'karasecurepass', '555-2345', 'karar', 'uploads/profile_pics/kara_profile_pic.jpg'),
(14, 'Liam', 'King', 'liam.king@example.com', 'kingPassword123', '555-7890', 'liamk', 'uploads/profile_pics/liam_profile_pic.jpg'),
(15, 'Mia', 'Scott', 'mia.scott@example.com', 'scottpass456', '555-5679', 'miascott', NULL),
(16, 'Noah', 'Allen', 'noah.allen@example.com', 'noahpass123', '555-6547', 'noahallen', 'uploads/profile_pics/noah_profile_pic.jpg'),
(17, 'Olivia', 'Martinez', 'olivia.martinez@example.com', 'olivia1234', '555-7891', 'oliviamart', 'uploads/profile_pics/olivia_profile_pic.jpg'),
(18, 'Peter', 'Davis', 'peter.davis@example.com', 'peterdpassword', '555-5432', 'peterd', NULL),
(19, 'Quinn', 'Evans', 'quinn.evans@example.com', 'evanspass678', '555-9874', 'quinne', 'uploads/profile_pics/quinn_profile_pic.jpg'),
(20, 'Rachel', 'Robinson', 'rachel.robinson@example.com', 'rachelpassword9', '555-2346', 'rachelrobin', 'uploads/profile_pics/rachel_profile_pic.jpg'),
(21, NULL, NULL, 'antonio.karam@lau.edu', '$2y$10$yehdjtDjMAMp6hNeWV9J3eKefhd3QdJpFqKQzINu9j21mGl3lUUxC', NULL, 're-red0', NULL),
(22, 'asdas', 'null', '123', '$2y$10$T1KpNAcCV13v8LkmDGmSauatlNRGoWvWswOXCxPZGRpG7.4lb6t/i', '8181818', '123', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `User_id` (`User_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`User_id`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`ID`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` varchar(255) NOT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `subscription` varchar(50) DEFAULT NULL,
  `address` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `account_type` int NOT NULL,
  `user_key` int NOT NULL,
  `img_profile` varchar(100) NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `date_inserted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `token` text,
  `phone_number` varchar(10) NOT NULL,
  `interest` text NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `specialty` varchar(50) DEFAULT NULL,
  `experience` int DEFAULT NULL,
  `is_provider` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
);

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
INSERT INTO `users` VALUES ('64a89e458c6bb','jemeurs','defaim','b@gmail.com','free','13 rue de champmotteux','$2y$10$t/GvXy.67P9BWiuZkCx2duXCZXWEaZw9J9d8QUSWBp.28w3qKV/fq',0,290,'default.png',0,'2023-07-08 01:22:45',NULL,'','2146338023','[\"Healthy living\",\"Vegetarian\"]','azerytrefz','none',0,0),('64a8a10a083dc','Gabriel','Pivaty','pivatygabriel@gmail.com','free','456 rue ecole','$2y$10$4hC8Q7UDV66k18QL.0BioelzggeDRkIV.2oN2Optwvx.p4psd.Vb2',0,0,'default.png',0,'2023-07-08 01:34:34','2023-07-08 16:55:06','','1239871211','[\"Healthy living\",\"Vegetarian\"]','Gab','none',0,1),('64a8a19e7fac4','Marceau','Kakou','kakou@gmail.com','free','13 rue de champmotteux','$2y$10$nUjmyiVAhVErPUqxPOFRXesnVTYQIn2mUUJA80DLE50PDZR29BG7e',3,1,'default.png',1,'2023-07-08 01:37:02','2023-07-08 14:16:49','','1239871214','[\"Healthy living\",\"Discover\"]','Yvann','none',0,1),('64a9e3d416568','doe','john','john@gmail.com','free','123 main street','$2y$10$1bQnKHBdNMMQtM4xKTHzhenRlYLxJhcunz34dioESM9f9pToI6mJ.',0,414,'default.png',0,'2023-07-09 00:31:48',NULL,'','1234567891','[\"Discover\"]','john','none',0,0);
UNLOCK TABLES;

--
-- Table structure for table `providers`
--

DROP TABLE IF EXISTS `providers`;
CREATE TABLE `providers` (
  `id` char(15) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `specialty` varchar(50) DEFAULT NULL,
  `experience` int DEFAULT NULL,
  `user_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `providers_fk` (`user_id`),
  CONSTRAINT `providers_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
);


--
-- Dumping data for table `providers`
--

LOCK TABLES `providers` WRITE;
INSERT INTO `providers` VALUES ('pr-48c34db29fb4','Pivaty','Gabriel','Grillades',2,'64a8a10a083dc');
UNLOCK TABLES;



DROP TABLE IF EXISTS `chats`;
CREATE TABLE `chats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` varchar(255) NOT NULL,
  `receiver_id` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_by_sender` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_by_receiver` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  CONSTRAINT `chats_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  CONSTRAINT `chats_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`)
);


--
-- Dumping data for table `chats`
--

LOCK TABLES `chats` WRITE;
UNLOCK TABLES;

--
-- Table structure for table `formations`
--

DROP TABLE IF EXISTS `formations`;
CREATE TABLE `formations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `id_provider` char(15) NOT NULL,
  `img` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_provider` (`id_provider`),
  CONSTRAINT `formations_ibfk_1` FOREIGN KEY (`id_provider`) REFERENCES `providers` (`id`)
);

--
-- Dumping data for table `formations`
--

LOCK TABLES `formations` WRITE;
INSERT INTO `formations` VALUES (1,'Sushi','Comment faire des sushi','pr-48c34db29fb4','path/to/img');
UNLOCK TABLES;


--
-- Table structure for table `recipes`
--

DROP TABLE IF EXISTS `recipes`;
CREATE TABLE `recipes` (
  `id` char(15) NOT NULL,
  `name` varchar(155) DEFAULT NULL,
  `description` text,
  `serves` int DEFAULT NULL,
  `prep_time` int DEFAULT NULL,
  `creator` char(15) DEFAULT NULL,
  `img_md` varchar(255) DEFAULT NULL,
  `img_sm` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `status` enum('free','starter','premium') NOT NULL DEFAULT 'free',
  PRIMARY KEY (`id`),
  KEY `creator` (`creator`),
  CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`creator`) REFERENCES `providers` (`id`)
);

--
-- Dumping data for table `recipes`
--

LOCK TABLES `recipes` WRITE;
INSERT INTO `recipes` VALUES ('rc-64aaaafe4fbb','Chicken Stir-Fry','A quick and flavorful chicken stir-fry with vegetables.',2,30,'pr-48c34db29fb4','img_url_md','img_url_sm','video_url','free'),('rc-64aaab73ada6','Pasta Carbonara','A classic Italian pasta dish with a creamy bacon and egg sauce.',4,20,'pr-48c34db29fb4','img_url_md','img_url_sm','video_url','free');
UNLOCK TABLES;


--
-- Table structure for table `ingredients`
--

DROP TABLE IF EXISTS `ingredients`;
CREATE TABLE `ingredients` (
  `id` char(15) NOT NULL,
  `name` varchar(155) DEFAULT NULL,
  `fats` decimal(5,2) DEFAULT NULL,
  `proteins` decimal(5,2) DEFAULT NULL,
  `carbs` decimal(5,2) DEFAULT NULL,
  `Kcal` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ingredients_name_unique` (`name`)
);

--
-- Dumping data for table `ingredients`
--

LOCK TABLES `ingredients` WRITE;
INSERT INTO `ingredients` VALUES ('ing-64aaaafe508','chicken breast',NULL,NULL,NULL,NULL),('ing-64aaaafe50b','bell pepper',NULL,NULL,NULL,NULL),('ing-64aaaafe50d','carrot',NULL,NULL,NULL,NULL),('ing-64aaaafe510','broccoli florets',NULL,NULL,NULL,NULL),('ing-64aaaafe518','soy sauce',NULL,NULL,NULL,NULL),('ing-64aaaafe51b','oyster sauce',NULL,NULL,NULL,NULL),('ing-64aaaafe51d','cornstarch',NULL,NULL,NULL,NULL),('ing-64aaaafe51e','vegetable oil',NULL,NULL,NULL,NULL),('ing-64aaaafe520','salt',NULL,NULL,NULL,NULL),('ing-64aaaafe522','black pepper',NULL,NULL,NULL,NULL),('ing-64aaab73ae7','spaghetti',NULL,NULL,NULL,NULL),('ing-64aaab73aea','bacon',NULL,NULL,NULL,NULL),('ing-64aaab73aec','eggs',NULL,NULL,NULL,NULL),('ing-64aaab73aee','grated Parmesan cheese',NULL,NULL,NULL,NULL);
UNLOCK TABLES;

--
-- Table structure for table `formations_recipes`
--

DROP TABLE IF EXISTS `formations_recipes`;
CREATE TABLE `formations_recipes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `formation_id` int NOT NULL,
  `recipe_id` char(15) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_formation_recipe` (`formation_id`,`recipe_id`),
  KEY `formation_id` (`formation_id`),
  KEY `recipe_id` (`recipe_id`),
  CONSTRAINT `formations_recipes_ibfk_1` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`),
  CONSTRAINT `formations_recipes_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`)
);

--
-- Dumping data for table `formations_recipes`
--

LOCK TABLES `formations_recipes` WRITE;
INSERT INTO `formations_recipes` VALUES (11,1,'rc-64aaab73ada6');
UNLOCK TABLES;


--
-- Table structure for table `recipe_steps`
--

DROP TABLE IF EXISTS `recipe_steps`;
CREATE TABLE `recipe_steps` (
  `step_id` char(15) NOT NULL,
  `recipe_id` char(15) DEFAULT NULL,
  `step_number` int DEFAULT NULL,
  `instruction` text,
  PRIMARY KEY (`step_id`),
  KEY `recipe_id` (`recipe_id`),
  CONSTRAINT `recipe_steps_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`)
);

--
-- Dumping data for table `recipe_steps`
--

LOCK TABLES `recipe_steps` WRITE;
INSERT INTO `recipe_steps` VALUES ('stp-64aaaafe540','rc-64aaaafe4fbb',1,'Slice the chicken into thin strips.'),('stp-64aaaafe543','rc-64aaaafe4fbb',2,'In a wok or large skillet, heat oil over high heat.'),('stp-64aaaafe544','rc-64aaaafe4fbb',3,'Add the chicken strips and stir-fry until cooked through.'),('stp-64aaaafe547','rc-64aaaafe4fbb',4,'Remove the chicken from the wok and set aside.'),('stp-64aaaafe548','rc-64aaaafe4fbb',5,'Add the sliced vegetables to the wok and stir-fry until crisp-tender.'),('stp-64aaaafe54a','rc-64aaaafe4fbb',6,'Return the chicken to the wok and toss with the vegetables.'),('stp-64aaaafe54b','rc-64aaaafe4fbb',7,'In a small bowl, whisk together the soy sauce, oyster sauce, and cornstarch.'),('stp-64aaaafe54c','rc-64aaaafe4fbb',8,'Pour the sauce over the chicken and vegetables. Stir until well-coated and heated through.'),('stp-64aaaafe54e','rc-64aaaafe4fbb',9,'Serve hot with steamed rice.'),('stp-64aaab73afb','rc-64aaab73ada6',1,'Cook the pasta in a large pot of boiling salted water until al dente.'),('stp-64aaab73afd','rc-64aaab73ada6',2,'While the pasta is cooking, cook the bacon in a large skillet until crispy.'),('stp-64aaab73afe','rc-64aaab73ada6',3,'In a bowl, whisk together the eggs, grated Parmesan cheese, and black pepper.'),('stp-64aaab73b00','rc-64aaab73ada6',4,'Drain the cooked pasta and add it to the skillet with the bacon.'),('stp-64aaab73b01','rc-64aaab73ada6',5,'Pour the egg mixture over the hot pasta and toss quickly to coat the pasta.'),('stp-64aaab73b03','rc-64aaab73ada6',6,'The heat from the pasta will cook the eggs and create a creamy sauce.'),('stp-64aaab73b05','rc-64aaab73ada6',7,'Serve immediately with extra grated Parmesan cheese on top.');
UNLOCK TABLES;

--
-- Table structure for table `recipes_ingredients`
--

DROP TABLE IF EXISTS `recipes_ingredients`;
CREATE TABLE `recipes_ingredients` (
  `recipe_id` char(15) NOT NULL,
  `ingredient_id` char(15) NOT NULL,
  `quantity` decimal(5,2) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`recipe_id`,`ingredient_id`),
  KEY `ingredient_id` (`ingredient_id`),
  CONSTRAINT `recipes_ingredients_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`),
  CONSTRAINT `recipes_ingredients_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`)
);

--
-- Dumping data for table `recipes_ingredients`
--

LOCK TABLES `recipes_ingredients` WRITE;
INSERT INTO `recipes_ingredients` VALUES ('rc-64aaaafe4fbb','ing-64aaaafe508',2.00,'pcs'),('rc-64aaaafe4fbb','ing-64aaaafe50b',1.00,'pcs'),('rc-64aaaafe4fbb','ing-64aaaafe50d',1.00,'pcs'),('rc-64aaaafe4fbb','ing-64aaaafe510',1.00,'cup'),('rc-64aaaafe4fbb','ing-64aaaafe518',2.00,'tbsp'),('rc-64aaaafe4fbb','ing-64aaaafe51b',2.00,'tbsp'),('rc-64aaaafe4fbb','ing-64aaaafe51d',1.00,'tbsp'),('rc-64aaaafe4fbb','ing-64aaaafe51e',2.00,'tbsp'),('rc-64aaaafe4fbb','ing-64aaaafe520',1.00,'tsp'),('rc-64aaaafe4fbb','ing-64aaaafe522',1.00,'tsp'),('rc-64aaab73ada6','ing-64aaaafe520',1.00,'tsp'),('rc-64aaab73ada6','ing-64aaaafe522',1.00,'tsp'),('rc-64aaab73ada6','ing-64aaab73ae7',8.00,'oz'),('rc-64aaab73ada6','ing-64aaab73aea',4.00,'slices'),('rc-64aaab73ada6','ing-64aaab73aec',3.00,'pcs'),('rc-64aaab73ada6','ing-64aaab73aee',1.00,'cup');
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type_event` varchar(50) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `id_client` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `events_ibfk_1` (`id_client`)
);

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE `reservations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_reservation` date DEFAULT NULL,
  `id_event` int DEFAULT NULL,
  `id_client` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_event` (`id_event`),
  KEY `reservations_ibfk_2` (`id_client`)
);

--
-- Dumping data for table `reservations`
--
LOCK TABLES `reservations` WRITE;
UNLOCK TABLES;


--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `id` int NOT NULL,
  `name_room` varchar(50) DEFAULT NULL,
  `capacity` int DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `district` int DEFAULT NULL,
  PRIMARY KEY (`id`)
);

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
UNLOCK TABLES;


--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` int NOT NULL,
  `type_service` varchar(50) DEFAULT NULL,
  `price` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int NOT NULL,
  `date_order` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `id_client` varchar(255) DEFAULT NULL,
  `id_service` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_service` (`id_service`),
  KEY `orders_ibfk_1` (`id_client`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`id_service`) REFERENCES `services` (`id`)
);

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE `subscriptions` (
  `id` int NOT NULL,
  `type_subscription` varchar(50) DEFAULT NULL,
  `price` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
UNLOCK TABLES;

--
-- Table structure for table `workshops`
--

DROP TABLE IF EXISTS `workshops`;
CREATE TABLE `workshops` (
  `id` char(15) NOT NULL,
  `name_workshop` varchar(50) DEFAULT NULL,
  `description` text,
  `id_room` int DEFAULT NULL,
  `id_provider` char(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_room` (`id_room`),
  KEY `workshops_ibfk_2` (`id_provider`),
  CONSTRAINT `workshops_ibfk_1` FOREIGN KEY (`id_room`) REFERENCES `rooms` (`id`),
  CONSTRAINT `workshops_ibfk_2` FOREIGN KEY (`id_provider`) REFERENCES `providers` (`id`)
);

--
-- Dumping data for table `workshops`
--

LOCK TABLES `workshops` WRITE;
UNLOCK TABLES;

--
DROP TABLE IF EXISTS `user_services`;
CREATE TABLE `user_services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL,
  `service_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `user_services_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `user_subscriptions`
--

DROP TABLE IF EXISTS `user_subscriptions`;
CREATE TABLE `user_subscriptions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
);

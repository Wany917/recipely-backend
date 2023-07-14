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

LOCK TABLES `users` WRITE;
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

DROP TRIGGER IF EXISTS before_insert_providers;
CREATE TRIGGER before_insert_providers
BEFORE INSERT ON providers
FOR EACH ROW
BEGIN
  SET NEW.id = CONCAT('pr-', SUBSTRING(UUID(), 1, 11));
END;



LOCK TABLES `providers` WRITE;
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

LOCK TABLES `chats` WRITE;
UNLOCK TABLES;


--
-- Table structure for table `formations`
--

DROP TABLE IF EXISTS `formations`;
CREATE TABLE `formations` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `id_provider` char(15) NOT NULL,
  `img` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_provider` (`id_provider`),
  CONSTRAINT `formations_ibfk_1` FOREIGN KEY (`id_provider`) REFERENCES `providers` (`id`)
);

LOCK TABLES `formations` WRITE;
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
  `status` enum('free','starter','master') NOT NULL DEFAULT 'free',
  PRIMARY KEY (`id`),
  KEY `creator` (`creator`),
  CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`creator`) REFERENCES `providers` (`id`)
);

LOCK TABLES `recipes` WRITE;
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
  PRIMARY KEY (`id`)
);

LOCK TABLES `ingredients` WRITE;
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

LOCK TABLES `formations_recipes` WRITE;
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

LOCK TABLES `recipes_ingredients` WRITE;
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
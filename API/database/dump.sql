
-- Les infos utilisateurs
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
  `token` varchar(40) DEFAULT NULL,
  `phone_number` varchar(10) NOT NULL,
  `interest` text NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `specialty` varchar(50) DEFAULT NULL,
  `experience` int DEFAULT NULL,
  `is_provider` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
);

LOCK TABLES `users` WRITE;
INSERT INTO `users` VALUES ('1a2b3c4d','aa','Doe','john.doe@example.com','Premium','123 Main St, New York, NY','password_hash',1,123456,'img_profile1.jpg',1,'2023-05-28 22:10:31','2023-06-03 15:53:38','token1','1234567890','cooking,reading','john_doe',NULL,NULL,0),('5e6f7g8h','Jane','Doe','jane.doe@example.com','Standard','456 Secondary St, New York, NY','password_hash',2,789012,'img_profile2.jpg',1,'2023-05-28 22:10:31','2023-05-28 22:10:31','token2','0987654321','hiking,baking','jane_doe',NULL,NULL,0),('647497fa52fb9','Kakou','Yvann','helloWos@gmail.com',NULL,NULL,'$2y$10$gYDfzbNSNBhVcqouhvHe6eIKI7YBq7fYDXKt1RDobvp9zgiYJLvuS',1,1,'default.png',0,'2023-05-29 14:18:02',NULL,NULL,'123456789','Array','Flackooo',NULL,NULL,0),('64749a31a6b47','Marceau','Kakou','yvannkakou03@gmail.com',NULL,NULL,'$2y$10$aiS8Ii.pvQiE12AiGwn7oeXvwaeH3Gn9CTiQWwU6xqaDOUdwuDu9C',2,1,'default.png',0,'2023-05-29 14:27:29',NULL,NULL,'123456789','Array','Ewii',NULL,NULL,0),('647b3bee7db1b','yvann','ouistit','kakou30@gmail.com',NULL,NULL,'$2y$10$7RcfM0PY/ptRY08txrqZPeBMsIboxQQg3pPjLy0CS2UBVFFo.PJNO',1,1,'default.png',0,'2023-06-03 15:11:10',NULL,NULL,'123456789','Array','Ewii',NULL,NULL,0),('647b7a4c5b73e','Jon','Snow','jon.snow@gmail.com',NULL,NULL,'$2y$10$V4Tzs.YeGMtZb5gHvddQMu7FB60i09uAsw/WkM5vbMie8rmyMq7H6',1,0,'default.png',0,'2023-06-03 19:37:16',NULL,NULL,'123456789','Array','oui',NULL,NULL,0),('647b7a675882a','Jon','kakou','jonsnow@gmail.com',NULL,NULL,'$2y$10$ncMBnirGgc745ZX3ZbejRu/nxPEzKxVl8yz.o.8LWNQj1YWvx5Pkq',1,0,'default.png',0,'2023-06-03 19:37:43',NULL,NULL,'123456780','Array','oui',NULL,NULL,0),('647b7aa4206d3','Jon','kakou','azeerttfr@gmail.com',NULL,NULL,'$2y$10$DdnukTkUvxBQj0TOa.UZxOvQSpAp1kekoA0XTjGDmnboTsqR9g2ku',1,0,'default.png',0,'2023-06-03 19:38:44',NULL,NULL,'123456780','Array','oui',NULL,NULL,0),('647b84219af57','le','reufton','azee@gmail.com',NULL,NULL,'$2y$10$CiIPThYiTzGXJB7WiOjXvO4e1sG2qXFrmZd01432nBwKqllxSkC1K',1,0,'default.png',0,'2023-06-03 20:19:13','2023-06-03 22:38:22',NULL,'122456780','Array','anderson',NULL,NULL,1);
UNLOCK TABLES;


-- Gestiion d'un chat privé
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
) ;

LOCK TABLES `chats` WRITE;
UNLOCK TABLES;


-- Gestion d'un evenements plusieur évènements peuvent être crée assoicé à 
-- client/provider cette evènement peut être dans un locale ou chez le client.
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
  PRIMARY KEY (`id`),
  KEY `creator` (`creator`),
  CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`creator`) REFERENCES `providers` (`id`)
);

LOCK TABLES `recipes` WRITE;
UNLOCK TABLES;

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


DROP TABLE IF EXISTS `recipe_steps`;
CREATE TABLE `recipe_steps` (
  `step_id` char(15) NOT NULL,
  `recipe_id` char(15) DEFAULT NULL,
  `step_number` int DEFAULT NULL,
  `instruction` text,
  PRIMARY KEY (`step_id`),
  KEY `recipe_id` (`recipe_id`),
  CONSTRAINT `recipe_steps_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`)
) ;
LOCK TABLES `recipe_steps` WRITE;
UNLOCK TABLES;

DROP TABLE IF EXISTS `recipes_ingredients`;
CREATE TABLE `recipes_ingredients` (
  `recipe_id` char(15) NOT NULL,
  `ingredient_id` char(15) NOT NULL,
  `quantite` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`recipe_id`,`ingredient_id`),
  KEY `ingredient_id` (`ingredient_id`),
  CONSTRAINT `recipes_ingredients_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`),
  CONSTRAINT `recipes_ingredients_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`)
);

LOCK TABLES `recipes_ingredients` WRITE;
UNLOCK TABLES;


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
) ;


LOCK TABLES `providers` WRITE;
INSERT INTO `providers` VALUES ('d35b3f48-0252-1','reufton','le','feu',21,'647b84219af57');
UNLOCK TABLES;
DELIMITER ;;
DELIMITER ;



DROP TABLE IF EXISTS `reservations`;
CREATE TABLE `reservations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_reservation` date DEFAULT NULL,
  `id_event` int DEFAULT NULL,
  `id_client` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_event` (`id_event`),
  KEY `reservations_ibfk_2` (`id_client`)
) ;

LOCK TABLES `reservations` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `id` int NOT NULL,
  `name_room` varchar(50) DEFAULT NULL,
  `capacity` int DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `district` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ;


LOCK TABLES `rooms` WRITE;
INSERT INTO `rooms` VALUES (647,'Lorem ipsum',12,'13 rue de champmotteux',75001),(64788,'crampter',22,'11 rue de champmotteux',75010);
UNLOCK TABLES;


-- Gestion des services en gros c'est comme les soubscription
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` int NOT NULL,
  `type_service` varchar(50) DEFAULT NULL,
  `price` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ;

LOCK TABLES `services` WRITE;
UNLOCK TABLES;

DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE `subscriptions` (
  `id` int NOT NULL,
  `type_subscription` varchar(50) DEFAULT NULL,
  `price` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

LOCK TABLES `subscriptions` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int NOT NULL,
  `date_order` date DEFAULT NULL,
  `id_client` varchar(255) DEFAULT NULL,
  `id_service` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_service` (`id_service`),
  KEY `orders_ibfk_1` (`id_client`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`id_service`) REFERENCES `services` (`id`)
) ;

LOCK TABLES `orders` WRITE;
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
) ;


LOCK TABLES `workshops` WRITE;
INSERT INTO `workshops` VALUES ('ws-647c44faa433','Test','oui',64788,'d35b3f48-0252-1'),('ws-647c479c5b83','Titleee','Degustation Chez-Antoine',647,'d35b3f48-0252-1');
UNLOCK TABLES;


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

COMMIT;
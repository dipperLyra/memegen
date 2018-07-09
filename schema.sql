-- MySQL Distrib 5.7.22, for ubuntu-linux-gnu (x64)
--
-- Host: localhost    Database: cardbuilder
-- ------------------------------------------------------
-- Server version	5.7.22-0ubuntu18.04.1

--
-- Table schema for `image`
--

DROP TABLE IF EXISTS `image`;
CREATE TABLE `image` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `file_name` VARCHAR (45) NOT NULL,
  `file_path` VARCHAR (45),
  PRIMARY KEY (id)
);

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
CREATE TABLE `content` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `header` VARCHAR (255) NOT NULL,
  `body` VARCHAR (255),
  `footer` VARCHAR (255),
  `image_id` INT,
  FOREIGN KEY (image_id) REFERENCES image (id),
  PRIMARY KEY (id)
);




DROP TABLE `items`;
DROP TABLE `books`;
DROP TABLE `users`;
DROP TABLE `borrowings`;
DROP TABLE `home_views`;
DROP TABLE `visitors`;

-- items table
CREATE TABLE `items` (
  `id` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `type` VARCHAR(30) NOT NULL,
  `quantity` INT(11) NOT NULL DEFAULT 1
);

-- books table
CREATE TABLE `books` (
  `item_id` INT(11) NOT NULL PRIMARY KEY,
  `title` VARCHAR(40) NOT NULL,
  `author` VARCHAR(40) NOT NULL,
  `genre` VARCHAR(30) NOT NULL
);

ALTER TABLE `books`
ADD CONSTRAINT `books_fk` FOREIGN KEY (`item_id`) REFERENCES `items` (id) ON DELETE CASCADE;

-- users table
CREATE TABLE `users` (
  `id` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `email` VARCHAR(40) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `roles` VARCHAR(50) DEFAULT NULL,
  `activation_code` VARCHAR(64) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0
);

-- borrowings table
CREATE TABLE `borrowings` (
  `id` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `item_id` INT(11) NOT NULL,
  `created_at` DATE NOT NULL DEFAULT current_timestamp(),
  `due_date` DATE NOT NULL DEFAULT current_timestamp(),
  `returned` tinyint(1) NOT NULL DEFAULT 0
);

ALTER TABLE `borrowings`
ADD CONSTRAINT `borrowings_fk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (id) ON DELETE CASCADE;

ALTER TABLE `borrowings`
ADD CONSTRAINT `borrowings_fk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (id) ON DELETE CASCADE;

-- home_views table
CREATE TABLE `home_views` (
  `id` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `timestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp()
);

-- visitors table
CREATE TABLE `visitors` (
  `ip_address` VARCHAR(20) NOT NULL PRIMARY KEY,
  `timestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp()
);
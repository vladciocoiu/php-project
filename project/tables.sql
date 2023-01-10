-- items table
CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `type` varchar(30) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
)

ALTER TABLE `items`
ADD PRIMARY KEY (`id`);

ALTER TABLE `items`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- books table
CREATE TABLE `books` (
  `item_id` int(11) NOT NULL,
  `title` varchar(40) NOT NULL,
  `author` varchar(40) NOT NULL,
  `genre` varchar(30) NOT NULL
)

ALTER TABLE `books`
ADD PRIMARY KEY (`item_id`);

ALTER TABLE `books`
ADD CONSTRAINT `books_fk` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

-- users table
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `email` varchar(40) NOT NULL,
  `password` varchar(255) NOT NULL,
  `roles` varchar(50) DEFAULT NULL,
  `activation_code` varchar(64) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0
)

ALTER TABLE `users`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- borrowings table
CREATE TABLE `borrowings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  `due_date` date NOT NULL DEFAULT current_timestamp(),
  `returned` tinyint(1) NOT NULL DEFAULT 0
)

ALTER TABLE `borrowings`
ADD PRIMARY KEY (`id`);

ALTER TABLE `borrowings`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `borrowings`
ADD CONSTRAINT `borrowings_fk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

ALTER TABLE `borrowings`
ADD CONSTRAINT `borrowings_fk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- home_views table
CREATE TABLE `home_views` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
)

ALTER TABLE `home_views`
ADD PRIMARY KEY (`id`);

ALTER TABLE `home_views`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- visitors table
CREATE TABLE `visitors` (
  `ip_address` varchar(20) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
)
START TRANSACTION;

CREATE TABLE `collections` (
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` varchar(256) NOT NULL,
    `access` varchar(256) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
);

CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` varchar(256) NOT NULL UNIQUE,
    `first_name` varchar(256) NOT NULL,
    `last_name` varchar(256) NOT NULL,
    `email` varchar(256) NOT NULL UNIQUE,
    `password` text NOT NULL,
    `password_reset_token` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
);

CREATE TABLE `collection_access` (
    `collection_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `role` varchar(256) NOT NULL,
    UNIQUE KEY `collection_user_id` (`collection_id`, `user_id`),
    FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

CREATE TABLE `stamps` (
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `collection_id` int(11) NOT NULL,
    `name` varchar(256) NOT NULL,
    `used` tinyint(1) NOT NULL,
    `damaged` tinyint(1) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE
);

CREATE TABLE assets (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `collection` VARCHAR(255) NOT NULL,
    `filename` VARCHAR(255) NOT NULL,
    `mimetype` VARCHAR(255) NOT NULL,
    `size` INT NOT NULL,
    `model` VARCHAR(255) NOT NULL,
    `model_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

COMMIT;

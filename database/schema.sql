-- USIU Campus Events Database Schema

CREATE TABLE `users` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `password` VARCHAR(255) DEFAULT NULL,
    `role` ENUM('admin','user') NOT NULL DEFAULT 'user',
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `clubs` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) DEFAULT NULL,
    `description` TEXT,
    `image_url` VARCHAR(255) DEFAULT NULL,
    `contact_info` VARCHAR(255) DEFAULT NULL,
    `founded_year` INT DEFAULT NULL,
    `president_name` VARCHAR(100) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `events` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(150) DEFAULT NULL,
    `description` TEXT,
    `event_date` DATE DEFAULT NULL,
    `club_id` INT DEFAULT NULL,
    `image_path` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `club_id` (`club_id`),
    CONSTRAINT `events_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `bookmarks` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `event_id` INT NOT NULL,
    `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_bookmark` (`user_id`,`event_id`),
    KEY `event_id` (`event_id`),
    CONSTRAINT `bookmarks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `bookmarks_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `comments` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT DEFAULT NULL,
    `event_id` INT DEFAULT NULL,
    `comment` VARCHAR(255) DEFAULT NULL,
    `posted_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `event_id` (`event_id`),
    CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
    CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

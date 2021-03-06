#!/bin/bash
echo 'SHOW DATABASES'  | mysql -u root | grep 'majires' > /dev/null || \
  echo 'CREATE DATABASE majires' | mysql -u root

SQL=$(cat <<'SQL'
DROP TABLE IF EXISTS `rooms`; CREATE TABLE `rooms` (
  `id` int auto_increment,
  `title` varchar(64) NOT NULL,
  `overview` TEXT,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
;
DROP TABLE IF EXISTS `comments`; CREATE TABLE `comments` (
  `id` int auto_increment,
  `parent_comment_id` int NOT NULL DEFAULT 0,
  `room_id` int NOT NULL,
  `comment_type` int NOT NULL DEFAULT 0,
  `content` TEXT NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`room_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
;
DROP TABLE IF EXISTS `likes`; CREATE TABLE `likes` (
  `comment_id` int NOT NULL,
  `session_id`  varchar(64) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`comment_id`, `session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
;
SQL
)
echo "${SQL}" | mysql -u root majires


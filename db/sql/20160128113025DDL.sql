SET NAMES utf8 /

CREATE DATABASE `weimg`
  DEFAULT CHARSET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci /

DROP TABLE IF EXISTS `users`/
CREATE TABLE `users` (
  `userId`              INT(11)      NOT NULL  AUTO_INCREMENT,
  `username`            VARCHAR(127) NOT NULL,
  `mobilePhoneNumber`   VARCHAR(63)  NOT NULL,
  `avatarUrl`           VARCHAR(255) NOT NULL,
  `sessionToken`        VARCHAR(127) NOT NULL,
  `sessionTokenCreated` TIMESTAMP    NOT NULL  DEFAULT CURRENT_TIMESTAMP,
  `password`            VARCHAR(127) NOT NULL,
  `created`             TIMESTAMP    NOT NULL  DEFAULT CURRENT_TIMESTAMP,
  `updated`             TIMESTAMP    NOT NULL  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `NAME_IDX` (`username`),
  UNIQUE KEY `PHONE_IDX` (`mobilePhoneNumber`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 /

DROP TABLE IF EXISTS `images` /
CREATE TABLE `images` (
  `imageId`     VARCHAR(31)  NOT NULL,
  `link`        VARCHAR(255) NOT NULL,
  `author`      INT(31)      NOT NULL,
  `description` VARCHAR(511),
  `created`     TIMESTAMP    NOT NULL  DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imageId`),
  FOREIGN KEY (`author`) REFERENCES `users` (`userId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 /

DROP TABLE IF EXISTS `posts` /
CREATE TABLE `posts` (
  `postId`  INT(11)      NOT NULL  AUTO_INCREMENT,
  `title`   VARCHAR(255) NOT NULL,
  `topic`   VARCHAR(31),
  `cover`   VARCHAR(16)  NOT NULL,
  `author`  INT(11)      NOT NULL,
  `score`   FLOAT(11, 7) NOT NULL,
  `created` TIMESTAMP    NOT NULL  DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`postId`),
  FOREIGN KEY (`author`) REFERENCES `users` (`userId`),
  FOREIGN KEY (`cover`) REFERENCES `images` (`imageId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 /

DROP TABLE IF EXISTS `post_images` /
CREATE TABLE `post_images` (
  `postId`  INT(11)     NOT NULL,
  `imageId` VARCHAR(31) NOT NULL,
  PRIMARY KEY (`postId`, `imageId`),
  FOREIGN KEY (`postId`) REFERENCES `posts` (`postId`),
  FOREIGN KEY (`imageId`) REFERENCES `images` (`imageId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 /

DROP TABLE IF EXISTS `post_votes` /
CREATE TABLE `post_votes` (
  `voteId` INT(11)     NOT NULL AUTO_INCREMENT,
  `userId` INT(11)     NOT NULL,
  `postId` INT(11)     NOT NULL,
  `vote`   VARCHAR(16) NOT NULL,
  PRIMARY KEY (`voteId`),
  UNIQUE KEY `VOTE_IDX` (`userId`, `postId`),
  FOREIGN KEY (`postId`) REFERENCES `posts` (`postId`),
  FOREIGN KEY (`userId`) REFERENCES `users` (`userId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 /

DROP TABLE IF EXISTS `comments` /
CREATE TABLE `comments` (
  `commentId` INT(11)      NOT NULL AUTO_INCREMENT,
  `postId`    INT(11),
  `parentId`  INT(11),
  `comment`   VARCHAR(255) NOT NULL,
  `author`    INT(11)      NOT NULL,
  PRIMARY KEY (`commentId`),
  FOREIGN KEY (`postId`) REFERENCES `posts` (`postId`),
  FOREIGN KEY (`author`) REFERENCES `users` (`userId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 /

SET NAMES utf8 /

CREATE DATABASE `weimg`
  DEFAULT CHARSET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci /

CREATE TABLE `users` (
  `userId`              VARCHAR(31)  NOT NULL,
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

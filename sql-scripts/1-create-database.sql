CREATE DATABASE IF NOT EXISTS au_lms
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE au_lms;

-- Drop if re-running
DROP TABLE IF EXISTS book_status;
DROP TABLE IF EXISTS book;
DROP TABLE IF EXISTS user;

-- 1) Users table
CREATE TABLE user
(
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_type ENUM ('Member','Admin') NOT NULL DEFAULT 'Member',
    first_name  VARCHAR(20)             NOT NULL,
    last_name   VARCHAR(20)             NOT NULL,
    email       VARCHAR(50)             NOT NULL UNIQUE,
    password    CHAR(32)                NOT NULL
);

-- 2) Books table
CREATE TABLE book
(
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    isbn        VARCHAR(50)  NOT NULL,
    title       VARCHAR(30)  NOT NULL,
    author      VARCHAR(30)  NOT NULL,
    publisher   VARCHAR(30)  NOT NULL,
    language    ENUM ('English','French','German','Mandarin','Japanese','Russian','Other')
                             NOT NULL DEFAULT 'English',
    category    ENUM ('Fiction','Non-Fiction','Reference')
                             NOT NULL,
    cover_image VARCHAR(255) NULL,
    deleted_at  DATETIME     NULL
);

-- 3) Book status history
CREATE TABLE book_status
(
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    book_id      INT UNSIGNED                          NOT NULL,
    member_id    INT UNSIGNED                          NULL,
    status       ENUM ('Available','Onloan','Deleted') NOT NULL DEFAULT 'Available',
    applied_date DATETIME                              NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bs_book FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE,
    CONSTRAINT fk_bs_member FOREIGN KEY (member_id) REFERENCES user (id) ON DELETE SET NULL
);

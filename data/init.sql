/**
 * Database creation script
 */

/* Foreign key constraints need to be explicitly in SQLite */
PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS user;

CREATE TABLE user (
    id INTEGER PRIMARY KEY NOT NULL,
    username VARCHAR UNIQUE NOT NULL,
    password VARCHAR NOT NULL,
    access INTEGER NOT NULL,
    created_at VARCHAR NOT NULL,
    is_enabled BOOLEAN NOT NULL DEFAULT true
);

/* This whill become user = 1. I'm creating this just to satisfy constraints.
   The password will be properly hashed in the installer */
INSERT INTO
    user(
        username, password, access, created_at, is_enabled
    )
    VALUES(
        "admin", "unhashed-password", 0, datetime('now', '-3 months'), 0
    );

DROP TABLE IF EXISTS post;

CREATE TABLE post (
    id INTEGER PRIMARY KEY NOT NULL,
    title VARCHAR NOT NULL,
    body MEDIUMTEXT NOT NULL,
    user_id INTEGER NOT NULL,
    created_at VARCHAR NOT NULL,
    updated_at VARCHAR,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);

DROP TABLE IF EXISTS comment;

CREATE TABLE comment (
    id INTEGER PRIMARY KEY NOT NULL,
    post_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    created_at VARCHAR NOT NULL,
    text VARCHAR NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
    FOREIGN KEY (post_id) REFERENCES post(id) ON DELETE CASCADE
);
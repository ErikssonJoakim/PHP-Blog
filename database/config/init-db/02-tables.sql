USE blog;

CREATE TABLE IF NOT EXISTS users (
    user_id INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(80) NOT NULL,
    email VARCHAR(80) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(80) NOT NULL DEFAULT "lambda",
    created_on DATETIME NOT NULL DEFAULT NOW(),
    updated_on DATETIME ON UPDATE NOW(),
    PRIMARY KEY (user_id)
);

CREATE TABLE IF NOT EXISTS posts (
    post_id INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT(6) UNSIGNED NOT NULL,
    title VARCHAR(80) NOT NULL,
    slug VARCHAR(80) NOT NULL UNIQUE,
    body VARCHAR(255) NOT NULL,
    reading_time INT(2) UNSIGNED NOT NULL,
    img VARCHAR(255) NOT NULL,
    created_on DATETIME NOT NULL DEFAULT NOW(),
    updated_on DATETIME ON UPDATE NOW(),
    PRIMARY KEY (post_id),
    CONSTRAINT FK__user_id__posts FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comments (
    comment_id INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
    post_id INT(6) UNSIGNED NOT NULL,
    user_id INT(6) UNSIGNED NOT NULL,
    body VARCHAR(255) NOT NULL,
    created_on DATETIME NOT NULL DEFAULT NOW(),
    updated_on DATETIME ON UPDATE NOW(),
    PRIMARY KEY (comment_id),
    CONSTRAINT FK__post_id__comments FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
    CONSTRAINT FK__user_id__comments FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
 );
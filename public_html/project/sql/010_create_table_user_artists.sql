CREATE TABLE IF NOT EXISTS  `UserArtists`
(
    `id`         int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id`    int NOT NULL,
    `artist_id`  int NOT NULL,
    `is_active`  TINYINT(1) default 1,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    FOREIGN KEY (`artist_id`) REFERENCES `Shazam-Artists`(`id`),
    FOREIGN KEY (`user_id`) REFERENCES `Users`(`id`),
    UNIQUE KEY (`artist_id` , `user_id`)
)
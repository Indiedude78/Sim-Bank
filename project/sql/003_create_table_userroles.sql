--User roles table
CREATE TABLE IF NOT EXISTS  `UserRoles`
(
    `id`         int auto_increment not null,
    `user_id`    int,
    `role_id`    int,
    `is_active`  TINYINT(1) default 1,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`), --set primary key to id
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`), --reference key from Users table
    FOREIGN KEY (`role_id`) REFERENCES Roles(`id`), --reference key from Roles table
    UNIQUE KEY (`user_id`, `role_id`) --set unique keys
)
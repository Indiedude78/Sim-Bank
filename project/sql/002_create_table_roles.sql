--Roles table for users
CREATE TABLE IF NOT EXISTS `Roles`
(
    `id`            int auto_increment not null, --ID
    `name`          varchar(100) not null unique, --Name
    `description`   varchar(100) default '', --Description if necessary
    `is_active`     TINYINT(1) default 1,
    `created`       timestamp default current_timestamp,
    `modified`      timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`) --Set primary key to ID
)
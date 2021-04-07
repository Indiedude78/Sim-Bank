CREATE TABLE IF NOT EXISTS `Accounts` (
    `id`                INT NOT NULL AUTO_INCREMENT,
    `account_number`    VARCHAR(12) NOT NULL,
    `user_id`           INT,
    `account_type`      VARCHAR(20),
    `opened_date`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_updated`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `balance`           DECIMAL(12, 2) DEFAULT 0.00,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES Users (`id`),
    UNIQUE KEY (`account_number`)    
)
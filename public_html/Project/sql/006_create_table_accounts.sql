CREATE TABLE IF NOT EXISTS `Accounts`
(
    `id` int auto_increment PRIMARY KEY,
    `account` varchar(12) unique,
    `user_id` int,
    `balance` int DEFAULT 0,
    `created` timestamp default current_timestamp,
    `modified` timestamp default current_timestamp on update current_timestamp,
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`),
    check (`balance` >= 0 AND LENGTH(`account`) = 12)
)
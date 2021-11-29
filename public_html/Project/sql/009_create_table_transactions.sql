CREATE TABLE IF NOT EXISTS `Transactions`
 (
     `id` int auto_increment PRIMARY KEY,
     `account_number` varchar(12) unique,
     `routing_number` varchar(12) unique, 
     `user_id` int, 
     `balance` bigint default 0,
     `created` timestamp default current_timestamp,
     `modified` timestamp default current_timestamp on update current_timestamp,
     `account_type` varchar(20),
     FOREIGN KEY (`user_id`) REFERENCES Users(`id`),
     check (LENGTH(`account_number`) = 12)
 )
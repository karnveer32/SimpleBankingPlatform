ALTER TABLE Users
ADD is_active tinyint(1) default 1
COMMENT 'Boolean of active or not active user';
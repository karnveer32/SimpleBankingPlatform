ALTER TABLE Accounts
ADD Frozen tinyint(1) default 0 
COMMENT 'Boolean of frozen or not frozen account';
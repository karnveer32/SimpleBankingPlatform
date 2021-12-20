ALTER TABLE Accounts
ADD COLUMN Active tinyint(1) default 1 
COMMENT 'Boolean of active or not active account';
--
-- Connected with webhook.php
--

DELIMITER $$
CREATE FUNCTION `CreateUpdateRemoveClient`(`github_username` VARCHAR(39), `accountID` INT) RETURNS int(11)
BEGIN
	INSERT IGNORE INTO `Sql1437734_5`.`client`(`github_username`)
	VALUES (github_username);
	
	INSERT INTO `Sql1437734_5`.`client_account`(`clientID`, `accountID`)
	VALUES (github_username, accountID);
	
	RETURN 200;
END$$
DELIMITER ;
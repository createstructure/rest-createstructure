--
-- Connected with webhook.php
--

DELIMITER $$
CREATE FUNCTION `CreateUpdateRemoveClient`(`github_username` VARCHAR(39), `accountID` INT) RETURNS int(11)
BEGIN
	IF (GET_LOCK(CONCAT("CreateUpdateRemoveClient", github_username), 60) = 0)
    THEN
		RETURN 504;
	END IF;

	INSERT IGNORE INTO `client`(`github_username`)
	VALUES (github_username);
	
	INSERT INTO `client_account`(`clientID`, `accountID`)
	VALUES (github_username, accountID);
	
	RETURN 200;
END$$
DELIMITER ;
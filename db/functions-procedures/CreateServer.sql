--
-- Procedure to create a DB
--

DELIMITER $$
CREATE FUNCTION `CreateServer`(`clientID` VARCHAR(39), `server_name` TEXT, `description` TEXT, `server_password` TEXT, `server_public_key` TEXT) RETURNS int(11)
BEGIN
	IF
	(
		(
			SELECT json_extract(GetClient(clientID), '$.super')
		) = 0
	)
	THEN
		RETURN 401;
	END IF;

	INSERT INTO `server_list`(`name`, `description`)
	VALUES (`server_name`, `description`);
	
	INSERT INTO `server_secrets`(`serverID`, `server_password`, `server_public_key`)
	VALUES (
				LAST_INSERT_ID(), 
				server_password,
				server_public_key
		);

	RETURN 200;
END$$
DELIMITER ;
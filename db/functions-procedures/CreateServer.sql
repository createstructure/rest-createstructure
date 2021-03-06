--
-- Procedure to create a DB, it isn't connected to the REST API
--

DELIMITER $$
CREATE PROCEDURE `CreateServer`(IN `name` TEXT, IN `description` TEXT, IN `server_password` TEXT, IN `server_public_key` TEXT)
BEGIN
	INSERT INTO `server_list`(`name`, `description`)
	VALUES (name, description);
	
	INSERT INTO `server_secrets`(`serverID`, `server_password`, `server_public_key`)
	VALUES (
				LAST_INSERT_ID(), 
				server_password,
				server_public_key
		);
END$$
DELIMITER ;
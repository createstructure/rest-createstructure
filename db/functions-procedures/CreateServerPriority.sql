--
-- Connected with server_set_priority.php
--

DELIMITER $$
CREATE PROCEDURE `CreateServerPriority`(IN `client_ID` VARCHAR(39), IN `instruction` TEXT, IN `server_ID` INT)
BEGIN
	INSERT INTO `server_priority_declaration`(`client_ID`, `instruction_ID`, `server_ID`)
	VALUES (
		client_ID, 
		(
			SELECT server_priority_instructions.ID
			FROM `server_priority_instructions` AS server_priority_instructions
			WHERE server_priority_instructions.name = instruction
		),
		server_ID
	);
	
	INSERT INTO `server_priority_log`(`priority_ID`, `status_ID`)
	VALUES (
		LAST_INSERT_ID(), 
		(
			SELECT server_priority_status.ID
			FROM `server_priority_status` AS server_priority_status
			WHERE server_priority_status.description = "To do"
			LIMIT 1
		)
	);
END$$
DELIMITER ;
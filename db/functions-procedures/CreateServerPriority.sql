--
-- Connected with server_set_priority.php
--

DELIMITER $$
CREATE PROCEDURE `CreateServerPriority`(IN `clientID` VARCHAR(39), IN `instruction` TEXT, IN `serverID` INT)
BEGIN
	INSERT INTO `server_priority_declaration`(`clientID`, `instructionID`, `serverID`)
	VALUES (
		clientID, 
		(
			SELECT server_priority_instructions.ID
			FROM `server_priority_instructions` AS server_priority_instructions
			WHERE server_priority_instructions.name = instruction
		),
		serverID
	);
	
	INSERT INTO `server_priority_log`(`priorityID`, `statusID`)
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
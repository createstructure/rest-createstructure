--
-- Connected with server_set_priority.php
--

DELIMITER $$
CREATE FUNCTION `ServerSetPriority`(`clientID` VARCHAR(39), `server_name` TEXT, `priority_instruction` TEXT) RETURNS int(11)
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

	INSERT INTO `server_priority_declaration`(`clientID`, `serverID`, `instructionID`)
	VALUES 
		(
			clientID,
			(
				SELECT server_list.ID
				FROM `server_list` AS server_list
				WHERE server_list.name = server_name
				LIMIT 1
			),
			(
				SELECT server_priority_instructions.ID
				FROM `server_priority_instructions` AS server_priority_instructions
				WHERE server_priority_instructions.name = priority_instruction
				LIMIT 1
			)
		);

	INSERT INTO `server_priority_log`(`priorityID`, `statusID`)
	VALUES
		(
			LAST_INSERTID(),
			(
				SELECT server_priority_status.ID
				FROM `server_priority_status` AS server_priority_status
				WHERE server_priority_status.description = "To do"
				LIMIT 1
			)
		);
	
	RETURN 200;
END$$
DELIMITER ;
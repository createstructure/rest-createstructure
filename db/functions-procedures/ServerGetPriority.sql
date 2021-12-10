--
-- Connected with server_get_priority.php
--

DELIMITER $$
CREATE FUNCTION `ServerGetPriority`(`server_name` TEXT, `server_password` TEXT) RETURNS text CHARSET latin1
BEGIN
	IF 
	(
		(
			SELECT 
				COUNT(server_list.ID) 
			FROM 
				(
					`server_list` AS server_list 
					INNER JOIN `server_secrets` AS server_secrets
					ON server_secrets.serverID = server_list.ID
				) 
			WHERE 
				server_list.name = server_name 
				AND server_secrets.server_password = server_password 
			LIMIT 1
		) != 1
	) THEN
	RETURN "-1";
	END IF;
	
	IF (
	(
		SELECT COUNT(*) 
		FROM `server_priority_declaration` AS server_priority_declaration1
		WHERE 
			server_priority_declaration1.serverID = (
				SELECT server_list1a.ID 
				FROM `server_list` AS server_list1a
				WHERE server_list1a.name = server_name
				LIMIT 1
			) 
			AND (
				SELECT MAX(server_priority_log1b.statusID)
				FROM `server_priority_log` AS server_priority_log1b
				WHERE server_priority_log1b.priorityID = server_priority_declaration1.ID
				GROUP BY server_priority_log1b.priorityID
				LIMIT 1
			) = (
				SELECT server_priority_status1c.ID
				FROM `server_priority_status` AS server_priority_status1c
				WHERE server_priority_status1c.description = "To Do"
				LIMIT 1
			)
		) = 0
	) THEN RETURN "-2";
	END IF;

	SELECT JSON_OBJECT(
		'priority_instruction', server_priority_instructions2.name,
		'priorityID', server_priority_declaration2.ID
	)
	INTO @priority
	FROM (
		`server_priority_declaration` AS server_priority_declaration2
		INNER JOIN `server_priority_instructions` AS server_priority_instructions2
		ON server_priority_declaration2.instructionID = server_priority_instructions2.ID
	)
	WHERE 
		server_priority_declaration2.serverID = (
			SELECT server_list2a.ID 
			FROM `server_list` AS server_list2a
			WHERE server_list2a.name = server_name
			LIMIT 1
		) 
		AND (
			SELECT MAX(server_priority_log2b.statusID)
			FROM `server_priority_log` AS server_priority_log2b
			WHERE server_priority_log2b.priorityID = server_priority_declaration2.ID
			GROUP BY server_priority_log2b.priorityID
			LIMIT 1
		) = (
			SELECT server_priority_status2c.ID
			FROM `server_priority_status` AS server_priority_status2c
			WHERE server_priority_status2c.description = "To Do"
			LIMIT 1
		)
	LIMIT 1;

	RETURN @priority;
END$$
DELIMITER ;
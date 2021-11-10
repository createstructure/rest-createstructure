--
-- Connected with server_set_priority_done.php
--

DELIMITER $$
CREATE FUNCTION `ServerSetPriorityDone`(`server_name` TEXT, `server_password` TEXT, `priorityID` TEXT) RETURNS int(11)
BEGIN
	IF (
		(
			SELECT COUNT(server_list.ID) 
			FROM 
				(
					`server_list` AS server_list 
					INNER JOIN `server_secrets` AS server_secrets ON server_secrets.serverID = server_list.ID
				) 
			WHERE 
				server_list.name = server_name 
				AND server_secrets.server_password = server_password 
			LIMIT 1
		) != 1
	) THEN RETURN 401;
	END IF;

	IF (
		(
			SELECT COUNT(*) 
			FROM `server_priority_log` AS server_priority_log1
			WHERE 
				server_priority_log1.priorityID = priorityID 
				AND server_priority_log1.statusID = (
						SELECT server_priority_status1.ID 
						FROM `server_priority_status` AS server_priority_status1
						WHERE server_priority_status1.description = "Done" 
						LIMIT 1
					) 
			LIMIT 1
		) != 0
	) THEN RETURN 409;
	END IF;

	INSERT INTO `server_priority_log` (`priorityID`, `statusID`)
	VALUES
	(
		priorityID,
		(
			SELECT server_priority_status2.ID 
			FROM `server_priority_status` AS server_priority_status2
			WHERE server_priority_status2.description = "Done" 
			LIMIT 1
		) 
	);

	RETURN 200;
END$$
DELIMITER ;
--
-- Connected with server_set_job_done.php
--

DELIMITER $$
CREATE FUNCTION `ServerSetJobDone`(`server_name` TEXT, `server_password` TEXT, `repo_ID` INT) RETURNS int(11)
BEGIN
	IF 
	(
		(
			SELECT COUNT(server_list.ID) 
			FROM 
				(
					`server_list` AS server_list 
					INNER JOIN `server_secrets` AS server_secrets ON server_secrets.server_ID = server_list.ID
				) 
			WHERE server_list.name = server_name 
				AND server_secrets.server_password = server_password 
			LIMIT 1
		) != 1
	) THEN RETURN 401;
	END IF;

	IF 
	(
		(
			SELECT COUNT(*) 
			FROM `repo_log` AS repo_log1
			WHERE repo_log1.repo_ID = repo_ID 
				AND repo_log1.status_ID = (
						SELECT repo_status1.ID 
						FROM `repo_status` AS repo_status1
						WHERE repo_status1.description = "Done" 
						LIMIT 1
					) 
			LIMIT 1
		) != 0
	) THEN RETURN 409;
	END IF;

	INSERT
	INTO `repo_log` (`repo_ID`, `server_ID`, `status_ID`)
	VALUES
	(
		repo_ID,
		server_ID,
		(
			SELECT repo_status2.ID 
			FROM `repo_status` AS repo_status2
			WHERE repo_status2.description = "Done" 
			LIMIT 1
			)
	);
	
	RETURN 200;
END$$
DELIMITER ;
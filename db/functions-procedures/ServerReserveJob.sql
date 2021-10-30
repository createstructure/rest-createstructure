DELIMITER $$
CREATE FUNCTION `ServerReserveJob`(`server_name` TEXT, `server_password` TEXT) RETURNS int(11)
BEGIN
	IF 
	(
		(
			SELECT 
				COUNT(server_list.ID) 
			FROM 
				(
					`server_list` AS server_list 
					INNER JOIN `server_secrets` AS server_secrets ON server_secrets.server_ID = server_list.ID
				) 
			WHERE 
				server_list.name = server_name 
				AND server_secrets.server_password = server_password 
			LIMIT 
				1
		) != 1
	) THEN RETURN -1;
	END IF;

	SELECT COUNT(*)
	INTO @n_repo
	FROM 
		(
			SELECT 
				repo_log1.repo_ID 
			FROM 
				(
					`repo_log` AS repo_log1 
					INNER JOIN `repo_status` AS repo_status1 ON repo_log1.status_ID = repo_status1.ID
				) 
			GROUP BY 
				repo_log1.repo_ID 
			HAVING 
				(
					SELECT 
						repo_status1b.description 
					FROM 
						`repo_status` AS repo_status1b 
					WHERE 
						repo_status1b.ID = MAX(repo_log1.status_ID)
				) = "To do"
		) AS tmp;

	IF (@n_repo = 0)
	THEN 
	RETURN -2;
	END IF;

	SELECT repo_log2.repo_ID
	INTO @repo_ID 
	FROM 
		(
			`repo_log` AS repo_log2 
			INNER JOIN `repo_status` AS repo_status2 ON repo_log2.status_ID = repo_status2.ID
		) 
	GROUP BY repo_log2.repo_ID 
	HAVING 
		(
			SELECT 
				repo_status2b.description 
			FROM 
				`repo_status` AS repo_status2b 
			WHERE 
				repo_status2b.ID = MAX(repo_log2.status_ID)
		) = "To do" 
	LIMIT 1;

	INSERT INTO `repo_log` (`repo_ID`, `server_ID`, `status_ID`) 
	VALUES 
		(
			@repo_ID, 
			(
				SELECT server_list3.ID 
				FROM `server_list` AS server_list3 
				WHERE server_list3.name = server_name 
				LIMIT 1
			), (
				SELECT repo_status4.ID 
				FROM `repo_status` AS repo_status4
				WHERE repo_status4.description = "Reserved" 
				LIMIT 1
			)
		);
		
	RETURN @repo_ID;
END$$
DELIMITER ;
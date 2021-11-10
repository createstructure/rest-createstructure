--
-- Connected with server_reserve_job.php
--

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
					INNER JOIN `server_secrets` AS server_secrets ON server_secrets.serverID = server_list.ID
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
				repo_log1.repoID 
			FROM 
				(
					`repo_log` AS repo_log1 
					INNER JOIN `repo_status` AS repo_status1 ON repo_log1.statusID = repo_status1.ID
				) 
			GROUP BY 
				repo_log1.repoID 
			HAVING 
				(
					SELECT 
						repo_status1b.description 
					FROM 
						`repo_status` AS repo_status1b 
					WHERE 
						repo_status1b.ID = MAX(repo_log1.statusID)
				) = "To do"
		) AS tmp;

	IF (@n_repo = 0)
	THEN 
	RETURN -2;
	END IF;

	SELECT repo_log2.repoID
	INTO @repoID 
	FROM 
		(
			`repo_log` AS repo_log2 
			INNER JOIN `repo_status` AS repo_status2 ON repo_log2.statusID = repo_status2.ID
		) 
	GROUP BY repo_log2.repoID 
	HAVING 
		(
			SELECT 
				repo_status2b.description 
			FROM 
				`repo_status` AS repo_status2b 
			WHERE 
				repo_status2b.ID = MAX(repo_log2.statusID)
		) = "To do" 
	LIMIT 1;

	INSERT INTO `repo_log` (`repoID`, `serverID`, `statusID`) 
	VALUES 
		(
			@repoID, 
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
		
	RETURN @repoID;
END$$
DELIMITER ;
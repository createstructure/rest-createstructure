--
-- Debug function
--

DELIMITER $$
CREATE FUNCTION `ServerGetJobInfo`(`server_name` TEXT, `server_password` TEXT, `repoID` INT) RETURNS text CHARSET latin1
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
	) THEN RETURN -1;
	END IF;

	SELECT repo_declaration2.data
	INTO @repo_info 
	FROM `repo_declaration` AS repo_declaration2 
	WHERE repo_declaration2.ID = repoID
	LIMIT 1;

	RETURN @repo_info;
END$$
DELIMITER ;
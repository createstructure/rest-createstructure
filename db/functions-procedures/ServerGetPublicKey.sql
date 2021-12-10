--
-- Connected with server_get_job_info.php
--

DELIMITER $$
CREATE FUNCTION `ServerGetPublicKey`(`server_name` TEXT, `server_password` TEXT) RETURNS text CHARSET latin1
BEGIN
	IF
	(
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

	SELECT server_secrets2.server_public_key
	INTO @public_key
	FROM 
		(
			`server_list` AS server_list2
			INNER JOIN `server_secrets` AS server_secrets2 ON server_secrets2.serverID = server_list2.ID
		) 
	WHERE 
		server_list2.name = server_name 
		AND server_secrets2.server_password = server_password 
	LIMIT 1;
		
	RETURN @public_key;
END$$
DELIMITER ;
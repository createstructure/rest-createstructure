--
-- Connected to 
--

DELIMITER $$
CREATE FUNCTION `CreateRepo`(`client_ID` VARCHAR(39), `payload` TEXT) RETURNS int(11)
BEGIN
	DECLARE consumer TEXT DEFAULT GetClient(client_ID); 
	IF
	(
		SELECT json_extract(
			consumer,
			'$.remaining_day'
		)
	) > 0
	AND
	(
		SELECT json_extract(
			consumer,
			'$.remaining_h'
		)
	) > 0
	AND
	(
		SELECT json_extract(
			consumer,
			'$.remaining_m'
		)
	) > 0
	THEN
		INSERT INTO `repo_declaration`(`client_ID`, `data`)			
		VALUES (client_ID, payload);
		
		INSERT INTO `repo_log`(`repo_ID`, `status_ID`)
		VALUES (
					LAST_INSERT_ID(), 
					(
						SELECT repo_status.ID
						FROM `repo_status` AS repo_status
						WHERE repo_status.description = "To do"
						LIMIT 1
					)
			);
		RETURN 200;
	ELSE
		RETURN 429;
	END IF;
END$$
DELIMITER ;
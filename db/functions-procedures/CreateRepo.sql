--
-- Connected to 
--

DELIMITER $$
CREATE FUNCTION `CreateRepo`(`clientID` VARCHAR(39), `payload` TEXT) RETURNS int(11)
BEGIN
	DECLARE consumer TEXT DEFAULT GetClient(clientID); 
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
		INSERT INTO `repo_declaration`(`clientID`, `data`)			
		VALUES (clientID, payload);
		
		INSERT INTO `repo_log`(`repoID`, `statusID`)
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
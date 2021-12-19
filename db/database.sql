--
-- Create DB
--

CREATE DATABASE createstructure;
USE createstructure;

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `github_username` varchar(39) NOT NULL COMMENT 'Github account username',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`github_username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table listing service customers';
--
-- Table structure for table `client_account`
--

CREATE TABLE `client_account` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Table ID',
  `clientID` varchar(39) NOT NULL COMMENT 'Client ID',
  `accountID` int(11) NOT NULL COMMENT 'Account ID',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`),
  KEY (`clientID`, `accountID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table for connecting users to various accounts';
--
-- Table structure for table `client_accounts_type`
--

CREATE TABLE `client_accounts_type` (
  `ID` int(11) NOT NULL COMMENT 'Table ID',
  `active` tinyint(1) NOT NULL COMMENT '"true" if it represents an active account, otherwise "false"',
  `super` tinyint(1) NOT NULL COMMENT '"true" if it represents a super account, otherwise "false"',
  `max_day` int(11) NOT NULL COMMENT 'Maximum repository creation for every day',
  `max_h` int(11) NOT NULL COMMENT 'Maximum repository creation for every hour',
  `max_m` int(11) NOT NULL COMMENT 'Maximum repository creation for every minute',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table for the definition of available accounts';

--
-- Add data for table `client_accounts_type`
--

INSERT INTO `client_accounts_type` (`ID`, `active`, `super`, `max_day`, `max_h`, `max_m`, `description`) VALUES
(0, 1, 1, 10000, 1000, 100, 'Super user'),
(1, 0, 0, 0, 0, 0, 'Disabled account'),
(6488, 1, 0, 100, 20, 1, 'Free account');
--
-- Table structure for table `repo_declaration`
--

CREATE TABLE `repo_declaration` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Table ID',
  `clientID` varchar(39) NOT NULL COMMENT 'Client ID',
  `data` text NOT NULL COMMENT 'All information for the creation of the repository',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`),
  KEY (`clientID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='The declaration of the repo to create';
--
-- Table structure for table `repo_log`
--

CREATE TABLE `repo_log` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Table ID',
  `repoID` int(11) NOT NULL COMMENT 'Repository ID',
  `serverID` int(11) DEFAULT NULL COMMENT 'ID of the server taking charge of the operation',
  `statusID` int(11) NOT NULL COMMENT 'Status ID',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`),
  KEY (`repoID`,`serverID`,`statusID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table to track the progress of repo creation';
--
-- Table structure for table `repo_status`
--

CREATE TABLE `repo_status` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Table ID',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table for the possible stages of creating a repository';

--
-- Add data for table `repo_status`
--

INSERT INTO `repo_status` (`description`) VALUES
('To do'),
('Reserved'),
('Done');
--
-- Table structure for table `server_list`
--

CREATE TABLE `server_list` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Table ID',
  `name` text NOT NULL COMMENT 'Server name',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='List of the servers';
--
-- Table structure for table `server_priority_declaration`
--

CREATE TABLE `server_priority_declaration` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Table ID',
  `serverID` int(11) NOT NULL COMMENT 'Server ID',
  `clientID` varchar(39) NOT NULL COMMENT 'ID of who made the request',
  `instructionID` int(11) NOT NULL COMMENT 'Instruction ID',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`),
  KEY (`serverID`, `clientID`, `instructionID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='The declaration of the server instructions to do';
--
-- Table structure for table `server_priority_instructions`
--

CREATE TABLE `server_priority_instructions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Table ID',
  `name` text NOT NULL COMMENT 'Name of the instuction',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='List of possible server instructions';

--
-- Add data for table `server_priority_instructions`
--

INSERT INTO `server_priority_instructions` (`name`, `description`) VALUES
('test', 'Test routine'),
('update', 'Do an update of all the needed packages'),
('shutdown', 'Shutdown the server'),
('reboot', 'Reboot the server');
--
-- Table structure for table `server_priority_log`
--

CREATE TABLE `server_priority_log` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Table ID',
  `priorityID` int(11) NOT NULL COMMENT 'Priority ID',
  `statusID` int(11) NOT NULL COMMENT 'Status ID',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`),
  KEY (`priorityID`,`statusID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='The log of server instructions';
--
-- Table structure for table `server_priority_status`
--

CREATE TABLE `server_priority_status` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Table ID',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='The status of the instruction execution';

--
-- Add data for table `server_priority_status`
--

INSERT INTO `server_priority_status` (`description`) VALUES
('To do'),
('Done');
--
-- Table structure for table `server_secrets`
--

CREATE TABLE `server_secrets` (
  `ID` int(11) AUTO_INCREMENT NOT NULL COMMENT 'Table ID',
  `serverID` int(11) NOT NULL COMMENT 'Server ID',
  `server_password` text NOT NULL COMMENT 'Server password',
  `server_public_key` text NOT NULL COMMENT 'Server public key',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`),
  KEY (`serverID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='The secrets of the servers';
--
-- Connected to 
--

DELIMITER $$
CREATE FUNCTION `CreateRepo`(`clientID` VARCHAR(39), `payload` TEXT) RETURNS int(11)
BEGIN
	IF (GET_LOCK(CONCAT("CreateRepo", clientID), 60) = 0)
    THEN
		RETURN 504;
	END IF;
    
	SELECT GetClient(clientID)
    INTO @consumer;
    
	IF
	(
		SELECT json_extract(
			@consumer,
			'$.remaining_day'
		)
	) > 0
	AND
	(
		SELECT json_extract(
			@consumer,
			'$.remaining_h'
		)
	) > 0
	AND
	(
		SELECT json_extract(
			@consumer,
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
DELIMITER ;--
-- Procedure to create a DB, it isn't connected to the REST API
--

DELIMITER $$
CREATE PROCEDURE `CreateServer`(IN `name` TEXT, IN `description` TEXT, IN `server_password` TEXT, IN `server_public_key` TEXT)
BEGIN
	IF (GET_LOCK("CreateServer", 60) = 0)
    THEN
		RETURN 504;
	END IF;

	INSERT INTO `server_list`(`name`, `description`)
	VALUES (name, description);
	
	INSERT INTO `server_secrets`(`serverID`, `server_password`, `server_public_key`)
	VALUES (
				LAST_INSERT_ID(), 
				server_password,
				server_public_key
		);
END$$
DELIMITER ;--
-- Connected with webhook.php
--

DELIMITER $$
CREATE FUNCTION `CreateUpdateRemoveClient`(`github_username` VARCHAR(39), `accountID` INT) RETURNS int(11)
BEGIN
	IF (GET_LOCK(CONCAT("CreateUpdateRemoveClient", github_username), 60) = 0)
    THEN
		RETURN 504;
	END IF;

	INSERT IGNORE INTO `client`(`github_username`)
	VALUES (github_username);
	
	INSERT INTO `client_account`(`clientID`, `accountID`)
	VALUES (github_username, accountID);
	
	RETURN 200;
END$$
DELIMITER ;--
-- Connected with auth.php and other functions
--

DELIMITER $$
CREATE FUNCTION `GetClient`(`github_username` VARCHAR(39)) RETURNS text CHARSET latin1
	DETERMINISTIC
BEGIN
	IF (GET_LOCK(CONCAT("GetClient", github_username), 60) = 0)
    THEN
		RETURN 504;
	END IF;
	
	SELECT JSON_OBJECT(
		'client', client.github_username,
		'client_description', client.description,
		'active', client_accounts_type.active,
		'super', client_accounts_type.super,
		'max_day', client_accounts_type.max_day,
		'max_h', client_accounts_type.max_h,
		'max_m', client_accounts_type.max_m,
		'remaining_day', client_accounts_type.max_day - (
			SELECT COUNT(repo_declaration.ID)
			FROM `repo_declaration` AS repo_declaration
			WHERE
				repo_declaration.clientID = github_username AND
				repo_declaration.timestamp >= (date_sub(now(), interval 1 day))
			),
		'remaining_h', client_accounts_type.max_h  - (
			SELECT COUNT(repo_declaration.ID)
			FROM `repo_declaration` AS repo_declaration
			WHERE
				repo_declaration.clientID = github_username AND
				repo_declaration.timestamp >= (date_sub(now(), interval 1 hour))
			),
		'remaining_m', client_accounts_type.max_m  - (
			SELECT COUNT(repo_declaration.ID)
			FROM `repo_declaration` AS repo_declaration
			WHERE
				repo_declaration.clientID = github_username AND
				repo_declaration.timestamp >= (date_sub(now(), interval 1 minute))
			),
		'account_description', client_accounts_type.description,
		'timestamp', client_account.timestamp
	)
	INTO @output
	FROM 
		(
			(
				`client` AS client
				INNER JOIN `client_account` AS client_account 
				ON client.github_username = client_account.clientID
			)
			INNER JOIN `client_accounts_type` AS client_accounts_type 
			ON client_account.accountID = client_accounts_type.ID
		)
	WHERE client.github_username = github_username
	ORDER BY client_account.timestamp DESC
	LIMIT 1;
	
	RETURN @output;
END$$
DELIMITER ;--
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
DELIMITER ;--
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
DELIMITER ;--
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
DELIMITER ;--
-- Connected with server_reserve_job.php
--

DELIMITER $$
CREATE FUNCTION `ServerReserveJob`(`server_name` TEXT, `server_password` TEXT) RETURNS int(11)
BEGIN
	IF (GET_LOCK("ServerReserveJob", 60) = 0)
    THEN
		RETURN -3;
	END IF;

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
DELIMITER ;--
-- Connected with server_set_job_done.php
--

DELIMITER $$
CREATE FUNCTION `ServerSetJobDone`(`server_name` TEXT, `server_password` TEXT, `repoID` INT) RETURNS int(11)
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
			WHERE repo_log1.repoID = repoID 
				AND repo_log1.statusID = (
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
	INTO `repo_log` (`repoID`, `serverID`, `statusID`)
	VALUES
	(
		repoID, 
		(
			SELECT server_list2.ID 
			FROM `server_list` AS server_list2
			WHERE server_list2.name = server_name 
			LIMIT 1
		),
		(
			SELECT repo_status2.ID 
			FROM `repo_status` AS repo_status2
			WHERE repo_status2.description = "Done" 
			LIMIT 1
			)
	);
	
	RETURN 200;
END$$
DELIMITER ;--
-- Connected with server_set_priority.php
--

DELIMITER $$
CREATE FUNCTION `ServerSetPriority`(`clientID` VARCHAR(39), `server_name` TEXT, `priority_instruction` TEXT) RETURNS int(11)
BEGIN
	IF (GET_LOCK(CONCAT("ServerSetPriority", server_name), 60) = 0)
    THEN
		RETURN 504;
	END IF;

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
			LAST_INSERT_ID(),
			(
				SELECT server_priority_status.ID
				FROM `server_priority_status` AS server_priority_status
				WHERE server_priority_status.description = "To do"
				LIMIT 1
			)
		);
	
	RETURN 200;
END$$
DELIMITER ;--
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
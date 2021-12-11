--
-- Connected with auth.php and other functions
--

DELIMITER $$
CREATE FUNCTION `GetClient`(`github_username` VARCHAR(39)) RETURNS text CHARSET latin1
	DETERMINISTIC
BEGIN
	
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
DELIMITER ;
<?php
/**
 * Auth action
 *
 * PHP version 7.4.16
 *
 * @package    rest-createstructure
 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
 * @license    GNU
 * @link       https://github.com/createstructure/rest-createstructure
 */

// Import(s)
include_once "config/database.php";
include_once "action.php";

class Auth implements Action
{
	// class variabile(s)
	private $payload;
	private $conn;
	private $username;
	private $token;
	private $clientData;

	/**
	 * Constructor
	 *
	 * @param mixed		The payload of the request
	 */
	public function __construct($payload)
	{
		$this->payload = $payload;
		$this->username = $payload["payload"]["username"];
		$this->token = $payload["payload"]["token"];
	}

	/**
	 * Run main code
	 *
	 * @return array Array with the response, if there wasn"t any error
	 */
	public function run()
	{
		$this->conn = new Database();

		$this->checkGitHub();
		$this->getUser();

		$this->conn->closeConnection();

		return array(
			"code" => 200,
			"message" => "Login made with success",
			"auth_info" => $this->clientData
		);
	}

	/**
	 * Check the Github user account
	 */
	private function checkGitHub()
	{
		// Setup request to GitHub REST API
		$ch = curl_init("https://api.github.com/user");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, "createstructure");
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->token);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		if (!$result)
			trigger_error(curl_error($ch));
		curl_close($ch);

		// Check results
		if (json_decode($result, true)["login"] != $this->username)
			die(
				json_encode(
					array(
						"code" => 401,
						"message" => "GitHub credentials are not corrent"
					)
				)
			);
	}

	/**
	 * Get user information
	 */
	private function getUser()
	{
		// Get user id
		$query = "SELECT GetClient(?) AS client;";

		// prepare and execute query
		$stmt = $this->conn->getConnection()->stmt_init();
		$stmt->prepare($query);
		$stmt->bind_param("s", $this->username);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();

		if ($result->num_rows == 0)
			die(
				json_encode(
					array(
						"code" => 401,
						"message" => "You seem not registered into createstructure service (https://github.com/marketplace/createstructure)"
					)
				)
			);

		if ($result->num_rows > 0) {
			while ($row = $result->fetch_array())
				$this->clientData = json_decode($row["client"]);
		}
	}
}

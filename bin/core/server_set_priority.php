<?php
/**
 * Get priority action (server)
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

class ServerSetPriority implements Action
{
	// class variabile(s)
	private $payload;
	private $conn;
	private $username;
	private $token;
	private $serverName;
	private $serverPriority;
	private $statusCode;

	/**
	 * Constructor
	 *
	 * @param mixed		The payload of the request
	 */
	public function __construct($payload)
	{
		$this->payload = $payload;
		$this->username = $payload["username"];
		$this->token = $payload["token"];
		$this->serverName = $payload["server_name"];
		$this->serverPriority = $payload["server_priority"];
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
		$this->createPriority();

		$this->conn->closeConnection();

		$res = array();
		switch ($this->statusCode) {
			case "200":
			case 200:
				$res = array(
					"code" => 200,
					"message" => "Priority creation made with success"
				);
				break;
			case "504":
			case 504:
				$res = array(
					"code" => 504,
					"message" => "There is a problem, the DB seems to be full of work, please try again later"
				);
				break;
			case "401":
			case 401:
			default:
				$res = array(
					"code" => 401,
					"message" => "Sorry, you are not a super-user"
				);
		}
		return $res;
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
	 * Create the priority
	 */
	private function createPriority()
	{
		// Get user id
		$query = "SELECT ServerSetPriority(?, ?, ?) AS statusCode;"; // statusCode is the output of the CreateRepo function

		// prepare and execute query
		$stmt = $this->conn->getConnection()->stmt_init();
		$stmt->prepare($query);
		$stmt->bind_param(
						"sss",
						$this->username,
						$this->serverName,
						$this->serverPriority
					);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();

		if ($result->num_rows == 0)
			die(
				json_encode(
					array(
						"code" => 409,
						"message" => "Generic error in priority creation"
					)
				)
			);

		while ($row = $result->fetch_array())
			$this->statusCode = $row["statusCode"];
	}
}

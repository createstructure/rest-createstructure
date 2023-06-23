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

class ServerGetpriority implements Action
{
	// class variabile(s)
	private $payload;
	private $conn;
	private $serverName;
	private $serverPassword;
	private $response;

	/**
	 * Constructor
	 *
	 * @param mixed		The payload of the request
	 */
	public function __construct($payload)
	{
		$this->payload = $payload;
		$this->serverName = $payload["server_name"];
		$this->serverPassword = $payload["server_password"];
	}

	/**
	 * Run main code
	 *
	 * @return array Array with the response, if there wasn"t any error
	 */
	public function run()
	{
		$this->conn = new Database();

		$this->getPriority();

		$this->conn->closeConnection();

		$res = array();

		switch ($this->response) {
			case -1:
			case "-1":
				$res = array(
					"code" => 401,
					"message" => "Wrong credentials"
				);
				break;
			case -2:
			case "-2":
				$res = array(
					"code" => 201,
					"message" => "No new priority"
				);
				break;
			default:
				$priority = json_decode($this->response, true);
				$res = array(
					"code" => 200,
					"message" => "Priority",
					"priority_instruction" => $priority["priority_instruction"],
					"priorityID" => $priority["priorityID"]
				);
		}
		return $res;
	}

	/**
	 * Get priority information
	 */
	private function getPriority()
	{
		// Get user id
		$query = "SELECT ServerGetPriority(?, ?) AS response;";

		// prepare and execute query
		$stmt = $this->conn->getConnection()->stmt_init();
		$stmt->prepare($query);
		$stmt->bind_param(
						"ss",
						$this->serverName,
						$this->serverPassword
					);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();

		if ($result->num_rows == 0)
			die(
				json_encode(
					array(
						"code" => 409,
						"message" => "Generic error getting priority info"
					)
				)
			);

		if ($result->num_rows > 0) {
			while ($row = $result->fetch_array())
				$this->response = $row["response"];
		}
	}
}

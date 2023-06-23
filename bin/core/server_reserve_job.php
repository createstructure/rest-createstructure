<?php
/**
 * Reserve job action (server)
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

class ServerReservejob implements Action
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

		$this->getRepo();

		$this->conn->closeConnection();

		$res = array();
		switch ($this->response) {
			case -1:
				$res = array(
					"code" => 401,
					"message" => "Wrong credentials"
				);
				break;
			case -2:
				$res = array(
					"code" => 204,
					"message" => "No new repo to create"
				);
				break;
			case -3:
			case "-3":
				$res = array(
					"code" => 504,
					"message" => "There is a problem, the DB seems to be full of work, please try again later"
				);
				break;
			default:
			$res = array(
					"code" => 200,
					"message" => "Reserved repo",
					"repoID" => $this->response
				);
		}
		return $res;
	}

	/**
	 * Get reserveted repo information
	 */
	private function getRepo()
	{
		// Get user id
		$query = "SELECT ServerReserveJob(?, ?) AS response;";

		// prepare and execute query
		$stmt = $this->conn->getConnection()->stmt_init();
		$stmt->prepare($query);
		$stmt->bind_param("ss", $this->serverName, $this->serverPassword);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();

		if ($result->num_rows == 0)
			die(
				json_encode(
					array(
						"code" => 409,
						"message" => "Generic error on reservation"
					)
				)
			);

		if ($result->num_rows > 0) {
			while ($row = $result->fetch_array())
				$this->response = $row["response"];
		}
	}
}

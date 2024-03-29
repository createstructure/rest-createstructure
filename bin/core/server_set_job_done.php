<?php
/**
 * Set job as done action
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

class ServerSetJobDone implements Action
{
	// class variabile(s)
	private $payload;
	private $conn;
	private $serverName;
	private $serverPassword;
	private $repoID;
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
		$this->repoID = $payload["repoID"];
	}

	/**
	 * Run main code
	 *
	 * @return array Array with the response, if there wasn"t any error
	 */
	public function run()
	{
		$this->conn = new Database();

		$this->setJobDone();

		$this->conn->closeConnection();

		$res = array();

		switch ($this->response) {
			case 401:
				$res = array(
					"code" => 401,
					"message" => "Wrong credentials"
				);
				break;
			case 409:
				$res = array(
					"code" => 409,
					"message" => "Repo already setted as done"
				);
				break;
			case "504":
			case 504:
				$res = array(
					"code" => 504,
					"message" => "There is a problem, the DB seems to be full of work, please try again later"
				);
				break;
			case 200:
				$res = array(
					"code" => 200,
					"message" => "Setted repo as done"
				);
				break;
			default:
				$res = array(
					"code" => 400,
					"message" => "Generic error"
				);
		}
		return $res;
	}

	/**
	 * Get reserveted repo information
	 */
	private function setJobDone()
	{
		// Get user id
		$query = "SELECT ServerSetJobDone(?, ?, ?) AS response;";
		// prepare and execute query
		$stmt = $this->conn->getConnection()->stmt_init();
		$stmt->prepare($query);
		$stmt->bind_param("sss", $this->serverName, $this->serverPassword, $this->repoID);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();

		if ($result->num_rows == 0)
			die(
				json_encode(
					array(
						"code" => 409,
						"message" => "Generic error on setting job as done"
					)
				)
			);

		if ($result->num_rows > 0) {
			while ($row = $result->fetch_array())
				$this->response = $row["response"];
		}
	}
}

<?php
	/**
	 * Set priority as done action 
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
	
	class ServerSetPriorityDone implements Action{
		// class variabile(s)
		private $payload;
		private $conn;
		private $server_name;
		private $server_password;
		private $priorityID;
		private $response;
		
		/**
		 * Constructor
		 * 
		 * @param payload		The payload of the request
		 */ 
		public function __construct($payload){
			$this->payload = $payload;
			$this->server_name = $payload["server_name"];
			$this->server_password = $payload["server_password"];
			$this->priorityID = $payload["priorityID"];
		}
		
		/**
		 * Run main code
		 * 
		 * @return array Array with the response, if there wasn"t any error
		 */ 
		public function run() {
			$this->conn = new Database();

			$this->setPriorityDone();

			$this->conn->close_connection();

			switch ($this->response) {
				case 401:
					return array(
						"code" => 401,
						"message" => "Wrong credentials"
					);
				
				case 409:
					return array(
						"code" => 409,
						"message" => "Priority already setted as done"
					);
				case "504":
				case 504:
					return array(
						"code" => 504,
						"message" => "There is a problem, the DB seems to be full of work, please try again later"
					);
				case 200:
					return array(
						"code" => 200,
						"message" => "Setted priority as done"
					);
			}
		}

		/**
		 * Set priority as done
		 */
		private function setPriorityDone() {
			// Get user id
			$query = "SELECT ServerSetPriorityDone(?, ?, ?) AS response;";
			// prepare and execute query
			$stmt = $this->conn->get_connection()->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("sss", $this->server_name, $this->server_password, $this->priorityID);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
			
			if ($result->num_rows == 0)
				die(
					json_encode(
						array(
							"code" => 409,
							"message" => "Generic error on setting priority as done"
							)
						)
					);
				
			if($result->num_rows > 0){
				while ($row = $result->fetch_array())
					$this->response = $row["response"];
			}
		}
	}	
?>

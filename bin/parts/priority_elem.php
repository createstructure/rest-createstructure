<?php
	/**
	 * Manage priority elements
	 *
	 * PHP version 7.4.16
	 *
	 * @package    rest-createstructure
	 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
	 * @license    GNU
	 * @link       https://github.com/createstructure/rest-createstructure
	 */	
	class Priority{
		// class variabile(s)
		private $conn;
		private $table_name = "server_priority";
		private $table_name2 = "server_priority_log";
		private $table_name4 = "server_priority_reserve_ID";
		private $table_status = "server_priority_status";
		private $table_server = "server";
		private $table_instruction = "server_instruction";
		private $table_server_status = "server_status";
		private $table_secrets = "server_secrets";
		private $table_users = "super_user";
		private $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		private $nums = "0123456789";
		private $priority_id;
		
		/**
		 * Constructor
		 * 
		 * @param db			The DB connection
		 */ 
		public function __construct($db){
			$this->conn = $db;
		}
		
		/**
		 * Check the server
		 * 
		 * @param server_id		The server id
		 * @param server_code	The server code
		 */
		private function serverCheck($server_id, $server_code) {
			$server_id = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->nums, "/")."]/", '', $server_id)));
			$server_code = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->chars . $this->nums, "/")."]/", '', $server_code)));
			
			$query = "SELECT 
						0
					FROM
						" . $this->table_server . " first
					LEFT JOIN
						" . $this->table_server_status . " second
						ON
							first.request_state_id = second.status_id
					LEFT JOIN
						" . $this->table_secrets . " third
						ON
							first.server_id = third.server_id
					WHERE
						first.server_id = ? AND
						third.server_code = ?
					GROUP BY
						first.server_id
					HAVING
						MAX( second.description ) = 'accepted'
					LIMIT
						1";

			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("ss", $server_id, $server_code);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
			
			if ($result->num_rows == 0)
				die(json_encode(
					array(
						"message" => "error",
						"error" => "Error giving server id."
						)
					));
		}
		
		/**
		 * Check the user
		 * 
		 * @param username		The username
		 * @param token			The user token
		 */
		private function userCheck($username, $token) {
			// Curl auth call
			$ch = curl_init("https://api.github.com/user");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_USERAGENT, "createstructure");
			curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $token);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			if (!$result)
				trigger_error(curl_error($ch));
			curl_close($ch);

			// Check results
			if (json_decode($result, true)["login"] != $username)
				die(json_encode(
					array(
						"message" => "error",
						"error" => "Error user auth failed."
						)
					));
					
			// Get user id
			$query = "SELECT 
						MAX(id) AS user_id
					FROM 
						" . $this->table_users . "
					WHERE 
						username = ?
					GROUP BY 
						username";

			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
			
			if ($result->num_rows == 0)
				die(json_encode(
					array(
						"message" => "error",
						"error" => "Error getting user last id."
						)
					));
				
			if($result->num_rows > 0){
				while ($row = $result->fetch_array())
					$id = $row["user_id"];
			}
					
			// Get if the user is active or not
			$query = "SELECT 
						id
					FROM 
						" . $this->table_users . "
					WHERE
						id = ? &
						active = 1";

			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
			
			if ($result->num_rows == 0)
				die(json_encode(
					array(
						"message" => "error",
						"error" => "You are not a superuser"
						)
					));
		}
		
		/**
		 * Get the user id
		 * 
		 * @param username		The username
		 * @return user_id		The user id
		 */
		private function getUserID($username) {
			$username = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->chars . $this->nums, "/")."]/", '', $username)));
			$query = "SELECT
							*
						FROM
							" . $this->table_users . "
						WHERE
							username=?
						LIMIT
							1";

			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
		
			
			if ($result->num_rows == 0)
				die(json_encode(
					array(
						"message" => "error",
						"error" => "User not founded."
						)
					));
			
			if($result->num_rows > 0){				
				while ($row = $result->fetch_array())
					return $row["id"];
			}
		}
		
		/**
		 * Get status code by description
		 * 
		 * @param status_description	The status description
		 * @return status_id			The status id
		 */
		private function getStatusCode($status_description) {
			// Get to do code
			$query = "SELECT
						*
					FROM 
						" . $this->table_status . " 
					WHERE
						description = ?";

			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("s", $status_description);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
			
			if ($result->num_rows == 0)
				die(json_encode(
					array(
						"message" => "error",
						"error" => "Error getting status id."
						)
					));
				
			if($result->num_rows > 0){
				while ($row = $result->fetch_array())
					return $row["priority_state_id"];
			}
		}
		
		/**
		 * Get instruction code by description
		 * 
		 * @param instruction_description	The instruction description
		 * @return instruction_id			The instruction id
		 */
		private function getInstructionCode($instruction_description) {
			// Get to do code
			$query = "SELECT
						*
					FROM 
						" . $this->table_instruction . " 
					WHERE
						description = ?";

			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("s", $instruction_description);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
			
			if ($result->num_rows == 0)
				die(json_encode(
					array(
						"message" => "error",
						"error" => "Error getting instruction id."
						)
					));
				
			if($result->num_rows > 0){
				while ($row = $result->fetch_array())
					return $row["instruction_id"];
			}
		}
		
		/**
		 * Get instruction description by code
		 * 
		 * @return code						The instruction id
		 * @param instruction_description	The instruction description
		 */
		private function getInstructionByCode($code) {
			// Get to do code
			$query = "SELECT
						*
					FROM 
						" . $this->table_instruction . " 
					WHERE
						instruction_id = ?";

			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("s", $code);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
			
			if ($result->num_rows == 0)
				die(json_encode(
					array(
						"message" => "error",
						"error" => "Error getting instruction by code."
						)
					));
				
			if($result->num_rows > 0){
				while ($row = $result->fetch_array())
					return $row["priority_instruction"];
			}
		}
		
		/**
		 * Get work id
		 * 
		 * @param server_id		The server id
		 * @param server_code	The server code
		 * @return work 		The work id if it exists
		 */
		public function getWorkID($server_id, $server_code) {
			$this->serverCheck($server_id, $server_code);
			return $this->priority_id;
		}
		
		/**
		 * Mark a work as finished
		 * 
		 * @param server_id		The server id
		 * @param server_code	The server code
		 * @param work_id		The work id
		 */
		public function finishWork($server_id, $server_code, $work_id) {
			$this->serverCheck($server_id, $server_code);
			
			$query = "INSERT INTO
						" . $this->table_name2 . " 
						(priority_id, status_id, description)
					VALUES
						(?, ?, '')";

			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("ss", $work_id, $this->getStatusCode("Done"));
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
		}
		
		/**
		 * Get a new element
		 * 
		 * @param server_id		The server id
		 * @param server_code	The server code
		 */
		public function getElement($server_id, $server_code){
			$this->serverCheck($server_id, $server_code);
			
			// select all query
			$query = "SELECT
						first.priority_id as c_id, MAX(second.priority) as priority_code
					FROM
						" . $this->table_name2 . " first
						LEFT JOIN 
							" . $this->table_name . " second
						    ON 
							    second.priority_id = first.priority_id
					GROUP BY
						first.priority_id
					HAVING
						MAX(first.status_id) = ? AND
						MAX(second.priority_server_id) = ?
					LIMIT
						1";

			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("ss", $this->getStatusCode("To do"), $server_id);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
			$possible = "";
			if($result->num_rows > 0) {	
				while ($row = $result->fetch_array()) {
					$this->priority_id = $row["c_id"];
					return $this->getInstructionByCode($row["priority_code"]);
				}
			}
			
			return ""; // No data found
		}
		
		/**
		 * Get execution ID
		 * 
		 * @return id 				The new id
		 */
		private function getEsecID() {	
			// Prenote a new job
			$query = "INSERT INTO
						" . $this->table_name4 . " 
						(priority_id, description)
					SELECT
						MAX(first.priority_id) + ?, ''
						FROM
							" . $this->table_name2 . " first";

			// prepare and execute query
			$increment = "1";
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("s", $increment);
			$stmt->execute();
			$stmt->get_result();			
			$stmt->close();
			
			// Get the priority_id of the created item
			$query = "SELECT
						*
					FROM
						" . $this->table_name4 . "
					WHERE
						id = ?
					LIMIT
						1";

			$my_id = $this->conn->insert_id;
			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("i", $my_id);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
			
			if($result->num_rows > 0)	
				while ($row = $result->fetch_array())
					$priority_id = $row["priority_id"];
			else
				return;
				
			// Sleep and see if this server is "the winner"
			usleep( 50 * 1000 );
			
			// Get the winner of the created item
			$query = "SELECT
						*
					FROM
						" . $this->table_name4 . " 
					WHERE
						priority_id = ?
					LIMIT
						1";

			$id = $this->conn->insert_id;
			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("i", $priority_id);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
			
			if($result->num_rows > 0)	
				while ($row = $result->fetch_array())
					$winner_id = $row["id"];
			else
				return;
			
			if ($my_id == $winner_id)
			{
				$query = "INSERT 
						INTO
							" . $this->table_name2 . " 
							(`priority_id`, `status_id`, `description`)
						VALUES
							(?, ?, '')";

				// prepare and execute query
				$stmt = $this->conn->stmt_init();
				$stmt->prepare($query);
				$stmt->bind_param("ss", $priority_id, $this->getStatusCode("To do"));
				$stmt->execute();
				$stmt->get_result();			
				$stmt->close();
				
				return $priority_id;
			} 
			/*else {
				echo "someone else was faster";
			}*/
			return $this->getEsecID($userID, $projectName);
		}
		
		/**
		 * Create a new job to do
		 * 
		 * @param input				An array with all inputs
		 * @return message			A message of the insert
		 */
		public function create($input){
			// query to insert record
			$query = "INSERT 
					INTO 
						" . $this->table_name . "
						(`priority_id`, `priority_server_id`, `priority`, `description`) 
					VALUES 
						(?, ?, ?, '')";

			// prepare query
			$stmt = $this->conn->prepare($query);

			// sanitize
			$data = array();
			$data["username"] = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->chars . $this->nums, "/")."]/", '', $input["username"])));
			$data["token"] = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->chars . $this->nums, "/")."]/", '', $input["token"])));
			$data["server_id"] = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->nums, "/")."]/", '', $input["server_id"])));
			$data["instruction"] = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->chars, "/")."]/", '', $input["instruction"])));

			$this->userCheck($data["username"], $data["token"]);
			$user_id = $this->getUserID($data["username"]);
			$esec_id = $this->getEsecID();

			// bind values
			$stmt->bind_param("sss", $esec_id, $data["server_id"], $this->getInstructionCode($data["instruction"]));
			
			// execute query
			if($stmt->execute()){
				return "Request pushed in the queue.";
			}

			return "Generic error trying to accept the request.";

		}
	}
?>

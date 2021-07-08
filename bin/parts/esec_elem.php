<?php
	/**
	 * Manage 
	 *
	 * PHP version 7.4.16
	 *
	 * @package    rest-createstructure
	 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
	 * @license    GNU
	 * @link       https://github.com/createstructure/rest-createstructure
	 */	
	include_once '../parts/crypto_elem.php';
	include_once '../config/database.php';
	
	class Esec{
		// class variabile(s)
		private $conn;
		private $table_name = "`create`";
		private $table_name2 = "create_log";
		private $table_name3 = "create_reservation";
		private $table_name4 = "create_reserve_ID";
		private $table_status = "esec_status";
		private $table_server = "server";
		private $table_server_status = "server_status";
		private $table_secrets = "server_secrets";
		private $table_users = "user";
		private $table_user_subscription = "subscription";
		private $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		private $nums = "0123456789";
		private $creation_id;
		private $crypto;
		
		/**
		 * Constructor
		 * 
		 * @param db			The DB connection
		 */ 
		public function __construct($db){
			$this->conn = $db;
			$crypto = new Crypto($this->conn);
		}
		
		/**
		 * Server check
		 * 
		 * @param server_id		The server id
		 * @param server_code	The server code
		 */ 
		private function serverCheck($server_id, $server_code) {
			// Sanitize
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
			
			// Check server
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

			// Get user subscription
			$query = "SELECT 
						second.description AS description, second.max_min AS max_min, second.max_h AS max_h, second.max_day AS max_day
					FROM 
						" . $this->table_users . " first
					LEFT JOIN
						" . $this->table_user_subscription . " second
					ON
						first.subscription_id = second.subscription_id
					WHERE 
						first.id = ?";

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
						"error" => "Error getting user subscription."
						)
					));
				
			if($result->num_rows > 0){
				while ($row = $result->fetch_array()){
					$subtype = $row["description"];
					$max = array();
					$max["min"] = $row["max_min"];
					$max["h"] = $row["max_h"];
					$max["day"] = $row["max_day"];
				}
			}
			
			if ($subtype == "disabled")
				die(json_encode(
					array(
						"message" => "error",
						"error" => "User subscription not already valid."
						)
					));

			// Check max usage
			if ($this->inLastPeriod($username, time() - 60) > $max["min"])
				die(json_encode(
					array(
						"message" => "error",
						"error" => "Reached max minute creations, with your subscription you can do max " . $max['min'] . " repo(s)/minute."
						)
					));
					
			if ($this->inLastPeriod($username, time() - 60*60) > $max["h"])
				die(json_encode(
					array(
						"message" => "error",
						"error" => "Reached max minute creations, with your subscription you can do max" . $max['h'] . " repo(s)/hour."
						)
					));	
					
			if ($this->inLastPeriod($username, time() - 60*60*24) > $max["day"])
				die(json_encode(
					array(
						"message" => "error",
						"error" => "Reached max minute creations, with your subscription you can do max" . $max['day'] . " repo(s)/day"
						)
					));			
		}
		
		/**
		 * Get the number of usages from ...
		 * 
		 * @param username			The username
		 * @param time				The time to check from
		 */
		private function inLastPeriod($username, $time) {
			// Get user id
			$query = "SELECT 
						MIN(id) AS user_id
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
						"error" => "Error getting user first id."
						)
					));
				
			if($result->num_rows > 0){
				while ($row = $result->fetch_array())
					$id = $row["user_id"];
			}
			
			$query = "SELECT 
						COUNT(0) AS n
					FROM 
						" . $this->table_name . "
					WHERE 
						user_id = ? AND
						`change` > ?";

			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("ss", $id, $from);
			$from = date("Y-m-d H:i:s", $time);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
			
			if ($result->num_rows == 0)
				die(json_encode(
					array(
						"message" => "error",
						"error" => "Error getting period of usage."
						)
					));
				
			if($result->num_rows > 0){
				while ($row = $result->fetch_array())
					return $row["n"];
			}
		}
		
		/**
		 * Get the status code by the status descrition
		 * 
		 * @param status_description	The status description
		 * @return status_code			The status code
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
					return $row["status_id"];
			}
		}
		
		/**
		 * Check if there is an element assigned yet
		 * 
		 * @param server_id		The server id
		 * @param server_code	The server code
		 * @return data			The data if the element exist yet, else ""
		 */
		private function checkElement($server_id, $server_code) {
			$this->serverCheck($server_id, $server_code);
			
			// select all query
			$query = "SELECT
						first.creation_id as c_id, MAX(second.cripted_data) as c_data
					FROM
						" . $this->table_name2 . " first
						LEFT JOIN 
							" . $this->table_name . " second
						    ON 
							    second.creation_id = first.creation_id
					GROUP BY
						first.creation_id
					HAVING
						MAX(first.status_id) = ? AND
						MAX(first.server_id) = ?
					LIMIT
						1";

			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("ss", $this->getStatusCode("Reserved"), $server_id);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
			$possible = "";
			if($result->num_rows > 0) {	
				while ($row = $result->fetch_array()) {
					$this->creation_id = $row["c_id"];
					return $row["c_data"];
				}
			}
			
			return ""; // No data found
		}
		
		/**
		 * Gets the work id
		 * 
		 * @return creation_id			The work id
		 */
		public function getWorkID($server_id, $server_code) {
			$this->serverCheck($server_id, $server_code);
			return $this->creation_id;
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
						(server_id, creation_id, status_id, description)
					VALUES
						(?, ?, ?, '')";

			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("sss", $server_id, $work_id, $this->getStatusCode("Done"));
			$stmt->execute();
			$stmt->get_result();			
			$stmt->close();
		}
		
		/**
		 * Mark a work as started
		 * 
		 * @param server_id		The server id
		 * @param server_code	The server code
		 * @param work_id		The work id
		 */
		public function startedWork($server_id, $server_code, $workId) {
			$this->serverCheck($server_id, $server_code);
			
			$query = "INSERT INTO
						" . $this->table_name2 . " 
						(server_id, creation_id, status_id, description)
					VALUES
						(?, ?, ?, '')";

			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("sss", $server_id, $workId, $this->getStatusCode("In progress"));
			$stmt->execute();
			$stmt->get_result();			
			$stmt->close();
		}
		
		/**
		 * Prenot a new item
		 * 
		 * @param server_id		The server id	
		 */
		private function prenoteItem ($server_id) {
			// Prenote a new job
			
			$query = "INSERT INTO
						" . $this->table_name3 . " 
						(server_id, creation_id, description)
					SELECT
						?, first.creation_id as c_id, ''
						FROM
							" . $this->table_name2 . " first
						GROUP BY
							first.creation_id
						HAVING
							MAX(first.status_id) = ? AND
							MAX(first.server_id) = 0
						LIMIT
							1";

			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("ss", $server_id, $this->getStatusCode("To do"));
			$stmt->execute();
			$stmt->get_result();			
			$stmt->close();
			
			// Get the creation_id of the created item
			$query = "SELECT
						*
					FROM
						" . $this->table_name3 . "
					WHERE
						id = ?
					LIMIT
						1";

			$id = $this->conn->insert_id;
			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
			
			if($result->num_rows > 0)	
				while ($row = $result->fetch_array())
					$creation_id = $row["creation_id"];
			else
				return;
				
			// Sleep and see if this server is "the winner"
			usleep( 50 * 1000 );
			
			// Get the winner of the created item
			$query = "SELECT
						*
					FROM
						" . $this->table_name3 . " 
					WHERE
						creation_id = ?
					ORDER BY
						id DESC
					LIMIT
						1";

			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("s", $creation_id);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
			
			if($result->num_rows > 0)	
				while ($row = $result->fetch_array())
					$winner_server_id = $row["server_id"];
			else
				return;
			
			if ($server_id == $winner_server_id)
			{
				$query = "INSERT INTO
							" . $this->table_name2 . " 
							(server_id, creation_id, status_id, description)
						VALUES
							(?, ?, ?, '')";

				// prepare and execute query
				$stmt = $this->conn->stmt_init();
				$stmt->prepare($query);
				$stmt->bind_param("sss", $server_id, $creation_id, $this->getStatusCode("Reserved"));
				$stmt->execute();
				$stmt->get_result();			
				$stmt->close();
			} 
			/*else {
				echo "someone else was faster";
			}*/
		}
		
		/**
		 * Get if there is/ are avariable item(s)
		 * 
		 * @return bool			true only if there is/ are avariable item(s)
		 */
		private function avariable() {
			// Get to do code	
			$query = "SELECT
						0
					FROM
						" . $this->table_name2 . "
					GROUP BY
						creation_id
					HAVING
						MAX(status_id) = ? AND
						MAX(server_id) = 0
					LIMIT
						1";

			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("s", $this->getStatusCode("To do"));
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
			
			return $result->num_rows > 0;
		}
		
		/**
		 * Get a new element
		 * 
		 * @param server_id		The server id
		 * @param server_code	The server code
		 */
		public function getElement($server_id, $server_code){
			$this->serverCheck($server_id, $server_code);
			
			$possible = $this->checkElement($server_id, $server_code);
			if ($possible != "")
				return $possible;

			if ($this->avariable()) {
				$this->prenoteItem ($server_id);
			
				// Sleep and try again
				usleep( 50 * 1000 );
				return $this->getElement($server_id, $server_code);
			} else {
				return json_encode (
					array (
						"message" => "no work to do"
					)
				);
			}
		}
		
		/**
		 * Gets the user id
		 * 
		 * @param username			The username
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
		 * Get execution ID
		 * 
		 * @param username			The username
		 * @param token				The user token
		 * @return id 				The new id
		 */
		private function getEsecID($username, $token) {
			$this->userCheck($username, $token);
			
			// Prenote a new job
			$query = "INSERT INTO
						" . $this->table_name4 . " 
						(creation_id, description)
					SELECT
						MAX(first.creation_id) + ?, ''
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
			
			// Get the creation_id of the created item
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
					$creation_id = $row["creation_id"];
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
						creation_id = ?
					LIMIT
						1";

			$id = $this->conn->insert_id;
			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("s", $creation_id);
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
				$query = "INSERT INTO
							" . $this->table_name2 . " 
							(server_id, creation_id, status_id, description)
						VALUES
							(0, ?, ?, '')";

				// prepare and execute query
				$stmt = $this->conn->stmt_init();
				$stmt->prepare($query);
				$stmt->bind_param("ss", $creation_id, $this->getStatusCode("To do"));
				$stmt->execute();
				$stmt->get_result();			
				$stmt->close();
				
				return $creation_id;
			} 
			/*else {
				echo "someone else was faster";
			}*/
			return $this->getEsecID($username, $token);
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
						(`user_id`, `cripted_data`, `creation_id`, `description`) 
					VALUES 
						(?, ?, ?, '')";

			// prepare query
			$stmt = $this->conn->prepare($query);

			// sanitize
			$data = array();
			$data["username"] = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->chars . $this->nums, "/")."]/", '', $input["username"])));
			$data["token"] = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->chars . $this->nums, "/")."]/", '', $input["token"])));
			$data["answers"] = array();
			$data["answers"]["name"] = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->chars . $this->nums, "/")."]/", '', $input["answers"]["name"])));
			$data["answers"]["template"] = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->chars . "-" . "\/", "/")."]/", '', $input["answers"]["template"])));
			$data["answers"]["descr"] = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->chars . $this->nums . " ", "/")."]/", '', $input["answers"]["descr"])));
			$data["answers"]["prefix"] = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->chars . $this->nums . "-", "/")."]/", '', $input["answers"]["prefix"])));
			$data["answers"]["team"] = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->chars . $this->nums, "/")."]/", '', $input["answers"]["team"])));
			$data["answers"]["private"] = (is_bool($input["answers"]["private"]) ? $input["answers"]["private"] : false);
			$data["answers"]["isOrg"] = (is_bool($input["answers"]["private"]) ? $input["answers"]["isOrg"] : false);
			if ($data["answers"]["isOrg"] == "true")
				$data["answers"]["org"] = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->chars . $this->nums, "/")."]/", '', $input["answers"]["org"])));

			$this->userCheck($data["username"], $data["token"]);
			$user_id = $this->getUserID($data["username"]);
			$encrypted_data = json_encode($this->crypto->encrypt(json_encode($data)));
			$esec_id = $this->getEsecID($data["username"], $data["token"]);

			// bind values
			$stmt->bind_param("iss", $user_id, $encrypted_data, $esec_id);
			
			// execute query
			if($stmt->execute()){
				return "Request pushed in the queue.";
			}

			return "Generic error trying to accept the request.";
		}
	}	
?>

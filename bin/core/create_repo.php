<?php
	/**
	 * Create Repo action 
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
	include_once "config/key.php";
    include_once "action.php";
	
	class CreateRepo implements Action{
		// class variabile(s)
		private $payload;
		private $conn;
		private $username;
		private $token;
		private $status_code;
		
		/**
		 * Constructor
		 * 
		 * @param payload		The payload of the request
		 */
		public function __construct($payload){
			$this->payload = $payload;
			$this->username = $payload["payload"]["username"];
			$this->token = $payload["payload"]["token"];
		}
		
		/**
		 * Run main code
		 * 
		 * @return array Array with the response, if there wasn"t any error
		 */ 
		public function run() {
			$this->conn = new Database();

			$this->checkRepoInfo();
			$this->checkGitHub();
			$this->createRepo();

			$this->conn->close_connection();

			switch ($this->status_code) {
				case "200":
				case 200:
					return array(
						"code" => 200,
						"message" => "Repo creation saved with success"
					);
				case "429":
				case 429:
				default:
					return array(
						"code" => 429,
						"message" => "You seem to have created more repo than your actual subscription. To change subscription: https://github.com/marketplace/createstructure"
					);

			}

		}

		/**
		 * Check the Github user account
		 */
		private function checkGitHub() {
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
		 * Checks repo info
		 */
		private function checkRepoInfo() {
			if(
				!(
					is_string($this->payload["payload"]["username"]) &&
					is_string($this->payload["payload"]["token"]) &&
					is_string($this->payload["payload"]["answers"]["name"]) &&
					(
						!isset($this->payload["payload"]["answers"]["template"]) ||
						is_string($this->payload["payload"]["answers"]["template"])
					) &&
					(
						!isset($this->payload["payload"]["answers"]["descr"]) ||
						is_string($this->payload["payload"]["answers"]["descr"])
					) &&
					(
						!isset($this->payload["payload"]["answers"]["prefix"]) ||
						is_string($this->payload["payload"]["answers"]["prefix"])
					) &&
					(
						!isset($this->payload["payload"]["answers"]["private"]) ||
						is_bool($this->payload["payload"]["answers"]["private"])
					) &&
					(
						!isset($this->payload["payload"]["answers"]["isOrg"]) ||
						(
							is_bool($this->payload["payload"]["answers"]["isOrg"]) &&
							(
								(
									$this->payload["payload"]["answers"]["isOrg"] &&
									is_string($this->payload["payload"]["answers"]["org"]) &&
									(
										!isset($this->payload["payload"]["answers"]["team"]) ||
										is_string($this->payload["payload"]["answers"]["team"])
									)
								) || 
									!$this->payload["payload"]["answers"]["isOrg"]
							)
						)
					)
					
				)
			)
			{
				die(
					json_encode(
						array(
							"code" => 400,
							"message" => "Inputs malformed"
							)
						)
					);
			}
		}

		/**
		 * Create the new repo
		 */
		private function createRepo() {
			// Get user id
			$query = "SELECT CreateRepo(?, ?) AS status_code;"; // status_code is the output of the CreateRepo function
			
			// prepare and execute query
			$stmt = $this->conn->get_connection()->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param(
				"ss",
				$this->username,
				$this->encrypt_DB($this->payload["payload"])
			);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
			
			if ($result->num_rows == 0)
				die(
					json_encode(
						array(
							"code" => 409,
							"message" => "Generic error in repo creation"
							)
						)
					);

			while ($row = $result->fetch_array())
				$this->status_code = $row["status_code"];
		}

		/**
		 * Encrypt for the DB
		 * 
		 * @param original		The original message
		 * @return encrypted	Encrypted message
		 */
		private function encrypt_DB($original) {
			// If not done before, convert $original to string
			if (!is_string($original))
				$original = json_encode($original);

			// Set output array
			$output = array();

			// Get key
			$key = new Key();

			// Encrypt every part of the original message and put them into the array
			foreach (
				str_split(
					$original,
					random_int(100, 200)
					) as $part
				) {
				openssl_public_encrypt($part, $encrypted, $key->get_public_key());

				$output[] = base64_encode($encrypted);
			}
			
			// Return the encrypted message
			return json_encode($output);
		}
	}	
?>

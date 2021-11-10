<?php
	/**
	 * Get job action (server) 
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
	
	class ServerGetJobInfo implements Action{
		// class variabile(s)
		private $payload;
		private $conn;
		private $server_name;
		private $server_password;
		private $server_public_key;
		private $repoID;
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
			$this->repoID = $payload["repoID"];
		}
		
		/**
		 * Run main code
		 * 
		 * @return array Array with the response, if there wasn"t any error
		 */ 
		public function run() {
			$this->conn = new Database();

			$this->getRepoInfo();
			$this->getServerPublicKey();

			$this->conn->close_connection();

			
			// Decrypt and encrypt again for the server
			$decrypted = $this->decrypt($this->response, true);
			$encrypted = array();
			for ($i = 0; $i < 16; $i++)
				$encrypted[] = $this->encrypt($decrypted);

			switch ($this->response) {
				case -1:
					return array(
						"code" => 401,
						"message" => "Wrong credentials"
					);

				default:
					return array(
						"code" => 200,
						"message" => "Reserved repo",
						"repo_info" => $encrypted
					);
			}
		}

		/**
		 * Get reserveted repo information
		 */
		private function getRepoInfo() {
			// Get user id
			$query = "SELECT ServerGetJobInfo(?, ?, ?) AS response;";

			// prepare and execute query
			$stmt = $this->conn->get_connection()->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param(
				"sss",
				$this->server_name,
				$this->server_password,
				$this->repoID
			);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
			
			if ($result->num_rows == 0)
				die(
					json_encode(
						array(
							"code" => 400,
							"message" => "Generic error getting repo info"
							)
						)
					);
				
			if($result->num_rows > 0){
				while ($row = $result->fetch_array())
					$this->response = $row["response"];
			}
		}

		/**
		 * Get server public key
		 */
		private function getServerPublicKey() {
			// Get user id
			$query = "SELECT ServerGetPublicKey(?, ?) AS public_key;";

			// prepare and execute query
			$stmt = $this->conn->get_connection()->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param(
				"ss",
				$this->server_name,
				$this->server_password
			);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();

			if ($result->num_rows == 0)
				die(
					json_encode(
						array(
							"code" => 409,
							"message" => "Generic error getting server public key"
							)
						)
					);
				
			if($result->num_rows > 0){
				while ($row = $result->fetch_array())
					$this->server_public_key = openssl_get_publickey($row["public_key"]);
			}
		}

		/**
		 * Decrypt by the DB
		 * 
		 * @param original		The crypted message
		 * @return encrypted	Decrypted message
		 */
		private function decrypt($original) {
			// Get key
			$key = new Key();

			// $original by Json string to array
			if (is_string($original))
				$original = json_decode($original, true);

			// Set the decrypt string
			$decrypted = "";

			// Decrypt every part of the original message
			foreach ($original as $part) {
				openssl_private_decrypt(base64_decode($part), $tmp, $key->get_private_key());
				
				$decrypted .= $tmp;
			}
			
			// return the string with the decrypted message
			return json_decode($decrypted);
		}

		/**
		 * Encrypt for the server
		 * 
		 * @param original		The original message
		 * @return encrypted	Encrypted message
		 */
		private function encrypt($original) {
			// If not done before, convert $original to string
			if (!is_string($original))
				$original = json_encode($original);

			// Set output array
			$output = array();

			// Encrypt every part of the original message and put them into the array
			foreach (
				str_split(
					$original,
					random_int(100, 200)
					) as $part
				) {
				openssl_public_encrypt("$part", $encrypted, $this->server_public_key);
				$output[] = base64_encode($encrypted);
			}
			
			// Return the encrypted message
			return json_encode($output);
		}
	}	
?>

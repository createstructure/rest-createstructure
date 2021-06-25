<?php
	/**
	 * Manage rest key(s)
	 *
	 * PHP version 7.4.16
	 *
	 * @package    rest-createstructure
	 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
	 * @license    GNU
	 * @link       https://github.com/createstructure/rest-createstructure
	 */
	
	class Key{
		// class variabile(s)
		private $conn;
		private $public_key;
		private $private_key;
		private $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		private $nums = "0123456789";
		private $server_keys = "server_secrets";

		/**
		 * Constructor
		 * 
		 * @param db			The DB connection
		 */ 
		public function __construct($db){
			$this->conn = $db;
			$this->public_key = file_get_contents("public.pem");
			$this->private_key = file_get_contents("private.pem");
		}
		
		/**
		 * Get the public key
		 *
		 * @return public_key	public key
		 */ 
		public function getPublicKey() {
			return $this->public_key;
		}
		
		/**
		 * Get the private key
		 *
		 * @return private_key	private key
		 */ 
		public function getPrivateKey() {
			return $this->private_key;
		}

		/**
		 * Get the server key
		 *
		 * @param server_id		server id
		 * @param server_code	server code
		 * @return server_key	server key
		 */ 
		public function getServerKey($server_id, $server_code) {
			// select all query
			$query = "SELECT
							*
						FROM
							`" . $this->server_keys . "`
						WHERE
							server_id=? AND 
							server_code=?
						LIMIT
							1";

			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param(
						"ss", 
						htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->nums, "/")."]/", '', $server_id))),
						htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->chars . $this->nums, "/")."]/", '', $server_code)))
					);
			$stmt->execute();
			$data = $stmt->get_result();			
			$stmt->close();
			
			if($data->num_rows == 1){
				while ($row = $data->fetch_array()) {
					return $row["key"];
				}
			} else {

				// set response code - 404 Not found
				http_response_code(404);

				// tell the user no products found
				die(
					json_encode(
						array(
								"message" => "error",
								"error" => "No key found."
								)
						)
					);
			}
		}
	}
?>

<?php
	/**
	 * Utility to create a new user
	 *
	 * PHP version 7.4.16
	 *
	 * @package    rest-createstructure
	 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
	 * @license    GNU
	 * @link       https://github.com/createstructure/rest-createstructure
	 */

	class Create{
		// class variabile(s)
		private $conn;
		private $privateKey;
		private $table_users = "user";
		private $table_user_subscription = "subscription";
		private $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		private $nums = "0123456789";
		private $creation_id;

		/**
		 * Constructor
		 * 
		 * @param db			The DB connection
		 */ 
		public function __construct($db){
			$this->conn = $db;
		}
		
		/**
		 * Create the new user
		 *
		 * @param username			the username to add
		 * @param sub_id			the subscrition id
		 */ 
		public function create($username, $sub_id){
			// Query to insert record
			$query = "INSERT 
					INTO 
						" . $this->table_users . "
						(`username`, `subscription_id`, `description`) 
					VALUES 
						(?, ?, '')";

			// Prepare query
			$stmt = $this->conn->prepare($query);

			// Sanitize
			$username = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->chars . $this->nums. "-", "/")."]/", '', $username)));
			$sub_id = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($this->nums, "/")."]/", '', strval($sub_id))));
			
			// Bind values
			$stmt->bind_param("ss", $username, $sub_id);
			
			// Execute query
			if($stmt->execute()){
				echo json_encode(
							array(
								"message" => "added, changed or removed user",
								"username" => $username,
								"sub_id" => $sub_id
								)
						);
			} else {
				die(
					json_encode(
						array(
								"message" => "error",
								"error" => "Unable to add a new user"
						)
					)
				);
			}
		}
	}	
?>

<?php
	/**
	 * Manage DB
	 *
	 * PHP version 7.4.16
	 *
	 * @package    rest-createstructure
	 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
	 * @license    GNU
	 * @link       https://github.com/createstructure/rest-createstructure
	 */

	class Database{
		// class variabile(s)
		private $servername = "<YOUR_DB_NAME>"; // TODO
		private $username = "<YOUR_DB_USERNAME>"; // TODO
		private $password = "<YOUR_DB_PASSWORD>"; // TODO
		private $dbname = "<YOUR_DB_TABLE_NAME>"; // TODO
		private $conn;

		/**
		 * Constructor
		 */
		public function __construct(){
			try{
				$this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
				if ($this->conn->connect_errno)
					throw new Exception($this->conn->connect_error);
			}catch(Exception $e){
				die(
					json_encode(
						array(
								"message" => "error",
								"error" => "Connection error: " . $e->getMessage()
							)
						)
					);
			}
		}

		/**
		 * Get the database connection
		 *
		 * @return DB 			DB connection
		 */ 
		public function get_connection(){
			return $this->conn;
		}

		/**
		 * Close the database connection
		 */ 
		public function close_connection(){
			$this->conn->close();
		}
	}
?>


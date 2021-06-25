<?php
	/**
	 * Manage DB credentials
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
		private $servername = "<YOUR_DB_NAME>";
		private $username = "<YOUR_DB_USERNAME>";
		private $password = "<YOUR_DB_PASSWORD>";
		private $dbname = "<YOUR_DB_TABLE_NAME>";
		private $conn;

		/**
		 * Constructor
		 */ 
		public function __construct(){
			try{
				$this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
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
		public function getConnection(){
			return $this->conn;
		}
	}
?>
<?php
	/**
	 * Manage dictionary
	 *
	 * PHP version 7.4.16
	 *
	 * @package    rest-createstructure
	 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
	 * @license    GNU
	 * @link       https://github.com/createstructure/rest-createstructure
	 */

	class Languages{
		// class variabile(s)
		private $conn;
		private $table_name = "languages";
		private $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		private $nums = "0123456789";

		/**
		 * Constructor
		 * 
		 * @param db			The DB connection
		 */ 
		public function __construct($db){
			$this->conn = $db;
		}
		
		/**
		 * Read all dictionary
		 * 
		 * @return dictionary	The dictionary
		 */
		public function read(){
			// select all query
			$query = "SELECT 
							*
						FROM
							`" . $this->table_name . "` first
						ORDER BY
							first.id ASC";

			// execute query
			$query = $this->conn->query($query);

			return $query;
		}
		
		/**
		 * Read an item of dictionary
		 * 
		 * @param id 			The item id
		 * @return dictionary	The dictionary item info
		 */
		public function readOne($id){
			// select all query
			$query = "SELECT
							*
						FROM
							`" . $this->table_name . "` first
						WHERE
							first.id=?
						LIMIT
							1";

			// prepare and execute query
			$stmt = $this->conn->stmt_init();
			$stmt->prepare($query);
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$result = $stmt->get_result();			
			$stmt->close();
			
			return $result;
		}
	}
?>

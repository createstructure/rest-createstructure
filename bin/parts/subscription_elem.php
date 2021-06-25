<?php
	/**
	 * Manage suscriptions
	 *
	 * PHP version 7.4.16
	 *
	 * @package    rest-createstructure
	 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
	 * @license    GNU
	 * @link       https://github.com/createstructure/rest-createstructure
	 */

	class Subscription{
		// class variabile(s)
		private $conn;
		private $table_name = "subscription";
		
		/**
		 * Constructor
		 * 
		 * @param db			The DB connection
		 */ 
		public function __construct($db){
			$this->conn = $db;
		}
		
		/**
		 * Read all the items in the suscription list
		 * 
		 * @return suscription	The suscription items info
		 */
		public function read(){
			// select all query
			$query = "SELECT 
							*
						FROM
							`" . $this->table_name . "` first
						LEFT JOIN dictionary second
							ON first.subscription_lang = second.phrase_id
						ORDER BY
							first.subscription_id ASC";

			// execute query
			$query = $this->conn->query($query);

			return $query;
		}
		
		/**
		 * Read an item of the suscription list
		 * 
		 * @param id 			The item id
		 * @return suscription	The suscription item info
		 */
		public function readOne($id){
			// select all query
			$query = "SELECT
							*
						FROM
							`" . $this->table_name . "` first
						LEFT JOIN dictionary second
								ON first.subscription_lang = second.phrase_id
						WHERE
							first.subscription_id=?
						ORDER BY
							first.subscription_id ASC
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

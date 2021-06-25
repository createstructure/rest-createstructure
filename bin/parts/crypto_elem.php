<?php
	/**
	 * Manage encryptation & decryptation
	 *
	 * PHP version 7.4.16
	 *
	 * @package    rest-createstructure
	 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
	 * @license    GNU
	 * @link       https://github.com/createstructure/rest-createstructure
	 */
	include_once '../config/key.php';
	
	class Crypto{
		// class variabile(s)
		private $conn;
		private $publicKey;
		private $privateKey;
		private $serverKey;

		/**
		 * Constructor
		 * 
		 * @param db			The DB connection
		 * @param server_id		The server which do the request 
		 * @param server_code	The server which do the request 
		 */ 
		public function __construct($db, $server_id="", $server_code=""){
			// Setup DB connection
			$this->conn = $db;
			
			$key = new Key($this->conn);

			// Get public key for encryption
			if ($server_id != "" || $server_code != "")
				$this->serverKey = openssl_get_publickey($key->getServerKey($server_id, $server_code));
			
			$this->publicKey = openssl_get_publickey($key->getPublicKey());
			
			$this->privateKey = openssl_get_privatekey($key->getPrivateKey());
		}
		
		/**
		 * Encrypt data for the server
		 * 
		 * @param original		The original message
		 */
		function server_encrypt($original) {
			// Set output array
			$output = array();

			// Encrypt every part of the original message and put them into the array
			foreach (str_split($original, random_int(100, 200)) as $part) {
				openssl_public_encrypt($part, $encrypted, $this->serverKey);
				array_push($output, bin2hex($encrypted));
			}
			
			// Return the array with the encryptions
			return $output;
		}
		
		/**
		 * Encrypt for the DB
		 * 
		 * @param original		The original message
		 */
		function encrypt($original) {
			// Set output array
			$output = array();

			// Encrypt every part of the original message and put them into the array
			foreach (str_split($original, random_int(100, 200)) as $part) {
				openssl_public_encrypt($part, $encrypted, $this->publicKey);
				array_push($output, bin2hex($encrypted));
			}
			
			// Return the array with the encryptions
			return $output;
		}

		/**
		 * Decrypt by the DB
		 * 
		 * @param original		The crypted message
		 */
		function decrypt($original) {
			// Set the decrypt string
			$decrypted = "";

			// Decrypt every part of the original message
			foreach ($original as $part) {
				openssl_private_decrypt(hex2bin($part), $tmp, $this->privateKey);
				$decrypted .= $tmp;
			}
			
			// return the string with the decrypted message
			return $decrypted;
		}
	}
?>

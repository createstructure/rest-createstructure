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
		private $public_key_string = ""; // TODO
		private $private_key_string = ""; // TODO
		private $public_key;
		private $private_key;

		/**
		 * Constructor
		 */ 
		public function __construct(){
			$this->public_key = openssl_get_publickey($this->public_key_string);
			$this->private_key = openssl_get_privatekey($this->private_key_string);
		}
		
		/**
		 * Get the public key
		 *
		 * @return public_key	public key
		 */ 
		public function get_public_key() {
			return $this->public_key;
		}
		
		/**
		 * Get the private key
		 *
		 * @return private_key	private key
		 */ 
		public function get_private_key() {
			return $this->private_key;
		}
	}
?>


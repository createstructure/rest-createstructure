<?php
	/**
	 * Action interface 
	 *
	 * PHP version 7.4.16
	 *
	 * @package    rest-createstructure
	 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
	 * @license    GNU
	 * @link       https://github.com/createstructure/rest-createstructure
	 */
	
	interface Action{
		/**
		 * Constructor
		 * 
		 * @param payload		The payload of the request
		 */
		public function __construct($payload);

		/**
		 * Run main code
		 * 
		 * @return array Array with the response, if there wasn't any error
		 */ 
		public function run();
	}	
?>

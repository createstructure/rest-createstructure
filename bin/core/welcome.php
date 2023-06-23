<?php
/**
 * Welcome action
 *
 * PHP version 7.4.16
 *
 * @package    rest-createstructure
 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
 * @license    GNU
 * @link       https://github.com/createstructure/rest-createstructure
 */

// Import(s)
include_once "action.php";

class Welcome implements Action
{
	// class variabile(s)
	private $payload;
	private $messages;

	/**
	 * Constructor
	 *
	 * @param mixed		The payload of the request
	 */
	public function __construct($payload)
	{
		$this->payload = $payload;
		$this->messages = array(
			"Hi",
			"Hello",
			"Welcome to createstructure",
			"Random welcome message"
		);
	}

	/**
	 * Run main code
	 *
	 * @return array Array with the response, if there wasn"t any error
	 */
	public function run()
	{
		return array(
			"code" => 200,
			"message" => $this->messages[array_rand($this->messages)]
		);
	}
}

<?php
/**
 * Manage Webhook secret(s)
 *
 * PHP version 7.4.16
 *
 * @package    rest-createstructure
 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
 * @license    GNU
 * @link       https://github.com/createstructure/rest-createstructure
 */

class Webhook
{
	// class variabile(s)
	private $secret = ""; // TODO

	/**
	 * Constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Get the database connection
	 *
	 * @return string 	Webhook secret
	 */
	public function getSecret()
	{
		return $this->secret;
	}
}

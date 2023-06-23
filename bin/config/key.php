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

class Key
{
	// class variabile(s)
	private $publicKeyString = "<publicKey>"; // TODO
	private $privateKeyString = "<privateKey>"; // TODO
	private $publicKey;
	private $privateKey;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->publicKey = openssl_get_publickey($this->publicKeyString);
		$this->privateKey = openssl_get_privatekey($this->privateKeyString);
	}

	/**
	 * Get the public key
	 *
	 * @return OpenSSLAsymmetricKey	public key
	 */
	public function getPublicKey()
	{
		return $this->publicKey;
	}

	/**
	 * Get the private key
	 *
	 * @return OpenSSLAsymmetricKey	private key
	 */
	public function getPrivateKey()
	{
		return $this->privateKey;
	}
}

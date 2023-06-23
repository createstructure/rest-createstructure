<?php
/**
 * Create Repo action
 *
 * PHP version 7.4.16
 *
 * @package    rest-createstructure
 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
 * @license    GNU
 * @link       https://github.com/createstructure/rest-createstructure
 */

// Import(s)
include_once "config/database.php";
include_once "config/key.php";
include_once "action.php";

class CreateRepo implements Action
{
	// class variabile(s)
	private $payload;
	private $conn;
	private $username;
	private $token;
	private $statusCode;

	/**
	 * Constructor
	 *
	 * @param mixed		The payload of the request
	 */
	public function __construct($payload)
	{
		$this->payload = $payload;
		$this->username = $payload["payload"]["username"];
		$this->token = $payload["payload"]["token"];
	}

	/**
	 * Run main code
	 *
	 * @return array Array with the response, if there wasn"t any error
	 */
	public function run()
	{
		$this->conn = new Database();

		$this->checkRepoInfo();
		$this->checkGitHub();
		$this->createRepoCore();

		$this->conn->closeConnection();

		$res = array();

		switch ($this->statusCode) {
			case "200":
			case 200:
				$res = array(
					"code" => 200,
					"message" => "Repo creation saved with success"
				);
				break;
			case "504":
			case 504:
				$res = array(
					"code" => 504,
					"message" => "There is a problem, the DB seems to be full of work, please try again later"
				);
				break;
			case "429":
			case 429:
			default:
				$res = array(
					"code" => 429,
					"message" => "You seem to have created more repo than your actual subscription. To change subscription: https://github.com/marketplace/createstructure"
				);
		}
		return $res;
	}

	/**
	 * Check the Github user account
	 */
	private function checkGitHub()
	{
		// Setup request to GitHub REST API
		$ch = curl_init("https://api.github.com/user");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, "createstructure");
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->token);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		if (!$result)
			trigger_error(curl_error($ch));
		curl_close($ch);

		// Check results
		if (json_decode($result, true)["login"] != $this->username)
			die(
				json_encode(
					array(
						"code" => 401,
						"message" => "GitHub credentials are not corrent"
					)
				)
			);
	}

	/**
	 * Checks repo info
	 */
	private function checkRepoInfo()
	{
		if (
			!(
				is_string($this->payload["payload"]["username"]) &&
				is_string($this->payload["payload"]["token"]) &&
				is_string($this->payload["payload"]["answers"]["name"]) &&
				(
					!isset($this->payload["payload"]["answers"]["template"]) ||
					is_string($this->payload["payload"]["answers"]["template"])
				) &&
				(
					!isset($this->payload["payload"]["answers"]["descr"]) ||
					is_string($this->payload["payload"]["answers"]["descr"])
				) &&
				(
					!isset($this->payload["payload"]["answers"]["prefix"]) ||
					is_string($this->payload["payload"]["answers"]["prefix"])
				) &&
				(
					!isset($this->payload["payload"]["answers"]["private"]) ||
					is_bool($this->payload["payload"]["answers"]["private"])
				) &&
				(
					!isset($this->payload["payload"]["answers"]["isOrg"]) ||
					(
						is_bool($this->payload["payload"]["answers"]["isOrg"]) &&
						(
							(
								$this->payload["payload"]["answers"]["isOrg"] &&
								is_string($this->payload["payload"]["answers"]["org"]) &&
								(
									!isset($this->payload["payload"]["answers"]["team"]) ||
									is_string($this->payload["payload"]["answers"]["team"])
								)
							) ||
							!$this->payload["payload"]["answers"]["isOrg"]
						)
					)
				)

			)
		) {
			die(
				json_encode(
					array(
						"code" => 400,
						"message" => "Inputs malformed"
					)
				)
			);
		}
	}

	/**
	 * Create the new repo
	 */
	private function createRepoCore()
	{
		// Get user id
		$query = "SELECT CreateRepo(?, ?) AS statusCode;"; // statusCode is the output of the CreateRepo function

		// prepare and execute query
		$stmt = $this->conn->getConnection()->stmt_init();
		$stmt->prepare($query);
		$encryptedDB = $this->encryptDB($this->payload["payload"]);
		$stmt->bind_param(
						"ss",
						$this->username,
						$encryptedDB
					);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();

		if ($result->num_rows == 0)
			die(
				json_encode(
					array(
						"code" => 409,
						"message" => "Generic error in repo creation"
					)
				)
			);

		while ($row = $result->fetch_array())
			$this->statusCode = $row["statusCode"];
	}

	/**
	 * Encrypt for the DB
	 *
	 * @param mixed		The original message
	 * @return mixed	Encrypted message
	 */
	private function encryptDB($original)
	{
		// If not done before, convert $original to string
		if (!is_string($original))
			$original = json_encode($original);

		// Set output array
		$output = array();

		// Get key
		$key = new Key();

		// Encrypt every part of the original message and put them into the array
		foreach (str_split($original, random_int(100, 200)) as $part) {
			openssl_public_encrypt($part, $encrypted, $key->getPublicKey());

			$output[] = base64_encode($encrypted);
		}

		// Return the encrypted message
		return json_encode($output);
	}
}

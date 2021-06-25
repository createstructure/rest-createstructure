<?php
	/**
	 * Create a user
	 *
	 * PHP version 7.4.16
	 *
	 * @package    rest-createstructure
	 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
	 * @license    GNU
	 * @link       https://github.com/createstructure/rest-createstructure
	 */
	include_once '../config/database.php';
	include_once '../config/webhook.php';
	include_once '../parts/create_user_elem.php';

	// required headers
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

	// local variabile(s)
	$database = new Database();
	$db = $database->getConnection();
	$webhook = new Webhook();
	$hookSecret = $webhook->getSecret();
	$create = new Create($db);

	// Check singnature avariability
	if (!isset($_SERVER['HTTP_X_HUB_SIGNATURE'])) {
		die("HTTP header 'X-Hub-Signature' is missing.");
	} elseif (!extension_loaded('hash')) {
		die("Missing 'hash' extension to check the secret code validity.");
	}

	// Get singnature
	list($algo, $hash) = explode('=', $_SERVER['HTTP_X_HUB_SIGNATURE'], 2) + array('', '');
	if (!in_array($algo, hash_algos(), true)) {
		die("Hash algorithm '$algo' is not supported.");
	}

	// Check singnature
	$rawPost = file_get_contents('php://input');
	if (!hash_equals($hash, hash_hmac($algo, $rawPost, $hookSecret))) {
		die('Hook secret does not match.');
	}

	// Check content
	if (!isset($_SERVER['CONTENT_TYPE'])) {
		die("Missing HTTP 'Content-Type' header.");
	} elseif (!isset($_SERVER['HTTP_X_GITHUB_EVENT'])) {
		die("Missing HTTP 'X-Github-Event' header.");
	}

	// Get input in any modality (xml and json)
	switch ($_SERVER['CONTENT_TYPE']) {
		case 'application/json':
			$json = $rawPost ?: file_get_contents('php://input');
			break;

		case 'application/x-www-form-urlencoded':
			$json = $_POST['payload'];
			break;

		default:
			die("Unsupported content type: $_SERVER[CONTENT_TYPE]");
	}

	# Payload structure depends on triggered event
	# https://developer.github.com/v3/activity/events/types/
	$payload = json_decode($json, true);

	switch (strtolower($payload["action"])) {
		case "purchased":
		case "changed":
			$create->create($payload["marketplace_purchase"]["account"]["login"], $payload["marketplace_purchase"]["plan"]["id"]);
			break;
			
		case "cancelled":
			$create->create($payload["marketplace_purchase"]["account"]["login"], 1); // 1 => disabled
			break;

		default:
			header('HTTP/1.0 404 Not Found');
			echo "Event: " . $payload['action'] . "Payload:\n";
			print_r($payload); # For debug only. Can be found in GitHub hook log.
			die();
	}
?>

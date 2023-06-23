<?php
	/**
	 * Manage webhook requests
	 *
	 * PHP version 7.4.16
	 *
	 * @package    rest-createstructure
	 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
	 * @license    GNU
	 * @link       https://github.com/createstructure/rest-createstructure
	 */
	include_once "config/webhook.php";
	include_once "config/database.php";

	// required headers
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

	// local variabile(s)
	$webhook = new Webhook();
	$hookSecret = $webhook->getSecret();
	$conn = new Database();

	// Some checks
	if (!isset($_SERVER["HTTP_X_HUB_SIGNATURE"])) {
		die(
			json_encode(
				array(
					"code" => 412,
					"message" => "HTTP header \"X-Hub-Signature\" is missing."
					)
				)
			);
	} elseif (!extension_loaded("hash")) {
		die(
			json_encode(
				array(
					"code" => 412,
					"message" => "Missing \"hash\" extension to check the secret code validity."
					)
				)
			);
	}

	list($algo, $hash) = explode("=", $_SERVER["HTTP_X_HUB_SIGNATURE"], 2) + array("", "");
	if (!in_array($algo, hash_algos(), true)) {
		die(
			json_encode(
				array(
					"code" => 412,
					"message" => "Hash algorithm \"$algo\" is not supported."
					)
				)
			);
	}

	$rawPost = file_get_contents("php://input");
	if (!hash_equals($hash, hash_hmac($algo, $rawPost, $hookSecret))) {
		die(
			json_encode(
				array(
					"code" => 412,
					"message" => "Hook secret does not match."
					)
				)
			);
	}

	if (!isset($_SERVER["CONTENT_TYPE"])) {
		die(
			json_encode(
				array(
					"code" => 412,
					"message" => "Missing HTTP \"Content-Type\" header."
					)
				)
			);
	} elseif (!isset($_SERVER["HTTP_X_GITHUB_EVENT"])) {
		die(
			json_encode(
				array(
					"code" => 412,
					"message" => "Missing HTTP \"X-Github-Event\" header."
					)
				)
			);
	}

	// Get payload (xml or json)
	switch ($_SERVER["CONTENT_TYPE"]) {
		case "application/json":
			$json = $rawPost ?: file_get_contents("php://input");
			break;

		case "application/x-www-form-urlencoded":
			$json = $_POST["payload"];
			break;

		default:
			die(
				json_encode(
					array(
						"code" => 405,
						"message" => "Unsupported content type: $_SERVER[CONTENT_TYPE]"
						)
					)
				);
	}

	/*
	// For test onlys
	$payload = array();
    try {
        $post = file_get_contents("php://input");
        if ($post != "")
            $payload = json_decode($post, true);
    } catch (Exception $e) {
    }
	*/
	$payload = json_decode($json, true);

	if (
		strtolower($payload["action"]) == "purchased" ||
		strtolower($payload["action"]) == "changed"
	)
		$accountID = $payload["marketplace_purchase"]["plan"]["id"];
	else
		$accountID = "1"; // Disabled account

	$query = "SELECT CreateUpdateRemoveClient(?, ?) AS statusCode;";

	// prepare and execute query
	$stmt = $conn->getConnection()->stmt_init();
	$stmt->prepare($query);
	$stmt->bind_param("ss", $payload["marketplace_purchase"]["account"]["login"], $accountID);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();

	if (
			is_bool($result) ||
			$result->num_rows != 1
		)
		die(
			json_encode(
				array(
					"code" => 409,
					"message" => "Failed client changes"
					)
				)
			);

	$res = array();

	while ($row = $result->fetch_array())
		switch ($row["statusCode"]) {
			case "200":
			case 200:
				$res = array(
					"code" => 200,
					"message" => "Priority creation made with success"
				);
				break;
			case "504":
			case 504:
				$res = array(
					"code" => 504,
					"message" => "There is a problem, the DB seems to be full of work, please try again later"
				);
				break;
			default:
				$res = array(
					"code" => 400,
					"message" => "Generic error"
				);
		}

	echo json_encode($response);

	$conn->closeConnection();

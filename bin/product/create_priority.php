<?php
	/**
	 * Create priority
	 *
	 * PHP version 7.4.16
	 *
	 * @package    rest-createstructure
	 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
	 * @license    GNU
	 * @link       https://github.com/createstructure/rest-createstructure
	 */
	include_once '../config/database.php';
	include_once '../parts/priority_elem.php';

	// required headers
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


	// local variabile(s)
	$database = new Database();
	$db = $database->getConnection();
	$priority = new Priority($db);

	// get posted data
	$data = json_decode(file_get_contents("php://input"));
	$data = json_decode(json_encode($data), true); // $data to array

	// make sure data is not empty
	if(
			is_string($data["username"]) &&
			is_string($data["token"]) &&
			is_string($data["server_id"]) &&
			is_string($data["instruction"])
		){
		// tell the user
		echo json_encode(array("message" => $priority->create($data)));
	} else{
		// set response code - 400 bad request
		http_response_code(400);

		// tell the user
		die(
			json_encode(
				array(
						"message" => "error",
						"error" => "Unable to create product. Data is incomplete."
					)
			)
		);
	}
?>

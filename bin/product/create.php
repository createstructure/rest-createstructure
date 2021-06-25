<?php
	/**
	 * Manage DB credentials
	 *
	 * PHP version 7.4.16
	 *
	 * @package    rest-createstructure
	 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
	 * @license    GNU
	 * @link       https://github.com/createstructure/rest-createstructure
	 */
	include_once '../config/database.php';
	include_once '../parts/esec_elem.php';

	// required headers
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


	// local variabile(s)
	$database = new Database();
	$db = $database->getConnection();
	$esec = new Esec($db);
	$data = json_decode(
					json_encode(
						json_decode(
							file_get_contents("php://input"), true)
						), true
					); // $data to array

	// make sure data is not empty
	if(
			is_string($data["username"]) &&
			is_string($data["token"]) &&
			is_string($data["answers"]["name"]) &&
			is_string($data["answers"]["template"]) &&
			is_string($data["answers"]["descr"]) &&
			is_string($data["answers"]["prefix"]) &&
			is_string($data["answers"]["team"]) &&
			is_bool($data["answers"]["private"]) &&
			is_bool($data["answers"]["isOrg"]) &&
			(
				(
					$data["answers"]["isOrg"] && is_string($data["answers"]["org"])
				) || 
					!$data["answers"]["isOrg"]
			)
		){
		// tell the user
		echo json_encode(array("message" => $esec->create($data)));
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

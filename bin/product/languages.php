<?php
	/**
	 * Get dictionary
	 *
	 * PHP version 7.4.16
	 *
	 * @package    rest-createstructure
	 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
	 * @license    GNU
	 * @link       https://github.com/createstructure/rest-createstructure
	 */
	include_once '../config/database.php';
	include_once '../parts/languages_elem.php';

	// required headers
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");

	// local variabile(s)
	$database = new Database();
	$db = $database->getConnection();
	$language = new Languages($db);

	if (isset($_GET["id"])) {
		$data = $language->readOne($_GET["id"]);
	} else {
		$data = $language->read();
	}
	
	// check if more than 0 record found
	if($data->num_rows > 0){
		// products array
		$products_arr=array();
		$products_arr["records"]=array();
		
		while ($row = $data->fetch_assoc()){
			// extract row
			// this will make $row['name'] to
			// just $name only
			extract($row);

			$product_item=array(
				"id" => $id,
				"language" => $language,
				"description" => html_entity_decode($description),
				"change" => $change
			);

			array_push($products_arr["records"], $product_item);
		}
	
		// set response code - 200 OK
		http_response_code(200);

		// show products data in json format
		echo json_encode($products_arr);
	} else {
		// set response code - 404 Not found
		http_response_code(404);

		// tell the user no products found
		die(
			json_encode(
				array(
						"message" => "error",
						"error" => "No status found."
					)
				)
			);
	}
?>
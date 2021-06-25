<?php
	/**
	 * Mark a priority as finished
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
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$nums = "0123456789";
	$data = json_decode(file_get_contents("php://input"));
	$data = json_decode(json_encode($data), true); // $data to array
	$server_id = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($nums, "/")."]/", '', $data["server_id"])));
	$server_code = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($chars . $nums, "/")."]/", '', $data["server_code"])));
	$workId = htmlspecialchars(strip_tags(preg_replace("/[^".preg_quote($chars . $nums, "/")."]/", '', $data["work_id"])));
	$database = new Database();
	$db = $database->getConnection();
	$priority = new Priority($db);

	$priority->finishWork($server_id, $server_code, $workId);
	
	echo json_encode(
				array(
					"message" => "success"
				)
			);
	return;
?>

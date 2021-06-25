<?php
	/**
	 * give a new job
	 *
	 * PHP version 7.4.16
	 *
	 * @package    rest-createstructure
	 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
	 * @license    GNU
	 * @link       https://github.com/createstructure/rest-createstructure
	 */
	include_once '../config/database.php';
	include_once '../parts/crypto_elem.php';
	include_once '../parts/priority_elem.php';
	include_once '../parts/esec_elem.php';

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
	$database = new Database();
	$db = $database->getConnection();	
	$priority = new Priority($db);
	$priorityCode = $priority->getElement($server_id, $server_code);
	
	if ($priorityCode == "") {
		$priority->finishWork($server_id, $server_code, $workId);

		$esec = new Esec($db);
		$crypto = new Crypto($db, $server_id, $server_code);
		
		$work = $esec->getElement($server_id, $server_code);
		if (json_decode($work, true)["message"] == "no work to do") {
			echo $work;
		} else {
			$work = json_decode($crypto->decrypt(json_decode($work, true)), true);
			$work["server_id"] = $server_id;
			$work["server_code"] = $server_code;
			$work["work_id"] = strval($esec->getWorkID($server_id, $server_code));
			$work = $crypto->server_encrypt(json_encode($work));
			
			if ($work != "") {
				echo json_encode(
							array(
								"message" => "work given",
								"code" => "assigned new work",
								"data" => $work
							)
						);
				return;
			}
		}
	} else {
		echo json_encode(
					array(
							"message" => "super work",
							"priority_message" => $priorityCode,
							"priority_id" => strval($priority->getWorkID($server_id, $server_code))
						)
					);
	}
?>

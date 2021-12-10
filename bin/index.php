<?php
	/**
	 * Index of the REST API
	 *
	 * PHP version 7.4.16
	 *
	 * @package    rest-createstructure
	 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
	 * @license    GNU
	 * @link       https://github.com/createstructure/rest-createstructure
	 */	

    // Import(s)
    include_once "./core/splitter.php";

    // Get POST data
    $data = array();
    try {
        $post = file_get_contents("php://input");
        if ($post != "")
            $data = json_decode($post, true);
    } catch (Exception $e) {
    }
    //print_r($data);

    // Initialize splitter
    $splitter = new Splitter($data);

    // Run and get responce
    $results = $splitter->run();

	// required headers
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Final print
    echo json_encode($results);
?>
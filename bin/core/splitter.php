<?php
/**
 * Splitter action
 *
 * PHP version 7.4.16
 *
 * @package    rest-createstructure
 * @author     Castellani Davide (@DavideC03) <help@castellanidavide.it>
 * @license    GNU
 * @link       https://github.com/createstructure/rest-createstructure
 */

// Import(s)
include_once "action.php";
include_once "welcome.php";
include_once "help.php";
include_once "auth.php";
include_once "create_repo.php";
include_once "create_server.php";
include_once "server_reserve_job.php";
include_once "server_get_job_info.php";
include_once "server_set_job_done.php";
include_once "server_set_priority.php";
include_once "server_get_priority.php";
include_once "server_set_priority_done.php";

class Splitter implements Action
{
	// class variabile(s)
	private $payload;
	private $action;

	/**
	 * Constructor
	 *
	 * @param mixed		The payload of the request
	 */
	public function __construct($payload)
	{
		$this->payload = $payload;
		if (empty($this->payload))
			$this->payload["request"] = "help";
	}

	/**
	 * Run main code
	 *
	 * @return array Array with the response, if there wasn"t any error
	 */
	public function run()
	{
		switch ($this->payload["request"]) {
			case "welcome":
				$this->action = new Welcome($this->payload);
				break;
			case "help":
				$this->action = new Help($this->payload);
				break;
			case "login":
				$this->action = new Auth($this->payload);
				break;
			case "create_repo":
				$this->action = new CreateRepo($this->payload);
				break;
			case "create_server":
				$this->action = new CreateServer($this->payload);
				break;
			case "server_reserve_job":
				$this->action = new ServerReservejob($this->payload);
				break;
			case "server_get_job_info":
				$this->action = new ServerGetJobInfo($this->payload);
				break;
			case "server_set_job_done":
				$this->action = new ServerSetJobDone($this->payload);
				break;
			case "server_set_priority":
				$this->action = new ServerSetPriority($this->payload);
				break;
			case "server_get_priority":
				$this->action = new ServerGetpriority($this->payload);
				break;
			case "server_set_priority_done":
				$this->action = new ServerSetPriorityDone($this->payload);
				break;
			default:
				return array(
					"code" => 400,
					"error" => "Request not founded",
					"request" => $this->payload
				);
		}
		return $this->action->run();
	}
}

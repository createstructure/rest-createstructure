<?php
	/**
	 * Help action 
	 *
	 * PHP version 7.4.16
	 *
	 * @package    rest-createstructure
	 * @author     Castellani Davide (@DavideC03) "help@castellanidavide.it>
	 * @license    GNU
	 * @link       https://github.com/createstructure/rest-createstructure
	 */

    // Import(s)
    include_once "action.php";
	
	class Help implements Action{
		// class variabile(s)
		private $payload;
		
		/**
		 * Constructor
		 * 
		 * @param payload		The payload of the request
		 */
		public function __construct($payload){
			$this->payload = $payload;
		}
		
		/**
		 * Run main code
		 * 
		 * @return array Array with the response, if there wasn"t any error
		 */ 
		public function run() {
			return array(
				"code" => 200,
				"message" => "Help",
				"help" => array(
					"welcome" => array(
						"name" => "Welcome",
						"type" => "POST",
						"action" => "Returns a random welcome message",
						"request" => "{\"request\": \"welcome\"}",
						"URL" => "/",
						"response" => "{\"code\": <code>, \"message\": <random_welcome_message>}",
						"notes" => "Basic request"
					),
					"help" => array(
						"name" => "Help",
						"type" => "POST",
						"action" => "Returns a message to help user to use this API",
						"request" => "{\"request\": \"help\"} or {}",
						"URL" => "/",
						"response" => "{\"code\": <code>, \"message\": <help_generic_message>, \"help\": {<REST_command_name>: {\"name\": <REST_command_name>, \"type\": <GET_or_POST>, \"action\": <functionality>, \"request\": <request_structure>, \"URL\": <REST_URL>, \"response\": <response_structure>, \"notes\": <description>}, ...}}",
						"notes" => "Gives to the user all the information to use this REST API"
					),
					"auth" => array(
						"name" => "Auth",
						"type" => "POST",
						"action" => "Check if account is it ok",
						"request" => "{\"request\": \"login\", \"payload\": {\"username\": <GitHub_username>, \"token\": <Github_token>}}",
						"URL" => "/",
						"response" => "{\"code\": <code>, \"message\": <ok_or_error_message>, \"sub_info\": {\"name\": <sub_name>, \"active\": <true/false>, \"super\": <true/false>, \"max\": {\"day\": <max_usages_for_day>, \"h\": <max_usages_for_hour>, \"m\": <max_usages_for_minute>}, \"remaining\": {\"day\": <remaining_usages_for_day>, \"h\": <remaining_usages_for_hour>, \"m\": <remaining_usages_for_minute>}}}",
						"notes" => "This is userfull to get any usefull info about a consumer"
					),
					"create_repo" => array(
						"name" => "Create Repository",
						"type" => "POST",
						"action" => "Permits user to create a repository",
						"request" => "{\"request\": \"create_repo\", payload: {\"token\": <GitHub_token>, \"username\": <GitHub_username>, \"answers\": { \"name\": <New_repo_name> [, \"template\": <Template_to_use(eg. default or Owner/repo-template)>] [, \"descr\": <Description>] [, \"prefix\": <The_prefix_of_the_repo(if you want once)>] [, \"private\": <true/false>] [, \"isOrg\": <If_you_want_your_repo_in_an_organization(true/false)> , \"org\": <Name_of_the_org_if_isOrg_true> [, \"team\": <The_name_of_the_team_if_isOrg_true>] ]}}}",
						"URL" => "/",
						"response" => "{\"code\": <code>, \"message\": <response_message>}",
						"notes" => "This REST API call permits to the consumer to ask to createstructure\"s service to create a repository"
					),
					"server_reserve_job" => array(
						"name" => "Reserve a new repo to create (server-side)",
						"type" => "POST",
						"action" => "functionality",
						"request" => "{\"request\": \"server_reserve_job\", \"server_name\": <server_name>, \"server_password\": <server_password>}",
						"URL" => "/",
						"response" => "{\"code\": <code>, \"message\": <response_message>, \"repoID\": <repoID>}",
						"notes" => "Usefull for the server to reserve a repo to create it"
					),
					"server_get_job_info" => array(
						"name" => "Get a new repo info to create it (server-side)",
						"type" => "POST",
						"action" => "functionality",
						"request" => "{\"request\": \"server_get_job_info\", \"server_name\": <server_name>, \"server_password\": <server_password>, \"repoID\": <repoID>}",
						"URL" => "/",
						"response" => "{\"code\": <code>, \"message\": <response_message>, \"repo_info\": <repo_info>}",
						"notes" => "Usefull for the server to ask a repo info to create it"
					),
					"server_set_job_done" => array(
						"name" => "Set a repo job as done (server-side)",
						"type" => "POST",
						"action" => "functionality",
						"request" => "{\"request\": \"server_set_job_done\", \"server_name\": <server_name>, \"server_password\": <server_password>, \"repoID\": <repoID>}",
						"URL" => "/",
						"response" => "{\"code\": <code>, \"message\": <response_message>}",
						"notes" => "Usefull for the server to set repo job as done"
					),
					"server_set_priority" => array(
						"name" => "Set a new server priority (server-side)",
						"type" => "POST",
						"action" => "functionality",
						"request" => "{\"request\": \"server_set_priority\", \"username\": <GitHub_username>, \"token\": <Github_token>, \"server_name\": <server_name>, \"server_priority\": <server_priority>}",
						"URL" => "/",
						"response" => "{\"code\": <code>, \"message\": <response_message>}",
						"notes" => "Ask to do some commands to a server without ssh"
					),
					"server_get_priority" => array(
						"name" => "Get a priority if there was one (server-side)",
						"type" => "POST",
						"action" => "functionality",
						"request" => "{\"request\": \"server_get_priority\", \"server_name\": <server_name>, \"server_password\": <server_password>}",
						"URL" => "/",
						"response" => "{\"code\": <code>, \"message\": <response_message> [, \"priority_instruction\": <priority_instruction>, \"priorityID\": <priorityID>]}",
						"notes" => "Usefull for the server to get the priority"
					),
					"server_set_priority_done" => array(
						"name" => "Set a repo job as done (server-side)",
						"type" => "POST",
						"action" => "functionality",
						"request" => "{\"request\": \"server_set_priority_done\", \"server_name\": <server_name>, \"server_password\": <server_password>, \"priorityID\": <priorityID>}",
						"URL" => "/",
						"response" => "{\"code\": <code>, \"message\": <response_message>}",
						"notes" => "Usefull to set priority as done"
					)
				)
			);
		}
	}	
?>

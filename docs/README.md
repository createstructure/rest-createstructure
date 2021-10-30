## :octocat: Description :octocat:
This repository contains the REST API created for the createstructure service. To get more info about how it works and how can you can contribute, please go to the wiki.
![createstructure/rest-createstructure](https://opengraph.githubassets.com/cad05156c359e8665206bdb005420539bb843cba414c759a62ce06acf3376749/createstructure/rest-createstructure)
## :octocat: Directory structure :octocat:

```
$GITHUB_REPOSITORY
├── bin # PHP source code
│   ├── config # configuration file where to store passwords, tokens, ...
│   │   ├── database.php
│   │   ├── key.php
│   │   └── webhook.php
│   ├── core # main part of the REST API
│   │   ├── action.php # interface for any of the other actions
│   │   ├── auth.php
│   │   ├── create_repo.php
│   │   ├── help.php
│   │   ├── server_get_job_info.php
│   │   ├── server_get_priority.php
│   │   ├── server_reserve_job.php
│   │   ├── server_set_job_done.php
│   │   ├── server_set_priority.php
│   │   ├── server_set_priority_done.php
│   │   ├── splitter.php # where the request is splitted to the needed action
│   │   └── welcome.php
│   ├── index.php
│   └── webhook.php
├── db # DB utilities
│   ├── functions-procedures # folder containing all the needed functions and procedures
│   │   ├── CreateRepo.sql
│   │   ├── CreateServer.sql
│   │   ├── CreateServerPriority.sql
│   │   ├── CreateUpdateRemoveClient.sql
│   │   ├── GetClient.sql
│   │   ├── ServerGetJobInfo.sql
│   │   ├── ServerGetPriority.sql
│   │   ├── ServerGetPublicKey.sql
│   │   ├── ServerReserveJob.sql
│   │   ├── ServerSetJobDone.sql
│   │   ├── ServerSetPriority.sql
│   │   └── ServerSetPriorityDone.sql
│   └── tables # DB tables definition
│       ├── client.sql
│       ├── client_account.sql
│       ├── client_accounts_type.sql
│       ├── repo_declaration.sql
│       ├── repo_log.sql
│       ├── repo_status.sql
│       ├── server_list.sql
│       ├── server_priority_declaration.sql
│       ├── server_priority_instructions.sql
│       ├── server_priority_log.sql
│       ├── server_priority_status.sql
│       └── server_secrets.sql
└── docs # documentation
    ├── CHANGELOG.md
    ├── ERD.svg
    ├── LICENSE
    └── README.md

7 directories, 45 files
```
## :octocat: Database structure (ERD) :octocat:

![ERD](https://raw.githubusercontent.com/createstructure/rest-createstructure/v10-beta/docs/ERD.svg)
## :octocat: REST API actions :octocat:
| name | type | action | request | URL | response | notes |
| --- | --- | --- | --- | --- | --- | --- |
| Welcome | POST | Returns a random welcome message | {"request": "welcome"} | / | {"status": <status_code>, "message": <random_welcome_message>} | Basic request |
| Help | POST | Returns a message to help user to use this API | {"request": "help"} or {} | / | {"status": <status_code>, "message": <help_generic_message>, "help": {<REST_command_name>: {"name": <REST_command_name>, "type": <GET_or_POST>, "action": <functionality>, "request": <request_structure>, "URL": <REST_URL>, "response": <response_structure>, "notes": <description>}, ...}} | Gives to the user all the information to use this REST API |
| Auth | POST | Check if account is it ok | {"request": "login", "payload": {"username": <GitHub_username>, "token": <Github_token>}} | / | {"status": <status_code>, "message": <ok_or_error_message>, "sub_info": {"name": <sub_name>, "active": <true/false>, "super": <true/false>, "max": {"day": <max_usages_for_day>, "h": <max_usages_for_hour>, "m": <max_usages_for_minute>}, "remaining": {"day": <remaining_usages_for_day>, "h": <remaining_usages_for_hour>, "m": <remaining_usages_for_minute>}}} | This is userfull to get any usefull info about a consumer |
| Create Repository | POST | Permits user to create a repository | {"request": "create_repo", payload: {"token": <GitHub_token>, "username": <GitHub_username>, "answers": { "name": <New_repo_name> [, "template": <Template_to_use(eg. default or Owner/repo-template)>] [, "descr": <Description>] [, "prefix": <The_prefix_of_the_repo(if you want once)>] [, "private": <true/false>] [, "isOrg": <If_you_want_your_repo_in_an_organization(true/false)> , "org": <Name_of_the_org_if_isOrg_true> [, "team": <The_name_of_the_team_if_isOrg_true>] ]}}} | / | {"status": <status_code>, "message": <response_message>} | This REST API call permits to the consumer to ask to createstructure"s service to create a repository |
| Reserve a new repo to create (server-side) | POST | functionality | {"request": "server_reserve_job", "server_name": <server_name>, "server_password": <server_password>} | / | {"status": <status_code>, "message": <response_message>, "repo_id": <repo_id>} | Usefull for the server to reserve a repo to create it |
| Get a new repo info to create it (server-side) | POST | functionality | {"request": "server_get_job_info", "server_name": <server_name>, "server_password": <server_password>, "repo_ID": <repo_ID>} | / | {"status": <status_code>, "message": <response_message>, "payload": [<payload_try1>, ...]} | Usefull for the server to ask a repo info to create it |
| Set a repo job as done (server-side) | POST | functionality | {"request": "server_set_job_done", "server_name": <server_name>, "server_password": <server_password>, "repo_ID": <repo_ID>} | / | {"status": <status_code>, "message": <response_message>} | Usefull for the server to set repo job as done |
| Set a new server priority (server-side) | POST | functionality | {"request": "server_set_priority", "username": <GitHub_username>, "token": <Github_token>, "server_name": <server_name>, "server_priority": <server_priority>} | / | {"status": <status_code>, "message": <response_message>} | Ask to do some commands to a server without ssh |
| Get a priority if there was one (server-side) | POST | functionality | {"request": "server_get_priority", "server_name": <server_name>, "server_password": <server_password>} | / | {"status": <status_code>, "message": <response_message> [, "priority_instruction": <priority_instruction>, "priority_ID": <priority_ID>]} | Usefull for the server to get the priority |
| Set a repo job as done (server-side) | POST | functionality | {"request": "server_set_priority_done", "server_name": <server_name>, "server_password": <server_password>, "priority_ID": <priority_ID>} | / | {"status": <status_code>, "message": <response_message>} | Usefull to set priority as done |
## :octocat: Changelog :octocat:
Repo containing the public part of the REST/API

- [:octocat: Changelog :octocat:](#changelog)
  - [[09.01.04] - 2021-07-10](#090104---2021-07-10)
    - [Changed](#changed)
  - [[09.01.03] - 2021-07-08](#090103---2021-07-08)
    - [Changed](#changed-1)
  - [[09.01.02] - 2021-07-08](#090102---2021-07-08)
    - [Changed](#changed-2)
  - [[09.01.01] - 2021-06-19](#090101---2021-06-19)
    - [Added](#added)

### [09.01.04] - 2021-07-10
#### Changed
- Now usernames can have "-" inside 

### [09.01.03] - 2021-07-08
#### Changed
- Fixed a bug in esec_elem.php file

### [09.01.02] - 2021-07-08
#### Changed
- Now you can use your own temlpate :happy:
- Fixed release action

### [09.01.01] - 2021-06-19
#### Added
- Initial version for this repo
---
Made w/ :heart: by Castellani Davide

If you want to contribute you can start with:
- [Discussion](https://github.com/createstructure/rest-createstructure/discussions)
- [Issue](https://github.com/createstructure/rest-createstructure/issues/new)

# rest-createstructure
[![GitHub license](https://img.shields.io/badge/license-GNU-green?style=flat)](https://github.com/createstructure/rest-createstructure/blob/v10-beta/docs/LICENSE)
![Author](https://img.shields.io/badge/author-Castellani%20Davide-green?style=flat)
![sys.platform supported](https://img.shields.io/badge/OS%20platform%20supported-all-blue?style=flat) 

##  Description 
This repository contains the REST API created for the createstructure service.

To get more info about how it works and how can you can contribute, please go to the [wiki](https://github.com/createstructure/rest-createstructure/wiki).
![createstructure/rest-createstructure](https://opengraph.githubassets.com/cad05156c359e8665206bdb005420539bb843cba414c759a62ce06acf3376749/createstructure/rest-createstructure)
##  Directory structure 

```

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
│   ├── database.sql
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

7 directories, 46 files
```
##  Database structure (ERD) 

![ERD](https://raw.githubusercontent.com/createstructure/rest-createstructure/v10-beta/docs/ERD.svg)
##  REST API actions 
| name | type | action | request | URL | response | notes |
| --- | --- | --- | --- | --- | --- | --- |
| Welcome | POST | Returns a random welcome message | {"request": "welcome"} | / | {"code": <code>, "message": <random_welcome_message>} | Basic request |
| Help | POST | Returns a message to help user to use this API | {"request": "help"} or {} | / | {"code": <code>, "message": <help_generic_message>, "help": {<REST_command_name>: {"name": <REST_command_name>, "type": <GET_or_POST>, "action": <functionality>, "request": <request_structure>, "URL": <REST_URL>, "response": <response_structure>, "notes": <description>}, ...}} | Gives to the user all the information to use this REST API |
| Auth | POST | Check if account is it ok | {"request": "login", "payload": {"username": <GitHub_username>, "token": <Github_token>}} | / | {"code": <code>, "message": <ok_or_error_message>, "sub_info": {"name": <sub_name>, "active": <true/false>, "super": <true/false>, "max": {"day": <max_usages_for_day>, "h": <max_usages_for_hour>, "m": <max_usages_for_minute>}, "remaining": {"day": <remaining_usages_for_day>, "h": <remaining_usages_for_hour>, "m": <remaining_usages_for_minute>}}} | This is userfull to get any usefull info about a consumer |
| Create Repository | POST | Permits user to create a repository | {"request": "create_repo", payload: {"token": <GitHub_token>, "username": <GitHub_username>, "answers": { "name": <New_repo_name> [, "template": <Template_to_use(eg. default or Owner/repo-template)>] [, "descr": <Description>] [, "prefix": <The_prefix_of_the_repo(if you want once)>] [, "private": <true/false>] [, "isOrg": <If_you_want_your_repo_in_an_organization(true/false)> , "org": <Name_of_the_org_if_isOrg_true> [, "team": <The_name_of_the_team_if_isOrg_true>] ]}}} | / | {"code": <code>, "message": <response_message>} | This REST API call permits to the consumer to ask to createstructure"s service to create a repository |
| Reserve a new repo to create (server-side) | POST | functionality | {"request": "server_reserve_job", "server_name": <server_name>, "server_password": <server_password>} | / | {"code": <code>, "message": <response_message>, "repoID": <repoID>} | Usefull for the server to reserve a repo to create it |
| Get a new repo info to create it (server-side) | POST | functionality | {"request": "server_get_job_info", "server_name": <server_name>, "server_password": <server_password>, "repoID": <repoID>} | / | {"code": <code>, "message": <response_message>, "repo_info": <repo_info>} | Usefull for the server to ask a repo info to create it |
| Set a repo job as done (server-side) | POST | functionality | {"request": "server_set_job_done", "server_name": <server_name>, "server_password": <server_password>, "repoID": <repoID>} | / | {"code": <code>, "message": <response_message>} | Usefull for the server to set repo job as done |
| Set a new server priority (server-side) | POST | functionality | {"request": "server_set_priority", "username": <GitHub_username>, "token": <Github_token>, "server_name": <server_name>, "server_priority": <server_priority>} | / | {"code": <code>, "message": <response_message>} | Ask to do some commands to a server without ssh |
| Get a priority if there was one (server-side) | POST | functionality | {"request": "server_get_priority", "server_name": <server_name>, "server_password": <server_password>} | / | {"code": <code>, "message": <response_message> [, "priority_instruction": <priority_instruction>, "priorityID": <priorityID>]} | Usefull for the server to get the priority |
| Set a repo job as done (server-side) | POST | functionality | {"request": "server_set_priority_done", "server_name": <server_name>, "server_password": <server_password>, "priorityID": <priorityID>} | / | {"code": <code>, "message": <response_message>} | Usefull to set priority as done |
##  Changelog 
Repo containing the public part of the REST/API

- [ Changelog ](#changelog)
  - [[10.01.01] - 2021-12-10](#100101---2021-12-10)
  - [[09.01.04] - 2021-07-10](#090104---2021-07-10)
    - [Changed](#changed)
  - [[09.01.03] - 2021-07-08](#090103---2021-07-08)
    - [Changed](#changed-1)
  - [[09.01.02] - 2021-07-08](#090102---2021-07-08)
    - [Changed](#changed-2)
  - [[09.01.01] - 2021-06-19](#090101---2021-06-19)
    - [Added](#added)


### [10.01.01] - 2021-12-10
- [v10-beta (rest-createstructure)](https://github.com/createstructure/rest-createstructure/issues/2)
  - [Create a "manual" of REST possible requests](https://github.com/createstructure/rest-createstructure/issues/3)
  - [Improve security (for config file)](https://github.com/createstructure/rest-createstructure/issues/4)
  - [All REST API requests on one link](https://github.com/createstructure/rest-createstructure/issues/5)
    - [Create a splitter](https://github.com/createstructure/rest-createstructure/issues/9)
    - [Create an action interface](https://github.com/createstructure/rest-createstructure/issues/8)
    - [Recreate all the actions](https://github.com/createstructure/rest-createstructure/issues/10)
  - [Improve DB management](https://github.com/createstructure/rest-createstructure/issues/6)
    - [Improve DB table structure](https://github.com/createstructure/rest-createstructure/issues/11)
    - [Create DB functions/ procedures, instead to have SQL code into PHP source](https://github.com/createstructure/rest-createstructure/issues/12)
    - [Document all DB tables, functions, ...](https://github.com/createstructure/rest-createstructure/issues/17)
  - [Improve documentation](https://github.com/createstructure/rest-createstructure/issues/7)
    - [Add wiki](https://github.com/createstructure/rest-createstructure/issues/13)
    - [Automate documentation creation](https://github.com/createstructure/rest-createstructure/issues/14)
    - [Create a short documentation for each folder / file explaining its functionality](https://github.com/createstructure/rest-createstructure/issues/15)
  - [Create a guide to recreate the REST / API locally](https://github.com/createstructure/rest-createstructure/issues/16)
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
##  Rebuild the REST API locally 
Install the REST API locally permits you to do some debug and try new configurations.

> ATTENTION
>
> Some features, like subscription with GitHub marketplace, can't be done with the local configuration.
>
> To do these actions you need to run manually the functions, in the previous example `CreateUpdateRemoveClient`

### Steps:
1. [Install VirtualBox on the PC](#1-install-virtualbox-on-the-pc)
2. [Install Ubuntu](#2-install-ubuntu)
3. [Install dependencies](#3-install-dependencies)
4. [Add PHP source code](#4-add-php-source-code)
5. [Create server](#5-create-server)
6. [Use REST API](#6-use-rest-api)
7. [Do the changes/ debug you want](#7-do-the-changes-debug-you-want)

### 1. Install VirtualBox on the PC
For installation we suggest VirtualBox, a tool that allows you to create one or more virtual machines :computer:.
If any of these crashes, in any case, your PC will not lose data, at most you will have to restart it :smile:.

To install VirtualBox on your PC you need to:
- Get in into the UEFI
- Enable the function which name is like "Virtualization" (for each UEFI this step is different but similar)
- Save the configuration and restart the PC
- Go to the [VirtualBox website](https://www.virtualbox.org/)
- Press "Download"
- Run the downloaded file
- Follow the installation steps

### 2. Install Ubuntu
As the OS we suggest to use Ubuntu, because it is lightweight (for RAM and CPU) and it's free.

To install Ubuntu on VirtualBox you need to:
- Download the last LTS version of Ubuntu by the following link: [https://ubuntu.com/download/desktop](https://ubuntu.com/download/desktop)
> Now you can continue with the other steps during the download
- Open VirtualBox
- Press "New"
- Compile the form
    - As name put "rest-createstructure"
    - As Type: "Linux"
    - As version: "Ubuntu (64-bit)" or "Ubuntu (32-bit)"
    - Press "Next >"
- Set the RAM to use for the VirtualMachine, at most half of local RAM and press "Next >"
- Leave "Create a virtual hard disk now" and press "Create"
- Leave "VDI ..." and press "Next >"
- Leave "Dynamically allocated" and press "Next >"
- Change the hard disk memory, we suggest 16GB and press "Create"
> Make sure that Ubuntu download is finished before to continue
- On the VirtualBox console, selecting the created VM, press "Start"
- Select as start-up disk Ubuntu, already downloaded
    - Press the folder icon
    - Press "Add", in the top menu
    - Select the Ubuntu iso, the file will have a structure like "ubuntu-version-other_info.iso"
    - Press "Choose" and "Start"
- Follow the install steps (the installation needs some minutes)

### 3. Install dependencies
Now you have to install lamp (Linux Apache MySQL (DB) PHP) on the VM.
> We suggest you to open this guide on the virtual machine, so you can copy and paste easlier the following commands. 

To install Dependes on the Virtual Machine you need to:
- On the VM (Virtual Machine) open the terminal (`Ctrl + Alt + T`)
- On the terminal paste `sudo apt install apache2 curl mysql-server php libapache2-mod-php php-mysql git -y; sudo systemctl restart apache2` and press enter (you have to insert your password)
- On the terminal paste `sudo apt install phpmyadmin -y; sudo ln -s /etc/phpmyadmin/apache.conf /etc/apache2/conf-available/phpmyadmin.conf; sudo a2enconf phpmyadmin.conf; sudo systemctl reload apache2.service`
    - leave "apache2" in the selecting menu
    - when required select "\<Yes\>"
    - set a password for phpmyadmin, after that select "\<Ok\>"
- Check the installation opening Firefox (the first item in the left menu bar) and as link put `localhost`
- If it works check also `localhost/phpmyadmin/` as link, better if in a new tab
    - To do the login here put "phpmyadmin" ad username and the password you setted a while go
### 4. Add PHP source code
To add the PHP source code on VirtualBox you need to:
- Go back to the terminal and type `cd /var/www/html; sudo git clone https://github.com/createstructure/rest-createstructure.git; cd rest-createstructure/bin/config/; sed -i 's/<YOUR_DB_NAME>/localhost/g' database.php; sed -i 's/<YOUR_DB_USERNAME>/localhost/g' database.php; sed -i 's/<YOUR_DB_PASSWORD>/localhost/g' database.php; sed -i 's/<YOUR_DB_TABLE_NAME>/createstructure/g' database.php; sed -i 's/ \/\/ TODO//g' database.php`
- Generate GPG key: `gpg --gen-key` and insert your data (DO NOT PUT ANY PASSWORD)
- On the terminal type `echo  --armor --export <YOUR_EMAIL>) | sed -e 's/ /\n/g' -e 's/\nPGP\nPUBLIC\nKEY\nBLOCK/ PGP PUBLIC KEY BLOCK/g'` (Replace in the string <YOUR_EMAIL> with your email) and copy the result (to copy Ctrl + Alt + C)
- Type `sudo nano key.php` and replace <PUBLIC_KEY> with the copied text (to paste Ctrl + Alt + V) and save (Ctrl + X => Y => Enter)
- On the terminal type `echo  --armor --export-secret-keys <YOUR_EMAIL>) | sed -e 's/ /\n/g' -e 's/\nPGP\nPRIVATE\nKEY\nBLOCK/ PGP PRIVATE KEY BLOCK/g'` (Replace in the string <YOUR_EMAIL> with your email) and copy the result (to copy Ctrl + Alt + C)
- Type `sudo nano key.php` and replace <PRIVATE_KEY> with the copied text (to paste Ctrl + Alt + V) and save (Ctrl + X => Y => Enter)

### 5. Add DB basic structure
Now you will create a DB and the basic structure.

To do that you need to:
- Go back to the phpmyadmin page
- On the hight menu select "SQL"
- Copy [this](https://raw.githubusercontent.com/createstructure/rest-createstructure/v10-beta/db/database.sql) and paste it in the box
- Press "Go"

### 6. Use REST API

To use the REST API you need to:
- On the terminal write: `curl -d '{<INSERT_YOUR_REQUEST>}' -H "Content-Type: application/json" -X POST http://localhost/rest-createstructure/bin/ | json_pp -json_opt pretty,canonical`, replacing <INSERT_YOUR_REQUEST> with your request, see requests rupported [here](https://github.com/createstructure/rest-createstructure/wiki/REST-Actions)

### 7. Do the changes/ debug you want 
Now you can try any changes you want and, if you want, improve the REST API (using [Issues](https://github.com/createstructure/rest-createstructure/issues), [Pull requests](https://github.com/createstructure/rest-createstructure/pulls), or if you want to suggest/ discuss on how to improve [Discussion](https://github.com/createstructure/rest-createstructure/discussions))

---
Made w/ :heart: by Castellani Davide

If you want to contribute you can start with:
- [Discussion](https://github.com/createstructure/rest-createstructure/discussions)
- [Issue](https://github.com/createstructure/rest-createstructure/issues/new)

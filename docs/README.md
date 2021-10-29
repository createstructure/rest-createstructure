# rest-createstructure
[![GitHub license](https://img.shields.io/badge/license-GNU-green?style=flat)](https://github.com/CastellaniDavide/restcreatestructure-restcreatestructure/blob/master/LICENSE)
![Author](https://img.shields.io/badge/author-Castellani%20Davide-green?style=flat)
![sys.platform supported](https://img.shields.io/badge/OS%20platform%20supported-all-blue?style=flat) 
[![On GitHub](https://img.shields.io/badge/on%20GitHub-True-green?style=flat&logo=github)](https://github.com/createstructure/rest-createstructure)


## Table of contents
- [rest-createstructure](#rest-createstructure)
  - [Table of contents](#table-of-contents)
  - [Description](#description)
  - [Directories structure](#directories-structure)
  - [Requirements](#requirements)

## Description
Repo containing the public part of the REST API

## Directories structure
```
├── bin
│   ├── config
│   │   ├── database.php
│   │   ├── key.php
│   │   └── webhook.php
│   ├── core
│   │   ├── action.php
│   │   ├── auth.php
│   │   ├── create_repo.php
│   │   ├── help.php
│   │   ├── server_get_job_info.php
│   │   ├── server_get_priority.php
│   │   ├── server_reserve_job.php
│   │   ├── server_set_job_done.php
│   │   ├── server_set_priority_done.php
│   │   ├── server_set_priority.php
│   │   ├── splitter.php
│   │   └── welcome.php
│   ├── index.php
│   └── webhook.php
└── docs
    ├── CHANGELOG.md
    ├── LICENSE
    └── README.md
```

## Requirements
Here all the requirements to run this repo:
 - php (>= 7.4.16)
 - DB (MySQL)

---
Made w/ :love: by Castellani Davide 
If you want to contribute you can start with:
- [Discussion](https://github.com/createstructure/rest-createstructure/discussions)
- [Issue](https://github.com/createstructure/rest-createstructure/issues/new)

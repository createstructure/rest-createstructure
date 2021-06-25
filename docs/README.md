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
Repo containing the public part of the REST/API

## Directories structure
```
├── bin
│   ├── config
│   │   ├── database.php
│   │   ├── key.php
│   │   └── webhook.php
│   ├── parts
│   │   ├── create_user_elem.php
│   │   ├── crypto_elem.php
│   │   ├── esec_elem.php
│   │   ├── languages_elem.php
│   │   ├── priority_elem.php
│   │   └── subscription_elem.php
│   └── product
│       ├── create.php
│       ├── create_priority.php
│       ├── create_user.php
│       ├── esec_status.php
│       ├── finished_priority.php
│       ├── finished_work.php
│       ├── give_work.php
│       ├── languages.php
│       ├── started_work.php
│       └── subscription.php
├── docs
│   ├── CHANGELOG.md
│   ├── LICENSE
│   └── README.md
├── .git
│   └── ...
├── .github
│   └── workflows
│       └── release.yml
└── .gitignore   
```

## Requirements
 - php (>= 7.4.16)
 - DB

---
Made w/ :love: by Castellani Davide 
If you have any problem please contact me:
- [Discussion](https://github.com/createstructure/createstructure/discussions/new) and choose "core-createstructure" category
- [Issue](https://github.com/createstructure/core-createstructure/issues/new)
- [help@castellanidavide.it](mailto:help@castellanidavide.it)

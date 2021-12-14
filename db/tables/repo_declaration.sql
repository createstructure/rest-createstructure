--
-- Table structure for table `repo_declaration`
--

CREATE TABLE `repo_declaration` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Table ID',
  `clientID` varchar(39) NOT NULL COMMENT 'Client ID',
  `data` text NOT NULL COMMENT 'All information for the creation of the repository',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`),
  KEY (`clientID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='The declaration of the repo to create';

--
-- Table structure for table `repo_log`
--

CREATE TABLE `repo_log` (
  `ID` int(11) NOT NULL COMMENT 'Table ID',
  `repo_ID` int(11) NOT NULL COMMENT 'Repository ID',
  `server_ID` int(11) DEFAULT NULL COMMENT 'ID of the server taking charge of the operation',
  `status_ID` int(11) NOT NULL COMMENT 'Status ID',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`),
  KEY (`repo_ID`,`server_ID`,`status_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table to track the progress of repo creation';

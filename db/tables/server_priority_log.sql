--
-- Table structure for table `server_priority_log`
--

CREATE TABLE `server_priority_log` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Table ID',
  `priorityID` int(11) NOT NULL COMMENT 'Priority ID',
  `statusID` int(11) NOT NULL COMMENT 'Status ID',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`),
  KEY (`priorityID`,`statusID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='The log of server instructions';

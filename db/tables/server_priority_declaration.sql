--
-- Table structure for table `server_priority_declaration`
--

CREATE TABLE `server_priority_declaration` (
  `ID` int(11) NOT NULL COMMENT 'Table ID',
  `serverID` int(11) NOT NULL COMMENT 'Server ID',
  `clientID` varchar(39) NOT NULL COMMENT 'ID of who made the request',
  `instructionID` int(11) NOT NULL COMMENT 'Instruction ID',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`),
  KEY (`serverID`, `clientID`, `instructionID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='The declaration of the server instructions to do';

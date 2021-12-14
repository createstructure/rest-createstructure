--
-- Table structure for table `client_account`
--

CREATE TABLE `client_account` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Table ID',
  `clientID` varchar(39) NOT NULL COMMENT 'Client ID',
  `accountID` int(11) NOT NULL COMMENT 'Account ID',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`),
  KEY (`clientID`, `accountID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table for connecting users to various accounts';

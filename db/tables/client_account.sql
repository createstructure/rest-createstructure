--
-- Table structure for table `client_account`
--

CREATE TABLE `client_account` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Table ID',
  `client_ID` varchar(39) NOT NULL COMMENT 'Client ID',
  `account_ID` int(11) NOT NULL COMMENT 'Account ID',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`),
  KEY (`client_ID`, `account_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table for connecting users to various accounts';

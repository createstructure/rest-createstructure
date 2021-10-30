--
-- Table structure for table `server_secrets`
--

CREATE TABLE `server_secrets` (
  `ID` int(11) NOT NULL COMMENT 'Table ID',
  `server_ID` int(11) NOT NULL COMMENT 'Server ID',
  `server_password` text NOT NULL COMMENT 'Server password',
  `server_public_key` text NOT NULL COMMENT 'Server public key',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`),
  KEY (`server_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='The secrets of the servers';

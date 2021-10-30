--
-- Table structure for table `server_list`
--

CREATE TABLE `server_list` (
  `ID` int(11) NOT NULL COMMENT 'Table ID',
  `name` text NOT NULL COMMENT 'Server name',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='List of the servers';

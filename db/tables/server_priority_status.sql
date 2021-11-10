--
-- Table structure for table `server_priority_status`
--

CREATE TABLE `server_priority_status` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Table ID',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='The status of the instruction execution';

--
-- Add data for table `server_priority_status`
--

INSERT INTO `server_priority_status` (`description`) VALUES
('To do'),
('Done');

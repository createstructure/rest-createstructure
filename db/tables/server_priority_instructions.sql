--
-- Table structure for table `server_priority_instructions`
--

CREATE TABLE `server_priority_instructions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Table ID',
  `name` text NOT NULL COMMENT 'Name of the instuction',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='List of possible server instructions';

--
-- Add data for table `server_priority_instructions`
--

INSERT INTO `server_priority_instructions` (`name`, `description`) VALUES
('test', 'Test routine'),
('update', 'Do an update of all the needed packages'),
('shutdown', 'Shutdown the server'),
('reboot', 'Reboot the server');

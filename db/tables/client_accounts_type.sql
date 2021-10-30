--
-- Table structure for table `client_accounts_type`
--

CREATE TABLE `client_accounts_type` (
  `ID` int(11) NOT NULL COMMENT 'Table ID',
  `active` tinyint(1) NOT NULL COMMENT '"true" if it represents an active account, otherwise "false"',
  `super` tinyint(1) NOT NULL COMMENT '"true" if it represents a super account, otherwise "false"',
  `max_day` int(11) NOT NULL COMMENT 'Maximum repository creation for every day',
  `max_h` int(11) NOT NULL COMMENT 'Maximum repository creation for every hour',
  `max_m` int(11) NOT NULL COMMENT 'Maximum repository creation for every minute',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`),
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table for the definition of available accounts';

--
-- Add data for table `client_accounts_type`
--

INSERT INTO `client_accounts_type` (`ID`, `active`, `super`, `max_day`, `max_h`, `max_m`, `description`) VALUES
(0, 1, 1, 10000, 1000, 100, 'Super user'),
(1, 0, 0, 0, 0, 0, 'Disabled account'),
(6488, 1, 0, 100, 20, 1, 'Free account');

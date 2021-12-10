--
-- Table structure for table `repo_status`
--

CREATE TABLE `repo_status` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Table ID',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table for the possible stages of creating a repository';

--
-- Add data for table `repo_status`
--

INSERT INTO `repo_status` (`description`) VALUES
('To do'),
('Reserved'),
('Done');

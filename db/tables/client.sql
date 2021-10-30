--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `github_username` varchar(39) NOT NULL COMMENT 'Github account username',
  `description` text DEFAULT NULL COMMENT 'An optional description',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'The time of the last change',
  PRIMARY KEY (`github_username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table listing service customers';

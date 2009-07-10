--
-- Table structure for table `beds`
--

CREATE TABLE IF NOT EXISTS `beds` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `station_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `occupancies`
--

CREATE TABLE IF NOT EXISTS `occupancies` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `patient_id` int(11) unsigned NOT NULL,
  `bed_id` int(11) unsigned NOT NULL,
  `station_id` int(10) unsigned NOT NULL,
  `occupiedFrom` date NOT NULL,
  `occupiedTo` date NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE IF NOT EXISTS `patients` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `socialSecurityNumber` varchar(16) NOT NULL,
  `birthDate` date NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `socialSecurityNumber` (`socialSecurityNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `stations`
--

CREATE TABLE IF NOT EXISTS `stations` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

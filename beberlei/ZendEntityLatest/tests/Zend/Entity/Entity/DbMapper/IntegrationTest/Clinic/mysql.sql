--
-- Table structure for table `beds`
--

CREATE TABLE IF NOT EXISTS `zfclinic_beds` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `station_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `occupancies`
--

CREATE TABLE IF NOT EXISTS `zfclinic_occupancies` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `patient_id` int(11) unsigned NOT NULL,
  `bed_id` int(11) unsigned NOT NULL,
  `station_id` int(10) unsigned NOT NULL,
  `occupied_from` date NOT NULL,
  `occupied_to` date NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE IF NOT EXISTS `zfclinic_patients` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `social_security_number` varchar(16) NOT NULL,
  `birth_date` date NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `social_security_number` (`social_security_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `stations`
--

CREATE TABLE IF NOT EXISTS `zfclinic_stations` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

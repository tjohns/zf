SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `zendentitydemo`
--

-- --------------------------------------------------------

--
-- Table structure for table `zfaccounts`
--

CREATE TABLE IF NOT EXISTS `zfaccounts` (
  `account_id` int(10) unsigned NOT NULL auto_increment,
  `account_name` varchar(100) NOT NULL,
  PRIMARY KEY  (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `zfaccounts`
--

INSERT INTO `zfaccounts` (`account_id`, `account_name`) VALUES
(1, 'dduck'),
(2, 'goofy'),
(3, 'mmouse');

-- --------------------------------------------------------

--
-- Table structure for table `zfbugs`
--

CREATE TABLE IF NOT EXISTS `zfbugs` (
  `bug_id` int(11) NOT NULL auto_increment,
  `bug_description` varchar(100) default NULL,
  `bug_status` varchar(20) default NULL,
  `bug_created` datetime default NULL,
  `reported_by` int(10) unsigned NOT NULL,
  `assigned_to` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`bug_id`),
  KEY `reported_by` (`reported_by`),
  KEY `assigned_to` (`assigned_to`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `zfbugs`
--

INSERT INTO `zfbugs` (`bug_id`, `bug_description`, `bug_status`, `bug_created`, `reported_by`, `assigned_to`) VALUES
(1, 'System needs electricity to run', 'NEW', '2007-04-01 00:00:00', 2, 3),
(2, 'Implement Do What I Mean function', 'VERIFIED', '2007-04-02 00:00:00', 2, 3),
(3, 'Where are my keys?', 'FIXED', '2007-04-03 00:00:00', 1, 3),
(4, 'Bug no product', 'INCOMPLETE', '2007-04-04 00:00:00', 3, 2),
(5, 'Something does not work!', 'NEW', '2009-08-30 20:09:22', 1, 2),
(6, 'Something does not work!', 'NEW', '2009-08-30 20:09:37', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `zfbugs_products`
--

CREATE TABLE IF NOT EXISTS `zfbugs_products` (
  `bug_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY  (`bug_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `zfbugs_products`
--

INSERT INTO `zfbugs_products` (`bug_id`, `product_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 3),
(3, 2),
(3, 3),
(5, 1),
(6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `zfproducts`
--

CREATE TABLE IF NOT EXISTS `zfproducts` (
  `product_id` int(11) NOT NULL auto_increment,
  `product_name` varchar(100) default NULL,
  PRIMARY KEY  (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `zfproducts`
--

INSERT INTO `zfproducts` (`product_id`, `product_name`) VALUES
(1, 'Windows'),
(2, 'Linux'),
(3, 'OS X');

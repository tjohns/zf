CREATE TABLE IF NOT EXISTS `university_courses` (
  `course_id` int(11) NOT NULL auto_increment,
  `course_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`course_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

CREATE TABLE IF NOT EXISTS `university_students` (
  `student_id` int(10) unsigned NOT NULL auto_increment,
  `student_name` varchar(255) NOT NULL,
  `student_campus_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`student_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

CREATE TABLE IF NOT EXISTS `university_students_semester_courses` (
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  PRIMARY KEY  (`student_id`,`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `university_courses` (
  `course_id` int(11) NOT NULL auto_increment,
  `course_name` varchar(255) NOT NULL,
  `teacher_id` INT(10) unsigned NOT NULL,
  PRIMARY KEY  (`course_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `university_professors` (
    `professor_id` int(10) unsigned NOT NULL auto_increment,
    `name` varchar(255) NOT NULL,
    `salary` int(10) unsigned NOT NULL,
    PRIMARY KEY (`professor_id`)
) ENGINE=InnoDb DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `university_students` (
  `student_id` int(10) unsigned NOT NULL auto_increment,
  `student_name` varchar(255) NOT NULL,
  `student_campus_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`student_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `university_students_semester_courses` (
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  PRIMARY KEY  (`student_id`,`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

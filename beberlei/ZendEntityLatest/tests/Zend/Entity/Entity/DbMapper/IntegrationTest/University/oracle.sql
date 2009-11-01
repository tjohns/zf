DROP TABLE "university_courses";

CREATE TABLE "university_courses" (
        "course_id" number NOT NULL,
        "course_name" varchar2(255) NOT NULL,
        "teacher_id" number DEFAULT 0 NOT NULL
);

ALTER TABLE "university_courses" ADD CONSTRAINT "university_courses_pkey" PRIMARY KEY ( "course_id" );

CREATE INDEX "teacher_id_fkey" ON "university_courses" ( "teacher_id" );

DROP TABLE "university_professors";

CREATE TABLE "university_professors" (
        "professor_id" number NOT NULL,
        "name" varchar2(255) NOT NULL,
        "salary" number DEFAULT 0 NOT NULL
);

ALTER TABLE "university_professors" ADD CONSTRAINT "university_professors_pkey" PRIMARY KEY ( "professor_id" );

DROP TABLE "university_stu_sem_courses";

CREATE TABLE "university_stu_sem_courses" (
        "course_id" number,
        "student_id" number
);

ALTER TABLE "university_stu_sem_courses" ADD CONSTRAINT "university_stu_sem_courses_pkey" PRIMARY KEY ( "course_id", "student_id" );

DROP TABLE "university_students";

CREATE TABLE "university_students" (
        "student_id" number NOT NULL,
        "student_name" varchar2(255) NOT NULL,
        "student_campus_id" number DEFAULT 0 NOT NULL
);

ALTER TABLE "university_students" ADD CONSTRAINT "university_students_pkey" PRIMARY KEY ( "student_id" );
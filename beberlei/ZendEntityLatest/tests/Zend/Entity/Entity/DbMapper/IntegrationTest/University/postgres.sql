DROP TABLE "university_courses";

CREATE TABLE "university_courses" (
        "course_id" serial PRIMARY KEY NOT NULL,
        "course_name" varchar(255) NOT NULL,
        "teacher_id" bigint NOT NULL DEFAULT 0
);

CREATE INDEX "teacher_id_fkey" ON "university_courses" ( "teacher_id" );

DROP TABLE "university_professors";

CREATE TABLE "university_professors" (
        "professor_id" serial PRIMARY KEY NOT NULL,
        "name" varchar(255) NOT NULL,
        "salary" bigint NOT NULL DEFAULT 0
);

DROP TABLE "university_stu_sem_courses";

CREATE TABLE "university_stu_sem_courses" (
        "student_id" bigint,
        "course_id" bigint
);

ALTER TABLE "university_stu_sem_courses" ADD CONSTRAINT "university_stu_sem_courses_pkey" PRIMARY KEY ( "course_id", "student_id" );

DROP TABLE "university_students";

CREATE TABLE "university_students" (
        "student_id" serial PRIMARY KEY NOT NULL,
        "student_name" varchar(255) NOT NULL,
        "student_campus_id" bigint NOT NULL DEFAULT 0
);


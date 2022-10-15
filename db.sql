USE `rmu_admissions`;

/*
Tables for form purchase
*/
DROP TABLE IF EXISTS `admission_period`;
CREATE TABLE `admission_period` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `info` TEXT,
    `active` TINYINT DEFAULT 0
);
INSERT INTO `admission_period`(`start_date`,`end_date`, `active`) 
VALUES('2022-07-01', '2022-10-01', 1);

DROP TABLE IF EXISTS `form_type`;
CREATE TABLE `form_type` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `amount` DECIMAL(6,2) NOT NULL
);
INSERT INTO `form_type`(`name`, `amount`) 
VALUES ("Postgraduate", 250), ("Undergraduate (Degree)", 180), 
("Undergraduate (Diploma)", 120), ("Short courses", 100);

DROP TABLE IF EXISTS `payment_method`;
CREATE TABLE `payment_method` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL
);
INSERT INTO `payment_method`(`name`) VALUES ("Credit Card"), ("Mobile Money"), ("Bank Deposit");

DROP TABLE IF EXISTS `vendor_details`;
CREATE TABLE `vendor_details` (
    `id` INT(11) PRIMARY KEY,
    `type` VARCHAR(10) NOT NULL,
    `vendor_name` VARCHAR(50) NOT NULL,
    `tin` VARCHAR(15) NOT NULL,
    `email_address` VARCHAR(100),
    `country_name` VARCHAR(30),
    `country_code` VARCHAR(30) NOT NULL,
    `phone_number` VARCHAR(13) NOT NULL,
    `address` VARCHAR(50),
    `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP()
);
INSERT INTO `vendor_details`(`id`, `type`,`vendor_name`, `tin`, `country_code`, `phone_number`) VALUES 
(1665605087, 'ONLINE', 'RMU ONLINE', 'RMU123', '+233', '0555351068'), 
(1665605341, 'VENDOR', 'RMU CAMPUS', 'RMU123', '+233', '0555351068'), 
(1665605866, 'VENDOR', 'MAXIM RETAIL', 'T14529045', '+233', '0555351068');

DROP TABLE IF EXISTS `vendor_login`;
CREATE TABLE `vendor_login` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_name` VARCHAR(255) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    
    `vendor` INT(11) NOT NULL,
    CONSTRAINT `fk_vendor_login` FOREIGN KEY (`vendor`) 
    REFERENCES `vendor_details`(`id`) ON UPDATE CASCADE,

    `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP()
);

INSERT INTO `vendor_login`(`vendor`,`user_name`,`password`) VALUES 
(1665605866, 'd8ded753c6fd237dc576c1846382387e7e739337', '$2y$10$jmxuunWRqwB2KgT2jIypwufas3dPtqT9f21gdKT9lOOlNGNQCqeMC'),
(1665605341, 'bc4f6e0e173b58999ff3cd1253cc97c1924ecc2e', '$2y$10$jmxuunWRqwB2KgT2jIypwufas3dPtqT9f21gdKT9lOOlNGNQCqeMC');

DROP TABLE IF EXISTS `purchase_detail`; 
CREATE TABLE `purchase_detail` (
    `id` INT(11) PRIMARY KEY,

    `first_name` VARCHAR(50) NOT NULL,
    `last_name` VARCHAR(50) NOT NULL,
    `email_address` VARCHAR(100),
    `country_name` VARCHAR(30) NOT NULL,
    `country_code` VARCHAR(30) NOT NULL,
    `phone_number` VARCHAR(15) NOT NULL,
    `amount` DECIMAL(6,2) NOT NULL,

    `app_number` VARCHAR(10) NOT NULL,
    `pin_number` VARCHAR(10) NOT NULL,

    `status_code` VARCHAR(3), -- added
    `device_info` VARCHAR(200), -- added
    `ip_address` VARCHAR(15), -- added
    `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    
    `vendor` INT(11) NOT NULL, -- added
    `form_type` INT NOT NULL,
    `admission_period` INT(11) NOT NULL, -- added
    `payment_method` VARCHAR(20),

    CONSTRAINT `fk_purchase_vendor_details` FOREIGN KEY (`vendor`) REFERENCES `vendor_details`(`id`) ON UPDATE CASCADE,
    CONSTRAINT `fk_purchase_form_type` FOREIGN KEY (`form_type`) REFERENCES `form_type`(`id`) ON UPDATE CASCADE,
    CONSTRAINT `fk_purchase_admission_period` FOREIGN KEY (`admission_period`) REFERENCES `admission_period`(`id`) ON UPDATE CASCADE

);

DROP TABLE IF EXISTS `applicants_login`;
CREATE TABLE `applicants_login` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `app_number` VARCHAR(255) UNIQUE NOT NULL,
    `pin` VARCHAR(255) NOT NULL,
    `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    
    `purchase_id` INT NOT NULL,
    CONSTRAINT `fk_purchase_id` FOREIGN KEY (`purchase_id`) REFERENCES `purchase_detail`(`id`) ON UPDATE CASCADE
);

/*
Tables for applicants form registration
*/

DROP TABLE IF EXISTS `programs`;
CREATE TABLE `programs` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `type` INT NOT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    CONSTRAINT `fk_prog_form_type` FOREIGN KEY (`type`) REFERENCES `form_type`(`id`) ON UPDATE CASCADE
);
INSERT INTO `programs`(`type`, `name`) VALUES 
(1, 'MSc. Environmental Engineering'), (1, 'MA. Ports and Shipping Administration'), 
(2, 'BSc. Computer Science'), (2, 'BSc. Electrical Engineering'), (2, 'BSc. Marine Engineering'),
(2, 'Diploma Computer Engineering'), (2, 'Diploma Electrical Engineering'), (2, 'Diploma Marine Engineering'),
(3, 'Marine Engine Mechanic'), (3, 'Marine Refrigeration Mechanic');

DROP TABLE IF EXISTS `halls`;
CREATE TABLE `halls` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP()
);
INSERT INTO `halls`(`name`) VALUES ('Cadet Hostel'), ('Non-cadet Hostel');

DROP TABLE IF EXISTS `grades`;
CREATE TABLE `grades` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `grade` VARCHAR(2) NOT NULL,
    `type` VARCHAR(15)
);
INSERT INTO `grades`(`grade`, `type`) 
VALUES ('A1', 'WASSCE'), ('B2', 'WASSCE'), ('B3', 'WASSCE'), ('C4', 'WASSCE'), ('C5', 'WASSCE'), 
        ('C6', 'WASSCE'), ('D7', 'WASSCE'), ('E8', 'WASSCE'), ('F9', 'WASSCE'),
        ('A', 'SSCE'), ('B', 'SSCE'), ('C', 'SSCE'), ('D', 'SSCE'), ('E', 'SSCE'), ('F', 'SSCE');
        

DROP TABLE IF EXISTS `high_shcool_courses`;
CREATE TABLE `high_shcool_courses` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `course` VARCHAR(25) NOT NULL,
    `type` VARCHAR(15)
);

INSERT INTO `high_shcool_courses`(`type`, `course`) VALUES 
("core", "CORE MATHEMATICS"), ("core", "ENGLISH LANGUAGE"), ("core", "INTEGRATED SCIENCE"), 
("core", "SOCIAL STUDIES"), ("elective", "BUSINESS"), ("elective", "GENERAL ARTS"), 
("elective", "GENERAL SCIENCE"), ("elective", "HOME ECONOMICS"), ("elective", "VISUAL ARTS"), ("elective", "TECHNICAL") ;

DROP TABLE IF EXISTS `high_sch_elective_subjects`;
CREATE TABLE `high_sch_elective_subjects` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `subject` VARCHAR(25) NOT NULL,
    `course` INT NOT NULL,
    CONSTRAINT `fk_elective_sbjs` FOREIGN KEY (`course`) REFERENCES `high_shcool_courses`(`id`) ON UPDATE CASCADE
);

INSERT INTO `high_sch_elective_subjects`(`course`, `subject`) VALUES 
(5, 'ACCOUNTING'), (5, 'BUSINESS MANAGEMENT'), (5, 'ECONOMICS'), (5, 'PRINCIPLE OF COSTING'), (5, 'ELECTIVE MATHS'), (5, 'FRENCH'),
(6, 'LITERATURE IN ENGLISH'), (6, 'FRENCH'), (6, 'ECONOMICS'), (6, 'GEOGRAPHY'), (6, 'HISTORY'), (6, 'GOVERNMENT'), (6, 'RELIGIOUS STUDIES'),
(7, 'PHYSICS'), (7, 'CHEMISTRY'), (7, 'ELECTIVE MATHS'), (7, 'BIOLOGY'), (7, 'GEOGRAPHY'),
(8, 'MANAGEMENT IN LIVING'), (8, 'FOOD AND NUTRITION'), (8, 'GENERAL KNOWLEDGE IN ARTS'), (8, 'TEXTILE'), (8, 'FRENCH'), (8, 'ECONOMICS'),
(9, 'GENERAL KNOWLEDGE IN ARTS'), (9, 'TEXTILE'), (9, 'GRAPHIC DESIGN'), (9, 'LITERATURE IN ENGLISH'), (9, 'FRENCH'),
(9, 'ECONOMICS'), (9, 'BASKETRY'), (9, 'LEATHER WORK'), (9, 'PICTURE MAKING'), (9, 'CERAMICS AND SCULPTURE'),
(10, 'Building Construction Technology'), (10, 'Carpentry And Joinery'), (10, 'Catering'), 
(10, 'Electrical Installation Work'), (10, 'Electronics'), (10, 'Fashion And Design'), 
(10, 'General Textiles'), (10, 'Industrial Mechanics'), (10, 'Mechanical Engineering Craft Practice'), 
(10, 'Metal Work'), (10, 'Photography'), (10, 'Plumbing Craft'), (10, 'Printing Craft'), (10, 'Welding And Fabrication'), (10, 'Wood Work');

/*Application Data*/

DROP TABLE IF EXISTS `applicant_uploads`;
CREATE TABLE `applicant_uploads` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `type` VARCHAR(25), -- photo, certificate, transcript
    `file_name` VARCHAR(50),
    `app_login` INT NOT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    CONSTRAINT `fk_uploaded_files` FOREIGN KEY (`app_login`) REFERENCES `applicants_login`(`id`) ON UPDATE CASCADE
);

ALTER TABLE `applicant_uploads` ADD COLUMN `edu_code` INT(11) AFTER `type`;

DROP TABLE IF EXISTS `personal_information`;
CREATE TABLE `personal_information` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,

    -- Legal Name
    `prefix` VARCHAR(10),
    `first_name` VARCHAR(100),
    `middle_name` VARCHAR(100),
    `last_name` VARCHAR(100),
    `suffix` VARCHAR(10),

    -- Personal Details
    `gender` VARCHAR(7),
    `dob` DATE,
    `marital_status` VARCHAR(25),
    `nationality` VARCHAR(25),
    `country_res` VARCHAR(25),
    `disability` TINYINT,
    `disability_descript` VARCHAR(25),
    `photo` VARCHAR(25),

    -- Place of birth
    `country_birth` VARCHAR(25),
    `spr_birth` VARCHAR(25),
    `city_birth` VARCHAR(25),

    -- Languages Spoken
    `english_native` TINYINT,
    `other_language` VARCHAR(25),

    -- Address
    `postal_addr` VARCHAR(255),
    `postal_town` VARCHAR(50),
    `postal_spr` VARCHAR(50),
    `postal_country` VARCHAR(50),

    -- Contact
    `phone_no1_code` VARCHAR(5),
    `phone_no1` VARCHAR(13),
    `phone_no2_code` VARCHAR(5),
    `phone_no2` VARCHAR(13),
    `email_addr` VARCHAR(50),
    
    -- Alternate/Parent/Guardian Information

    -- Legal Name
    `p_prefix` VARCHAR(10),
    `p_first_name` VARCHAR(100),
    `p_last_name` VARCHAR(100),
    `p_occupation` VARCHAR(50),
    `p_phone_no_code` VARCHAR(5),
    `p_phone_no` VARCHAR(13),
    `p_email_addr` VARCHAR(50),

    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),

    `app_login` INT NOT NULL,
    CONSTRAINT `fk_app_pf` FOREIGN KEY (`app_login`) REFERENCES `applicants_login`(`id`) ON UPDATE CASCADE
);

ALTER TABLE `personal_information` ADD COLUMN `speaks_english` TINYINT;

DROP TABLE IF EXISTS `awaiting_certs`;
CREATE TABLE `awaiting_certs` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,

    `awaiting` TINYINT DEFAULT 0,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    
    `app_login` INT NOT NULL,
    CONSTRAINT `fk_app_a_certs` FOREIGN KEY (`app_login`) REFERENCES `applicants_login`(`id`) ON UPDATE CASCADE
);

DROP TABLE IF EXISTS `academic_background`;
CREATE TABLE `academic_background` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `s_number` INT(11) UNIQUE NOT NULL,

    -- Certificate info
    `school_name` VARCHAR(100),
    `country` VARCHAR(100),
    `region` VARCHAR(100),
    `city` VARCHAR(100),
    
    `cert_type` VARCHAR(20),
    `index_number` VARCHAR(20),
    `month_started` VARCHAR(3),
    `year_started` VARCHAR(4),
    `month_completed` VARCHAR(3),
    `year_completed` VARCHAR(4),
    
    `course_of_study` VARCHAR(100),

    -- Transcripts and cerfiticate files
    `certificate` VARCHAR(50),
    `transcript` VARCHAR(50),

    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),

    `app_login` INT NOT NULL,
    CONSTRAINT `fk_app_aca_bac` FOREIGN KEY (`app_login`) REFERENCES `applicants_login`(`id`) ON UPDATE CASCADE
);

DROP TABLE IF EXISTS `high_school_results`;
CREATE TABLE `high_school_results` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `type` VARCHAR(10) DEFAULT 'core',
    `subject` VARCHAR(100) NOT NULL,
    `grade` VARCHAR(2) NOT NULL,
    `acad_back_id` INT NOT NULL, -- Referencing academic background
    CONSTRAINT `fk_grades_aca_bac` FOREIGN KEY (`acad_back_id`) REFERENCES `academic_background`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
);

DROP TABLE IF EXISTS `program_info`;
CREATE TABLE `program_info` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,

    -- programs
    `first_prog` VARCHAR(100),
    `second_prog` VARCHAR(100),

    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),

    `app_login` INT NOT NULL,   
    CONSTRAINT `fk_app_prog_info` FOREIGN KEY (`app_login`) REFERENCES `applicants_login`(`id`) ON UPDATE CASCADE
);

DROP TABLE IF EXISTS `previous_uni_records`;
CREATE TABLE `previous_uni_records` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `pre_uni_rec` TINYINT DEFAULT 0,   
    `name_of_uni` VARCHAR(150),   
    `program` VARCHAR(150),  

    `month_enrolled` VARCHAR(3),
    `year_enrolled` VARCHAR(4),
    `completed` TINYINT DEFAULT 0,
    `month_completed` VARCHAR(3),
    `year_completed` VARCHAR(4),

    `state` VARCHAR(25),
    `reasons` TEXT,

    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),

    `app_login` INT NOT NULL,   
    CONSTRAINT `fk_app_prev_uni` FOREIGN KEY (`app_login`) REFERENCES `applicants_login`(`id`) ON UPDATE CASCADE
);

CREATE TABLE `form_sections_chek` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `use_of_info` TINYINT DEFAULT 0,
    `personal` TINYINT DEFAULT 0,
    `education` TINYINT DEFAULT 0,
    `programme` TINYINT DEFAULT 0,
    `uploads` TINYINT DEFAULT 0,
    `app_login` INT NOT NULL,   
    CONSTRAINT `fk_app_form_sec_check` FOREIGN KEY (`app_login`) REFERENCES `applicants_login`(`id`) ON UPDATE CASCADE
);

SELECT `purchase_detail`.`form_type` FROM `purchase_detail`, `applicants_login`
WHERE `applicants_login`.`purchase_id` = `purchase_detail`.`id` AND `applicants_login`.`id` = 1;



/*
    Restructuring DB according to sections in and questions
*/

/* Website Pages */
DROP TABLE IF EXISTS `web_pages`;
CREATE TABLE `web_pages` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    -- `upgid` VARCHAR(255) UNIQUE NOT NULL,
    `page_name` VARCHAR(150) NOT NULL UNIQUE
);
INSERT INTO `web_pages`(`page_name`) VALUES
('Use of Information'),('Personal Information'),('Education Background'),
('Programme Information'),('Uploads'),('Declaration');

/*Page Sections*/
DROP TABLE IF EXISTS `page_sections`;
CREATE TABLE `page_sections` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    -- `ustid` VARCHAR(255) UNIQUE NOT NULL,
    `name` VARCHAR(150) NOT NULL UNIQUE,
    `description` VARCHAR(255),
    `page` INT NOT NULL,   
    CONSTRAINT `fk_page_section` FOREIGN KEY (`page`) REFERENCES `web_pages`(`id`) ON UPDATE CASCADE
);
INSERT INTO `page_sections`(`name`, `page`) VALUES
('Use of Information Agreement', 1),                             
('Legal Name', 2),('Personal Details', 2),('Place of Birth', 2),('Language', 2),
('Address', 2),('Contact', 2),('Parent/Guardian', 2),('Education', 3),
('Programmes', 4),('Passport Picture', 5),('Certificates', 5),('Transcripts', 5);

/*Section Questions*/
DROP TABLE IF EXISTS `section_questions`;
CREATE TABLE `section_questions` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    -- `uqtid` VARCHAR(255) UNIQUE NOT NULL,
    `question` VARCHAR(255) NOT NULL,
    `type` VARCHAR(25) NOT NULL DEFAULT 'text', -- text, dropdown, radio, checkbox, date, etc.
    `place_holder` VARCHAR(25),
    `required` TINYINT DEFAULT 1,
    `section` INT NOT NULL,
    CONSTRAINT `fk_section_question` FOREIGN KEY (`section`) REFERENCES `page_sections`(`id`) ON UPDATE CASCADE
);
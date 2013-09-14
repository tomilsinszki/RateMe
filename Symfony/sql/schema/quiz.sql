CREATE TABLE `answer` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `text` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `answer_index_text` (`text`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `question_group` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_question_group_name` (`name`),
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `question` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `group_id` INT(11) DEFAULT NULL,
  `text` VARCHAR(255) NOT NULL,
  `correct_answer_id` INT(11) NOT NULL,
  `wrong_answer1_id` INT(11) NOT NULL,
  `wrong_answer2_id` INT(11) NOT NULL,
  `last_occured_at` DATETIME DEFAULT NULL NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_question_text` (`text`),
  KEY `question_FI_question_group` (`group_id`),
  KEY `question_FI_correct_answer` (`correct_answer_id`),
  KEY `question_FI_wrong_answer1` (`wrong_answer1_id`),
  KEY `question_FI_wrong_answer2` (`wrong_answer2_id`),
  CONSTRAINT `question_FK_question_group` FOREIGN KEY (`group_id`) REFERENCES `question_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `question_FK_correct_answer` FOREIGN KEY (`correct_answer_id`) REFERENCES `answer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `question_FK_wrong_answer1` FOREIGN KEY (`wrong_answer1_id`) REFERENCES `answer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `question_FK_wrong_answer2` FOREIGN KEY (`wrong_answer2_id`) REFERENCES `answer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `quiz` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `rateable_id` INT(11) NOT NULL,
  `question_id` INT(11) NOT NULL,
  `given_answer_id` INT(11) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `quiz_FI_rateable` (`rateable_id`),
  KEY `quiz_FI_question` (`question_id`),
  KEY `quiz_FI_given_answer` (`given_answer_id`),
  CONSTRAINT `quiz_FK_rateable` FOREIGN KEY (`rateable_id`) REFERENCES `rateable` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `quiz_FK_question` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `quiz_FK_given_answer` FOREIGN KEY (`given_answer_id`) REFERENCES `answer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

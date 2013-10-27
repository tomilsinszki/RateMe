SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `rateable_collection` 
ADD COLUMN `question_order_id` INT(11) NOT NULL AFTER `company_id`,
ADD INDEX `IDX_CC0020A0EE97DD34` (`question_order_id` ASC);

CREATE TABLE IF NOT EXISTS `sub_rating` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `answer_id` INT(11) NOT NULL,
  `rating_id` INT(11) NOT NULL,
  `created` DATETIME NOT NULL,
  `updated` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `IDX_6AD8696EAA334807` (`answer_id` ASC),
  INDEX `IDX_6AD8696EA32EFC6` (`rating_id` ASC),
  CONSTRAINT `FK_6AD8696EA32EFC6`
    FOREIGN KEY (`rating_id`)
    REFERENCES `rating` (`id`),
  CONSTRAINT `FK_6AD8696EAA334807`
    FOREIGN KEY (`answer_id`)
    REFERENCES `sub_rating_answer` (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `sub_rating_answer` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `question_id` INT(11) NOT NULL,
  `type_id` INT(11) NOT NULL,
  `text` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `is_enabled` TINYINT(1) NOT NULL,
  `created` DATETIME NOT NULL,
  `updated` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `IDX_3A82E7731E27F6BF` (`question_id` ASC),
  INDEX `IDX_3A82E773C54C8C93` (`type_id` ASC),
  CONSTRAINT `FK_3A82E773C54C8C93`
    FOREIGN KEY (`type_id`)
    REFERENCES `sub_rating_answer_type` (`id`),
  CONSTRAINT `FK_3A82E7731E27F6BF`
    FOREIGN KEY (`question_id`)
    REFERENCES `sub_rating_question` (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `sub_rating_answer_type` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `question_type_id` INT(11) NOT NULL,
  `name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `IDX_E339D53BCB90598E` (`question_type_id` ASC),
  CONSTRAINT `FK_E339D53BCB90598E`
    FOREIGN KEY (`question_type_id`)
    REFERENCES `sub_rating_question_type` (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `sub_rating_question` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `type_id` INT(11) NULL DEFAULT NULL,
  `collection_id` INT(11) NOT NULL,
  `sequence` INT(11) NULL DEFAULT NULL,
  `title` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `text` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `target` INT(11) NOT NULL,
  `created` DATETIME NOT NULL,
  `updated` DATETIME NOT NULL,
  `deleted` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `UNIQ_F2718C965286D72B514956FD` (`sequence` ASC, `collection_id` ASC),
  INDEX `IDX_F2718C96C54C8C93` (`type_id` ASC),
  INDEX `IDX_F2718C96514956FD` (`collection_id` ASC),
  CONSTRAINT `FK_F2718C96514956FD`
    FOREIGN KEY (`collection_id`)
    REFERENCES `rateable_collection` (`id`),
  CONSTRAINT `FK_F2718C96C54C8C93`
    FOREIGN KEY (`type_id`)
    REFERENCES `sub_rating_question_type` (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `sub_rating_question_order` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `sub_rating_question_type` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

ALTER TABLE `rateable_collection` 
ADD CONSTRAINT `FK_CC0020A0EE97DD34`
  FOREIGN KEY (`question_order_id`)
  REFERENCES `sub_rating_question_order` (`id`);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

INSERT INTO `sub_rating_question_type` (`name`) VALUES 
('yes/no'), 
('scale');

INSERT INTO `sub_rating_answer_type` (`name`, `question_type_id`) SELECT 'yes' AS name, t.id AS question_type_id FROM `sub_rating_question_type` t WHERE t.name='yes/no';
INSERT INTO `sub_rating_answer_type` (`name`, `question_type_id`) SELECT 'no' AS name, t.id AS question_type_id FROM `sub_rating_question_type` t WHERE t.name='yes/no';
INSERT INTO `sub_rating_answer_type` (`name`, `question_type_id`) SELECT 'n/a' AS name, t.id AS question_type_id FROM `sub_rating_question_type` t WHERE t.name='yes/no';

INSERT INTO `sub_rating_answer_type` (`name`, `question_type_id`) SELECT '1' AS name, t.id AS question_type_id FROM `sub_rating_question_type` t WHERE t.name='scale';
INSERT INTO `sub_rating_answer_type` (`name`, `question_type_id`) SELECT '2' AS name, t.id AS question_type_id FROM `sub_rating_question_type` t WHERE t.name='scale';
INSERT INTO `sub_rating_answer_type` (`name`, `question_type_id`) SELECT '3' AS name, t.id AS question_type_id FROM `sub_rating_question_type` t WHERE t.name='scale';
INSERT INTO `sub_rating_answer_type` (`name`, `question_type_id`) SELECT '4' AS name, t.id AS question_type_id FROM `sub_rating_question_type` t WHERE t.name='scale';
INSERT INTO `sub_rating_answer_type` (`name`, `question_type_id`) SELECT '5' AS name, t.id AS question_type_id FROM `sub_rating_question_type` t WHERE t.name='scale';
INSERT INTO `sub_rating_answer_type` (`name`, `question_type_id`) SELECT 'n/a' AS name, t.id AS question_type_id FROM `sub_rating_question_type` t WHERE t.name='scale';

INSERT INTO `sub_rating_question_order` (`name`) VALUES 
('sequential'), 
('random'), 
('weighted random'), 
('balanced');

UPDATE `rateable_collection` SET `question_order_id`=(SELECT `id` FROM `sub_rating_question_order` WHERE `name`='sequential');


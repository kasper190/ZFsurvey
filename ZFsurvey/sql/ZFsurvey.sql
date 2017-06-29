SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP SCHEMA IF EXISTS `ZFsurvey` ;
CREATE SCHEMA IF NOT EXISTS `ZFsurvey` DEFAULT CHARACTER SET utf8 COLLATE utf8_polish_ci ;
USE `ZFsurvey` ;

-- -----------------------------------------------------
-- Table `ZFsurvey`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ZFsurvey`.`user` (
  `user_id` INT NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(45) NULL ,
  `password` VARCHAR(128) NULL ,
  `email` VARCHAR(128) NULL ,
  `salt` VARCHAR(128) NULL ,
  PRIMARY KEY (`user_id`) ,
  UNIQUE INDEX `username_UNIQUE` (`username` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_polish_ci;


-- -----------------------------------------------------
-- Table `ZFsurvey`.`survey`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ZFsurvey`.`survey` (
  `survey_id` INT NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(128) NULL ,
  `start_msg` VARCHAR(2000) NULL ,
  `end_msg` VARCHAR(2000) NULL ,
  `date` DATE NULL ,
  `ip_filter` TINYINT(1)  NULL DEFAULT false ,
  `status` TINYINT(1)  NULL ,
  `user_id` INT NOT NULL ,
  PRIMARY KEY (`survey_id`) ,
  INDEX `fk_survey_user` (`user_id` ASC) ,
  CONSTRAINT `fk_survey_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `ZFsurvey`.`user` (`user_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_polish_ci;


-- -----------------------------------------------------
-- Table `ZFsurvey`.`question`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ZFsurvey`.`question` (
  `question_id` INT NOT NULL AUTO_INCREMENT ,
  `question` VARCHAR(2000) NULL ,
  `type` VARCHAR(45) NULL ,
  `required` TINYINT(1)  NULL ,
  `survey_id` INT NOT NULL ,
  PRIMARY KEY (`question_id`) ,
  INDEX `fk_question_survey1` (`survey_id` ASC) ,
  CONSTRAINT `fk_question_survey1`
    FOREIGN KEY (`survey_id` )
    REFERENCES `ZFsurvey`.`survey` (`survey_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_polish_ci;


-- -----------------------------------------------------
-- Table `ZFsurvey`.`answer`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ZFsurvey`.`answer` (
  `answer_id` INT NOT NULL AUTO_INCREMENT ,
  `answer` VARCHAR(500) NULL ,
  `question_id` INT NOT NULL ,
  PRIMARY KEY (`answer_id`) ,
  INDEX `fk_answer_question1` (`question_id` ASC) ,
  CONSTRAINT `fk_answer_question1`
    FOREIGN KEY (`question_id` )
    REFERENCES `ZFsurvey`.`question` (`question_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_polish_ci;


-- -----------------------------------------------------
-- Table `ZFsurvey`.`respondent`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ZFsurvey`.`respondent` (
  `respondent_id` INT NOT NULL AUTO_INCREMENT ,
  `ip` VARCHAR(45) NULL ,
  `date` DATETIME NULL ,
  `survey_id` INT NOT NULL ,
  PRIMARY KEY (`respondent_id`) ,
  INDEX `fk_respondent_survey1` (`survey_id` ASC) ,
  CONSTRAINT `fk_respondent_survey1`
    FOREIGN KEY (`survey_id` )
    REFERENCES `ZFsurvey`.`survey` (`survey_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_polish_ci;


-- -----------------------------------------------------
-- Table `ZFsurvey`.`response`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ZFsurvey`.`response` (
  `response_id` INT NOT NULL AUTO_INCREMENT ,
  `response` VARCHAR(2000) NULL ,
  `respondent_id` INT NOT NULL ,
  `question_id` INT NULL ,
  `answer_id` INT NULL ,
  PRIMARY KEY (`response_id`) ,
  INDEX `fk_response_respondent1` (`respondent_id` ASC) ,
  INDEX `fk_response_question1` (`question_id` ASC) ,
  INDEX `fk_response_answer1` (`answer_id` ASC) ,
  CONSTRAINT `fk_response_respondent1`
    FOREIGN KEY (`respondent_id` )
    REFERENCES `ZFsurvey`.`respondent` (`respondent_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_response_question1`
    FOREIGN KEY (`question_id` )
    REFERENCES `ZFsurvey`.`question` (`question_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_response_answer1`
    FOREIGN KEY (`answer_id` )
    REFERENCES `ZFsurvey`.`answer` (`answer_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_polish_ci;


-- -----------------------------------------------------
-- Table `ZFsurvey`.`template`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ZFsurvey`.`template` (
  `template_id` INT NOT NULL AUTO_INCREMENT ,
  `logo` VARCHAR(10) NULL ,
  `radius` TINYINT(1)  NULL ,
  `titlefont` VARCHAR(10) NULL ,
  `mainfont` VARCHAR(10) NULL ,
  `background` VARCHAR(10) NULL ,
  `frame` VARCHAR(10) NULL ,
  `main` VARCHAR(10) NULL ,
  `survey_id` INT NOT NULL ,
  PRIMARY KEY (`template_id`) ,
  INDEX `fk_template_survey1` (`survey_id` ASC) ,
  CONSTRAINT `fk_template_survey1`
    FOREIGN KEY (`survey_id` )
    REFERENCES `ZFsurvey`.`survey` (`survey_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_polish_ci;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

UPDATE  `sysvals` SET  `sysval_value` =  '1| Perm-Relocation
2| Perm-IGA
3| Perm-Placement
4| Succession Planning
5| Legal
6| Nursing/Palliative care
7| Transport
8| Education
9| Food
10| Rent
11| Solidarity
12| Direct support
13| Medical Support
14| Training Support' WHERE  `sysvals`.`sysval_id` =133;

DROP TABLE IF EXISTS `form_master`;
CREATE TABLE  `form_master` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`title` VARCHAR( 255 ) NULL ,
`fields` TEXT NULL,
`digest` VARCHAR( 255 ) NULL DEFAULT NULL,
`registry` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  0,
`action` TEXT NULL,
`valid` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
`valid_change` DATE NULL DEFAULT NULL ,
`subs` VARCHAR( 255 ) NULL,
`touch` DATE NOT NULL
) ENGINE = MYISAM ;

drop table if exists `svsets`;
CREATE TABLE  `svsets` (
`id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`title` VARCHAR( 100 ) NOT NULL ,
`vtype` VARCHAR( 20 ) NOT NULL ,
`level` VARCHAR( 10 ) NOT NULL ,
`parent` INT( 11 ) NOT NULL ,
`status` TINYINT( 1 ) NOT NULL DEFAULT  '1',
`touch` DATE NOT NULL ,
`options` TEXT NOT NULL
) ENGINE = MYISAM ;

ALTER TABLE  `sysvals` ADD  `sysval_tport` ENUM(  '0',  '1' ) NOT NULL DEFAULT  '0';

CREATE TABLE  `staff_position` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`contact_id` INT( 11 ) NOT NULL ,
`position_id` TINYINT( 2 ) NOT NULL
) ENGINE = INNODB;

CREATE TABLE  `positions` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`title` VARCHAR( 100 ) NOT NULL
) ENGINE = MYISAM ;
UPDATE  `sysvals` SET  `sysval_value` =  '1|Social Worker
4|Counsellor
2|Clinician
3|MDT' WHERE  `sysvals`.`sysval_id` =178;

UPDATE  `sysvals` SET  `sysval_value` =  '1|a. Self
2|b. Caregiver
3|c. Institution
4|d. CHW
5|e. LTP' WHERE  `sysvals`.`sysval_id` =176;

UPDATE  `sysvals` SET  `sysval_value` =  '1|Not enrolled
2|No school uniform, books, fees
3|Sent away from school
4|School transport
' WHERE  `sysvals`.`sysval_id` =80;

UPDATE `sysvals` SET  `sysval_value` =  '1|No or low income 
2|Many dependants 
4|Chronically ill or bedridden caregiver 
5|Elderly Caregiver 
6|Child headed household' WHERE  `sysvals`.`sysval_id` =81;

ALTER TABLE `chw_info_staging` CHANGE `chw_adh_support` `chw_adh_support` VARCHAR( 50 ) NULL DEFAULT NULL;

ALTER TABLE `chw_info` CHANGE `chw_adh_support` `chw_adh_support` VARCHAR( 50 ) NULL DEFAULT NULL;

UPDATE  `sysvals` SET  `sysval_value` =  '1|Active
5|Retested Negative
10|Relocated
3|Transfer Out
6|Exits over 18yrs
4|Deceased
2|Lost to follow-up
7|Transfer to LTP Centre
8|Declined
9|VCT
11|Reactivated' WHERE  `sysvals`.`sysval_id` =15;

DROP TABLE IF EXISTS `discharge_info`;
CREATE TABLE IF NOT EXISTS `discharge_info` (
  `dis_id` int(11) NOT NULL AUTO_INCREMENT,
  `dis_client_id` int(11) NOT NULL,
  `dis_client_adm_no` varchar(50) NOT NULL,
  `dis_center` tinyint(3) NOT NULL,
  `dis_entry_date` date NOT NULL,
  `dis_time_in` varchar(10) DEFAULT NULL,
  `dis_age_years` INT( 3 ) NULL DEFAULT NULL,
  `dis_age_months` INT( 3 ) NULL DEFAULT NULL,
  `dis_age_exact` ENUM(  '1',  '2' ) NULL DEFAULT NULL,
  `dis_client_status` tinyint(2) DEFAULT NULL,
  `dis_status_delta_date` date DEFAULT NULL,
  `dis_status_mdt_date` date DEFAULT NULL,
  `dis_status_next_date` date DEFAULT NULL,
  `dis_phys_address` varchar(100) DEFAULT NULL,
  `dis_landmarks` varchar(200) DEFAULT NULL,
  `dis_contact` varchar(100) DEFAULT NULL,
  `dis_caregiver` int(11) DEFAULT NULL,
  `dis_caregiver_relship` varchar(100) DEFAULT NULL,
  `dis_client_health` text,
  `dis_client_health_staff` int(10) DEFAULT NULL,
  `dis_client_health_date` date DEFAULT NULL,
  `dis_client_psy` text,
  `dis_client_psy_staff` int(10) DEFAULT NULL,
  `dis_client_psy_date` date DEFAULT NULL,
  `dis_client_social` text,
  `dis_client_social_staff` int(10) DEFAULT NULL,
  `dis_client_social_date` date DEFAULT NULL,
  `dis_form_type` ENUM(  'dis',  'ltp' ) NULL DEFAULT NULL,
  PRIMARY KEY (`dis_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `discharge_staging`;
CREATE TABLE IF NOT EXISTS `discharge_info` (
  `dis_id` int(11) NOT NULL AUTO_INCREMENT,
  `dis_client_id` int(11) NOT NULL,
  `dis_client_adm_no` varchar(50) NOT NULL,
  `client_adm_no` varchar(50) DEFAULT NULL,
  `client_first_name` varchar(100) DEFAULT '',
  `client_other_name` varchar(100) DEFAULT '',
  `client_last_name` varchar(100) DEFAULT '',
  `dis_center` tinyint(3) NOT NULL,
  `clinic_name` varchar(100) DEFAULT '',
  `dis_entry_date` date NOT NULL,
  `dis_time_in` varchar(10) DEFAULT NULL,
  `dis_age_years` int(3) DEFAULT NULL,
  `dis_age_months` int(3) DEFAULT NULL,
  `dis_age_exact` enum('1','2') DEFAULT NULL,
  `dis_client_status` tinyint(2) DEFAULT NULL,
  `dis_status_delta_date` date DEFAULT NULL,
  `dis_status_mdt_date` date DEFAULT NULL,
  `dis_status_next_date` date DEFAULT NULL,
  `dis_phys_address` varchar(100) DEFAULT NULL,
  `dis_landmarks` varchar(200) DEFAULT NULL,
  `dis_contact` varchar(100) DEFAULT NULL,
  `dis_caregiver` int(11) DEFAULT NULL,
  `dis_caregiver_relship` varchar(100) DEFAULT NULL,
  `dis_client_health` text,
  `dis_client_health_staff` int(10) DEFAULT NULL,
  `dis_client_health_staff_name` varchar(200) DEFAULT NULL,
  `dis_client_health_date` date DEFAULT NULL,
  `dis_client_psy` text,
  `dis_client_psy_staff` int(10) DEFAULT NULL,
  `dis_client_psy_staff_name` varchar(200) DEFAULT NULL,
  `dis_client_psy_date` date DEFAULT NULL,
  `dis_client_social` text,
  `dis_client_social_staff` int(10) DEFAULT NULL,
  `dis_client_social_staff_name` varchar(200) DEFAULT NULL,
  `dis_client_social_date` date DEFAULT NULL,
  `dis_form_type` ENUM(  'dis',  'ltp' ) NULL DEFAULT NULL,
  PRIMARY KEY (`dis_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

DROP TABLE IF EXISTS `status_client_staging`;
CREATE TABLE IF NOT EXISTS  `status_client_staging` (
 `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
 `social_client_id` INT( 10 ) NOT NULL ,
 `client_adm_no` VARCHAR( 20 ) NOT NULL ,
 `social_client_status` INT( 10 ) DEFAULT NULL ,
 `social_entry_date` DATE DEFAULT NULL ,
 `mode` ENUM(  'status',  'center' ) NOT NULL ,
PRIMARY KEY (  `id` )
) ENGINE = MYISAM DEFAULT CHARSET latin1;

ALTER TABLE  `config` CHANGE  `config_value`  `config_value` VARCHAR( 2048 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT  '';

update clients set client_status="9" where client_status="0";
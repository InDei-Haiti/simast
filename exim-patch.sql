alter table `activities_staging` add `activity_id` int(11) unsigned DEFAULT NULL;
alter table `counselling_info` add `counselling_referral_source_old` varchar(100) DEFAULT NULL;
alter table `social_visit` add `social_any_needs` tinyint(1) DEFAULT NULL;
alter table `social_staging` add `social_any_needs` tinyint(1) DEFAULT NULL;
alter table `training_staging` add `training_curriculum` int(11) DEFAULT NULL,
    add `training_curriculum_desc` varchar(150) DEFAULT NULL,
    add `training_custom` longtext;
INSERT INTO `config` (`config_id`, `config_name`, `config_value`, `config_group`, `config_type`) VALUES (NULL, 'current_center', '', '', 'select');
alter table `clients_staging` add `clinic_name` varchar(30) null default null,
    add `client_center` int(4) null default null;
    
-- Update clients table, add value for client_center
-- update counselling_info, clients set client_center = counselling_clinic where counselling_client_id=client_id
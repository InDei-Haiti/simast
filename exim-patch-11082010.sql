alter table activities_staging add `activity_id` int(11) unsigned DEFAULT NULL;
alter table counselling_info add `counselling_referral_source_old` varchar(100) DEFAULT NULL;
alter table `social_visit` add `social_any_needs` tinyint(1) DEFAULT NULL;
alter table `social_staging` add `social_any_needs` tinyint(1) DEFAULT NULL;
alter table `training_staging` add `training_curriculum` int(11) DEFAULT NULL,
    add `training_curriculum_desc` varchar(150) DEFAULT NULL,
    add `training_custom` longtext;
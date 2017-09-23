INSERT INTO  `modules` (
`mod_id` ,
`mod_name` ,
`mod_directory` ,
`mod_version` ,
`mod_setup_class` ,
`mod_type` ,
`mod_active` ,
`mod_ui_name` ,
`mod_ui_icon` ,
`mod_ui_order` ,
`mod_ui_active` ,
`mod_description` ,
`permissions_item_table` ,
`permissions_item_field` ,
`permissions_item_label`
)
VALUES (
'34',  'Manage',  'manager',  '0.1',  '',  'core',  '1',  'Manage',  '',  '27',  '1',  'System maintance module', NULL , NULL , NULL
);

INSERT INTO  `gacl_axo` (
`id` ,
`section_value` ,
`value` ,
`order_value` ,
`name` ,
`hidden`
)
VALUES ('67',  'app',  'manager',  '50',  'Manage',  '0');

INSERT INTO  `gacl_aro_map` (
`acl_id` ,
`section_value` ,
`value`
)
VALUES ('60',  'user',  '1');

INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES ('60', 'app', 'manager');

INSERT INTO  `gacl_aco_map` (
`acl_id` ,
`section_value` ,
`value`
)
VALUES 
('60',  'application',  'access'),
('60',  'application',  'add'),
('60',  'application',  'delete'),
('60',  'application',  'edit'),
('60',  'application',  'view');

INSERT INTO  `gacl_aro_groups_map` (
`acl_id` ,
`group_id`
)
VALUES ('60',  '20');

INSERT INTO  `gacl_axo_groups_map` (
`acl_id` ,
`group_id`
)
VALUES ('60',  '17');

INSERT INTO  `gacl_acl` (
`id` ,
`section_value` ,
`allow` ,
`enabled` ,
`return_value` ,
`note` ,
`updated_date`
)
VALUES ('60',  'user',  '1',  '1', '' , '' ,  '1293381444 ');

INSERT INTO  `gacl_groups_axo_map` (
`group_id` ,
`axo_id`
)
VALUES ('17',  '67');

ALTER TABLE  `files` ADD  `file_mode` ENUM(  'export',  'import' ) NOT NULL DEFAULT  'import';

ALTER TABLE `ltp_transfers` ADD `status` ENUM('1','0') NOT NULL;

CREATE TABLE  `ltp_inbox` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`client_adm_no` VARCHAR( 20 ) NOT NULL ,
`client_name` VARCHAR( 200 ) NOT NULL ,
`client_arrived` DATE NOT NULL ,
`center_origin` VARCHAR( 30 ) NOT NULL ,
`ltp_status` ENUM(  'accepted',  'pending' ) NOT NULL DEFAULT  'pending',
`client_real_data` TEXT NOT NULL
) ENGINE = MYISAM ;
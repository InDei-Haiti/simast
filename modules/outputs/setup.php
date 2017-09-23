<?php
/*
 * Name:      Outputs
 * Directory: Outputs
 * Version:   0.1
 * Class:     user
 * UI Name:   Outputs
 * UI Icon:
 */

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'Outputs';
$config['mod_version'] = '0.1';
$config['mod_directory'] = 'outputs';
$config['mod_setup_class'] = 'CSetupOutputs';
$config['mod_type'] = 'core';
$config['mod_ui_name'] = 'Outputs';
$config['mod_ui_icon'] = '';
$config['mod_description'] = 'A module for parsing and analyzing existing data';

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetupOutputs {   

	function install() {
		$sql = " (".
			" `id` int(11) NOT NULL AUTO_INCREMENT,
			  `qname` varchar(255) NOT NULL,
			  `qdesc` text NOT NULL,
			  `sdate` int(8) NOT NULL DEFAULT '0',
			  `edate` int(8) NOT NULL DEFAULT '0',
			  `visits` enum('all','last') NOT NULL DEFAULT 'all',
			  `fils` text NOT NULL,
			  `posts` text NOT NULL,
			  `visible` enum('0','1') NOT NULL DEFAULT '1',
			  `actives` enum('0','1') NOT NULL DEFAULT '0',
			  `created` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM ";
		$q = new DBQuery;
		$q->createTable('queries');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();
		$sql="(".
			"  `id` int(12) NOT NULL AUTO_INCREMENT,
			  `qname` varchar(255) NOT NULL DEFAULT '',
			  `qdesc` text NOT NULL,
			  `rows` text NOT NULL,
			  `cols` text NOT NULL,
			  `ranges` text NOT NULL,
			  `turns` varchar(100) NOT NULL,
			  `sdate` int(8) NOT NULL,
			  `edate` int(8) NOT NULL,
			  `show_result` enum('0','1') NOT NULL DEFAULT '1',
			  `query_id` int(12) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM";
		$q->createTable('stat_queries');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();
		return db_error();
	}
	
	function remove() {
		$q = new DBQuery;
		$q->dropTable('queries');
		$q->exec();
		$q->clear();
		$q->dropTable('stat_queries');
		$q->exec();
		$q->clear();
		return db_error();
	}
	
	function upgrade($old_version) {
/*		$q = new DBQuery;
		switch ($old_version) {
			case '0.1':
				$q->alterTable('history');
				$q->addField('history_table', 'varchar(15) NOT NULL default \'\'');
				$q->addField('history_action', 'varchar(10) NOT NULL default \'modify\'');
				$q->dropField('history_module');
				$q->exec();
				$q->clear();
			case '0.2':
				$q->alterTable('history');
				$q->addField('history_item', 'int(10) NOT NULL');
				$q->exec();
				$q->clear();
			case '0.3':
				break;
		}
		return db_error();*/
		return;
	}
}

?>

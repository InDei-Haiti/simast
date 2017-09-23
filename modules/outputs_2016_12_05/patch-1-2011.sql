DROP TABLE IF EXISTS `reports`;
CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `entries` blob NOT NULL,
  `backdoor` blob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

ALTER TABLE  `stat_queries` 
    ADD  `chart_data` TEXT NOT NULL ,
    ADD  `qmode` ENUM(  'stat',  'graph' ) NOT NULL DEFAULT  'stat',
    CHANGE  `turns`  `turns` VARCHAR( 250 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

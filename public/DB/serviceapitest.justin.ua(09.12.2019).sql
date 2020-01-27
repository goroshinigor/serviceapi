/*
SQLyog Ultimate v12.09 (64 bit)
MySQL - 10.1.43-MariaDB-1~jessie : Database - serviceapi
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*Table structure for table `serviceapi_attika_filial_services` */

DROP TABLE IF EXISTS `serviceapi_attika_filial_services`;

CREATE TABLE `serviceapi_attika_filial_services` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `json` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Table structure for table `serviceapi_attika_filials` */

DROP TABLE IF EXISTS `serviceapi_attika_filials`;

CREATE TABLE `serviceapi_attika_filials` (
  `int` int(11) NOT NULL AUTO_INCREMENT,
  `filial_number` varchar(11) NOT NULL,
  `json_basic` text NOT NULL,
  `json_services` text NOT NULL,
  `json_public` text NOT NULL,
  `json_photos` text NOT NULL,
  `update_datetime` datetime NOT NULL,
  PRIMARY KEY (`int`)
) ENGINE=InnoDB AUTO_INCREMENT=6402630 DEFAULT CHARSET=utf8;

/*Table structure for table `serviceapi_branches` */

DROP TABLE IF EXISTS `serviceapi_branches`;

CREATE TABLE `serviceapi_branches` (
  `number` int(11) NOT NULL,
  `adress` varchar(255) DEFAULT NULL,
  `locality` varchar(100) DEFAULT NULL,
  `type` varchar(200) DEFAULT NULL,
  `format` varchar(200) DEFAULT NULL,
  `delivery_branch_id` varchar(100) DEFAULT NULL,
  `max_weight` int(5) DEFAULT NULL,
  `lat` varchar(255) DEFAULT NULL,
  `lng` varchar(255) DEFAULT NULL,
  `description` text,
  `shedule_description` text,
  `photos` text,
  `services` text,
  `public` text,
  `updatetime` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `serviceapi_buffering_locator` */

DROP TABLE IF EXISTS `serviceapi_buffering_locator`;

CREATE TABLE `serviceapi_buffering_locator` (
  `forming_number` int(11) NOT NULL,
  `lat` varchar(255) DEFAULT NULL,
  `lng` varchar(255) DEFAULT NULL,
  `buffering_time` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`forming_number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `serviceapi_ew_all_info_buffering` */

DROP TABLE IF EXISTS `serviceapi_ew_all_info_buffering`;

CREATE TABLE `serviceapi_ew_all_info_buffering` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_uuid` varchar(70) NOT NULL,
  `client_number` varchar(70) NOT NULL,
  `json_basic` text NOT NULL,
  `updatetime` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;

/*Table structure for table `serviceapi_ew_info_basic_buffering` */

DROP TABLE IF EXISTS `serviceapi_ew_info_basic_buffering`;

CREATE TABLE `serviceapi_ew_info_basic_buffering` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(50) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `command` int(1) NOT NULL,
  `updatetime` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=728 DEFAULT CHARSET=utf8;

/*Table structure for table `serviceapi_ew_info_detail_buffering` */

DROP TABLE IF EXISTS `serviceapi_ew_info_detail_buffering`;

CREATE TABLE `serviceapi_ew_info_detail_buffering` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `basic_id` int(11) NOT NULL,
  `is_archive` int(1) NOT NULL COMMENT '1 - archive, 0 - not',
  `is_incoming` int(1) NOT NULL COMMENT '1 - incoming, 0 - outgoing',
  `sender_uuid_1c` varchar(255) NOT NULL,
  `sender_phone` varchar(255) NOT NULL,
  `sender_full_name` text NOT NULL,
  `sender_first_name` varchar(255) NOT NULL,
  `sender_second_name` varchar(255) NOT NULL,
  `sender_last_name` varchar(255) NOT NULL,
  `sender_company` text NOT NULL,
  `receiver_uuid_1c` varchar(255) NOT NULL,
  `receiver_phone` varchar(255) NOT NULL,
  `receiver_full_name` text NOT NULL,
  `receiver_first_name` varchar(255) NOT NULL,
  `receiver_second_name` varchar(255) NOT NULL,
  `receiver_last_name` varchar(255) NOT NULL,
  `receiver_company` text NOT NULL,
  `sender_department_uuid_1c` varchar(255) NOT NULL,
  `sender_department_number` varchar(255) NOT NULL,
  `sender_department_address` text NOT NULL,
  `sender_department_city` varchar(255) NOT NULL,
  `receiver_department_uuid_1c` varchar(255) NOT NULL,
  `receiver_department_number` varchar(255) NOT NULL,
  `receiver_department_address` text NOT NULL,
  `receiver_department_city` varchar(255) NOT NULL,
  `ew_number` text NOT NULL,
  `client_number` text NOT NULL,
  `ttn` text NOT NULL,
  `order_date` datetime NOT NULL,
  `description` text NOT NULL,
  `delivery_type` varchar(10) NOT NULL,
  `status_id` varchar(255) DEFAULT NULL,
  `status_description` text,
  `status_date` datetime NOT NULL,
  `weight` varchar(100) NOT NULL,
  `max_size` varchar(50) NOT NULL,
  `type_size` varchar(100) NOT NULL,
  `count_cargo_places` int(11) NOT NULL,
  `delivery_payment` varchar(50) NOT NULL,
  `delivery_payment_received` varchar(50) NOT NULL,
  `delivery_payment_payer` int(1) NOT NULL,
  `delivery_payment_status` int(1) NOT NULL,
  `declared_cost` varchar(50) NOT NULL,
  `cod_payment` varchar(50) NOT NULL,
  `cod_payment_received` varchar(50) NOT NULL,
  `cod_summ` varchar(100) NOT NULL,
  `cod_commission_external` varchar(100) NOT NULL,
  `cod_commission_external_payer` int(1) NOT NULL,
  `cod_is_available` int(1) NOT NULL,
  `cod_delivery_type` int(1) NOT NULL,
  `cod_card_number` varchar(100) NOT NULL,
  `cod_payment_status` int(1) NOT NULL,
  `cod_commission_payment_status` int(1) NOT NULL,
  `cost_pay_sender` varchar(100) NOT NULL,
  `cost_pay_receiver` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7821 DEFAULT CHARSET=utf8;

/*Table structure for table `serviceapi_ew_size` */

DROP TABLE IF EXISTS `serviceapi_ew_size`;

CREATE TABLE `serviceapi_ew_size` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `weight_a` decimal(11,2) NOT NULL,
  `weight_b` decimal(11,2) NOT NULL,
  `length_a` int(11) NOT NULL,
  `length_b` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Table structure for table `serviceapi_handbook_statuses` */

DROP TABLE IF EXISTS `serviceapi_handbook_statuses`;

CREATE TABLE `serviceapi_handbook_statuses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(200) DEFAULT NULL,
  `code` varchar(200) DEFAULT NULL,
  `description` text,
  `is_archive` int(1) NOT NULL COMMENT '1 - archive, 0 - not',
  `updatetime` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

/*Table structure for table `serviceapi_log_requests_to_api` */

DROP TABLE IF EXISTS `serviceapi_log_requests_to_api`;

CREATE TABLE `serviceapi_log_requests_to_api` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `session_uuid` varchar(255) NOT NULL,
  `request_url` text NOT NULL,
  `request_data` text NOT NULL,
  `request_headers` text NOT NULL,
  `request_datetime` datetime NOT NULL,
  `request_remote_ip` text NOT NULL,
  `status` int(1) NOT NULL,
  `msg_code` int(5) NOT NULL,
  `result_data` text NOT NULL,
  `result_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `session_uuid` (`session_uuid`),
  KEY `login` (`login`),
  KEY `msg_code` (`msg_code`)
) ENGINE=InnoDB AUTO_INCREMENT=136673 DEFAULT CHARSET=utf8;

/*Table structure for table `serviceapi_pms_branches` */

DROP TABLE IF EXISTS `serviceapi_pms_branches`;

CREATE TABLE `serviceapi_pms_branches` (
  `int` int(11) NOT NULL AUTO_INCREMENT,
  `filial_number` varchar(11) NOT NULL,
  `filial_uuid` varchar(255) NOT NULL,
  `json_basic` text NOT NULL,
  `update_datetime` datetime NOT NULL,
  PRIMARY KEY (`int`)
) ENGINE=InnoDB AUTO_INCREMENT=1501488 DEFAULT CHARSET=utf8;

/*Table structure for table `serviceapi_price` */

DROP TABLE IF EXISTS `serviceapi_price`;

CREATE TABLE `serviceapi_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `memberId` varchar(9) NOT NULL,
  `datetime_start` datetime NOT NULL,
  `datetime_end` datetime NOT NULL,
  `is_active` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `serviceapi_price_cod` */

DROP TABLE IF EXISTS `serviceapi_price_cod`;

CREATE TABLE `serviceapi_price_cod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `point_a` float(8,2) NOT NULL,
  `point_b` float(8,2) NOT NULL,
  `cod_proc` float(5,2) NOT NULL,
  `cod_fix` float(7,2) NOT NULL,
  `cod_min` float(7,2) NOT NULL,
  `cod_max` float(7,2) NOT NULL,
  `cod_over` float(7,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `serviceapi_price_insurance` */

DROP TABLE IF EXISTS `serviceapi_price_insurance`;

CREATE TABLE `serviceapi_price_insurance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `point_a` float(8,2) NOT NULL,
  `point_b` float(8,2) NOT NULL,
  `insurance_proc` float(5,2) NOT NULL,
  `insurance_fix` float(7,2) NOT NULL,
  `insurance_min` float(7,2) NOT NULL,
  `insurance_max` float(7,2) NOT NULL,
  `insurance_over` float(7,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `serviceapi_price_size` */

DROP TABLE IF EXISTS `serviceapi_price_size`;

CREATE TABLE `serviceapi_price_size` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `size` varchar(10) NOT NULL,
  `price_town` decimal(11,2) NOT NULL,
  `price_country` decimal(11,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Table structure for table `serviceapi_users` */

DROP TABLE IF EXISTS `serviceapi_users`;

CREATE TABLE `serviceapi_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `login` text NOT NULL,
  `key` text NOT NULL,
  `access_end_date` date NOT NULL,
  `is_test_mode` int(1) NOT NULL,
  `is_disable` int(1) NOT NULL,
  `is_deleted` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;


insert  into `serviceapi_ew_size`(`id`,`name`,`weight_a`,`weight_b`,`length_a`,`length_b`) values (1,'XS','0.01','0.50',1,40),(2,'S','0.51','1.00',1,40),(3,'M','1.01','2.00',1,40),(4,'L','2.01','5.00',41,60),(5,'XL','5.01','10.00',41,60),(6,'XXL','10.01','15.00',61,90),(7,'XXXL','15.01','30.00',61,90);
insert  into `serviceapi_price`(`id`,`memberId`,`datetime_start`,`datetime_end`,`is_active`) values (1,'0','2019-01-01 00:00:00','2020-01-01 00:00:00',0);
insert  into `serviceapi_price_cod`(`id`,`point_a`,`point_b`,`cod_proc`,`cod_fix`,`cod_min`,`cod_max`,`cod_over`) values (1,0.00,1000000.00,2.00,0.00,0.00,0.00,15.00);
insert  into `serviceapi_price_insurance`(`id`,`point_a`,`point_b`,`insurance_proc`,`insurance_fix`,`insurance_min`,`insurance_max`,`insurance_over`) values (1,0.00,200.00,0.00,0.00,0.00,0.00,0.00),(2,200.01,1000000.00,0.50,0.00,0.00,0.00,0.00);
insert  into `serviceapi_price_size`(`id`,`parent_id`,`size`,`price_town`,`price_country`) values (1,1,'XS','25.00','33.00'),(2,1,'S','30.00','37.00'),(3,1,'M','35.00','42.00'),(4,1,'L','38.00','47.00'),(5,1,'XL','49.00','57.00'),(6,1,'XXL','65.00','75.00'),(7,1,'XXXL','90.00','95.00');
insert  into `serviceapi_users`(`id`,`name`,`login`,`key`,`access_end_date`,`is_test_mode`,`is_disable`,`is_deleted`) values (14,'TEST','test','7ac6afc5d17f8e5fe37a568288202574d7dc5ed2f973e7f7f4132a3419e9d766b5b8ea2d73d259680a30ab877edfcc045980d6f8c890456a8bdbc23cbebb344110af2ae9fd389fa47a84f2c5e014e43ac79b4ec89d89f74ce49e945574c355a664d12f8b5b6946ed44dc2b0a76416377a2e6f98a2379b210504c730255707624adf78633f427c207fc49e872ff7f652bdc0c28870cfc9126c0a8902c6a269edbed429276cbc98099459fb3b34825f5388802b37f650f611592c49ab49281f44e8c0100472602a8510447f595110cbeec59e8f1d3107d0bffcfb16d231b0164b37ad07002c440ac5510e0df621f1dfbe130199235806f13caf4e142492dff1719','2020-01-01',1,0,0),(15,'ЛК','po_personal','8ac6afc5d17f8e5fe37a568288202574d7dc5ed2f973e7f7f4132a3419e9d766b5b8ea2d73d259680a30ab877edfcc045980d6f8c890456a8bdbc23cbebb344110af2ae9fd389fa47a84f2c5e014e43ac79b4ec89d89f74ce49e945574c355a664d12f8b5b6946ed44dc2b0a76416377a2e6f98a2379b210504c730255707624adf78633f427c207fc49e872ff7f652bdc0c28870cfc9126c0a8902c6a269edbed429276cbc98099459fb3b34825f5388802b37f650f611592c49ab49281f44e8c0100472602a8510447f595110cbeec59e8f1d3107d0bffcfb16d231b0164b37ad07002c440ac5510e0df621f1dfbe130199235806f13caf4e142492dff1719','2020-01-01',1,0,0),(16,'МП С2С','app_personal','9ac6afc5d17f8e5fe37a568288202574d7dc5ed2f973e7f7f4132a3419e9d766b5b8ea2d73d259680a30ab877edfcc045980d6f8c890456a8bdbc23cbebb344110af2ae9fd389fa47a84f2c5e014e43ac79b4ec89d89f74ce49e945574c355a664d12f8b5b6946ed44dc2b0a76416377a2e6f98a2379b210504c730255707624adf78633f427c207fc49e872ff7f652bdc0c28870cfc9126c0a8902c6a269edbed429276cbc98099459fb3b34825f5388802b37f650f611592c49ab49281f44e8c0100472602a8510447f595110cbeec59e8f1d3107d0bffcfb16d231b0164b37ad07002c440ac5510e0df621f1dfbe130199235806f13caf4e142492dff1719','2020-01-01',1,0,0),(17,'Chatbot','chatbot','9bc3afc5d17f8e5fe37a568288202574d7dc5ed2f973e7f7f4132a3419e9d766b5b8ea2d73d259680a30ab877edfcc045980d6f8c890456a8bdbc23cbebb344110af2ae9fd389fa47a84f2c5e014e43ac79b4ec89d89f74ce49e945574c355a664d12f8b5b6946ed44dc2b0a76416377a2e6f98a2379b210504c730255707624adf78633f427c207fc49e872ff7f652bdc0c28870cfc9126c0a8902c6a269edbed429276cbc98099459fb3b34825f5388802b37f650f611592c49ab49281f44e8c0100472602a8510447f595110cbeec59e8f1d3107d0bffcfb16d231b0164b37ad07002c440ac5510e0df621f1dfbe130199235806f13caf4e142492dff1719','2020-01-01',1,0,0);
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

CREATE TABLE `serviceapi_attika_platforms_statuses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `json` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

CREATE TABLE `serviceapi_handbook_statuses_platforms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(200) NOT NULL,
  `code` varchar(200) NOT NULL,
  `platforms_json` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=127 DEFAULT CHARSET=utf8;

CREATE TABLE `serviceapi_buffering_tracking` (
  `number_ttn` varchar(255) COLLATE utf8_bin NOT NULL,
  `answer_serialize` text COLLATE utf8_bin,
  `ew_info` text COLLATE utf8_bin,
  `updatetime` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`number_ttn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

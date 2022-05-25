<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2016 VINADES.,JSC. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Sun, 08 May 2016 09:31:28 GMT
 */
if (! defined('NV_IS_FILE_MODULES'))
    die('Stop!!!');

global $op, $db;

$sql_drop_module = array();

$result = $db->query("SHOW TABLE STATUS LIKE '" . $db_config['prefix'] . "\_" . $module_data . "\_money\_%'");
$num_table = intval($result->rowCount());
$array_lang_module_setup = array();
$set_lang_data = '';

if ($num_table > 0) {
    while ($item = $result->fetch()) {
        $array_lang_module_setup[] = str_replace($db_config['prefix'] . "_" . $module_data . "_money_", "", $item['name']);
    }
    
    if ($lang != $global_config['site_lang'] and in_array($global_config['site_lang'], $array_lang_module_setup)) {
        $set_lang_data = $global_config['site_lang'];
    } else {
        foreach ($array_lang_module_setup as $lang_i) {
            if ($lang != $lang_i) {
                $set_lang_data = $lang_i;
                break;
            }
        }
    }
}

$result = $db->query("SHOW TABLE STATUS LIKE '" . $db_config['prefix'] . "\_" . $lang . "\_comment'");
$rows = $result->fetchAll();
if (sizeof($rows)) {
    $sql_drop_module[] = "DELETE FROM " . $db_config['prefix'] . "_" . $lang . "_comment WHERE module='" . $module_name . "'";
}

if (in_array($lang, $array_lang_module_setup) and $num_table > 1) {
    $sql_drop_module[] = 'ALTER TABLE ' . $db_config['prefix'] . '_' . $module_data . '_cat
	 DROP ' . $lang . '_title,
	 DROP ' . $lang . '_alias,
	 DROP ' . $lang . '_custom_title,
	 DROP ' . $lang . '_keywords,
	 DROP ' . $lang . '_description,
	 DROP ' . $lang . '_description_html';
    
    $sql_drop_module[] = 'ALTER TABLE ' . $db_config['prefix'] . '_' . $module_data . '_services
	 DROP ' . $lang . '_title,
     DROP ' . $lang . '_note';
    
    $sql_drop_module[] = 'ALTER TABLE ' . $db_config['prefix'] . '_' . $module_data . '_vehicle
	 DROP ' . $lang . '_title,
     DROP ' . $lang . '_note';
    
    $sql_drop_module[] = 'ALTER TABLE ' . $db_config['prefix'] . '_' . $module_data . '_rows
	 DROP ' . $lang . '_title,
	 DROP ' . $lang . '_alias,
     DROP ' . $lang . '_title_custom,
     DROP ' . $lang . '_plan,
     DROP ' . $lang . '_description,
     DROP ' . $lang . '_description_html,
     DROP ' . $lang . '_note';
    
    $sql_drop_module[] = 'ALTER TABLE ' . $db_config['prefix'] . '_' . $module_data . '_hotels
	 DROP ' . $lang . '_title,
     DROP ' . $lang . '_description,
	 DROP ' . $lang . '_address';
    
    $sql_drop_module[] = 'ALTER TABLE ' . $db_config['prefix'] . '_' . $module_data . '_subprice
	 DROP ' . $lang . '_title,
     DROP ' . $lang . '_note';
    
    $sql_drop_module[] = 'ALTER TABLE ' . $db_config['prefix'] . '_' . $module_data . '_images
	 DROP ' . $lang . '_title,
     DROP ' . $lang . '_description';
    
    $sql_drop_module[] = 'ALTER TABLE ' . $db_config['prefix'] . '_' . $module_data . '_block_cat
	 DROP ' . $lang . '_title,
     DROP ' . $lang . '_alias,
     DROP ' . $lang . '_description,
     DROP ' . $lang . '_keywords';
    
    $sql_drop_module[] = 'ALTER TABLE ' . $db_config['prefix'] . '_' . $module_data . '_payment_method
	 DROP ' . $lang . '_description';
} elseif ($op != 'setup') {
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_cat";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_services";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_vehicle";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_discounts";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_payport";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_guides";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_rows";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_hotels";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_flying";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_subprice";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_images";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_block";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_block_cat";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_payment_method";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_booking";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_booking_customer";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_coupons";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_transaction";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_payment";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_config";
}

$sql_drop_module[] = 'DROP TABLE IF EXISTS ' . $db_config['prefix'] . '_' . $module_data . '_money_' . $lang;
$sql_drop_module[] = 'DROP TABLE IF EXISTS ' . $db_config['prefix'] . '_' . $module_data . '_tags_' . $lang;
$sql_drop_module[] = 'DROP TABLE IF EXISTS ' . $db_config['prefix'] . '_' . $module_data . '_tags_id_' . $lang;

$sql_create_module = $sql_drop_module;

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_cat(
  id smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  parentid smallint(4) unsigned NOT NULL DEFAULT '0',
  inhome tinyint(1) unsigned NOT NULL DEFAULT '1',
  numlinks smallint(3) unsigned NOT NULL DEFAULT '6',
  gettype varchar(50) NOT NULL DEFAULT 'getcat',
  viewtype varchar(50) NOT NULL DEFAULT 'viewgrid',
  groups_view varchar(255) NOT NULL DEFAULT '6',
  image varchar(255) NOT NULL DEFAULT '',
  price_method tinyint(1) unsigned NOT NULL DEFAULT '1',
  price_method_auto tinyint(1) unsigned NOT NULL DEFAULT '0',
  price_method_config text NOT NULL,
  subprice text NOT NULL DEFAULT '',  
  lev smallint(4) unsigned NOT NULL DEFAULT '0',
  numsub smallint(4) unsigned NOT NULL DEFAULT '0',
  subid varchar(255) NOT NULL,
  sort smallint(4) unsigned NOT NULL DEFAULT '0',
  weight smallint(4) unsigned NOT NULL DEFAULT '0',
  status tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Trạng thái',
  PRIMARY KEY (id)
) ENGINE=MyISAM";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_cat ADD " . $lang . "_title VARCHAR( 250 ) NOT NULL DEFAULT ''";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_cat ADD " . $lang . "_alias VARCHAR( 250 ) NOT NULL DEFAULT ''";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_cat ADD " . $lang . "_custom_title VARCHAR( 255 ) NOT NULL DEFAULT ''";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_cat ADD " . $lang . "_keywords text NOT NULL";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_cat ADD " . $lang . "_description tinytext NOT NULL";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_cat ADD " . $lang . "_description_html TEXT NOT NULL";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_money_" . $lang . " (
 id mediumint(11) NOT NULL,
 code char(3) NOT NULL,
 currency varchar(250) NOT NULL,
 exchange float NOT NULL default '0',
 round varchar(10) NOT NULL,
 number_format varchar(5) NOT NULL DEFAULT ',||.',
 PRIMARY KEY (id),
 UNIQUE KEY code (code)
) ENGINE=MyISAM";
$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_money_" . $lang . " (id, code, currency, exchange, round, number_format) VALUES (840, 'USD', 'US Dollar', 21000, '0.01', ',||.')";
$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_money_" . $lang . " (id, code, currency, exchange, round, number_format) VALUES (704, 'VND', 'Vietnam Dong', 1, '100', ',||.')";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_services(
  id smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  weight smallint(4) unsigned NOT NULL DEFAULT '0',
  status tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Trạng thái',
  PRIMARY KEY (id)
) ENGINE=MyISAM";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_services ADD " . $lang . "_title varchar(255) NOT NULL";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_services ADD " . $lang . "_note text NOT NULL";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_vehicle(
  id smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  icon varchar(255) NOT NULL, 
  weight smallint(4) unsigned NOT NULL DEFAULT '0',
  status tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Trạng thái',
  PRIMARY KEY (id)
) ENGINE=MyISAM";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_vehicle ADD " . $lang . "_title varchar(255) NOT NULL";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_vehicle ADD " . $lang . "_note text NOT NULL";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_discounts (
  did smallint(6) NOT NULL AUTO_INCREMENT,
  title varchar(100) NOT NULL DEFAULT '',
  percent mediumint(8) unsigned NOT NULL DEFAULT '0',
  begin_time int(11) unsigned NOT NULL DEFAULT '0',
  end_time int(11) unsigned NOT NULL DEFAULT '0',
  status tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (did),
  KEY begin_time (begin_time,end_time)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_payment (
 payment varchar(100) NOT NULL,
 paymentname varchar(250) NOT NULL,
 domain varchar(250) NOT NULL,
 active tinyint(4) NOT NULL default '0',
 weight int(11) NOT NULL default '0',
 config text NOT NULL,
 images_button varchar(250) NOT NULL,
 PRIMARY KEY (payment)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_guides(
  id smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  first_name varchar(100) NOT NULL,
  last_name varchar(255) NOT NULL,
  description text NOT NULL,
  birthday int(11) unsigned NOT NULL,
  address varchar(255) NOT NULL,
  gender tinyint(1) unsigned NOT NULL,
  phone varchar(25) NOT NULL,
  image varchar(255) NOT NULL,
  status tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_rows(
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  catid smallint(4) NOT NULL,
  code varchar(50) NOT NULL,
  price float unsigned NOT NULL,
  price_config text NOT NULL,
  money_unit varchar(3) NOT NULL,
  discounts_id smallint(6) unsigned NOT NULL DEFAULT '0',
  num_day smallint(4) unsigned NOT NULL COMMENT 'Số ngày',
  date_start_method tinyint(1) unsigned DEFAULT '1',
  date_start_config tinytext NOT NULL,
  date_start int(11) unsigned NOT NULL COMMENT 'Ngày khởi hành',
  place_start mediumint(4) unsigned NOT NULL COMMENT 'Nơi khởi hành',
  place_end mediumint(4) unsigned NOT NULL COMMENT 'Nơi đến',
  homeimgfile varchar(255) NOT NULL,
  homeimgalt varchar(255) NOT NULL,
  homeimgthumb tinyint(41) NOT NULL DEFAULT '0',
  allowed_rating tinyint(1) NOT NULL DEFAULT '0',
  show_price tinyint(1) NOT NULL DEFAULT '1',
  services text NOT NULL,
  vehicle smallint(4) unsigned NOT NULL DEFAULT '0',
  flying_begin text NOT NULL COMMENT 'Thông tin chuyến bay đi',
  flying_end text NOT NULL COMMENT 'Thông tin chuyến bay về',
  hotels_info text NOT NULL COMMENT 'Thông tin khách sạn',
  hotels_star tinyint(1) unsigned NOT NULL COMMENT 'Xếp hạng khách sạn cao nhất',
  guides text NOT NULL COMMENT 'Hướng dẫn viên',
  admin_id int(11) unsigned NOT NULL,
  hitstotal mediumint(8) NOT NULL DEFAULT '0',
  hitscm mediumint(8) NOT NULL DEFAULT '0',
  num_seat mediumint(8) unsigned NOT NULL COMMENT 'Số chổ tối đa',
  rest mediumint(8) unsigned NOT NULL COMMENT 'Số chổ còn lại',
  addtime int(11) unsigned NOT NULL,
  edittime int(11) NOT NULL DEFAULT '0',
  groups_view varchar(255) NOT NULL DEFAULT '6',
  groups_comment varchar(255) NOT NULL DEFAULT '6',
  status tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
) ENGINE=MyISAM";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_rows ADD " . $lang . "_title varchar(250) NOT NULL";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_rows ADD " . $lang . "_alias varchar(250) NULL";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_rows ADD " . $lang . "_title_custom varchar(250) NOT NULL";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_rows ADD " . $lang . "_plan text NOT NULL";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_rows ADD " . $lang . "_description text NOT NULL";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_rows ADD " . $lang . "_description_html text NOT NULL";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_rows ADD " . $lang . "_note text NOT NULL";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_hotels(
  id smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  phone varchar(25) NOT NULL,
  website varchar(100) NOT NULL,
  image varchar(255) NOT NULL,
  star int(1) unsigned NOT NULL DEFAULT '0',
  status tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Trạng thái',
  PRIMARY KEY (id)
) ENGINE=MyISAM";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_hotels ADD " . $lang . "_title varchar(255) NOT NULL";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_hotels ADD " . $lang . "_description text NOT NULL";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_hotels ADD " . $lang . "_address varchar(255) NOT NULL";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_flying(
  id smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(255) NOT NULL,
  image varchar(255) NOT NULL,
  note tinytext NOT NULL COMMENT 'Ghi chú',
  weight smallint(4) unsigned NOT NULL DEFAULT '0',
  status tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Trạng thái',
  PRIMARY KEY (id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_subprice(
  id smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  is_optional tinyint(1) unsigned NOT NULL DEFAULT '0',
  weight smallint(4) unsigned NOT NULL DEFAULT '0',
  status tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Trạng thái',
  PRIMARY KEY (id)
) ENGINE=MyISAM";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_subprice ADD " . $lang . "_title varchar(255) NOT NULL";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_subprice ADD " . $lang . "_note text NOT NULL";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_images(
  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  rows_id int(11) unsigned NOT NULL,
  homeimgfile varchar(255) NOT NULL,
  weight smallint(4) unsigned NOT NULL DEFAULT '0',
  status tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
) ENGINE=MyISAM";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_images ADD " . $lang . "_title varchar(255) NOT NULL";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_images ADD " . $lang . "_description text NOT NULL";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_block(
  bid smallint(5) unsigned NOT NULL,
  id int(11) unsigned NOT NULL,
  weight int(11) unsigned NOT NULL,
  UNIQUE KEY bid (bid,id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_block_cat(
  bid smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  adddefault tinyint(4) NOT NULL DEFAULT '0',
  numbers smallint(5) NOT NULL DEFAULT '10',
  image varchar(255) DEFAULT '',
  weight smallint(5) NOT NULL DEFAULT '0',
  add_time int(11) NOT NULL DEFAULT '0',
  edit_time int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (bid)
) ENGINE=MyISAM";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_block_cat ADD " . $lang . "_title varchar(250) NULL";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_block_cat ADD " . $lang . "_alias varchar(250) NULL";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_block_cat ADD " . $lang . "_description text NOT NULL";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_block_cat ADD " . $lang . "_keywords varchar(255) NOT NULL";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_payment_method(
  id smallint(4) unsigned NOT NULL,
  weight smallint(4) unsigned NOT NULL,
  status tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Trạng thái',
  PRIMARY KEY (id)
) ENGINE=MyISAM";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_payment_method ADD " . $lang . "_description text NOT NULL";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_booking (
 booking_id int(11) unsigned NOT NULL auto_increment,
 booking_code varchar(30) NOT NULL default '',
 tour_id int(11) unsigned NOT NULL,
 lang char(2) NOT NULL default 'en',
 contact_fullname varchar(250) NOT NULL,
 contact_address varchar(250) NOT NULL,
 contact_phone varchar(20) NOT NULL,
 contact_email varchar(250) NOT NULL,
 contact_note text NOT NULL,
 user_id int(11) unsigned NOT NULL default '0',
 ip varchar(100) NOT NULL default '',
 customerprice tinytext NOT NULL,
 unit_total char(3) NOT NULL,
 booking_total double unsigned NOT NULL default '0',
 booking_time int(11) unsigned NOT NULL default '0',
 booking_viewed tinyint(2) NOT NULL DEFAULT '0',
 discounts_id smallint(6) NOT NULL DEFAULT '0',
 coupons_id mediumint(8) NOT NULL DEFAULT '0',
 coupons_value float DEFAULT '0',
 payment_method tinyint(1) NOT NULL DEFAULT '0',
 transaction_status tinyint(4) NOT NULL,
 transaction_id int(11) NOT NULL default '0',
 transaction_count int(11) NOT NULL,
 checksum varchar(32) NOT NULL,
 PRIMARY KEY (booking_id),
 UNIQUE KEY booking_code (booking_code),
 KEY user_id (user_id),
 KEY booking_time (booking_time)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_booking_customer (
 id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
 booking_id int(11) NOT NULL,
 fullname varchar(255) NOT NULL,
 birthday int(11) unsigned NOT NULL,
 address varchar(255) NOT NULL,
 gender TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
 age tinyint(1) NOT NULL DEFAULT '0',
 customer_type tinyint(1) NOT NULL DEFAULT '0',
 optional varchar(255) NOT NULL,
 price double NOT NULL,
 PRIMARY KEY (id),
 UNIQUE KEY booking_id (booking_id, id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_tags_" . $lang . " (
  tid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  numpro mediumint(8) NOT NULL DEFAULT '0',
  alias varchar(250) NOT NULL DEFAULT '',
  image varchar(250) DEFAULT '',
  description text,
  keywords varchar(250) DEFAULT '',
  PRIMARY KEY (tid),
  UNIQUE KEY alias (alias)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_tags_id_" . $lang . " (
  id int(11) NOT NULL,
  tid mediumint(9) NOT NULL,
  keyword varchar(65) NOT NULL,
  UNIQUE KEY sid (id,tid),
  KEY tid (tid)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_coupons (
  id mediumint(8) NOT NULL AUTO_INCREMENT,
  title varchar(100) NOT NULL DEFAULT '',
  code varchar(50) NOT NULL DEFAULT '',
  type varchar(1) NOT NULL DEFAULT 'p',
  discount float NOT NULL DEFAULT '0',
  date_start int(11) unsigned NOT NULL DEFAULT '0',
  date_end int(11) unsigned NOT NULL DEFAULT '0',
  tourid TEXT NOT NULL COMMENT 'Tour được áp dụng',
  quantity int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Số lượng mã',
  quantity_used int(11) NOT NULL DEFAULT '0' COMMENT 'Số lượt sử dụng',
  date_added int(11) unsigned NOT NULL DEFAULT '0',
  status tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_transaction (
 transaction_id int(11) NOT NULL AUTO_INCREMENT,
 transaction_time int(11) NOT NULL DEFAULT '0',
 transaction_status int(11) NOT NULL,
 booking_id int(11) NOT NULL DEFAULT '0',
 userid int(11) NOT NULL DEFAULT '0',
 payment varchar(100) NOT NULL DEFAULT '0',
 payment_id varchar(22) NOT NULL DEFAULT '0',
 payment_time int(11) NOT NULL DEFAULT '0',
 payment_amount float NOT NULL DEFAULT '0',
 payment_data text NOT NULL,
 PRIMARY KEY (transaction_id),
 KEY booking_id (booking_id),
 KEY payment_id (payment_id)
) ENGINE=MyISAM";

// Cau hinh mac dinh
$data = array();
$data['allow_auto_code'] = 1;
$data['format_code'] = 'T%06s';
$data['format_booking_code'] = '%09s';
$data['money_unit'] = 'VND';
$data['structure_upload'] = 'Ym';
$data['home_type'] = '0';
$data['title_lenght'] = 50;
$data['booking_type'] = 2;
$data['booking_groups'] = 6;
$data['rule_content'] = '';
$data['booking_sendmail'] = 1;
$data['booking_groups_sendmail'] = 3;
$data['default_place_start'] = 1; // ha noi
$data['tags_alias'] = 0;
$data['auto_tags'] = 1;
$data['tags_remind'] = 0;
$data['coupons'] = 1;
$data['date_start_method'] = 1;
$data['contact_info'] = '';
$data['note_content'] = '';
$data['age_config'] = 'a:3:{i:0;a:4:{s:4:"name";s:14:"Người lớn";s:4:"from";s:1:"9";s:2:"to";s:3:"100";s:10:"price_base";i:1;}i:1;a:4:{s:4:"name";s:8:"Trẻ em";s:4:"from";s:1:"6";s:2:"to";s:1:"8";s:10:"price_base";i:0;}i:2;a:4:{s:4:"name";s:6:"Em bé";s:4:"from";s:1:"0";s:2:"to";s:1:"5";s:10:"price_base";i:0;}}';
$data['home_image_size'] = '250x150';
$data['booking_price_method'] = 0;
$data['no_image'] = '';
$data['per_page'] = 21;

foreach ($data as $config_name => $config_value) {
    $sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', " . $db->quote($module_name) . ", " . $db->quote($config_name) . ", " . $db->quote($config_value) . ")";
}

// Copy du lieu khi cai dat ngon ngu moi
if (! empty($set_lang_data)) {
    $numrow = $db->query("SELECT count(*) FROM " . $db_config['prefix'] . "_" . $module_data . "_cat")->fetchColumn();
    if ($numrow) {
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_cat SET " . $lang . "_title = " . $global_config['site_lang'] . "_title";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_cat SET " . $lang . "_alias = " . $set_lang_data . "_alias";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_cat SET " . $lang . "_custom_title = " . $global_config['site_lang'] . "_custom_title";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_cat SET " . $lang . "_keywords = " . $set_lang_data . "_keywords";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_cat SET " . $lang . "_description = " . $set_lang_data . "_description";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_cat SET " . $lang . "_description_html = " . $set_lang_data . "_description_html";
    }
    
    $numrow = $db->query("SELECT count(*) FROM " . $db_config['prefix'] . "_" . $module_data . "_services")->fetchColumn();
    if ($numrow) {
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_services SET " . $lang . "_title = " . $set_lang_data . "_title";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_services SET " . $lang . "_note = " . $set_lang_data . "_note";
    }
    
    $numrow = $db->query("SELECT count(*) FROM " . $db_config['prefix'] . "_" . $module_data . "_vehicle")->fetchColumn();
    if ($numrow) {
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_vehicle SET " . $lang . "_title = " . $set_lang_data . "_title";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_vehicle SET " . $lang . "_note = " . $set_lang_data . "_note";
    }
    
    $numrow = $db->query("SELECT count(*) FROM " . $db_config['prefix'] . "_" . $module_data . "_rows")->fetchColumn();
    if ($numrow) {
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_rows SET " . $lang . "_title = " . $set_lang_data . "_title";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_rows SET " . $lang . "_alias = " . $set_lang_data . "_alias";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_rows SET " . $lang . "_title_custom = " . $set_lang_data . "_title_custom";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_rows SET " . $lang . "_plan = " . $set_lang_data . "_plan";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_rows SET " . $lang . "_description = " . $set_lang_data . "_description";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_rows SET " . $lang . "_description_html = " . $set_lang_data . "_description_html";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_rows SET " . $lang . "_note = " . $set_lang_data . "_note";
    }
    
    $numrow = $db->query("SELECT count(*) FROM " . $db_config['prefix'] . "_" . $module_data . "_hotels")->fetchColumn();
    if ($numrow) {
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_hotels SET " . $lang . "_title = " . $set_lang_data . "_title";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_hotels SET " . $lang . "_address = " . $set_lang_data . "_address";
    }
    
    $numrow = $db->query("SELECT count(*) FROM " . $db_config['prefix'] . "_" . $module_data . "_images")->fetchColumn();
    if ($numrow) {
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_images SET " . $lang . "_title = " . $set_lang_data . "_title";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_images SET " . $lang . "_description = " . $set_lang_data . "_description";
    }
    
    $numrow = $db->query("SELECT count(*) FROM " . $db_config['prefix'] . "_" . $module_data . "_block_cat")->fetchColumn();
    if ($numrow) {
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_block_cat SET " . $lang . "_title = " . $set_lang_data . "_title";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_block_cat SET " . $lang . "_alias = " . $set_lang_data . "_alias";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_block_cat SET " . $lang . "_description = " . $set_lang_data . "_description";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_block_cat SET " . $lang . "_keywords = " . $set_lang_data . "_keywords";
    }
    
    $numrow = $db->query("SELECT count(*) FROM " . $db_config['prefix'] . "_" . $module_data . "_payment_method")->fetchColumn();
    if ($numrow) {
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_payment_method SET " . $lang . "_description = " . $set_lang_data . "_description";
    }
    
    $numrow = $db->query("SELECT count(*) FROM " . $db_config['prefix'] . "_" . $module_data . "_subprice")->fetchColumn();
    if ($numrow) {
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_subprice SET " . $lang . "_title = " . $set_lang_data . "_title";
        $sql_create_module[] = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_subprice SET " . $lang . "_note = " . $set_lang_data . "_note";
    }
}

// comment config
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . "(lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'auto_postcomm', '1')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . "(lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'allowed_comm', '-1')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . "(lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'view_comm', '6')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . "(lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'setcomm', '4')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . "(lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'activecomm', '1')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . "(lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'emailcomm', '0')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . "(lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'adminscomm', '')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . "(lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'sortcomm', '0')";
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . "(lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'captcha', '1')";

// unique
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_cat ADD UNIQUE(" . $lang . "_alias)";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_rows ADD UNIQUE(" . $lang . "_alias)";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_block_cat ADD UNIQUE(" . $lang . "_title)";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_block_cat ADD UNIQUE(" . $lang . "_alias)";
<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-10-2010 20:59
 */
if (! defined('NV_ADMIN'))
    die('Stop!!!');

/**
 * Note:
 * - Module var is: $lang, $module_file, $module_data, $module_upload, $module_theme, $module_name
 * - Accept global var: $db, $db_config, $global_config
 */

global $op;

$result = $db->query("SHOW TABLE STATUS LIKE '" . $db_config['prefix'] . "\_" . $module_data . "\_money\_%'");
$num_table = intval($result->rowCount());

// Truncate data
if ($num_table == 1 and $op != 'setup') {
    $db->query("TRUNCATE " . $db_config['prefix'] . "_" . $module_data . "_money_" . $lang);
    $db->query("TRUNCATE " . $db_config['prefix'] . "_" . $module_data . "_cat");
}

if ($num_table == 1) {
    // chủ đề
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_cat (id, parentid, inhome, numlinks, viewtype, lev, numsub, subid, sort, weight, status, vi_title, vi_alias, vi_custom_title, vi_keywords, vi_description, vi_description_html) VALUES('2', '0', '1', '6', 'viewgrid', '0', '4', '4,5,6,7', '1', '1', '1', 'Du lịch trong nước', 'Du-lich-trong-nuoc', '', '', '', '')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_cat (id, parentid, inhome, numlinks, viewtype, lev, numsub, subid, sort, weight, status, vi_title, vi_alias, vi_custom_title, vi_keywords, vi_description, vi_description_html) VALUES('3', '0', '1', '6', 'viewgrid', '0', '4', '8,9,10,11', '6', '2', '1', 'Du lịch nước ngoài', 'Du-lich-nuoc-ngoai', '', '', '', '')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_cat (id, parentid, inhome, numlinks, viewtype, lev, numsub, subid, sort, weight, status, vi_title, vi_alias, vi_custom_title, vi_keywords, vi_description, vi_description_html) VALUES('4', '2', '1', '6', 'viewgrid', '1', '0', '', '2', '1', '1', 'Du lịch Hà Nội', 'Du-lich-Ha-Noi', '', '', '', '')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_cat (id, parentid, inhome, numlinks, viewtype, lev, numsub, subid, sort, weight, status, vi_title, vi_alias, vi_custom_title, vi_keywords, vi_description, vi_description_html) VALUES('5', '2', '1', '6', 'viewgrid', '1', '0', '', '3', '2', '1', 'Du lịch Hạ Long', 'Du-lich-Ha-Long', '', '', '', '')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_cat (id, parentid, inhome, numlinks, viewtype, lev, numsub, subid, sort, weight, status, vi_title, vi_alias, vi_custom_title, vi_keywords, vi_description, vi_description_html) VALUES('6', '2', '1', '6', 'viewgrid', '1', '0', '', '4', '3', '1', 'Du lịch Lào Cai', 'Du-lich-Lao-Cai', '', '', '', '')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_cat (id, parentid, inhome, numlinks, viewtype, lev, numsub, subid, sort, weight, status, vi_title, vi_alias, vi_custom_title, vi_keywords, vi_description, vi_description_html) VALUES('7', '2', '1', '6', 'viewgrid', '1', '0', '', '5', '4', '1', 'Du lịch Hải Phòng', 'Du-lich-Hai-Phong', '', '', '', '')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_cat (id, parentid, inhome, numlinks, viewtype, lev, numsub, subid, sort, weight, status, vi_title, vi_alias, vi_custom_title, vi_keywords, vi_description, vi_description_html) VALUES('8', '3', '1', '6', 'viewgrid', '1', '0', '', '7', '1', '1', 'Du lịch Madagascar', 'Du-lich-Madagascar', '', '', '', '')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_cat (id, parentid, inhome, numlinks, viewtype, lev, numsub, subid, sort, weight, status, vi_title, vi_alias, vi_custom_title, vi_keywords, vi_description, vi_description_html) VALUES('9', '3', '1', '6', 'viewgrid', '1', '0', '', '8', '2', '1', 'Du lịch Mexico', 'Du-lich-Mexico', '', '', '', '')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_cat (id, parentid, inhome, numlinks, viewtype, lev, numsub, subid, sort, weight, status, vi_title, vi_alias, vi_custom_title, vi_keywords, vi_description, vi_description_html) VALUES('10', '3', '1', '6', 'viewgrid', '1', '0', '', '9', '3', '1', 'Du lịch Hawaii', 'Du-lich-Hawaii', '', '', '', '')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_cat (id, parentid, inhome, numlinks, viewtype, lev, numsub, subid, sort, weight, status, vi_title, vi_alias, vi_custom_title, vi_keywords, vi_description, vi_description_html) VALUES('11', '3', '1', '6', 'viewgrid', '1', '0', '', '10', '4', '1', 'Du lịch Hoa Kỳ', 'Du-lich-Hoa-Ky', '', '', '', '')");
    
    // dịch vụ
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_services (id, weight, status, vi_title, vi_note) VALUES('2', '1', '1', 'Bảo hiểm', '')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_services (id, weight, status, vi_title, vi_note) VALUES('3', '2', '1', 'Bữa ăn theo chương trình', '')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_services (id, weight, status, vi_title, vi_note) VALUES('4', '3', '1', 'Hướng dẫn viên', '')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_services (id, weight, status, vi_title, vi_note) VALUES('5', '4', '1', 'Vé tham quan', '')");
    
    // giam gia
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_discounts (did, title, percent, begin_time, end_time, status) VALUES('2', 'Giảm giá 20% lễ 30/4/2016', '20', '1462726800', '0', '1')");
    
    // huong dan vien
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_guides (id, first_name, last_name, description, birthday, address, gender, phone, image, status) VALUES('1', 'Triển', 'Hồ Ngọc', '', '713811600', 'Triệu Phong, Quảng Trị', '1', '01692777913', 'selection_508.png', '1')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_guides (id, first_name, last_name, description, birthday, address, gender, phone, image, status) VALUES('2', 'Bình', 'Trần Văn', '', '831402000', '', '1', '01222567345', '', '1')");
    
    // khach san
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_hotels (id, phone, website, image, status, vi_title, vi_description, vi_address) VALUES('1', '08.7714.2342', '', '', '1', 'Tajmasago Resort', '<p>Tọa lạc tại trung tâm đô thị mới Phú Mỹ Hưng, lâu đài TajmaSago là khu nghĩ dưỡng cao cấp dành cho giới doanh nhân, các du khách trong và ngoài nước khi đến du lịch hoặc làm việc tại TPHCM. Lâu đài tạo cho du khách cảm giác như lạc vào cung điện cổ, như đến với Taj Mahal Ấn Độ. Đến với TajmaSago, du khách sẽ có cơ hội tận hưởng những dịch vụ đẳng cấp nhất chỉ có tại tòa lâu đài xa hoa này.<br  /><br  />&nbsp;Bên cạnh những tiện ích trong phòng như tivi 3D thông minh, bồn tắm, iPad, mỹ phẩm Spa Pure, áo choàng lụa, wifi…, TajmaSago cung cấp các dịch vụ giải trí cao cấp như rạp chiếu phim 3D, thư viện, spa và phòng gym với trang thiết bị hiện đại. Lâu đài còn đặc biệt ấn tượng du khách ở hồ bơi nước biển có thể nghe nhạc dưới nước. Các dịch vụ đưa đón khách bằng xe siêu sang như Roll Royce, Audi, BMW tạo sự thuận lợi nhất cho khách hàng. Hãy khám phá sự hòa hợp giữa cung cách phục vụ chuyên nghiệp và vô số tiện nghi tối tân ở lâu đài Tajmasago.</p>', '06 Phan văn Chương, Quận 7, Hồ Chí Minh (Sài Gòn)')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_hotels (id, phone, website, image, status, vi_title, vi_description, vi_address) VALUES('2', '08.7714.2343', '', '', '1', 'Rex Sài Gòn', '<p>Khi bạn dừng chân tại thành phố nhộn nhịp này bạn sẽ không khỏi ngạc nhiên bởi vẻ đẹp lung linh huyền ảo của&nbsp;Khách sạn Rexnguy nga và lộng lẫy dưới những ánh đèn. Dường như Thành Phố Sài Gòn về đêm càng tô thêm vẻ đẹp của Khách sạn bởi nhiều hoạt động giải trí, vui chơi giúp du khách thuận tiện hơn khi nghỉ dưỡng tại đây.&nbsp;<br  /><br  />Không những thế khi du khách nghỉ ngơi tại&nbsp;Khách Sạn Rex&nbsp;du khách có thể tham quan những di tích lịch sử như: toà nhà Ủy ban Nhân dân, nhà hát Thành phố, chợ Bến Thành, dinh Thống Nhất, nhà thờ Đức Bà… Bạn hãy đến với Khách Sạn Rex để có những kì nghỉ khám phá những công trình kiến trúc vĩ đại của nhân loại.<br  /><br  />Khách sạn Rex&nbsp;gồm 5 tầng với 230 phòng, trong đó có 53 phòng Suite và 14 phòng dạng căn hộ. Kiến trúc của khách sạn cuốn hút bởi sự cân đối, hài hòa giữa hiện đại và truyền thống. Nội thất của các phòng trong khách sạn được trang trí bằng mây tạo nên một nét rất riêng của&nbsp;khách sạn Rex. Tất cả các phòng đều được trang bị các thiết bị hiện đại, tiện nghi.</p>', '141 Nguyễn Huệ, Quận 1, Hồ Chí Minh (Sài Gòn)')");
    
    // hang hang khong
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_flying (id, title, image, note, weight, status) VALUES('2', 'Vietnam Airlines (Hãng hàng không quốc gia Việt Nam)', '', '', '1', '1')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_flying (id, title, image, note, weight, status) VALUES('3', 'Vietjet Air', '', '', '2', '1')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_flying (id, title, image, note, weight, status) VALUES('4', 'Jetstar', '', '', '3', '1')");
    
    // hang muc phu thu
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_subprice (id, weight, status, vi_title, vi_note) VALUES('4', '1', '1', 'Phụ thu xăng dầu và Bảo hiểm hàng không', '')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_subprice (id, weight, status, vi_title, vi_note) VALUES('5', '2', '1', 'Phụ thu lệ phí sân bay', '')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_subprice (id, weight, status, vi_title, vi_note) VALUES('6', '3', '1', 'Phụ thu visa (nếu có)', '')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_subprice (id, weight, status, vi_title, vi_note) VALUES('7', '4', '1', 'Phụ thu Visa tái nhập Việt Nam', '')");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_subprice (id, weight, status, vi_title, vi_note) VALUES('8', '5', '1', 'Phụ thu phòng đơn', '')");
    
    // hinh thuc thanh toan
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_payment_method (id, weight) VALUES ('0', '1');");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_payment_method (id, weight) VALUES ('1', '2');");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_payment_method (id, weight) VALUES ('2', '3');");
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_payment_method (id, weight) VALUES ('3', '4');");
}
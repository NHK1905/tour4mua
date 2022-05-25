<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Ho Ngoc Trien (hongoctrien@2mit.org)
 * @Copyright (C) 2015 Ho Ngoc Trien. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sun, 16 Aug 2015 01:05:44 GMT
 */
if (! defined('NV_MAINFILE'))
    die('Stop!!!');

if(!isset($site_mods['location']) or !file_exists(NV_ROOTDIR . '/modules/location/data.functions.php')){
    $contents = nv_theme_alert($lang_module['error_location_title'], $lang_module['error_location_content'], 'danger');
    nv_info_die($lang_module['error_location_title'], $lang_module['error_location_title'], $lang_module['error_location_content']);
}

$array_config = $module_config[$module_name];

$_sql = 'SELECT id, parentid, ' . NV_LANG_DATA . '_title title, ' . NV_LANG_DATA . '_alias alias, ' . NV_LANG_DATA . '_custom_title custom_title, ' . NV_LANG_DATA . '_keywords keywords, ' . NV_LANG_DATA . '_description description, ' . NV_LANG_DATA . '_description_html description_html, inhome, numlinks, gettype, viewtype, price_method, price_method_auto, price_method_config, subprice, lev, numsub, subid, sort, weight, status, image, groups_view FROM ' . $db_config['prefix'] . '_' . $module_data . '_cat WHERE status=1 ORDER BY sort ASC';
$array_cat = $nv_Cache->db($_sql, 'id', $module_name);

$_sql = 'SELECT id, ' . NV_LANG_DATA . '_title title FROM ' . $db_config['prefix'] . '_' . $module_data . '_vehicle WHERE status=1 ORDER BY weight';
$array_vehicle = $nv_Cache->db($_sql, 'id', $module_name);

$_sql = 'SELECT id, ' . NV_LANG_DATA . '_title title, star FROM ' . $db_config['prefix'] . '_' . $module_data . '_hotels WHERE status=1 ORDER BY id DESC';
$array_hotels = $nv_Cache->db($_sql, 'id', $module_name);

// Ty gia ngoai te
$sql = 'SELECT code, currency, exchange, round, number_format FROM ' . $db_config['prefix'] . '_' . $module_data . '_money_' . NV_LANG_DATA . ' ORDER BY id';
$cache_file = NV_LANG_DATA . '_' . md5($sql) . '_' . NV_CACHE_PREFIX . '.cache';
if (($cache = $nv_Cache->getItem($module_name, $cache_file)) != false) {
    $money_config = unserialize($cache);
} else {
    $money_config = array();
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $money_config[$row['code']] = array(
            'code' => $row['code'],
            'currency' => $row['currency'],
            'exchange' => $row['exchange'],
            'round' => $row['round'],
            'number_format' => $row['number_format'],
            'decimals' => $row['round'] > 1 ? $row['round'] : strlen($row['round']) - 2,
            'is_config' => ($row['code'] == $array_config['money_unit']) ? 1 : 0
        );
    }
    $result->closeCursor();
    $cache = serialize($money_config);
    $nv_Cache->setItem($module_name, $cache_file, $cache);
}

// giam gia
$sql = 'SELECT did, title, begin_time, end_time, percent FROM ' . $db_config['prefix'] . '_' . $module_data . '_discounts';
$cache_file = NV_LANG_DATA . '_' . md5($sql) . '_' . NV_CACHE_PREFIX . '.cache';
if (($cache = $nv_Cache->getItem($module_name, $cache_file)) != false) {
    $discounts_config = unserialize($cache);
} else {
    $discounts_config = array();
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $discounts_config[$row['did']] = array(
            'title' => $row['title'],
            'percent' => $row['percent'],
            'begin_time' => $row['begin_time'],
            'end_time' => $row['end_time']
        );
    }
    $result->closeCursor();
    $cache = serialize($discounts_config);
    $nv_Cache->setItem($module_name, $cache_file, $cache);
}

$array_customer_type = array(
    '0' => $lang_module['customer_type_0'],
    '1' => $lang_module['customer_type_1'],
    '2' => $lang_module['customer_type_2']
);

$array_gender = array(
    '1' => $lang_module['gender_1'],
    '0' => $lang_module['gender_0'],
    '2' => $lang_module['gender_2']
);

$array_payment_method = array(
    '0' => $lang_module['payment_method_0'],
    '1' => $lang_module['payment_method_1'],
    '2' => $lang_module['payment_method_2'],
    '3' => $lang_module['payment_method_3']
);

$array_payment_status = array(
    '0' => $lang_module['payment_status_0'],
    '1' => $lang_module['payment_status_1']
);

function nv_tour_delete($id)
{
    global $db, $db_config, $module_data;

    // xoa tour
    $_sql = 'DELETE FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows  WHERE id = ' . $id;
    if($db->exec($_sql)){
        // Xoa booking
        $result = $db->query('SELECT booking_id FROM ' . $db_config['prefix'] . '_' . $module_data . '_booking WHERE tour_id=' . $id);
        while(list($booking_id) = $result->fetch(3)){
            nv_booking_delete($booking_id);
        }
        
        // Xoa anh khac
        nv_image_delete($id);
    }
}

/**
 * nv_images_delete()
 *
 * @param mixed $rows_id
 * @return
 *
 */
function nv_image_delete($rows_id)
{
    global $db, $db_config, $module_data, $module_upload;

    // xoa hinh anh khac
    $result = $db->query('SELECT id, homeimgfile FROM ' . $db_config['prefix'] . '_' . $module_data . '_images WHERE rows_id=' . $rows_id);
    while(list($id, $homeimgfile) = $result->fetch(3)){
        $_sql = 'DELETE FROM ' . $db_config['prefix'] . '_' . $module_data . '_images WHERE id = ' . $id;
        if($db->exec($_sql)){
            @nv_deletefile(NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $homeimgfile);
        }   
    }
}

/**
 * nv_booking_delete()
 *
 * @param mixed $booking_id
 * @return
 *
 */
function nv_booking_delete($booking_id)
{
    global $db, $db_config, $module_data;

    $booking_info = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_booking WHERE booking_id=' . $booking_id)->fetch();
    if (!empty($booking_info)) {
        // xoa booking
        $db->query('DELETE FROM ' . $db_config['prefix'] . '_' . $module_data . '_booking WHERE booking_id=' . $booking_id);

        // xoa danh sach khach hang
        $db->query('DELETE FROM ' . $db_config['prefix'] . '_' . $module_data . '_booking_customer WHERE booking_id=' . $booking_id);

        // cap nhat so luot su dung coupons
        if($booking_info['coupons_id'] > 0){
            nv_update_coupons_quantity($booking_info['coupons_id'], '-');
        }
        
        // Xoa lich su giao dich
        $db->query('DELETE FROM ' . $db_config['prefix'] . '_' . $module_data . '_transaction WHERE transaction_id=' . $booking_info['transaction_id']);
    }
}

/**
 * nv_number_format()
 *
 * @param mixed $number            
 * @param integer $decimals            
 * @return
 *
 */
function nv_number_format($number, $decimals = 0)
{
    global $money_config, $array_config;
    
    $number_format = explode('||', $money_config[$array_config['money_unit']]['number_format']);
    $str = number_format($number, $decimals, $number_format[0], $number_format[1]);
    return $str;
}

/**
 * nv_get_decimals()
 *
 * @param mixed $currency_convert            
 * @return
 *
 */
function nv_get_decimals($currency_convert)
{
    global $money_config;

    $r = $money_config[$currency_convert]['round'];
    $decimals = 0;
    if ($r <= 1) {
        $decimals = $money_config[$currency_convert]['decimals'];
    }
    return $decimals;
}

/**
 * nv_get_price()
 *
 * @param mixed $pro_id            
 * @param mixed $currency_convert            
 * @param mixed $price            
 * @param mixed $module            
 * @return
 *
 */
function nv_get_price($pro_id, $currency_convert, $price = 0, $module = '')
{
    global $db, $db_config, $site_mods, $module_data, $array_cat, $array_config, $money_config, $discounts_config;
    
    $return = array();
    $discount_percent = 0;
    $discount = 0;
    $module_data = ! empty($module) ? $site_mods[$module]['module_data'] : $module_data;
    $product = $db->query('SELECT catid, price, money_unit, price_config, discounts_id FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows WHERE id = ' . $pro_id)->fetch();
    $price = $price > 0 ? $price : $product['price'];
    
    $r = $money_config[$product['money_unit']]['round'];
    $decimals = nv_get_decimals($currency_convert);
    if ($r > 1) {
        $price = round($price / $r) * $r;
    } else {
        $price = round($price, $decimals);
    }
    
    if (isset($discounts_config[$product['discounts_id']])) {
        $_config = $discounts_config[$product['discounts_id']];
        if ($_config['begin_time'] < NV_CURRENTTIME and ($_config['end_time'] > NV_CURRENTTIME or empty($_config['end_time']))) {
            $discount_percent = $_config['percent'];
            $discount = ($price * ($discount_percent / 100));
        }
    }
    
    $price = nv_currency_conversion($price, $product['money_unit'], $currency_convert);
    $return['price'] = $price; // Giá sản phẩm chưa format
    $return['price_format'] = nv_number_format($price, $decimals); // Giá sản phẩm đã format
    $return['discount'] = $discount; // Số tiền giảm giá sản phẩm chưa format
    $return['discount_format'] = nv_number_format($discount, $decimals); // Số tiền giảm giá sản phẩm đã format
    $return['discount_percent'] = $discount_percent;
    $return['sale'] = $price - $discount; // Giá bán thực tế của sản phẩm
    $return['sale_format'] = nv_number_format($return['sale'], $decimals); // Giá bán thực tế của sản phẩm đã format
    $return['unit'] = $currency_convert;
    return $return;
}

/**
 * nv_currency_conversion()
 *
 * @param mixed $price            
 * @param mixed $currency_curent            
 * @param mixed $currency_convert            
 * @return
 *
 */
function nv_currency_conversion($price, $currency_curent, $currency_convert)
{
    global $array_config, $money_config, $discounts_config;
    
    if ($currency_curent == $array_config['money_unit']) {
        $price = $price / $money_config[$currency_convert]['exchange'];
    } elseif ($currency_convert == $array_config['money_unit']) {
        $price = $price * $money_config[$currency_curent]['exchange'];
    }
    
    return $price;
}

/**
 * nv_currency_conversion()
 *
 * @param mixed $price
 * @param mixed $counpons_code
 * @return
 *
 */
function nv_counpons_discount($price_total, $coupons_code)
{
    global $db, $db_config, $module_data;

    $price_discount = 0;
    if($price_total > 0 and !empty($coupons_code)){
        $coupons_info = $db->query('SELECT type, discount FROM ' . $db_config['prefix'] . '_' . $module_data . '_coupons WHERE code=' . $db->quote($coupons_code) . ' AND status=1')->fetch();
        if($coupons_info['type'] == 'p'){
            $price_discount = ($price_total * $coupons_info['discount']) / 100;
        }elseif($coupons_info['type'] == 'f'){
            $price_discount = $price_total - $coupons_info['discount'];
        }
    }

    return $price_discount;
}

/**
 * nv_update_rest()
 *
 * @param mixed $tour_id
 * @param mixed $numcustomer
 * @param string $type
 * @return
 *
 */
function nv_update_rest($tour_id, $numcustomer, $type = '-')
{
    global $db_config, $db, $module_data;

    $db->query('UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_rows SET rest=rest ' . $type . ' ' . intval($numcustomer) . ' WHERE id=' . $tour_id);
}

/**
 * nv_update_coupons_quantity()
 *
 * @param mixed $tour_id
 * @param mixed $numcustomer
 * @param string $type
 * @return
 *
 */
function nv_update_coupons_quantity($coupons_id, $type = '+')
{
    global $db_config, $db, $module_data;

    $db->query('UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_coupons SET quantity_used=quantity_used ' . $type . ' 1 WHERE id=' . $coupons_id);
}
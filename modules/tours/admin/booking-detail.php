<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2016 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 09 May 2016 04:02:53 GMT
 */
if (! defined('NV_IS_FILE_ADMIN'))
    die('Stop!!!');

$booking_id = $nv_Request->get_int('booking_id', 'post,get', 0);
$table_name = $db_config['prefix'] . '_' . $module_data . '_booking';

$booking_info = $db->query('SELECT * FROM ' . $table_name . ' WHERE booking_id=' . $booking_id)->fetch();
if (empty($booking_info)) {
    Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=booking');
    die();
}
$booking_info['booking_time'] = nv_date('H:i d/m/Y', $booking_info['booking_time']);
$booking_info['payment_method'] = $array_payment_method[$booking_info['payment_method']];
$booking_info['customerprice'] = !empty($booking_info['customerprice']) ? unserialize($booking_info['customerprice']) : array();
$booking_info['customer'] = array();
$result = $db->query('SELECT * FROM ' . $table_name . '_customer WHERE booking_id=' . $booking_info['booking_id']);
while ($_row = $result->fetch()) {
    $booking_info['customer'][] = $_row;
}

$tour_info = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows WHERE status=1 AND id=' . $booking_info['tour_id'])->fetch();
$tour_info['title'] = $tour_info[NV_LANG_DATA . '_title'];
$tour_info['alias'] = $tour_info[NV_LANG_DATA . '_alias'];
$tour_info['date_start'] = nv_date('d/m/Y', $tour_info['date_start']);
$tour_info['province_start'] = nv_location_get_province_info($tour_info['place_start']);
$tour_info['province_end'] = nv_location_get_province_info($tour_info['place_end']);
$tour_info['link'] = nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_cat[$tour_info['catid']]['alias'] . '/' . $tour_info['alias'] . $global_config['rewrite_exturl'], true);
$tour_info['price'] = nv_get_price($tour_info['id'], $array_config['money_unit'], $tour_info['price']);

$cat_info = $array_cat[$tour_info['catid']];
$tour_info['price_method'] = $cat_info['price_method'];
$tour_info['subprice'] = array();
$cat_info['subprice'] = unserialize($cat_info['subprice']);
$cat_info['subprice'][] = 0;
$result = $db->query('SELECT id, ' . NV_LANG_DATA . '_title title, is_optional, ' . NV_LANG_DATA . '_note note FROM ' . $db_config['prefix'] . '_' . $module_data . '_subprice WHERE status=1 AND id IN (' . implode(',', $cat_info['subprice']) . ') ORDER BY weight');
while ($_row = $result->fetch()) {
    $tour_info['subprice'][$_row['id']] = $_row;
}

if ($nv_Request->isset_request('change_payment_status', 'post')) {
    $status = $nv_Request->get_int('status', 'post,get', 0);
    if ($status) {
        $transaction_status = 0;
        $operator = '-';
    } else {
        $transaction_status = 1;
        $operator = '+';
    }
    $result = $db->query('UPDATE ' . $table_name . ' SET transaction_status=' . $transaction_status . ', transaction_count=transaction_count' . $operator . '1 WHERE booking_id=' . $booking_id);
    if ($result) {
        if($transaction_status){
            // gui mail thong bao xac nhan thanh toan
            $booking_info['payment_time'] = nv_date('H:i d/m/Y', NV_CURRENTTIME);
            $booking_info['url_payment'] = NV_MY_DOMAIN .  nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['payment'] . '/' . $booking_info['booking_code'] . '-' . $booking_info['checksum'], true);
            
            $lang_module['sendmail_payment_welcome'] = sprintf($lang_module['sendmail_payment_welcome'], $booking_info['contact_fullname']);
            $lang_module['sendmail_payment_thank'] = sprintf($lang_module['sendmail_payment_thank'], $global_config['site_name'], $booking_info['booking_code']);
            
            $xtpl = new XTemplate('sendmail_payment_status.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
            $xtpl->assign('LANG', $lang_module);
            $xtpl->assign('TOUR_INFO', $tour_info);
            $xtpl->assign('BOOKING_INFO', $booking_info);
            $xtpl->assign('MONEY', $money_config[$array_config['money_unit']]);

            if(!empty($booking_info['customer'])){
                $number = 1;
                $price_total = 0;
                $age_config = ! empty($array_config['age_config']) ? unserialize($array_config['age_config']) : array();
                
                foreach($booking_info['customer'] as $customer){
                    $price_total += $customer['price'];
                    $customer['number'] = $number++;
                    $customer['birthday'] = nv_date('d/m/Y', $customer['birthday']);
                    $customer['gender'] = $lang_module['gender_' . $customer['gender']];
                    $customer['age'] = $age_config[$customer['age']]['name'];
                    $customer['customer_type'] = $customer['customer_type'] >= 0 ? $lang_module['customer_type_' . $customer['customer_type']] : '';
                    $customer['price'] = nv_number_format($customer['price']);
                    $xtpl->assign('CUSTOMER', $customer);
            
                    $price_method = $tour_info['price_method'];
                    $subprice = $tour_info['subprice'];
            
                    if ($price_method == 1) {
                        $xtpl->parse('main.age_thead');
                        $xtpl->parse('main.customer.age_tbody');
                    } elseif ($price_method == 2) {
                        $xtpl->parse('main.age_thead');
                        $xtpl->parse('main.customer.age_tbody');
            
                        $xtpl->parse('main.customer_type_thead');
                        $xtpl->parse('main.customer.customer_type_tbody');
                    }
            
                    $optional_thead = array();
                    if (! empty($subprice)) {
                        $array_optional = explode(',', $customer['optional']);
                        if(!empty($array_optional)){
                            foreach($array_optional as $optional){
                                list( $subprice_id, $subprice_value) = explode('_', $optional);
                                $xtpl->assign('OPTIONAL', $subprice_value ? $lang_module['yes'] : $lang_module['no']);
                                $xtpl->parse('main.customer.optional_tbody');
                                $optional_thead[$subprice_id] = $subprice[$subprice_id]['title'];
                            }
                        }
                    }
                    $xtpl->parse('main.customer');
                }
            
                if (! empty($optional_thead)) {
                    foreach($optional_thead as $head){
                        $xtpl->assign('TITLE', $head);
                        $xtpl->parse('main.optional_thead');
                    }
                }
            
                $xtpl->assign('TOTAL', nv_number_format($price_total));
            }
            
            $xtpl->parse('main');
            $email_content = $xtpl->text('main');
            
            nv_sendmail(array(
                $global_config['site_name'],
                $global_config['site_email']
            ), $booking_info['contact_email'], sprintf($lang_module['sendmail_payment_title'], $booking_info['booking_code']), $email_content);
        }
        die('OK');
    }
    die('NO');
}

$db->query('UPDATE ' . $table_name . ' SET booking_viewed = 1 WHERE booking_id=' . $booking_id);

$lang_module['payment_status_str'] = $lang_module['payment_status_' . $booking_info['transaction_status']];
$lang_module['booking_payment_success'] = $booking_info['transaction_status'] == 0 ? $lang_module['booking_payment_success'] : $lang_module['booking_payment_drop'];

$is_coupons = 0;
$booking_info['coupons_code'] = '';
if($array_config['coupons'] and $booking_info['coupons_id'] > 0){
    $is_coupons = 1;
    $booking_info['coupons_code'] = $db->query('SELECT code FROM ' . $db_config['prefix'] . '_' . $module_data . '_coupons WHERE id=' . $booking_info['coupons_id'])->fetchColumn();
    $lang_module['booking_coupons_note'] = sprintf($lang_module['booking_coupons_note'], $booking_info['coupons_code'], nv_number_format($booking_info['coupons_value']), $booking_info['unit_total']);
}

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('TOUR_INFO', $tour_info);
$xtpl->assign('BOOKING_INFO', $booking_info);
$xtpl->assign('MONEY', $money_config[$booking_info['unit_total']]);
$xtpl->assign('URL_DELETE', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=booking&amp;delete_id=' . $booking_id . '&amp;delete_checkss=' . md5($booking_id . NV_CACHE_PREFIX . $client_info['session_id']));
$xtpl->assign('SELFURL', $client_info['selfurl']);
$total_money = $booking_info['booking_total'] - $booking_info['coupons_value'];
$xtpl->assign('MONEY_TOTAL', nv_number_format($total_money));

if (! empty($booking_info['contact_note'])) {
    $xtpl->parse('main.contact_note');
}

$price_method = $tour_info['price_method'];
$decimals = nv_get_decimals($array_config['money_unit']);
$price_total = 0;
$age_config = ! empty($array_config['age_config']) ? unserialize($array_config['age_config']) : array();
if($array_config['booking_price_method'] == 1){
    $number = 1;
    $colspan = 5;

    if(!empty($booking_info['customerprice'])){
        foreach ($booking_info['customerprice'] as $_customerprice) {
            $_customerprice['number'] = $number;
            $price_total += $_customerprice['price'];
            $_customerprice['priceunit'] = nv_number_format($_customerprice['priceunit'], $decimals);
            $_customerprice['price'] = nv_number_format($_customerprice['price'], $decimals);

            $xtpl->assign('CUSTOMERPRICE', $_customerprice);
            if($price_method != 0){
                $colspan++;
                $xtpl->assign('AGE', $age_config[$_customerprice['age']]['name']);
                $xtpl->parse('main.tour_price_caculate.loop.age_tbody');
                $xtpl->parse('main.tour_price_caculate.age_thead');
            }

            if($price_method == 1){
                //
            }elseif($price_method == 2){
                $colspan++;
                $xtpl->assign('CUS_TYPE', $array_customer_type[$_customerprice['type']]);
                $xtpl->parse('main.tour_price_caculate.customer_type_thead');
                $xtpl->parse('main.tour_price_caculate.loop.customer_type_tbody');
            }

            $xtpl->parse('main.tour_price_caculate.loop');
            $number++;
        }
        $xtpl->assign('TOTAL', nv_number_format($price_total));
        $xtpl->assign('COLSPAN', $colspan);
        $xtpl->parse('main.tour_price_caculate');
    }
}

if (! empty($booking_info['customer'])) {
    $number = 1;
    $price_total = 0;
    foreach ($booking_info['customer'] as $customer) {
        $price_total += $customer['price'];
        $customer['number'] = $number ++;
        $customer['birthday'] = nv_date('d/m/Y', $customer['birthday']);
        $customer['gender'] = $lang_module['gender_' . $customer['gender']];
        $customer['age'] = $age_config[$customer['age']]['name'];
        $customer['customer_type'] = $customer['customer_type'] >= 0 ? $lang_module['customer_type_' . $customer['customer_type']] : '';
        $customer['price'] = nv_number_format($customer['price']);
        $xtpl->assign('CUSTOMER', $customer);
        
        $price_method = $tour_info['price_method'];
        $subprice = $tour_info['subprice'];
        
        if ($price_method == 1) {
            $xtpl->parse('main.age_thead');
            $xtpl->parse('main.customer.loop.age_tbody');
        } elseif ($price_method == 2) {
            $xtpl->parse('main.age_thead');
            $xtpl->parse('main.customer.loop.age_tbody');
            
            $xtpl->parse('main.customer.customer_type_thead');
            $xtpl->parse('main.customer.loop.customer_type_tbody');
        }
        
        $optional_thead = array();
        if (! empty($subprice)) {
            if (! empty($customer['optional'])) {
                $array_optional = explode(',', $customer['optional']);
                foreach ($array_optional as $optional) {
                    list ($subprice_id, $subprice_value) = explode('_', $optional);
                    $xtpl->assign('OPTIONAL', $subprice_value ? $lang_module['yes'] : $lang_module['no']);
                    $xtpl->parse('main.customer.loop.optional_tbody');
                    $optional_thead[$subprice_id] = $subprice[$subprice_id]['title'];
                }   
            }
        }
        $xtpl->parse('main.customer.loop');
    }
    
    if (! empty($optional_thead)) {
        foreach ($optional_thead as $head) {
            $xtpl->assign('TITLE', $head);
            $xtpl->parse('main.optional_thead');
        }
    }
    $xtpl->assign('TOTAL', nv_number_format($price_total));
    $xtpl->parse('main.customer');
}

if (! empty($_array_payment_method)) {
    $xtpl->assign('PAYMENT_METHOD', $_array_payment_method[$booking_info['payment_method']]);
    $xtpl->parse('main.payment_method');
}

if($is_coupons){
    $xtpl->parse('main.coupons_note');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = sprintf($lang_module['booking_detail'], $booking_info['booking_code']);
$set_active_op = 'booking';

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
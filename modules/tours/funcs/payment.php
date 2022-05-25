<?php

/**
 * @Project NUKEVIET 4.x
 * @Author hongoctrien (contact@mynukeviet.net)
 * @Copyright (C) 2016 hongoctrien. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Sun, 08 May 2016 07:42:57 GMT
 */
if (! defined('NV_IS_MOD_TOURS'))
    die('Stop!!!');

$tour_info = $booking_info = array();
$ajax = $nv_Request->isset_request('ajax', 'post, get');
if ($ajax) {
    $code = $nv_Request->get_title('code', 'post, get', '');
    $checksum = $nv_Request->get_title('checksum', 'post, get', '');
    $array_op[1] = $code . '-' . $checksum;
}

$booking_id = $nv_Request->get_int('booking_id', 'get', 0);
$checkss = $nv_Request->get_string('checkss', 'get', '');

if (isset($array_op[1])) {
    $booking = explode('-', $array_op[1]);
    if (count($booking) < 2) {
        Header('Location: ' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true));
        die();
    }
    list ($booking_code, $checksum) = $booking;
    
    $booking_info = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_booking WHERE booking_code=' . $db->quote($booking_code) . ' AND checksum=' . $db->quote($checksum))
        ->fetch();
    if (empty($booking_info)) {
        Header('Location: ' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true));
        die();
    }
    $booking_info['booking_time_str'] = nv_date('H:i d/m/Y', $booking_info['booking_time']);
    $booking_info['customerprice'] = ! empty($booking_info['customerprice']) ? unserialize($booking_info['customerprice']) : array();
    
    $tour_info = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows WHERE status=1 AND id=' . $booking_info['tour_id'])->fetch();
    $tour_info['title'] = $tour_info[NV_LANG_DATA . '_title'];
    $tour_info['alias'] = $tour_info[NV_LANG_DATA . '_alias'];
    $tour_info['date_start'] = nv_get_date_start($tour_info['date_start_method'], $tour_info['date_start_config'], $tour_info['date_start']);
    $tour_info['province'] = nv_location_get_province_info($tour_info['place_start']);
    $tour_info['link'] = NV_MY_DOMAIN . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_cat[$tour_info['catid']]['alias'] . '/' . $tour_info['alias'] . $global_config['rewrite_exturl'], true);
    
    $cat_info = $array_cat[$tour_info['catid']];
    $tour_info['price_method'] = $cat_info['price_method'];
    $tour_info['subprice'] = array();
    $cat_info['subprice'] = unserialize($cat_info['subprice']);
    $cat_info['subprice'][] = 0;
    $result = $db->query('SELECT id, ' . NV_LANG_DATA . '_title title, is_optional, ' . NV_LANG_DATA . '_note note FROM ' . $db_config['prefix'] . '_' . $module_data . '_subprice WHERE status=1 AND id IN (' . implode(',', $cat_info['subprice']) . ') ORDER BY weight');
    while ($_row = $result->fetch()) {
        $tour_info['subprice'][$_row['id']] = $_row;
    }
} elseif ($booking_id > 0 and $nv_Request->isset_request('payment', 'get') and $nv_Request->isset_request('checksum', 'get')) {
    $checksum = $nv_Request->get_string('checksum', 'get');
    $payment = $nv_Request->get_string('payment', 'get');
    
    // Thong tin booking
    $result = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_booking WHERE booking_id=' . $booking_id);
    $data = $result->fetch();
    
    if (empty($data)) {
        Header('Location: ' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true));
        die();
    }
    
    if (isset($data['transaction_status']) and intval($data['transaction_status']) == 0 and preg_match('/^[a-zA-Z0-9_]+$/', $payment) and $checksum == md5($booking_id . $payment . $global_config['sitekey'] . session_id()) and file_exists(NV_ROOTDIR . '/modules/' . $module_file . '/payment/' . $payment . '.checkout_url.php')) {
        $config = $db->query("SELECT * FROM " . $db_config['prefix'] . "_" . $module_data . "_payment WHERE payment = '" . $payment . "'")->fetch();
        $payment_config = unserialize(nv_base64_decode($config['config']));
        
        // Cap nhat cong thanh toan
        $transaction_status = 1;
        $payment_id = 0;
        $payment_amount = 0;
        $payment_data = '';
        
        $userid = (defined('NV_IS_USER')) ? $user_info['userid'] : 0;
        
        // $transaction_id = $db->insert_id("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_transaction (transaction_id, transaction_time, transaction_status, booking_id, userid, payment, payment_id, payment_time, payment_amount, payment_data) VALUES (NULL, " . NV_CURRENTTIME . ", '" . $transaction_status . "', '" . $booking_id . "', '" . $userid . "', '" . $payment . "', '" . $payment_id . "', " . NV_CURRENTTIME . ", '" . $payment_amount . "', '" . $payment_data . "')");
        
        // $db->query('UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_booking SET transaction_status=' . $transaction_status . ' , transaction_id = ' . $transaction_id . ' , transaction_count = 1 WHERE booking_id=' . $booking_id);
        
        require_once NV_ROOTDIR . '/modules/' . $module_file . '/payment/' . $payment . '.checkout_url.php';
    } else {
        if ($result->rowCount() > 0) {
            $url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $module_info['alias']['payment'] . '/' . $data['booking_code'] . '-' . $data['checksum'];
        } else {
            $url = NV_BASE_SITEURL;
        }
        Header('Location: ' . $url);
        die();
    }
} elseif ($nv_Request->isset_request('complete', 'get') and $nv_Request->isset_request('payment', 'get') and $nv_Request->isset_request('token', 'get')) {
    $payment = $nv_Request->get_title('payment', 'get', '');
    if (file_exists(NV_ROOTDIR . "/modules/" . $module_file . "/payment/" . $payment . ".complete.php")) {
        $stmt = $db->prepare("SELECT * FROM " . $db_config['prefix'] . "_" . $module_data . "_payment WHERE active=1 and payment= :payment");
        $stmt->bindParam(':payment', $payment, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount()) {
            $row = $stmt->fetch();
            $payment_config = unserialize(nv_base64_decode($row['config']));
            $payment_config['paymentname'] = $row['paymentname'];
            $payment_config['domain'] = $row['domain'];

            // Thong tin booking
            $result = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_booking WHERE booking_id=' . $booking_id);
            $data = $result->fetch();

            require_once NV_ROOTDIR . "/modules/" . $module_file . "/payment/" . $payment . ".complete.php";
        }
    }
} else {
    Header('Location: ' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true));
    die();
}

$booking_info['customer'] = array();
$result = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_booking_customer WHERE booking_id=' . $booking_info['booking_id']);
while ($_row = $result->fetch()) {
    $booking_info['customer'][] = $_row;
}

$_sql = 'SELECT id, ' . NV_LANG_DATA . '_description description FROM ' . $db_config['prefix'] . '_' . $module_data . '_payment_method WHERE status=1 ORDER BY weight';
$_array_payment_method = $nv_Cache->db($_sql, 'id', $module_name);

$booking_info['coupons_code'] = '';
if ($array_config['coupons'] and $booking_info['coupons_id'] > 0) {
    $booking_info['coupons_code'] = $db->query('SELECT code FROM ' . $db_config['prefix'] . '_' . $module_data . '_coupons WHERE id=' . $booking_info['coupons_id'])->fetchColumn();
}

// Xay dung cac url thanh toan truc tuyen
$url_checkout = array();
$intro_pay = '';

if (intval($booking_info['transaction_status']) == - 1) {
    $intro_pay = $lang_module['payment_none_pay'];
} elseif ($booking_info['transaction_status'] == 0) {
    $sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_payment WHERE active=1 ORDER BY weight ASC';
    $result = $db->query($sql);
    
    while ($row = $result->fetch()) {
        if (file_exists(NV_ROOTDIR . '/modules/' . $module_file . '/payment/' . $row['payment'] . '.checkout_url.php')) {
            $payment_config = unserialize(nv_base64_decode($row['config']));
            $payment_config['paymentname'] = $row['paymentname'];
            $payment_config['domain'] = $row['domain'];
            
            $images_button = $row['images_button'];
            
            $url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;booking_id=' . $booking_info['booking_id'] . '&amp;payment=' . $row['payment'] . '&amp;checksum=' . md5($booking_info['booking_id'] . $row['payment'] . $global_config['sitekey'] . session_id());
            
            if (! empty($images_button) and file_exists(NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $images_button)) {
                $images_button = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $images_button;
            }
            
            $url_checkout[] = array(
                'name' => $row['paymentname'],
                'url' => $url,
                'images_button' => $images_button
            );
        }
    }
} elseif ($booking_info['transaction_status'] == 1 and $booking_info['transaction_id'] > 0) {
    if ($nv_Request->isset_request('cancel', 'get')) {
        $db->query('DELETE FROM ' . $db_config['prefix'] . '_' . $module_data . '_transaction WHERE transaction_id = ' . $data['transaction_id']);
        $db->query('UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_orders SET transaction_status=0, transaction_id = 0, transaction_count = 0 WHERE booking_id=' . $booking_id);
        Header('Location: ' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=payment&booking_id=' . $booking_id . '&checkss=' . $checkss);
        die();
    }
    
    $payment = $db->query('SELECT payment FROM ' . $db_config['prefix'] . '_' . $module_data . '_transaction WHERE transaction_id=' . $booking_info['transaction_id'])->fetchColumn();
    $config = $db->query("SELECT * FROM " . $db_config['prefix'] . "_" . $module_data . "_payment WHERE payment = '" . $payment . "'")->fetch();
    $intro_pay = sprintf($lang_module['order_by_payment'], $config['domain'], $config['paymentname']);
}
if ($booking_info['transaction_status'] == 4) {
    $booking_info['transaction_name'] = $lang_module['history_payment_yes'];
} elseif ($booking_info['transaction_status'] == 3) {
    $booking_info['transaction_name'] = $lang_module['history_payment_cancel'];
} elseif ($booking_info['transaction_status'] == 2) {
    $booking_info['transaction_name'] = $lang_module['history_payment_check'];
} elseif ($booking_info['transaction_status'] == 1) {
    $booking_info['transaction_name'] = $lang_module['history_payment_send'];
} elseif ($booking_info['transaction_status'] == 0) {
    $booking_info['transaction_name'] = $lang_module['history_payment_no'];
} elseif ($booking_info['transaction_status'] == - 1) {
    $booking_info['transaction_name'] = $lang_module['history_payment_wait'];
} else {
    $booking_info['transaction_name'] = 'ERROR';
}

$contents = nv_theme_tours_info($tour_info, $booking_info, 'payment', $url_checkout, $intro_pay);
$page_title = $lang_module['booking_detail'] . ' ' . $booking_info['booking_code'];
$array_mod_title[] = array(
    'title' => $lang_module['payment']
);

if ($ajax) {
    echo $contents;
} else {
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

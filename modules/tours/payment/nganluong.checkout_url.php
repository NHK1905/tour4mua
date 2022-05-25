<?php

/**
 * @Project NUKEVIET 3.0
 * @Author VINADES., JSC (contact@vinades.vn)
 * @Copyright (C) 2010 VINADES ., JSC. All rights reserved
 * @Createdate Dec 29, 2010  10:42:00 PM
 */
if (! defined('NV_IS_MOD_TOURS')) {
    die('Stop!!!');
}

// Thông tin cấu hình gian hàng
foreach ($payment_config as $ckey => $cval) {
    $payment_config[$ckey] = nv_unhtmlspecialchars($cval);
}
unset($ckey, $cval);

$config = array(
    "merchant_id" => $payment_config['merchant_id'],
    "merchant_pass" => $payment_config['merchant_pass'],
    "signature" => $payment_config['signature'],
    "receiver" => $payment_config['receiver'],
    "url_api" => $payment_config['url_api']
);

include (NV_ROOTDIR . '/modules/' . $module_file . '/payment/nganluong.class.php');
$nlcheckout = new NL_CheckOutV3($config['merchant_id'], $config['merchant_pass'], $config['receiver'], $config['url_api']);

$return_url = urlencode(NV_MY_DOMAIN . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $module_info['alias']['payment'] . '&booking_id=' . $booking_id . '&payment=' . $payment . '&checksum=' . md5($booking_id . $payment . $global_config['sitekey'] . session_id()) . '&getexpresscheckoutdetails=1');
$_cancel_url = NV_MY_DOMAIN . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $module_info['alias']['payment'] . '/' . $data['booking_code'] . '-' . $data['checksum'], true);
$cancel_url = urlencode($_cancel_url);

if ($nv_Request->isset_request('getexpresscheckoutdetails', 'get')) {
    $token = $nv_Request->get_title('token', 'get', '');
    $nl_result = $nlcheckout->GetTransactionDetail($token);
    if ($nl_result) {
        $nl_errorcode = (string) $nl_result->error_code;
        $nl_transaction_status = (string) $nl_result->transaction_status;
        if ($nl_errorcode == '00') { 
            $PayerData = array(
                'error_code' => $nl_errorcode,
                'token' => (string)$nl_result->token,
                'order_code' => (string)$nl_result->order_code,
                'total_amount' => (string)$nl_result->total_amount,
                'payment_method' => (string)$nl_result->payment_method,
                'payment_type' => (string)$nl_result->payment_type,
                'buyer_fullname' => (string)$nl_result->buyer_fullname,
                'buyer_email' => (string)$nl_result->buyer_email,
                'buyer_mobile' => (string)$nl_result->buyer_mobile,
                'buyer_address' => (string)$nl_result->buyer_address,
                'transaction_status' => (string)$nl_transaction_status,
                'transaction_id' => (int)$nl_result->transaction_id
            );
            $nv_Request->set_Session($module_data . "_payerdata_nganluong", serialize($PayerData));
            $completeURL = NV_MY_DOMAIN . NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $module_info['alias']['payment'] . "&complete=1&booking_id=" . $booking_id . "&payment=" . $payment . "&token=" . $nl_result->token;
            header("Location:" . $completeURL);
            die();
        } else {
            $contents = nv_theme_alert($lang_module['payment_title'], $nlcheckout->GetErrorMessage($nl_errorcode), 'error', $_cancel_url, $lang_module['payment_back']);
            include NV_ROOTDIR . '/includes/header.php';
            echo nv_site_theme($contents);
            include NV_ROOTDIR . '/includes/footer.php';
        }
    }
}

$total_amount = $data['booking_total'];

$array_items[0] = array(
    'item_name1' => $data['booking_code'],
    'item_quantity1' => 1,
    'item_amount1' => $data['booking_total'],
    'item_url1' => ''
);

$array_items = array();
$payment_method = 'NL';
$booking_code = $data['booking_code'];

$payment_type = '';
$discount_amount = 0;
$booking_description = '';
$tax_amount = 0;
$fee_shipping = 0;

$buyer_fullname = $data['contact_fullname'];
$buyer_email = $data['contact_email'];
$buyer_mobile = $data['contact_phone'];
$buyer_address = '';

if ($payment_method != '' && $buyer_email != "" && $buyer_mobile != "" && $buyer_fullname != "" && empty(nv_check_valid_email($buyer_email))) {
    $nl_result = $nlcheckout->NLCheckout($booking_code, $total_amount, $payment_type, $booking_description, $tax_amount, $fee_shipping, $discount_amount, $return_url, $cancel_url, $buyer_fullname, $buyer_email, $buyer_mobile, $buyer_address, $array_items);
    if ($nl_result->error_code == '00') {
        header('Location:' . $nl_result->checkout_url);
    } else {
        $contents = nv_theme_alert($lang_module['payment_title'], $nl_result->error_message, 'error', $_cancel_url, $lang_module['payment_back']);
        include NV_ROOTDIR . '/includes/header.php';
        echo nv_site_theme($contents);
        include NV_ROOTDIR . '/includes/footer.php';
    }
} else {
    $contents = nv_theme_alert($lang_module['payment_title'], $lang_module['payment_info_not_vaild'], 'error', $_cancel_url, $lang_module['payment_back']);
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

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
$token = $nv_Request->get_title('token', 'get', '');
$nl_result = $nlcheckout->GetTransactionDetail($token);

if ($nl_result) {
    $nl_errorcode = (string) $nl_result->error_code;
    $nl_transaction_status = (string) $nl_result->transaction_status;
    $nl_transaction_id = (string) $nl_result->transaction_id;
    if ($nl_errorcode == '00') {
        $Status = 0;
        switch ($nl_transaction_status) {
            case '00':
                $Status = 4;
                break;
            case '01':
                $Status = 2;
                break;
            default:
                $Status = 0;
        }
        
        $nv_Request->unset_request($module_data . "_payerdata_nganluong", "session");
        
        if ($Status == 4 and $nl_transaction_id > 0) {
            $error_update = false;
                       
            $PaymentDetail = $nv_Request->get_string($module_data . "_payerdata_nganluong", 'session', '');
            $PaymentDetail = $PaymentDetail ? unserialize($PaymentDetail) : array();
            $payment_data = nv_base64_encode(serialize($PaymentDetail));
            
            $db->sqlreset()
            ->select('payment_data')
            ->from($db_config['prefix'] . "_" . $module_data . "_transaction")
            ->where("payment='" . $payment . "' AND payment_id= :payment_id")
            ->order('transaction_id DESC')
            ->limit(1);
            
            $stmt = $db->prepare($db->sql());
            $stmt->bindParam(':payment_id', $PaymentDetail['transaction_id'], PDO::PARAM_STR);
            $stmt->execute();
            
            $payment_data_old = $stmt->fetchColumn();
                        
            if ($payment_data != $payment_data_old) {
                $nv_transaction_status = intval($Status);
                $payment_amount = intval($amt);
                $payment_time = $PaymentDate;
            
                $transaction_id = $db->insert_id("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_transaction (transaction_id, transaction_time, transaction_status, booking_id, userid, payment, payment_id, payment_time, payment_amount, payment_data) VALUES (NULL, " . NV_CURRENTTIME . ", '" . $nv_transaction_status . "', '" . $data['booking_id'] . "', '0', '" . $payment . "', '" . $PaymentDetail['transaction_id'] . "', '" . NV_CURRENTTIME . "', '" . $PaymentDetail['total_amount'] . "', '" . $payment_data . "')");
            
                if ($transaction_id > 0) {
                    $db->query("UPDATE " . $db_config['prefix'] . "_" . $module_data . "_booking SET transaction_status=" . $nv_transaction_status . " , transaction_id = " . $transaction_id . " , transaction_count = transaction_count+1 WHERE booking_id=" . $data['booking_id']);
                } else {
                    $error_update = true;
                }
            }

            if (! $error_update) {
                $lang_module['payment_complete'] = sprintf($lang_module['payment_complete'], $data['booking_code']);
                $contents = nv_theme_alert($lang_module['payment_title'], $lang_module['payment_complete'], 'info', $cancel_url, $lang_module['payment_back'], 10);
                include NV_ROOTDIR . '/includes/header.php';
                echo nv_site_theme($contents);
                include NV_ROOTDIR . '/includes/footer.php';
            }
        }
    } else {
        $contents = nv_theme_alert($message_title, $nlcheckout->GetErrorMessage($nl_errorcode), 'error', $cancel_url, $lang_module['payment_back']);
        include NV_ROOTDIR . '/includes/header.php';
        echo nv_site_theme($contents);
        include NV_ROOTDIR . '/includes/footer.php';
    }
}

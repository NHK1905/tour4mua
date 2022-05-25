<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES., JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES ., JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Dec 29, 2010 10:42:00 PM
 */
if (! defined('NV_IS_MOD_TOURS')) {
    die('Stop!!!');
}

use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\EBLBaseComponents\AddressType;
use PayPal\EBLBaseComponents\PaymentDetailsType;
use PayPal\EBLBaseComponents\SetExpressCheckoutRequestDetailsType;
use PayPal\PayPalAPI\SetExpressCheckoutRequestType;
use PayPal\EBLBaseComponents\BillingAgreementDetailsType;
use PayPal\PayPalAPI\SetExpressCheckoutReq;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsReq;
use PayPal\Service\PayPalAPIInterfaceServiceService;
use PayPal\EBLBaseComponents\PaymentDetailsItemType;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsRequestType;
use PayPal\EBLBaseComponents\DoExpressCheckoutPaymentRequestDetailsType;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentRequestType;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentReq;

// Thông tin cấu hình gian hàng
foreach ($payment_config as $ckey => $cval) {
    $payment_config[$ckey] = nv_unhtmlspecialchars($cval);
}
unset($ckey, $cval);

$config = array(
    "mode" => $payment_config['environment'],
    "acct1.UserName" => $payment_config['apiusername'],
    "acct1.Password" => $payment_config['apipassword'],
    "acct1.Signature" => $payment_config['signature']
);

// Đường dẫn trả về nếu có lỗi
$BackUrl = NV_MY_DOMAIN . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $module_info['alias']['payment'] . '/' . $data['booking_code'] . '-' . $data['checksum'], true);

/*
 * DoExpressCheckoutPayment API
 */

// Lấy thông tin
$payerID = $nv_Request->get_string("payerid", "get", "");
$token = $nv_Request->get_string("token", "get", "");
$paymentAction = $payment_config['paymentaction'];

if (empty($payerID) or empty($token)) {
    $contents = nv_theme_alert($lang_module['payment_title'], "Error Access!!!", 'error', $BackUrl, $lang_module['payment_back']);
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

$getExpressCheckoutDetailsRequest = new GetExpressCheckoutDetailsRequestType($token);
$getExpressCheckoutReq = new GetExpressCheckoutDetailsReq();
$getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;

$paypalService = new PayPalAPIInterfaceServiceService($config);
try {
    $getECResponse = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);
} catch (Exception $ex) {
    $contents = nv_theme_alert($lang_module['payment_title'], $ex->getMessage(), 'error', $BackUrl, $lang_module['payment_back']);
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

// Lấy thông tin session
$PaymentDetail = $nv_Request->get_string($module_data . "_payerdata_paypal", 'session', '');
$PaymentDetail = $PaymentDetail ? unserialize($PaymentDetail) : array();

if (empty($PaymentDetail) or $PaymentDetail['token'] !== $token or $PaymentDetail['id'] !== $payerID) {
    $contents = nv_theme_alert($lang_module['payment_title'], 'Data Error!!!', 'error', $BackUrl, $lang_module['payment_back']);
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

$orderTotal = new BasicAmountType();
$orderTotal->currencyID = $PaymentDetail['currency'];
$orderTotal->value = $PaymentDetail['amount'];

$paymentDetails = new PaymentDetailsType();
$paymentDetails->OrderTotal = $orderTotal;

$DoECRequestDetails = new DoExpressCheckoutPaymentRequestDetailsType();
$DoECRequestDetails->PayerID = $payerID;
$DoECRequestDetails->Token = $token;
$DoECRequestDetails->PaymentAction = $paymentAction;
$DoECRequestDetails->PaymentDetails[0] = $paymentDetails;

$DoECRequest = new DoExpressCheckoutPaymentRequestType();
$DoECRequest->DoExpressCheckoutPaymentRequestDetails = $DoECRequestDetails;

$DoECReq = new DoExpressCheckoutPaymentReq();
$DoECReq->DoExpressCheckoutPaymentRequest = $DoECRequest;
try {
    $DoECResponse = $paypalService->DoExpressCheckoutPayment($DoECReq);
} catch (Exception $ex) {
    $contents = nv_theme_alert($lang_module['payment_title'], $ex->getMessage(), 'error', $cancelUrl, $lang_module['payment_back']);
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

if (isset($DoECResponse)) {
    if ($DoECResponse->Ack == 'Success' or $DoECResponse->Ack == 'SuccessWithWarning') {
        // Lấy thông tin chi tiết
        $details = $DoECResponse->DoExpressCheckoutPaymentResponseDetails;
        
        $payment_info = $details->PaymentInfo[0];
        $tran_ID = $payment_info->TransactionID;
        
        $amt_obj = $payment_info->GrossAmount;
        $amt = $amt_obj->value;
        $currency_cd = $amt_obj->currencyID;
        
        $PaymentStatus = $payment_info->PaymentStatus;
        $PaymentDate = $payment_info->PaymentDate;
        $PaymentDate = strtotime($PaymentDate);
        if ($PaymentDate < 0) {
            $PaymentDate = 0;
        }
        
        /*
         * Thông số mặc định của PayPal
         * Completed - Thanh toán hoàn thành
         * Pending - Thanh toán đang chờ
         * Failed - Thanh toán không thành công
         * Denied - Bị từ chối thanh toán
         * Refunded - Được hoàn tiền thanh toán
         * Canceled_Reversal - Thanh toán ngược bị hủy
         * Reversed - Thanh toán ngược lại (hoàn trả)
         * Expired - Thanh toán bị hết hạn
         * Processed - Đang thực hiện thanh toán
         * Voided - Bị hủy bỏ vì không được xác thực
         * Created - Đang khởi tạo
         */
        
        $Status = 0;
        switch ($PaymentStatus) {
            case 'Canceled_Reversal':
                $Status = 5;
                break;
            case 'Completed':
                $Status = 4;
                break;
            case 'Denied':
                $Status = 6;
                break;
            case 'Expired':
                $Status = 7;
                break;
            case 'Failed':
                $Status = 8;
                break;
            case 'Pending':
                $Status = 2;
                break;
            case 'Processed':
                $Status = 9;
                break;
            case 'Refunded':
                $Status = 10;
                break;
            case 'Reversed':
                $Status = 11;
                break;
            case 'Voided':
                $Status = 3;
                break;
            case 'Created':
                $Status = 0;
                break;
            default:
                $Status = - 1;
        }
        
        $nv_Request->unset_request($module_data . "_payerdata_paypal", "session");
        
        if ($PaymentDetail['booking_id'] > 0) {
            $error_update = false;
            
            $PaymentDetail['transaction_status'] = $Status;
            $PaymentDetail['transaction_time'] = $PaymentDate;
            $PaymentDetail['transaction_id'] = $tran_ID;
            $payment_data = nv_base64_encode(serialize($PaymentDetail));
            
            $db->sqlreset()
                ->select('payment_data')
                ->from($db_config['prefix'] . "_" . $module_data . "_transaction")
                ->where("payment='" . $payment . "' AND payment_id= :payment_id")
                ->order('transaction_id DESC')
                ->limit(1);
            
            $stmt = $db->prepare($db->sql());
            $stmt->bindParam(':payment_id', $tran_ID, PDO::PARAM_STR);
            $stmt->execute();
            
            $payment_data_old = $stmt->fetchColumn();
            
            if ($payment_data != $payment_data_old) {
                $nv_transaction_status = intval($Status);
                $payment_amount = intval($amt);
                $payment_time = $PaymentDate;
                
                $transaction_id = $db->insert_id("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_transaction (transaction_id, transaction_time, transaction_status, booking_id, userid, payment, payment_id, payment_time, payment_amount, payment_data) VALUES (NULL, " . NV_CURRENTTIME . ", '" . $nv_transaction_status . "', '" . $PaymentDetail['booking_id'] . "', '0', '" . $payment . "', '" . $tran_ID . "', '" . $payment_time . "', '" . $payment_amount . "', '" . $payment_data . "')");
                
                if ($transaction_id > 0) {
                    $db->query("UPDATE " . $db_config['prefix'] . "_" . $module_data . "_booking SET transaction_status=" . $nv_transaction_status . " , transaction_id = " . $transaction_id . " , transaction_count = transaction_count+1 WHERE booking_id=" . $PaymentDetail['booking_id']);
                } else {
                    $error_update = true;
                }
            }

            if (! $error_update) {
                $lang_module['payment_complete'] = sprintf($lang_module['payment_complete'], $data['booking_code']);
                $contents = nv_theme_alert($lang_module['payment_title'], $lang_module['payment_complete'], 'info', $BackUrl, $lang_module['payment_back'], 10);
                include NV_ROOTDIR . '/includes/header.php';
                echo nv_site_theme($contents);
                include NV_ROOTDIR . '/includes/footer.php';
            }
        }
    }
}

$contents = nv_theme_alert($lang_module['payment_title'], "Unknow Error!!!", 'error', $BackUrl, $lang_module['payment_back']);
include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
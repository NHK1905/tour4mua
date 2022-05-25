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

// Thông tin cấu hình gian hàng
foreach ($payment_config as $ckey => $cval) {
    $payment_config[$ckey] = nv_unhtmlspecialchars($cval);
}
unset($ckey, $cval);

$config = array(
    "mode" => $payment_config['environment'],
    "acct1.UserName" => $payment_config['apiusername'],
    "acct1.Password" => $payment_config['apipassword'],
    "acct1.Signature" => $payment_config['signature'],
);

// Đường dẫn trả về
$returnUrl = NV_MY_DOMAIN . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $module_info['alias']['payment'] . '&booking_id=' . $booking_id . '&payment=' . $payment . '&checksum=' . md5($booking_id . $payment . $global_config['sitekey'] . session_id()) . '&getexpresscheckoutdetails=1';

// Đường dẫn hủy thanh toán
$cancelUrl = NV_MY_DOMAIN . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $module_info['alias']['payment'] . '/' . $data['booking_code'] . '-' . $data['checksum'];

if ($nv_Request->isset_request("getexpresscheckoutdetails", "get")) {

    $token = nv_htmlspecialchars($nv_Request->get_string("token", "get", ""));

    $getExpressCheckoutDetailsRequest = new GetExpressCheckoutDetailsRequestType($token);

    $getExpressCheckoutReq = new GetExpressCheckoutDetailsReq();
    $getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;

    $paypalService = new PayPalAPIInterfaceServiceService($config);

    try {
        $getECResponse = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);
    } catch (Exception $ex) {
        redict_link($ex->getMessage(), $lang_module['cart_back'], $cancelUrl);
    }

    if (isset($getECResponse)) {
        if ($getECResponse->Ack == 'Success') {
            // Trích xuất thông tin
            $responseDetails = $getECResponse->GetExpressCheckoutDetailsResponseDetails;
            $payerInfo = $responseDetails->PayerInfo;

            $payer = $payerInfo->Payer;
            $payerID = $payerInfo->PayerID;
            $payer_name = $payerInfo->PayerName;
            $payer_fname = $payer_name->FirstName;
            $payer_lname = $payer_name->LastName;

            $address = $payerInfo->Address;
            $street1 = $address->Street1;
            $street2 = $address->Street2;
            $cityName = $address->CityName;
            $stateOrProvince = $address->StateOrProvince;
            $postalCode = $address->PostalCode;
            $countryCode = $address->CountryName;

            $PaymentDetails = $responseDetails->PaymentDetails[0]->OrderTotal;

            $PayerData = array(
                "token" => $token,
                "id" => $payerID,
                "payer" => $payer,
                "fname" => $payer_fname,
                "lname" => $payer_lname,
                "street1" => $street1,
                "street2" => $street2,
                "cityname" => $cityName,
                "stateorprovince" => $stateOrProvince,
                "postalcode" => $postalCode,
                "countrycode" => $countryCode,
                "amount" => $PaymentDetails->value,
                "currency" => $PaymentDetails->currencyID,
                "booking_id" => $booking_id,
            );

            $nv_Request->set_Session($module_data . "_payerdata_paypal", serialize($PayerData));
            $doExpressURL = NV_MY_DOMAIN . NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $module_info['alias']['payment'] . "&complete=1&booking_id=" . $booking_id ."&payment=" . $payment . "&token=" . $token . "&payerid=" . $payerID;
            header("Location:" . $doExpressURL);
            exit();
        } else {
            $contents = nv_theme_alert($lang_module['payment_title'], $getECResponse->Errors[0]->ShortMessage . "<br />" . $getECResponse->Errors[0]->LongMessage, 'error', $cancelUrl, $lang_module['payment_back']);
            include NV_ROOTDIR . '/includes/header.php';
            echo nv_site_theme($contents);
            include NV_ROOTDIR . '/includes/footer.php';
        }
    }

    $contents = nv_theme_alert($lang_module['payment_title'], "Unknow Error!!!", 'error', $cancelUrl, $lang_module['payment_back']);
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

$paypalService = new PayPalAPIInterfaceServiceService($config);
$paymentDetails= new PaymentDetailsType();

$itemDetails = new PaymentDetailsItemType();
$itemDetails->Name = $data['booking_code'];
$itemAmount = nv_currency_conversion($data['booking_total'], $data['unit_total'], 'USD');
$itemAmount = round($itemAmount, 1);
$itemDetails->Amount = $itemAmount;
$itemQuantity = '1';
$itemDetails->Quantity = $itemQuantity;

$paymentDetails->PaymentDetailsItem[0] = $itemDetails;

$orderTotal = new BasicAmountType();
$orderTotal->currencyID = 'USD';
$orderTotal->value = $itemAmount * $itemQuantity;

$paymentDetails->OrderTotal = $orderTotal;
$paymentDetails->PaymentAction = 'Sale';

$setECReqDetails = new SetExpressCheckoutRequestDetailsType();
$setECReqDetails->PaymentDetails[0] = $paymentDetails;
$setECReqDetails->CancelURL = $cancelUrl;
$setECReqDetails->ReturnURL = $returnUrl;

$setECReqType = new SetExpressCheckoutRequestType();
$setECReqType->Version = '104.0';
$setECReqType->SetExpressCheckoutRequestDetails = $setECReqDetails;

$setECReq = new SetExpressCheckoutReq();
$setECReq->SetExpressCheckoutRequest = $setECReqType;

try {
    $setECResponse = $paypalService->SetExpressCheckout($setECReq);
} catch (Exception $ex) {
    $contents = nv_theme_alert($lang_module['payment_title'], $ex->getMessage(), 'info', $cancelUrl, $lang_module['payment_back']);
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

if (isset($setECResponse)) {
    if ($setECResponse->Ack == 'Success') {
        $token = $setECResponse->Token;

        $payPalURL = "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=" . $token;
        if ("sandbox" === $payment_config['environment'] || "beta-sandbox" === $payment_config['environment']) {
            $payPalURL = "https://www." . $payment_config['environment'] . ".paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=" . $token;
        }
        header("Location: " . $payPalURL);
        exit;
    } else {
        $contents = nv_theme_alert($lang_module['payment_title'], $setECResponse->Errors[0]->ShortMessage . "<br />" . $setECResponse->Errors[0]->LongMessage, 'info', $cancelUrl, $lang_module['payment_back']);
        include NV_ROOTDIR . '/includes/header.php';
        echo nv_site_theme($contents);
        include NV_ROOTDIR . '/includes/footer.php';
    }
}

$contents = nv_theme_alert($lang_module['payment_title'], "Unknow Error!!!", 'error', $cancelUrl, $lang_module['payment_back']);
include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
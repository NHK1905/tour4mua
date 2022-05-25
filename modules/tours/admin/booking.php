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

$table_name = $db_config['prefix'] . '_' . $module_data . '_booking';

if ($nv_Request->isset_request('delete_id', 'get') and $nv_Request->isset_request('delete_checkss', 'get')) {
    $id = $nv_Request->get_int('delete_id', 'get');
    $delete_checkss = $nv_Request->get_string('delete_checkss', 'get');
    if ($id > 0 and $delete_checkss == md5($id . NV_CACHE_PREFIX . $client_info['session_id'])) {
        nv_booking_delete($id);
        $nv_Cache->delMod($module_name);
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        die();
    }
} elseif ($nv_Request->isset_request('delete_list', 'post')) {
    $listall = $nv_Request->get_title('listall', 'post', '');
    $array_id = explode(',', $listall);
    
    if (! empty($array_id)) {
        foreach ($array_id as $id) {
            nv_booking_delete($id);
        }
        $nv_Cache->delMod($module_name);
        die('OK');
    }
    die('NO');
}

$row = array();
$error = array();
$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
$array_search = array(
    'code' => $nv_Request->get_title('code', 'get', ''),
    'from' => $nv_Request->get_title('from', 'get', ''),
    'to' => $nv_Request->get_title('to', 'get', ''),
    'payment' => $nv_Request->get_int('payment', 'get', -1),
);

$where = '';
if(!empty($array_search['code'])){
    $base_url .= '&code=' . $array_search['code'];
    $where .= ' AND booking_code=' . $db->quote($array_search['code']);
}

if (! empty($array_search['from']) and preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $array_search['from'], $m)) {
    $array_search['from'] = mktime(0, 0, 0, $m[2], $m[1], $m[3]);
    $base_url .= '&from=' . $array_search['from'];
    $where .= ' AND booking_time >= ' . $array_search['from'];
}

if (! empty($array_search['to']) and preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $array_search['to'], $m)) {
    $array_search['to'] = mktime(23, 59, 59, $m[2], $m[1], $m[3]);
    $base_url .= '&to=' . $array_search['to'];
    $where .= ' AND booking_time <= ' . $array_search['to'];
}

if($array_search['payment'] >= 0){
    $base_url .= '&payment=' . $array_search['payment'];
    $where .= ' AND transaction_status=' . $array_search['payment'];
}

$per_page = 20;
$page = $nv_Request->get_int('page', 'post,get', 1);
$db->sqlreset()
    ->select('COUNT(*)')
    ->from('' . $table_name . '')
    ->where('1=1' . $where);

$sth = $db->prepare($db->sql());
$sth->execute();
$num_items = $sth->fetchColumn();

$db->select('*')
    ->order('booking_id DESC')
    ->limit($per_page)
    ->offset(($page - 1) * $per_page);
$sth = $db->prepare($db->sql());
$sth->execute();

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);
$xtpl->assign('SEARCH', $array_search);
$xtpl->assign('BASE_URL', $base_url);

$generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
if (! empty($generate_page)) {
    $xtpl->assign('NV_GENERATE_PAGE', $generate_page);
    $xtpl->parse('main.generate_page');
}
while ($view = $sth->fetch()) {
    $view['booking_time'] = nv_date('H:i d/m/Y', $view['booking_time']);
    $booking_total = $view['booking_total'] - $view['coupons_value'];
    $view['booking_total'] = nv_number_format($booking_total);
    $view['payment_status'] = $lang_module['payment_status_' . $view['transaction_status']];
    $view['link_view'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=booking-detail&amp;booking_id=' . $view['booking_id'];
    $view['link_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;delete_id=' . $view['booking_id'] . '&amp;delete_checkss=' . md5($view['booking_id'] . NV_CACHE_PREFIX . $client_info['session_id']);
    $xtpl->assign('VIEW', $view);
    if(!$view['booking_viewed']){
        $xtpl->parse('main.loop.booking_new');
    }
    $xtpl->parse('main.loop');
}

if(!empty($array_payment_status)){
    foreach($array_payment_status as $index => $value){
        $xtpl->assign('PAYMENT_STATUS', array(
            'index' => $index,
            'value' => $value,
            'selected' => $array_search['payment'] == $index ? 'selected="selected"' : ''
        ));
        $xtpl->parse('main.payment_status');
    }
}

$array_action = array(
    'delete' => $lang_global['delete']
);
foreach ($array_action as $key => $value) {
    $xtpl->assign('ACTION', array(
        'key' => $key,
        'value' => $value
    ));
    $xtpl->parse('main.action');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['booking_list'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
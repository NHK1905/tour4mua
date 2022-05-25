<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-9-2010 14:43
 */
if (! defined('NV_IS_FILE_ADMIN'))
    die('Stop!!!');

$page_title = $lang_module['config'];

$data = array();
$currentpath = NV_UPLOADS_DIR . '/' . $module_upload;
$groups_list = nv_groups_list();

if ($nv_Request->isset_request('savesetting', 'post')) {
    $data['allow_auto_code'] = $nv_Request->get_int('allow_auto_code', 'post');
    $data['format_code'] = $nv_Request->get_title('format_code', 'post', 'T%06s');
    $data['format_booking_code'] = $nv_Request->get_title('format_booking_code', 'post', '%09s');
    $data['money_unit'] = $nv_Request->get_title('money_unit', 'post', 'VND');
    $data['structure_upload'] = $nv_Request->get_title('structure_upload', 'post', 'Ym');
    $data['home_type'] = $nv_Request->get_int('home_type', 'post', 0);
    $data['per_page'] = $nv_Request->get_int('per_page', 'post', 21);
    $data['title_lenght'] = $nv_Request->get_int('title_lenght', 'post', 25);
    $data['no_image'] = $nv_Request->get_title('no_image', 'post', '');
    if (is_file(NV_DOCUMENT_ROOT . $data['no_image'])) {
        $data['no_image'] = substr($data['no_image'], strlen(NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/'));
    } else {
        $data['no_image'] = '';
    }
    $data['booking_type'] = $nv_Request->get_int('booking_type', 'post', 2);
    $_groups_post = $nv_Request->get_array('booking_groups', 'post', array());
    $data['booking_groups'] = ! empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';
    $data['rule_content'] = $nv_Request->get_editor('rule_content', '', NV_ALLOWED_HTML_TAGS);
    $data['booking_sendmail'] = $nv_Request->get_int('booking_sendmail', 'post', 0);
    $_groups_post = $nv_Request->get_array('booking_groups_sendmail', 'post', array());
    $data['booking_groups_sendmail'] = ! empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';
    $data['default_place_start'] = $nv_Request->get_int('provinceid', 'post', 1);
    $data['coupons'] = $nv_Request->get_int('coupons', 'post', 0);
    $data['tags_alias'] = $nv_Request->get_int('tags_alias', 'post', 0);
    $data['auto_tags'] = $nv_Request->get_int('auto_tags', 'post', 0);
    $data['tags_remind'] = $nv_Request->get_int('tags_remind', 'post', 0);
    $data['date_start_method'] = $nv_Request->get_int('date_start_method', 'post', 0);
    $data['contact_info'] = $nv_Request->get_editor('contact_info', '', NV_ALLOWED_HTML_TAGS);
    $data['note_content'] = $nv_Request->get_editor('note_content', '', NV_ALLOWED_HTML_TAGS);
    $data['age_config'] = $nv_Request->get_array('age_config', 'post', '');
    $data['price_base'] = $nv_Request->get_int('price_base', 'post', 0);
    if(!empty($data['age_config'])){
        foreach($data['age_config'] as $index => $value){
            if(empty($value['name'])){
                unset($data['age_config'][$index]);
            }else{
                $data['age_config'][$index]['price_base'] = 0;
                if($data['price_base'] == $index){
                    $data['age_config'][$index]['price_base'] = 1;
                }
            }
        }
        $data['age_config'] = serialize($data['age_config']);
    }
    $data['home_image_size_w'] = $nv_Request->get_title('home_image_w', 'post', '250');
    $data['home_image_size_h'] = $nv_Request->get_title('home_image_h', 'post', '150');
    $data['home_image_size'] = $data['home_image_size_w'] . 'x' . $data['home_image_size_h'];
    $data['booking_price_method'] = $nv_Request->get_int('booking_price_method', 'post', 0);
    
    $sth = $db->prepare("UPDATE " . NV_CONFIG_GLOBALTABLE . " SET config_value = :config_value WHERE lang = '" . NV_LANG_DATA . "' AND module = :module_name AND config_name = :config_name");
    $sth->bindParam(':module_name', $module_name, PDO::PARAM_STR);
    foreach ($data as $config_name => $config_value) {
        $sth->bindParam(':config_name', $config_name, PDO::PARAM_STR);
        $sth->bindParam(':config_value', $config_value, PDO::PARAM_STR);
        $sth->execute();
    }
    
    nv_insert_logs(NV_LANG_DATA, $module_name, $lang_module['config'], "Config", $admin_info['userid']);
    $nv_Cache->delMod('settings');
    
    Header("Location: " . NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . '=' . $op);
    die();
}

$array_config['ck_allow_auto_code'] = $array_config['allow_auto_code'] ? 'checked="checked"' : '';
$array_config['style_format_code'] = $array_config['allow_auto_code'] ? '' : 'style="display: none"';
$array_config['style_booking_groups'] = $array_config['booking_type'] == 2 ? '' : 'style="display: none"';
$array_config['ck_booking_sendmail'] = $array_config['booking_sendmail'] ? 'checked="checked"' : '';
$array_config['ck_coupons'] = $array_config['coupons'] ? 'checked="checked"' : '';
$array_config['ck_tags_alias'] = $array_config['tags_alias'] ? 'checked="checked"' : '';
$array_config['ck_auto_tags'] = $array_config['auto_tags'] ? 'checked="checked"' : '';
$array_config['ck_tags_remind'] = $array_config['tags_remind'] ? 'checked="checked"' : '';

if (! empty($array_config['no_image']) and file_exists(NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $array_config['no_image'])) {
    $currentpath = NV_UPLOADS_DIR . '/' . $module_upload . '/' . dirname($array_config['no_image']);
    $array_config['no_image'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $array_config['no_image'];
}

if (defined('NV_EDITOR')) {
    require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
}

$array_config['contact_info'] = htmlspecialchars(nv_editor_br2nl($array_config['contact_info']));
$array_config['note_content'] = htmlspecialchars(nv_editor_br2nl($array_config['note_content']));
$array_config['rule_content'] = htmlspecialchars(nv_editor_br2nl($array_config['rule_content']));
if (defined('NV_EDITOR') and nv_function_exists('nv_aleditor')) {
    $array_config['contact_info'] = nv_aleditor('contact_info', '100%', '200px', $array_config['contact_info'], 'Basic');
    $array_config['note_content'] = nv_aleditor('note_content', '100%', '200px', $array_config['note_content'], 'Basic');
    $array_config['rule_content'] = nv_aleditor('rule_content', '100%', '200px', $array_config['rule_content'], 'Basic');
} else {
    $array_config['contact_info'] = '<textarea style="width:100%;height:300px" name="contact_info">' . $array_config['contact_info'] . '</textarea>';
    $array_config['contact_info'] = '<textarea style="width:100%;height:300px" name="contact_info">' . $array_config['contact_info'] . '</textarea>';
    $array_config['note_content'] = '<textarea style="width:100%;height:300px" name="note_content">' . $array_config['note_content'] . '</textarea>';
}

list($array_config['home_image_size_w'], $array_config['home_image_size_h']) = explode('x', $array_config['home_image_size']);

$xtpl = new XTemplate($op . ".tpl", NV_ROOTDIR . "/themes/" . $global_config['module_theme'] . "/modules/" . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('DATA', $array_config);
$xtpl->assign('MODULE_UPLOAD', $module_upload);
$xtpl->assign('CURRENTPATH', $currentpath);

// Tien te
$result = $db->query("SELECT code, currency FROM " . $db_config['prefix'] . "_" . $module_data . "_money_" . NV_LANG_DATA . " ORDER BY code DESC");
while (list ($code, $currency) = $result->fetch(3)) {
    $array_temp = array();
    $array_temp['value'] = $code;
    $array_temp['title'] = $code . " - " . $currency;
    $array_temp['selected'] = ($code == $array_config['money_unit']) ? " selected=\"selected\"" : "";
    $xtpl->assign('DATAMONEY', $array_temp);
    $xtpl->parse('main.money_loop');
}

$array_structure_image = array();
$array_structure_image[''] = NV_UPLOADS_DIR . '/' . $module_upload;
$array_structure_image['Y'] = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date('Y');
$array_structure_image['Ym'] = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date('Y_m');
$array_structure_image['Y_m'] = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date('Y/m');
$array_structure_image['Ym_d'] = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date('Y_m/d');
$array_structure_image['Y_m_d'] = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date('Y/m/d');
$array_structure_image['username'] = NV_UPLOADS_DIR . '/' . $module_upload . '/username_admin';

$array_structure_image['username_Y'] = NV_UPLOADS_DIR . '/' . $module_upload . '/username_admin/' . date('Y');
$array_structure_image['username_Ym'] = NV_UPLOADS_DIR . '/' . $module_upload . '/username_admin/' . date('Y_m');
$array_structure_image['username_Y_m'] = NV_UPLOADS_DIR . '/' . $module_upload . '/username_admin/' . date('Y/m');
$array_structure_image['username_Ym_d'] = NV_UPLOADS_DIR . '/' . $module_upload . '/username_admin/' . date('Y_m/d');
$array_structure_image['username_Y_m_d'] = NV_UPLOADS_DIR . '/' . $module_upload . '/username_admin/' . date('Y/m/d');

$structure_image_upload = isset($array_config['structure_upload']) ? $array_config['structure_upload'] : "Ym";

// Thu muc uploads
foreach ($array_structure_image as $type => $dir) {
    $xtpl->assign('STRUCTURE_UPLOAD', array(
        'key' => $type,
        'title' => $dir,
        'selected' => $type == $structure_image_upload ? ' selected="selected"' : ''
    ));
    $xtpl->parse('main.structure_upload');
}

$array_home_type = array(
    '0' => $lang_module['config_home_type_0'],
    '2' => $lang_module['config_home_type_2'],
    '1' => $lang_module['config_home_type_1'],
    '5' => $lang_module['config_home_type_5']
);
foreach ($array_home_type as $index => $value) {
    $xtpl->assign('HOME_TYPE', array(
        'index' => $index,
        'value' => $value,
        'selected' => $array_config['home_type'] == $index ? 'selected="selected"' : ''
    ));
    $xtpl->parse('main.home_type');
}

$array_booking_type = array(
    '2' => $lang_module['config_booking_type_2'],
    '1' => $lang_module['config_booking_type_1'],
    '0' => $lang_module['config_booking_type_0']
);
foreach ($array_booking_type as $index => $value) {
    $xtpl->assign('BOOKING_TYPE', array(
        'index' => $index,
        'value' => $value,
        'checked' => $array_config['booking_type'] == $index ? 'checked="checked"' : ''
    ));
    $xtpl->parse('main.booking_type');
}

$groups_booking = explode(',', $array_config['booking_groups']);
foreach ($groups_list as $group_id => $grtl) {
    $_groups_booking = array(
        'value' => $group_id,
        'checked' => in_array($group_id, $groups_booking) ? ' checked="checked"' : '',
        'title' => $grtl
    );
    $xtpl->assign('GROUPS_BOOKING', $_groups_booking);
    $xtpl->parse('main.booking_groups');
}

$groups_booking_sendmail = explode(',', $array_config['booking_groups_sendmail']);
foreach ($groups_list as $group_id => $grtl) {
    $_groups_booking_sendmail = array(
        'value' => $group_id,
        'checked' => in_array($group_id, $groups_booking_sendmail) ? ' checked="checked"' : '',
        'title' => $grtl
    );
    $xtpl->assign('GROUPS_BOOKING_SENDMAIL', $_groups_booking_sendmail);
    $xtpl->parse('main.booking_groups_sendmail');
}

$data_config = array(
    'select_countyid' => nv_location_get_countryid_from_province($array_config['default_place_start']),
    'select_provinceid' => $array_config['default_place_start']
);
$xtpl->assign('LOCATION', nv_location_build_input($data_config));

foreach ($array_date_start_method as $index => $value) {
    $xtpl->assign('DATE_START_METHOD', array(
        'index' => $index,
        'value' => $value,
        'checked' => $array_config['date_start_method'] == $index ? 'checked="checked"' : ''
    ));
    $xtpl->parse('main.date_start_method');
}

$array_age_config[] = array(
    'index' => 0,
    'name' => '',
    'from' => '',
    'to' => '',
    'price_base' => 1
);
$array_config['age_config'] = !empty($array_config['age_config']) ? unserialize($array_config['age_config']) : $array_age_config;
if(!empty($array_config['age_config'])){
    $i=0;
    foreach($array_config['age_config'] as $index => $value){
        $value['index'] = $i;
        $value['checked'] = $value['price_base'] ? 'checked="checked"' : '';
        $xtpl->assign('AGE_CONFIG', $value);
        $xtpl->parse('main.age_config');
        $i++;
    }
}
$xtpl->assign('NUMAGE', count($array_config['age_config']));

$array_booking_price_method = array(
    '0' => $lang_module['config_booking_price_method_0'],
    '1' => $lang_module['config_booking_price_method_1']
);
foreach($array_booking_price_method as $index => $value){
    $ck = $array_config['booking_price_method'] == $index ? 'checked="checked"' : '';
    $xtpl->assign('PRICE_METHOD', array('index' => $index, 'value' => $value, 'checked' => $ck));
    $xtpl->parse('main.price_config');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
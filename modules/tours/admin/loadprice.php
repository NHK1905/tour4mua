<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2016 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 09 May 2016 09:18:57 GMT
 */
if (! defined('NV_IS_FILE_ADMIN'))
    die('Stop!!!');

$contents = '';
$subprice = $cat = array();
$price_method = 0;
$row = array(
    'price_method' => '',
    'money_unit' => $array_config['money_unit'],
    'discounts_id' => 0,
    'show_price' => 1
);

$catid = $nv_Request->get_int('catid', 'post, get', 0);
$row_id = $nv_Request->get_int('row_id', 'post, get', 0);

if ($catid > 0) {
    $cat = $array_cat[$catid];
    $price_method = $cat['price_method'];
    
    if ($row_id > 0) {
        $row = $db->query('SELECT price, price_config, money_unit, discounts_id, show_price FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows WHERE id=' . $row_id)->fetch();
        $row['price_config'] = unserialize($row['price_config']);
    }
    
    if ($nv_Request->isset_request('base_get_price', 'post, get')) {
        $price_base = $nv_Request->get_string('base_price', 'post,get', '');
        $price_base = floatval(preg_replace('/[^0-9]/', '', $price_base));
        $col = $nv_Request->get_int('col', 'post,get');
        
        if ($cat['price_method_auto'] and ! empty($cat['price_method_config']) and $price_base > 0) {
            $cat['price_method_config'] = unserialize($cat['price_method_config']);
            $auto_price = array();
            if(!empty($cat['price_method_config'])){
                foreach($cat['price_method_config'] as $index => $value){
                    if($cat['price_method'] == 1){
                        $auto_price[$index] = ($price_base * $value) / 100;
                    }elseif($cat['price_method'] == 2){
                        $auto_price[$index . '_' . $col] = ($price_base * $value) / 100;
                    }
                }
            }
            die(json_encode($auto_price));
        }
    }
    
    if (! empty($cat)) {
        $cat['subprice'] = unserialize($cat['subprice']);
        $cat['subprice'][] = 0;
        
        $result = $db->query('SELECT id, ' . NV_LANG_DATA . '_title title FROM ' . $db_config['prefix'] . '_' . $module_data . '_subprice WHERE status=1 AND id IN (' . implode(',', $cat['subprice']) . ') ORDER BY weight');
        while ($_row = $result->fetch()) {
            $subprice[$_row['id']] = $_row;
        }
    }
    
    // giam gia
    $_sql = 'SELECT did, title FROM ' . $db_config['prefix'] . '_' . $module_data . '_discounts ORDER BY did DESC';
    $array_discounts = $nv_Cache->db($_sql, 'did', $module_name);
}

$number_format = explode('||', $money_config[$array_config['money_unit']]['number_format']);

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('ROW_ID', $row_id);
$xtpl->assign('CAT_ID', $catid);
$xtpl->assign('NUMBER_FORMAT', array(
    'aSep' => $number_format[0],
    'aDec' => $number_format[1]
));

if ($price_method == 0) {
    $xtpl->assign('PRICE', isset($row['price_config']['price'][0]) ? $row['price_config']['price'][0] : '');
    if (! empty($subprice)) {
        foreach ($subprice as $subprice_id => $_subprice) {
            $_subprice['title_clean'] = nv_clean60($_subprice['title'], 20);
            $_subprice['price'] = (isset($row['price_config']['subprice'][$subprice_id]) and !empty($row['price_config']['subprice'][$subprice_id])) ? $row['price_config']['subprice'][$subprice_id] : '';
            $_subprice['price'] = $_subprice['price'] > 0 ? $_subprice['price'] : '';
            $xtpl->assign('SUBPRICE', $_subprice);
            $xtpl->parse('main.price_method_0.subprice.loop');
        }
        $xtpl->parse('main.price_method_0.subprice');
    }
    
    if($row['show_price']){
        $xtpl->parse('main.price_method_0.required');
    }
    
    $xtpl->parse('main.price_method_0');
} elseif ($price_method == 1) {
    $array_config['age_config'] = !empty($array_config['age_config']) ? unserialize($array_config['age_config']) : array();
    foreach($array_config['age_config'] as $index => $value){
        $xtpl->assign('TITLE', $value);
        $xtpl->assign('KEY', $index);
        $xtpl->assign('PRICE', (isset($row['price_config']['price'][$index]) and !empty($row['price_config']['price'][$index])) ? $row['price_config']['price'][$index] : '');
        if ($value['price_base'] and $cat['price_method_auto'] and ! empty($cat['price_method_config'])) {
            $xtpl->parse('main.price_method_1.price.price_base');
        }
        if($row['show_price']){
            $xtpl->parse('main.price_method_1.price.required');
        }
        $xtpl->parse('main.price_method_1.price');
        $xtpl->parse('main.price_method_1.title');
    }
    
    if (! empty($subprice)) {
        foreach ($subprice as $subprice_id => $_subprice) {
            foreach($array_config['age_config'] as $index => $value){
                $_subprice['key'] = $index;
                $_subprice['title_clean'] = nv_clean60($_subprice['title'], 20);
                $_subprice['price'] = (isset($row['price_config']['subprice'][$subprice_id][$index]) and !empty($row['price_config']['subprice'][$subprice_id][$index])) ? $row['price_config']['subprice'][$subprice_id][$index] : '';
                $_subprice['price'] = $_subprice['price'] > 0 ? $_subprice['price'] : '';
                $xtpl->assign('SUBPRICE', $_subprice);
                $xtpl->parse('main.price_method_1.subprice.loop');
            }
            $xtpl->parse('main.price_method_1.subprice');
        }
    }
    
    $xtpl->parse('main.price_method_1');
} elseif ($price_method == 2) {
    $array_config['age_config'] = !empty($array_config['age_config']) ? unserialize($array_config['age_config']) : array();
    foreach($array_config['age_config'] as $index => $value){
        $xtpl->assign('TITLE', $value);
        for ($i = 0; $i < 3; $i ++) {
            $xtpl->assign('KEY', array(
                'i' => $i,
                'j' => $index
            ));
            $xtpl->assign('PRICE', (isset($row['price_config']['price'][$index][$i]) and !empty($row['price_config']['price'][$index][$i])) ? $row['price_config']['price'][$index][$i] : '');
            if ($value['price_base'] and $cat['price_method_auto'] and ! empty($cat['price_method_config'])) {
                $xtpl->parse('main.price_method_2.price.loop.price_base');
            }
            $xtpl->parse('main.price_method_2.price.loop');
        }
        $xtpl->parse('main.price_method_2.price');
    }
    
    if (! empty($subprice)) {
        foreach ($subprice as $subprice_id => $_subprice) {
            for ($i = 0; $i < 3; $i ++) {
                $_subprice['key'] = $i;
                $_subprice['title_clean'] = nv_clean60($_subprice['title'], 30);
                $_subprice['price'] = (isset($row['price_config']['subprice'][$subprice_id][$i]) and !empty($row['price_config']['subprice'][$subprice_id][$i])) ? $row['price_config']['subprice'][$subprice_id][$i] : '';
                $_subprice['price'] = $_subprice['price'] > 0 ? $_subprice['price'] : '';
                $xtpl->assign('SUBPRICE', $_subprice);
                $xtpl->parse('main.price_method_2.subprice.loop');
            }
            $xtpl->parse('main.price_method_2.subprice');
        }
    }
    $xtpl->parse('main.price_method_2');
}

if (! empty($array_discounts)) {
    foreach ($array_discounts as $discounts) {
        $discounts['selected'] = $row['discounts_id'] == $discounts['did'] ? 'selected="selected"' : '';
        $xtpl->assign('DISCOUNTS', $discounts);
        $xtpl->parse('main.discounts');
    }
}

if (! empty($money_config)) {
    foreach ($money_config as $code => $info) {
        $info['selected'] = ($row['money_unit'] == $code) ? "selected=\"selected\"" : "";
        $xtpl->assign('MONEY', $info);
        $xtpl->parse('main.money_unit');
    }
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

die($contents);
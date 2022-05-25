<?php

/**
 * @Project NUKEVIET 4.x
 * @Author hongoctrien (contact@mynukeviet.net)
 * @Copyright (C) 2016 hongoctrien. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Sun, 08 May 2016 07:42:57 GMT
 */
if (! defined('NV_SYSTEM'))
    die('Stop!!!');
    
define('NV_IS_MOD_TOURS', true);
require_once NV_ROOTDIR . '/modules/' . $module_file . '/global.functions.php';
require_once NV_ROOTDIR . '/modules/' . $module_file . '/site.functions.php';
require_once NV_ROOTDIR . '/modules/location/data.functions.php';

$page = 1;
$per_page = $array_config['per_page'];

$catid = $parentid = 0;
$alias_cat_url = isset($array_op[0]) ? $array_op[0] : '';
$array_mod_title = array();

// Categories
foreach ($array_cat as $row) {
    $array_cat[$row['id']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $row['alias'];
    if ($alias_cat_url == $row['alias']) {
        $catid = $row['id'];
        $parentid = $row['parentid'];
    }
}

$count_op = sizeof($array_op);
if (! empty($array_op) and $op == 'main') {
    if ($catid == 0) {
        $contents = $lang_module['nocatpage'] . $array_op[0];
        if (isset($array_op[0]) and substr($array_op[0], 0, 5) == 'page-') {
            $page = intval(substr($array_op[0], 5));
        } elseif (! empty($alias_cat_url)) {
            $redirect = '<meta http-equiv="Refresh" content="3;URL=' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true) . '" />';
            nv_info_die($lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] . $redirect);
        }
    } else {
        $op = 'main';
        if (sizeof($array_op) == 2 and preg_match('/^([a-z0-9\-]+)$/i', $array_op[1]) and ! preg_match('/^page\-([0-9]+)$/', $array_op[1], $m2)) {
            $op = 'detail';
            $alias_url = $array_op[1];
        } else {
            $op = 'viewcat';
            if( isset($array_op[1]) and substr($array_op[1], 0, 5) == 'page-' ){
                $page = intval(substr($array_op[1], 5));
            }
        }
        
        $parentid = $catid;
        while ($parentid > 0) {
            $array_cat_i = $array_cat[$parentid];
            $array_mod_title[] = array(
                'catid' => $parentid,
                'title' => $array_cat_i['title'],
                'link' => $array_cat_i['link']
            );
            $parentid = $array_cat_i['parentid'];
        }
        sort($array_mod_title, SORT_NUMERIC);
    }
}

/**
 * booking_result()
 *
 * @param mixed $array            
 * @return
 *
 */
function nv_booking_result($array)
{
    $string = json_encode($array);
    return $string;
}

/**
 * nv_customer_price()
 *
 * @param mixed $tour_id            
 * @param mixed $age            
 * @param mixed $customer_type            
 * @param mixed $optional            
 * @return
 *
 */
function nv_customer_price($tour_id, $age, $customer_type, $optional = '')
{
    global $db, $db_config, $module_data, $array_cat, $array_config;
    
    $tour_info = $db->query('SELECT id, catid, price, price_config, money_unit FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows WHERE id=' . $tour_id)->fetch();
    if (! empty($tour_info)) {
        $price_method = $array_cat[$tour_info['catid']]['price_method'];
        $price_config = unserialize($tour_info['price_config']);
        $subprice = $array_cat[$tour_info['catid']]['subprice'];
        $subprice = unserialize($subprice);
        $total_price = $total_subprice = 0;
        
        // thong tin hang muc phu thu
        $array_subprice = array();
        if (! empty($subprice)) {
            $result = $db->query('SELECT id, is_optional FROM ' . $db_config['prefix'] . '_' . $module_data . '_subprice WHERE id IN(' . implode(',', $subprice) . ')');
            while ($_row = $result->fetch()) {
                $array_subprice[$_row['id']] = $_row;
            }
        }
        
        if ($price_method == 1) {
            $price = nv_get_price($tour_info['id'], $array_config['money_unit'], $price_config['price'][$age]);
            $total_price = $price['sale'];
            
            if (! empty($array_subprice)) {
                foreach ($array_subprice as $value) {
                    if ($value['is_optional'] == 0) {
                        if (isset($price_config['subprice'][$value['id']][$age]) and ! empty($price_config['subprice'][$value['id']][$age])) {
                            $price = nv_get_price($tour_info['id'], $array_config['money_unit'], $price_config['subprice'][$value['id']][$age]);
                            $total_subprice += $price['sale'];
                        }
                    } elseif (! empty($optional)) {
                        $array_optional = explode(',', $optional);
                        foreach ($array_optional as $_optional) {
                            list ($_id, $_value) = explode('_', $_optional);
                            if (isset($array_subprice[$_id]) and $array_subprice[$_id]['is_optional'] and $_value == 1 and ! empty($price_config['subprice'][$_id][$age])) {
                                $price = nv_get_price($tour_info['id'], $array_config['money_unit'], $price_config['subprice'][$_id][$age]);
                                $total_subprice += $price['sale'];
                            }
                        }
                    }
                }
            }
        } elseif ($price_method == 2) {
            $price = $price_config['price'][$age][$customer_type];
            $price = nv_get_price($tour_info['id'], $array_config['money_unit'], $price);
            $total_price = $price['sale'];
            
            if (! empty($array_subprice)) {
                foreach ($array_subprice as $value) {
                    if ($value['is_optional'] == 0) {
                        if (isset($price_config['subprice'][$value['id']][$customer_type]) and ! empty($price_config['subprice'][$value['id']][$customer_type])) {
                            $price = nv_get_price($tour_info['id'], $array_config['money_unit'], $price_config['subprice'][$value['id']][$customer_type]);
                            $total_subprice += $price['sale'];
                        }
                    } elseif (! empty($optional)) {
                        $array_optional = explode(',', $optional);
                        foreach ($array_optional as $_optional) {
                            list ($_id, $_value) = explode('_', $_optional);
                            if (isset($array_subprice[$_id]) and $array_subprice[$_id]['is_optional'] and $_value == 1 and ! empty($price_config['subprice'][$_id][$customer_type])) {
                                $price = nv_get_price($tour_info['id'], $array_config['money_unit'], $price_config['subprice'][$_id][$customer_type]);
                                $total_subprice += $price['sale'];
                            }
                        }
                    }
                }
            }
        } else {
            $price = nv_get_price($tour_info['id'], $array_config['money_unit']);
            $total_price = $price['sale'];
            
            if (! empty($array_subprice)) {
                foreach ($array_subprice as $value) {
                    if ($value['is_optional'] == 0) {
                        if (isset($price_config['subprice'][$value['id']]) and ! empty($price_config['subprice'][$value['id']])) {
                            $price = nv_get_price($tour_info['id'], $array_config['money_unit'], $price_config['subprice'][$value['id']]);
                            $total_subprice += $price['sale'];
                        }
                    } elseif (! empty($optional)) {
                        $array_optional = explode(',', $optional);
                        foreach ($array_optional as $_optional) {
                            list ($_id, $_value) = explode('_', $_optional);
                            if (isset($array_subprice[$_id]) and $array_subprice[$_id]['is_optional'] and $_value == 1 and ! empty($price_config['subprice'][$_id])) {
                                $price = nv_get_price($tour_info['id'], $array_config['money_unit'], $price_config['subprice'][$_id]);
                                $total_subprice += $price['sale'];
                            }
                        }
                    }
                }
            }
        }
        $total_price += $total_subprice;
    }
    return $total_price;
}

function nv_listmail_admin()
{
    global $db, $global_config, $array_config;
    
    $array_mail = array();
    if (! empty($array_config['booking_groups_sendmail'])) {
        $result = $db->query('SELECT email FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid IN ( SELECT userid FROM ' . NV_GROUPS_GLOBALTABLE . '_users WHERE group_id IN ( ' . $array_config['booking_groups_sendmail'] . ' ) )');
        while (list ($email) = $result->fetch(3)) {
            $array_mail[] = $email;
        }
    }
    $array_mail = array_unique($array_mail);
    return $array_mail;
}

/**
 * GetCatidInParent()
 *
 * @param mixed $catid            
 * @return
 *
 */
function GetCatidInParent($catid)
{
    global $array_cat;
    
    $_array_cat = array();
    $_array_cat[] = $catid;
    $subcatid = explode(',', $array_cat[$catid]['subid']);
    
    if (! empty($subcatid)) {
        foreach ($subcatid as $id) {
            if ($id > 0) {
                if ($array_cat[$id]['numsub'] == 0) {
                    $_array_cat[] = $id;
                } else {
                    $array_cat_temp = GetCatidInParent($id);
                    foreach ($array_cat_temp as $catid_i) {
                        $_array_cat[] = $catid_i;
                    }
                }
            }
        }
    }
    return array_unique($_array_cat);
}

/**
 * nv_search_date_start()
 *
 * @param mixed $date_begin
 * @param mixed $date_end
 * @return
 *
 */
function nv_search_date_start($date_begin, $date_end)
{
    global $db, $db_config, $module_data;
    
    $array_id = array();
    $result = $db->query('SELECT id, date_start_method, date_start_config, date_start FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows WHERE status=1');
    while($_row = $result->fetch()){
        if(!empty($date_begin) and !empty($date_end)){
            if($_row['date_start_method'] == 0){ // mot ngay
                if($_row['date_start'] >= $date_begin and $_row['date_start'] <= $date_end){
                    $array_id[] = $_row['id'];
                }
            }elseif($_row['date_start_method'] == 3){ // hang ngay
                $array_id[] = $_row['id'];
            }elseif($_row['date_start_method'] == 1){ // hang tuan
                $_row['date_start_config'] = unserialize($_row['date_start_config']);
                if(!empty($_row['date_start_config'])){
                    for($i=$date_begin; $i<=$date_end; $i+=86400){
                        if(in_array(nv_date('N', $i), $_row['date_start_config'])){
                            $array_id[] = $_row['id'];
                        }
                    }
                }
            }elseif($_row['date_start_method'] == 2){ // hang thang
                $_row['date_start_config'] = unserialize($_row['date_start_config']);
                if(!empty($_row['date_start_config'])){
                    for($i=$date_begin; $i<=$date_end; $i+=86400){
                        if(in_array(nv_date('j', $i), $_row['date_start_config'])){
                            $array_id[] = $_row['id'];
                        }
                    }
                }
            }
        }elseif(!empty($date_begin) or !empty($date_end)){
            if($_row['date_start_method'] == 0){ // mot ngay
                if($_row['date_start'] >= $date_begin){
                    $array_id[] = $_row['id'];
                }
            }elseif($_row['date_start_method'] == 1 or $_row['date_start_method'] == 2 or $_row['date_start_method'] == 3){
                $array_id[] = $_row['id'];
            }
        }
    }
    $array_id = array_unique($array_id);
    return $array_id;
}
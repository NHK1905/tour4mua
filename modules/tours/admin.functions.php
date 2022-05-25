<?php

/**
 * @Project NUKEVIET 4.x
 * @Author hongoctrien (contact@mynukeviet.net)
 * @Copyright (C) 2016 hongoctrien. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Sun, 08 May 2016 07:42:57 GMT
 */
if (! defined('NV_ADMIN') or ! defined('NV_MAINFILE') or ! defined('NV_IS_MODADMIN'))
    die('Stop!!!');

define('NV_IS_FILE_ADMIN', true);
require_once NV_ROOTDIR . '/modules/' . $module_file . '/global.functions.php';
require_once NV_ROOTDIR . '/modules/' . $module_file . '/site.functions.php';
require_once NV_ROOTDIR . '/modules/location/data.functions.php';

$allow_func = array(
    'main',
    'config',
    'cat',
    'change_cat',
    'services',
    'discounts',
    'guides',
    'guide-content',
    'content',
    'hotels',
    'hotels-content',
    'money',
    'flying',
    'subprice',
    'loadprice',
    'images',
    'upload',
    'groups',
    'list_block_cat',
    'chang_block_cat',
    'block',
    'blockcat',
    'change_block',
    'list_block',
    'payment-method',
    'tags',
    'tagsajax',
    'payport',
    'vehicle'
);

if (! $array_config['coupons']) {
    unset($submenu['coupons']);
} else {
    $allow_func[] = 'coupons';
    $allow_func[] = 'coupons-content';
}

if ($array_config['booking_type'] == 2) {
    $allow_func[] = 'booking';
    $allow_func[] = 'booking-detail';
}else{
    unset($submenu['booking']);
}

$array_date_start_method = array(
    '0' => $lang_module['config_date_start_method_0'],
    '3' => $lang_module['config_date_start_method_3'],
    '1' => $lang_module['config_date_start_method_1'],
    '2' => $lang_module['config_date_start_method_2'],
    '4' => $lang_module['config_date_start_method_4']
);

/**
 * nv_fix_order()
 *
 * @param integer $parentid            
 * @param integer $order            
 * @param integer $lev            
 * @return
 *
 */
function nv_fix_order($table_name, $parentid = 0, $sort = 0, $lev = 0)
{
    global $db, $db_config, $module_data;
    
    $sql = 'SELECT id, parentid FROM ' . $table_name . ' WHERE parentid=' . $parentid . ' ORDER BY weight ASC';
    $result = $db->query($sql);
    $array_order = array();
    while ($row = $result->fetch()) {
        $array_order[] = $row['id'];
    }
    $result->closeCursor();
    $weight = 0;
    if ($parentid > 0) {
        ++ $lev;
    } else {
        $lev = 0;
    }
    foreach ($array_order as $order_i) {
        ++ $sort;
        ++ $weight;
        
        $sql = 'UPDATE ' . $table_name . ' SET weight=' . $weight . ', sort=' . $sort . ', lev=' . $lev . ' WHERE id=' . $order_i;
        $db->query($sql);
        
        $sort = nv_fix_order($table_name, $order_i, $sort, $lev);
    }
    
    $numsub = $weight;
    
    if ($parentid > 0) {
        $sql = "UPDATE " . $table_name . " SET numsub=" . $numsub;
        if ($numsub == 0) {
            $sql .= ",subid=''";
        } else {
            $sql .= ",subid='" . implode(",", $array_order) . "'";
        }
        $sql .= " WHERE id=" . intval($parentid);
        $db->query($sql);
    }
    return $sort;
}

/**
 * nv_file_table()
 *
 * @param mixed $table            
 * @return
 *
 */
function nv_file_table($table)
{
    global $db_config, $db;
    $lang_value = nv_list_lang();
    $arrfield = array();
    $result = $db->query('SHOW COLUMNS FROM ' . $table);
    while (list ($field) = $result->fetch(3)) {
        $tmp = explode('_', $field, 2);
        foreach ($lang_value as $lang_i) {
            if (! empty($tmp[0]) && ! empty($tmp[1])) {
                if ($tmp[0] == $lang_i) {
                    $arrfield[] = array(
                        $tmp[0],
                        $tmp[1]
                    );
                    break;
                }
            }
        }
    }
    return $arrfield;
}

/**
 * nv_list_lang()
 *
 * @return
 *
 */
function nv_list_lang()
{
    global $db_config, $db;
    $re = $db->query('SELECT lang FROM ' . $db_config['prefix'] . '_setup_language WHERE setup=1');
    $lang_value = array();
    while (list ($lang_i) = $re->fetch(3)) {
        $lang_value[] = $lang_i;
    }
    return $lang_value;
}

function nv_resize_crop_images($img_path, $width, $height, $module_name = '', $id = 0)
{
    $new_img_path = str_replace(NV_ROOTDIR, '', $img_path);
    if (file_exists($img_path)) {
        $imginfo = nv_is_image($img_path);
        $basename = basename($img_path);
        
        $basename = preg_replace('/^\W+|\W+$/', '', $basename);
        $basename = preg_replace('/[ ]+/', '_', $basename);
        $basename = strtolower(preg_replace('/\W-/', '', $basename));
        
        if ($imginfo['width'] > $width or $imginfo['height'] > $height) {
            $basename = preg_replace('/(.*)(\.[a-zA-Z]+)$/', $module_name . '_' . $id . '_\1_' . $width . '-' . $height . '\2', $basename);
            if (file_exists(NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $basename)) {
                $new_img_path = NV_BASE_SITEURL . NV_TEMP_DIR . '/' . $basename;
            } else {
                $img_path = new NukeViet\Files\Image($img_path, NV_MAX_WIDTH, NV_MAX_HEIGHT);
                
                $thumb_width = $width;
                $thumb_height = $height;
                $maxwh = max($thumb_width, $thumb_height);
                if ($img_path->fileinfo['width'] > $img_path->fileinfo['height']) {
                    $width = 0;
                    $height = $maxwh;
                } else {
                    $width = $maxwh;
                    $height = 0;
                }
                
                $img_path->resizeXY($width, $height);
                $img_path->cropFromCenter($thumb_width, $thumb_height);
                $img_path->save(NV_ROOTDIR . '/' . NV_TEMP_DIR, $basename);
                
                if (file_exists(NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $basename)) {
                    $new_img_path = NV_BASE_SITEURL . NV_TEMP_DIR . '/' . $basename;
                }
            }
        }
    }
    return $new_img_path;
}

function gltJsonResponse($error = array(), $data = array())
{
    $contents = array(
        'jsonrpc' => '2.0',
        'error' => $error,
        'data' => $data
    );
    
    header('Content-Type: application/json');
    echo json_encode($contents);
    die();
}

function nv_delete_other_images_tmp($path, $thumb)
{
    if (file_exists($thumb)) {
        @nv_deletefile($thumb);
    }
    
    if (file_exists($path)) {
        $deleted = @nv_deletefile($path);
        $result = $deleted[0] ? true : false;
        
        return $result;
    }
}

/**
 * nv_show_groups_list()
 *
 * @return
 *
 */
function nv_show_groups_list()
{
    global $db, $db_config, $lang_module, $lang_global, $module_name, $module_data, $op, $module_file, $global_config, $module_info;
    
    $sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_block_cat ORDER BY weight ASC';
    $_array_block_cat = $db->query($sql)->fetchAll();
    $num = sizeof($_array_block_cat);
    
    if ($num > 0) {
        $array_adddefault = array(
            $lang_global['no'],
            $lang_global['yes']
        );
        
        $xtpl = new XTemplate('blockcat_lists.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
        $xtpl->assign('LANG', $lang_module);
        $xtpl->assign('GLANG', $lang_global);
        
        foreach ($_array_block_cat as $row) {
            $numnews = $db->query('SELECT COUNT(*) FROM ' . $db_config['prefix'] . '_' . $module_data . '_block where bid=' . $row['bid'])->fetchColumn();
            
            $xtpl->assign('ROW', array(
                'bid' => $row['bid'],
                'title' => $row[NV_LANG_DATA . '_title'],
                'numnews' => $numnews,
                'link' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=block&amp;bid=' . $row['bid'],
                'linksite' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['groups'] . '/' . $row[NV_LANG_DATA . '_alias'],
                'url_edit' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;bid=' . $row['bid'] . '#edit'
            ));
            
            for ($i = 1; $i <= $num; ++ $i) {
                $xtpl->assign('WEIGHT', array(
                    'key' => $i,
                    'title' => $i,
                    'selected' => $i == $row['weight'] ? ' selected="selected"' : ''
                ));
                $xtpl->parse('main.loop.weight');
            }
            
            foreach ($array_adddefault as $key => $val) {
                $xtpl->assign('ADDDEFAULT', array(
                    'key' => $key,
                    'title' => $val,
                    'selected' => $key == $row['adddefault'] ? ' selected="selected"' : ''
                ));
                $xtpl->parse('main.loop.adddefault');
            }
            
            for ($i = 1; $i <= 30; ++ $i) {
                $xtpl->assign('NUMBER', array(
                    'key' => $i,
                    'title' => $i,
                    'selected' => $i == $row['numbers'] ? ' selected="selected"' : ''
                ));
                $xtpl->parse('main.loop.number');
            }
            
            $xtpl->parse('main.loop');
        }
        
        $xtpl->parse('main');
        $contents = $xtpl->text('main');
    } else {
        $contents = '&nbsp;';
    }
    
    return $contents;
}

/**
 * nv_fix_block_cat()
 *
 * @return
 *
 */
function nv_fix_block_cat()
{
    global $db, $db_config, $module_data;
    
    $sql = 'SELECT bid FROM ' . $db_config['prefix'] . '_' . $module_data . '_block_cat ORDER BY weight ASC';
    $weight = 0;
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        ++ $weight;
        $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_block_cat SET weight=' . $weight . ' WHERE bid=' . intval($row['bid']);
        $db->query($sql);
    }
    $result->closeCursor();
}

/**
 * nv_show_block_list()
 *
 * @param mixed $bid            
 * @return
 *
 */
function nv_show_block_list($bid)
{
    global $db, $db_config, $lang_module, $lang_global, $module_name, $module_data, $op, $module_file, $global_config, $array_cat;
    
    $xtpl = new XTemplate('block_list.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('GLANG', $lang_global);
    $xtpl->assign('MODULE_NAME', $module_name);
    $xtpl->assign('OP', $op);
    $xtpl->assign('BID', $bid);
    
    $array_jobs[0] = array(
        'alias' => 'Other'
    );
    
    $sql = 'SELECT t1.id, t1.catid, t1.' . NV_LANG_DATA . '_title title, t1.' . NV_LANG_DATA . '_alias alias, t2.weight FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows t1 INNER JOIN ' . $db_config['prefix'] . '_' . $module_data . '_block t2 ON t1.id = t2.id WHERE t2.bid= ' . $bid . ' AND t1.status=1 ORDER BY t2.weight ASC';
    $array_block = $db->query($sql)->fetchAll();
    
    $num = sizeof($array_block);
    if ($num > 0) {
        foreach ($array_block as $row) {
            $xtpl->assign('ROW', array(
                'id' => $row['id'],
                'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_cat[$row['catid']]['alias'] . '/' . $row['alias'],
                'title' => $row['title']
            ));
            
            for ($i = 1; $i <= $num; ++ $i) {
                $xtpl->assign('WEIGHT', array(
                    'key' => $i,
                    'title' => $i,
                    'selected' => $i == $row['weight'] ? ' selected="selected"' : ''
                ));
                $xtpl->parse('main.loop.weight');
            }
            
            $xtpl->parse('main.loop');
        }
        
        $xtpl->parse('main');
        $contents = $xtpl->text('main');
    } else {
        $contents = '&nbsp;';
    }
    
    return $contents;
}

/**
 * nv_fix_block()
 *
 * @param mixed $bid            
 * @param bool $repairtable            
 * @return
 *
 */
function nv_fix_block($bid, $repairtable = true)
{
    global $db, $db_config, $module_data;
    $bid = intval($bid);
    if ($bid > 0) {
        $sql = 'SELECT id FROM ' . $db_config['prefix'] . '_' . $module_data . '_block where bid=' . $bid . ' ORDER BY weight ASC';
        $result = $db->query($sql);
        $weight = 0;
        while ($row = $result->fetch()) {
            ++ $weight;
            if ($weight <= 100) {
                $sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_block SET weight=' . $weight . ' WHERE bid=' . $bid . ' AND id=' . $row['id'];
            } else {
                $sql = 'DELETE FROM ' . $db_config['prefix'] . '_' . $module_data . '_block WHERE bid=' . $bid . ' AND id=' . $row['id'];
            }
            $db->query($sql);
        }
        $result->closeCursor();
        if ($repairtable) {
            $db->query('OPTIMIZE TABLE ' . $db_config['prefix'] . '_' . $module_data . '_block');
        }
    }
}

/**
 * drawselect_number()
 *
 * @param string $select_name
 * @param integer $number_start
 * @param integer $number_end
 * @param integer $number_curent
 * @param string $func_onchange
 * @return
 */
function drawselect_number( $select_name = "", $number_start = 0, $number_end = 1, $number_curent = 0, $func_onchange = "" )
{
    $html = "<select class=\"form-control\" name=\"" . $select_name . "\" onchange=\"" . $func_onchange . "\">";
    for( $i = $number_start; $i < $number_end; $i++ )
    {
        $select = ($i == $number_curent) ? "selected=\"selected\"" : "";
        $html .= "<option value=\"" . $i . "\"" . $select . ">" . $i . "</option>";
    }
    $html .= "</select>";
    return $html;
}
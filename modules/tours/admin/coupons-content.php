<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2015 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sun, 04 Jan 2015 08:16:04 GMT
 */
if (! defined('NV_IS_FILE_ADMIN'))
    die('Stop!!!');

if ($nv_Request->isset_request('delete_id', 'get') and $nv_Request->isset_request('delete_checkss', 'get')) {
    $id = $nv_Request->get_int('delete_id', 'get');
    $delete_checkss = $nv_Request->get_string('delete_checkss', 'get');
    if ($id > 0 and $delete_checkss == md5($id . NV_CACHE_PREFIX . $client_info['session_id'])) {
        $db->query('DELETE FROM ' . $db_config['prefix'] . '_' . $module_data . '_coupons WHERE id = ' . $db->quote($id));
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        die();
    }
}

if ($nv_Request->isset_request('get_tour_json', 'post, get')) {
    $q = $nv_Request->get_title('q', 'post, get', '');
    
    $db->sqlreset()
        ->select('id, code, ' . NV_LANG_DATA . '_title')
        ->from($db_config['prefix'] . '_' . $module_data . '_rows')
        ->where('code LIKE :code OR ' . NV_LANG_DATA . '_title LIKE :title OR ' . NV_LANG_DATA . '_alias LIKE :alias OR ' . NV_LANG_DATA . '_title_custom LIKE :title_custom OR ' . NV_LANG_DATA . '_plan LIKE :plan OR ' . NV_LANG_DATA . '_description LIKE :description OR ' . NV_LANG_DATA . '_description_html LIKE :description_html')
        ->order('id DESC')
        ->limit(20);
    
    $sth = $db->prepare($db->sql());
    $sth->bindValue(':code', '%' . $q . '%', PDO::PARAM_STR);
    $sth->bindValue(':title', '%' . $q . '%', PDO::PARAM_STR);
    $sth->bindValue(':alias', '%' . $q . '%', PDO::PARAM_STR);
    $sth->bindValue(':title_custom', '%' . $q . '%', PDO::PARAM_STR);
    $sth->bindValue(':plan', '%' . $q . '%', PDO::PARAM_STR);
    $sth->bindValue(':description', '%' . $q . '%', PDO::PARAM_STR);
    $sth->bindValue(':description_html', '%' . $q . '%', PDO::PARAM_STR);
    $sth->execute();
    
    $array_data = array();
    while (list ($id, $code, $title) = $sth->fetch(3)) {
        $array_data[] = array(
            'id' => $id,
            'code' => $code,
            'title' => $title
        );
    }
    
    header('Cache-Control: no-cache, must-revalidate');
    header('Content-type: application/json');
    
    ob_start('ob_gzhandler');
    echo json_encode($array_data);
    exit();
}

$row = $product_old = array();
$error = array();
$row['id'] = $nv_Request->get_int('id', 'post,get', 0);

if ($nv_Request->isset_request('submit', 'post')) {
    $row['title'] = $nv_Request->get_title('title', 'post', '');
    $row['code'] = $nv_Request->get_title('code', 'post', '');
    $row['type'] = $nv_Request->get_title('type', 'post', 'p');
    $row['discount'] = $nv_Request->get_title('discount', 'post', '');
    $row['tourid'] = $nv_Request->get_typed_array('tourid', 'post', 'int');
    $row['tourid'] = array_diff($row['tourid'], array(0));
    
    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $nv_Request->get_string('date_start', 'post'), $m)) {
        $_hour = 0;
        $_min = 0;
        $row['date_start'] = mktime($_hour, $_min, 0, $m[2], $m[1], $m[3]);
    } else {
        $row['date_start'] = 0;
    }
    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $nv_Request->get_string('date_end', 'post'), $m)) {
        $_hour = 23;
        $_min = 59;
        $_sec = 59;
        $row['date_end'] = mktime($_hour, $_min, $_sec, $m[2], $m[1], $m[3]);
    } else {
        $row['date_end'] = 0;
    }
    $row['quantity'] = $nv_Request->get_int('quantity', 'post', 0);
    
    if (empty($row['title'])) {
        $error[] = $lang_module['coupons_error_required_title'];
    } elseif (empty($row['code'])) {
        $error[] = $lang_module['coupons_error_required_code'];
    } elseif (! preg_match('/^\w+$/', $row['code'])) {
        $error[] = $lang_module['coupons_error_vail_code'];
    } elseif (empty($row['discount'])) {
        $error[] = $lang_module['coupons_error_required_discount'];
    }
    
    if (empty($error)) {
        try {
            $tourid = '';
            if (! empty($row['tourid'])) {
                $tourid = serialize($row['tourid']);
            }
            
            if (empty($row['id'])) {
                $sql = 'INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_coupons (title, code, type, discount, date_start, date_end, tourid, quantity, date_added, status) VALUES (:title, :code, :type, :discount, :date_start, :date_end, :tourid, :quantity, ' . NV_CURRENTTIME . ', 1)';
                $data_insert = array();
                $data_insert['title'] = $row['title'];
                $data_insert['code'] = $row['code'];
                $data_insert['type'] = $row['type'];
                $data_insert['discount'] = $row['discount'];
                $data_insert['date_start'] = $row['date_start'];
                $data_insert['date_end'] = $row['date_end'];
                $data_insert['tourid'] = $tourid;
                $data_insert['quantity'] = $row['quantity'];
                $insert_id = $db->insert_id($sql, 'id', $data_insert);
            } else {
                $stmt = $db->prepare('UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_coupons SET title = :title, code = :code, type = :type, discount = :discount, date_start = :date_start, date_end = :date_end, tourid = :tourid, quantity = :quantity WHERE id=' . $row['id']);
                $stmt->bindParam(':title', $row['title'], PDO::PARAM_STR);
                $stmt->bindParam(':code', $row['code'], PDO::PARAM_STR);
                $stmt->bindParam(':type', $row['type'], PDO::PARAM_STR);
                $stmt->bindParam(':discount', $row['discount'], PDO::PARAM_STR);
                $stmt->bindParam(':date_start', $row['date_start'], PDO::PARAM_INT);
                $stmt->bindParam(':date_end', $row['date_end'], PDO::PARAM_INT);
                $stmt->bindParam(':tourid', $tourid, PDO::PARAM_STR);
                $stmt->bindParam(':quantity', $row['quantity'], PDO::PARAM_INT);
                $exc = $stmt->execute();
                $insert_id = $row['id'];
            }
            
            if ($insert_id > 0) {
                $nv_Cache->delMod($module_name);
                Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=coupons');
                die();
            }
        } catch (PDOException $e) {
            trigger_error($e->getMessage());
            die($e->getMessage());
            // Remove this line after checks finished
        }
    }
} elseif ($row['id'] > 0) {
    $row = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_coupons WHERE id=' . $row['id'])->fetch();
    if (empty($row)) {
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=coupons');
        die();
    }
} else {
    $row['id'] = 0;
    $row['title'] = '';
    $row['code'] = '';
    $row['type'] = 'p';
    $row['discount'] = '';
    $row['date_start'] = NV_CURRENTTIME;
    $row['date_end'] = 0;
    $row['tourid'] = '';
    $row['quantity'] = '';
}

if (empty($row['date_start'])) {
    $row['date_start'] = '';
} else {
    $row['date_start'] = date('d/m/Y', $row['date_start']);
}

if (empty($row['date_end'])) {
    $row['date_end'] = '';
} else {
    $row['date_end'] = date('d/m/Y', $row['date_end']);
}

$row['quantity'] = ! empty($row['quantity']) ? $row['quantity'] : '';
$row['tourid'] = !empty($row['tourid']) ? unserialize($row['tourid']) : array();

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);

if (! empty($error)) {
    $xtpl->assign('ERROR', implode('<br />', $error));
    $xtpl->parse('main.error');
}

$array_select_type = array(
    'p' => $lang_module['coupons_type_percentage'],
    'f' => $lang_module['coupons_type_fixed_amount']
);
foreach ($array_select_type as $key => $title) {
    $xtpl->assign('OPTION', array(
        'key' => $key,
        'title' => $title,
        'selected' => ($key == $row['type']) ? ' selected="selected"' : ''
    ));
    $xtpl->parse('main.select_type');
}

if (! empty($row['tourid'])) {
    $array_tours = array();
    $result = $db->query('SELECT id, ' . NV_LANG_DATA . '_title FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows WHERE id IN (' . implode(',', $row['tourid']) . ')');
    while (list ($id, $title) = $result->fetch(3)) {
        $array_tours[$id] = array(
            'id' => $id,
            'title' => $title
        );
    }
    
    if(!empty($array_tours)){
        foreach ($row['tourid'] as $pid) {
            $xtpl->assign('TOUR', array(
                'id' => $pid,
                'title' => $array_tours[$pid]['title']
            ));
            $xtpl->parse('main.tours');
        }   
    }
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['coupons'];
$set_active_op = 'coupons';

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
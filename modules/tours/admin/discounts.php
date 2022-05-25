<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2016 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 09 May 2016 03:12:44 GMT
 */
if (! defined('NV_IS_FILE_ADMIN'))
    die('Stop!!!');
    
    // change status
if ($nv_Request->isset_request('change_status', 'post, get')) {
    $did = $nv_Request->get_int('did', 'post, get', 0);
    $content = 'NO_' . $did;
    
    $query = 'SELECT status FROM ' . $db_config['prefix'] . '_' . $module_data . '_discounts WHERE did=' . $did;
    $row = $db->query($query)->fetch();
    if (isset($row['status'])) {
        $status = ($row['status']) ? 0 : 1;
        $query = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_discounts SET status=' . intval($status) . ' WHERE did=' . $did;
        $db->query($query);
        $content = 'OK_' . $did;
    }
    $nv_Cache->delMod($module_name);
    include NV_ROOTDIR . '/includes/header.php';
    echo $content;
    include NV_ROOTDIR . '/includes/footer.php';
    exit();
}

if ($nv_Request->isset_request('delete_did', 'get') and $nv_Request->isset_request('delete_checkss', 'get')) {
    $did = $nv_Request->get_int('delete_did', 'get');
    $delete_checkss = $nv_Request->get_string('delete_checkss', 'get');
    if ($did > 0 and $delete_checkss == md5($did . NV_CACHE_PREFIX . $client_info['session_id'])) {
        $db->query('DELETE FROM ' . $db_config['prefix'] . '_' . $module_data . '_discounts  WHERE did = ' . $db->quote($did));
        $nv_Cache->delMod($module_name);
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        die();
    }
}

$row = array();
$error = array();
$row['did'] = $nv_Request->get_int('did', 'post,get', 0);
if ($nv_Request->isset_request('submit', 'post')) {
    $row['title'] = $nv_Request->get_title('title', 'post', '');
    $row['percent'] = $nv_Request->get_int('percent', 'post', 0);
    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $nv_Request->get_string('begin_time', 'post'), $m)) {
        $_hour = 0;
        $_min = 0;
        $row['begin_time'] = mktime($_hour, $_min, 0, $m[2], $m[1], $m[3]);
    } else {
        $row['begin_time'] = 0;
    }
    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $nv_Request->get_string('end_time', 'post'), $m)) {
        $_hour = 0;
        $_min = 0;
        $row['end_time'] = mktime($_hour, $_min, 0, $m[2], $m[1], $m[3]);
    } else {
        $row['end_time'] = 0;
    }
    $row['status'] = $nv_Request->get_int('status', 'post', 0);
    
    if (empty($row['title'])) {
        $error[] = $lang_module['error_required_title'];
    } elseif (empty($row['percent'])) {
        $error[] = $lang_module['error_required_percent'];
    } elseif ($row['percent'] <= 0) {
        $error[] = $lang_module['error_vaild_percent'];
    } elseif (empty($row['begin_time'])) {
        $error[] = $lang_module['error_required_begin_time'];
    }
    
    if (empty($error)) {
        try {
            if (empty($row['did'])) {
                $stmt = $db->prepare('INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_discounts (title, percent, begin_time, end_time) VALUES (:title, :percent, :begin_time, :end_time)');
            } else {
                $stmt = $db->prepare('UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_discounts SET title = :title, percent = :percent, begin_time = :begin_time, end_time = :end_time WHERE did=' . $row['did']);
            }
            $stmt->bindParam(':title', $row['title'], PDO::PARAM_STR);
            $stmt->bindParam(':percent', $row['percent'], PDO::PARAM_STR);
            $stmt->bindParam(':begin_time', $row['begin_time'], PDO::PARAM_INT);
            $stmt->bindParam(':end_time', $row['end_time'], PDO::PARAM_INT);
            
            $exc = $stmt->execute();
            if ($exc) {
                $nv_Cache->delMod($module_name);
                Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
                die();
            }
        } catch (PDOException $e) {
            trigger_error($e->getMessage());
            die($e->getMessage()); // Remove this line after checks finished
        }
    }
} elseif ($row['did'] > 0) {
    $row = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_discounts WHERE did=' . $row['did'])->fetch();
    if (empty($row)) {
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        die();
    }
} else {
    $row['did'] = 0;
    $row['title'] = '';
    $row['percent'] = '';
    $row['begin_time'] = 0;
    $row['end_time'] = 0;
}

if (empty($row['begin_time'])) {
    $row['begin_time'] = '';
} else {
    $row['begin_time'] = date('d/m/Y', $row['begin_time']);
}

if (empty($row['end_time'])) {
    $row['end_time'] = '';
} else {
    $row['end_time'] = date('d/m/Y', $row['end_time']);
}

$q = $nv_Request->get_title('q', 'post,get');

// Fetch Limit
$show_view = false;
if (! $nv_Request->isset_request('id', 'post,get')) {
    $show_view = true;
    $per_page = 20;
    $page = $nv_Request->get_int('page', 'post,get', 1);
    $db->sqlreset()
        ->select('COUNT(*)')
        ->from('' . $db_config['prefix'] . '_' . $module_data . '_discounts');
    
    if (! empty($q)) {
        $db->where('title LIKE :q_title');
    }
    $sth = $db->prepare($db->sql());
    
    if (! empty($q)) {
        $sth->bindValue(':q_title', '%' . $q . '%');
    }
    $sth->execute();
    $num_items = $sth->fetchColumn();
    
    $db->select('*')
        ->order('did DESC')
        ->limit($per_page)
        ->offset(($page - 1) * $per_page);
    $sth = $db->prepare($db->sql());
    
    if (! empty($q)) {
        $sth->bindValue(':q_title', '%' . $q . '%');
    }
    $sth->execute();
}

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('MODULE_UPLOAD', $module_upload);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);
$xtpl->assign('Q', $q);

if ($show_view) {
    $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
    if (! empty($q)) {
        $base_url .= '&q=' . $q;
    }
    $generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
    if (! empty($generate_page)) {
        $xtpl->assign('NV_GENERATE_PAGE', $generate_page);
        $xtpl->parse('main.view.generate_page');
    }
    $number = $page > 1 ? ($per_page * ($page - 1)) + 1 : 1;
    while ($view = $sth->fetch()) {
        $view['number'] = $number ++;
        $xtpl->assign('CHECK', $view['status'] == 1 ? 'checked' : '');
        $view['begin_time'] = (empty($view['begin_time'])) ? '' : nv_date('d/m/Y', $view['begin_time']);
        $view['end_time'] = (empty($view['end_time'])) ? '' : nv_date('d/m/Y', $view['end_time']);
        $view['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;did=' . $view['did'];
        $view['link_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;delete_did=' . $view['did'] . '&amp;delete_checkss=' . md5($view['did'] . NV_CACHE_PREFIX . $client_info['session_id']);
        $xtpl->assign('VIEW', $view);
        $xtpl->parse('main.view.loop');
    }
    $xtpl->parse('main.view');
}

if (! empty($error)) {
    $xtpl->assign('ERROR', implode('<br />', $error));
    $xtpl->parse('main.error');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['discounts'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
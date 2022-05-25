<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2016 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 09 May 2016 04:03:03 GMT
 */
if (! defined('NV_IS_FILE_ADMIN'))
    die('Stop!!!');

$table_name = $db_config['prefix'] . '_' . $module_data . '_guides';

$row = array();
$error = array();
$row['id'] = $nv_Request->get_int('id', 'post,get', 0);
if ($nv_Request->isset_request('submit', 'post')) {
    $row['first_name'] = $nv_Request->get_title('first_name', 'post', '');
    $row['last_name'] = $nv_Request->get_title('last_name', 'post', '');
    $row['description'] = $nv_Request->get_editor('description', '', NV_ALLOWED_HTML_TAGS);
    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $nv_Request->get_string('birthday', 'post'), $m)) {
        $_hour = 0;
        $_min = 0;
        $row['birthday'] = mktime($_hour, $_min, 0, $m[2], $m[1], $m[3]);
    } else {
        $row['birthday'] = 0;
    }
    $row['address'] = $nv_Request->get_title('address', 'post', '');
    $row['gender'] = $nv_Request->get_int('gender', 'post', 0);
    $row['phone'] = $nv_Request->get_title('phone', 'post', '');
    $row['image'] = $nv_Request->get_title('image', 'post', '');
    if (is_file(NV_DOCUMENT_ROOT . $row['image'])) {
        $row['image'] = substr($row['image'], strlen(NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/images/'));
    } else {
        $row['image'] = '';
    }
    $row['status'] = $nv_Request->get_int('status', 'post', 0);
    
    if (empty($row['first_name'])) {
        $error[] = $lang_module['error_required_first_name'];
    } elseif (empty($row['last_name'])) {
        $error[] = $lang_module['error_required_last_name'];
    } elseif (empty($row['phone'])) {
        $error[] = $lang_module['error_required_phone'];
    }
    
    if (empty($error)) {
        try {
            if (empty($row['id'])) {
                $stmt = $db->prepare('INSERT INTO ' . $table_name . ' (first_name, last_name, description, birthday, address, gender, phone, image) VALUES (:first_name, :last_name, :description, :birthday, :address, :gender, :phone, :image)');
            } else {
                $stmt = $db->prepare('UPDATE ' . $table_name . ' SET first_name = :first_name, last_name = :last_name, description = :description, birthday = :birthday, address = :address, gender = :gender, phone = :phone, image = :image WHERE id=' . $row['id']);
            }
            $stmt->bindParam(':first_name', $row['first_name'], PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $row['last_name'], PDO::PARAM_STR);
            $stmt->bindParam(':description', $row['description'], PDO::PARAM_STR, strlen($row['description']));
            $stmt->bindParam(':birthday', $row['birthday'], PDO::PARAM_INT);
            $stmt->bindParam(':address', $row['address'], PDO::PARAM_STR);
            $stmt->bindParam(':gender', $row['gender'], PDO::PARAM_INT);
            $stmt->bindParam(':phone', $row['phone'], PDO::PARAM_STR);
            $stmt->bindParam(':image', $row['image'], PDO::PARAM_STR);
            
            $exc = $stmt->execute();
            if ($exc) {
                $nv_Cache->delMod($module_name);
                Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=guides');
                die();
            }
        } catch (PDOException $e) {
            trigger_error($e->getMessage());
            die($e->getMessage()); // Remove this line after checks finished
        }
    }
} elseif ($row['id'] > 0) {
    $row = $db->query('SELECT * FROM ' . $table_name . ' WHERE id=' . $row['id'])->fetch();
    if (empty($row)) {
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        die();
    }
    
    $lang_module['guide-content'] = $lang_module['guide_edit'];
} else {
    $row['id'] = 0;
    $row['first_name'] = '';
    $row['last_name'] = '';
    $row['description'] = '';
    $row['birthday'] = 0;
    $row['address'] = '';
    $row['gender'] = 1;
    $row['phone'] = '';
    $row['image'] = '';
    $row['status'] = 1;
    
    $lang_module['guide-content'] = $lang_module['guide_add'];
}

if (empty($row['birthday'])) {
    $row['birthday'] = '';
} else {
    $row['birthday'] = date('d/m/Y', $row['birthday']);
}
if (! empty($row['image']) and is_file(NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/images/' . $row['image'])) {
    $row['image'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/images/' . $row['image'];
}

if (defined('NV_EDITOR'))
    require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
$row['description'] = htmlspecialchars(nv_editor_br2nl($row['description']));
if (defined('NV_EDITOR') and nv_function_exists('nv_aleditor')) {
    $row['description'] = nv_aleditor('description', '100%', '300px', $row['description']);
} else {
    $row['description'] = '<textarea style="width:100%;height:300px" name="description">' . $row['description'] . '</textarea>';
}

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('MODULE_UPLOAD', $module_upload);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);

$array_gender = array(
    '1' => $lang_module['gender_1'],
    '0' => $lang_module['gender_0']
);
foreach ($array_gender as $key => $value) {
    $ck = $key == $row['gender'] ? 'checked="checked"' : '';
    $xtpl->assign('GENDER', array(
        'key' => $key,
        'value' => $value,
        'checked' => $ck
    ));
    $xtpl->parse('main.gender');
}

if (! empty($error)) {
    $xtpl->assign('ERROR', implode('<br />', $error));
    $xtpl->parse('main.error');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['guide-content'];
$set_active_op = 'guides';

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
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

$table_name = $db_config['prefix'] . '_' . $module_data . '_hotels';

$row = array();
$error = array();
$row['id'] = $nv_Request->get_int('id', 'post,get', 0);
if ($nv_Request->isset_request('submit', 'post')) {
    $row['title'] = $nv_Request->get_title('title', 'post', '');
    $row['description'] = $nv_Request->get_editor('description', '', NV_ALLOWED_HTML_TAGS);
    $row['address'] = $nv_Request->get_title('address', 'post', '');
    $row['website'] = $nv_Request->get_title('website', 'post', '');
    $row['phone'] = $nv_Request->get_title('phone', 'post', '');
    $row['star'] = $nv_Request->get_title('star', 'post', 0);
    $row['image'] = $nv_Request->get_title('image', 'post', '');
    if (is_file(NV_DOCUMENT_ROOT . $row['image'])) {
        $row['image'] = substr($row['image'], strlen(NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/images/'));
    } else {
        $row['image'] = '';
    }
    
    if (empty($row['title'])) {
        $error[] = $lang_module['error_required_title'];
    }
    
    if (empty($error)) {
        $field_lang = nv_file_table($table_name);
        $listfield = $listvalue = '';
        foreach ($field_lang as $field_lang_i) {
            list ($flang, $fname) = $field_lang_i;
            $listfield .= ', ' . $flang . '_' . $fname;
            $listvalue .= ', :' . $fname;
        }
        try {
            if (empty($row['id'])) {
                $stmt = $db->prepare('INSERT INTO ' . $table_name . ' (phone, website, image, star' . $listfield . ') VALUES (:phone, :website, :image, :star' . $listvalue . ')');
            } else {
                $stmt = $db->prepare('UPDATE ' . $table_name . ' SET phone = :phone, website = :website, image = :image, star = :star, ' . NV_LANG_DATA . '_title = :title, ' . NV_LANG_DATA . '_description = :description, ' . NV_LANG_DATA . '_address = :address WHERE id=' . $row['id']);
            }
            $stmt->bindParam(':phone', $row['phone'], PDO::PARAM_STR);
            $stmt->bindParam(':website', $row['website'], PDO::PARAM_STR);
            $stmt->bindParam(':image', $row['image'], PDO::PARAM_STR);
            $stmt->bindParam(':star', $row['star'], PDO::PARAM_STR);
            foreach ($field_lang as $field_lang_i) {
                list ($flang, $fname) = $field_lang_i;
                $stmt->bindParam(':' . $fname, $row[$fname], PDO::PARAM_STR);
            }
            $exc = $stmt->execute();
            if ($exc) {
                $nv_Cache->delMod($module_name);
                Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=hotels');
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
    
    $row['title'] = $row[NV_LANG_DATA . '_title'];
    $row['address'] = $row[NV_LANG_DATA . '_address'];
    $row['description'] = $row[NV_LANG_DATA . '_description'];
    
    $lang_module['hotels-content'] = $lang_module['hotels_edit'];
} else {
    $row['id'] = 0;
    $row['title'] = '';
    $row['description'] = '';
    $row['address'] = '';
    $row['website'] = '';
    $row['phone'] = '';
    $row['image'] = '';
    $row['star'] = '';
    
    $lang_module['hotels-content'] = $lang_module['hotels_add'];
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
    $row['description'] = '<textarea style="width:100%;height:300px" name="description">' . $row[NV_LANG_DATA . '_description'] . '</textarea>';
}

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('MODULE_UPLOAD', $module_upload);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);

$array_star = array(
    0 => $lang_module['hotels_type_0'],
    1 => $lang_module['hotels_type_1'],
    2 => $lang_module['hotels_type_2'],
    3 => $lang_module['hotels_type_3'],
    4 => $lang_module['hotels_type_4'],
    5 => $lang_module['hotels_type_5'],
);
foreach($array_star as $index => $value){
    $ck = $index == $row['star'] ? 'checked="checked"' : '';
    $xtpl->assign('STAR', array('index' => $index, 'value' => $value, 'checked' => $ck));
    $xtpl->parse('main.star');
}

if (! empty($error)) {
    $xtpl->assign('ERROR', implode('<br />', $error));
    $xtpl->parse('main.error');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['hotels-content'];
$set_active_op = 'hotels';

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
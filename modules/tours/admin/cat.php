<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2015 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Tue, 02 Jun 2015 07:53:31 GMT
 */
if (! defined('NV_IS_FILE_ADMIN'))
    die('Stop!!!');

$table_name = $db_config['prefix'] . '_' . $module_data . '_cat';
$currentpath = NV_UPLOADS_DIR . '/' . $module_upload;
$groups_list = nv_groups_list();

if ($nv_Request->isset_request('get_alias_title', 'post')) {
    $alias = $nv_Request->get_title('get_alias_title', 'post', '');
    $alias = change_alias($alias);
    die($alias);
}

// change status
if ($nv_Request->isset_request('change_status', 'post, get')) {
    $id = $nv_Request->get_int('id', 'post, get', 0);
    $content = 'NO_' . $id;
    
    $query = 'SELECT status FROM ' . $table_name . ' WHERE id=' . $id;
    $row = $db->query($query)->fetch();
    if (isset($row['status'])) {
        $status = ($row['status']) ? 0 : 1;
        $query = 'UPDATE ' . $table_name . ' SET status=' . intval($status) . ' WHERE id=' . $id;
        $db->query($query);
        $content = 'OK_' . $id;
    }
    $nv_Cache->delMod($module_name);
    include NV_ROOTDIR . '/includes/header.php';
    echo $content;
    include NV_ROOTDIR . '/includes/footer.php';
    exit();
}

if ($nv_Request->isset_request('ajax_action', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $new_vid = $nv_Request->get_int('new_vid', 'post', 0);
    $content = 'NO_' . $id;
    
    list ($id, $parentid) = $db->query('SELECT id, parentid FROM ' . $table_name . ' WHERE id=' . $id)->fetch(3);
    
    if ($new_vid > 0) {
        $sql = 'SELECT id FROM ' . $table_name . ' WHERE id!=' . $id . ' AND parentid=' . $parentid . ' ORDER BY weight ASC';
        $result = $db->query($sql);
        $weight = 0;
        while ($row = $result->fetch()) {
            ++ $weight;
            if ($weight == $new_vid)
                ++ $weight;
            $sql = 'UPDATE ' . $table_name . ' SET weight=' . $weight . ' WHERE id=' . $row['id'];
            $db->query($sql);
        }
        $sql = 'UPDATE ' . $table_name . ' SET weight=' . $new_vid . ' WHERE id=' . $id;
        $db->query($sql);
        $content = 'OK_' . $id;
    }
    $nv_Cache->delMod($module_name);
    include NV_ROOTDIR . '/includes/header.php';
    echo $content;
    include NV_ROOTDIR . '/includes/footer.php';
    exit();
}

if ($nv_Request->isset_request('delete_id', 'get') and $nv_Request->isset_request('delete_checkss', 'get')) {
    $id = $nv_Request->get_int('delete_id', 'get');
    $delete_checkss = $nv_Request->get_string('delete_checkss', 'get');
    if ($id > 0 and $delete_checkss == md5($id . NV_CACHE_PREFIX . $client_info['session_id'])) {
        list ($id, $parentid) = $db->query('SELECT id, parentid FROM ' . $table_name . ' WHERE id=' . $id)->fetch(3);
        
        $weight = 0;
        $sql = 'SELECT weight FROM ' . $table_name . ' WHERE id =' . $db->quote($id);
        $result = $db->query($sql);
        list ($weight) = $result->fetch(3);
        
        $db->query('DELETE FROM ' . $table_name . '  WHERE id = ' . $db->quote($id) . ' OR parentid=' . $id);
        if ($weight > 0) {
            $sql = 'SELECT id, weight FROM ' . $table_name . ' WHERE weight >' . $weight;
            $result = $db->query($sql);
            while (list ($id, $weight) = $result->fetch(3)) {
                $weight --;
                $db->query('UPDATE ' . $table_name . ' SET weight=' . $weight . ' WHERE id=' . intval($id));
            }
        }
        
        nv_fix_order($table_name);
        
        // xoa tour
        $result = $db->query('SELECT id FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows WHERE catid=' . $id);
        while(list($rows_id) = $result->fetch(3)){
            nv_tour_delete($rows_id);
        }
        
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&parentid=' . $parentid);
        die();
    }
}

$row = array();
$error = array();
$row['id'] = $nv_Request->get_int('id', 'post,get', 0);
$row['parentid'] = $nv_Request->get_int('parentid', 'get,post', 0);

if ($row['id'] > 0) {
    $lang_module['cat_add'] = $lang_module['cat_edit'];
    $row = $db->query('SELECT * FROM ' . $table_name . ' WHERE id=' . $row['id'])->fetch();
    if (empty($row)) {
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        die();
    }
    $row['title'] = $row[NV_LANG_DATA . '_title'];
    $row['custom_title'] = $row[NV_LANG_DATA . '_custom_title'];
    $row['alias'] = $row[NV_LANG_DATA . '_alias'];
    $row['description'] = $row[NV_LANG_DATA . '_description'];
    $row['description_html'] = $row[NV_LANG_DATA . '_description_html'];
    $row['keywords'] = $row[NV_LANG_DATA . '_keywords'];

    if (! empty($row['image']) and file_exists(NV_UPLOADS_REAL_DIR)) {
        $currentpath = NV_UPLOADS_DIR . '/' . $module_upload . '/' . dirname($row['image']);
    }
} else {
    $row['id'] = 0;
    $row['price_method'] = 1;
    $row['title'] = '';
    $row['custom_title'] = '';
    $row['alias'] = '';
    $row['keywords'] = '';
    $row['description'] = '';
    $row['description_html'] = '';
    $row['price_method'] = 1;
    $row['subprice'] = '';
    $row['groups_view'] = '6';
    $row['price_method'] = '1';
    $row['price_method_auto'] = '0';
    $row['price_method_config'] = '';

    if ($row['parentid'] > 0) {
        $row['price_method'] = $array_cat[$row['parentid']]['price_method'];
        $row['subprice'] = $array_cat[$row['parentid']]['subprice'];
        $row['price_method_auto'] = $array_cat[$row['parentid']]['price_method_auto'];
        $row['price_method_config'] = $array_cat[$row['parentid']]['price_method_config'];
    }
}

if ($nv_Request->isset_request('submit', 'post')) {
    $row['parentid'] = $nv_Request->get_int('parentid', 'post', 0);
    $data['parentid_old'] = $nv_Request->get_int('parentid_old', 'post', 0);
    $row['title'] = $nv_Request->get_title('title', 'post', '');
    $row['custom_title'] = $nv_Request->get_title('custom_title', 'post', '');
    $row['keywords'] = $nv_Request->get_title('keywords', 'post', '');

    // xu ly alias
    $row['alias'] = $nv_Request->get_title('alias', 'post', '', 1);
    $row['alias'] = empty($row['alias']) ? change_alias($row['title']) : $row['alias'];
    $stmt = $db->prepare('SELECT COUNT(*) FROM ' . $table_name . ' WHERE id !=' . $row['id'] . ' AND ' . NV_LANG_DATA . '_alias = :alias');
    $stmt->bindParam(':alias', $row['alias'], PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->fetchColumn()) {
        $weight = $db->query('SELECT MAX(id) FROM ' . $table_name)->fetchColumn();
        $weight = intval($weight) + 1;
        $row['alias'] = $row['alias'] . '-' . $weight;
    }
    
    $row['description'] = $nv_Request->get_string('description', 'post', '');
    $row['description_html'] = $nv_Request->get_editor('description_html', '', NV_ALLOWED_HTML_TAGS);
    $row['image'] = $nv_Request->get_title('image', 'post', '');
    if (is_file(NV_DOCUMENT_ROOT . $row['image'])) {
        $row['image'] = substr($row['image'], strlen(NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/'));
    } else {
        $row['image'] = '';
    }
    $row['price_method'] = $nv_Request->get_int('price_method', 'post', 1);
    $row['price_method_auto'] = $nv_Request->get_int('cat_price_method_auto', 'post', 0);
    if ($row['price_method_auto']) {
        $row['price_method_config'] = $nv_Request->get_array('price_method_config', 'post', '');
        if (! empty($row['price_method_config'])) {
            $row['price_method_config'] = serialize($row['price_method_config']);
        }
    }
    $row['subprice'] = $nv_Request->get_typed_array('subprice', 'post', 'int');
    $row['subprice'] = serialize($row['subprice']);
    
    $_groups_post = $nv_Request->get_array('groups_view', 'post', array());
    $row['groups_view'] = ! empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';
    
    if (empty($row['title'])) {
        $error[] = $lang_module['error_required_title'];
    }
    
    if (empty($error)) {
        $field_lang = nv_file_table($table_name);
        $listfield = $listvalue = '';
        foreach ($field_lang as $field_lang_i) {
            list ($flang, $fname) = $field_lang_i;
            $listfield .= ', ' . $flang . '_' . $fname;
            $listvalue .= ', :' . $flang . '_' . $fname;
        }
        
        try {
            if (empty($row['id'])) {
                $data_insert = array();
                $_sql = 'INSERT INTO ' . $table_name . ' (parentid, groups_view, image, price_method, price_method_auto, price_method_config, subprice, weight' . $listfield . ') VALUES (:parentid, :groups_view, :image, :price_method, :price_method_auto, :price_method_config, :subprice, :weight' . $listvalue . ')';
                $weight = $db->query('SELECT max(weight) FROM ' . $table_name . '')->fetchColumn();
                $weight = intval($weight) + 1;
                
                $data_insert['parentid'] = $row['parentid'];
                $data_insert['groups_view'] = $row['groups_view'];
                $data_insert['image'] = $row['image'];
                $data_insert['price_method'] = $row['price_method'];
                $data_insert['price_method_auto'] = $row['price_method_auto'];
                $data_insert['price_method_config'] = $row['price_method_config'];
                $data_insert['subprice'] = $row['subprice'];
                $data_insert['weight'] = $weight;
                foreach ($field_lang as $field_lang_i) {
                    list ($flang, $fname) = $field_lang_i;
                    $data_insert[$flang . '_' . $fname] = $row[$fname];
                }
                $exc = $new_id = $db->insert_id($_sql, 'id', $data_insert);
            } else {
                $stmt = $db->prepare('UPDATE ' . $table_name . ' SET parentid=:parentid, groups_view = :groups_view, image = :image, price_method = :price_method, price_method_auto = :price_method_auto, price_method_config = :price_method_config, subprice = :subprice, ' . NV_LANG_DATA . '_title = :title, ' . NV_LANG_DATA . '_alias = :alias, ' . NV_LANG_DATA . '_custom_title = :custom_title, ' . NV_LANG_DATA . '_keywords = :keywords, ' . NV_LANG_DATA . '_description = :description, ' . NV_LANG_DATA . '_description_html = :description_html WHERE id=' . $row['id']);
                $stmt->bindParam(':parentid', $row['parentid'], PDO::PARAM_INT);
                $stmt->bindParam(':groups_view', $row['groups_view'], PDO::PARAM_STR);
                $stmt->bindParam(':image', $row['image'], PDO::PARAM_STR);
                $stmt->bindParam(':price_method', $row['price_method'], PDO::PARAM_INT);
                $stmt->bindParam(':price_method_auto', $row['price_method_auto'], PDO::PARAM_INT);
                $stmt->bindParam(':price_method_config', $row['price_method_config'], PDO::PARAM_STR);
                $stmt->bindParam(':subprice', $row['subprice'], PDO::PARAM_STR);
                $stmt->bindParam(':title', $row['title'], PDO::PARAM_STR);
                $stmt->bindParam(':alias', $row['alias'], PDO::PARAM_STR);
                $stmt->bindParam(':custom_title', $row['custom_title'], PDO::PARAM_STR);
                $stmt->bindParam(':keywords', $row['keywords'], PDO::PARAM_STR);
                $stmt->bindParam(':description', $row['description'], PDO::PARAM_STR, strlen($row['description']));
                $stmt->bindParam(':description_html', $row['description_html'], PDO::PARAM_STR, strlen($row['description_html']));
                $exc = $stmt->execute();
                $new_id = $row['id'];
            }
            
            if ($exc) {
                nv_fix_order($table_name);
                $nv_Cache->delMod($module_name);
                Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&parentid=' . $row['parentid']);
                die();
            }
        } catch (PDOException $e) {
            trigger_error($e->getMessage());
            die($e->getMessage()); // Remove this line after checks finished
        }
    }
}

// Fetch Limit
$show_view = false;
if (! $nv_Request->isset_request('id', 'post,get')) {
    $show_view = true;
    $per_page = 20;
    $page = $nv_Request->get_int('page', 'post,get', 1);
    $db->sqlreset()
        ->select('COUNT(*)')
        ->from($table_name)
        ->where('parentid=' . $row['parentid']);
    
    $sth = $db->prepare($db->sql());
    
    $sth->execute();
    $num_items = $sth->fetchColumn();
    
    $db->select('*')
        ->order('weight ASC')
        ->limit($per_page)
        ->offset(($page - 1) * $per_page);
    $sth = $db->prepare($db->sql());
    $sth->execute();
}

$sql = 'SELECT id, ' . NV_LANG_DATA . '_title, lev FROM ' . $table_name . ' WHERE id !=' . $row['id'] . ' AND status=1 ORDER BY sort ASC';
$result = $db->query($sql);
$array_cat_list = array();
$array_cat_list[0] = array(
    '0',
    $lang_module['cat_main']
);

while (list ($id_i, $title_i, $lev_i) = $result->fetch(3)) {
    $xtitle_i = '';
    if ($lev_i > 0) {
        $xtitle_i .= '&nbsp;';
        for ($i = 1; $i <= $lev_i; $i ++) {
            $xtitle_i .= '---';
        }
    }
    $xtitle_i .= $title_i;
    $array_cat_list[] = array(
        $id_i,
        $xtitle_i
    );
}

$row['description_html'] = htmlspecialchars(nv_editor_br2nl($row['description_html']));
if (! empty($row['image']) and is_file(NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $row['image'])) {
    $row['image'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $row['image'];
}

if (defined('NV_EDITOR')) {
    require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
}

if (defined('NV_EDITOR') and nv_function_exists('nv_aleditor')) {
    $row['description_html'] = nv_aleditor('description_html', '100%', '200px', $row['description_html'], 'Basic');
} else {
    $row['description_html'] = "<textarea style=\"width: 100%\" name=\"description_html\" id=\"description_html\" cols=\"20\" rows=\"15\">" . $row['description_html'] . "</textarea>";
}

$_sql = 'SELECT id, ' . NV_LANG_DATA . '_title title FROM ' . $db_config['prefix'] . '_' . $module_data . '_subprice WHERE status=1 ORDER BY weight';
$array_subprice = $nv_Cache->db($_sql, '', $module_name);

$row['ck_price_method_auto'] = $row['price_method_auto'] ? 'checked="checked"' : '';
$row['style_price_method'] = !in_array($row['price_method'], array(1,2)) ? 'style="display: none"' : '';
$row['style_price_method_auto'] = !$row['price_method_auto'] ? 'style="display: none"' : '';

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);
$xtpl->assign('MODULE_UPLOAD', $module_upload);
$xtpl->assign('CURENTPATH', $currentpath);

$array_inhome = array(
    '1' => $lang_module['cat_inhome_1'],
    '0' => $lang_module['cat_inhome_0']
);

$array_gettype = array(
    'getall' => $lang_module['cat_gettype_all'],
    'getcat' => $lang_module['cat_gettype_cat']
);

$array_viewtype = array(
    'viewgrid' => $lang_module['cat_viewtype_grid'],
    'viewlist' => $lang_module['cat_viewtype_list']
);

if ($show_view) {
    $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
    $generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
    if (! empty($generate_page)) {
        $xtpl->assign('NV_GENERATE_PAGE', $generate_page);
        $xtpl->parse('main.view.generate_page');
    }
    while ($view = $sth->fetch()) {
        $view['title'] = $view[NV_LANG_DATA . '_title'];
        for ($i = 1; $i <= $num_items; ++ $i) {
            $xtpl->assign('WEIGHT', array(
                'key' => $i,
                'title' => $i,
                'selected' => ($i == $view['weight']) ? ' selected="selected"' : ''
            ));
            $xtpl->parse('main.view.loop.weight_loop');
        }
        if ($view['status'] == 1) {
            $check = 'checked';
        } else {
            $check = '';
        }
        $xtpl->assign('CHECK', $check);
        
        foreach ($array_inhome as $key => $value) {
            $sl = $view['inhome'] == $key ? 'selected="selected"' : '';
            $xtpl->assign('INHOME', array(
                'key' => $key,
                'value' => $value,
                'selected' => $sl
            ));
            $xtpl->parse('main.view.loop.inhome');
        }

        foreach ($array_gettype as $key => $value) {
            $sl = $view['gettype'] == $key ? 'selected="selected"' : '';
            $xtpl->assign('GETTYPE', array(
                'key' => $key,
                'value' => $value,
                'selected' => $sl
            ));
            $xtpl->parse('main.view.loop.gettype');
        }
        
        foreach ($array_viewtype as $key => $value) {
            $sl = $view['viewtype'] == $key ? 'selected="selected"' : '';
            $xtpl->assign('VIEWTYPE', array(
                'key' => $key,
                'value' => $value,
                'selected' => $sl
            ));
            $xtpl->parse('main.view.loop.viewtype');
        }
        
        for ($i = 0; $i <= 20; ++ $i) {
            $xtpl->assign('NUMLINKS', array(
                'key' => $i,
                'title' => $i,
                'selected' => $i == $view['numlinks'] ? ' selected="selected"' : ''
            ));
            $xtpl->parse('main.view.loop.numlinks');
        }
        
        $view['link_view'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;parentid=' . $view['id'];
        $view['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;id=' . $view['id'];
        $view['link_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;delete_id=' . $view['id'] . '&amp;delete_checkss=' . md5($view['id'] . NV_CACHE_PREFIX . $client_info['session_id']);
        $xtpl->assign('VIEW', $view);
        $xtpl->parse('main.view.loop');
    }
    $xtpl->parse('main.view');
}

foreach ($array_cat_list as $rows_i) {
    $sl = ($rows_i[0] == $row['parentid']) ? ' selected="selected"' : '';
    $xtpl->assign('pid', $rows_i[0]);
    $xtpl->assign('ptitle', $rows_i[1]);
    $xtpl->assign('pselect', $sl);
    $xtpl->parse('main.parent_loop');
}

$array_price_method = array(
    '0' => $lang_module['cat_price_method_0'],
    '1' => $lang_module['cat_price_method_1'],
    '2' => $lang_module['cat_price_method_2']
);
foreach ($array_price_method as $index => $value) {
    $ck = $index == $row['price_method'] ? 'checked="checked"' : '';
    $xtpl->assign('PRICE_METHOD', array(
        'index' => $index,
        'value' => $value,
        'checked' => $ck
    ));
    $xtpl->parse('main.price_method');
}

$row['subprice'] = ! empty($row['subprice']) ? unserialize($row['subprice']) : array();

if (! empty($array_subprice)) {
    foreach ($array_subprice as $subprice) {
        $subprice['checked'] = in_array($subprice['id'], $row['subprice']) ? 'checked="checked"' : '';
        $xtpl->assign('SUBPRICE', $subprice);
        $xtpl->parse('main.subprice.loop');
    }
    $xtpl->parse('main.subprice');
}

$groups_view = explode(',', $row['groups_view']);
foreach ($groups_list as $group_id => $grtl) {
    $_groups_view = array(
        'value' => $group_id,
        'checked' => in_array($group_id, $groups_view) ? ' checked="checked"' : '',
        'title' => $grtl
    );
    $xtpl->assign('GROUPS_VIEW', $_groups_view);
    $xtpl->parse('main.groups_view');
}

$row['price_method_config'] = !empty($row['price_method_config']) ? unserialize($row['price_method_config']) : array();
$array_config['age_config'] = !empty($array_config['age_config']) ? unserialize($array_config['age_config']) : array();
foreach($array_config['age_config'] as $index => $value){
    if(!$value['price_base']){
        $_value = isset($row['price_method_config'][$index]) ? $row['price_method_config'][$index] : '';
        $note = $value['from'] . ' ' . $lang_module['to'] . ' ' . $value['to'] . ' ' . $lang_module['age'];
        $xtpl->assign('AGE', array(
            'index' => $index,
            'value' => $_value,
            'title' => $value['name'],
            'note' => $note
        ));
        $xtpl->parse('main.age');
    }
}

if (empty($row['id'])) {
    $xtpl->parse('main.auto_get_alias');
}

if (! empty($error)) {
    $xtpl->assign('ERROR', implode('<br />', $error));
    $xtpl->parse('main.error');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['cat'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
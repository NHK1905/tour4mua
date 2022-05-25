<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3-6-2010 0:14
 */
if (! defined('NV_IS_MOD_TOURS'))
    die('Stop!!!');

$alias = $nv_Request->get_title('alias', 'get');
$array_op = explode('/', $alias);
$alias = $array_op[0];
$viewtype = 'viewgrid';

if (isset($array_op[1])) {
    if (sizeof($array_op) == 2 and preg_match('/^page\-([0-9]+)$/', $array_op[1], $m)) {
        $page = intval($m[1]);
    } else {
        $alias = '';
    }
}
$page_title = trim(str_replace('-', ' ', $alias));

if (! empty($page_title) and $page_title == strip_punctuation($page_title)) {
    $stmt = $db->prepare('SELECT tid, image, description, keywords FROM ' . $db_config['prefix'] . '_' . $module_data . '_tags_' . NV_LANG_DATA . ' WHERE alias= :alias');
    $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
    $stmt->execute();
    list ($tid, $image_tag, $description, $key_words) = $stmt->fetch(3);
    
    if ($tid > 0) {
        $array_data = array();
        
        $base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=tag/' . $alias;
        if ($page > 1) {
            $page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
        }
        
        $array_mod_title[] = array(
            'catid' => 0,
            'title' => $page_title,
            'link' => $base_url
        );
        
        $db->sqlreset()
        ->select('COUNT(*)')
            ->from($db_config['prefix'] . '_' . $module_data . '_rows')
            ->where('status=1 AND id IN (SELECT id FROM ' . $db_config['prefix'] . '_' . $module_data . '_tags_id_' . NV_LANG_DATA . ' WHERE tid=' . $tid . ')');
    
        $sth = $db->prepare($db->sql());
        
        $sth->execute();
        $num_items = $sth->fetchColumn();
        
        $db->select('id, catid, ' . NV_LANG_DATA . '_title title, ' . NV_LANG_DATA . '_alias alias, homeimgfile, homeimgthumb, ' . NV_LANG_DATA . '_description description, date_start_method, date_start_config, date_start, code, discounts_id, money_unit, show_price, num_day, date_start, groups_view, show_price')
            ->order('id DESC')
            ->limit($per_page)
            ->offset(($page - 1) * $per_page);
        $sth = $db->prepare($db->sql());
        $sth->execute();
        
        while ($row = $sth->fetch()) {
            if (nv_user_in_groups($row['groups_view'])) {
                $row['title_clean'] = nv_clean60($row['title'], $array_config['title_lenght']);
                $row['date_start'] = nv_get_date_start($row['date_start_method'], $row['date_start_config'], $row['date_start']);
                $row['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_cat[$row['catid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl'];
                $row['thumb'] = nv_tour_get_thumb($row['homeimgfile'], $row['homeimgthumb'], $module_upload);
                $array_data[$row['id']] = $row;
            }
        }

        if (empty($array_data) and $page > 1) {
            Header('Location: ' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true));
            exit();
        }
        
        if (! empty($image_tag)) {
            $image_tag = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_upload . '/' . $image_tag;
        }
        
        $base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
        $html_pages = nv_alias_page($page_title, $base_url, $num_items, $per_page, $page);
        
        $contents = nv_theme_tours_viewtag($array_data, $viewtype, $html_pages);
        
        include NV_ROOTDIR . '/includes/header.php';
        echo nv_site_theme($contents);
        include NV_ROOTDIR . '/includes/footer.php';
    }
}

$redirect = '<meta http-equiv="Refresh" content="3;URL=' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true) . '" />';
nv_info_die($lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] . $redirect);
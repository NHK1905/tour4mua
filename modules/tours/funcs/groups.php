<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2015 VINADES.,JSC. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Wed, 16 Dec 2015 01:44:45 GMT
 */
if (! defined('NV_IS_MOD_TOURS'))
    die('Stop!!!');

$viewtype = 'viewgrid';

if (isset($array_op[1])) {
    $alias = trim($array_op[1]);
    $page = (isset($array_op[2]) and substr($array_op[2], 0, 5) == 'page-') ? intval(substr($array_op[2], 5)) : 1;
    
    $stmt = $db->prepare('SELECT bid, ' . NV_LANG_DATA . '_title title, ' . NV_LANG_DATA . '_alias alias, image, ' . NV_LANG_DATA . '_description description, ' . NV_LANG_DATA . '_keywords keywords FROM ' . $db_config['prefix'] . '_' . $module_data . '_block_cat WHERE ' . NV_LANG_DATA . '_alias= :alias');
    $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
    $stmt->execute();
    list ($bid, $page_title, $alias, $image_group, $description, $key_words) = $stmt->fetch(3);
    if ($bid > 0) {
        $base_url_rewrite = $base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['groups'] . '/' . $alias;
        $array_data = array();
        
        if ($page > 1) {
            $page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
            $base_url_rewrite .= '/page-' . $page;
        }
        $base_url_rewrite = nv_url_rewrite($base_url_rewrite, true);
        if ($_SERVER['REQUEST_URI'] != $base_url_rewrite and NV_MAIN_DOMAIN . $_SERVER['REQUEST_URI'] != $base_url_rewrite) {
            Header('Location: ' . $base_url_rewrite);
            die();
        }
        
        $array_mod_title[] = array(
            'catid' => 0,
            'title' => $page_title,
            'link' => $base_url
        );
        
        $db->sqlreset()
            ->select('COUNT(*)')
            ->from($db_config['prefix'] . '_' . $module_data . '_rows t1')
            ->join('INNER JOIN ' . $db_config['prefix'] . '_' . $module_data . '_block t2 ON t1.id = t2.id')
            ->where('t2.bid= ' . $bid . ' AND t1.status= 1')
            ->where('status=1 AND t2.bid= ' . $bid);
        
        $all_page = $db->query($db->sql())
            ->fetchColumn();
        
        $db->select('t1.id, catid, ' . NV_LANG_DATA . '_title title, ' . NV_LANG_DATA . '_alias alias, homeimgfile, homeimgthumb, ' . NV_LANG_DATA . '_description description, date_start_method, date_start_config, date_start, code, discounts_id, money_unit, show_price, num_day, date_start, groups_view, show_price')
            ->order('t1.id DESC')
            ->limit($per_page)
            ->offset(($page - 1) * $per_page);
        
        $_query = $db->query($db->sql());
        while ($row = $_query->fetch()) {
            if (nv_user_in_groups($row['groups_view'])) {
                $row['title_clean'] = nv_clean60($row['title'], $array_config['title_lenght']);
                $row['date_start'] = nv_get_date_start($row['date_start_method'], $row['date_start_config'], $row['date_start']);
                $row['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_cat[$row['catid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl'];
                $row['thumb'] = nv_tour_get_thumb($row['homeimgfile'], $row['homeimgthumb'], $module_upload);
                $array_data[$row['id']] = $row;
            }
        }
        
        $groups_data = array(
            'title' => $page_title,
            'image' => $image_group,
            'description' => $description
        );
        
        $generate_page = nv_alias_page($page_title, $base_url, $all_page, $per_page, $page);
        
        $contents = nv_theme_tours_viewgroups($groups_data, $array_data, $viewtype, $generate_page);
    }
} else {
    Header('Location: ' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true));
    die();
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
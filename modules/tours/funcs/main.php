<?php

/**
 * @Project NUKEVIET 4.x
 * @Author hongoctrien (contact@mynukeviet.net)
 * @Copyright (C) 2016 hongoctrien. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Sun, 08 May 2016 07:42:57 GMT
 */
if (! defined('NV_IS_MOD_TOURS'))
    die('Stop!!!');

$page_title = $module_info['custom_title'];
$key_words = $module_info['keywords'];

$array_data = array();
$contents = '';

$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;
$base_url_rewrite = nv_url_rewrite($base_url, true);
$page_url_rewrite = ($page > 1) ? nv_url_rewrite($base_url . '/page-' . $page, true) : $base_url_rewrite;
$request_uri = $_SERVER['REQUEST_URI'];
if (! ($home or $request_uri == $base_url_rewrite or $request_uri == $page_url_rewrite or NV_MAIN_DOMAIN . $request_uri == $base_url_rewrite or NV_MAIN_DOMAIN . $request_uri == $page_url_rewrite)) {
    $redirect = '<meta http-equiv="Refresh" content="3;URL=' . $base_url_rewrite . '" />';
    nv_info_die($lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] . $redirect, 404);
}

// hien thi tat ca, 0: luoi, 1: danh sach
if (in_array($array_config['home_type'], array(0, 2))) {
    $viewtype = 'viewgrid';
    if($array_config['home_type'] == 2){
        $viewtype = 'viewlist';
    }
    
    $db->sqlreset()
        ->select('COUNT(*)')
        ->from($db_config['prefix'] . '_' . $module_data . '_rows')
        ->where('status=1');
    
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
            $row['date_start'] = nv_get_date_start($row['date_start_method'], $row['date_start_config'], $row['date_start']);
            $row['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_cat[$row['catid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl'];
            $row['thumb'] = nv_tour_get_thumb($row['homeimgfile'], $row['homeimgthumb'], $module_upload);
            $array_data[$row['id']] = $row;
        }
    }
    
    if ($page > 1) {
        $page_title = $page_title . ' - ' . $lang_global['page'] . ' ' . $page;
    }
    $page = nv_alias_page($page_title, $base_url, $num_items, $per_page, $page);
    
    $contents = nv_theme_tours_main($array_data, $viewtype, $page);
} elseif ($array_config['home_type'] == 1) {
    if (! empty($array_cat)) {
        foreach ($array_cat as $catid_i => $array_info_i) {
            if ($array_info_i['parentid'] == 0 and $array_info_i['inhome']) {
                $array_cat_id = GetCatidInParent($catid_i);
                
                $db->sqlreset()
                ->select('COUNT(*)')
                ->from($db_config['prefix'] . '_' . $module_data . '_rows')
                ->where('status=1 AND catid IN (' . implode(',', $array_cat_id) . ')');
                
                $sth = $db->prepare($db->sql());
                
                $sth->execute();
                $num_items = $sth->fetchColumn();
                
                $db->select('id, catid, ' . NV_LANG_DATA . '_title title, ' . NV_LANG_DATA . '_alias alias, homeimgfile, homeimgthumb, ' . NV_LANG_DATA . '_description description, date_start_method, date_start_config, date_start, code, discounts_id, money_unit, show_price, num_day, date_start, groups_view, show_price')
                ->order('id DESC')
                ->limit($array_info_i['numlinks']);
                
                $sth = $db->prepare($db->sql());
                $sth->execute();
                
                $array_tours = array();
                while ($row = $sth->fetch()) {
                    if (nv_user_in_groups($row['groups_view'])) {
                        $row['date_start'] = nv_get_date_start($row['date_start_method'], $row['date_start_config'], $row['date_start']);
                        $row['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_cat[$row['catid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl'];
                        $row['thumb'] = nv_tour_get_thumb($row['homeimgfile'], $row['homeimgthumb'], $module_upload);
                        $row['vehicle'] = !empty($row['vehicle']) ? $array_vehicle[$row['vehicle']]['title'] : ''; 
                        $row['viewtype'] = $array_info_i['viewtype'];
                        $array_tours[$row['id']] = $row;
                    }
                }
                
                $array_data[$catid_i] = array(
                    'title' => $array_info_i['title'],
                    'link' => $array_info_i['link'],
                    'viewtype' => $array_info_i['viewtype'],
                    'numlinks' => $array_info_i['numlinks'],
                    'counttour' => $num_items,
                    'tours' => $array_tours
                );
            }
        }
    }
    
    $contents = nv_theme_tours_main_cat($array_data);
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
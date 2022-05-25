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

$array_data = array();
$cat_info = $array_cat[$catid];
$base_url = $array_cat[$catid]['link'];
$date_start_note = $cat_info['viewtype'] == 'viewlist' ? 1 : 0;
$contents = '';

$page_title = ! empty($cat_info['custom_title']) ? $cat_info['custom_title'] : $cat_info['title'];
$key_words = $cat_info['keywords'];
$description = $cat_info['description'];

if ($page > 1) {
    $page_title = $page_title . ' - ' . $lang_global['page'] . ' ' . $page;
}

if(empty($cat_info['subid'])){
    $cat_info['gettype'] = 'getall';
}

if ($cat_info['gettype'] == 'getall') {
    if ($cat_info['numsub'] == 0) {
        $where = 'catid=' . $catid;
    } else {
        $array_catid = GetCatidInParent($catid, true);
        $where = 'catid IN (' . implode(',', $array_catid) . ')';
    }
    
    $db->sqlreset()
        ->select('COUNT(*)')
        ->from($db_config['prefix'] . '_' . $module_data . '_rows')
        ->where($where);
    
    $sth = $db->prepare($db->sql());
    
    $sth->execute();
    $num_items = $sth->fetchColumn();
    
    $db->select('id, catid, ' . NV_LANG_DATA . '_title title, ' . NV_LANG_DATA . '_alias alias, homeimgfile, homeimgthumb, ' . NV_LANG_DATA . '_description description, date_start_method, date_start_config, date_start, code, discounts_id, money_unit, show_price, num_day, date_start, groups_view, show_price, place_start, place_end, vehicle, hotels_info')
        ->order('id DESC')
        ->limit($per_page)
        ->offset(($page - 1) * $per_page);
    
    $sth = $db->prepare($db->sql());
    $sth->execute();
    
    while ($row = $sth->fetch()) {
        if (nv_user_in_groups($row['groups_view'])) {
            $row['title_clean'] = nv_clean60($row['title'], $array_config['title_lenght']);
            $row['date_start'] = nv_get_date_start($row['date_start_method'], $row['date_start_config'], $row['date_start'], $date_start_note);
            $row['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_cat[$row['catid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl'];
            $row['thumb'] = nv_tour_get_thumb($row['homeimgfile'], $row['homeimgthumb'], $module_upload);
			$row['province_start'] = nv_location_get_province_info($row['place_start']);
			$row['province_end'] = nv_location_get_province_info($row['place_end']);
			$row['vehicle'] = !empty($row['vehicle']) ? $array_vehicle[$row['vehicle']]['title'] : '';
			$row['hotel_star'] = nv_tours_get_hotel_star($row['hotels_info'], $array_hotels);
			if(!empty($row['hotel_star'])){
				$row['hotel_star_title'] = $lang_module['hotels_type_' . $row['hotel_star']];
			}
            $array_data[$row['id']] = $row;
        }
    }
	
    $alias_page = nv_alias_page($page_title, $base_url, $num_items, $per_page, $page);
    
    $contents = nv_theme_tours_viewcat($array_data, $cat_info['viewtype'], $alias_page);
} elseif ($cat_info['gettype'] == 'getcat') {
    $array_data = array();
    $array_subcatid = explode(',', $array_cat[$catid]['subid']);
    foreach ($array_subcatid as $catid_i) {
        $array_tours = array();
        
        $array_info_i = $array_cat[$catid_i];
        $array_catid = GetCatidInParent($catid_i);
        
        $db->sqlreset()
            ->select('COUNT(*)')
            ->from($db_config['prefix'] . '_' . $module_data . '_rows')
            ->where('catid IN (' . implode(',', $array_catid) . ')');

        $sth = $db->prepare($db->sql());
        
        $sth->execute();
        $num_items = $sth->fetchColumn();
        
        $db->select('id, catid, ' . NV_LANG_DATA . '_title title, ' . NV_LANG_DATA . '_alias alias, homeimgfile, homeimgthumb, ' . NV_LANG_DATA . '_description description, date_start_method, date_start_config, date_start, code, discounts_id, money_unit, show_price, num_day, date_start, groups_view, show_price, place_start, place_end, vehicle, hotels_info')
            ->order('id DESC')
            ->limit($per_page)
            ->offset(($page - 1) * $per_page);
        
        $sth = $db->prepare($db->sql());
        $sth->execute();
        
        while ($row = $sth->fetch()) {
            if (nv_user_in_groups($row['groups_view'])) {
                $row['title_clean'] = nv_clean60($row['title'], $array_config['title_lenght']);
                $row['date_start'] = nv_get_date_start($row['date_start_method'], $row['date_start_config'], $row['date_start'], $date_start_note);
                $row['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_cat[$row['catid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl'];
                $row['thumb'] = nv_tour_get_thumb($row['homeimgfile'], $row['homeimgthumb'], $module_upload);
				$row['province_start'] = nv_location_get_province_info($row['place_start']);
				$row['province_end'] = nv_location_get_province_info($row['place_end']);
				$row['vehicle'] = !empty($row['vehicle']) ? $array_vehicle[$row['vehicle']]['title'] : '';
				$row['hotel_star'] = nv_tours_get_hotel_star($row['hotels_info'], $array_hotels);
				if(!empty($row['hotel_star'])){
					$row['hotel_star_title'] = $lang_module['hotels_type_' . $row['hotel_star']];
				}
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
    
    $contents = nv_theme_tours_main_cat($array_data);
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
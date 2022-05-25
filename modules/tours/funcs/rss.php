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

$channel = array();
$items = array();

$channel['title'] = $module_info['custom_title'];
$channel['link'] = NV_MY_DOMAIN . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;
$channel['atomlink'] = NV_MY_DOMAIN . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=rss';
$channel['description'] = ! empty($module_info['description']) ? $module_info['description'] : $global_config['site_description'];

$catid = 0;
if (isset($array_op[1])) {
    $alias_cat_url = $array_op[1];
    $cattitle = '';
    foreach ($array_cat as $catid_i => $array_cat_i) {
        if ($alias_cat_url == $array_cat_i['alias']) {
            $catid = $catid_i;
            break;
        }
    }
}

$db_slave->sqlreset()
    ->select('id, catid, ' . NV_LANG_DATA . '_title title, ' . NV_LANG_DATA . '_alias alias, homeimgfile, homeimgthumb, ' . NV_LANG_DATA . '_description description, addtime, groups_view')
    ->order('id DESC')
    ->limit(30);

if (! empty($catid)) {
    $channel['title'] = $module_info['custom_title'] . ' - ' . $array_cat[$catid]['title'];
    $channel['link'] = NV_MY_DOMAIN . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $alias_cat_url;
    $channel['description'] = $array_cat[$catid]['description'];

    $db_slave->from($db_config['prefix'] . '_' . $module_data . '_rows')
        ->where('status=1 AND catid=' . $catid);
} else {
    $db_slave->from($db_config['prefix'] . '_' . $module_data . '_rows')
        ->where('status=1');
}
if ($module_info['rss']) {
    $result = $db_slave->query($db_slave->sql());
    while ($row = $result->fetch()) {
        $catalias = $array_cat[$row['catid']]['alias'];
        if (nv_user_in_groups($row['groups_view'])) {
            $row['thumb'] = nv_tour_get_thumb($row['homeimgfile'], $row['homeimgthumb'], $module_upload);
            $row['thumb'] = (! empty($row['thumb'])) ? '<img src="' . $row['thumb'] . '" width="100" align="left" border="0">' : '';
            $items[] = array(
                'title' => $row['title'],
                'link' => NV_MY_DOMAIN . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_cat[$row['catid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl'],
                'guid' => $module_name . '_' . $row['id'],
                'description' => $row['thumb'] . $row['description'],
                'pubdate' => $row['addtime']
            );
        }
    }
}

nv_rss_generate($channel, $items);
die();
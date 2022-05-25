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

$page_title = $lang_module['search'];

$page = $nv_Request->get_int('page', 'get', 1);
$array_data = array();
$where = '';
$viewtype = 'viewlist';
$is_search = 0;

$array_data = array();
$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
$base_url_rewrite = $request_uri = urldecode($_SERVER['REQUEST_URI']);

$array_search = array(
    'q' => $nv_Request->get_title('q', 'post,get', ''),
    'catid' => $nv_Request->get_int('catid', 'post,get', 0),
    'date_begin' => $nv_Request->get_title('date_begin', 'post,get', ''),
    'date_end' => $nv_Request->get_title('date_end', 'post,get', ''),
    'place_start' => $nv_Request->get_int('place_start', 'post,get', 0),
    'place_end' => $nv_Request->get_int('place_end', 'post,get', 0),
    'discount' => $nv_Request->get_int('discount', 'post,get', 0),
    'time' => $nv_Request->get_int('time', 'post,get', 0),
    'price_spread' => $nv_Request->get_title('price_spread', 'post,get', ''),
    'hotel_star' => $nv_Request->get_int('hotel_star', 'post,get', 0),
    'vehicle' => $nv_Request->get_int('vehicle', 'post,get', 0)
);

if (! empty($array_search['q'])) {
    $base_url .= '&q=' . $array_search['q'];
    $where .= ' AND (code LIKE ' . $db->quote("%" . $array_search['q'] . "%") . '
	OR ' . NV_LANG_DATA . '_title LIKE ' . $db->quote("%" . $array_search['q'] . "%") . '
	OR ' . NV_LANG_DATA . '_alias LIKE ' . $db->quote("%" . $array_search['q'] . "%") . '
	OR ' . NV_LANG_DATA . '_title_custom LIKE ' . $db->quote("%" . $array_search['q'] . "%") . '
	OR ' . NV_LANG_DATA . '_plan LIKE ' . $db->quote("%" . $array_search['q'] . "%") . '
	OR ' . NV_LANG_DATA . '_description LIKE ' . $db->quote("%" . $array_search['q'] . "%") . ')';
}

if (! empty($array_search['catid'])) {
    $_array_cat = GetCatidInParent($array_search['catid']);
    $base_url .= '&catid=' . $array_search['catid'];
    $where .= ' AND catid IN (' . implode(',', $_array_cat) . ')';
} else {
    $base_url_rewrite = str_replace('&catid=' . $array_search['catid'], '', $base_url_rewrite);
}

if (! empty($array_search['date_begin'])) {
    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $array_search['date_begin'], $m)) {
        $array_search['date_begin'] = mktime(0, 0, 0, $m[2], $m[1], $m[3]);
        $base_url .= 'date_begin=' . $array_search['date_begin'];
        $date_begin = $array_search['date_begin'];
    }
} else {
    $date_begin = 0;
    $base_url_rewrite = str_replace('&date_begin=' . $array_search['date_begin'], '', $base_url_rewrite);
}

if (! empty($array_search['date_end'])) {
    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $array_search['date_end'], $m)) {
        $array_search['date_end'] = mktime(23, 59, 59, $m[2], $m[1], $m[3]);
        $base_url .= 'date_end=' . $array_search['date_end'];
        $date_end = $array_search['date_end'];
    }
} else {
    $date_end = 0;
    $base_url_rewrite = str_replace('&date_end=' . $array_search['date_end'], '', $base_url_rewrite);
}

$array_search_id = nv_search_date_start($date_begin, $date_end);
if(!empty($array_search_id)){
    $where .= ' AND id IN (' . implode(',', $array_search_id) . ')';
}

if (! empty($array_search['place_start'])) {
    $base_url .= 'place_start=' . $array_search['place_start'];
    $where .= ' AND place_start=' . $array_search['place_start'];
} else {
    $base_url_rewrite = str_replace('&place_start=' . $array_search['place_start'], '', $base_url_rewrite);
}

if (! empty($array_search['place_end'])) {
    $base_url .= 'place_end=' . $array_search['place_start'];
    $where .= ' AND place_end=' . $array_search['place_end'];
} else {
    $base_url_rewrite = str_replace('&place_end=' . $array_search['place_end'], '', $base_url_rewrite);
}

if (! empty($array_search['discount'])) {
    $base_url .= 'discount=' . $array_search['discount'];
    $where .= ' AND discounts_id > 0';
} else {
    $base_url_rewrite = str_replace('&discount=' . $array_search['discount'], '', $base_url_rewrite);
}

if(!empty($array_search['time'])){
    $base_url .= 'time=' . $array_search['time'];
    $where .= ' AND num_day=' . $array_search['time'];
}else{
    $base_url_rewrite = str_replace('&time=' . $array_search['time'], '', $base_url_rewrite);
}

if(!empty($array_search['price_spread'])){
    $base_url .= 'price_spread=' . $array_search['price_spread'];
    
    $price_spread = explode('-', $array_search['price_spread']);
    if(sizeof($price_spread) == 2){
        if(!empty($price_spread[0]) and !empty($price_spread[1])){
            $where .= ' AND (price >= ' . $price_spread[0] . ' AND price <= ' . $price_spread[1] . ')';
        }elseif(!empty($price_spread[0]) and empty($price_spread[1])){
            $where .= ' AND price >= ' . $price_spread[0];
        }
    }
}else{
    $base_url_rewrite = str_replace('&price_spread=' . $array_search['price_spread'], '', $base_url_rewrite);
}

if(!empty($array_search['hotel_star'])){
    $base_url .= 'hotel_star=' . $array_search['hotel_star'];
    $where .= ' AND hotels_star=' . $array_search['hotel_star'];
}else{
    $base_url_rewrite = str_replace('&hotel_star=' . $array_search['hotel_star'], '', $base_url_rewrite);
}

if(!empty($array_search['vehicle'])){
    $base_url .= 'vehicle=' . $array_search['vehicle'];
    $where .= ' AND vehicle=' . $array_search['vehicle'];
}else{
    $base_url_rewrite = str_replace('&vehicle=' . $array_search['vehicle'], '', $base_url_rewrite);
}


$base_url_rewrite = nv_url_rewrite($base_url_rewrite, true);
if ($request_uri != $base_url_rewrite and NV_MAIN_DOMAIN . $request_uri != $base_url_rewrite) {
    header('Location: ' . $base_url_rewrite);
    die();
}

if (! empty($where)) {
    $is_search = 1;
}

$db->sqlreset()
    ->select('COUNT(*)')
    ->from($db_config['prefix'] . '_' . $module_data . '_rows')
    ->where('status=1' . $where);

$sth = $db->prepare($db->sql());

$sth->execute();
$num_items = $sth->fetchColumn();

$db->select('id, catid, ' . NV_LANG_DATA . '_title title, ' . NV_LANG_DATA . '_alias alias, homeimgfile, homeimgthumb, ' . NV_LANG_DATA . '_description description, date_start_method, date_start_config, date_start, code, discounts_id, money_unit, show_price, num_day, date_start, groups_view, show_price, vehicle, hotels_info')
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
		$row['vehicle'] = !empty($row['vehicle']) ? $array_vehicle[$row['vehicle']]['title'] : '';
		$row['hotel_star'] = nv_tours_get_hotel_star($row['hotels_info'], $array_hotels);
		if(!empty($row['hotel_star'])){
			$row['hotel_star_title'] = $lang_module['hotels_type_' . $row['hotel_star']];
		}
        $array_data[$row['id']] = $row;
    }
}

$lang_module['search_result_number'] = sprintf($lang_module['search_result_number'], $num_items);

if ($page > 1) {
    $page_title = $page_title . ' - ' . $lang_global['page'] . ' ' . $page;
}
$generate_page = '';
if ($num_items > $per_page) {
    $url_link = $_SERVER['REQUEST_URI'];
    if (strpos($url_link, '&page=') > 0) {
        $url_link = substr($url_link, 0, strpos($url_link, '&page='));
    } elseif (strpos($url_link, '?page=') > 0) {
        $url_link = substr($url_link, 0, strpos($url_link, '?page='));
    }
    $_array_url = array( 'link' => $url_link, 'amp' => '&page=' );
    $generate_page = nv_generate_page($_array_url, $num_items, $per_page, $page);
}

$contents = nv_theme_tours_search($array_data, $is_search, $viewtype, $generate_page);

$array_mod_title[] = array(
    'title' => $page_title,
    'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op
);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
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
    
if ($nv_Request->isset_request('delete', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    if (empty($id))
        die('NO');
    nv_tour_delete($id);
    die('OK');
}

$tour_info = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows WHERE status=1 AND ' . NV_LANG_DATA . '_alias=' . $db->quote($alias_url))->fetch();

if (empty($tour_info) or ! nv_user_in_groups($array_cat[$catid]['groups_view']) or ! nv_user_in_groups($tour_info['groups_view'])) {
    $redirect = '<meta http-equiv="Refresh" content="3;URL=' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true) . '" />';
    nv_info_die($lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] . $redirect);
}

$tour_info['title'] = $tour_info[NV_LANG_DATA . '_title'];
$tour_info['alias'] = $tour_info[NV_LANG_DATA . '_alias'];

$base_url_rewrite = nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_cat[$tour_info['catid']]['alias'] . '/' . $tour_info['alias'] . $global_config['rewrite_exturl'], true);
if ($_SERVER['REQUEST_URI'] == $base_url_rewrite) {
    $canonicalUrl = NV_MAIN_DOMAIN . $base_url_rewrite;
} elseif (NV_MAIN_DOMAIN . $_SERVER['REQUEST_URI'] != $base_url_rewrite) {
    // chuyen huong neu doi alias
    header('HTTP/1.1 301 Moved Permanently');
    Header('Location: ' . $base_url_rewrite);
    die();
} else {
    $canonicalUrl = $base_url_rewrite;
}

$tour_info['title_custom'] = $tour_info[NV_LANG_DATA . '_title_custom'];
$tour_info['plan'] = $tour_info[NV_LANG_DATA . '_plan'];
$tour_info['description'] = $tour_info[NV_LANG_DATA . '_description'];
$tour_info['description_html'] = $tour_info[NV_LANG_DATA . '_description_html'];
$tour_info['note'] = $tour_info[NV_LANG_DATA . '_note'];
$tour_info['date_start'] = nv_get_date_start($tour_info['date_start_method'], $tour_info['date_start_config'], $tour_info['date_start'], 1);
$tour_info['province'] = nv_location_get_province_info($tour_info['place_start']);
$tour_info['price_config'] = unserialize($tour_info['price_config']);

$cat_info = $array_cat[$tour_info['catid']];
$tour_info['price_method'] = $cat_info['price_method'];
$tour_info['subprice'] = array();
$cat_info['subprice'] = unserialize($cat_info['subprice']);
$cat_info['subprice'][] = 0;
$result = $db->query('SELECT id, ' . NV_LANG_DATA . '_title title FROM ' . $db_config['prefix'] . '_' . $module_data . '_subprice WHERE status=1 AND id IN (' . implode(',', $cat_info['subprice']) . ') ORDER BY weight');
while ($_row = $result->fetch()) {
    $tour_info['subprice'][$_row['id']] = $_row;
}

// thong tin chuyen bay, luot di
$tour_info['flying_begin'] = unserialize($tour_info['flying_begin']);
if (! empty($tour_info['flying_begin']['id'])) {
    $tour_info['flying_begin']['flying_title'] = $db->query('SELECT title FROM ' . $db_config['prefix'] . '_' . $module_data . '_flying WHERE id=' . $tour_info['flying_begin']['id'])->fetchColumn();
    $tour_info['flying_begin']['flying_title_clean'] = nv_clean60($tour_info['flying_begin']['flying_title'], 35);
}
$tour_info['flying_begin']['time'] = ! empty($tour_info['flying_begin']['time']) ? nv_date('H:i d/m/Y', $tour_info['flying_begin']['time']) : '';

// thong tin chuyen bay, luot ve
$tour_info['flying_end'] = unserialize($tour_info['flying_end']);
if (! empty($tour_info['flying_end']['id'])) {
    $tour_info['flying_end']['flying_title'] = $db->query('SELECT title FROM ' . $db_config['prefix'] . '_' . $module_data . '_flying WHERE id=' . $tour_info['flying_end']['id'])->fetchColumn();
    $tour_info['flying_end']['flying_title_clean'] = nv_clean60($tour_info['flying_end']['flying_title'], 35);
}
$tour_info['flying_end']['time'] = ! empty($tour_info['flying_end']['time']) ? nv_date('H:i d/m/Y', $tour_info['flying_end']['time']) : '';

// thong tin khach san
$tour_info['hotels_info'] = unserialize($tour_info['hotels_info']);
if (! empty($tour_info['hotels_info'])) {
    foreach ($tour_info['hotels_info'] as $index => $value) {
        $hotel_info = $db->query('SELECT ' . NV_LANG_DATA . '_title title, star FROM ' . $db_config['prefix'] . '_' . $module_data . '_hotels WHERE id=' . $value['id'])->fetch();
        if(!empty($hotel_info)){
            $tour_info['hotels_info'][$index]['title'] = $hotel_info['title'];
            $tour_info['hotels_info'][$index]['star'] = $hotel_info['star'];
        }
    }
}

// dich vu
$tour_info['services'] = unserialize($tour_info['services']);
if(!empty($tour_info['services'])){
    $_sql = 'SELECT id, ' . NV_LANG_DATA . '_title title FROM ' . $db_config['prefix'] . '_' . $module_data . '_services WHERE status=1 ORDER BY weight';
    $array_services = $nv_Cache->db($_sql, 'id', $module_name);
    foreach($tour_info['services'] as $index => $value){
        $tour_info['services'][$index] = $array_services[$value]['title'];
    }
}

// thong tin huong dan vien
if (! empty($tour_info['guides'])) {
    $guides = unserialize($tour_info['guides']);
    $tour_info['guides'] = array();
    $result = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_guides WHERE id IN (' . implode(',', $guides) . ')');
    while ($_row = $result->fetch()) {
        $_row['full_name'] = $_row['last_name'] . ' ' . $_row['first_name'];
        $_row['age'] = ! empty($_row['birthday']) ? (nv_date('Y', NV_CURRENTTIME) - nv_date('Y', $_row['birthday'])) : '';
        $_row['gender'] = $lang_module['gender_' . $_row['gender']];
        $tour_info['guides'][$_row['id']] = $_row;
    }
}

// Phuong tien
if(!empty($tour_info['vehicle'])){    
    $tour_info['vehicle'] = $array_vehicle[$tour_info['vehicle']]['title'];
}

// anh tour
$tour_info['image'] = array();
/*if (! empty($tour_info['homeimgfile'])) {
    $src = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $tour_info['homeimgfile'];
    $meta_property['og:image'] = (preg_match('/^(http|https|ftp|gopher)\:\/\//', $src)) ? $src : NV_MY_DOMAIN . $src;
    
    $tour_info['image'][] = array(
        'title' => $tour_info['title'],
        'description' => $tour_info['description'],
        'homeimgfile' => $src
    );
}*/
$result = $db->query('SELECT ' . NV_LANG_DATA . '_title title, ' . NV_LANG_DATA . '_description description, homeimgfile FROM ' . $db_config['prefix'] . '_' . $module_data . '_images WHERE rows_id=' . $tour_info['id']);
while ($_row = $result->fetch()) {
    $_row['homeimgfile'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $_row['homeimgfile'];
    $_row['description'] = strip_tags($_row['description']);
    $tour_info['image'][] = $_row;
}

// tour cung chu de
$array_tour_other = array();
$db->select('id, catid, ' . NV_LANG_DATA . '_title title, ' . NV_LANG_DATA . '_alias alias, homeimgfile, homeimgthumb, ' . NV_LANG_DATA . '_description description, date_start_method, date_start_config, date_start, code, discounts_id, money_unit, show_price, num_day, date_start, groups_view, show_price')
->from($db_config['prefix'] . '_' . $module_data . '_rows')
->where('status=1 AND catid=' . $tour_info['catid'] . ' AND id!=' . $tour_info['id'])
->order('id DESC')
->limit($array_cat[$tour_info['catid']]['numlinks']);

$sth = $db->prepare($db->sql());
$sth->execute();

while ($_row = $sth->fetch()) {
    if (nv_user_in_groups($_row['groups_view'])) {
        $_row['date_start'] = nv_get_date_start($_row['date_start_method'], $_row['date_start_config'], $_row['date_start']);
        $_row['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $array_cat[$_row['catid']]['alias'] . '/' . $_row['alias'] . $global_config['rewrite_exturl'];
        $_row['thumb'] = nv_tour_get_thumb($_row['homeimgfile'], $_row['homeimgthumb'], $module_upload);
        $array_tour_other[$_row['id']] = $_row;
    }
}

// dem so luot xem tour
$time_set = $nv_Request->get_int($module_data . '_' . $op . '_' . $tour_info['id'], 'session');
if (empty($time_set)) {
    $nv_Request->set_Session($module_data . '_' . $op . '_' . $tour_info['id'], NV_CURRENTTIME);
    $query = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_rows SET hitstotal=hitstotal+1 WHERE id=' . $tour_info['id'];
    $db->query($query);
}

// Tu khoa
$array_keyword = array();
$_query = $db->query('SELECT a1.keyword keyword, a2.alias alias FROM ' . $db_config['prefix'] . '_' . $module_data . '_tags_id_' . NV_LANG_DATA . ' a1 INNER JOIN ' . $db_config['prefix'] . '_' . $module_data . '_tags_' . NV_LANG_DATA . ' a2 ON a1.tid=a2.tid WHERE a1.id=' . $tour_info['id']);
while ($row = $_query->fetch()) {
    $array_keyword[] = $row;
    $key_words[] = $row['keyword'];
}
$key_words = !empty($key_words) ? implode(',', $key_words) : '';

// comment
if (isset($site_mods['comment']) and isset($module_config[$module_name]['activecomm'])) {
    define('NV_COMM_ID', $tour_info['id']); // ID bài viết
    define('NV_COMM_AREA', $module_info['funcs'][$op]['func_id']); // để đáp ứng comment ở bất cứ đâu không cứ là bài viết
    $allowed = $module_config[$module_name]['allowed_comm']; // check allow comemnt
    
    if ($allowed == '-1') {
        $allowed = $tour_info['groups_comment'];
    }
    
    define('NV_PER_PAGE_COMMENT', 5);
    
    // Số bản ghi hiển thị bình luận
    require_once NV_ROOTDIR . '/modules/comment/comment.php';
    $area = (defined('NV_COMM_AREA')) ? NV_COMM_AREA : 0;
    $checkss = md5($module_name . '-' . $area . '-' . NV_COMM_ID . '-' . $allowed . '-' . NV_CACHE_PREFIX);
    
    // get url comment
    $url_info = parse_url($client_info['selfurl']);
    $content_comment = nv_comment_module($module_name, $checkss, $area, NV_COMM_ID, $allowed, 1);
} else {
    $content_comment = '';
}

$meta_property['og:type'] = 'article';
$meta_property['article:published_time'] = date('Y-m-dTH:i:s', $tour_info['addtime']);
$meta_property['article:modified_time'] = date('Y-m-dTH:i:s', $tour_info['edittime']);
$meta_property['article:section'] = $array_cat[$tour_info['catid']]['title'];

$page_title = ! empty($tour_info['title_custom']) ? $tour_info['title_custom'] : $tour_info['title'];
$description = $tour_info['description'];

$contents = nv_theme_tours_detail($tour_info, $array_tour_other, $content_comment);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
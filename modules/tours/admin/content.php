<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2016 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 09 May 2016 09:39:30 GMT
 */
if (! defined('NV_IS_FILE_ADMIN'))
    die('Stop!!!');

if(empty($array_cat)){
    $urlcat = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat';
    $contents = nv_theme_alert($lang_module['error_data_title'], $lang_module['error_data_cat_empty'], 'danger', $urlcat, $lang_module['cat_manage']);
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_admin_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

$array_country = nv_location_get_country();
if(empty($array_country)){
    $urllocation = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=location';
    $contents = nv_theme_alert($lang_module['error_data_title'], $lang_module['error_data_location_empty'], 'danger', $urllocation, $lang_module['location_manage']);
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_admin_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

$table_name = $db_config['prefix'] . '_' . $module_data . '_rows';

if ($nv_Request->isset_request('get_alias_title', 'post')) {
    $alias = $nv_Request->get_title('get_alias_title', 'post', '');
    $alias = change_alias($alias);
    die($alias);
}

$groups_list = nv_groups_list();
$row = array();
$error = array();
$id_block_content = array();
$row['id'] = $nv_Request->get_int('id', 'post,get', 0);

$username_alias = change_alias($admin_info['username']);
$array_structure_image = array();
$array_structure_image[''] = $module_upload;
$array_structure_image['Y'] = $module_upload . '/' . date('Y');
$array_structure_image['Ym'] = $module_upload . '/' . date('Y_m');
$array_structure_image['Y_m'] = $module_upload . '/' . date('Y/m');
$array_structure_image['Ym_d'] = $module_upload . '/' . date('Y_m/d');
$array_structure_image['Y_m_d'] = $module_upload . '/' . date('Y/m/d');
$array_structure_image['username'] = $module_upload . '/' . $username_alias;

$array_structure_image['username_Y'] = $module_upload . '/' . $username_alias . '/' . date('Y');
$array_structure_image['username_Ym'] = $module_upload . '/' . $username_alias . '/' . date('Y_m');
$array_structure_image['username_Y_m'] = $module_upload . '/' . $username_alias . '/' . date('Y/m');
$array_structure_image['username_Ym_d'] = $module_upload . '/' . $username_alias . '/' . date('Y_m/d');
$array_structure_image['username_Y_m_d'] = $module_upload . '/' . $username_alias . '/' . date('Y/m/d');

$structure_upload = isset($module_config[$module_name]['structure_upload']) ? $module_config[$module_name]['structure_upload'] : 'Ym';
$currentpath = isset($array_structure_image[$structure_upload]) ? $array_structure_image[$structure_upload] : '';

if (file_exists(NV_UPLOADS_REAL_DIR . '/' . $currentpath)) {
    $upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $currentpath;
} else {
    $upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $module_upload;
    $e = explode('/', $currentpath);
    if (! empty($e)) {
        $cp = '';
        foreach ($e as $p) {
            if (! empty($p) and ! is_dir(NV_UPLOADS_REAL_DIR . '/' . $cp . $p)) {
                $mk = nv_mkdir(NV_UPLOADS_REAL_DIR . '/' . $cp, $p);
                if ($mk[0] > 0) {
                    $upload_real_dir_page = $mk[2];
                    try {
                        $db->query("INSERT INTO " . NV_UPLOAD_GLOBALTABLE . "_dir (dirname, time) VALUES ('" . NV_UPLOADS_DIR . "/" . $cp . $p . "', 0)");
                    } catch (PDOException $e) {
                        trigger_error($e->getMessage());
                    }
                }
            } elseif (! empty($p)) {
                $upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $cp . $p;
            }
            $cp .= $p . '/';
        }
    }
    $upload_real_dir_page = str_replace('\\', '/', $upload_real_dir_page);
}

$currentpath = str_replace(NV_ROOTDIR . '/', '', $upload_real_dir_page);
$uploads_dir_user = NV_UPLOADS_DIR . '/' . $module_upload;
if (! defined('NV_IS_SPADMIN') and strpos($structure_upload, 'username') !== false) {
    $array_currentpath = explode('/', $currentpath);
    if ($array_currentpath[2] == $username_alias) {
        $uploads_dir_user = NV_UPLOADS_DIR . '/' . $module_upload . '/' . $username_alias;
    }
}

if ($row['id'] > 0) {
    $lang_module['content'] = $lang_module['content_edit'];
    
    $row = $db->query('SELECT * FROM ' . $table_name . ' WHERE id=' . $row['id'])->fetch();
    if (empty($row)) {
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        die();
    }
    
    $row['title'] = $row[NV_LANG_DATA . '_title'];
    $row['title_custom'] = $row[NV_LANG_DATA . '_title_custom'];
    $row['alias'] = $row[NV_LANG_DATA . '_alias'];
    $row['plan'] = $row[NV_LANG_DATA . '_plan'];
    $row['description'] = $row[NV_LANG_DATA . '_description'];
    $row['description_html'] = $row[NV_LANG_DATA . '_description_html'];
    $row['note'] = $row[NV_LANG_DATA . '_note'];
    
    if (! empty($row['homeimgfile']) and file_exists(NV_UPLOADS_REAL_DIR)) {
        $currentpath = NV_UPLOADS_DIR . '/' . $module_upload . '/' . dirname($row['homeimgfile']);
    }
    
    $id_block_content = array();
    $sql = 'SELECT bid FROM ' . $db_config['prefix'] . '_' . $module_data . '_block where id=' . $row['id'];
    $result = $db->query($sql);
    while (list ($bid_i) = $result->fetch(3)) {
        $id_block_content[] = $bid_i;
    }
    
    // Old keywords
    $array_keywords_old = array();
    $_query = $db->query('SELECT tid, keyword FROM ' . $db_config['prefix'] . '_' . $module_data . '_tags_id_' . NV_LANG_DATA . ' WHERE id=' . $row['id'] . ' ORDER BY keyword ASC');
    while ($_row = $_query->fetch()) {
        $array_keywords_old[$_row['tid']] = $_row['keyword'];
    }
    $row['keywords'] = implode(', ', $array_keywords_old);
    $row['keywords_old'] = $_row['keywords'];
} else {
    $lang_module['content'] = $lang_module['content_add'];
    
    $row['id'] = 0;
    $row['catid'] = 0;
    $row['code'] = '';
    $row['num_day'] = 0;
    $row['date_start_method'] = $array_config['date_start_method'];
    $row['date_start_config'] = '';
    $row['date_start'] = 0;
    $row['place_start'] = $array_config['default_place_start'];
    $row['place_end'] = 0;
    $row['homeimgfile'] = '';
    $row['homeimgalt'] = '';
    $row['homeimgthumb'] = 0;
    $row['allowed_rating'] = 1;
    $row['groups_view'] = 6;
    $row['groups_comment'] = 6;
    $row['title'] = '';
    $row['alias'] = '';
    $row['title_custom'] = '';
    $row['plan'] = '';
    $row['description'] = '';
    $row['description_html'] = '';
    $row['note'] = '';
    $row['services'] = '';
    $row['vehicle'] = 0;
    $row['flying_begin'] = $row['flying_end'] = $row['hotels_info'] = $row['guiders'] = array();
    $row['hotels_star'] = 0;
    $row['show_price'] = 1;
    $row['num_seat'] = $row['rest'] = 0;
}

if ($nv_Request->isset_request('submit', 'post')) {
    $row['catid'] = $nv_Request->get_int('catid', 'post', 0);
    $row['code'] = $nv_Request->get_title('code', 'post', '');
    
    $price_method = $array_cat[$row['catid']]['price_method'];
    $price = 0;
    
    $price_config = $nv_Request->get_array('price_config', 'post', array());
    if (! empty($price_config)) {
        if ($price_method == 0) {
            $price = $price_config['price'][0] = floatval(preg_replace('/[^0-9]/', '', $price_config['price'][0]));
            if(isset($price_config['subprice'])){
                $subprice = $price_config['subprice'];
                if (! empty($subprice)) {
                    foreach ($subprice as $subprice_id => $subprice_value) {
                        if (! empty($subprice_value)) {
                            $price_config['subprice'][$subprice_id] = floatval(preg_replace('/[^0-9]/', '', $subprice_value));
                        } else {
                            $price_config['subprice'][$subprice_id] = 0;
                        }
                    }
                }   
            }
        } elseif ($price_method == 1) {
            if (! empty($price_config['price'])) {
                foreach ($price_config['price'] as $price_id => $price_value) {
                    if (! empty($price_value)) {
                        $price_config['price'][$price_id] = floatval(preg_replace('/[^0-9]/', '', $price_value));
                    } else {
                        $price_config['price'][$price_id] = 0;
                    }
                }
            }
            $price = $price_config['price'][0];
            if(isset($price_config['subprice'])){
                $subprice = $price_config['subprice'];
                if (! empty($subprice)) {
                    foreach ($subprice as $subprice_id => $subprice_value) {
                        for ($i = 0; $i < 3; $i ++) {
                            if (! empty($subprice_value)) {
                                $price_config['subprice'][$subprice_id][$i] = floatval(preg_replace('/[^0-9]/', '', $subprice_value[$i]));
                            } else {
                                $price_config['subprice'][$subprice_id][$i] = 0;
                            }
                        }
                    }
                }   
            }
        } elseif ($price_method == 2) {
            if (! empty($price_config['price'])) {
                foreach ($price_config['price'] as $price_id => $price_value) {
                    for ($i = 0; $i < 3; $i ++) {
                        if (! empty($price_value[$i])) {
                            $price_config['price'][$price_id][$i] = floatval(preg_replace('/[^0-9]/', '', $price_value[$i]));
                        } else {
                            $price_config['price'][$price_id][$i] = 0;
                        }
                    }
                }
            }
            $price = $price_config['price'][0][0];
            
            if(isset($price_config['subprice'])){
                $subprice = $price_config['subprice'];
                if (! empty($subprice)) {
                    foreach ($subprice as $subprice_id => $subprice_value) {
                        for ($i = 0; $i < 3; $i ++) {
                            if (! empty($subprice_value[$i])) {
                                $price_config['subprice'][$subprice_id][$i] = floatval(preg_replace('/[^0-9]/', '', $subprice_value[$i]));
                            } else {
                                $price_config['subprice'][$subprice_id][$i] = 0;
                            }
                        }
                    }
                }   
            }
        }
    }
    $row['price'] = $price;
    $row['price_config'] = serialize($price_config);
    
    $row['money_unit'] = $nv_Request->get_title('money_unit', 'post', '');
    $row['discounts_id'] = $nv_Request->get_int('discounts_id', 'post', 0);
    
    $row['num_day'] = $nv_Request->get_int('num_day', 'post', 0);
    $row['date_start_method'] = $nv_Request->get_int('date_start_method', 'post', 1);
    $row['date_start_config'] = $nv_Request->get_array('date_start_config', 'post', array());
    $date_start_config = isset($row['date_start_config'][$row['date_start_method']]) ? $row['date_start_config'][$row['date_start_method']] : array();
    $row['date_start_config'] = array_diff($date_start_config, array(0));

    if (! empty($date_start_config)) {
        $row['date_start_config'] = serialize($date_start_config);
    }else{
        $row['date_start_config'] = '';
    }
    
    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $nv_Request->get_string('date_start', 'post'), $m)) {
        $_hour = 0;
        $_min = 0;
        $row['date_start'] = mktime($_hour, $_min, 0, $m[2], $m[1], $m[3]);
    } else {
        $row['date_start'] = 0;
    }
    $row['place_start'] = $nv_Request->get_int('place_start', 'post', 0);
    $row['place_end'] = $nv_Request->get_int('place_end', 'post', 0);
    $row['homeimgfile'] = $nv_Request->get_title('homeimgfile', 'post', '');
    $row['homeimgalt'] = $nv_Request->get_title('homeimgalt', 'post', '');
    
    $_groups_post = $nv_Request->get_array('groups_view', 'post', array());
    $row['groups_view'] = ! empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';
    
    $_groups_post = $nv_Request->get_array('groups_comment', 'post', array());
    $row['groups_comment'] = ! empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';
    
    $row['allowed_rating'] = $nv_Request->get_int('allowed_rating', 'post');
    $row['show_price'] = $nv_Request->get_int('show_price', 'post');
    $row['title'] = $nv_Request->get_title('title', 'post', '');
    $row['title_custom'] = $nv_Request->get_title('title_custom', 'post', '');
    $row['plan'] = $nv_Request->get_editor('plan', '', NV_ALLOWED_HTML_TAGS);
    $row['description'] = $nv_Request->get_textarea('description', '', NV_ALLOWED_HTML_TAGS);
    $row['description_html'] = $nv_Request->get_editor('description_html', '', NV_ALLOWED_HTML_TAGS);
    $row['note'] = $nv_Request->get_editor('note', '', NV_ALLOWED_HTML_TAGS);
    
    // xu ly alias
    $row['alias'] = $nv_Request->get_title('alias', 'post', '', 1);
    $row['alias'] = empty($row['alias']) ? change_alias($row['title']) : $row['alias'];
    $stmt = $db->prepare('SELECT COUNT(*) FROM ' . $table_name . ' WHERE id !=' . $row['id'] . ' AND ' . NV_LANG_DATA . '_alias = :alias');
    $stmt->bindParam(':alias', $row['alias'], PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->fetchColumn()) {
        $weight = $db->query('SELECT MAX(id) FROM ' . $table_name . '')->fetchColumn();
        $weight = intval($weight) + 1;
        $row['alias'] = $row['alias'] . '-' . $weight;
    }
    
    $row['flying_begin'] = $nv_Request->get_array('flying_begin', 'post');
    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2})$/', $row['flying_begin']['time'], $m)) {
        $row['flying_begin']['time'] = mktime($m[4], $m[5], 59, $m[2], $m[1], $m[3]);
    } else {
        $row['flying_begin']['time'] = 0;
    }
    $row['flying_begin'] = serialize($row['flying_begin']);
    
    $row['flying_end'] = $nv_Request->get_array('flying_end', 'post');
    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2})$/', $row['flying_end']['time'], $m)) {
        $row['flying_end']['time'] = mktime($m[4], $m[5], 59, $m[2], $m[1], $m[3]);
    } else {
        $row['flying_end']['time'] = 0;
    }
    $row['flying_end'] = serialize($row['flying_end']);
    
    $row['hotels'] = $nv_Request->get_array('hotels', 'post');
    if (! empty($row['hotels'])) {
        foreach ($row['hotels'] as $index => $value) {
            if (! empty($value['id'])) {
                if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2}) - ([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2})$/', $row['hotels'][$index]['time'], $m)) {
                    $row['hotels'][$index]['time_begin'] = mktime($m[4], $m[5], 00, $m[2], $m[1], $m[3]);
                    $row['hotels'][$index]['time_end'] = mktime($m[9], $m[10], 59, $m[7], $m[6], $m[8]);
                } else {
                    $row['hotels'][$index]['time_begin'] = 0;
                    $row['hotels'][$index]['time_end'] = 0;
                }
            } else {
                unset($row['hotels'][$index]);
            }
        }
    }
    $row['hotels'] = serialize($row['hotels']);
    $row['hotels_star'] = nv_tours_get_hotel_star($row['hotels'], $array_hotels);
    
    $row['id_block_content_post'] = array_unique($nv_Request->get_typed_array('bids', 'post', 'int', array()));
    $row['num_seat'] = $nv_Request->get_int('num_seat', 'post', 0);
    $row['vehicle'] = $nv_Request->get_int('vehicle', 'post', 0);
    
    $row['guides'] = $nv_Request->get_typed_array('guides', 'post', 'int', array());
    $row['guides'] = ! empty($row['guides']) ? serialize($row['guides']) : '';
    
    $row['services'] = $nv_Request->get_typed_array('services', 'post', 'int', array());
    $row['services'] = ! empty($row['services']) ? serialize($row['services']) : '';
    
    $row['keywords'] = $nv_Request->get_array('keywords', 'post', '');
    $row['keywords'] = implode(', ', $row['keywords']);
    
    if (empty($row['title'])) {
        $error[] = $lang_module['error_required_title'];
    } elseif (empty($row['catid'])) {
        $error[] = $lang_module['error_required_catid'];
    } elseif (! $array_config['allow_auto_code'] and empty($row['code'])) {
        $error[] = $lang_module['error_required_code'];
    } elseif (empty($row['num_day'])) {
        $error[] = $lang_module['error_required_num_day'];
    } elseif ($row['date_start_method'] == 0 and empty($row['date_start'])) {
        $error[] = $lang_module['error_required_date_start'];
    } elseif (($row['date_start_method'] == 1 or $row['date_start_method'] == 2) and empty($row['date_start_config'])) {
        $error[] = $lang_module['error_required_date_start'];
    } elseif (empty($row['place_start'])) {
        $error[] = $lang_module['error_required_place_start'];
    } elseif (empty($row['place_end'])) {
        $error[] = $lang_module['error_required_place_end'];
    } elseif (empty($row['description'])) {
        $error[] = $lang_module['error_required_description'];
    }
    
    if (empty($error)) {
        
        // Xu ly tu khoa
        if ($row['keywords'] == '' and $array_config['auto_tags']) {
            $keywords = ($row['description'] != '') ? $row['description'] : $row['description_html'];
            $keywords = nv_get_keywords($keywords, 100);
            $keywords = explode(',', $keywords);
            // Ưu tiên lọc từ khóa theo các từ khóa đã có trong tags thay vì đọc từ từ điển
            $keywords_return = array();
            $sth = $db->prepare('SELECT COUNT(*) FROM ' . $db_config['prefix'] . "_" . $module_data . '_tags_id_' . NV_LANG_DATA . ' where keyword = :keyword');
            foreach ($keywords as $keyword_i) {
                $sth->bindParam(':keyword', $keyword_i, PDO::PARAM_STR);
                $sth->execute();
                if ($sth->fetchColumn()) {
                    $keywords_return[] = $keyword_i;
                    if (sizeof($keywords_return) > 20) {
                        break;
                    }
                }
            }
            $sth->closeCursor();
            if (sizeof($keywords_return) < 20) {
                foreach ($keywords as $keyword_i) {
                    if (! in_array($keyword_i, $keywords_return)) {
                        $keywords_return[] = $keyword_i;
                        if (sizeof($keywords_return) > 20) {
                            break;
                        }
                    }
                }
            }
            $row['keywords'] = implode(',', $keywords);
        }
        
        // Xu ly anh minh hoa
        $row['homeimgthumb'] = 0;
        if (! nv_is_url($row['homeimgfile']) and nv_is_file($row['homeimgfile'], NV_UPLOADS_DIR . '/' . $module_upload) === true) {
            $lu = strlen(NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/');
            $row['homeimgfile'] = substr($row['homeimgfile'], $lu);
            if (file_exists(NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module_upload . '/' . $row['homeimgfile'])) {
                $row['homeimgthumb'] = 1;
            } else {
                $row['homeimgthumb'] = 2;
            }
        } elseif (nv_is_url($row['homeimgfile'])) {
            $row['homeimgthumb'] = 3;
        } else {
            $row['homeimgfile'] = '';
        }
        
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
                $_sql = 'INSERT INTO ' . $table_name . ' (catid, code, price, price_config, money_unit, discounts_id, num_day, date_start_method, date_start_config, date_start, place_start, place_end, homeimgfile, homeimgalt, homeimgthumb, allowed_rating, show_price, services, vehicle, flying_begin, flying_end, hotels_info, hotels_star, guides, admin_id, num_seat, rest, addtime, groups_view, groups_comment' . $listfield . ') VALUES (:catid, :code, :price, :price_config, :money_unit, :discounts_id, :num_day, :date_start_method, :date_start_config, :date_start, :place_start, :place_end, :homeimgfile, :homeimgalt, :homeimgthumb, :allowed_rating, :show_price, :services, :vehicle, :flying_begin, :flying_end, :hotels_info, :hotels_star, :guides, :admin_id, :num_seat, :rest, ' . NV_CURRENTTIME . ', :groups_view, :groups_comment' . $listvalue . ')';
                $data_insert['catid'] = $row['catid'];
                $data_insert['code'] = $row['code'];
                $data_insert['price'] = $row['price'];
                $data_insert['price_config'] = $row['price_config'];
                $data_insert['money_unit'] = $row['money_unit'];
                $data_insert['discounts_id'] = $row['discounts_id'];
                $data_insert['num_day'] = $row['num_day'];
                $data_insert['date_start_method'] = $row['date_start_method'];
                $data_insert['date_start_config'] = $row['date_start_config'];
                $data_insert['date_start'] = $row['date_start'];
                $data_insert['place_start'] = $row['place_start'];
                $data_insert['place_end'] = $row['place_end'];
                $data_insert['homeimgfile'] = $row['homeimgfile'];
                $data_insert['homeimgalt'] = $row['homeimgalt'];
                $data_insert['homeimgthumb'] = $row['homeimgthumb'];
                $data_insert['allowed_rating'] = $row['allowed_rating'];
                $data_insert['show_price'] = $row['show_price'];
                $data_insert['services'] = $row['services'];
                $data_insert['vehicle'] = $row['vehicle'];
                $data_insert['flying_begin'] = $row['flying_begin'];
                $data_insert['flying_end'] = $row['flying_end'];
                $data_insert['hotels_info'] = $row['hotels'];
                $data_insert['hotels_star'] = $row['hotels_star'];
                $data_insert['guides'] = $row['guides'];
                $data_insert['admin_id'] = $admin_info['userid'];
                $data_insert['num_seat'] = $row['num_seat'];
                $data_insert['rest'] = $row['num_seat'];
                $data_insert['groups_view'] = $row['groups_view'];
                $data_insert['groups_comment'] = $row['groups_comment'];
                foreach ($field_lang as $field_lang_i) {
                    list ($flang, $fname) = $field_lang_i;
                    $data_insert[$flang . '_' . $fname] = $row[$fname];
                }
                $exc = $new_id = $db->insert_id($_sql, 'id', $data_insert);
            } else {
                $stmt = $db->prepare('UPDATE ' . $table_name . ' SET catid = :catid, code = :code, price = :price, price_config = :price_config, money_unit = :money_unit, discounts_id = :discounts_id, num_day = :num_day, date_start_method = :date_start_method, date_start_config = :date_start_config, date_start = :date_start, place_start = :place_start, place_end = :place_end, homeimgfile = :homeimgfile, homeimgalt = :homeimgalt, homeimgthumb = :homeimgthumb, allowed_rating = :allowed_rating, show_price = :show_price, services = :services, vehicle = :vehicle, flying_begin = :flying_begin, flying_end = :flying_end, hotels_info = :hotels_info, hotels_star = :hotels_star, guides = :guides, num_seat = :num_seat, edittime = ' . NV_CURRENTTIME . ', groups_view = :groups_view, groups_comment = :groups_comment, ' . NV_LANG_DATA . '_title = :title, ' . NV_LANG_DATA . '_alias = :alias, ' . NV_LANG_DATA . '_title_custom = :title_custom, ' . NV_LANG_DATA . '_plan = :plan, ' . NV_LANG_DATA . '_description = :description, ' . NV_LANG_DATA . '_description_html = :description_html, ' . NV_LANG_DATA . '_note = :note WHERE id=' . $row['id']);
                $stmt->bindParam(':catid', $row['catid'], PDO::PARAM_INT);
                $stmt->bindParam(':code', $row['code'], PDO::PARAM_STR);
                $stmt->bindParam(':price', $row['price'], PDO::PARAM_INT);
                $stmt->bindParam(':price_config', $row['price_config'], PDO::PARAM_STR);
                $stmt->bindParam(':money_unit', $row['money_unit'], PDO::PARAM_STR);
                $stmt->bindParam(':discounts_id', $row['discounts_id'], PDO::PARAM_INT);
                $stmt->bindParam(':num_day', $row['num_day'], PDO::PARAM_INT);
                $stmt->bindParam(':date_start_method', $row['date_start_method'], PDO::PARAM_INT);
                $stmt->bindParam(':date_start_config', $row['date_start_config'], PDO::PARAM_STR);
                $stmt->bindParam(':date_start', $row['date_start'], PDO::PARAM_INT);
                $stmt->bindParam(':place_start', $row['place_start'], PDO::PARAM_INT);
                $stmt->bindParam(':place_end', $row['place_end'], PDO::PARAM_INT);
                $stmt->bindParam(':homeimgfile', $row['homeimgfile'], PDO::PARAM_STR);
                $stmt->bindParam(':homeimgalt', $row['homeimgalt'], PDO::PARAM_STR);
                $stmt->bindParam(':homeimgthumb', $row['homeimgthumb'], PDO::PARAM_INT);
                $stmt->bindParam(':allowed_rating', $row['allowed_rating'], PDO::PARAM_INT);
                $stmt->bindParam(':show_price', $row['show_price'], PDO::PARAM_INT);
                $stmt->bindParam(':services', $row['services'], PDO::PARAM_STR);
                $stmt->bindParam(':vehicle', $row['vehicle'], PDO::PARAM_INT);
                $stmt->bindParam(':flying_begin', $row['flying_begin'], PDO::PARAM_STR);
                $stmt->bindParam(':flying_end', $row['flying_end'], PDO::PARAM_STR);
                $stmt->bindParam(':hotels_info', $row['hotels'], PDO::PARAM_STR);
                $stmt->bindParam(':hotels_star', $row['hotels_star'], PDO::PARAM_INT);
                $stmt->bindParam(':guides', $row['guides'], PDO::PARAM_STR);
                $stmt->bindParam(':num_seat', $row['num_seat'], PDO::PARAM_INT);
                $stmt->bindParam(':groups_view', $row['groups_view'], PDO::PARAM_STR);
                $stmt->bindParam(':groups_comment', $row['groups_comment'], PDO::PARAM_STR);
                $stmt->bindParam(':title', $row['title'], PDO::PARAM_STR);
                $stmt->bindParam(':alias', $row['alias'], PDO::PARAM_STR);
                $stmt->bindParam(':title_custom', $row['title_custom'], PDO::PARAM_STR);
                $stmt->bindParam(':plan', $row['plan'], PDO::PARAM_STR, strlen($row['plan']));
                $stmt->bindParam(':description', $row['description'], PDO::PARAM_STR, strlen($row['description']));
                $stmt->bindParam(':description_html', $row['description_html'], PDO::PARAM_STR, strlen($row['description_html']));
                $stmt->bindParam(':note', $row['note'], PDO::PARAM_STR, strlen($row['note']));
                $exc = $stmt->execute();
                $new_id = $row['id'];
            }
            
            if ($exc) {
                $auto_code = '';
                if (empty($row['code'])) {
                    $i = 1;
                    $format_code = ! empty($array_config['format_code']) ? $array_config['format_code'] : 'T%06s';
                    $auto_code = vsprintf($format_code, $new_id);
                    
                    $stmt = $db->prepare('SELECT id FROM ' . $table_name . ' WHERE code= :code');
                    $stmt->bindParam(':code', $auto_code, PDO::PARAM_STR);
                    $stmt->execute();
                    while ($stmt->rowCount()) {
                        $auto_code = vsprintf($format_code, ($new_id + $i ++));
                    }
                    
                    $stmt = $db->prepare('UPDATE ' . $table_name . ' SET code= :code WHERE id=' . $new_id);
                    $stmt->bindParam(':code', $auto_code, PDO::PARAM_STR);
                    $stmt->execute();
                }
                
                // luu nhom tour
                $id_block_content_new = array_diff($row['id_block_content_post'], $id_block_content);
                $id_block_content_del = array_diff($id_block_content, $row['id_block_content_post']);
                
                $array_block_fix = array();
                foreach ($id_block_content_new as $bid_i) {
                    $db->query('INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_block (bid, id, weight) VALUES (' . $bid_i . ', ' . $new_id . ', 0)');
                    $array_block_fix[] = $bid_i;
                }
                foreach ($id_block_content_del as $bid_i) {
                    $db->query('DELETE FROM ' . $db_config['prefix'] . '_' . $module_data . '_block WHERE id = ' . $new_id . ' AND bid = ' . $bid_i);
                    $array_block_fix[] = $bid_i;
                }
                
                $array_block_fix = array_unique($array_block_fix);
                foreach ($array_block_fix as $bid_i) {
                    nv_fix_block($bid_i, false);
                }
                
                // Update tags list
                if ($row['keywords'] != $row['keywords_old']) {
                    $keywords = explode(',', $row['keywords']);
                    $keywords = array_map('strip_punctuation', $keywords);
                    $keywords = array_map('trim', $keywords);
                    $keywords = array_diff($keywords, array(
                        ''
                    ));
                    $keywords = array_unique($keywords);
                    foreach ($keywords as $keyword) {
                        if (! in_array($keyword, $array_keywords_old)) {
                            $alias_i = ($module_config[$module_name]['tags_alias']) ? change_alias($keyword) : str_replace(' ', '-', $keyword);
                            $alias_i = nv_strtolower($alias_i);
                            $sth = $db->prepare('SELECT tid, alias, description, keywords FROM ' . $db_config['prefix'] . '_' . $module_data . '_tags_' . NV_LANG_DATA . ' where alias= :alias OR FIND_IN_SET(:keyword, keywords)>0');
                            $sth->bindParam(':alias', $alias_i, PDO::PARAM_STR);
                            $sth->bindParam(':keyword', $keyword, PDO::PARAM_STR);
                            $sth->execute();
                            list ($tid, $alias, $keywords_i) = $sth->fetch(3);
                            if (empty($tid)) {
                                $array_insert = array();
                                $array_insert['alias'] = $alias_i;
                                $array_insert['keyword'] = $keyword;
                                $tid = $db->insert_id("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_tags_" . NV_LANG_DATA . " (numpro, alias, description, image, keywords) VALUES (1, :alias, '', '', :keyword)", "tid", $array_insert);
                            } else {
                                if ($alias != $alias_i) {
                                    if (! empty($keywords_i)) {
                                        $keyword_arr = explode(',', $keywords_i);
                                        $keyword_arr[] = $keyword;
                                        $keywords_i2 = implode(',', array_unique($keyword_arr));
                                    } else {
                                        $keywords_i2 = $keyword;
                                    }
                                    if ($keywords_i != $keywords_i2) {
                                        $sth = $db->prepare('UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_tags_' . NV_LANG_DATA . ' SET keywords= :keywords WHERE tid =' . $tid);
                                        $sth->bindParam(':keywords', $keywords_i2, PDO::PARAM_STR);
                                        $sth->execute();
                                    }
                                }
                                $db->query('UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_tags_' . NV_LANG_DATA . ' SET numpro = numpro+1 WHERE tid = ' . $tid);
                            }

                            try {
                                $sth = $db->prepare('INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_tags_id_' . NV_LANG_DATA . ' (id, tid,  keyword) VALUES (' . $new_id . ', ' . intval($tid) . ', :keyword)');
                                $sth->bindParam(':keyword', $keyword, PDO::PARAM_STR);
                                $sth->execute();
                            } catch (PDOException $e) {
                                $sth = $db->prepare('UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_tags_id_' . NV_LANG_DATA . ' SET  keyword = :keyword WHERE id = ' . $new_id . ' AND tid=' . intval($tid));
                                $sth->bindParam(':keyword', $keyword, PDO::PARAM_STR);
                                $sth->execute();
                            }
                            unset($array_keywords_old[$tid]);
                        }
                    }
                    foreach ($array_keywords_old as $tid => $keyword) {
                        if (! in_array($keyword, $keywords)) {
                            $db->query('UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_tags_' . NV_LANG_DATA . ' SET numpro = numpro-1 WHERE tid = ' . $tid);
                            $db->query('DELETE FROM ' . $db_config['prefix'] . '_' . $module_data . '_tags_id_' . NV_LANG_DATA . ' WHERE id = ' . $new_id . ' AND tid=' . $tid);
                        }
                    }
                }
                
                $nv_Cache->delMod($module_name);
                Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=main');
                die();
            }
        } catch (PDOException $e) {
            trigger_error($e->getMessage());
            die($e->getMessage()); // Remove this line after checks finished
        }
    }
    $id_block_content = $row['id_block_content_post'];
}

if (empty($row['date_start'])) {
    $row['date_start'] = '';
} else {
    $row['date_start'] = date('d/m/Y', $row['date_start']);
}
if (! empty($row['homeimgfile']) and is_file(NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $row['homeimgfile'])) {
    $row['homeimgfile'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $row['homeimgfile'];
}

if (defined('NV_EDITOR')) {
    require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
}
$row['description_html'] = htmlspecialchars(nv_editor_br2nl($row['description_html']));
$row['plan'] = htmlspecialchars(nv_editor_br2nl($row['plan']));
$row['note'] = htmlspecialchars(nv_editor_br2nl($row['note']));
if (defined('NV_EDITOR') and nv_function_exists('nv_aleditor')) {
    $row['description_html'] = nv_aleditor('description_html', '100%', '200px', $row['description_html'], 'Basic');
    $row['plan'] = nv_aleditor('plan', '100%', '300px', $row['plan']);
    $row['note_editor'] = nv_aleditor('note', '100%', '300px', $row['note'], 'Basic');
} else {
    $row['description_html'] = '<textarea style="width:100%;height:300px" name="description_html">' . $row['description_html'] . '</textarea>';
    $row['plan'] = '<textarea style="width:100%;height:300px" name="plan">' . $row['plan'] . '</textarea>';
    $row['note_editor'] = '<textarea style="width:100%;height:300px" name="note">' . $row['note'] . '</textarea>';
}

$row['allowed_rating_ck'] = $row['allowed_rating'] ? 'checked="checked"' : '';
$row['show_price_ck'] = $row['show_price'] ? 'checked="checked"' : '';
$row['num_day'] = ! empty($row['num_day']) ? $row['num_day'] : '';
$row['num_seat'] = ! empty($row['num_seat']) ? $row['num_seat'] : '';

$flying_time = array(
    'id' => 0,
    'time' => 0,
    'code' => ''
);
$row['flying_begin'] = ! empty($row['flying_begin']) ? unserialize($row['flying_begin']) : $flying_time;
$row['flying_begin']['time'] = ! empty($row['flying_begin']['time']) ? nv_date('d/m/Y H:i', $row['flying_begin']['time']) : '';
$row['flying_end'] = ! empty($row['flying_end']) ? unserialize($row['flying_end']) : $flying_time;
$row['flying_end']['time'] = ! empty($row['flying_end']['time']) ? nv_date('d/m/Y H:i', $row['flying_end']['time']) : '';

$hotels_info[] = array(
    'id' => 0,
    'time' => ''
);
$row['hotels_info'] = ! empty($row['hotels_info']) ? unserialize($row['hotels_info']) : $hotels_info;

$array_block_cat_module = array();
$sql = 'SELECT bid, adddefault, ' . NV_LANG_DATA . '_title title FROM ' . $db_config['prefix'] . '_' . $module_data . '_block_cat ORDER BY weight ASC';
$result = $db->query($sql);
while (list ($bid_i, $adddefault_i, $title_i) = $result->fetch(3)) {
    $array_block_cat_module[$bid_i] = $title_i;
    if ($adddefault_i) {
        $id_block_content[] = $bid_i;
    }
}
$id_block_content = array_unique($id_block_content);

$_sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_flying WHERE status=1 ORDER BY weight ASC';
$array_flying = $nv_Cache->db($_sql, 'id', $module_name);

$_sql = 'SELECT id, ' . NV_LANG_DATA . '_title title FROM ' . $db_config['prefix'] . '_' . $module_data . '_hotels WHERE status=1 ORDER BY ' . NV_LANG_DATA . '_title';
$array_hotels = $nv_Cache->db($_sql, 'id', $module_name);

$_sql = 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_guides WHERE status=1 ORDER BY first_name';
$array_guides = $nv_Cache->db($_sql, 'id', $module_name);

$_sql = 'SELECT id, ' . NV_LANG_DATA . '_title title FROM ' . $db_config['prefix'] . '_' . $module_data . '_services WHERE status=1 ORDER BY weight';
$array_services = $nv_Cache->db($_sql, 'id', $module_name);

$_sql = 'SELECT id, ' . NV_LANG_DATA . '_title title FROM ' . $db_config['prefix'] . '_' . $module_data . '_vehicle WHERE status=1 ORDER BY weight';
$array_vehicle = $nv_Cache->db($_sql, 'id', $module_name);

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('MODULE_UPLOAD', $module_upload);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);
$xtpl->assign('TEMPLATE', $global_config['module_theme']);
$xtpl->assign('CURRENTPATH', $currentpath);

if (! empty($array_cat)) {
    foreach ($array_cat as $catid => $value) {
        $value['space'] = '';
        if ($value['lev'] > 0) {
            for ($i = 1; $i <= $value['lev']; $i ++) {
                $value['space'] .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            }
        }
        $value['selected'] = $catid == $row['catid'] ? ' selected="selected"' : '';
        
        $xtpl->assign('CAT', $value);
        $xtpl->parse('main.cat');
    }
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

$groups_comment = explode(',', $row['groups_comment']);
foreach ($groups_list as $group_id => $grtl) {
    $_groups_comment = array(
        'value' => $group_id,
        'checked' => in_array($group_id, $groups_comment) ? ' checked="checked"' : '',
        'title' => $grtl
    );
    $xtpl->assign('GROUPS_COMMENT', $_groups_comment);
    $xtpl->parse('main.groups_comment');
}

$provinceid = ! empty($row['place_start']) ? $row['place_start'] : 0;
$data_config = array(
    'select_countyid' => nv_location_get_countryid_from_province($provinceid),
    'select_provinceid' => $provinceid,
    'name_province' => 'place_start',
    'index' => 0
);
$xtpl->assign('PLACE_START', nv_location_build_input($data_config));

$provinceid = ! empty($row['place_end']) ? $row['place_end'] : 0;
$data_config = array(
    'select_countyid' => nv_location_get_countryid_from_province($provinceid),
    'select_provinceid' => $provinceid,
    'name_province' => 'place_end',
    'index' => 1
);
$xtpl->assign('PLACE_END', nv_location_build_input($data_config));

if (! $array_config['allow_auto_code']) {
    $xtpl->parse('main.code_required_star');
    $xtpl->parse('main.code_required_attr');
}

if (! empty($array_flying)) {
    foreach ($array_flying as $flying) {
        $flying['selected_begin'] = $flying['id'] == $row['flying_begin']['id'] ? 'selected="selected"' : '';
        $flying['selected_end'] = $flying['id'] == $row['flying_end']['id'] ? 'selected="selected"' : '';
        $xtpl->assign('FLYING', $flying);
        $xtpl->parse('main.flying_begin');
        $xtpl->parse('main.flying_end');
    }
}

if (! empty($row['flying_begin']['id']) or ! empty($row['flying_begin']['time']) or ! empty($row['flying_begin']['code']) or ! empty($row['flying_end']['id']) or ! empty($row['flying_end']['time']) or ! empty($row['flying_end']['code'])) {
    $xtpl->parse('main.flying_collapse_in');
}

if (empty($row['hotels_info'])) {
    $row['hotels_info'] = $hotels_info;
} elseif (! empty($row['hotels_info'][0]['id'])) {
    $xtpl->parse('main.hotels_collapse_in');
}

if (! empty($row['note'])) {
    $xtpl->parse('main.note_collapse_in');
}

$i = 0;
if (! empty($row['hotels_info'])) {
    foreach ($row['hotels_info'] as $hotels_info) {
        $hotels_info['index'] = $i;
        $hotels_info['time_begin'] = ! empty($hotels_info['time_begin']) ? nv_date('d/m/Y H:i', $hotels_info['time_begin']) : '';
        $hotels_info['time_end'] = ! empty($hotels_info['time_end']) ? nv_date('d/m/Y H:i', $hotels_info['time_end']) : '';
        $hotels_info['time'] = $hotels_info['time_begin'] . ' - ' . $hotels_info['time_end'];
        $xtpl->assign('HOTELS_INFO', $hotels_info);
        if (! empty($array_hotels)) {
            foreach ($array_hotels as $hotels) {
                $hotels['selected'] = $hotels['id'] == $hotels_info['id'] ? 'selected="selected"' : '';
                $xtpl->assign('HOTELS', $hotels);
                $xtpl->parse('main.hotels_info.hotels');
            }
        }
        
        $xtpl->parse('main.hotels_info');
        $i ++;
    }
}
$xtpl->assign('NUMHOTELS', $i);

if (! empty($array_hotels)) {
    foreach ($array_hotels as $hotels) {
        $xtpl->assign('HOTELS', $hotels);
        $xtpl->parse('main.hotels_js');
    }
}

if (sizeof($array_block_cat_module)) {
    foreach ($array_block_cat_module as $bid_i => $bid_title) {
        $xtpl->assign('BLOCKS', array(
            'title' => $bid_title,
            'bid' => $bid_i,
            'checked' => in_array($bid_i, $id_block_content) ? 'checked="checked"' : ''
        ));
        $xtpl->parse('main.block_cat.loop');
    }
    $xtpl->parse('main.block_cat');
}

if (! empty($array_guides)) {
    $row['guides'] = ! empty($row['guides']) ? unserialize($row['guides']) : array();
    foreach ($array_guides as $guides) {
        $guides['checked'] = in_array($guides['id'], $row['guides']) ? 'checked="checked"' : '';
        $guides['birthday'] = ! empty($guides['birthday']) ? nv_date('d/m/Y', $guides['birthday']) : '';
        $guides['gender'] = $lang_module['gender_' . $guides['gender']];
        $xtpl->assign('GUIDES', $guides);
        if (! empty($guides['checked'])) {
            $xtpl->parse('main.guides.loop.success');
        }
        $xtpl->parse('main.guides.loop');
    }
    if (! empty($row['guides'])) {
        $xtpl->parse('main.guides.guides_collapse_in');
    }
    $xtpl->parse('main.guides');
}

if (! empty($array_services)) {
    $row['services'] = ! empty($row['services']) ? unserialize($row['services']) : array();
    foreach($array_services as $services){
        $services['checked'] = in_array($services['id'], $row['services']) ? 'checked="checked"' : '';
        $xtpl->assign('SERVICES', $services);
        $xtpl->parse('main.services.loop');
    }
    if (! empty($row['services'])) {
        $xtpl->parse('main.services.services_collapse_in');
    }
    $xtpl->parse('main.services');
}

if (! empty($array_vehicle)) {
    foreach($array_vehicle as $vehicle){
        $vehicle['checked'] = $vehicle['id'] == $row['vehicle'] ? 'checked="checked"' : '';
        $xtpl->assign('VEHICLE', $vehicle);
        $xtpl->parse('main.vehicle.loop');
    }
    if (! empty($row['vehicle'])) {
        $xtpl->parse('main.vehicle.vehicle_collapse_in');
    }
    $xtpl->parse('main.vehicle');
}

if (! empty($row['keywords'])) {
    $keywords_array = explode(',', $row['keywords']);
    foreach ($keywords_array as $keywords) {
        $xtpl->assign('KEYWORDS', $keywords);
        $xtpl->parse('main.keywords');
    }
}

foreach ($array_date_start_method as $index => $value) {
    $xtpl->assign('DATE_START_METHOD', array(
        'index' => $index,
        'value' => $value,
        'selected' => $row['date_start_method'] == $index ? 'selected="selected"' : ''
    ));
    $xtpl->parse('main.date_start_method');
}
$xtpl->assign('DATE_START_METHOD_DISPLAY_' . $row['date_start_method'], 'style="display: block"');

$row['date_start_config'] = (in_array($row['date_start_method'], array(1, 2)) and ! empty($row['date_start_config'])) ? unserialize($row['date_start_config']) : array();
foreach ($array_day_week as $index => $value) {
    $xtpl->assign('DAY_WEEK', array(
        'index' => $index,
        'value' => $value,
        'selected' => in_array($index, $row['date_start_config']) ? 'selected="selected"' : ''
    ));
    $xtpl->parse('main.day_week');
}

for ($i = 1; $i <= 31; $i ++) {
    $sl = in_array($i, $row['date_start_config']) ? 'selected="selected"' : '';
    $xtpl->assign('DAY_MONTH', array(
        'index' => $i,
        'selected' => $sl
    ));
    $xtpl->parse('main.day_month');
}

if (! empty($error)) {
    $xtpl->assign('ERROR', implode('<br />', $error));
    $xtpl->parse('main.error');
}
if (empty($row['id'])) {
    $xtpl->parse('main.auto_get_alias');
} else {
    $xtpl->parse('main.loadprice');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['content'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
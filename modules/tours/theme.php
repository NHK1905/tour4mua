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

/**
 * nv_theme_tours_main()
 *
 * @param mixed $array_data            
 * @return
 *
 */
function nv_theme_tours_main($array_data, $viewtype, $page)
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op;
    
    $xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    
    if (nv_function_exists('nv_theme_tours_' . $viewtype)) {
        $xtpl->assign('DATA', call_user_func('nv_theme_tours_' . $viewtype, $array_data));
    } else {
        return '';
    }
    
    if (! empty($page)) {
        $xtpl->assign('PAGE', $page);
        $xtpl->parse('main.page');
    }
    
    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_tours_main_cat()
 *
 * @param mixed $array_data            
 * @return
 *
 */
function nv_theme_tours_main_cat($array_data)
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op;
    
    $xtpl = new XTemplate('main_cat.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    if (! empty($array_data)) {
        foreach ($array_data as $catid => $catinfo) {
            if($catinfo['counttour'] > 0){
                $xtpl->assign('CAT', $catinfo);
                if (nv_function_exists('nv_theme_tours_' . $catinfo['viewtype'])) {
                    $xtpl->assign('TOURS', call_user_func('nv_theme_tours_' . $catinfo['viewtype'], $catinfo['tours']));
                }
                
                if($catinfo['counttour'] > $catinfo['numlinks']){
                    $xtpl->parse('main.cat.viewall');
                }
                
                $xtpl->parse('main.cat');
            }
        }
    }
    
    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_tours_viewcat()
 *
 * @param mixed $array_data            
 * @param mixed $viewtype            
 * @return
 *
 */
function nv_theme_tours_viewcat($array_data, $viewtype, $page = '')
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $array_config, $catid, $array_cat, $module_upload;
    
    $is_image = 0;
    if (! empty($array_cat[$catid]['image'])) {
        $array_cat[$catid]['image'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $array_cat[$catid]['image'];
        $is_image = 1;
    }
    
    $xtpl = new XTemplate('viewcat.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('CAT_INFO', $array_cat[$catid]);
    
    if (nv_function_exists('nv_theme_tours_' . $viewtype)) {
        $xtpl->assign('DATA', call_user_func('nv_theme_tours_' . $viewtype, $array_data));
    } else {
        return '';
    }
    
    if ($is_image) {
        $xtpl->parse('main.image');
    }
    
    if (! empty($array_cat[$catid]['description_html'])) {
        $xtpl->parse('main.description');
    }
    
    if (! empty($page)) {
        $xtpl->assign('PAGE', $page);
        $xtpl->parse('main.page');
    }
    
    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_tours_viewgroups()
 *
 * @param mixed $groups_data            
 * @param mixed $array_data            
 * @param mixed $viewtype            
 * @return
 *
 */
function nv_theme_tours_viewgroups($groups_data, $array_data, $viewtype, $page = '')
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $array_config, $catid, $array_cat, $module_upload;
    
    $is_image = 0;
    if (! empty($groups_data['image'])) {
        $groups_data['image'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $groups_data['image'];
        $is_image = 1;
    }
    
    $xtpl = new XTemplate('viewgroups.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('GROUPS', $groups_data);
    
    if (nv_function_exists('nv_theme_tours_' . $viewtype)) {
        $xtpl->assign('DATA', call_user_func('nv_theme_tours_' . $viewtype, $array_data));
    } else {
        return '';
    }
    
    if ($is_image) {
        $xtpl->parse('main.image');
    }
    
    if (! empty($groups_data['description'])) {
        $xtpl->parse('main.description');
    }
    
    if (! empty($page)) {
        $xtpl->assign('PAGE', $page);
        $xtpl->parse('main.page');
    }
    
    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_tours_viewtag()
 *
 * @param mixed $array_data            
 * @param mixed $viewtype            
 * @return
 *
 */
function nv_theme_tours_viewtag($array_data, $viewtype, $page = '')
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $array_config, $catid, $array_cat, $module_upload;
    
    $xtpl = new XTemplate('viewtag.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    
    if (nv_function_exists('nv_theme_tours_' . $viewtype)) {
        $xtpl->assign('DATA', call_user_func('nv_theme_tours_' . $viewtype, $array_data));
    } else {
        return '';
    }
    
    if (! empty($page)) {
        $xtpl->assign('PAGE', $page);
        $xtpl->parse('main.page');
    }
    
    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_tours_viewgrid()
 *
 * @param mixed $array_data            
 * @return
 *
 */
function nv_theme_tours_viewgrid($array_data)
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $array_config;
    
    $xtpl = new XTemplate('viewgrid.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $home_image_size = explode('x', $array_config['home_image_size']);
    $xtpl->assign('WIDTH', $home_image_size[0]);
    $xtpl->assign('HEIGHT', $home_image_size[1]);
    
    if (! empty($array_data)) {
        foreach ($array_data as $data) {
            $data['title_clean'] = nv_clean60($data['title'], $array_config['title_lenght']);
            $xtpl->assign('ROW', $data);
            if ($data['show_price'] == '1') {
                $price = nv_get_price($data['id'], $array_config['money_unit']);
                $xtpl->assign('PRICE', $price);
                if ($data['discounts_id'] and $price['discount_percent'] > 0) {
                    $xtpl->parse('main.loop.price.discounts');
                } else {
                    $xtpl->parse('main.loop.price.no_discounts');
                }
                $xtpl->parse('main.loop.price');
            } else {
                $xtpl->parse('main.loop.contact');
            }
            
            if ($data['discounts_id'] and $data['show_price']) {
                $xtpl->parse('main.loop.discounts');
            }
            
			if(!empty($data['vehicle'])){
                $xtpl->parse('main.loop.vehicle');
            }
			
			if(!empty($data['hotel_star'])){
                for($i=1; $i<=$data['hotel_star']; $i++){
					$xtpl->parse('main.loop.star.loop');
                }
                $xtpl->parse('main.loop.star');
            }
			
            $xtpl->parse('main.loop');
        }
    }
    
    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_tours_viewlist()
 *
 * @param mixed $array_data            
 * @return
 *
 */
function nv_theme_tours_viewlist($array_data)
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $array_config;
    
    $xtpl = new XTemplate('viewlist.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $home_image_size = explode('x', $array_config['home_image_size']);
    $xtpl->assign('WIDTH', $home_image_size[0]);
    $xtpl->assign('HEIGHT', $home_image_size[1]);
    
    if (! empty($array_data)) {
        foreach ($array_data as $data) {
            $data['title_clean'] = $data['title'];
            $xtpl->assign('ROW', $data);
            if ($data['show_price'] == '1') {
                $price = nv_get_price($data['id'], $array_config['money_unit']);
                $xtpl->assign('PRICE', $price);
                if ($data['discounts_id'] and $price['discount_percent'] > 0) {
                    $xtpl->parse('main.loop.price.discounts');
                } else {
                    $xtpl->parse('main.loop.price.no_discounts');
                }
                
                if ($array_config['booking_type'] == 2) {
                    $xtpl->assign('URL_BOOKING', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['booking'] . '/' . $data['code']);
                    $xtpl->parse('main.loop.price.btn_booking');
                } elseif ($array_config['booking_type'] == 1) {
                    $xtpl->parse('main.loop.price.btn_contact');
                }
                
                $xtpl->parse('main.loop.price');
            } else {
                $xtpl->parse('main.loop.contact');
            }
			
			if(!empty($data['vehicle'])){
                $xtpl->parse('main.loop.vehicle');
            }
			
			if(!empty($data['hotel_star'])){
                for($i=1; $i<=$data['hotel_star']; $i++){
					$xtpl->parse('main.loop.star.loop');
                }
                $xtpl->parse('main.loop.star');
            }
			
            $xtpl->parse('main.loop');
        }
    }
    
    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_tours_detail()
 *
 * @param mixed $array_data
 * @param mixed $array_tour_other                        
 * @param mixed $content_comment            
 * @return
 *
 */
function nv_theme_tours_detail($tour_info, $array_tour_other, $content_comment)
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $array_config, $money_config, $client_info, $array_keyword, $array_vehicle;
    
    $xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('DATA', $tour_info);
    $xtpl->assign('TEMPLATE', $module_info['template']);
    $xtpl->assign('SELFURL', $client_info['selfurl']);
    
    if (! empty($tour_info['image'])) {
        foreach ($tour_info['image'] as $image) {
            $xtpl->assign('IMAGE', $image);
            if (! empty($image['description'])) {
                $xtpl->parse('main.image.loop.description');
            }
            $xtpl->parse('main.image.loop');
            $xtpl->parse('main.image.loop_thumb');
        }
        $xtpl->parse('main.image');
    }
    
    if(!empty($tour_info['vehicle'])){
        $xtpl->parse('main.vehicle');
    }
    
    if ($tour_info['show_price'] == '1') {
        $price = nv_get_price($tour_info['id'], $array_config['money_unit']);
        $xtpl->assign('PRICE', $price);
        if ($tour_info['discounts_id'] and $price['discount_percent'] > 0) {
            $xtpl->parse('main.price.discounts');
        } else {
            $xtpl->parse('main.price.no_discounts');
        }
        
        if ($array_config['booking_type'] == 2) {
            $xtpl->assign('URL_BOOKING', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['booking'] . '/' . $tour_info['code']);
            $xtpl->parse('main.btn_booking2');
            $xtpl->parse('main.price_plan.btn_booking');
        } elseif ($array_config['booking_type'] == 1) {
            $xtpl->parse('main.price.btn_contact');
        }
        $xtpl->parse('main.price');
        $xtpl->parse('main.price_plan');
    } else {
        $xtpl->parse('main.contact');
    }
    
    if (! empty($tour_info['description_html'])) {
        $xtpl->parse('main.description_html');
    }
    
    $flying_begin = $tour_info['flying_begin'];
    $is_flying_begin = 0;
    if (! empty($flying_begin['id']) or ! empty($flying_begin['id']['code']) or ! empty($flying_begin['id']['time'])) {
        $is_flying_begin = 1;
        $xtpl->parse('main.flying.flying_begin');
    }
    
    $flying_end = $tour_info['flying_end'];
    $is_flying_end = 0;
    if (! empty($flying_end['id']) or ! empty($flying_end['id']['code']) or ! empty($flying_end['id']['time'])) {
        $is_flying_end = 1;
        $xtpl->parse('main.flying.flying_end');
    }
    
    if ($is_flying_begin or $is_flying_end) {
        $xtpl->parse('main.flying');
    }

    if (! empty($tour_info['services'])) {
        foreach ($tour_info['services'] as $services) {
            $xtpl->assign('SERVICES', $services);
            $xtpl->parse('main.services.loop');
        }
        $xtpl->parse('main.services');
    }
    
    if (! empty($tour_info['hotels_info'])) {
        foreach ($tour_info['hotels_info'] as $hotel) {
            $hotel['star_str'] = $lang_module['hotels_type_' . $hotel['star']];
            $xtpl->assign('HOTEL', $hotel);
            if($hotel['star'] > 0){
                for($i=1; $i<=$hotel['star']; $i++){
                    $xtpl->parse('main.hotel.loop.star');
                }
            }
            $xtpl->parse('main.hotel.loop');
        }
        $xtpl->parse('main.hotel');
    }
    
    if (! empty($tour_info['guides'])) {
        foreach ($tour_info['guides'] as $guides) {
            $xtpl->assign('GUIDES', $guides);
            $xtpl->parse('main.guides.loop');
        }
        $xtpl->parse('main.guides');
    }
    
    if ($tour_info['show_price']) {
        $xtpl->assign('MONEY', $money_config[$array_config['money_unit']]);
        $price_method = $tour_info['price_method'];
        $subprice = $tour_info['subprice'];
        if ($price_method == 0) {
            $price = isset($tour_info['price_config']['price'][0]) ? $tour_info['price_config']['price'][0] : 0;
            $xtpl->assign('PRICE', ! empty($price) ? nv_get_price($tour_info['id'], $array_config['money_unit'], $price) : '');
            if (! empty($subprice)) {
                foreach ($subprice as $subprice_id => $_subprice) {
                    $price = isset($tour_info['price_config']['subprice'][$subprice_id]) ? $tour_info['price_config']['subprice'][$subprice_id] : 0;
                    $_subprice['price'] = ! empty($price) ? nv_get_price($tour_info['id'], $array_config['money_unit'], $price) : '';
                    $xtpl->assign('SUBPRICE', $_subprice);
                    $xtpl->parse('main.tab_price_content.price_method_0.subprice.loop');
                }
                $xtpl->parse('main.tab_price_content.price_method_0.subprice');
            }
            $xtpl->parse('main.tab_price_content.price_method_0');
        } elseif ($price_method == 1) {
            $array_config['age_config'] = ! empty($array_config['age_config']) ? unserialize($array_config['age_config']) : array();
            foreach ($array_config['age_config'] as $index => $value) {
                $xtpl->assign('TITLE', $value);
                $price = isset($tour_info['price_config']['price'][$index]) ? $tour_info['price_config']['price'][$index] : 0;
                $xtpl->assign('PRICE', ! empty($price) ? nv_get_price($tour_info['id'], $array_config['money_unit'], $price) : '');
                $xtpl->parse('main.tab_price_content.price_method_1.price');
                $xtpl->parse('main.tab_price_content.price_method_1.title');
            }
            
            if (! empty($subprice)) {
                foreach ($subprice as $subprice_id => $_subprice) {
                    foreach ($array_config['age_config'] as $index => $value) {
                        $_subprice['key'] = $index;
                        $_subprice['title_clean'] = nv_clean60($_subprice['title'], 25);
                        $price = isset($tour_info['price_config']['subprice'][$subprice_id][$index]) ? $tour_info['price_config']['subprice'][$subprice_id][$index] : 0;
                        $_subprice['price'] = ! empty($price) ? nv_get_price($tour_info['id'], $array_config['money_unit'], $price) : '-';
                        $xtpl->assign('SUBPRICE', $_subprice);
                        $xtpl->parse('main.tab_price_content.price_method_1.subprice.loop');
                    }
                    $xtpl->parse('main.tab_price_content.price_method_1.subprice');
                }
            }
            $xtpl->parse('main.tab_price_content.price_method_1');
        } elseif ($price_method == 2) {
            $array_config['age_config'] = ! empty($array_config['age_config']) ? unserialize($array_config['age_config']) : array();
            foreach ($array_config['age_config'] as $index => $value) {
                $xtpl->assign('TITLE', $value);
                for ($i = 0; $i < 3; $i ++) {
                    $xtpl->assign('KEY', array(
                        'i' => $i,
                        'j' => $index
                    ));
                    $price = isset($tour_info['price_config']['price'][$index][$i]) ? $tour_info['price_config']['price'][$index][$i] : 0;
                    $xtpl->assign('PRICE', ! empty($price) ? nv_get_price($tour_info['id'], $array_config['money_unit'], $price) : '-');
                    $xtpl->parse('main.tab_price_content.price_method_2.price.loop');
                }
                $xtpl->parse('main.tab_price_content.price_method_2.price');
            }
            
            if (! empty($subprice)) {
                foreach ($subprice as $subprice_id => $_subprice) {
                    for ($i = 0; $i < 3; $i ++) {
                        $_subprice['key'] = $i;
                        $_subprice['title_clean'] = nv_clean60($_subprice['title'], 30);
                        $price = isset($tour_info['price_config']['subprice'][$subprice_id][$i]) ? $tour_info['price_config']['subprice'][$subprice_id][$i] : 0;
                        $_subprice['price'] = ! empty($price) ? nv_get_price($tour_info['id'], $array_config['money_unit'], $price) : '-';
                        $xtpl->assign('SUBPRICE', $_subprice);
                        $xtpl->parse('main.tab_price_content.price_method_2.subprice.loop');
                    }
                    $xtpl->parse('main.tab_price_content.price_method_2.subprice');
                }
            }
            $xtpl->parse('main.tab_price_content.price_method_2');
        }
        $xtpl->parse('main.tab_price_title');
        $xtpl->parse('main.tab_price_content');
    }
    
    if (! empty($content_comment)) {
        $xtpl->assign('COMMENT', $content_comment);
        $xtpl->parse('main.comment');
    }
    
    if ($array_config['booking_type'] == 2) {
        $xtpl->assign('URL_BOOKING', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['booking'] . '/' . $tour_info['code']);
        $xtpl->parse('main.btn_booking');
    } elseif ($array_config['booking_type'] == 1) {
        $xtpl->parse('main.btn_contact');
    }
    
    if (defined('NV_IS_MODADMIN')) {
        $xtpl->assign('URL_EDIT', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=content&id=' . $tour_info['id']);
        $xtpl->parse('main.admin_control');
    }
    
    if (! empty($array_config['contact_info'])) {
        $xtpl->assign('CONTACT_INFO', $array_config['contact_info']);
        $xtpl->parse('main.contact_info_title');
        $xtpl->parse('main.contact_info_content');
    }
    
    $note_content = !empty($tour_info['note']) ? $tour_info['note'] : $array_config['note_content'];
    if (!empty($note_content)) {
        $xtpl->assign('NOTE', $note_content);
        $xtpl->parse('main.note_content_title');
        $xtpl->parse('main.note_content_content');
    }
    
    if (! empty($array_keyword)) {
        $t = sizeof($array_keyword) - 1;
        foreach ($array_keyword as $i => $value) {
            $xtpl->assign('KEYWORD', $value['keyword']);
            $xtpl->assign('LINK_KEYWORDS', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=tag/' . urlencode($value['alias']));
            $xtpl->assign('SLASH', ($t == $i) ? '' : ', ');
            $xtpl->parse('main.keywords.loop');
        }
        $xtpl->parse('main.keywords');
    }
    
    if(!empty($array_tour_other)){
        $xtpl->assign('TOUR_OTHER', nv_theme_tours_viewlist($array_tour_other));
        $xtpl->parse('main.tour_other');
    }
    
    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_tours_search()
 *
 * @param mixed $array_data            
 * @param mixed $viewtype            
 * @param mixed $page            
 * @return
 *
 */
function nv_theme_tours_search($array_data, $is_search, $viewtype, $page)
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op;
    
    $xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);

    if ($is_search) {
        if (! empty($array_data)) {
            if (nv_function_exists('nv_theme_tours_' . $viewtype)) {
                $xtpl->assign('DATA', call_user_func('nv_theme_tours_' . $viewtype, $array_data));
            } else {
                return '';
            }
            
            if (! empty($page)) {
                $xtpl->assign('PAGE', $page);
                $xtpl->parse('main.result.page');
            }
			
			if(!empty($data['vehicle'])){
                $xtpl->parse('main.loop.vehicle');
            }
			
			if(!empty($data['hotel_star'])){
                for($i=1; $i<=$data['hotel_star']; $i++){
					$xtpl->parse('main.loop.star.loop');
                }
                $xtpl->parse('main.loop.star');
            }
            $xtpl->parse('main.result');
        } else {
            $xtpl->parse('main.result_empty');
        }
    } else {
        $xtpl->parse('main.empty');
    }
    
    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_tours_booking()
 *
 * @param mixed $tour_info            
 * @param mixed $array_booking            
 * @return
 *
 */
function nv_theme_tours_booking($tour_info, $array_booking)
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $array_config, $money_config, $array_gender, $array_customer_type, $_array_payment_method, $array_payment_method;
    
    $xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('TOUR_INFO', $tour_info);
    $xtpl->assign('BOOKING_INFO', $array_booking);
    $xtpl->assign('MONEY', $money_config[$array_config['money_unit']]);
    $xtpl->assign('MODULE_NAME', $module_name);
    $xtpl->assign('OP', $op);
    
    $price_method = $tour_info['price_method'];
    $subprice = $tour_info['subprice'];
    $decimals = nv_get_decimals($array_config['money_unit']);
    $pricetotal = 0;
    
    if (in_array($price_method, array(
        1,
        2
    ))) {
        $array_config['age_config'] = ! empty($array_config['age_config']) ? unserialize($array_config['age_config']) : array();
    }
    
    if ($tour_info['show_price']) {
        if ($price_method == 0) {
            $price = isset($tour_info['price_config']['price'][0]) ? $tour_info['price_config']['price'][0] : 0;
            $xtpl->assign('PRICE', ! empty($price) ? nv_get_price($tour_info['id'], $array_config['money_unit'], $price) : '');
            if (! empty($subprice)) {
                foreach ($subprice as $subprice_id => $_subprice) {
                    $price = isset($tour_info['price_config']['subprice'][$subprice_id]) ? $tour_info['price_config']['subprice'][$subprice_id] : 0;
                    $_subprice['price'] = ! empty($price) ? nv_get_price($tour_info['id'], $array_config['money_unit'], $price) : '';
                    $xtpl->assign('SUBPRICE', $_subprice);
                    $xtpl->parse('main.price_table.price_method_0.subprice.loop');
                }
                $xtpl->parse('main.price_table.price_method_0.subprice');
            }
            $xtpl->parse('main.price_table.price_method_0');
        } elseif ($price_method == 1) {
            foreach ($array_config['age_config'] as $index => $value) {
                $xtpl->assign('TITLE', $value);
                $price = isset($tour_info['price_config']['price'][$index]) ? $tour_info['price_config']['price'][$index] : 0;
                $xtpl->assign('PRICE', ! empty($price) ? nv_get_price($tour_info['id'], $array_config['money_unit'], $price) : '');
                $xtpl->parse('main.price_table.price_method_1.price');
                $xtpl->parse('main.price_table.price_method_1.title');
            }
            
            if (! empty($subprice)) {
                foreach ($subprice as $subprice_id => $_subprice) {
                    foreach ($array_config['age_config'] as $index => $value) {
                        $_subprice['key'] = $index;
                        $_subprice['title_clean'] = nv_clean60($_subprice['title'], 25);
                        $price = isset($tour_info['price_config']['subprice'][$subprice_id][$index]) ? $tour_info['price_config']['subprice'][$subprice_id][$index] : 0;
                        $_subprice['price'] = ! empty($price) ? nv_get_price($tour_info['id'], $array_config['money_unit'], $price) : '-';
                        $xtpl->assign('SUBPRICE', $_subprice);
                        $xtpl->parse('main.price_table.price_method_1.subprice.loop');
                    }
                    $xtpl->parse('main.price_table.price_method_1.subprice');
                }
            }
            $xtpl->parse('main.price_table.price_method_1');
        } elseif ($price_method == 2) {
            foreach ($array_config['age_config'] as $index => $value) {
                $xtpl->assign('TITLE', $value);
                for ($i = 0; $i < 3; $i ++) {
                    $xtpl->assign('KEY', array(
                        'i' => $i,
                        'j' => $index
                    ));
                    $price = isset($tour_info['price_config']['price'][$index][$i]) ? $tour_info['price_config']['price'][$index][$i] : 0;
                    $xtpl->assign('PRICE', ! empty($price) ? nv_get_price($tour_info['id'], $array_config['money_unit'], $price) : '-');
                    $xtpl->parse('main.price_table.price_method_2.price.loop');
                }
                $xtpl->parse('main.price_table.price_method_2.price');
            }
            
            if (! empty($subprice)) {
                foreach ($subprice as $subprice_id => $_subprice) {
                    for ($i = 0; $i < 3; $i ++) {
                        $_subprice['key'] = $i;
                        $_subprice['title_clean'] = nv_clean60($_subprice['title'], 30);
                        $price = isset($tour_info['price_config']['subprice'][$subprice_id][$i]) ? $tour_info['price_config']['subprice'][$subprice_id][$i] : 0;
                        $_subprice['price'] = ! empty($price) ? nv_get_price($tour_info['id'], $array_config['money_unit'], $price) : '-';
                        $xtpl->assign('SUBPRICE', $_subprice);
                        $xtpl->parse('main.price_table.price_method_2.subprice.loop');
                    }
                    $xtpl->parse('main.price_table.price_method_2.subprice');
                }
            }
            $xtpl->parse('main.price_table.price_method_2');
        }
        $xtpl->parse('main.price_table');
    }
    
    if ($array_config['booking_price_method'] == 1) {
        if (! empty($array_booking['customerprice'])) {
            $number = 1;
            $colspan = 5;
            foreach ($array_booking['customerprice'] as $_customerprice) {
                $_customerprice['number'] = $number;
                $_customerprice['index'] = $number - 1;
                
                $priceunit = nv_customer_price($tour_info['id'], $_customerprice['age'], $_customerprice['type']);
                $_customerprice['priceunit'] = $priceunit;
                $_customerprice['priceunit_format'] = nv_number_format($priceunit, $decimals);
                $price = $priceunit * $_customerprice['quantity'];
                $_customerprice['price'] = $price;
                $_customerprice['price_format'] = nv_number_format($price, $decimals);
                $pricetotal += $price;
                
                $xtpl->assign('CUSTOMERPRICE', $_customerprice);
                if ($price_method != 0) {
                    $colspan ++;
                    foreach ($array_config['age_config'] as $index => $value) {
                        $xtpl->assign('AGE', array(
                            'index' => $index,
                            'value' => $value['name'],
                            'selected' => $index == $_customerprice['age'] ? 'selected="selected"' : ''
                        ));
                        $xtpl->parse('main.tour_price_caculate.loop.age_tbody.loop');
                        $xtpl->parse('main.tour_price_caculate_js.age_js.loop');
                    }
                    $xtpl->parse('main.tour_price_caculate_js.age_js');
                    $xtpl->parse('main.tour_price_caculate.loop.age_tbody');
                    $xtpl->parse('main.tour_price_caculate.age_thead');
                }
                
                if ($price_method == 1) {
                    //
                } elseif ($price_method == 2) {
                    $colspan ++;
                    foreach ($array_customer_type as $index => $value) {
                        $xtpl->assign('CUS_TYPE', array(
                            'index' => $index,
                            'value' => $value,
                            'selected' => $index == $_customerprice['type'] ? 'selected="selected"' : ''
                        ));
                        $xtpl->parse('main.tour_price_caculate.loop.customer_type_tbody.loop');
                        $xtpl->parse('main.tour_price_caculate_js.customer_type_js.loop');
                    }
                    $xtpl->parse('main.tour_price_caculate.customer_type_thead');
                    $xtpl->parse('main.tour_price_caculate.loop.customer_type_tbody');
                    $xtpl->parse('main.tour_price_caculate_js.customer_type_js');
                }
                if ($number == 1) {
                    $xtpl->parse('main.tour_price_caculate.loop.btn_remove_disabled');
                }
                $xtpl->parse('main.tour_price_caculate.loop');
                $number ++;
            }
            $xtpl->assign('PRICRTOTAL', nv_number_format($pricetotal, $decimals));
            $xtpl->assign('COLSPAN', $colspan);
            
            if ($array_config['coupons']) {
                $xtpl->parse('main.tour_price_caculate.coupons');
            }
            
            $xtpl->parse('main.tour_price_caculate');
            $xtpl->parse('main.tour_price_caculate_js');
        }
    } else {
        if (! empty($array_booking['customer'])) {
            $customer = $array_booking['customer'];
            $number = 1;
            $colspan = 8;
            foreach ($customer as $_customer) {
                $_customer['number'] = $number;
                $_customer['index'] = $number - 1;
                
                $price = nv_customer_price($tour_info['id'], $_customer['age'], $_customer['customer_type'], $_customer['optional']);
                $_customer['price'] = $price;
                $_customer['price_format'] = nv_number_format($price, $decimals);
                $pricetotal += $price;
                
                $xtpl->assign('CUSTOMER', $_customer);
                
                if ($price_method == 0) {
                    //
                } elseif ($price_method == 1) {
                    $colspan ++;
                    foreach ($array_config['age_config'] as $index => $value) {
                        $xtpl->assign('AGE', array(
                            'index' => $index,
                            'value' => $value['name']
                        ));
                        $xtpl->parse('main.customer.loop.age_tbody.loop');
                        $xtpl->parse('main.customer_js.age_js.loop');
                    }
                    $xtpl->parse('main.customer.age_thead');
                    $xtpl->parse('main.customer.loop.age_tbody');
                    $xtpl->parse('main.customer_js.age_js');
                } elseif ($price_method == 2) {
                    $colspan ++;
                    foreach ($array_config['age_config'] as $index => $value) {
                        $xtpl->assign('AGE', array(
                            'index' => $index,
                            'value' => $value['name']
                        ));
                        $xtpl->parse('main.customer.loop.age_tbody.loop');
                        $xtpl->parse('main.customer_js.age_js.loop');
                    }
                    $xtpl->parse('main.customer.age_thead');
                    $xtpl->parse('main.customer.loop.age_tbody');
                    $xtpl->parse('main.customer_js.age_js');
                    
                    foreach ($array_customer_type as $index => $value) {
                        $xtpl->assign('CUS_TYPE', array(
                            'index' => $index,
                            'value' => $value
                        ));
                        $xtpl->parse('main.customer.loop.customer_type_tbody.loop');
                        $xtpl->parse('main.customer_js.customer_type_js.loop');
                    }
                    $xtpl->parse('main.customer.customer_type_thead');
                    $xtpl->parse('main.customer.loop.customer_type_tbody');
                    $xtpl->parse('main.customer_js.customer_type_js');
                }
                
                if (! empty($subprice)) {
                    foreach ($subprice as $subprice_id => $_subprice) {
                        if ($_subprice['is_optional']) {
                            $xtpl->assign('OPTIONAL', $_subprice);
                            $xtpl->parse('main.customer.optional_thead');
                            $xtpl->parse('main.customer.customer.loop.optional_tbody');
                            $xtpl->parse('main.customer_js.optional_js');
                        }
                    }
                }
                
                foreach ($array_gender as $index => $value) {
                    $xtpl->assign('GENDER', array(
                        'index' => $index,
                        'value' => $value,
                        'selected' => $_customer['gender'] == $index ? 'selected="selected"' : ''
                    ));
                    $xtpl->parse('main.customer.loop.gender');
                    $xtpl->parse('main.customer_js.gender_js');
                }
                
                $xtpl->parse('main.customer.loop');
                $number ++;
            }
            $xtpl->assign('PRICRTOTAL', nv_number_format($pricetotal, $decimals));
            $xtpl->assign('NUMCUSTOMER', $number - 1);
            $xtpl->assign('COLSPAN', $colspan);
        }
        
        if ($array_config['coupons']) {
            $xtpl->parse('main.customer.coupons');
        }
        
        $xtpl->parse('main.customer');
        $xtpl->parse('main.customer_js');
    }
    
    if (! empty($_array_payment_method)) {
        foreach ($_array_payment_method as $payment_method) {
            $payment_method['title'] = $array_payment_method[$payment_method['id']];
            $payment_method['description'] = $payment_method['description'];
            $payment_method['checked'] = $array_booking['payment_method'] == $payment_method['id'] ? 'checked="checked"' : '';
            $xtpl->assign('PAYMENT_METHOD', $payment_method);
            $xtpl->parse('main.payment_method.loop');
            if (! empty($payment_method['description'])) {
                $xtpl->assign('CLASS', $array_booking['payment_method'] == $payment_method['id'] ? '' : 'hidden');
                $xtpl->parse('main.payment_method.description');
            }
        }
        $xtpl->parse('main.payment_method');
    }
    
    if (! empty($array_config['rule_content'])) {
        $xtpl->assign('RULE_CONTENT', $array_config['rule_content']);
        $xtpl->parse('main.rule_content');
    }
    
    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_theme_tours_info()
 *
 * @param mixed $tour_info            
 * @param mixed $booking_info            
 * @return
 *
 */
function nv_theme_tours_info($tour_info, $booking_info, $mod = 'payment', $url_checkout = array())
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $array_config, $money_config, $array_cat, $_array_payment_method, $array_payment_method, $array_customer_type;
    
    if ($mod == 'payment') {
        $lang_module['payment_status_str'] = $lang_module['payment_status_' . $booking_info['transaction_status']];
    }
    $price_method = $tour_info['price_method'];
    $decimals = nv_get_decimals($array_config['money_unit']);
    $price_total = 0;
    
    $xtpl = new XTemplate($mod . '.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('TOUR_INFO', $tour_info);
    $xtpl->assign('BOOKING_INFO', $booking_info);
    $xtpl->assign('MONEY', $money_config[$booking_info['unit_total']]);
    $money_total = $booking_info['booking_total'] - $booking_info['coupons_value'];
    $xtpl->assign('MONEY_TOTAL', nv_number_format($money_total));
    
    $age_config = ! empty($array_config['age_config']) ? unserialize($array_config['age_config']) : array();
    if ($array_config['booking_price_method'] == 1) {
        $number = 1;
        $colspan = 5;
        
        if (! empty($booking_info['customerprice'])) {
            foreach ($booking_info['customerprice'] as $_customerprice) {
                $_customerprice['number'] = $number;
                $price_total += $_customerprice['price'];
                $_customerprice['priceunit'] = nv_number_format($_customerprice['priceunit'], $decimals);
                $_customerprice['price'] = nv_number_format($_customerprice['price'], $decimals);
                
                $xtpl->assign('CUSTOMERPRICE', $_customerprice);
                if ($price_method != 0) {
                    $colspan ++;
                    $xtpl->assign('AGE', $age_config[$_customerprice['age']]['name']);
                    $xtpl->parse('main.tour_price_caculate.loop.age_tbody');
                    $xtpl->parse('main.tour_price_caculate.age_thead');
                }
                
                if ($price_method == 1) {
                    //
                } elseif ($price_method == 2) {
                    $colspan ++;
                    $xtpl->assign('CUS_TYPE', $array_customer_type[$_customerprice['type']]);
                    $xtpl->parse('main.tour_price_caculate.customer_type_thead');
                    $xtpl->parse('main.tour_price_caculate.loop.customer_type_tbody');
                }
                
                $xtpl->parse('main.tour_price_caculate.loop');
                $number ++;
            }
            $xtpl->assign('TOTAL', nv_number_format($price_total));
            $xtpl->assign('COLSPAN', $colspan);
            $xtpl->parse('main.tour_price_caculate');
        }
    }
    
    if (! empty($booking_info['customer'])) {
        $number = 1;
        $customer_total = 0;
        foreach ($booking_info['customer'] as $customer) {
            $customer['number'] = $number ++;
            $customer['birthday'] = nv_date('d/m/Y', $customer['birthday']);
            $customer['gender'] = $lang_module['gender_' . $customer['gender']];
            $customer['age'] = $age_config[$customer['age']]['name'];
            $customer['customer_type'] = $customer['customer_type'] >= 0 ? $lang_module['customer_type_' . $customer['customer_type']] : '';
            $customer_total += $customer['price'];
            $customer['price'] = nv_number_format($customer['price']);
            $xtpl->assign('CUSTOMER', $customer);
            
            $price_method = $tour_info['price_method'];
            $subprice = $tour_info['subprice'];
            
            if ($price_method == 1) {
                $xtpl->parse('main.customer_list.age_thead');
                $xtpl->parse('main.customer_list.loop.age_tbody');
            } elseif ($price_method == 2) {
                $xtpl->parse('main.customer_list.age_thead');
                $xtpl->parse('main.customer_list.loop.age_tbody');
                
                // $xtpl->parse('main.customer_list.customer_type_thead');
                $xtpl->parse('main.customer_list.loop.customer_type_tbody');
            }
            
            $optional_thead = array();
            if (! empty($subprice)) {
                if (! empty($customer['optional'])) {
                    $array_optional = explode(',', $customer['optional']);
                    foreach ($array_optional as $optional) {
                        list ($subprice_id, $subprice_value) = explode('_', $optional);
                        $xtpl->assign('OPTIONAL', $subprice_value ? $lang_module['yes'] : $lang_module['no']);
                        $xtpl->parse('main.customer_list.loop.optional_tbody');
                        $optional_thead[$subprice_id] = $subprice[$subprice_id]['title'];
                    }
                }
            }
            $xtpl->parse('main.customer_list.loop');
        }
        if (! empty($optional_thead)) {
            foreach ($optional_thead as $head) {
                $xtpl->assign('TITLE', $head);
                $xtpl->parse('main.customer_list.optional_thead');
            }
        }
        $xtpl->assign('TOTAL', nv_number_format($customer_total));
        $xtpl->parse('main.customer_list');
    }
    
    if (! empty($_array_payment_method)) {
        $payment_method = $_array_payment_method[$booking_info['payment_method']];
        $xtpl->assign('PAYMENT_METHOD', $payment_method);
        if ($booking_info['payment_method'] == 0 or $booking_info['payment_method'] == 1) {
            $xtpl->parse('main.payment.payment_info');
        } elseif ($booking_info['payment_method'] == 2) {
            if (! empty($payment_method['description'])) {
                if (! empty($url_checkout)) {
                    foreach ($url_checkout as $value) {
                        $xtpl->assign('DATA_PAYMENT', $value);
                        $xtpl->parse('main.payment.payment_payport.description.actpay.loop');
                    }
                    $xtpl->parse('main.payment.payment_payport.description.actpay');
                }
                $xtpl->parse('main.payment.payment_payport.description');
            }
            $xtpl->parse('main.payment.payment_payport');
        }
        $xtpl->parse('main.payment');
    }
    
    $xtpl->parse('main');
    return $xtpl->text('main');
}
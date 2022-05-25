<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3/9/2010 23:25
 */
if (! defined('NV_MAINFILE'))
    die('Stop!!!');

if (! nv_function_exists('nv_tours_block_search')) {

    if (! nv_function_exists('nv_tours_numoney_to_strmoney')) {
        function nv_tours_numoney_to_strmoney( $money )
        {
            global $lang_module;
            
            if( $money > 1000 and $money < 1000000 )
            {
                $money = $money / 1000;
                return $money . ' ' . $lang_module['thousand'];
            }
            elseif( $money >= 1000000 )
            {
                $money = $money / 1000000;
                return $money . ' ' . $lang_module['million'];
            }
            return $money;
        }   
    }
    
    function nv_block_config_search_blocks($module, $data_block, $lang_block)
    {
        global $site_mods, $global_config;
        
        $array_search_template = array(
            'vertical' => $lang_block['search_template_vertical'],
            'horizontal' => $lang_block['search_template_horizontal'],
            'list' => $lang_block['search_template_list'],
            'vertical_f1' => $lang_block['search_template_vertical_f1'],
			'header' => $lang_block['search_template_header']
        );
        
        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $site_mods[$module]['module_file'] . '/block_search_config.tpl')) {
            $block_theme = $global_config['module_theme'];
        } else {
            $block_theme = 'default';
        }
        
        $data_block['vertical_f1_style'] = $data_block['search_template'] != 'vertical_f1' ? 'style="display: none"' : '';
        
        $xtpl = new XTemplate('block_search_config.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $site_mods[$module]['module_file']);
        $xtpl->assign('LANG', $lang_block);
        $xtpl->assign('DATA', $data_block);
        
        foreach($array_search_template as $index => $value){
            $sl = (isset($data_block['search_template']) and $data_block['search_template'] == $index) ? 'selected="selected"' : '';
            $xtpl->assign('TEMPLATE', array('index' => $index, 'value' => $value, 'selected' => $sl));
            $xtpl->parse('main.template');
        }
        
        $xtpl->parse('main');
        return $xtpl->text('main');
    }

    function nv_block_config_search_blocks_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['search_template'] = $nv_Request->get_title('config_search_template', 'post', 'vertical');
        $return['config']['maxday'] = $nv_Request->get_int('config_maxday', 'post', 10);
        $return['config']['price_begin'] = $nv_Request->get_title('config_price_begin', 'post', 1000000);
        $return['config']['price_end'] = $nv_Request->get_title('config_price_end', 'post', 20000000);
        $return['config']['price_step'] = $nv_Request->get_title('config_price_step', 'post', 1000000);
        return $return;
    }

    function nv_tours_block_search($block_config, $mod_data)
    {
        global $module_array_cat, $site_mods, $module_info, $db, $module_config, $global_config, $module_name, $db_config, $nv_Request, $my_head, $op, $lang_module, $module_array_vehicle;
        
        $module = $block_config['module'];
        $mod_file = $site_mods[$module]['module_file'];
        
        $tplfile = 'block_search_vertical.tpl';
        if ($block_config['search_template'] == 'horizontal') {
            $tplfile = 'block_search_horizontal.tpl';
        } elseif ($block_config['search_template'] == 'list') {
            $tplfile = 'block_search_list.tpl';
        } elseif ($block_config['search_template'] == 'vertical_f1') {
            $tplfile = 'block_search_vertical_f1.tpl';
        }
		elseif ($block_config['search_template'] == 'header') {
            $tplfile = 'block_search_header.tpl';
        }
        
        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $mod_file . '/' . $tplfile)) {
            $block_theme = $global_config['module_theme'];
        } else {
            $block_theme = 'default';
        }
        
        if ($module != $module_name) {
            require_once NV_ROOTDIR . '/modules/location/data.functions.php';
            
            $my_head .= '<link rel="StyleSheet" href="' . NV_BASE_SITEURL . 'themes/' . $block_theme . '/css/' . $site_mods[$module]['module_file'] . '.css">';
            
            include NV_ROOTDIR . '/modules/' . $site_mods[$module]['module_file'] . '/language/' . NV_LANG_INTERFACE . '.php';
        }
        
        if($block_config['search_template'] == 'vertical_f1'){
            $array_search = array(
                'time' => $nv_Request->get_int('time', 'post,get', 0),
                'price_spread' => $nv_Request->get_title('price_spread', 'post,get', ''),
                'hotel_star' => $nv_Request->get_int('hotel_star', 'post,get', 0),
                'vehicle' => $nv_Request->get_int('vehicle', 'post,get', 0)
            );
        }else{
            $array_search = array(
                'q' => $nv_Request->get_title('q', 'post,get', ''),
                'catid' => $nv_Request->get_int('catid', 'post,get', 0),
                'date_begin' => $nv_Request->get_title('date_begin', 'post,get', ''),
                'date_end' => $nv_Request->get_title('date_end', 'post,get', ''),
                'date_start' => $nv_Request->get_title('date_start', 'post,get', ''),
                'place_start' => $nv_Request->get_int('place_start', 'post,get', 0),
                'place_end' => $nv_Request->get_int('place_end', 'post,get', 0),
                'discount' => $nv_Request->get_int('discount', 'post,get', 0)
            );
        }

        $xtpl = new XTemplate($tplfile, NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $mod_file);
        $xtpl->assign('LANG', $lang_module);
        $xtpl->assign('TEMPLATE', $block_theme);
        $xtpl->assign('MODULE_NAME', $module);
        $xtpl->assign('SEARCH', $array_search);
        
        if($block_config['search_template'] == 'vertical_f1'){
            
            // So ngay di tour
            for($i=1; $i<=$block_config['maxday']; $i++){
                $sl = $array_search['time'] == $i ? 'selected="selected"' : '';
                $xtpl->assign('TIME', array('index' => $i, 'value' => strtolower($i . ' ' . $lang_module['day']), 'selected' => $sl));
                $xtpl->parse('main.time_tour');
            }
            
            // Chon khoang gia
            $array_price_spread = array();
            $val = $block_config['price_begin'];
            while (true) {
                $price1 = $val;
                $price2 = $val + $block_config['price_step'];
                if ($val < $block_config['price_end']) {
                    $array_price_spread[] = array(
                        'index' => $price1 . '-' . $price2,
                        'title' => nv_tours_numoney_to_strmoney($price1, $mod_file) . ' - ' . nv_tours_numoney_to_strmoney($price2, $mod_file) 
                    );
                } elseif ($val >= $block_config['price_end']) {
                    $array_price_spread[] = array(
                        'index' => $price1 . '-0',
                        'title' => $lang_module['from'] . ' ' . nv_tours_numoney_to_strmoney($val, $mod_file)
                    );
                }

                if ($val >= $block_config['price_end']) {
                    break;
                }
                $val += $block_config['price_step'];
            }
            
            if(!empty($array_price_spread)){
                foreach($array_price_spread as $price_spread){
                    $price_spread['selected'] = $array_search['price_spread'] == $price_spread['index'] ? 'selected="selected"' : '';
                    $xtpl->assign('PRICE_SPREAD', $price_spread);
                    $xtpl->parse('main.price_spread');
                }
            }
            
            // loai khach san
            $array_star = array(
                1 => $lang_module['hotels_type_1'],
                2 => $lang_module['hotels_type_2'],
                3 => $lang_module['hotels_type_3'],
                4 => $lang_module['hotels_type_4'],
                5 => $lang_module['hotels_type_5'],
            );
            foreach($array_star as $index => $value){
                $sl = $index == $array_search['hotel_star'] ? 'selected="selected"' : '';
                $xtpl->assign('HOTEL', array('index' => $index, 'value' => $value, 'selected' => $sl));
                $xtpl->parse('main.hotel_star');
            }
            
            // phuong tien
            if(!empty($module_array_vehicle)){
                foreach($module_array_vehicle as $vehicle){
                    $vehicle['selected'] = $array_search['vehicle'] == $vehicle['id'] ? 'selected="selected"' : '';
                    $xtpl->assign('VEHICLE', $vehicle);
                    $xtpl->parse('main.vehicle');
                }
            }
            
        }else{
            if (! empty($module_array_cat)) {
                foreach ($module_array_cat as $catid => $value) {
                    $value['space'] = '';
                    if ($value['lev'] > 0) {
                        for ($i = 1; $i <= $value['lev']; $i ++) {
                            $value['space'] .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        }
                    }
                    $value['selected'] = $catid == $array_search['catid'] ? ' selected="selected"' : '';
            
                    $xtpl->assign('CAT', $value);
                    $xtpl->parse('main.cat');
                }
            }
            
            $data_config = array(
                'select_countryid' => nv_location_get_countryid_from_province($array_search['place_start']),
                'select_provinceid' => $array_search['place_start'],
                'name_country' => '',
                'name_province' => 'place_start',
                'blank_title_province' => 1,
                'index' => 0,
                'col_class' => 'col-xs-24 col-sm-24 col-md-24'
            );
            $xtpl->assign('PLACE_START', nv_location_build_input($data_config));
            
            $data_config = array(
                'select_countryid' => nv_location_get_countryid_from_province($array_search['place_end']),
                'select_provinceid' => $array_search['place_end'],
                'name_country' => '',
                'name_province' => 'place_end',
                'blank_title_province' => 1,
                'index' => 1,
                'col_class' => 'col-xs-24 col-sm-24 col-md-24'
            );
            $xtpl->assign('PLACE_END', nv_location_build_input($data_config));            
        }
        
        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}

if (defined('NV_SYSTEM')) {
    global $db_config, $site_mods, $module_name, $array_cat, $module_array_cat, $array_block, $module_array_block, $nv_Cache, $array_vehicle, $module_array_vehicle;
    
    $module = $block_config['module'];
    if (isset($site_mods[$module])) {
        $mod_data = $site_mods[$module]['module_data'];
        if ($module == $module_name) {
            $module_array_cat = $array_cat;
            $module_array_vehicle = $array_vehicle;
        } else {
            $module_array_cat = array();
            $_sql = 'SELECT id, parentid, ' . NV_LANG_DATA . '_title title, ' . NV_LANG_DATA . '_alias alias, ' . NV_LANG_DATA . '_custom_title custom_title, ' . NV_LANG_DATA . '_keywords keywords, ' . NV_LANG_DATA . '_description description, ' . NV_LANG_DATA . '_description_html description_html, inhome, numlinks, viewtype, price_method, subprice, lev, numsub, subid, sort, weight, status, image, groups_view FROM ' . $db_config['prefix'] . '_' . $mod_data . '_cat WHERE status=1 ORDER BY sort ASC';
            $list = $nv_Cache->db($_sql, 'id', $module);
            foreach ($list as $l) {
                $module_array_cat[$l['id']] = $l;
                $module_array_cat[$l['id']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $l['alias'];
            }
        }
        
        $_sql = 'SELECT id, ' . NV_LANG_DATA . '_title title FROM ' . $db_config['prefix'] . '_' . $mod_data . '_vehicle WHERE status=1 ORDER BY weight';
        $module_array_vehicle = $nv_Cache->db($_sql, 'id', $module);
        
        $content = nv_tours_block_search($block_config, $mod_data);
    }
}
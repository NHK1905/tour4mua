<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sat, 10 Dec 2011 06:46:54 GMT
 */
if (! defined('NV_MAINFILE')) {
    die('Stop!!!');
}

if (! nv_function_exists('nv_block_tours_groups')) {
    
    if (! nv_function_exists('nv_get_price_tmp')) {

        function nv_get_price_tmp($module_name, $module_data, $module_file, $pro_id)
        {
            global $nv_Cache, $db, $db_config, $lang_module, $module_config, $discounts_config, $money_config, $array_config, $site_mods, $lang_global;
            $price = array();
            $array_config = $module_config[$module_name];
            require_once NV_ROOTDIR . '/modules/' . $module_file . '/global.functions.php';
            $price = nv_get_price($pro_id, $array_config['money_unit'], 0, $module_name);
            return $price;
        }
    }

    function nv_block_config_tours_groups($module, $data_block, $lang_block)
    {
        global $db_config, $nv_Cache, $site_mods;
        
        $html_input = '';
        
        $array_template = array(
            '0' => $lang_block['template_0'],
            '1' => $lang_block['template_1']
        );
        
        $html = '';
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['blockid'] . '</td>';
        $html .= '<td><select name="config_blockid" class="form-control w200">';
        $html .= '<option value="0"> -- </option>';
        $sql = 'SELECT bid, ' . NV_LANG_DATA . '_title title, ' . NV_LANG_DATA . '_alias alias FROM ' . $db_config['prefix'] . '_' . $site_mods[$module]['module_data'] . '_block_cat ORDER BY weight ASC';
        $list = $nv_Cache->db($sql, '', $module);
        foreach ($list as $l) {
            $html_input .= '<input type="hidden" id="config_blockid_' . $l['bid'] . '" value="' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $site_mods[$module]['alias']['groups'] . '/' . $l['alias'] . '" />';
            $html .= '<option value="' . $l['bid'] . '" ' . (($data_block['blockid'] == $l['bid']) ? ' selected="selected"' : '') . '>' . $l['title'] . '</option>';
        }
        $html .= '</select>';
        $html .= $html_input;
        $html .= '<script type="text/javascript">';
        $html .= '	$("select[name=config_blockid]").change(function() {';
        $html .= '		$("input[name=title]").val($("select[name=config_blockid] option:selected").text());';
        $html .= '		$("input[name=link]").val($("#config_blockid_" + $("select[name=config_blockid]").val()).val());';
        $html .= '	});';
        $html .= '</script>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['template'] . '</td>';
        $html .= '<td><select class="form-control" name="config_template">';
        foreach($array_template as $index => $value){
            $sl = $index == $data_block['template'] ? 'selected="selected"' : '';
            $html .= '<option value="' . $index . '" ' . $sl . ' >' . $value . '</option>';
        }
        $html .= '</select></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['numrow'] . '</td>';
        $html .= '<td><input type="text" class="form-control w200" name="config_numrow" size="5" value="' . $data_block['numrow'] . '"/></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['title_lenght'] . '</td>';
        $html .= '<td><input type="text" class="form-control w200" name="config_title_lenght" size="5" value="' . $data_block['title_lenght'] . '"/></td>';
        $html .= '</tr>';
        return $html;
    }

    function nv_block_config_tours_groups_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['blockid'] = $nv_Request->get_int('config_blockid', 'post', 0);
        $return['config']['template'] = $nv_Request->get_int('config_template', 'post', 0);
        $return['config']['numrow'] = $nv_Request->get_int('config_numrow', 'post', 0);
        $return['config']['title_lenght'] = $nv_Request->get_int('config_title_lenght', 'post', 0);
        return $return;
    }

    function nv_block_tours_groups($block_config)
    {        
        global $db_config, $module_array_cat, $module_info, $site_mods, $module_config, $global_config, $nv_Cache, $db, $array_config, $lang_module, $money_config, $module_name, $module_array_vehicle, $module_array_hotels, $my_head, $lang_global, $array_day_week, $array_day_month;

        $module = $block_config['module'];
        $mod_data = $site_mods[$module]['module_data'];
        $mod_file = $site_mods[$module]['module_file'];
        $mod_upload = $site_mods[$module]['module_upload'];
        $array_config = $module_config[$module];
        
        if ($module != $module_name) {
            $array_config = $module_config[$module];
            require_once NV_ROOTDIR . '/modules/' . $mod_file . '/language/' . NV_LANG_INTERFACE . '.php';
            require_once NV_ROOTDIR . '/modules/' . $mod_file . '/site.functions.php';
        }
        
        $db->sqlreset()
            ->select('t1.id, t1.catid, t1.' . NV_LANG_DATA . '_title title, t1.' . NV_LANG_DATA . '_alias alias, t1.homeimgfile, t1.homeimgthumb, t1.' . NV_LANG_DATA . '_description description, t1.date_start, t1.code, t1.discounts_id, t1.money_unit, t1.show_price, t1.num_day, t1.date_start_method, t1.date_start_config, t1.date_start, t1.show_price, t1.hotels_info hotels, t1.vehicle')
            ->from($db_config['prefix'] . '_' . $site_mods[$module]['module_data'] . '_rows t1')
            ->join('INNER JOIN ' . $db_config['prefix'] . '_' . $site_mods[$module]['module_data'] . '_block t2 ON t1.id = t2.id')
            ->where('t2.bid= ' . $block_config['blockid'] . ' AND t1.status= 1')
            ->order('t2.weight ASC')
            ->limit($block_config['numrow']);
        $list = $nv_Cache->db($db->sql(), '', $module);
        
        if (! empty($list)) {
            if($block_config['template'] == 1){
                $template = 'block_groups_vertical.tpl';
            }else{
                $template = 'block_groups.tpl';
            }
            
            if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/tours/' . $template)) {
                $block_theme = $global_config['module_theme'];
            } else {
                $block_theme = 'default';
            }
            
            if ($module != $module_name) {
                $my_head .= '<link rel="StyleSheet" href="' . NV_BASE_SITEURL . 'themes/' . $block_theme . '/css/tours.css' . '">';
            }
            
            $xtpl = new XTemplate($template, NV_ROOTDIR . '/themes/' . $block_theme . '/modules/tours');
            $xtpl->assign('LANG', $lang_module);
            $xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
            $xtpl->assign('TEMPLATE', $block_theme);
            $home_image_size = explode('x', $array_config['home_image_size']);
            $xtpl->assign('WIDTH', $home_image_size[0]);
            $xtpl->assign('HEIGHT', $home_image_size[1]);
            
            foreach ($list as $l) {
                $l['title_clean'] = nv_clean60($l['title'], $block_config['title_lenght']);
                $l['date_start'] = nv_get_date_start($l['date_start_method'], $l['date_start_config'], $l['date_start']);
                $l['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $module_array_cat[$l['catid']]['alias'] . '/' . $l['alias'] . $global_config['rewrite_exturl'];
                $l['thumb'] = nv_tour_get_thumb($l['homeimgfile'], $l['homeimgthumb'], $mod_upload);
                $l['vehicle'] = !empty($l['vehicle']) ? $module_array_vehicle[$l['vehicle']]['title'] : '';
                $hotel_star = nv_tours_get_hotel_star($l['hotels'], $module_array_hotels);
                if(!empty($hotel_star)){
                    $l['star'] = $lang_module['hotels_type_' . $hotel_star];
                }
                
                $xtpl->assign('ROW', $l);
                
                if ($l['show_price'] == '1') {
                    $price = nv_get_price_tmp($module, $mod_data, $mod_file, $l['id']);
                    $xtpl->assign('PRICE', $price);
                    if ($l['discounts_id'] and $price['discount_percent'] > 0) {
                        $xtpl->parse('main.loop.price.discounts');
                    } else {
                        $xtpl->parse('main.loop.price.no_discounts');
                    }
                    $xtpl->parse('main.loop.price');
                } else {
                    $xtpl->parse('main.loop.contact');
                }
                
                if (! empty($l['thumb'])) {
                    $xtpl->parse('main.loop.img');
                }
                
                if ($l['discounts_id'] and $price['discount_percent'] > 0 and $l['show_price']) {
                    $xtpl->parse('main.loop.discounts');
                }
                
                if(!empty($hotel_star)){
                    for($i=1; $i<=$hotel_star; $i++){
                        $xtpl->parse('main.loop.star.loop');
                    }
                    $xtpl->parse('main.loop.star');
                }
                
                if(!empty($l['vehicle'])){
                    $xtpl->parse('main.loop.vehicle');
                }
                
                $xtpl->parse('main.loop');
            }
            
            $xtpl->parse('main');
            return $xtpl->text('main');
        }
    }
}
if (defined('NV_SYSTEM')) {

    global $site_mods, $module_name, $array_cat, $module_array_cat, $nv_Cache, $db, $array_vehicle, $module_array_vehicle, $array_hotels, $module_array_hotels, $db_config;
    $module = $block_config['module'];

    if (isset($site_mods[$module])) {
        if ($module == $module_name) {
            $module_array_cat = $array_cat;
            $module_array_vehicle = $array_vehicle;
            $module_array_hotels = $array_hotels;
        } else {
            $module_array_cat = array();
            $_sql = 'SELECT id, parentid, ' . NV_LANG_DATA . '_title title, ' . NV_LANG_DATA . '_alias alias, ' . NV_LANG_DATA . '_custom_title custom_title, ' . NV_LANG_DATA . '_keywords keywords, ' . NV_LANG_DATA . '_description description, ' . NV_LANG_DATA . '_description_html description_html, inhome, numlinks, viewtype, price_method, subprice, lev, numsub, subid, sort, weight, status FROM ' . $db_config['prefix'] . '_' . $site_mods[$module]['module_data'] . '_cat WHERE status=1 ORDER BY sort ASC';

            $list = $nv_Cache->db($_sql, 'id', $module);
            if (! empty($list)) {
                foreach ($list as $l) {
                    $module_array_cat[$l['id']] = $l;
                    $module_array_cat[$l['id']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $l['alias'];
                }
            }
            
            $_sql = 'SELECT id, ' . NV_LANG_DATA . '_title title FROM ' . $db_config['prefix'] . '_' . $site_mods[$module]['module_data'] . '_vehicle WHERE status=1 ORDER BY weight';
            $module_array_vehicle = $nv_Cache->db($_sql, 'id', $module);
            
            $_sql = 'SELECT id, ' . NV_LANG_DATA . '_title title, star FROM ' . $db_config['prefix'] . '_' . $site_mods[$module]['module_data'] . '_hotels WHERE status=1 ORDER BY id DESC';
            $module_array_hotels = $nv_Cache->db($_sql, 'id', $module);
        }
        $content = nv_block_tours_groups($block_config);
    }
}

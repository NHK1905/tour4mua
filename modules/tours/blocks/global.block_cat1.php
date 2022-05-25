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

if (! nv_function_exists('nv_block_tours_carousel')) {
    
    if (! nv_function_exists('nv_get_price_tmp')) {

        function nv_get_price_tmp($module_name, $module_data, $module_file, $pro_id)
        {
            global $nv_Cache, $db, $db_config, $lang_module, $lang_global, $module_config, $discounts_config, $money_config, $array_config;
            
            $price = array();
            $array_config = $module_config[$module_name];
            require_once NV_ROOTDIR . '/modules/' . $module_file . '/global.functions.php';
            $price = nv_get_price($pro_id, $array_config['money_unit'], 0, $module_name);
            return $price;
        }
    }

    function nv_block_config_tours_carousel($module, $data_block, $lang_block)
    {
        global $db_config, $nv_Cache, $site_mods, $global_config;
                
        $array_type = array(
            '0' => $lang_block['catid'],
            '1' => $lang_block['blockid']
        );
        
        $_sql = 'SELECT id, ' . NV_LANG_DATA . '_title title, lev FROM ' . $db_config['prefix'] . '_' . $site_mods[$module]['module_data'] . '_cat WHERE status=1 ORDER BY sort ASC';
        $array_cat = $nv_Cache->db($_sql, 'id', $module);
        
        $sql = 'SELECT bid, ' . NV_LANG_DATA . '_title title, ' . NV_LANG_DATA . '_alias alias FROM ' . $db_config['prefix'] . '_' . $site_mods[$module]['module_data'] . '_block_cat ORDER BY weight ASC';
        $arrar_block = $nv_Cache->db($sql, '', $module);
        
        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/tours/block_carousel.tpl')) {
            $block_theme = $global_config['module_theme'];
        } else {
            $block_theme = 'default';
        }
        
        $data_block['ck_slideshow'] = $data_block['slideshow'] ? 'checked="checked"' : '';
        
        $xtpl = new XTemplate('block_carousel.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/tours');
        $xtpl->assign('LANG', $lang_block);
        $xtpl->assign('DATA', $data_block);
        
        foreach($array_type as $index => $value){
            $sl = $index == $data_block['type'] ? 'selected="selected"' : '';
            $xtpl->assign('TYPE', array('index' => $index, 'value' => $value, 'selected' => $sl));
            $xtpl->parse('config.type');
        }
        
        if (! empty($array_cat)) {
            $data_block['catid'] = !empty($data_block['catid']) ? $data_block['catid'] : array();
            foreach ($array_cat as $catid => $value) {
                $value['space'] = '';
                if ($value['lev'] > 0) {
                    for ($i = 1; $i <= $value['lev']; $i ++) {
                        $value['space'] .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    }
                }
                $value['checked'] = in_array($catid, $data_block['catid']) ? 'checked="checked"' : '';
        
                $xtpl->assign('CAT', $value);
                $xtpl->parse('config.cat');
            }
        }
        
        if(!empty($arrar_block)){
            foreach($arrar_block as $block){
                $block['selected'] = $block['bid'] == $data_block['blockid'] ? 'selected="selected"' : '';                
                $xtpl->assign('BLOCK', $block);
                $xtpl->parse('config.block');                
            }
        }
        
        if($data_block['type'] == 0){
            $xtpl->assign('BLOCK_HIDE', 'style="display: none"');
        }else{
            $xtpl->assign('CAT_HIDE', 'style="display: none"');
        }
        
        $xtpl->parse('config');
        return $xtpl->text('config');
    }

    function nv_block_config_tours_carousel_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['module_name'] = $nv_Request->get_title('config_module', 'post', '');
        $return['config']['type'] = $nv_Request->get_int('config_type', 'post', 0);
        $return['config']['catid'] = $nv_Request->get_typed_array('config_catid', 'post', 'int', array());
        $return['config']['blockid'] = $nv_Request->get_int('config_blockid', 'post', 0);
        $return['config']['numrow'] = $nv_Request->get_int('config_numrow', 'post', 0);
        $return['config']['title_lenght'] = $nv_Request->get_int('config_title_lenght', 'post', 0);
        $return['config']['numpage'] = $nv_Request->get_int('config_numpage', 'post', 3);
        $return['config']['slideshow'] = $nv_Request->get_int('config_slideshow', 'post', 0);
        return $return;
    }

    function nv_block_tours_carousel($block_config)
    {
        global $db_config, $module_array_cat, $module_info, $site_mods, $module_config, $global_config, $nv_Cache, $db, $array_config, $lang_module, $money_config, $module_name;
        
        $module = $block_config['module'];
        $mod_data = $site_mods[$module]['module_data'];
        $mod_file = $site_mods[$module]['module_file'];
        $mod_upload = $site_mods[$module]['module_upload'];
        $array_config = $module_config[$module];
        $block_config['slideshow'] = $block_config['slideshow'] ? 'true' : 'false';
        
        if ($module != $module_name) {
            $array_config = $module_config[$module];
            require_once NV_ROOTDIR . '/modules/' . $mod_file . '/language/' . NV_LANG_INTERFACE . '.php';
            require_once NV_ROOTDIR . '/modules/' . $mod_file . '/site.functions.php';
        }
        
        if($block_config['type'] == 1){
            $db->sqlreset()
            ->select('t1.id, t1.catid, t1.' . NV_LANG_DATA . '_title title, t1.' . NV_LANG_DATA . '_alias alias, t1.homeimgfile, t1.homeimgthumb, t1.' . NV_LANG_DATA . '_description description, t1.date_start, t1.code, t1.discounts_id, t1.money_unit, t1.show_price, t1.num_day, t1.date_start_method, t1.date_start_config, t1.date_start, t1.show_price')
            ->from($db_config['prefix'] . '_' . $site_mods[$module]['module_data'] . '_rows t1')
            ->join('INNER JOIN ' . $db_config['prefix'] . '_' . $site_mods[$module]['module_data'] . '_block t2 ON t1.id = t2.id')
            ->where('t2.bid= ' . $block_config['blockid'] . ' AND t1.status= 1')
            ->order('t2.weight ASC')
            ->limit($block_config['numrow']);
            $list = $nv_Cache->db($db->sql(), '', $module);
        }else{
            $db->sqlreset()
            ->select('id, catid, ' . NV_LANG_DATA . '_title title, ' . NV_LANG_DATA . '_alias alias, homeimgfile, homeimgthumb, ' . NV_LANG_DATA . '_description description, date_start, code, discounts_id, money_unit, show_price, num_day, date_start_method, date_start_config, date_start, show_price')
            ->from($db_config['prefix'] . '_' . $site_mods[$module]['module_data'] . '_rows t1')
            ->where('status=1 AND catid IN(' . implode(',', $block_config['catid']) . ')')
            ->order('id DESC')
            ->limit($block_config['numrow']);
            $list = $nv_Cache->db($db->sql(), '', $module);
        }
        
        if (! empty($list)) {
            if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/tours/cat.tpl')) {
                $block_theme = $global_config['module_theme'];
            } else {
                $block_theme = 'default';
            }

            $xtpl = new XTemplate('block_cat1.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/tours');
            $xtpl->assign('LANG', $lang_module);
            $xtpl->assign('BLOCK_CONFIG', $block_config);
            $xtpl->assign('TEMPLATE', $block_theme);
            $home_image_size = explode('x', $array_config['home_image_size']);
            $xtpl->assign('WIDTH', $home_image_size[0]);
            $xtpl->assign('HEIGHT', $home_image_size[1]);
			
            $_first = true;
			
            foreach ($list as $l) {
				if($_first)
				{
					$_first = false;
					$l['title_clean'] = nv_clean60($l['title'], $block_config['title_lenght']);
					$l['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $module_array_cat[$l['catid']]['alias'] . '/' . $l['alias'] . $global_config['rewrite_exturl'];
					$l['img'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $site_mods[$module]['module_upload'] . '/' . $l['homeimgfile'];
					$xtpl->assign( 'FIRST', $l );
					
					if ($l['show_price'] == '1') {
						$price = nv_get_price_tmp($module, $mod_data, $mod_file, $l['id']);
						$xtpl->assign('FIRST_PRICE', $price);
						if ($l['discounts_id'] and $price['discount_percent'] > 0) {
							$xtpl->parse('main.price.discounts');
						} else {
							$xtpl->parse('main.price.no_discounts');
						}
						$xtpl->parse('main.price');
					} else {
						$xtpl->parse('main.contact');
					}
				}
				else{
					$l['title_clean'] = nv_clean60($l['title'], $block_config['title_lenght']);
					$l['date_start'] = nv_get_date_start($l['date_start_method'], $l['date_start_config'], $l['date_start']);
					$l['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $module_array_cat[$l['catid']]['alias'] . '/' . $l['alias'] . $global_config['rewrite_exturl'];
					$l['thumb'] = nv_tour_get_thumb($l['homeimgfile'], $l['homeimgthumb'], $mod_upload);
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
					
					$xtpl->parse('main.loop');
				}
            }
            
            $xtpl->parse('main');
            return $xtpl->text('main');
        }
    }
}
if (defined('NV_SYSTEM')) {
    global $site_mods, $module_name, $array_cat, $module_array_cat, $nv_Cache, $db;
    
    $module = $block_config['module'];
    if (isset($site_mods[$module])) {
        if ($module == $module_name) {
            $module_array_cat = $array_cat;
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
        }
        $content = nv_block_tours_carousel($block_config);
    }
}

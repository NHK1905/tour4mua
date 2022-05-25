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

if (! nv_function_exists('nv_block_plan_short')) {

    function nv_block_config_plan_short($module, $data_block, $lang_block)
    {
        $html = '';
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['title_tag'] . '</td>';
        $html .= '<td><input type="text" class="form-control" name="config_title_tag" size="5" value="' . $data_block['title_tag'] . '"/></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['fixed'] . '</td>';
        $ck = $data_block['fixed'] ? 'checked="checked"' : '';
        $html .= '<td><input type="checkbox" name="config_fixed" value="1" ' . $ck . ' /></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['padding'] . '</td>';
        $html .= '<td><input type="text" class="form-control" name="config_padding" size="5" value="' . $data_block['padding'] . '"/></td>';
        $html .= '</tr>';
        
        return $html;
    }

    function nv_block_config_plan_short_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['title_tag'] = $nv_Request->get_title('config_title_tag', 'post', 'h2');
        $return['config']['fixed'] = $nv_Request->get_int('config_fixed', 'post', 0);
        $return['config']['padding'] = $nv_Request->get_title('config_padding', 'post', 0);
        return $return;
    }

    function nv_block_plan_short($block_config)
    {
        global $db_config, $module_info, $lang_module, $site_mods, $global_config, $module_name, $op;
        
        if ($op != 'detail')
            return '';
        
        $module = $block_config['module'];
        $mod_file = $site_mods[$module]['module_file'];
        
        if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/tours/block_groups.tpl')) {
            $block_theme = $global_config['module_theme'];
        } else {
            $block_theme = 'default';
        }
        $xtpl = new XTemplate('block_plan_short.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/tours');
        $xtpl->assign('LANG', $lang_module);
        $xtpl->assign('TEMPLATE', $block_theme);
        $xtpl->assign('CONFIG', $block_config);
        
        if ($block_config['fixed']) {
            $xtpl->parse('main.fixed');
        }
        
        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}
if (defined('NV_SYSTEM')) {
    $content = nv_block_plan_short($block_config);
}

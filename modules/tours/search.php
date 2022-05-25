<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 03-05-2010
 */

if (! defined('NV_IS_MOD_SEARCH')) {
    die('Stop!!!');
}

$db_slave->sqlreset()
    ->select('COUNT(*)')
    ->from($db_config['prefix'] . '_' . $m_values['module_data'] . '_rows')
    ->where('(' . nv_like_logic( NV_LANG_DATA . '_title', $dbkeywordhtml, $logic) . ' OR ' . nv_like_logic(NV_LANG_DATA . '_alias', $dbkeyword, $logic) . ' OR ' . nv_like_logic(NV_LANG_DATA . '_title_custom', $dbkeyword, $logic) . ' OR ' . nv_like_logic(NV_LANG_DATA . '_plan', $dbkeyword, $logic) . ' OR ' . nv_like_logic(NV_LANG_DATA . '_description', $dbkeyword, $logic) . ' OR ' . nv_like_logic(NV_LANG_DATA . '_description_html', $dbkeyword, $logic) . 'OR ' . nv_like_logic(NV_LANG_DATA . '_note', $dbkeyword, $logic) . ') AND status= 1');

$num_items = $db_slave->query($db_slave->sql())->fetchColumn();

if ($num_items) {
    $array_cat_alias = array();

    $sql_cat = 'SELECT id, ' . NV_LANG_DATA . '_alias alias FROM ' . $db_config['prefix'] . '_' . $m_values['module_data'] . '_cat';
    $re_cat = $db_slave->query($sql_cat);
    while (list($catid, $alias) = $re_cat->fetch(3)) {
        $array_cat_alias[$catid] = $alias;
    }

    $link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $m_values['module_name'] . '&amp;' . NV_OP_VARIABLE . '=';

    $db_slave->select('id, catid, ' . NV_LANG_DATA . '_title, ' . NV_LANG_DATA . '_alias, ' . NV_LANG_DATA . '_plan, ' . NV_LANG_DATA . '_description, ' . NV_LANG_DATA . '_description_html, ' . NV_LANG_DATA . '_note')
        ->order('addtime DESC')
        ->limit($limit)
        ->offset(($page - 1) * $limit);
    $result = $db_slave->query($db_slave->sql());
    while (list($id, $catid, $title, $alias, $plan, $description, $description_html, $note) = $result->fetch(3)) {
        $content = $description . ' ' . strip_tags($description_html) . ' ' . strip_tags($plan) . ' ' . strip_tags($note);
        $url = $link . $array_cat_alias[$catid] . '/' . $alias . '-' . $global_config['rewrite_exturl'];
        $result_array[] = array(
            'link' => $url,
            'title' => BoldKeywordInStr($title, $key, $logic),
            'content' => BoldKeywordInStr($content, $key, $logic)
        );
    }
}
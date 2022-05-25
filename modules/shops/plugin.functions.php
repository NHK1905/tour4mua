<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2017 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 04/18/2017 09:47
 */
if (!defined('NV_MAINFILE')) {
    die('Stop!!!');
}
include NV_ROOTDIR . '/modules/shops/language/plugin_vi.php';

$discounts = $db->query('SELECT t2.discounts, t1.discount_edit FROM ' . NV_USERS_GLOBALTABLE . '_cat t1 INNER JOIN ' . NV_USERS_GLOBALTABLE . '_discounts t2 on t1.discountid=t2.id where t1.userid=' . ($user_info['userid'] == true ? $user_info['userid'] : $admin_info['userid']))->fetch();

$discounts = $discounts['discount_edit'] ? $discounts['discount_edit'] : $discounts['discounts'];


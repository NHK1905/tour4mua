<?php

/**
 * @Project NUKEVIET 4.x
 * @Author hongoctrien (contact@mynukeviet.net)
 * @Copyright (C) 2016 hongoctrien. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Sun, 08 May 2016 07:42:57 GMT
 */
if (! defined('NV_MAINFILE'))
    die('Stop!!!');

$module_version = array(
    'name' => 'Tours',
    'modfuncs' => 'main,detail,search,groups,viewcat,booking,payment,search,tag',
    'change_alias' => 'main,detail,search,groups,booking,payment,tag',
    'submenu' => 'main,detail,search',
    'is_sysmod' => 0,
    'virtual' => 1,
    'version' => '2.0.02',
    'date' => 'Sun, 8 May 2016 07:42:57 GMT',
    'author' => 'hongoctrien (contact@mynukeviet.net)',
    'uploads_dir' => array(
        $module_upload,
        $module_upload . '/images'
    ),
    'note' => ''
);
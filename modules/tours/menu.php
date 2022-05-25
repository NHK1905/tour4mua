<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 21-04-2011 11:17
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$sql = 'SELECT id, parentid, ' . NV_LANG_DATA . '_title title, ' . NV_LANG_DATA . '_alias alias FROM ' . $db_config['prefix'] . '_' . $mod_data . '_cat ORDER BY sort ASC';
$result = $db->query( $sql );
while( $row = $result->fetch() )
{
	$array_item[$row['id']] = array(
		'parentid' => $row['parentid'],
		'groups_view' => 6,
		'key' => $row['id'],
		'title' => $row['title'],
		'alias' => $row['alias']
	);
}
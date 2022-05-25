<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-10-2010 18:49
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$catid = $nv_Request->get_int( 'catid', 'post', 0 );
$mod = $nv_Request->get_string( 'mod', 'post', '' );
$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
$content = 'NO_' . $catid;

list( $catid, $parentid, $numsubcat ) = $db->query( 'SELECT id, parentid, numsub FROM ' . $db_config['prefix'] . '_' . $module_data . '_cat WHERE id=' . $catid  )->fetch( 3 );
if( $catid > 0 )
{
	if( $mod == 'inhome' and ( $new_vid == 0 or $new_vid == 1 ) )
	{
		$sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_cat SET inhome=' . $new_vid . ' WHERE id=' . $catid ;
		$db->query( $sql );
		$content = 'OK_' . $parentid;
	}
	elseif( $mod == 'gettype' and $nv_Request->isset_request( 'new_vid', 'post' ) )
	{
	    $gettype = $nv_Request->get_title( 'new_vid', 'post' );
	    $stmt = $db->prepare( 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_cat SET gettype= :gettype WHERE id=' . $catid  );
	    $stmt->bindParam( ':gettype', $gettype, PDO::PARAM_STR );
	    $stmt->execute();
	    $content = 'OK_' . $parentid;
	}
	elseif( $mod == 'viewtype' and $nv_Request->isset_request( 'new_vid', 'post' ) )
	{
		$viewtype = $nv_Request->get_title( 'new_vid', 'post' );
		$stmt = $db->prepare( 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_cat SET viewtype= :viewtype WHERE id=' . $catid  );
		$stmt->bindParam( ':viewtype', $viewtype, PDO::PARAM_STR );
		$stmt->execute();
		$content = 'OK_' . $parentid;
	}
	elseif( $mod == 'numlinks' and $new_vid >= 0 and $new_vid <= 20 )
	{
		$sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_cat SET numlinks=' . $new_vid . ' WHERE id=' . $catid ;
		$db->query( $sql );
		$content = 'OK_' . $parentid;
	}

	$nv_Cache->delMod( $module_name );
}

include NV_ROOTDIR . '/includes/header.php';
echo $content;
include NV_ROOTDIR . '/includes/footer.php';
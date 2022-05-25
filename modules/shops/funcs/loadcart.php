<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2017 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 04/18/2017 09:47
 */

if (! defined('NV_IS_MOD_SHOPS')) {
    die('Stop!!!');
}

$num = isset($_SESSION[$module_data . '_cart']) ? count($_SESSION[$module_data . '_cart']) : 0;
$total = 0;
$total_old = 0;
$total_coupons = 0;

$coupons_check = $nv_Request->get_int('coupons_check', 'get', 0);
$coupons_load = $nv_Request->get_int('coupons_load', 'get', 0);
$coupons_code = $nv_Request->get_title('coupons_code', 'get', '');

$counpons = array(
    'title' => '',
    'type' => 'p',
    'discount' => 0,
    'total_amount' => 0,
    'date_start' => 0,
    'date_end' => 0
);

if (!empty($coupons_code)) {
    $result = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_coupons WHERE code = ' . $db->quote($coupons_code));
    $counpons = $result->fetch();
    $result = $db->query('SELECT pid FROM ' . $db_config['prefix'] . '_' . $module_data . '_coupons_product WHERE cid = ' . $counpons['id']);
    while (list($pid) = $result->fetch(3)) {
        $counpons['product'][] = $pid;
    }
}

if ($coupons_load) {
    $_SESSION[$module_data . '_coupons']['check'] = $coupons_check;
}

if (! empty($_SESSION[$module_data . '_cart'])) {
    foreach ($_SESSION[$module_data . '_cart'] as $pro_id => $info) {
    	$array=explode('_', $pro_id);
		$pro_id	=$array[0];
        $price = nv_get_price($pro_id, $pro_config['money_unit'], $info['num']);
        // Ap dung giam gia cho tung san pham dac biet
        if (!empty($counpons['product'])) {
            if (in_array($pro_id, $counpons['product'])) {
                $total_coupons = $total_coupons + $price['sale'];
            }
        }
        $total = $total + $price['sale'];
    }
    $total_old = $total;
}


if( !empty( $_SESSION[$module_data . '_cart'] ) )
{
	$arrayid = array( );

	foreach( $_SESSION[$module_data . '_cart'] as $pro_id => $pro_info )
	{
		
		
		

		$array = explode( '_', $pro_id );
		if( $array[1] == '' )
		{
			$sql = "SELECT t1.id, t1.listcatid, t1.publtime, t1." . NV_LANG_DATA . "_title, t1." . NV_LANG_DATA . "_alias, t1." . NV_LANG_DATA . "_hometext, t1.homeimgalt, t1.homeimgfile, t1.homeimgthumb, t1.product_number, t1.product_price, t1.discount_id, t2." . NV_LANG_DATA . "_title, t1.money_unit FROM " . $db_config['prefix'] . "_" . $module_data . "_rows AS t1, " . $db_config['prefix'] . "_" . $module_data . "_units AS t2 WHERE t1.product_unit = t2.id AND t1.id IN ('" . $array[0] . "') AND t1.status =1";

		}
		else
		{
			$sql = "SELECT t1.id, t1.listcatid, t1.publtime, t1." . NV_LANG_DATA . "_title, t1." . NV_LANG_DATA . "_alias, t1." . NV_LANG_DATA . "_hometext, t1.homeimgalt, t1.homeimgfile, t1.homeimgthumb, t1.product_number, t1.product_price, t1.discount_id, t2." . NV_LANG_DATA . "_title, t1.money_unit FROM " . $db_config['prefix'] . "_" . $module_data . "_rows AS t1, " . $db_config['prefix'] . "_" . $module_data . "_units AS t2, " . $db_config['prefix'] . "_" . $module_data . "_group_quantity t3 WHERE t1.product_unit = t2.id AND t1.id = t3.pro_id AND  t3.listgroup ='" . $array[1] . "' AND t1.id IN ('" . $array[0] . "') AND t1.status =1";

		}
		$result = $db->query( $sql );
		while( list( $id, $listcatid, $publtime, $title, $alias, $hometext, $homeimgalt, $homeimgfile, $homeimgthumb, $product_number, $product_price, $discount_id, $unit, $money_unit ) = $result->fetch( 3 ) )
		{
			if( $homeimgthumb == 1 )
			{
				//image thumb

				$thumb = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_upload . '/' . $homeimgfile;
			}
			elseif( $homeimgthumb == 2 )
			{
				//image file

				$thumb = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $homeimgfile;
			}
			elseif( $homeimgthumb == 3 )
			{
				//image url

				$thumb = $homeimgfile;
			}
			else
			{
				//no image

				$thumb = NV_BASE_SITEURL . 'themes/' . $module_info['template'] . '/images/' . $module_file . '/no-image.jpg';
			}

			$group = $_SESSION[$module_data . '_cart'][$id . '_' . $array[1]]['group'];
			$number = $_SESSION[$module_data . '_cart'][$id . '_' . $array[1]]['num'];

			if( !empty( $order_info ) )
			{
				$product_number = $product_number + (isset( $_SESSION[$module_data . '_cart'][$id . '_' . $array[1]]['num_old'] ) ? $_SESSION[$module_data . '_cart'][$id . '_' . $array[1]]['num_old'] : $_SESSION[$module_data . '_cart'][$id . '_' . $array[1]]['num']);
			}

			if( !empty( $group ) and $pro_config['active_warehouse'] )
			{
				$group = explode( ',', $group );
				asort( $group );
				$group = implode( ',', $group );
				$product_number = 1;
				$_result = $db->query( 'SELECT quantity FROM ' . $db_config['prefix'] . '_' . $module_data . '_group_quantity WHERE pro_id = ' . $id . ' AND listgroup="' . $group . '"' );
				if( $_result->rowCount( ) > 0 )
				{
					$product_number = $_result->fetchColumn( );
				}
			}

			if( $number > $product_number and $number > 0 and empty( $pro_config['active_order_number'] ) )
			{
				$number = $_SESSION[$module_data . '_cart'][$id . '_' . $array[1]]['num'] = $product_number;
				$array_error_product_number[] = sprintf( $lang_module['product_number_max'], $title, $product_number );
			}

			if( $pro_config['active_price'] == '0' )
			{
				$discount_id = $product_price = 0;
			}

			    $link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=';

			$data_content[] = array(
				'id' => $id,
				'listcatid' => $listcatid,
				'publtime' => $publtime,
				'title' => $title,
				'alias' => $alias,
				'hometext' => $hometext,
				'homeimgalt' => $homeimgalt,
				'homeimgthumb' => $thumb,
				'product_price' => $product_price,
				'discount_id' => $discount_id,
				'product_unit' => $unit,
				'money_unit' => $money_unit,
				'group' => $group,
				'link_pro' => $link . $global_array_shops_cat[$listcatid]['alias'] . '/' . $alias . $global_config['rewrite_exturl'],
				'num' => $number,
					'link_remove' => $link . 'remove&id=' . $id . '&group=' . $group
			);
			$_SESSION[$module_data . '_cart'][$id.'_'.$array[1]]['order'] = 1;
		}
		if( empty( $array_error_product_number ) and $nv_Request->isset_request( 'cart_order', 'post' ) )
		{
			Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=order', true ) );
			exit( );
		}
	}
}




if ((empty($counpons['total_amount']) or $total >= $counpons['total_amount']) and NV_CURRENTTIME >= $counpons['date_start'] and (empty($counpons['uses_per_coupon']) or $counpons['uses_per_coupon_count'] < $counpons['uses_per_coupon']) and (empty($counpons['date_end']) or NV_CURRENTTIME < $counpons['date_end'])) {
    // Ap dung giam gia cho tung san pham dac biet
    if ($total_coupons > 0) {
        if ($counpons['type'] == 'p') {
            if ($coupons_check) {
                $total = $total  - (($total_coupons * $counpons['discount']) / 100);
            }
        } else {
            if ($coupons_check) {
                $total = ($total_coupons - $counpons['discount']);
            }
        }
    } else {
        // Ap dung cho don hang

        if ($counpons['type'] == 'p') {
            if ($coupons_check) {
                $total = $total  - (($total * $counpons['discount']) / 100);
            }
        } else {
            if ($coupons_check) {
                $total = $total - $counpons['discount'];
            }
        }
    }

    if ($coupons_check) {
        $_SESSION[$module_data . '_coupons']['discount'] = $total_old - $total;
    }
}

if ($nv_Request->isset_request('get_shipping_price', 'get')) {
    $weight = $nv_Request->get_float('weight', 'get', 0);
    $weight_unit = $nv_Request->get_string('weight_unit', 'get', '');
    $location_id = $nv_Request->get_int('location_id', 'get', 0);
    $shops_id = $nv_Request->get_int('shops_id', 'get', 0);
    $carrier_id = $nv_Request->get_int('carrier_id', 'get', 0);

    $ship_price = nv_shipping_price($weight, $weight_unit, $location_id, $shops_id, $carrier_id);
    if (!empty($ship_price)) {
        $total += $ship_price;
    }
}

if ($pro_config['active_price'] == '0') {
    $total = 0;
}

$total = nv_number_format($total, nv_get_decimals($pro_config['money_unit']));

$lang_tmp['cart_title'] = $lang_module['cart_title'];
$lang_tmp['cart_product_title'] = $lang_module['cart_product_title'];
$lang_tmp['cart_product_total'] = $lang_module['cart_product_total'];
$lang_tmp['cart_check_out'] = $lang_module['cart_check_out'];
$lang_tmp['history_title'] = $lang_module['history_title'];
$lang_tmp['active_order_dis'] = $lang_module['active_order_dis'];
$lang_tmp['wishlist_product'] = $lang_module['wishlist_product'];
$lang_tmp['point_cart_text'] = $lang_module['point_cart_text'];
$lang_tmp['title_products'] = $lang_module['title_products'];
$lang_tmp['cart_check_cart'] = $lang_module['cart_check_cart'];

$xtpl = new XTemplate("block.cart.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file);
$xtpl->assign('LANG', $lang_tmp);
$xtpl->assign('total', $total);
$xtpl->assign('TEMPLATE', $module_info['template']);
$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
$xtpl->assign('LINK_VIEW', NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=cart");
$xtpl->assign('WISHLIST', NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=wishlist");

    $j = 1;
    if (!empty($data_content)) {
        foreach ($data_content as $data_row) {
            $xtpl->assign('id', $data_row['id']);
            $xtpl->assign('title_pro', $data_row['title']);
            $xtpl->assign('link_pro', $data_row['link_pro']);
			$xtpl->assign( 'img_pro', $data_row['homeimgthumb'] );
			$xtpl->assign( 'money_unit', $pro_config['money_unit']);
			 $xtpl->assign('link_remove', $data_row['link_remove']);
            $price = nv_get_price($data_row['id'], $pro_config['money_unit'], $data_row['num'], true);
            $xtpl->assign('PRICE', $price);
            $price = nv_get_price($data_row['id'], $pro_config['money_unit'], $data_row['num']);
            $xtpl->assign('PRICE_TOTAL', $price);
            $xtpl->assign('pro_no', $j);
            $xtpl->assign('pro_num', $data_row['num']);
            $xtpl->assign('product_unit', $data_row['product_unit']);
            if ($pro_config['active_price'] == '1') {
                $xtpl->parse('main.enable.loop.price2');
                $xtpl->parse('main.enable.loop.price5');
            }
            $xtpl->parse('main.enable.loop');
            $price_total = $price_total + $price['sale'];
            ++$j;
        }
    }
	
	
	



if (defined('NV_IS_USER')) {
    // Danh sach san pham yeu thich
    if ($pro_config['active_wishlist']) {
        $count = 0;
        $listid = $db->query('SELECT listid FROM ' . $db_config['prefix'] . '_' . $module_data . '_wishlist WHERE user_id = ' . $user_info['userid'] . '')->fetchColumn();
        if ($listid) {
            $count = count(explode(',', $listid));
        }
        $xtpl->assign('NUM_ID', $count);
        $xtpl->parse('main.wishlist');
    }

    // Lich su giao dich
    $xtpl->assign('LINK_HIS', NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=history");
    $xtpl->parse('main.history');

    // Diem tich luy
    if ($pro_config['point_active']) {
        $xtpl->assign('POINT_URL', NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=point");

        $point = 0;
        $result = $db->query('SELECT point_total FROM ' . $db_config['prefix'] . '_' . $module_data . '_point WHERE userid = ' . $user_info['userid']);
        if ($result->rowCount()) {
            $point = $result->fetchColumn();
        }
        $xtpl->assign('POINT', $point);
        $xtpl->parse('main.point');
    }
}

$xtpl->assign('money_unit', $pro_config['money_unit']);
$xtpl->assign('num', $num);

if ($pro_config['active_price'] == '1') {
    $xtpl->parse('main.enable.price');
}

if ($pro_config['active_order'] == '1') {
    $xtpl->parse('main.enable');
} else {
    $xtpl->parse('main.disable');
}

$xtpl->parse('main');
$content = $xtpl->text('main');
$content = nv_url_rewrite($content);

$type = $nv_Request->get_int('t', 'get', 0);
switch ($type) {
    case 0:
        echo $content;
        break;
    case 1:
        echo $num;
        break;
    case 2:
        echo $total;
        break;
    default:
        echo $content;
        break;
}

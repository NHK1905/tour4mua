<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Ho Ngoc Trien (hongoctrien@2mit.org)
 * @Copyright (C) 2015 Ho Ngoc Trien. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sun, 16 Aug 2015 01:05:44 GMT
 */
if (! defined('NV_MAINFILE'))
    die('Stop!!!');
    
$array_day_week = array(
    '1' => $lang_global['monday'],
    '2' => $lang_global['tuesday'],
    '3' => $lang_global['wednesday'],
    '4' => $lang_global['thursday'],
    '5' => $lang_global['friday'],
    '6' => $lang_global['saturday'],
    '7' => $lang_global['sunday']
);

function nv_tour_get_thumb($homeimgfile, $homeimgthumb, $module_upload)
{
    global $array_config;
    
    if ($homeimgthumb == 1) {
        $thumb = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_upload . '/' . $homeimgfile;
    } elseif ($homeimgthumb == 2) {
        $thumb = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $homeimgfile;
    } elseif ($homeimgthumb == 3) {
        $thumb = $homeimgfile;
    } elseif (! empty($array_config['no_image'])) {
        $thumb = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $array_config['no_image'];
    } else {
        $thumb = '';
    }
    return $thumb;
}

/**
 * nv_get_date_start()
 *
 * @param mixed $date_start_method
 * @param mixed $date_start_config
 * @param mixed $date_start
 * @return
 *
 */
function nv_get_date_start($date_start_method, $date_start_config, $date_start, $last = 0)
{
    global $lang_module, $array_day_week, $array_day_month;

    $str = '';
    if ($date_start_method == 0 and $date_start > 0) {
        $str = nv_date('d/m/Y', $date_start);
    } elseif ($date_start_method == 3) {
        $str = $lang_module['allday'];
    } elseif ($date_start_method == 4) {
        $str = $lang_module['contact'];
    } elseif (! empty($date_start_method)) {
        $date_start_config = ! empty($date_start_config) ? unserialize($date_start_config) : array();
        if (! empty($date_start_config)) {
            if ($date_start_method == 1) {
                $data = array();
                foreach ($date_start_config as $value) {
                    $data[] = $array_day_week[$value];
                }
                $str = implode(', ', $data) . ($last ? ' ' . $lang_module['per_week'] : '');
            } elseif ($date_start_method == 2) {
                $str = $lang_module['day'] . ' ' . implode(', ', $date_start_config) . ($last ? ' ' . $lang_module['per_month'] : '');
            }
        }
    }
    return $str;
}

/**
 * nv_tours_get_hotel_star()
 *
 * @param mixed $hotels
 * @param mixed $$array_hotels
 * @return
 *
 */
function nv_tours_get_hotel_star($hotels, $array_hotels)
{
    $star = array(0);
    
    if(!empty($hotels)){
        $hotels = unserialize($hotels);
        if(!empty($hotels)){
            foreach($hotels as $hotel){
                $star[] = $array_hotels[$hotel['id']]['star'];
            }
        }
    }
    
    return max($star);
}

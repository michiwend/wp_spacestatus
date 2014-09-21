<?php
/*  Copyright (c) 2014  Michael Wendland
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License, version 2, as 
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *  Authors:
 *      Michael Wendland <michael@michiwend.com>
 */

/*
 *  Plugin Name: WP SpaceStatus
 *  Plugin URI: https://github.com/michiwend/wp_spacestatus
 *  Description: a WordPress plugin that displays your space status.
 *  Version: 1.0.0
 *  Author: Michael Wendland
 *  Author URI: http://blog.michiwend.com
 *  License: GPL2
 */

include('settings.php');

/*
$args = array(
    'timeout'     => 5,
    'redirection' => 5,
    'httpversion' => '1.0',
    'user-agent'  => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
    'blocking'    => true,
    'headers'     => array(),
    'cookies'     => array(),
    'body'        => null,
    'compress'    => false,
    'decompress'  => true,
    'sslverify'   => true,
    'stream'      => false,
    'filename'    => null
);*/

function get_spacestatus() {

    $options = get_option('wp_spacestatus_options');

    $api_response = wp_remote_get( $options['api_url_string'] );

    $rsp_code = wp_remote_retrieve_response_code( &$api_response );
    $rsp_msg  = wp_remote_retrieve_response_message( &$api_response );
    $rsp_body = wp_remote_retrieve_body( &$api_response );

    if( $rsp_code != 200) {
        return new WP_ERROR('api_call_failed', "Failed calling ".$options['api_url_string'], $rsp_msg );
    }

    return json_decode( $rsp_body )->open;
}


function spacestatus_shortcode( $atts ) {

    $options = get_option('wp_spacestatus_options');

    $a = shortcode_atts( array(
        'type'  => 'icon_large',
        'class' => '',
        'id'    => '',
    ), $atts );
 
    $icon_baseurl = plugins_url()."/wp_spacestatus/status_icons"; // FIXME let user upload status icons
    $imgtag_begin = '<img id="'.$a['id'].'" class="'.$a['class'].'" src="'.$icon_baseurl."/";

    $status = get_spacestatus();

    if( is_wp_error( $status ) ) {
        return $status->get_error_message();
    }

    if( $status ) {

        switch( $a['type'] ) {
            case 'icon_large':  $out = $imgtag_begin."open_large.png\" alt=\"Space status open icon\" />" ; break;
            case 'icon_small':  $out = $imgtag_begin."open_small.png\" alt=\"Space status open icon\" />"; break;
            case 'text':        $out = $options['textstatus_open_string']; break;
            default:            $out = "undefined shortcode param"; break;
        }
    }
    else {

        switch( $a['type'] ) {
            case 'icon_large':  $out = $imgtag_begin."closed_large.png\" alt=\"Space status closed icon\" />"; break;
            case 'icon_small':  $out = $imgtag_begin."closed_small.png\" alt=\"Space status closed icon\" />"; break;
            case 'text':        $out = $options['textstatus_closed_string']; break;
            default:            $out = "undefined shortcode param"; break;
        }

    }

    return $out;
}


add_shortcode('space_status', 'spacestatus_shortcode');


?>

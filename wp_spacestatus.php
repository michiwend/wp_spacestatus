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

include('space_api.php');
include('settings.php');

$response = null;

// icon builds the HTML for a status icon.
function icon($status, $src, $options, $sc_attrs) {

    $alt   = "Status icon '$status'";
    $title = "Status: ".$options['textstatus_'.$status.'_string'];
    $style = "";

    if( $sc_attrs['width'] != '' )  $style = "width: {$sc_attrs['width']}; ";
    if( $sc_attrs['height'] != '' ) $style = $style."height: {$sc_attrs['height']};";

    return "<img src=\"$src\" alt=\"$alt\" title=\"$title\" style=\"$style\" class=\"{$sc_attrs['class']}\" id=\"{$sc_attrs['id']}\" />";
}

// spacestatus_shortcode() is the shortcode callback.
function spacestatus_shortcode( $atts ) {

    global $response;

    $a = shortcode_atts( array(
        'type'   => 'icon', // short code type defaults to icon.
        'width'  => '',
        'height' => '',
        'class'  => '',
        'id'     => '',
    ), $atts );

    // Validate short code type attr.
    if( $a['type'] != 'text' && $a['type'] != 'icon' ) return "Invalid short code attr.";

    $options  = get_option('wp_spacestatus_options');
    if ($response === null) {
        $response = callAPI($options['api_url_string']);
    }

    // Error occurred, return unknown status.
    if( is_wp_error( $response ) ) {
        if( $a['type'] == 'icon' ) {
            $src = $options["icon_unknown_url"];
            return icon($status, $src, $options, $a);
        }
        return $options['textstatus_unknown_string'];
    }

    if($response->getSpaceStatus() === true) {
        $status = "open";
        $text_type = "textstatus_open_string";
    }
    else if($response->getSpaceStatus() === false) {
        $status = "closed";
        $text_type = "textstatus_closed_string";
    }
    else {
        $status = "unknown";
        $text_type = "textstatus_unknown_string";
    }

    // return icon.
    if( $a['type'] == 'icon' ) {
        if ($options['use_spaceapi_icons'] === 'spaceapi') {
            if ($status === 'open') {
                $src = $response->getIconOpen();
            } else {
                $src = $response->getIconClosed();
            }
            return icon($status, $src, $options, $a);
        } else {
            $src = $options["icon_" . $status . "_url"];
            return icon($status, $src, $options, $a);
        }
    }

    // return text.
    return $options[$text_type];
}


function lastchange_shortcode( $atts ) {

    global $response;

    $options  = get_option('wp_spacestatus_options');

    if ($response === null) {
        $response = callAPI($options['api_url_string']);
    }

    date_default_timezone_set(get_option('timezone_string'));
    return strftime($options['lastchange_date_format'], $response->getLastChange());

}

add_shortcode('space_status', 'spacestatus_shortcode');
add_shortcode('space_lastchange', 'lastchange_shortcode');

?>

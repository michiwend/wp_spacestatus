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


// icon builds the HTML for a status icon.
function icon($status, $options, $sc_attrs) {

    $src   = $options["icon_".$status."_url"];
    $alt   = "Status icon '$status'";
    $style = "";

    if( $sc_attrs['width'] != '' )  $style = "width: {$sc_attrs['width']}; ";
    if( $sc_attrs['height'] != '' ) $style = $style."height: {$sc_attrs['height']};";

    return "<img src=\"$src\" alt=\"$alt\" style=\"$style\" class=\"{$sc_attrs['class']}\" id=\"{$sc_attrs['id']}\" />";
}

// spacestatus_shortcode() is the shortcode callback.
function spacestatus_shortcode( $atts ) {

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
    $response = callAPI($options['api_url_string']);

    // Error occurred, return unknown status.
    if( is_wp_error( $response ) ) {
        if( $a['type'] == 'icon' ) {
            return icon('unknown', $options, $a);
        }
        return $options['textstatus_unknown_string'];
    }

    // return icon.
    if( $a['type'] == 'icon' ) {
        return icon(
            $response->getSpaceStatus() ? 'open' : 'closed',
            $options,
            $a);
    }

    // return text.
    if( $response->getSpaceStatus() ) return $options['textstatus_open_string'];
    return $options['textstatus_closed_string'];
}

add_shortcode('space_status', 'spacestatus_shortcode');

?>

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
 *      Peter Grassberger <petertheone@gmail.com>
 *      Stefan More <dev+github@2904.cc>
 */

require_once('space_api.php');

// add the admin options page
add_action('admin_menu', 'plugin_admin_add_page');
function plugin_admin_add_page() {
    add_options_page('WP SpaceStatus Settings', 'WP SpaceStatus', 'manage_options', 'wp_spacestatus', 'wp_spacestatus_options_page');
}

// display the admin options page
function wp_spacestatus_options_page() {
?>
    <div>
        <h1>WP SpaceStatus Settings</h1>
        <p>Use the <code>[space_status]</code> template tag anywere on your blog (page, article, widget)
            to display the current status for your Hackerspace.<br />
            To customize the output you can use different attributes as there are: <em>type=icon/text, width, height, class, id</em>.
        <p>Example: <code>[space_status width=50px class=alignleft]</code></p>

        <form action="options.php" method="post">
            <?php settings_fields('wp_spacestatus_options'); ?>
            <?php do_settings_sections('wp_spacestatus'); ?>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes'); ?>"  />
            </p>
        </form>
    </div>
<?php
}



// SpaceAPI section ////////////////////////////////////////////////////////////
function wp_spacestatus_spaceapi_section_text() {
    echo '<p>Options relating the SpaceAPI.</p>';
}

function wp_spacestatus_api_url_string() {
    $options = get_option('wp_spacestatus_options');
    echo "<input id='wp_spacestatus_api_url_string' name='wp_spacestatus_options[api_url_string]' size='80' type='text' value='{$options['api_url_string']}' />";
    $rsp = callAPI($options['api_url_string']);
    if( !is_wp_error( $rsp ) ) {
        echo " <span style=\"padding: 3px 5px; display: inline-block; background-color: rgba(150, 255, 150, 1); border: 1px solid rgba(0, 220, 0, 1);\">v".$rsp->getAPIVersion()."</span>";
    }
    else {
        echo " <span style=\"padding: 3px 5px; display: inline-block; background-color: rgba(255, 150, 150, 1); border: 1px solid rgba(220, 0, 0, 1);\">Failed!</span>";
    }
}

function get_option_or_default($option, $default) {
    $options = get_option('wp_spacestatus_options');
    // Set default value if field is empty.
    if( !$options[$option] ) {
        $options[$option] = $default;
        update_option('wp_spacestatus_options', $options);
    }

    return $options[$option];
}


// Appearance section //////////////////////////////////////////////////////////
function wp_spacestatus_appearance_section_text() {
    echo '<p>Define what icons and text messages will be displayed.</p>';
}

function wp_spacestatus_textstatus_open_string() {
    $option = get_option_or_default('textstatus_open_string', 'Open \o/');

    echo "<input id='wp_spacestatus_textstatus_open_string'
            name='wp_spacestatus_options[textstatus_open_string]'
            size='20' type='text' value='$option' />";
}

function wp_spacestatus_textstatus_closed_string() {
    $option = get_option_or_default('textstatus_closed_string', 'Closed :(');

    echo "<input id='wp_spacestatus_textstatus_closed_string'
            name='wp_spacestatus_options[textstatus_closed_string]'
            size='20' type='text' value='$option' />";
}

function wp_spacestatus_textstatus_unknown_string() {
    $option = get_option_or_default('textstatus_unknown_string', 'Unknown :/');

    echo "<input id='wp_spacestatus_textstatus_unknown_string'
            name='wp_spacestatus_options[textstatus_unknown_string]'
            size='20' type='text' value='$option' />";
}

function wp_spacestatus_icon_open_url() {

    $option = get_option_or_default(
        'icon_open_url',
        plugins_url()."/wp_spacestatus/icons/open.png");

    echo "<input id='wp_spacestatus_icon_open_url'
            name='wp_spacestatus_options[icon_open_url]'
            size='80' type='text' value='$option' /> ";
    echo "<img style=\"width: 25px; height: 25px; vertical-align: middle;\"
            src=\"$option\" alt=\"\" title=\"\" /> ";
    //echo "<input style=\"margin-left: 20px;\" type=\"button\" class=\"button button-primary\" value=\"Upload one...\"  />";
}

function wp_spacestatus_use_spaceapi_icons() {

    $option = get_option_or_default('use_spaceapi_icons', 'spaceapi');

    echo '<input id="wp_spacestatus_use_spaceapi_icons_spaceapi" name="wp_spacestatus_options[use_spaceapi_icons]" ' .
            'type="radio" value="spaceapi"' . ($option === 'spaceapi' ? ' checked="checked"' : '') . ' />' .
            '<label for="wp_spacestatus_use_spaceapi_icons_spaceapi">Use SpaceAPI icons<label><br />';
    echo '<input id="wp_spacestatus_use_spaceapi_icons_manuel" name="wp_spacestatus_options[use_spaceapi_icons]" ' .
            'type="radio" value="manuel"' . ($option === 'manuel' ? ' checked="checked"' : '') . ' />' .
            '<label for="wp_spacestatus_use_spaceapi_icons_manuel">Set icons manuelly<label>';
}

function wp_spacestatus_force_protocol_relative() {

    $option = get_option_or_default('force_protocol_relative', 'false');

    echo '<input id="wp_spacestatus_force_protocol_relative" name="wp_spacestatus_options[force_protocol_relative]" ' .
            'type="checkbox" value="true"' . ($option === 'true' ? ' checked="checked"' : '') . ' />' .
            '<label for="wp_spacestatus_use_spaceapi_icons_spaceapi">replace "http://" by "//" in image URLs.<label><br />';
}

function wp_spacestatus_icon_closed_url() {

    $option = get_option_or_default(
        'icon_closed_url',
        plugins_url()."/wp_spacestatus/icons/closed.png");

    echo "<input id='wp_spacestatus_icon_closed_url'
            name='wp_spacestatus_options[icon_closed_url]'
            size='80' type='text' value='$option' /> ";
    echo "<img style=\"width: 25px; height: 25px; vertical-align: middle;\"
            src=\"$option\" alt=\"\" title=\"\" /> ";
    //echo "<input style=\"margin-left: 20px;\" type=\"button\" class=\"button button-primary\" value=\"Upload one...\"  />";
}

function wp_spacestatus_icon_unknown_url() {

    $option = get_option_or_default(
        'icon_unknown_url',
        plugins_url()."/wp_spacestatus/icons/unknown.png");

    echo "<input id='wp_spacestatus_icon_unknown_url'
            name='wp_spacestatus_options[icon_unknown_url]'
            size='80' type='text' value='$option' /> ";
    echo "<img style=\"width: 25px; height: 25px; vertical-align: middle;\"
            src=\"$option\" alt=\"\" title=\"\" /> ";
    //echo "<input style=\"margin-left: 20px;\" type=\"button\" class=\"button button-primary\" value=\"Upload one...\"  />";
}

function wp_spacestatus_lastchange_date_format() {
    $option = get_option_or_default('lastchange_date_format', '%H:%M');

    echo "<input id='wp_spacestatus_lastchange_date_format'
            name='wp_spacestatus_options[lastchange_date_format]'
            size='20' type='text' value='$option' />";
}

// add the admin settings and such
add_action('admin_init', 'plugin_admin_init');
function plugin_admin_init() {
    register_setting( 'wp_spacestatus_options', 'wp_spacestatus_options', 'plugin_options_validate' );

    add_settings_section('wp_spacestatus_spaceapi_section', 'SpaceAPI', 'wp_spacestatus_spaceapi_section_text', 'wp_spacestatus');
    add_settings_field('wp_spacestatus_api_url', 'SpaceAPI URL', 'wp_spacestatus_api_url_string', 'wp_spacestatus', 'wp_spacestatus_spaceapi_section');

    add_settings_section('wp_spacestatus_appearance_section', 'Appearance', 'wp_spacestatus_appearance_section_text', 'wp_spacestatus');
    add_settings_field('wp_spacestatus_textstatus_open', 'Text status <em>open</em>', 'wp_spacestatus_textstatus_open_string', 'wp_spacestatus', 'wp_spacestatus_appearance_section');
    add_settings_field('wp_spacestatus_textstatus_closed', 'Text status <em>closed</em>', 'wp_spacestatus_textstatus_closed_string', 'wp_spacestatus', 'wp_spacestatus_appearance_section');
    add_settings_field('wp_spacestatus_textstatus_unknown', 'Text status <em>unknown</em>', 'wp_spacestatus_textstatus_unknown_string', 'wp_spacestatus', 'wp_spacestatus_appearance_section');
    add_settings_field('wp_spacestatus_use_spaceapi_icons', 'Use SpaceAPI icons', 'wp_spacestatus_use_spaceapi_icons', 'wp_spacestatus', 'wp_spacestatus_appearance_section');
    add_settings_field('wp_spacestatus_force_protocol_relative', 'Force procol-relative URL', 'wp_spacestatus_force_protocol_relative', 'wp_spacestatus', 'wp_spacestatus_appearance_section');
    add_settings_field('wp_spacestatus_icon_open_url', 'Icon <em>open</em>', 'wp_spacestatus_icon_open_url', 'wp_spacestatus', 'wp_spacestatus_appearance_section');
    add_settings_field('wp_spacestatus_icon_closed_url', 'Icon <em>closed</em>', 'wp_spacestatus_icon_closed_url', 'wp_spacestatus', 'wp_spacestatus_appearance_section');
    add_settings_field('wp_spacestatus_icon_unknown_url', 'Icon <em>unknown</em>', 'wp_spacestatus_icon_unknown_url', 'wp_spacestatus', 'wp_spacestatus_appearance_section');
    add_settings_field('wp_spacestatus_lastchange_date_format', 'Date Format (see PHP strftime)', 'wp_spacestatus_lastchange_date_format', 'wp_spacestatus', 'wp_spacestatus_appearance_section');
}

// validate our options
function plugin_options_validate($input) {
    // FIXME throw an error, not just return an empty string
    $newinput['api_url_string'] = esc_url($input['api_url_string'], array('http', 'https'));

    $newinput['textstatus_open_string']   = $input['textstatus_open_string']; //FIXME validate
    $newinput['textstatus_closed_string'] = $input['textstatus_closed_string']; //FIXME validate
    $newinput['textstatus_unknown_string'] = $input['textstatus_unknown_string']; //FIXME validate

    $newinput['use_spaceapi_icons'] = $input['use_spaceapi_icons']; //FIXME validate
    $newinput['force_protocol_relative'] = $input['force_protocol_relative']; //FIXME validate
    $newinput['icon_open_url']    = $input['icon_open_url']; //FIXME validate
    $newinput['icon_closed_url']  = $input['icon_closed_url']; //FIXME validate
    $newinput['icon_unknown_url'] = $input['icon_unknown_url']; //FIXME validate

    $newinput['lastchange_date_format'] = $input['lastchange_date_format']; //FIXME validate

    return $newinput;
}

?>

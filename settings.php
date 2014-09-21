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

// add the admin options page
add_action('admin_menu', 'plugin_admin_add_page');
function plugin_admin_add_page() {
    add_options_page('WP SpaceStatus Settings', 'WP SpaceStatus', 'manage_options', 'wp_spacestatus', 'wp_spacestatus_options_page');
}

// display the admin options page
function wp_spacestatus_options_page() {
?>
    <div>
        <h2>WP SpaceStatus Settings</h2>
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
    echo '<p>Options relating your SpaceAPI instance.</p>';
}

function wp_spacestatus_api_url_string() {
    $options = get_option('wp_spacestatus_options');
    echo "<input id='wp_spacestatus_api_url_string' name='wp_spacestatus_options[api_url_string]' size='100' type='text' value='{$options['api_url_string']}' />";
}



// Appearance section //////////////////////////////////////////////////////////
function wp_spacestatus_appearance_section_text() {
    echo '<p>Define how the space status gets displayed.</p>';
}

function wp_spacestatus_textstatus_open_string() {
    $options = get_option('wp_spacestatus_options');
    if( $options['textstatus_open_string'] == "" ) $options['textstatus_open_string'] = "Open!"; 
    echo "<input id='wp_spacestatus_textstatus_open_string' name='wp_spacestatus_options[textstatus_open_string]' size='20' type='text' value='{$options['textstatus_open_string']}' />";
}

function wp_spacestatus_textstatus_closed_string() {
    $options = get_option('wp_spacestatus_options');
    if( $options['textstatus_closed_string'] == "" ) $options['textstatus_closed_string'] = "Closed.";
    echo "<input id='wp_spacestatus_textstatus_closed_string' name='wp_spacestatus_options[textstatus_closed_string]' size='20' type='text' value='{$options['textstatus_closed_string']}' />";
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
}

// validate our options
function plugin_options_validate($input) {
    // FIXME throw an error, not just return an empty string
    $newinput['api_url_string'] = esc_url($input['api_url_string'], array('http', 'https'));
    $newinput['textstatus_open_string'] = $input['textstatus_open_string']; //FIXME validate
    $newinput['textstatus_closed_string'] = $input['textstatus_closed_string']; //FIXME validate

    return $newinput;
}

?>

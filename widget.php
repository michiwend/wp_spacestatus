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
 */

require_once('space_api.php');

class Wp_Spacestatus_Widget extends WP_Widget {

    function Wp_Spacestatus_Widget() {
        parent::WP_Widget(false, $name = __('Wp Spacestatus Widget', 'Wp_Spacestatus_Widget') );
    }

    function form($instance) {
        $title = '';
        $showIcon = false;
        $iconWidth = '';
        $iconHeight = '';
        $showText = false;
        $showLastChange = false;
        if($instance) {
            $title = esc_attr($instance['title']);
            $showIcon = esc_attr($instance['showIcon']);
            $iconWidth = esc_attr($instance['iconWidth']);
            $iconHeight = esc_attr($instance['iconHeight']);
            $showText = esc_attr($instance['showText']);
            $showLastChange = esc_attr($instance['showLastChange']);
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'wp_widget_plugin'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

        <p>
            <input id="<?php echo $this->get_field_id('showIcon'); ?>" name="<?php echo $this->get_field_name('showIcon'); ?>" type="checkbox" value="1" <?php checked('1', $showIcon); ?> />
            <label for="<?php echo $this->get_field_id('showIcon'); ?>"><?php _e('Show icon', 'wp_spacestatus'); ?></label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('iconWidth'); ?>"><?php _e('Icon width', 'wp_spacestatus'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('iconWidth'); ?>" name="<?php echo $this->get_field_name('iconWidth'); ?>" type="text" placeholder="width" value="<?php echo $iconWidth; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('iconHeight'); ?>"><?php _e('Icon height', 'wp_spacestatus'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('iconHeight'); ?>" name="<?php echo $this->get_field_name('iconHeight'); ?>" type="text" placeholder="height" value="<?php echo $iconHeight; ?>" />
        </p>

        <p>
            <input id="<?php echo $this->get_field_id('showText'); ?>" name="<?php echo $this->get_field_name('showText'); ?>" type="checkbox" value="1" <?php checked('1', $showText); ?> />
            <label for="<?php echo $this->get_field_id('showText'); ?>"><?php _e('Show text', 'wp_spacestatus'); ?></label>
        </p>

        <p>
            <input id="<?php echo $this->get_field_id('showLastChange'); ?>" name="<?php echo $this->get_field_name('showLastChange'); ?>" type="checkbox" value="1" <?php checked('1', $showLastChange); ?> />
            <label for="<?php echo $this->get_field_id('showLastChange'); ?>"><?php _e('Show last change', 'wp_spacestatus'); ?></label>
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['showIcon'] = strip_tags($new_instance['showIcon']);
        $instance['iconWidth'] = strip_tags($new_instance['iconWidth']);
        $instance['iconHeight'] = strip_tags($new_instance['iconHeight']);
        $instance['showText'] = strip_tags($new_instance['showText']);
        $instance['showLastChange'] = strip_tags($new_instance['showLastChange']);
        return $instance;
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        $showIcon = apply_filters('widget_title', $instance['showIcon']);
        $iconWidth = apply_filters('widget_title', $instance['iconWidth']);
        $iconHeight = apply_filters('widget_title', $instance['iconHeight']);
        $showText = apply_filters('widget_title', $instance['showText']);
        $showLastChange = apply_filters('widget_title', $instance['showLastChange']);


        echo $before_widget;
        // displayOption the widget
        echo '<div class="widget-text wp_widget_plugin_box">';

        // Check if title is set
        if ($title) {
            echo $before_title . $title . $after_title;
        }

        if ($showIcon) {
            echo spacestatus_shortcode(array(
                "type" => "icon",
                "width" => $iconWidth,
                "height" => $iconHeight
            ));
            echo "<br />\n";
        }

        if ($showText) {
            echo '<span class="text">';
            echo spacestatus_shortcode(array(
                "type" => "text"
            ));
            echo "</span><br />\n";
        }

        if ($showLastChange) {
            echo '<span class="lastChange">';
            echo lastchange_shortcode(array());
            echo "</span><br />\n";
        }

        echo '</div>';
        echo $after_widget;
    }

}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("Wp_Spacestatus_Widget");'));

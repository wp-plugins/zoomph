<?php
/*
Plugin Name:    Zoomph
Plugin URI:     https://zoomph.com/
Description:    Easily embed Zoomph visuals within your Wordpress site.
Version:        1.0
Author:         Zoomph
License:        GPLv2 or later
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action('admin_head', 'zoomph_add_my_tc_button');

add_action( 'widgets_init', 'zoomph_register_widgets' );

add_shortcode( 'zoomph', 'zoomph_shortcode' );

//=============================================================================

function zoomph_register_widgets() {
	register_widget( 'ZoomphWidget' );
}

//=============================================================================

class ZoomphWidget extends WP_Widget {

	function ZoomphWidget() {
		// Instantiate the parent object
		parent::__construct(
			'Zoomph', // Base ID
			__('Zoomph', 'text_domain'), // Name
			array(
				'description' => 'Zoomph Widget',
				'visual'=>''
			) // Args
		);
	}

	function widget( $args, $instance ) {
		$src = 'https://visuals.zoomph.com/Visuals/?id=' . $instance['visual_id'];
		$style = 'border:0;width:100%;height:' . $instance['height'] . 'px';
		
		echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		echo "<iframe src='${src}' style='${style}' scrolling='no'></iframe>";
		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['visual_id'] = ( ! empty( $new_instance['visual_id'] ) ) ? strip_tags( $new_instance['visual_id'] ) : '';
		$instance['height'] = ( ! empty( $new_instance['height'] ) ) ? strip_tags( $new_instance['height'] ) : '400';
		return $instance;
	}

	function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array(
            'title' => '',
			'visual_id' => '',
			'height' => '400'
		) );

		?><p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
			</label>
		</p>
        <p>
			<label for="<?php echo $this->get_field_id( 'visual_id' ); ?>">Visual Id
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'visual_id' ); ?>" name="<?php echo $this->get_field_name( 'visual_id' ); ?>" value="<?php echo esc_attr($instance['visual_id']); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'height' ); ?>">Height (in pixels)
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo esc_attr($instance['height']); ?>" />
			</label>
		</p><?php
	}
}

//=============================================================================

function zoomph_shortcode( $atts ) {
	$atts = shortcode_atts( array ( 'id' => null, 'width' => null, 'height' => '400' ), $atts, 'zoomph' );
	
	$visuals = json_decode( get_option("zoomph_visuals" ) );
	
	$src = "https://visuals.zoomph.com/Visuals/?id=${atts['id']}";
	
	$style = 'border:0;width:'.($atts['width']==null?'100%':($atts['width'].'px')).';height:' . $atts['height'] . 'px';
	
	return "<iframe src='${src}' style='${style}' scrolling='no'></iframe>";
}

//=============================================================================

function zoomph_add_my_tc_button() {
    global $typenow;
    // check user permissions
    if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
    return;
    }
    // verify the post type
    if( ! in_array( $typenow, array( 'post', 'page' ) ) )
        return;
    // check if WYSIWYG is enabled
    if ( get_user_option( 'rich_editing' ) == 'true' ) {
        add_filter( 'mce_external_plugins', 'zoomph_add_tinymce_plugin' );
        add_filter( 'mce_buttons', 'zoomph_register_my_tc_button' );
    }
}

//=============================================================================

function zoomph_add_tinymce_plugin($plugin_array) {
    $plugin_array['zoomph_tc_button'] = plugin_dir_url( __FILE__ ) . 'zoomph-mce.js';
    return $plugin_array;
}

//=============================================================================

function zoomph_register_my_tc_button($buttons) {
   array_push( $buttons, 'zoomph_tc_button' );
   return $buttons;
}

//=============================================================================
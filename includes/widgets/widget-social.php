<?php
/**
 * Social Icon Widget
 *
 * @package 	Core_Functionality
 * @subpackage  Font_Awesome_Social_Icon_Widget
 * @since 		1.0.0
 * @author 		Joshua David Nelson <josh@joshuadnelson.com>, Bill Erickson <billerickson.net>
 * @copyright   Copyright (c) 2015, Joshua David Nelson
 * @license 	http://www.gnu.org/licenses/gpl-2.0.html GPLv2.0+
 *
 * Risen from the smoking remains of Bill Erickson's Social Widget (billerickson.net).
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'Font_Awesome_Social_Icon_Widget' ) ) {
	
	function core_register_social_widget() {
		register_widget( 'Font_Awesome_Social_Icon_Widget' );
	}
	add_action( 'widgets_init', 'core_register_social_widget' );
	
	class Font_Awesome_Social_Icon_Widget extends WP_Widget {
	
	    /**
	     * Constructor
	     *
	     * @return void
	     **/
		function Font_Awesome_Social_Icon_Widget() {
			$widget_ops = array( 'classname' => 'widget_socials', 'description' => 'Social icon widget' );
			$this->WP_Widget( 'social-icon-widget', 'Social Icon Widget', $widget_ops );

			// Enqueue Scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'widget_scripts' ) );
		}

		/**
		 * Social Options
		 */
		function social_options() {
			return array(
				'facebook'		=> 'Facebook',
				'linkedin'		=> 'LinkedIn',
				'twitter'		=> 'Twitter',
				'google-plus'	=> 'Google Plus',
				'pinterest'		=> 'Pinterest',
				'youtube'		=> 'YouTube',
				'instagram'		=> 'Instagram',
				'rss'			=> 'RSS Feed URL',
			);
		}
		
		function widget_scripts() {
			// Font Awesome
			wp_register_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', array(), '4.3.0' );
			wp_enqueue_style( 'font-awesome' );
		}

	    /**
	     * Outputs the HTML for this widget.
	     *
	     * @param array  An array of standard parameters for widgets in this theme 
	     * @param array  An array of settings for this widget instance 
	     * @return void Echoes it's output
	     **/
		function widget( $args, $instance ) {
			extract( $args, EXTR_SKIP );
			echo $before_widget;
		
			if( $instance['title'] )
				echo $before_title . esc_attr( $instance['title'] ) . $after_title;
			
			echo '<ul class="socials">';
			$socials = $this->social_options();
			foreach( $socials as $key => $label ) {
				if( !empty( $instance[$key] ) )
					echo '<li><a class="social-icon ' . $key . '" href="' . esc_url( $instance[$key] ) . '"><i class="fa fa-' . $key . '"></i></a></li>';
			}
			echo '</ul>';

			echo $after_widget;
		}

	    /**
	     * Deals with the settings when they are saved by the admin. Here is
	     * where any validation should be dealt with.
	     *
	     * @param array  An array of new settings as submitted by the admin
	     * @param array  An array of the previous settings 
	     * @return array The validated and (if necessary) amended settings
	     **/
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
		
			$instance['title'] = esc_attr( $new_instance['title'] );
			$socials = $this->social_options();
			foreach( $socials as $key => $label )
				$instance[$key] = esc_url( $new_instance[$key] );

			return $instance;
		}
	
	    /**
	     * Displays the form for this widget on the Widgets page of the WP Admin area.
	     *
	     * @param array  An array of the current settings for this widget
	     * @return void Echoes it's output
	     **/
		function form( $instance ) {
	
			$socials = $this->social_options();
			$defaults = array( 
				'title' => ''
			);
			foreach( $socials as $key => $label )
				$defaults[ $key ] = '';

			$instance = wp_parse_args( (array) $instance, $defaults ); 
		
			echo '<p><label for="' . $this->get_field_id( 'title' ) . '">Title: <input class="widefat" id="' . $this->get_field_id( 'title' ) .'" name="' . $this->get_field_name( 'title' ) . '" value="' . esc_attr( $instance['title'] ) . '" /></label></p>';
		
			foreach( $socials as $key => $label )
				echo '<p><label for="' . $this->get_field_id( $key ) . '">' . $label . ' URL: <input class="widefat" id="' . $this->get_field_id( $key ) .'" name="' . $this->get_field_name( $key ) . '" value="' . esc_url( $instance[$key] ) . '" /></label></p>';
		
		}
	}
}
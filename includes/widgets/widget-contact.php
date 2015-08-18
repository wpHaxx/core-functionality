<?php
/**
 * Contact Widget
 *
 * @package 	Core_Functionality
 * @subpackage  Contact_Widget
 * @since 		1.0.0
 * @author 		Joshua David Nelson <josh@joshuadnelson.com>
 * @copyright   Copyright (c) 2015, Joshua David Nelson
 * @license 	http://www.gnu.org/licenses/gpl-2.0.html GPLv2.0+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Widget Class
if ( ! class_exists( 'Contact_Widget' ) ) {

	function core_register_contact_widget() {
		register_widget( 'Contact_Widget' );
	}
	add_action( 'widgets_init', 'core_register_contact_widget' );
	
	class Contact_Widget extends WP_Widget {
	
	    /**
	     * Constructor
	     *
	     * @return void
	     **/
		function __construct() {
			$widget_ops = array( 'classname' => 'widget_contact', 'description' => 'Contact info widget' );
			parent::__construct( 'contact-widget', 'Contact Widget', $widget_ops );
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
		
			echo '<div class="contact">';
			if( $instance['title'] )
				echo $before_title . esc_attr( $instance['title'] ) . $after_title;
			if( $instance['email'] ) {
				$email = sanitize_email( $instance['email'] );
				echo '<div class="email"><a href="mailto:' . $email . '">' . $email . '</a></div>';
			}
			if( $instance['phone'] ) {
				$phone = esc_attr( $instance['phone'] );
				echo '<div class="phone"><a href="tel:' . $phone . '">' . $phone . '</a></div>';
			}
			echo '</div>';

			echo $after_widget;
		}

	    /**
	     * Deals with the settings when they are saved by the admin. Here is
	     * where any validation should be dealt with.
	     *
	     * @param array $new_instance An array of new settings as submitted by the admin
	     * @param array $instance An array of the previous settings 
	     * @return array $instance The validated and (if necessary) amended settings
	     **/
		function update( $new_instance, $instance ) {
		
			if( is_email( $new_instance['email'] ) )
				$instance['email'] = sanitize_email( $new_instance['email'] );
		
			if( $new_instance['phone'] ) {
				$regex = "/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i";
				if( preg_match( $regex, $new_instance['phone'] ) )
					$instance['phone'] = $new_instance['phone'];
			}
		
			$instance['title'] = esc_attr( $new_instance['title'] );

			return $instance;
		}
	
	    /**
	     * Displays the form for this widget on the Widgets page of the WP Admin area.
	     *
	     * @param array  An array of the current settings for this widget
	     * @return void Echoes it's output
	     **/
		function form( $instance ) {
		
			$defaults = array( 
				'title' => '',
				'email' => '',
				'phone' => '',
			);
			$instance = wp_parse_args( (array) $instance, $defaults ); 
		
			echo '<p><label for="' . $this->get_field_id( 'title' ) . '">Title: <input class="widefat" id="' . $this->get_field_id( 'title' ) .'" name="' . $this->get_field_name( 'title' ) . '" value="' . esc_attr( $instance['title'] ) . '" /></label></p>';
			echo '<p><label for="' . $this->get_field_id( 'email' ) . '">Email: <input class="widefat" id="' . $this->get_field_id( 'email' ) .'" name="' . $this->get_field_name( 'email' ) . '" value="' . sanitize_email( $instance['email'] ) . '" /></label></p>';
			echo '<p><label for="' . $this->get_field_id( 'phone' ) . '">Phone: <input class="widefat" id="' . $this->get_field_id( 'phone' ) .'" name="' . $this->get_field_name( 'phone' ) . '" value="' . esc_attr( $instance['phone'] ) . '" /></label></p>';
		
		}
	}
}
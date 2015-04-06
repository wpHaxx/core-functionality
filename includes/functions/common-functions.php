<?php
/**
 * Common Functions used in functionality plugin.
 * 
 * @package 	Core_Functionality
 * @subpackage  CF_Twitter_Timeline
 * @since 		1.0.0
 * @author 		Joshua David Nelson <josh@joshuadnelson.com>
 * @copyright   Copyright (c) 2015, Joshua David Nelson
 * @license 	http://www.gnu.org/licenses/gpl-2.0.html GPLv2.0+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Log any errors for debugging.
 *
 * @since 1.0.0
 *
 * @uses error_log
 * @global WP_DEBUG
 *
 * @param string|array|object $message The item to be placed in the debug log.
 * @return void
 */
if( !function_exists( 'log_me' ) {
	function log_me( $message ) {
	    if ( WP_DEBUG === true ) {
	        if ( is_array( $message ) || is_object( $message ) ) {
	            error_log( 'Core Functionality Plugin Error: ' . print_r( $message, true ) );
	        } else {
	            error_log( 'Core Functionality Plugin Error: ' . $message );
	        }
	    }
	}
}
/**
 * Recursive in_array function.
 *
 * @since 1.0.0
 *
 * @param string $needle The needle.
 * @param string $haystack The haystack.
 * @param string $strict Option for strick search.
 * @return boolean
 */
if( ! function_exists( 'in_array_r' ) ) {
	function in_array_r( $needle, $haystack, $strict = false ) {
	    foreach( $haystack as $item ) {
	        if( ($strict ? $item === $needle : $item == $needle ) || ( is_array( $item ) && in_array_r( $needle, $item, $strict ) ) ) {
	            return true;
	        }
	    }

	    return false;
	}
}

/**
 * Random hex color.
 *
 * @since 1.0.0
 *
 * @link https://stackoverflow.com/questions/5614530/generating-a-random-hex-color-code-with-php/9901154#9901154
 *
 * @return string $color Random hex color
 */
if( !function_exists( 'rand_color' ) ) {
	function rand_color() {
	    return '#' . strtoupper(dechex(rand(0x000000, 0xFFFFFF)));
	}
}
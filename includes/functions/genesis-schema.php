<?php
/**
 * Schema for Genesis
 *
 * @package 	Core_Functionality
 * @subpackage  CF_Twitter_Timeline
 * @since 		1.0.0
 * @author 		Joshua David Nelson <josh@joshuadnelson.com>, Bill Erickson <bill@billerickson.net>
 * @copyright  Copyright (c) 2015, Joshua Nelson
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 *
 * Based upon Bill Erickson's Event Calendar Schema:
 * @link       https://github.com/billerickson/BE-Events-Calendar
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main class
 */
if( !class_exists( 'CF_Custom_Genesis_Schema' ) ) {
	class CF_Custom_Genesis_Schema {
	
		/**
		 * The constructor
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init' ) );	
		}
	
		/**
		 * Add genesis attributes/markups for custom post type
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function init() {
			add_filter( 'genesis_attr_entry', array( $this, 'event_entry_schema' ), 20 );
			add_filter( 'genesis_attr_content', array( $this, 'entry_content_schema' ), 20 );
			add_filter( 'genesis_attr_entry-title', array( $this, 'entry_title_itemprop' ), 20 );
			add_filter( 'genesis_attr_entry-content', array( $this, 'entry_content_itemprop' ), 20 );
			add_filter( 'genesis_post_title_output', array( $this, 'title_link' ), 20 );
		}
	
		/**
		 * Event Schema
		 *
		 * @since 1.0.0
		 * @return array $attr Array of attributes
		 */
		function event_entry_schema( $attr ) {

			// Only run on event
			if( ! 'session' == get_post_type() )
				return $attr;
			
			$attr['itemtype'] = 'http://schema.org/BusinessEvent';
			$attr['itemprop'] = '';
			$attr['itemscope'] = 'itemscope';
			return $attr;
		}
	
		/**
		 * Event Schema
		 *
		 * @since 1.0.0
		 * @return array $attr Array of attributes
		 */
		function entry_content_schema( $attr ) {

			if( is_singular( 'conference' ) ) {	
				$attr['itemtype'] = 'http://schema.org/BusinessEvent';
				$attr['itemprop'] = '';
				$attr['itemscope'] = 'itemscope';
			}
		
			return $attr;
		}

		/**
		 * Entry Title Itemprop
		 *
		 * @since 1.0.0
		 * @return array $attr Array of attributes
		 */
		function entry_title_itemprop( $attr ) {
			if( 'session' == get_post_type() )
				$attr['itemprop'] = '';
			return $attr;
		}
	
		/**
		 * Event Description Itemprop
		 *
		 * @since 1.0.0
		 * @return array $attr Array of attributes
		 */
		function entry_content_itemprop( $attr ) {
			if( 'session' == get_post_type() )
				$attr['itemprop'] = 'description';
			return $attr;
		}
	
		/**
		 * Title Link
		 *
		 * @since 1.0.0
		 * @return string $output Replacement titlelink with attributes
		 */
		function title_link( $output ) {
			if( 'session' == get_post_type() )
				$output = str_replace( 'rel="bookmark"', 'rel="bookmark" itemprop="url"', $output );
			return $output;
		}
	}

	new CF_Custom_Genesis_Schema;
}
<?php
/**
 * Plugin Name: Core Functionality
 * Plugin URI: http://github.com/joshuadavidnelson/core-functionality
 * Description: This contains all your site's core functionality so that it is theme independent.
 * Version: 1.0.0
 * Author: Joshua Nelson
 * Author URI: http://joshuadnelson.com
 * Github Plugin URI: http://github.com/joshuadavidnelson/core-functionality
 * Github Branch: master
 * License: GPL v2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package 	Core_Functionality
 * @subpackage  Core_Functionality
 * @since 		1.0.0
 * @author 		Joshua David Nelson <josh@joshuadnelson.com>
 * @copyright   Copyright (c) 2015, Joshua David Nelson
 * @license 	http://www.gnu.org/licenses/gpl-2.0.html GPLv2.0+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Core_Functionality Class
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'Core_Functionality' ) ) {
	final class Core_Functionality {
		/** Singleton */

		/**
		 * @var $instance The one true Core_Functionality
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * Main Core_Functionality Instance
		 *
		 * Insures that only one instance of Core_Functionality exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 * @static
		 * @staticvar array $instance
		 * @uses Core_Functionality::setup_constants() Setup the constants needed
		 * @uses Core_Functionality::includes() Include the required files
		 * @uses Core_Functionality::load_textdomain() load the language files
		 * @see IC3()
		 * @return The one true Core_Functionality
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Core_Functionality ) ) {
				self::$instance = new Core_Functionality;
				self::$instance->setup_constants();
				self::$instance->includes();
			}
			return self::$instance;
		}
		
		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'eec' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since 1.0.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'eec' ), '1.0.0' );
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function setup_constants() {

			// Plugin version
			if ( ! defined( 'CF_VERSION' ) ) {
				define( 'CF_VERSION', '1.0.0' );
			}

			// Plugin Folder Path
			if ( ! defined( 'CF_DIR' ) ) {
				define( 'CF_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'CF_URL' ) ) {
				define( 'CF_URL', plugin_dir_url( __FILE__ ) );
			}
			
			// Plugin Text Domain
			if ( ! defined( 'CF_DOMAIN' ) ) {
				define( 'CF_DOMAIN', 'core-funtionality' );
			}
		}
		
		/**
		 * Include required files
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function includes() {
 			add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
			
			if( !$this->is_supported() )
				return;
			
			// Custom Metaboxes 2
			if( file_exists( CF_DIR . 'includes/CMB2/init.php' ) ) {
				require_once CF_DIR . 'includes/CMB2/init.php';
				require_once CF_DIR . 'includes/functions/cmb-fields.php';
			}
			
			// Helper Classes
			if( file_exists( CF_DIR . 'includes/admin-notice-helper/admin-notice-helper.php' ) ) {
				require_once CF_DIR . 'includes/admin-notice-helper/admin-notice-helper.php';
			}
					
			// Post Types
			require_once CF_DIR . 'includes/post-types/class-cpt.php';
			require_once CF_DIR . 'includes/post-types/staff.php';
			require_once CF_DIR . 'includes/post-types/project.php';
			
			// Widgets
			require_once CF_DIR . 'includes/widgets/widget-contact.php';
			require_once CF_DIR . 'includes/widgets/widget-social.php';
		}

 		/**
 		 * Verify that the plugin's dependencies are active, then run your hooks.
 		 *
 		 * @since 1.0.0
 		 * @access public
 		 * @return void
 		 */
		public function plugins_loaded() {
			if( ! $this->is_supported() ) {
				add_action( 'admin_notices', array( $this, 'deactivate_admin_notice' ) );
				add_action( 'admin_init', array( $this, 'plugin_deactivate' ) );
				return;
			}
			
			// Admin styles & scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
			
			// Add query variables
			add_filter( 'query_vars', array( $this, 'register_query_var' ) );
			
			// Modify Search Query
			add_action( 'pre_get_posts', array( $this, 'modify_query' ) );
		}
		
		/**
 		 * Verify the plugin is supported, add checks for dependencies.
 		 *
 		 * @since 1.0.0
 		 * @static
 		 * @access public
 		 * @return boolean 
 		 */
		private static function is_supported() {
			return true;
		}
		
		/**
 		 * Deactivate plugin.
 		 *
 		 * @since 1.0.0
 		 * @return void
 		 */
		function plugin_deactivate() {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}

		/**
		 *  Output admin notices.
		 *
		 * @since 1.0.0
 		 * @access public
		 * @return void
		 */
		public function deactivate_admin_notice( $message = '', $class = 'error' ) {
			if( empty( $message ) ) {
				$message = __( 'Core Functionality has been deactived. It requires: OTHER PLUGINS.', 'ic3' );
			}
			echo '<div class="' . $class . '"><p>' . $message . '</p></div>';
			if ( isset( $_GET['activate'] ) )
				unset( $_GET['activate'] );
		}
		
		/**
		 * Admin styles
		 *
		 * @since 1.0.0
 		 * @access public
		 * @return void
		 */
		public function admin_styles() {
			wp_enqueue_style( 'core-admin-styles', CF_URL . 'assets/css/admin-styles.css', array(), CF_VERSION, false );
			
			// Admin java
			wp_enqueue_script( 'core-admin-js', CF_URL . 'assets/js/admin.js', array( 'jquery' ), CF_VERSION, false );
		}

		/**
		 * Add query variables, using `$vars[] = 'query-var';`
		 *
		 * @since 1.0.0
		 *
		 * @param array $vars The current query vars.
		 * @return array $vars The modified query vars.
		 */
		function register_query_var( $vars ) {
			//$vars[] = 'query-var';
			return $vars;
		}
		
		/**
		 * Modify query to remove a post type from search results, but keep all others
		 * 
		 * @param object $query The main query object.
		 */
		function modify_query( $query ) {
	
			// First, make sure this isn't the admin and is the main query, otherwise bail
			if( is_admin() || ! $query->is_main_query() )
				return;
	
			// If this is a search result query
			if( $query->is_search() ) {
				// Gather all searchable post types
				$in_search_post_types = get_post_types( array( 'exclude_from_search' => false ) );
				// The post type you're removing, in this example 'page'
				$post_type_to_remove = 'page';
				// Make sure you got the proper results, and that your post type is in the results
				if( is_array( $in_search_post_types ) && in_array( $post_type_to_remove, $in_search_post_types ) ) {
					// Remove the post type from the array
					unset( $in_search_post_types[ $post_type_to_remove ] );
					// set the query to the remaining searchable post types
					$query->set( 'post_type', $in_search_post_types );
				}
			}
		}
	}
} // End if class_exists check

/**
 * The main function responsible for returning the one true Core_Functionality
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $cf = CF(); ?>
 *
 * @since 1.0.0
 * @return object The one true Core_Functionality Instance
 */
function CF() {
	return Core_Functionality::instance();
}

// Get CORE Running
CF();
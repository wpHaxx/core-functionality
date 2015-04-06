<?php
/**
 * A custom post type class for Projects.
 * 
 * @package 	Core_Functionality
 * @subpackage  CF_Projects
 * @since 		1.0.0
 * @author 		Joshua David Nelson <josh@joshuadnelson.com>
 * @copyright   Copyright (c) 2015, Joshua David Nelson
 * @license 	http://www.gnu.org/licenses/gpl-2.0.html GPLv2.0+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Class
 */
if( !class_exists( 'CF_Projects' ) ) {	
	/**
	 * Core Functionality Projects class extending the custom post type helper class.
	 *
	 * Used to register the post type 'project' and set all the metaboxes, actions, etc.
	 *
	 * @since 1.0.0
	 */
	class CF_Projects extends CF_Custom_Post_Types {
	
		/**
		 * The main class instance.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		var $instance;
	
		/**
		 * The constructor.
		 *
		 * Registers the activation hook, runs the init function and sets the custom post type variables.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		function __construct() {
			$this->instance =& $this;
			
			$this->post_type_name = 'project';
			
			$this->post_type_display_name = ucfirst( $this->post_type_name );
			
			$this->post_editor_id = 'project_description';
			
			$this->labels = array(
				'name' 					=> 'Project',
				'description' 			=> 'Project Portfolio',
				'singular_name' 		=> 'Project',
				'menu_name' 			=> 'Projects',
				'add_new' 				=> 'Add Project',
				'add_new_item' 			=> 'Add Project',
				'edit' 		 			=> 'Edit Project',
				'edit_item' 			=> 'Edit Project',
				'new_item' 				=> 'New Project',
				'view' 		 			=> 'View Projects',
				'view_item' 			=> 'View Project',
				'search_items' 			=> 'Search Projects',
				'not_found' 			=> 'No Projects Found',
				'not_found_in_trash' 	=> 'No Projects in Trash',
			);
		
			$this->arguments = array(
				'labels'		=> $this->labels,
				'public'		=> true,
				'query_var' 	=> true,
				'show_ui' 		=> true,
				'show_in_menu' 	=> true,
				'has_archive'	=> false,
				'show_in_nav_menus' => false,
				'supports' 		=> array( 'title', 'editor', 'thumbnail' ),
		        'menu_icon' 	=> 'dashicons-portfolio',
				'menu_position' => 5,
				'rewrite'		=> array(
					'slug' => 'project',
					'with_front' => false,
					'feeds' => false,
					'pages' => false,
				), 
				'hierarchical'	=> false,
			);
			
			add_action( 'plugins_loaded', array( $this, 'init' ) );
			
		}
	
		/**
		 * The main plugin init function, adds all the hooks and sets all the class variables.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function init() {
		
			// Create Post Type
			add_action( 'init', array( $this, 'post_type' ) );
		
			// Edit admin side columnes
			add_filter( "manage_edit-{$this->post_type_name}_columns", array( $this, 'edit_columns' ) ) ;
			add_action( "manage_{$this->post_type_name}_posts_custom_column", array( $this, 'manage_columns' ), 10, 2 );
		
			// Change POst Edit Screen title placeholder
			add_action( 'gettext', array( $this, 'change_title_text' ) );
		
			// Set post update messages
			add_filter( 'post_updated_messages', array( $this, 'set_update_messages' ) );
		
			// Create Metaboxes (Custom Metaboxes and Fields)
			add_filter( 'cmb2_meta_boxes', array( $this, 'metaboxes' ) );
		
			// Change Order to match menu order
			add_action( 'pre_get_posts', array( $this, 'custom_query' ) );
		
			// Save related meta
			add_action( 'save_post', array( $this, 'on_save' ), 10, 3 );
		
			// Move Post Editor Into Long Description for Project Post Type
			add_action( 'admin_enqueue_scripts', array( $this, 'move_posteditor' ), 10, 1 );
			
			// Admin styles
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		}
	
		/**
		 * Clear transients on post save.
		 *
		 * @since 1.0.0
		 *
		 * @uses get_the_terms
		 * @uses is_wp_error
		 * @uses get_transient
		 * @uses delete_transient
		 *
		 * @return void
		 */
		function on_save( $post_id, $post, $update ) {

		    // If this isn't the correct post type, don't update it.
		    if( $this->post_type_name != $post->post_type ) {
		        return;
		    }
		
			// Bail out if running an autosave, ajax, cron, or revision.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				return;
			}
			if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
				return;
			}
			if ( wp_is_post_revision( $post_id ) ) {
				return;
			}
		
			// Bail out if the user doesn't have the correct permissions
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		
			// Do your stuff on save here
		
		}
	
		/**
		 * Modify the query.
		 *
		 * @since 1.0.0
		 *
		 * @uses $query->set
		 * @uses $query->is_main_query
		 * @uses $query->is_feed
		 * @uses is_tax
		 * @uses is_post_type_archive
		 * @uses is_admin
		 *
		 * @param object $query The main query object.
		 *
		 * @return void
		 */
		function custom_query( $query ) {
			if( $query->is_main_query() & !$query->is_feed() && is_post_type_archive( $this->post_type_name ) && !is_admin() ) {
			//	$query->set( 'orderby', 'meta_value' );
			//	$query->set( 'meta_key', 'project_date' );
			//	$query->set( 'order', 'ASC' );
			}
		}
	
		/**
		 * Edit The Post Edit Admin Column titles.
		 *
		 * @since 1.0.0
		 *
		 * @link http://devpress.com/blog/custom-columns-for-custom-post-types/
		 *
		 * @param array $columns The current post type column titles.
		 *
		 * @return array $columns The modified post type column titles.
		 */
		function edit_columns( $columns ) {
	
			$columns = array(
				'cb'        => '<input type="checkbox" />',
				'title'		=> __( 'Title', CF_DOMAIN ),
				'desc'		=> __( 'Short Description', CF_DOMAIN ),
				'image'		=> __( 'Image', CF_DOMAIN ),
			);
	
			return $columns;
		}
	
		/**
		 * Edit the post type admin column content.
		 *
		 * @since 1.0.0
		 *
		 * @link http://devpress.com/blog/custom-columns-for-custom-post-types/
		 *
		 * @param string $column The current column slug.
		 * @param string $post_id The current post id.
		 *
		 * @return void
		 */
		function manage_columns( $column, $post_id ) {
		
			switch( $column ) {
			
				// The Description
				case 'desc':
					$description = get_the_content( $post_id );
					if( !empty( $description ) )
						echo wp_trim_words( $description, 60, '...' );
				break;
			
				// Display the 'image' column
				case 'image' :
			
					$thumb = has_post_thumbnail( $post_id );
					if( $thumb )
						echo get_the_post_thumbnail( $post_id, 'thumbnail' );
				
				break;
			
				// Just break out of the switch statement for everything else.
				default :
				break;
			}
		}
	
		/**
		 * Filter the CMB2 custom metaboxes for this post type.
		 *
		 * @since 1.0.0
		 *
		 * @param array $meta_boxes The CMB2 metaboxes.
		 * 
		 * @return array $meta_boxes The modified metaboxes.
		 */
		function metaboxes( $meta_boxes ) {
			$prefix = "{$this->post_type_name}_";
		
			$meta_boxes["{$this->post_type_name}_details"] = array(
				'id'         => "{$this->post_type_name}_details",
				'title'      => "{$this->post_type_display_name} Details",
				'object_types' => array( $this->post_type_name ), 
				'context'    => 'normal',
				'priority'   => 'high',
				'show_names' => true, 
				'fields'     => array(
			        array(
			        	'name' => 'Client',
			        	'desc' => 'Client(s) name',
			        	'id' => $prefix . 'client',
			       		'type' => 'text_medium',
			        ),
					array(
				        'name' => 'Client url',
				        'desc' => 'Website for the client',
				        'id' => $prefix . 'client_url',
				        'type' => 'text_medium',
					),
					array(
						'name' => 'Project Location',
						'desc' => 'City, State',
						'id' => $prefix . 'location',
						'type' => 'text_medium',
					),
				),
			);
		
			/* ------ [ Project Long Bio ] ------ */
			$meta_boxes[] = array(
			    'id' => $this->post_editor_id,
			    'title' => 'Project Description',
			    'object_types' => array( $this->post_type_name ), // post type
				'context' => 'normal',
				'priority' => 'high',
				'show_names' => true, // Show field names left of input
			    	'fields' => array( )
			); 
		
			return $meta_boxes;
		}
		
		/**
		 * Admin stylesheets for enqueuing.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		function admin_styles() {
			wp_enqueue_style( 'cf-admin-styles', CF_URL . '/assets/css/admin-styles.css', array() );
		}
	} // end class
	
	new CF_Projects();
}
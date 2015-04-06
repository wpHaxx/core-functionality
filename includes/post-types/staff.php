<?php
/**
 * A class loaded with tools for custom post tpes
 * 
 * @package 	Core_Functionality
 * @subpackage  Core_Staff
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
if( !class_exists( 'CF_Staff' ) ) {
	class CF_Staff extends CF_Custom_Post_Types {
	
		var $instance;
	
		/**
		 * Build the thing
		 */
		function __construct() {
			$this->instance =& $this;
			
			$this->post_type_name = 'staff';
			
			$this->post_type_display_name = ucfirst( $this->post_type_name );
		
			$this->labels = array(
				'name' 					=> 'Staff',
				'singular_name' 		=> 'Staff',
				'menu_name' 			=> 'Staff Members',
				'add_new' 				=> 'Add Staff',
				'add_new_item' 			=> 'Add Staff',
				'edit' 		 			=> 'Edit Staff',
				'edit_item' 			=> 'Edit Staff',
				'new_item' 				=> 'New Staff',
				'view' 		 			=> 'View Staff Members',
				'view_item' 			=> 'View Staff',
				'search_items' 			=> 'Search Staff Members',
				'not_found' 			=> 'No Staff Members Found',
				'not_found_in_trash' 	=> 'No Staff Members in Trash',
			);
		
			$this->arguments = array(
				'labels'			 => $this->labels,
				'public'			 => true,
				'publicly_queryable' => true,
				'query_var' 		 => true,
				'show_ui' 			 => true,
				'show_in_menu' 		 => true,
				'show_in_nav_menus'  => false,
				'has_archive'		 => true,
				'supports' 			 => array( 
					'title',
					'thumbnail'
				),
		        'menu_icon' 		=> 'dashicons-groups',
				'menu_position' 	=> 5,
				'rewrite'			=> array( 
					'slug'			=> 'staff', 
					'with_front'	=> false,
					'feeds'			=> false,
				), 
				'hierarchical'		=> false,
			);

			add_action( 'plugins_loaded', array( $this, 'init' ) );
		}
	
		/**
		 * Make it so!
		 */
		public function init() {
		
			// Create Post Type
			add_action( 'init', array( $this, 'post_type' ) );
		
			// Create Taxonomy
			add_action( 'init', array( $this, 'taxonomy' ) );
		
			// Save related meta
			add_action( 'save_post', array( $this, 'taxonomy_save' ), 10, 3 );
		
			// Edit admin side columnes
			add_filter( "manage_edit-{$this->post_type_name}_columns", array( $this, 'edit_columns' ) ) ;
			add_action( "manage_{$this->post_type_name}_posts_custom_column", array( $this, 'manage_columns' ), 10, 2 );
		
			// Change POst Edit Screen title placeholder
			add_action( 'gettext', array( $this, 'change_title_text' ) );
		
			// Set post update messages
			add_filter( 'post_updated_messages', array( $this, 'set_update_messages' ) );
		
			// Create Metaboxes (Custom Metaboxes and Fields)
			add_filter( 'cmb2_meta_boxes', array( $this, 'metaboxes' ) );
		
			// Save related meta
			add_action( 'save_post', array( $this, 'on_save' ), 10, 3 );
			add_action( 'delete_post', array( $this, 'on_delete' ), 10, 1 );
		
			// Change Order to match menu order
			add_action( 'pre_get_posts', array( $this, 'custom_query' ) );
		}
	
		/**
		 * Update things on the save
		 * @since 1.0.0
		 */
		function on_delete( $post_id ) {

		    // If this isn't the correct post type, don't update it.
		    if( $this->post_type_name != get_post_type( $post_id ) ) {
		        return;
		    }
		
			// Bail out if the user doesn't have the correct permissions
			if ( ! current_user_can( 'delete_posts', $post_id ) ) {
				return;
			}
		
			// Do stuff on delete
			
		}
	
		/**
		 * Update things on the save
		 * @since 1.0.0
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
		
			// Do stuff
			
		}
	
		/** 
		 * Build Taxonomy
		 */
		function taxonomy() {
			$labels = array(
				'name' => __( 'Departments' ),
				'singular_name' => __( 'Department' ),
				'menu_name' => __( 'Departments' ),
				'all_items' => __( 'All Departments' ),
				'edit_item' => __( 'Edit Department' ),
				'view_item' => __( 'View Department' ),
				'update_item' => __( 'Update Department' ),
				'add_new_item' => __( 'Add New Department' ),
				'new_item_name' => __( 'New Department Name' ),
				'search_items' => __( 'Search Departments' ),
				'separate_items_with_commas' => __( 'Separate departments with commas' ),
				'add_or_remove_items' => __( 'Add or remove departments' ),
				'choose_from_most_used' => __( 'Choose from the most used departments' ),
				'not_found' => __( 'No departments found' ),
			);

			register_taxonomy( 'department', array( $this->post_type_name ), 
				array(
					'hierarchical' => false,
					'public' => true,
					'show_in_nav_menus' => false,
					'show_tagcloud' => false,
					'labels' => $labels,
					'show_ui' => true,
					'query_var' => false,
					'rewrite' => false,
				)
			);
		}
	
	    /**
	     * Delete transients on taxonomy save.
	     *
	     * @since 1.0.0
		 *
		 * @uses get_transient
		 * @uses delete_transient
	     *
	     * @param int $term_id The taxonomy term id.
	     * @return void
	     */
		function taxonomy_save( $term_id ) {
			// Do stuff
		}
	
		/** 
		 * Modify archive query to display by menu order and only show posts with bios
		 * @since 1.0.0
		 */
		function custom_query( $query ) {
	
			if( $query->is_main_query() & !$query->is_feed() && is_post_type_archive( $this->post_type_name ) && !is_admin() ) {
				if( !is_admin() ) {
					$query->set( 'orderby', 'menu_order' );
					$query->set( 'order', 'ASC' );
					$meta_query = array(
						array(
							'key' => 'staff_bio',
							'compare' => 'EXISTS'
						)
					);
					$query->set( 'meta_query', $meta_query );
					$query->set( 'posts_per_page', '1000' );
				}
			}
 
		}
	
		/**
		 * Edit Column Titles
		 * @link http://devpress.com/blog/custom-columns-for-custom-post-types/
		 */
		function edit_columns( $columns ) {
	
			$columns = array(
				'cb'         => '<input type="checkbox" />',
				'title'		 => __( 'Name', CF_DOMAIN ),
				'bio'		 => __( 'Bio', CF_DOMAIN ),
				'image'		 => __( 'Thumbnail', CF_DOMAIN ),
				'department' => __( 'Department', CF_DOMAIN ),
			);
	
			return $columns;
		}
	
		/**
		 * Edit Column Content
		 * @link http://devpress.com/blog/custom-columns-for-custom-post-types/
		 */
		function manage_columns( $column, $post_id ) {		
			switch( $column ) {

				// The Staff Bio
				case 'bio':
					$text = esc_html( get_post_meta( $post_id, 'staff_bio', true ) );
					if( $text ){
						$trimmed = wp_trim_words( $text, $num_words = 35, '...' );
						echo $trimmed;
					}
				break;
				
				// Display the 'image' column
				case 'image' :
			
					$thumb = has_post_thumbnail( $post_id );
					if( $thumb )
						echo get_the_post_thumbnail( $post_id, 'thumbnail');
	
				break;
				
				// Display the 'department' column
				case 'department' :
				
					$departments = wp_get_post_terms( $post_id, 'department', array( 'orderby' => 'name', 'order' => 'ASC' ) );
					if( !is_wp_error( $departments ) && is_array( $departments ) && !empty( $departments ) ) {
						$output = '';
						foreach( $departments as $dept ) {
							$url = esc_url( get_edit_term_link( $dept->term_id, 'department', 'staff' ) );
							if( $url ) {
								$output .= '<a href="' . $url . '">' . $dept->name . '</a>, ';
							}
						}
						$output = rtrim( $output, ', ' );
					}
				
					if( isset( $output ) && !empty( $output ) )
						echo $output;
	
				break;
			
				// Just break out of the switch statement for everything else.
				default :
				break;
			}
		}
	
		/**
		 * Create Metaboxes
		 */
		function metaboxes( $meta_boxes ) {
			$prefix = "{$this->post_type_name}_";
		
			$meta_boxes["{$this->post_type_name}_details"] = array(
				'id'         => "{$this->post_type_name}_details",
				'title'      => "{$this->post_type_display_name} Details",
				'object_types'      => array( $this->post_type_name ), 
				'context'    => 'normal',
				'priority'   => 'high',
				'show_names' => true, 
				'fields'     => array(
					array(
					    'name' => 'Biography',
					    'desc' => '',
					    'id' => $prefix . 'bio',
					    'type' => 'wysiwyg',
					    'options' => array(
					        'wpautop' => true,
					        'media_buttons' => false,
					        'textarea_rows' => get_option( 'default_post_edit_rows', 10 ),
					        'tinymce' => true,
					        'quicktags' => false, 
					    ),
					),
					array(
					    'name' => 'Contact Email',
					    'id'   => $prefix . 'email',
					    'type' => 'text_email',
					),
				),
			);
		
			return $meta_boxes;
		}
	}
	
	new CF_Staff();
}
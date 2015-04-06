<?php
/**
 * Helper class for custom post types
 * 
 * @package 	Core_Functionality
 * @subpackage  Core_Custom_Post_Types
 * @since 		1.0.0
 * @author 		Joshua David Nelson <josh@joshuadnelson.com>
 * @copyright   Copyright (c) 2015, Joshua David Nelson
 * @license 	http://www.gnu.org/licenses/gpl-2.0.html GPLv2.0+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The main class
 */
if( ! class_exists( 'CF_Custom_Post_Types' ) ) {
	class CF_Custom_Post_Types {
		
		/**
		 * The post type labels argument for register_post_type.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		protected $labels;
		
		/**
		 * The post type arguments for register_post_type.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		protected $arguments;
		
		/**
		 * The metabox id for the post editor, if using the move-post-editor function.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		protected $post_editor_id = null;
		
		/**
		 * The post type slug.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $post_type_name;
		
		/**
		 * The post type nicename.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $post_type_display_name;
		
		/**
		 * The constructor.
		 *
		 * @since 1.0.0
		 */
		function __construct() {
			register_activation_hook( __FILE__, array( $this, 'activation_hook' ) );
		}
		
		/**
		 * Flush the rewrite rules on activation
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function activation_hook() {
			flush_rewrite_rules();
		}

		/**
		 * Build the post type
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function post_type() {
			
			// Make sure the labels argument is set
			if( !empty( $this->labels ) && ( !isset( $this->arguments['labels'] ) || empty( $this->arguments[ 'labels' ] ) ) )
				$this->arguments['labels'] = $this->$labels;
		
			// if there are arguments provided, create the post type
			if( !empty( $this->arguments ) && !empty( $this->post_type_name ) )
				register_post_type( $this->post_type_name, $this->arguments );
		}

		/**
		 * Change post type title prompt in the edit screen.
		 *
		 * @since 1.0.0
		 *
		 * @param string $translation 
		 * @return void
		 */
		public function change_title_text( $translation ) {
			global $post;
			if( isset( $post ) ) {
				switch( $post->post_type ){
					case $this->post_type_name :
						if( $translation == 'Enter title here' ) return 'Enter ' . $this->post_type_display_name . ' Title Here';
					break;
				}
			}
			return $translation;
		}
		/**
		 * Set post update messages.
		 *
		 * @since 1.0.0
		 *
		 * @param string $messages 
		 * @return void
		 */
		public function set_update_messages( $messages ) {
			global $post;
			if( !isset( $post->post_type ) )
				return $messages;
		
			if( $post->post_type == $this->post_type_name ) {
			
				$obj = get_post_type_object( $post->post_type );
				$singular = $obj->labels->singular_name;
				$messages[$post->post_type] = array(
					0  => '', // Unused. Messages start at index 1.
					1  => __( $singular . ' updated.' ),
					2  => __( 'Custom field updated.' ),
					3  => __( 'Custom field deleted.' ),
					4  => __( $singular . ' updated.' ),
					/* translators: %s: date and time of the revision */
					5  => isset( $_GET['revision'] ) ? sprintf( __( $singular . ' restored to revision from %s' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
					6  => __( $singular . ' published.' ),
					7  => __( $singular . ' saved.' ),
					8  => __( $singular . ' submitted.' ),
					9  => sprintf(
						__( $singular . ' scheduled for: <strong>%1$s</strong>.' ),
						// translators: Publish box date format, see http://php.net/date
						date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) )
					),
					10 => __( $singular . ' draft updated.' )
				);
				
				// Only add anchor links if the post type is publically queryable
				if ( $obj->publicly_queryable ) {
					$permalink = get_permalink( $post->ID );
			
					$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View ' . $singular ) );
					$messages[ $post->post_type ][1] .= $view_link;
					$messages[ $post->post_type ][6] .= $view_link;
					$messages[ $post->post_type ][9] .= $view_link;
			
					$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
					$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview ' . $singular ) );
					$messages[ $post->post_type ][8]  .= $preview_link;
					$messages[ $post->post_type ][10] .= $preview_link;
				}
		
			}
		
			return $messages;
		}
	
		/** 
		 * Remove quick edit row action.
		 *
		 * @since 1.0.0
		 *
		 * @param array $actions The row actions.
		 * @return array $actions The modified row actions
		 */
		public function remove_quick_edit( $actions ) {
			global $post;
			if( is_admin() && $post->post_type === $this->post_type_name ) {
				if( isset( $actions['inline hide-if-no-js'] ) )
					unset( $actions['inline hide-if-no-js'] );
			}
			return $actions;
		}
	
		/** 
		 * Remove view link.
		 *
		 * @since 1.0.0
		 *
		 * @param array $actions The row actions.
		 * @return array $actions The modified row actions
		 */
		public function remove_view_link( $actions ) {
			global $post;
			if( is_admin() && $post->post_type === $this->post_type_name ) {
				if( isset( $actions['view'] ) )
					unset( $actions['view'] );
			}
			return $actions;
		}
	
		/** 
		 * Remove Edit from bulk options.
		 *
		 * @since 1.0.0
		 *
		 * @param array $actions The bulk actions.
		 * @return array $actions The modified bulk actions.
		 */
		public function remove_edit_bulk_action( $actions ) {
			if( isset( $actions[ 'edit' ] ) )
				unset( $actions[ 'edit' ] );
			
			return $actions;
		}
	
		/**
		 * Remove post meta values.
		 *
		 * Handy for the on_save hook.
		 *
		 * @since 1.0.0
		 *
		 * @uses remove_post_meta()
		 * @uses log_me()
		 *
		 * @param int|array|object $posts Accepts either the post ID, post object, or an array of either.
		 * @param string|array $meta The post meta to delete, either a single string or array of strings.
		 * @return void
		 */
		public function remove_meta( $posts, $metas ) {
			if( is_array( $metas ) ) {
				foreach( $metas as $meta ) {
					remove_post_meta( $posts, $meta );
				}
			} elseif( is_string( $metas ) ) {
				remove_post_meta( $posts, $metas );
			} else {
				if( function_exists( 'log_me' ) )
					log_me( 'Invalid meta type in remove_post_meta' );
			}
		}

		/**
		 * Remove a post meta value.
		 *
		 * Handy for the on_save hook.
		 *
		 * @since 1.0.0
		 *
		 * @uses delete_post_meta()
		 * @uses log_me()
		 *
		 * @param int|array|object $posts Accepts either the post ID, post object, or an array of either.
		 * @param string $meta The post meta to delete.
		 * @return void
		 */
		public function remove_post_meta( $posts, $meta ) {
			$success = false;
			if( is_array( $posts ) ) {
				foreach( $posts as $post ) {
					$success = false;
					if( is_object( $post ) && isset( $post->ID ) ) {
						$success = delete_post_meta( $post->ID, $meta );
					} elseif( is_numeric( $post ) ) {
						$success = delete_post_meta( $post->ID, $meta );
					}
					if( !$success )
						if( function_exists( 'log_me' ) )
							log_me( "Unable to deleta meta: {$meta} from post {$post_id}" );
				}
			} elseif( is_numeric( $posts ) || ( is_object( $posts ) && isset( $posts->ID ) ) ) {
				if( is_numeric( $posts ) ) {
					$post_id = intval( $posts );
				} elseif( is_object( $posts ) ) {
					$post_id = $posts->ID;
				}
				$success = delete_post_meta( $post_id, $meta );
				if( !$success )
					if( function_exists( 'log_me' ) )
						log_me( "Unable to deleta meta: {$meta} from post {$post_id}" );
			}  else {
				if( function_exists( 'log_me' ) )
					log_me( 'Invalid post argument in remove_post_meta' );
			}
			return $success;
		}

		/**
		 * Set the post to a draft, useful on save.
		 *
		 * @since 1.0.0
		 *
		 * @uses log_me()
		 * @uses get_post_status()
		 * @uses wp_update_post()
		 *
		 * @param int $post_id The ID of the post to update.
		 * @return boolean True on success, false on failure.
		 */
		public function set_post_status( $post_id, $status = 'draft' ) {
			$stati = get_post_stati();
			if( is_numeric( $post_id ) && is_string( $status ) && in_array( $status, $stati ) ) {
				$post_id = intval( $post_id );
			} else {
				return false;
			}
	
			// if it's not already a draft, make it so!
			if( get_post_status( $post_id ) != $status ) {
				$args = array(
					'ID' => $post_id,
					'post_status' => $status
				);
				$id = wp_update_post( $args );
				if( $id != 0 ) {
					return true;
				} else {
					if( function_exists( 'log_me' ) )
						log_me( "Unable to update post {$post_id}" );
					return false;
				}
			} else {
				if( function_exists( 'log_me' ) )
					log_me( 'Post status already set' );
				return false;
			}
		}
	
		/**
		 * Set the status of the post children.
		 *
		 * @since 1.0.0
		 *
		 * @param int $post_id Parent post id.
		 * @param string $status The new status to set children to.
		 * @param string|array $child_status The status of children to find and set.
		 * @return void
		 */
		function set_children_status( $post_id, $new_status, $child_status = 'any' ) {
			$children = get_children( array(
				'post_parent' => $post_id,
				'post_status' => $child_status,
				'post_type'   => 'any'
			) );
			if( is_array( $children ) && !empty( $children ) ) {
				foreach( $children as $child ) {
					$success = $this->set_post_status( $child->ID, $new_status );
				}
			}
		}

		/**
		 * Remove the post's parent.
		 *
		 * Handy for the on_save hook.
		 *
		 * @since 1.0.0
		 *
		 * @uses log_me()
		 * @uses wp_update_post()
		 *
		 * @param int|object $post The post object or post ID.
		 * @return boolean True on success, false on failure.
		 */
		public function remove_post_parent( $post ) {
			if( is_numeric( $post ) ) {
				$post_id = intval( $post );
			} elseif( is_object( $post ) && isset( $post->ID ) && isset( $post->post_parent ) && is_numeric( $post->post_parent ) ) {
				$post_id = $post->ID;
				$current_parent = $post->post_parent;
			} else {
				return false;
			}
			
			// If the post doesn't have a parent, bail.
			if( !wp_get_post_parent_id( $post_id ) )
				return false;
			
			$args = array(
				'ID' => $post_id,
				'post_parent' => ''
			);
			$id = wp_update_post( $args );
			if( $id != 0 ) {
				return true;
			} else {
				if( function_exists( 'log_me' ) )
					log_me( "Unable to update post parent for post {$post_id}" );
				return false;
			}
		}
	
		/**
		 * Set the (potentially) new parent to the post.
		 *
		 * Handy for the on_save hook.
		 *
		 * @param string $post_id Current post id.
		 * @param string $new_parent Potential new parent id.
		 *
		 * @return boolean True on success, false on failure.
		 */
		public function new_post_parent( $post_id, $new_parent ) {
			$original_parent = wp_get_post_parent_id( $post_id );
			if( !$original_parent || ( $original_parent && $original_parent != $new_parent ) ) {
				$args = array(
					'ID' => $post_id,
					'post_parent' => $new_parent
				);
				$success = wp_update_post( $args );
				if( !$success ) {
					if( function_exists( 'log_me' ) )
						log_me( 'Unable to update session post_parent field' );
					return false;
				} else {
					return true;
				}
			}
			return false;
		}
		
		/**
		 * Move Post Editor Into Long Description for Project Post Type.
		 *
		 * @since 1.0.0
		 *
		 * @param string $hook The page hook.
		 * @return void
		 */
		function move_posteditor( $hook ) {
		  	if( $hook == 'post.php' || $hook == 'post-new.php' ) {
				wp_enqueue_script( 'jquery' );
				add_action( 'admin_print_footer_scripts', array( $this, 'move_posteditor_script' ) );
		  	}
		}
	
		/**
		 * Script to move the editor into the metabox.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		function move_posteditor_script() {
			if( isset( $this->post_editor_id ) ) {
				?>
				<script type="text/javascript">
					jQuery('#postdiv, #postdivrich').prependTo('#<?php echo $this->post_editor_id; ?> .inside');
				</script>
				<style type="text/css">
						#normal-sortables {margin-top: 20px;}
						#titlediv { margin-bottom: 0px; }
						#postdiv.postarea, #postdivrich.postarea { margin:0; }
						#post-status-info { line-height:1.4em; font-size:13px; }
						#custom_editor .inside { margin:2px 6px 6px 6px; }
						#ed_toolbar { display:none; }
						#postdiv #ed_toolbar, #postdivrich #ed_toolbar { display:block; }
				</style>
				<?php
			}
		}
	}
}
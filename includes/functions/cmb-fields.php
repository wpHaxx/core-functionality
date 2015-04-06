<?php
/**
 * Custom CMB2 Fields.
 *
 * @package 	Core_Functionality
 * @subpackage  CF_CMB_Fields
 * @since 		1.0.0
 * @author 		Joshua David Nelson <josh@joshuadnelson.com>
 * @copyright   Copyright (c) 2015, Joshua David Nelson
 * @license 	http://www.gnu.org/licenses/gpl-2.0.html GPLv2.0+
 *
 * Refer to the walkthrough:
 * @link https://github.com/WebDevStudios/CMB2/wiki/Adding-your-own-field-types
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Create it
if( ! class_exists( 'CF_CMB_Fields' ) ) {
	// Main Class
	class CF_CMB_Fields {
		
		/**
		 * The constructor.
		 *
		 * @return void
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init' ) );
		}
	
		/**
		 * Do all the functions.
		 *
		 * @return void
		 */
		public function init() {
			// Address field
			add_action( 'cmb2_render_address', array( $this, 'cmb2_render_address_field' ), 10, 5 );
			add_action( 'cmb2_render_text_number', array( $this, 'cmb2_render_text_number' ), 10, 5 );
			
			// Text number field
			add_filter( 'cmb2_sanitize_text_number', array( $this, 'cmb2_sanitize_text_number' ), 10, 2 );
			
			// Phone number field
			add_action( 'cmb2_render_phone_number', array( $this, 'cmb2_render_phone_number' ), 10, 5 );
			add_filter( 'cmb2_sanitize_phone_number', array( $this, 'cmb2_sanitize_phone_number' ), 10, 2 );
			
			// Uniqiue ID field
			add_filter( 'cmb2_render_unique_id', array( $this, 'cmb2_render_unique_id' ), 10, 5 );
			add_filter( 'cmb2_sanitize_unique_id', array( $this, 'cmb2_sanitize_unique_id' ), 10, 3 );
		}

		/**
		 * Render the address field.
		 * 
		 * @since 1.0.0
		 *
		 * @param array $field_args 
		 * @param string $escaped_value 
		 * @param int $object_id 
		 * @param string $object_type 
		 * @param object $field_type_object 
		 *
		 * @return void
		 */
		function cmb2_render_address_field( $field_args, $value, $object_id, $object_type, $field_type_object ) {

		    $state_list = array(
				'AL'=>'Alabama',
				'AK'=>'Alaska',
				'AZ'=>'Arizona',
				'AR'=>'Arkansas',
				'CA'=>'California',
				'CO'=>'Colorado',
				'CT'=>'Connecticut',
				'DE'=>'Delaware',
				'DC'=>'District Of Columbia',
				'FL'=>'Florida',
				'GA'=>'Georgia',
				'HI'=>'Hawaii',
				'ID'=>'Idaho',
				'IL'=>'Illinois',
				'IN'=>'Indiana',
				'IA'=>'Iowa',
				'KS'=>'Kansas',
				'KY'=>'Kentucky',
				'LA'=>'Louisiana',
				'ME'=>'Maine',
				'MD'=>'Maryland',
				'MA'=>'Massachusetts',
				'MI'=>'Michigan',
				'MN'=>'Minnesota',
				'MS'=>'Mississippi',
				'MO'=>'Missouri',
				'MT'=>'Montana',
				'NE'=>'Nebraska',
				'NV'=>'Nevada',
				'NH'=>'New Hampshire',
				'NJ'=>'New Jersey',
				'NM'=>'New Mexico',
				'NY'=>'New York',
				'NC'=>'North Carolina',
				'ND'=>'North Dakota',
				'OH'=>'Ohio',
				'OK'=>'Oklahoma',
				'OR'=>'Oregon',
				'PA'=>'Pennsylvania',
				'RI'=>'Rhode Island',
				'SC'=>'South Carolina',
				'SD'=>'South Dakota',
				'TN'=>'Tennessee',
				'TX'=>'Texas',
				'UT'=>'Utah',
				'VT'=>'Vermont',
				'VA'=>'Virginia',
				'WA'=>'Washington',
				'WV'=>'West Virginia',
				'WI'=>'Wisconsin',
				'WY'=>'Wyoming'
			);

		    $value = wp_parse_args( $value, array(
				'location-title' => '',
		        'address-1' 	 => '',
		        'address-2' 	 => '',
		        'city'      	 => '',
		        'state'      	 => '',
		        'zip'        	 => '',
		    ) );

		    $state_options = '';
		    foreach ( $state_list as $abrev => $state ) {
		        $state_options .= '<option value="'. $abrev .'" '. selected( $value['state'], $abrev, false ) . '>' . $state .'</option>';
		    }

		    ?>
		    <div><p><label for="<?php echo $field_type_object->_id( '_location_title' ); ?>">Location Title</label></p>
		        <?php echo $field_type_object->input( array(
		            'name'  => $field_type_object->_name( '[location-title]' ),
		            'id'    => $field_type_object->_id( '_location_title' ),
		            'value' => $value['location-title'],
		            'desc'  => '<em>For instance, "University of Washington"</em>',
		        ) ); ?>
			</div>
		    <div><p><label for="<?php echo $field_type_object->_id( '_address_1' ); ?>">Address 1</label></p>
		        <?php echo $field_type_object->input( array(
		            'name'  => $field_type_object->_name( '[address-1]' ),
		            'id'    => $field_type_object->_id( '_address_1' ),
		            'value' => $value['address-1'],
		            'desc'  => '',
		        ) ); ?>
		    </div>
		    <div><p><label for="<?php echo $field_type_object->_id( '_address_2' ); ?>'">Address 2</label></p>
		        <?php echo $field_type_object->input( array(
		            'name'  => $field_type_object->_name( '[address-2]' ),
		            'id'    => $field_type_object->_id( '_address_2' ),
		            'value' => $value['address-2'],
		            'desc'  => '',
		        ) ); ?>
		    </div>
		    <div class="alignleft"><p><label for="<?php echo $field_type_object->_id( '_city' ); ?>'">City</label></p>
		        <?php echo $field_type_object->input( array(
		            'class' => 'cmb2_text_medium',
		            'name'  => $field_type_object->_name( '[city]' ),
		            'id'    => $field_type_object->_id( '_city' ),
		            'value' => $value['city'],
		            'desc'  => '',
		        ) ); ?>
		    </div>
		    <div class="alignleft"><p><label for="<?php echo $field_type_object->_id( '_state' ); ?>'">State</label></p>
		        <?php echo $field_type_object->select( array(
		            'name'    => $field_type_object->_name( '[state]' ),
		            'id'      => $field_type_object->_id( '_state' ),
		            'desc'    => '',
		            'options' => $state_options,
		        ) ); ?>
		    </div>
		    <div class="alignleft"><p><label for="<?php echo $field_type_object->_id( '_zip' ); ?>'">Zip</label></p>
		        <?php echo $field_type_object->input( array(
		            'class' => 'cmb2_text_small',
		            'name'  => $field_type_object->_name( '[zip]' ),
		            'id'    => $field_type_object->_id( '_zip' ),
		            'value' => $value['zip'],
		            'desc'  => '',
		        ) ); ?>
		    </div>
		    <?php
		    echo $field_type_object->_desc( true );

		}

		/**
		 * Render the text number field.
		 *
		 * @since 1.0.0
		 *
		 * @param array $field_args 
		 * @param string $escaped_value 
		 * @param int $object_id 
		 * @param string $object_type 
		 * @param object $field_type_object
		 *
		 * @return void
		 */
		function cmb2_render_text_number( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {
		    echo $field_type_object->input( array( 'class' => 'cmb2_text_small', 'type' => 'number' ) );
		}
	
		/**
		 * Sanitize the text number field.
		 *
		 * @since 1.0.0
		 *
		 * @param string $null 
		 * @param string $new 
		 * @return string $new The sanitized field.
		 */
		function cmb2_sanitize_text_number( $null, $new ) {
		    if( !is_int( $new ) )
				$new = '';

		    return $new;
		}
	
		/**
		 * Render the phone number field
		 *
		 * @since 1.0.0
		 *
		 * @param array $field_args 
		 * @param string $escaped_value 
		 * @param int $object_id 
		 * @param string $object_type 
		 * @param object $field_type_object
		 *
		 * @return void
		 */
		function cmb2_render_phone_number( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {
		    echo $field_type_object->input( array( 'class' => 'cmb2_text_medium', 'type' => 'phone_number' ) );
		}
	
		/**
		 * Sanitize the phone number field.
		 *
		 * @since 1.0.0
		 *
		 * @param string $null 
		 * @param string $new 
		 * @return string $new The sanitized field.
		 */
		function cmb2_sanitize_phone_number( $override, $new ) {
			$regex = "/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i";
			if( preg_match( $regex, $new ) ) {
				$new = esc_attr( $new );
			} else {
				$new = '';
			}
		    return $new;
		}
	
		/**
		 * Render the uniqiue id field.
		 *
		 * @since 1.0.0
		 *
		 * @param array $field_args 
		 * @param string $escaped_value 
		 * @param int $object_id 
		 * @param string $object_type 
		 * @param object $field_type_object
		 *
		 * @return void
		 */
		function cmb2_render_unique_id( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {
		    echo $field_type_object->input( array( 'class' => 'cmb2_unique_id', 'type' => 'hidden' ) );
		}
	
		/**
		 * Sanitize the unique id field.
		 *
		 * @since 1.0.0
		 *
		 * @param string $null 
		 * @param string $new
		 * @param int $object_id The object id.
		 * @return string $new The sanitized field.
		 */
		function cmb2_sanitize_unique_id( $override, $new, $object_id ) {
			// Set unique id if it's not already set
			if( empty( $new ) || !is_string( $new ) ) {
				$value = uniqid( $object_id, false );
			} else {
				$value = $new;
			}
		    return $value;
		}
	}
	new CF_CMB_Fields;
}
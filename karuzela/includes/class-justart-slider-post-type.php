<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class JustartSliderPostType {

	private static $_instance = null;

	public $parent = null;
	
	public $post_type;

	public $field;
	
    public $plural;
    
	public $single;

	public $description;

	public $options;
	
	public $repeatable_fieldset_settings;

	public $settings;
	
	public function __construct( $parent, $post_type = '', $plural = '', $single = '', $description = '', $options = array() ) {
		$this->parent = $parent;
		
		if ( ! $post_type || ! $plural || ! $single ) {
			return;
		}
		
		$this->settings = array(
			'repeatable' => false,
			'fields' => array(

				'justart_slider_id' => array(
					'type'			=> 'id',
					'class'			=> 'full-width',
					'description'	=> 'The ID of slider\'s element',
					'prefix'		=> '#'
				),
				
				'justart_slider_pause_on_hover' => array(
					'type'			=> 'checkbox',
					'default'		=> true,
					'class'			=> '',
					'description'	=> 'If true stops autoplay on mouseover',
                ),
                
				'justart_slider_interval' => array(
					'label'			=> 'Interval time',
					'type'			=> 'milliseconds',
					'placeholder'	=> '',
					'suffix'		=> 'ms',
					'class'			=> 'full-width',
					'default'		=> 2500,
					'description' => 'The interval time between slides'
                ),

                'justart_slider_duration' => array(
                    'label'			=> 'Duration time',
					'type'			=> 'milliseconds',
					'placeholder'	=> '',
					'suffix'		=> 'ms',
					'class'			=> 'full-width',
					'default'		=> 400,
					'description' => 'Duration of the animation between slides in ms.'
                )
			)
		);
		
		$this->repeatable_fieldset_settings = array(
			'repeatable' => true,
			'fields' => array(
				'justart_slider_slide_image' => array(
					'type'			=> 'media_upload',
					'class'			=> '',
					'description'	=> '',
				),
				'justart_slider_slide_name' => array(
					'type'			=> 'text',
					'placeholder'	=> 'Name',
					'class'			=> '',
					'description'	=> ''
				),
				'justart_slider_slide_title' => array(
					'type'			=> 'text',
					'placeholder'	=> 'Title',
					'class'			=> '', //'full-width text',
					'description'	=> ''
				)
			)
		);

		// Post type name and labels.
		$this->post_type   = $post_type;
		$this->plural      = $plural;
		$this->single      = $single;
		$this->description = $description;
		$this->options     = $options;

		// Regsiter post type.
		add_action( 'init', array( $this, 'register_post_type' ) );

		// Add custom meta boxes
		add_action( 'admin_init', array( $this, 'add_meta_boxes' ) );

		add_action( 'save_post_justart-slider', array( $this, 'save_slides_meta' ) );
		add_action( 'save_post_justart-slider', array( $this, 'save_global_settings_meta' ) );

		// Register shortcodes
		add_shortcode( 'justart-slider', array( $this, 'justart_slider_shortcode' ) );

		// Display custom update messages for posts edits.
		add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_updated_messages' ), 10, 2 );
	}

	public function register_post_type() {
		$labels = array(
			'name'               => $this->plural,
			'singular_name'      => $this->single,
			'name_admin_bar'     => $this->single,
			'add_new'            => 'Add new',
			'add_new_item'       => sprintf( 'Add New %s', $this->single ),
			'edit_item'          => sprintf( 'Edit %s', $this->single ),
			'new_item'           => sprintf( 'New %s', $this->single ),
			'all_items'          => sprintf( 'All %s', $this->plural ),
			'view_item'          => sprintf( 'View %s', $this->single ),
			'search_items'       => sprintf( 'Search %s', $this->plural ),
			'not_found'          => sprintf( 'No %s Found', $this->plural ),
			'not_found_in_trash' => sprintf( 'No %s Found In Trash', $this->plural ),
			'parent_item_colon'  => sprintf( 'Parent %s', $this->single ),
			'menu_name'          => $this->plural,
		);

		$args = array(
			'labels'                => apply_filters( $this->post_type . '_labels', $labels ),
			'description'           => $this->description,
			'public'                => true,
			'publicly_queryable'    => true,
			'exclude_from_search'   => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => true,
			'query_var'             => true,
			'can_export'            => true,
			'rewrite'               => true,
			'capability_type'       => 'post',
			'has_archive'           => false,
			'hierarchical'          => false,
			'show_in_rest'          => true,
			'rest_base'             => $this->post_type,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'supports'              => array( 'title' ),
			'menu_position'         => 80,
			'menu_icon'             => 'dashicons-admin-post',
		);

		$args = array_merge( $args, $this->options );

		register_post_type( $this->post_type, apply_filters( $this->post_type . '_register_args', $args, $this->post_type ) );
	}

	/*
	* Setup custom meta boxes
	*/
	public function add_meta_boxes() {
		// Create the Slide Meta Boxes
		add_meta_box( 'justart-slider-slide-settings-group', 'Slides', array( $this, 'create_slide_settings_meta_box' ), 'justart-slider', 'normal', 'default' );
		add_filter( 'postbox_classes_justart-slider-slide-settings-group', array( $this, 'add_metabox_classes' ) );
		
		// Create the Shortcode Meta Box
		add_meta_box( 'justart-slider-shortcode-group', 'Shortcode', array( $this, 'create_shortcode_meta_box' ), 'justart-slider', 'side', 'high' );
		
		// Create the Global Settings Meta Box
		add_meta_box( 'justart-slider-global-settings-group', 'Global settings', array( $this, 'create_global_settings_meta_box' ), 'justart-slider', 'side', 'default' );
	}

	/*
	* Create repeatable slide fieldset
	*/
	public function create_slide_settings_meta_box() {
		global $post;
		
		$slide_settings = get_post_meta( $post->ID, 'justart-slider-slide-settings-group', true );

		wp_nonce_field( 'otb_repeater_nonce', 'otb_repeater_nonce' );
		?>
		
		<div class="otb-postbox-container">

			<table class="otb-panel-container multi sortable repeatable" width="100%" cellpadding="0" cellspacing="0" border="0">
				<tbody class="container">
					<?php
					$hidden_panel = false;
					
					if ( $slide_settings ) :
						foreach ( $slide_settings as $setting ) {
							$this->field = $setting;
							include( $this->parent->assets_dir .'/template-parts/repeatable-panel-slide.php' );
						}
					else : 
						// show a blank one
						include( $this->parent->assets_dir .'/template-parts/repeatable-panel-slide.php' );
					endif;
					
					$this->field = null;
					
					// Empty hidden panel used for creating a new panel
					$hidden_panel = true;
					include( $this->parent->assets_dir .'/template-parts/repeatable-panel-slide.php' );
					?>
				</tbody>
			</table>

			<div class="footer">
				
				<div class="right">
					<a class="button add-repeatable-panel" href="#">Add another slide</a>
				</div>
			</div>
			
		</div>
		
	<?php
	}
	
	public function add_metabox_classes( $classes ) {
		array_push( $classes, 'otb-postbox', 'seamless' );
		return $classes;
	}
	
	/*
	* Create global settings meta box
	*/
	public function create_global_settings_meta_box() {
		global $post;
		include( $this->parent->assets_dir .'template-parts/global-settings.php' );
	}
	
	/*
	* Create Shortcode meta box
	*/
	public function create_shortcode_meta_box() {
		global $post;
	?>
		<div class="text-input-with-button-container copyable">
			<input name="justart_slider_shortcode" value="<?php esc_html_e( '[justart-slider id="' . $post->ID . '"]' ); ?>" readonly />
			<button type="button" class="button button-secondary icon copy">Copy</button>
		</div>
	<?php
	}
	
	/*
	* Save slides meta
	*/
	public function save_slides_meta( $post_id ) {
		if ( !isset( $_POST['otb_repeater_nonce'] ) || !wp_verify_nonce( $_POST['otb_repeater_nonce'], 'otb_repeater_nonce' ) )
			return;
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( !current_user_can( 'edit_post', $post_id ) )
			return;

		$sss_old = get_post_meta( $post_id, 'justart-slider-slide-settings-group', true );
		$sss_new = array();
		
		$repeatable_fieldset_settings = $this->repeatable_fieldset_settings['fields'];
		//$repeatable_fieldset_settings_array = array();
		
        foreach ( $repeatable_fieldset_settings as $name => $config ) {
			$values_array = wp_unslash( $_POST[ $name ] );
			
			for ( $i=0; $i<count( $values_array ); $i++ ) {
				$sss_new[$i][ $name ] = $this->sanitize_field( $values_array[$i], $config['type'] );
			}
        }
        
		if ( !empty( $sss_new ) && $sss_new != $sss_old ) {
			update_post_meta( $post_id, 'justart-slider-slide-settings-group', $sss_new );
		} elseif ( empty( $sss_new ) && $sss_old ) {
			delete_post_meta( $post_id, 'justart-slider-slide-settings-group', $sss_old );
		}
	}
	
	/*
	* Save global settings meta
	*/
	public function save_global_settings_meta( $post_id ) {
		if ( !isset( $_POST['otb_repeater_nonce'] ) || !wp_verify_nonce( $_POST['otb_repeater_nonce'], 'otb_repeater_nonce' ) )
			return;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( !current_user_can( 'edit_post', $post_id ) )
			return;
		
		$settings = $this->settings['fields'];
		
        foreach ( $settings as $name => $config ) {
			$post = '';
			
			if ( isset( $_POST[ $name ] ) ) {
				$post = $_POST[ $name ];
			}

			$value = $this->sanitize_field( wp_unslash( $post ), $config['type'] );
			update_post_meta( $post_id, $name, $value);
        }
	}
	
	/* Utility function for creating form controls */
	public function create_justart_form_control( $id, $settings ) {
		global $post;
		
		$value = '';
		$formControl = null;
		
		$repeatable 	   = $this->getIfSet( $settings['repeatable'], false);
		$parent_field_type = $this->getIfSet($settings['type'], '');
		$field_counter 	   = $this->getIfSet($settings['field_counter'], '');
		$settings 		   = $settings['fields'][$id];
		$field_type 	   = $settings['type'];
		
		if ( ( $repeatable || $parent_field_type == 'repeatable_fieldset' ) && isset( $this->field[$id] ) ) {
			$value = $this->field[$id];
		} else if ( !$repeatable ) {
			$value = get_post_meta( $post->ID, $id, true );
		}

		if ( !is_numeric( $value ) && empty( $value ) && isset( $settings['default'] ) ) {		
			$value = $settings['default'];
		}
		
		$formControl = new JustArtSliderControl( $id, $this, $repeatable, $settings, $value, $field_counter );
		
		return $formControl;
	}
	
	public function sanitize_field( $value, $type ) {
		switch( $type ) {
			case 'id':
			case 'text':
			case 'email':
			case 'password':
				$value = sanitize_text_field( $value );
			break;

			case 'number':
            case 'milliseconds':
				$value = intval( $value );
			break;

			case 'float':
			case 'percentage':
			case 'range':
				$value = floatVal( $value );
			break;

			case 'color':
				$value = sanitize_hex_color( $value );
			break;
			
			case 'url':
				$value = esc_url( $value );
			break;
			
			case 'textarea':
				$value = sanitize_textarea_field( $value );
			break;

			case 'html':
				$value = wp_kses( $value, array(
					'a' => array(
						'href' => array(),
						'title' => array(),
						'target' => array()
					),
					'img' => array(
						'src' => array(),
						'height' => array(),
						'width' => array()
					),
					'ol' => array(),
					'ul' => array(),
					'li' => array(),
					'br' => array(),
					'em' => array(),
					'strong' => array(),
				) );
			break;
			
			case 'tinymce':
				$value = $value;
			break;

			case 'checkbox':
 				$value = intval( (bool) $value );
			break;
			
			case 'media_upload':
				$value = intval( $value );
			break;
		}
		
		return $value;
	}
	
	function getIfSet( &$var, $defaultValue ) {
		if(isset($var)) {
			return $var;
		} else {
			return $defaultValue;
		}
	}

	public function get_default_value( $id, $settings_array ) {
		return $this->$settings_array['fields'][$id]['default'];
	}
	

	function justart_slider_shortcode( $atts ) {
		extract( shortcode_atts( array(
			'id' => ''
		), $atts ) );
		
		ob_start();
		include( $this->parent->assets_dir .'/template-parts/slider.php' );
		return ob_get_clean();		
	}

	function create_slider( $id ) {
		ob_start();
		include( $this->parent->assets_dir .'/template-parts/slider.php' );
		return ob_get_clean();
	}
	
	public function updated_messages( $messages = array() ) {
		global $post, $post_ID;
		$messages[ $this->post_type ] = array(
			0  => '',
			1  => sprintf( '%1$s updated. %2$sView %3$s%4$s.', $this->single, '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', $this->single, '</a>' ),
			2  => 'Custom field updated.',
			3  => 'Custom field deleted.',
			4  => sprintf( '%1$s updated.', $this->single ),
			5  => isset( $_GET['revision'] ) ? sprintf( '%1$s restored to revision from %2$s.', $this->single, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => sprintf( '%1$s published. %2$sView %3$s%4s.', $this->single, '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', $this->single, '</a>' ),
			7  => sprintf( '%1$s saved.', $this->single ),
			8  => sprintf( '%1$s submitted. %2$sPreview post%3$s%4$s.', $this->single, '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', $this->single, '</a>' ),
			9  => sprintf( '%1$s scheduled for: %2$s. %3$sPreview %4$s%5$s.', $this->single, '<strong>' . date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ) ) . '</strong>', '<a target="_blank" href="' . esc_url( get_permalink( $post_ID ) ) . '">', $this->single, '</a>' ),
			10 => sprintf( '%1$s draft updated. %2$sPreview %3$s%4$s.', $this->single, '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', $this->single, '</a>' ),
		);

		return $messages;
	}

	public function bulk_updated_messages( $bulk_messages = array(), $bulk_counts = array() ) {

		//phpcs:disable
		$bulk_messages[ $this->post_type ] = array(
			'updated'   => sprintf( _n( '%1$s %2$s updated.', '%1$s %3$s updated.', $bulk_counts['updated'], 'super-simple-slider' ), $bulk_counts['updated'], $this->single, $this->plural ),
			'locked'    => sprintf( _n( '%1$s %2$s not updated, somebody is editing it.', '%1$s %3$s not updated, somebody is editing them.', $bulk_counts['locked'], 'super-simple-slider' ), $bulk_counts['locked'], $this->single, $this->plural ),
			'deleted'   => sprintf( _n( '%1$s %2$s permanently deleted.', '%1$s %3$s permanently deleted.', $bulk_counts['deleted'], 'super-simple-slider' ), $bulk_counts['deleted'], $this->single, $this->plural ),
			'trashed'   => sprintf( _n( '%1$s %2$s moved to the Trash.', '%1$s %3$s moved to the Trash.', $bulk_counts['trashed'], 'super-simple-slider' ), $bulk_counts['trashed'], $this->single, $this->plural ),
			'untrashed' => sprintf( _n( '%1$s %2$s restored from the Trash.', '%1$s %3$s restored from the Trash.', $bulk_counts['untrashed'], 'super-simple-slider' ), $bulk_counts['untrashed'], $this->single, $this->plural ),
		);

		return $bulk_messages;
	}
	
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	}

}

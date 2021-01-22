<?php
/**
 * Main plugin class file.
 *
 * @package Super Simple Slider/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
class JustartSlider {

	private static $_instance = null;

	public $admin = null;

	public $settings = null;
	
	public $post_type = null;

	public $_version;

	public $_token;

	public $_text_domain;

	public $file;

	public $dir;

	public $assets_dir;

	public $assets_url;

	public $script_suffix;

	public function __construct( $file = '', $version = JUST_ART_SLIDER_PLUGIN_VERSION ) {
		$this->_version = $version;
		$this->_text_domain	= 'justart-slider';
		$this->_token   = 'justart_slider';

		// Load plugin environment variables.
		$this->file       = $file;
		$this->dir        = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir );
		$this->assets_url = esc_url( trailingslashit( plugins_url( '', $this->file ) ) );
		
		$this->script_suffix = '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Load frontend JS & CSS.
		// add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_styles' ), 10 );
		// add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ), 10 );

		// Load admin JS & CSS.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ), 10, 1 );
		
		// Load the widget
		add_action( 'widgets_init', array( $this, 'load_custom_widgets' ) );
	}
	
	public function register_post_type( $post_type = '', $plural = '', $single = '', $description = '', $options = array() ) {

		if ( ! $post_type || ! $plural || ! $single ) {
			return false;
		}

		$post_type = new JustartSliderPostType( justart_slider(), $post_type, $plural, $single, $description, $options );

		$this->post_type = $post_type;
		
		return $post_type;
	}

	public function enqueue_frontend_styles() {
		// Font Awesome
		wp_register_style( $this->_text_domain . '-font-awesome', esc_url( $this->assets_url ) . 'fonts/sss-font-awesome/css/sss-font-awesome.css', array(), '4.7.0' );
		wp_enqueue_style( $this->_text_domain . '-font-awesome' );
		
		// The frontend stylesheet
		wp_register_style( $this->_text_domain . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_text_domain . '-frontend' );
	}

	public function enqueue_frontend_scripts() {
		// The slider plugin's default script file
		wp_register_script( 'carouFredSel-js', esc_url( $this->assets_url ) . 'sliders/carouFredSel/jquery.carouFredSel-6.2.1' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version, true );
		wp_enqueue_script( 'carouFredSel-js' );

		// TouchSwipe
		wp_register_script( $this->_text_domain . '-touchswipe-js', esc_url( $this->assets_url ) . 'js/jquery.touchSwipe' . $this->script_suffix . '.js', array('jquery'), $this->_version, true );
		wp_enqueue_script( $this->_text_domain . '-touchswipe-js' );
		
		// The custom script file for the slider plugin
		wp_register_script( $this->_text_domain . '-carouFredSel-custom-js', esc_url( $this->assets_url ) . 'js/carouFredSel-custom' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version, true );
		wp_enqueue_script( $this->_text_domain . '-carouFredSel-custom-js' );

		// FitText
		wp_register_script( $this->_text_domain . '-fittext-js', esc_url( $this->assets_url ) . 'js/jquery.fittext' . $this->script_suffix . '.js', array('jquery'), $this->_version, true );
		wp_enqueue_script( $this->_text_domain . '-fittext-js' );

		// FitButton
		wp_register_script( $this->_text_domain . '-fitbutton-js', esc_url( $this->assets_url ) . 'js/jquery.fitbutton' . $this->script_suffix . '.js', array('jquery'), $this->_version, true );
		wp_enqueue_script( $this->_text_domain . '-fitbutton-js' );
		
		// The frontend script file
		wp_register_script( $this->_text_domain . '-frontend-js', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery', $this->_text_domain . '-fittext-js', $this->_text_domain . '-fitbutton-js' ), $this->_version, true );
		wp_enqueue_script( $this->_text_domain . '-frontend-js' );
	}

	public function enqueue_admin_styles( $hook = '' ) {
		global $post;
		
		if ( ( $hook != 'post-new.php' && $hook != 'post.php' && $hook != 'super-simple-slider_page_themes' ) || ( $post && $post->post_type !== 'justart-slider' ) ) {
			return;
	    }

		// The admin stylesheet
		echo esc_url( $this->assets_url ) . 'css/admin.css';
		wp_register_style( $this->_text_domain . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_text_domain . '-admin' );
	}

	public function enqueue_admin_scripts( $hook = '' ) {
		global $post;

		if ( ( $hook != 'post-new.php' && $hook != 'post.php' ) || ( $post && $post->post_type !== 'justart-slider' ) ) {
			return;
	    }
		
		wp_enqueue_media();
		
		// The admin script file
		wp_register_script( $this->_text_domain . '-admin-js', esc_url( $this->assets_url ) . 'js/admin.js', array( 'jquery' ), $this->_version, true );
		wp_enqueue_script( $this->_text_domain . '-admin-js' );
	}

	public function load_custom_widgets() {
		register_widget( 'JustartSliderWidget' );
	}
	

	public function hex_to_rgb( $hex ) {
		// Remove "#" if it was added
		$color = trim( $hex, '#' );
	
		// Return empty array if invalid value was sent
		if ( ! ( 3 === strlen( $color ) ) && ! ( 6 === strlen( $color ) ) ) {
			return array();
		}
	
		// If the color is three characters, convert it to six.
		if ( 3 === strlen( $color ) ) {
			$color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
		}
	
		// Get the red, green, and blue values
		$red   = hexdec( $color[0] . $color[1] );
		$green = hexdec( $color[2] . $color[3] );
		$blue  = hexdec( $color[4] . $color[5] );
	
		// Return the RGB colors as an array
		return array( 'r' => $red, 'g' => $green, 'b' => $blue );
	}
	
	public static function instance( $file = '', $version = JUST_ART_SLIDER_PLUGIN_VERSION ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	}

	public static function is_plugin_active( $plugin_name ) {

		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, array_keys( get_site_option( 'active_sitewide_plugins', array() ) ) );
		}

		$plugin_filenames = array();

		foreach ( $active_plugins as $plugin ) {

			if ( false !== strpos( $plugin, '/' ) ) {

				// normal plugin name (plugin-dir/plugin-filename.php)
				list( , $filename ) = explode( '/', $plugin );

			} else {

				// no directory, just plugin file
				$filename = $plugin;
			}

			$plugin_filenames[] = $filename;
		}

		return in_array( $plugin_name, $plugin_filenames );
	}


	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cloning of Super_Simple_Slider is forbidden' ) ), esc_attr( $this->_version ) );
	}

	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Unserializing instances of Super_Simple_Slider is forbidden' ) ), esc_attr( $this->_version ) );
	}

	public function install() {
		$this->_log_version_number();
	}

	private function _log_version_number() {
		update_option( str_replace('-', '_', $this->_text_domain ) . '_version', $this->_version );
	}

}

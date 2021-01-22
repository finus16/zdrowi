<?php
/*
 * Plugin Name: Justart Slider
 * Version: 1.0.0
 * Plugin URI: https://just-art.pl
 * Description: Our custom responsive slider
 * Author: Just-Art
 * Requires PHP: 7.0
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'JUST_ART_SLIDER_PLUGIN_VERSION', '1.0.0' );

// Load plugin class files.
require_once 'includes/class-justart-slider.php';
require_once 'includes/class-justart-slider-post-type.php';
require_once 'includes/class-justart-slider-widget.php';
require_once 'includes/class-justart-slider-form-control.php';

function justart_slider() {
	$instance = JustartSlider::instance( __FILE__, JUST_ART_SLIDER_PLUGIN_VERSION );
	
	return $instance;
}

justart_slider();

$post_type = 'justart-slider';
$plural    = 'Justart Sliders';
$single    = 'Justart Slider';

// Create the Custom Post Type and a Taxonomy for the 'super-simple-slider' Post Type
justart_slider()->register_post_type( $post_type, $plural, $single, '', array(
	'labels'	=> array(
		'name'               => $plural,
		'singular_name'      => $single,
		'name_admin_bar'     => $single,
		'add_new'            => 'Create New Slider',
		'add_new_item'       => 'Create New Slider',
		'edit_item'          => 'Edit Slider',
		'new_item'           => 'New Justart Slider',
		'all_items' 		 => 'View Sliders',
		'view_item'          => 'View Slider',
		'search_items'       => 'Search Sliders',
		'not_found'          => 'No Sliders',
		'not_found_in_trash' => 'No Sliders Found In Trash',
		'parent_item_colon'  => 'Parent Justart Slider',
		'menu_name' => 'Justart Slider',
	),
	'public'    => true,
	'publicly_queryable' => true,
	'exclude_from_search' => true, // Check if this is legit
	'menu_icon' => 'dashicons-images-alt2'
) );

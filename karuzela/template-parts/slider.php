
<?php
if ( get_post_status( $id ) != 'publish' || get_post_type( $id ) != 'justart-slider' ) {
	return;
}

$slides = get_post_meta( $id, 'super-simple-slider-slide-settings-group', true );

$settings = $this->settings['fields'];
$slider_settings = array();

foreach ( $settings as $name => $config ) {
	$slider_settings[$name] = $this->sanitize_field( get_post_meta( $id, $name, true ), $config['type'] );
}

	if ( $slides ) :
		$sliderID = $slider_settings['justart_slider_id'];
		$template = $slider_settings['justart_slider_template'];
		?>
		<div id="<?php echo $sliderID; ?>" class="glide">
			<div class="glide__track" data-glide-el="track">
				<ul class="glide__slides">
					<?php
						foreach ($slides as $slide) {
							$image_id 	 = $slide['super_simple_slider_slide_image'];
							$title = trim( $slide['justart_slider_slide_title'] );
							$description  = trim( $slide['justart_slider_slide_description'] );

							$slide_image = wp_get_attachment_image_src( $image_id, 'full' );

							include( $this->parent->assets_dir .'/templates/' . $template . '.php' );
						}
					?>
				</ul>
			</div>

			<div class="glide__arrows" data-glide-el="controls">
				<button class="glide__arrow glide__arrow--left" data-glide-dir="<"><</button>
				<button class="glide__arrow glide__arrow--right" data-glide-dir=">">></button>
			</div>
		</div>

		<script type="text/javascript">
			jQuery(window).on('load', function() {
				new SuperSimpleSlider( '#super-simple-slider-<?php echo $slider_id; ?>', {
					speed: <?php echo $slider_settings['super_simple_slider_speed']; ?>,
				})
			});
		</script>

	<?php endif; ?>
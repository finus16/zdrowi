
<?php
if ( get_post_status( $id ) != 'publish' || get_post_type( $id ) != 'justart-slider' ) {
	return;
}

$slides = get_post_meta( $id, 'justart-slider-slide-settings-group', true );

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
							$image_id 	 = $slide['justart_slider_slide_image'];
							$title = trim( $slide['justart_slider_slide_title'] );
							$description  = trim( $slide['justart_slider_slide_description'] );

							$slide_image = wp_get_attachment_image_src( $image_id, 'full' );

							include( $this->parent->assets_dir .'templates/slides/' . $template . '.php' );
						}
					?>
				</ul>
			</div>

			<div class="glide__arrows" data-glide-el="controls">
				<button class="glide__arrow glide__arrow--left" data-glide-dir="<">
					<i class="fa fa-chevron-left"></i>
				</button>
				<button class="glide__arrow glide__arrow--right" data-glide-dir=">">
					<i class="fa fa-chevron-right"></i>
				</button>
			</div>
		</div>

	<?php endif; ?>
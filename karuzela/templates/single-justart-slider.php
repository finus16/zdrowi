<?php
/**
 * The template for displaying all single Justart Sliders.
 *
 * @package Justart Slider
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php
			if ( $queried_object ) {
				echo do_shortcode('[justart-slider id="' .$queried_object->ID. '"]');
			}
			?>		

		<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>

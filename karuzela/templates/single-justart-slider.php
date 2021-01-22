<?php

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php
			if ( $queried_object ) {
				echo do_shortcode('[justart-slider id="' .$queried_object->ID. '"]');
			}
			?>		

		<?php endwhile; ?>

		</main>
	</div>

<?php get_footer(); ?>

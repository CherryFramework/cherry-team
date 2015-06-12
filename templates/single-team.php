<?php
/**
 * The Template for displaying single CPT Team.
 *
 */

while ( have_posts() ) :

		the_post(); ?>

		<article <?php if ( function_exists( 'cherry_attr' ) ) cherry_attr( 'post' ); ?>>
		<?php

			do_action( 'cherry_post_before' );

			$args = array(
				'id'           => get_the_ID(),
				'template'     => 'single-team.tmpl',
				'custom_class' => 'team-page-single',
				'size'         => 'thumbnail',
				'container'    => false,
				'item_class'   => 'team-single-item'
			);
			$data = new Cherry_Team_Data;
			$data->the_team( $args );
			$data->microdata_markup();

		?>
		</article>

		<?php do_action( 'cherry_post_after' ); ?>

<?php endwhile; ?>
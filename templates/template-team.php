<?php
/**
 * Template Name: Team Page
 *
 * The template for displaying CPT Team.
 *
 * @package   Cherry_Team
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Cherry Team
 */

if ( have_posts() ) :

	while ( have_posts() ) :

			the_post(); ?>

			<article <?php
				if ( function_exists( 'cherry_attr' ) ) {
					cherry_attr( 'post' );
				}
			?> >

				<?php

					// Display a page content.
					the_content();

					$args = array(
						'template'     => 'default.tmpl',
						'limit'        => 3,
						'custom_class' => 'team-listing row',
						'item_class'   => 'team-listing_item',
						'col_xs'       => '12',
						'col_sm'       => '6',
						'col_md'       => '4',
						'col_lg'       => false,
						'container'    => false,
						'size'         => 'thumbnail',
						'pager'        => true,
						'limit'        => Cherry_Team_Templater::get_posts_per_archive_page(),
					);

					$data = new Cherry_Team_Data;
					$data->the_team( $args );
				?>

			</article>

	<?php endwhile;

endif;

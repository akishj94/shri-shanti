<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Shri_Shanti
 */

?>

<section class="no-results not-found">
	<div class="container_medium">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'shri-shanti' ); ?></h1>
	</header><!-- .page-header -->

	<div class="page-content">
		<?php
		if ( is_home() && current_user_can( 'publish_posts' ) ) :

			printf(
				'<p>' . wp_kses(
					/* translators: 1: link to WP admin new post page. */
					__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'shri-shanti' ),
					array(
						'a' => array(
							'href' => array(),
						),
					)
				) . '</p>',
				esc_url( admin_url( 'post-new.php' ) )
			);

		elseif ( is_search() ) :
			?>

			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'shri-shanti' ); ?></p>
			<?php
			get_search_form();

		else :
			?>			
			<p><?php esc_html_e( 'It looks like no blog posts have been published yet. Stay tuned—new stories, tips, and updates will appear here soon.', 'shri-shanti' ); ?></p>
			
			<?php
			// get_search_form();

		endif;
		?>
	</div><!-- .page-content -->
	</div>
</section><!-- .no-results -->

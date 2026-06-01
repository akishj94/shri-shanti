<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Shri_Shanti
 */

// Determine author display name (no link):
// Administrators → "Admin", any other role → "Editorial Team"
$author_id      = (int) get_the_author_meta( 'ID' );
$author_user    = get_userdata( $author_id );
$author_display = ( $author_user && $author_user->has_cap( 'administrator' ) ) ? 'Admin' : 'Editorial Team';

// Badge label: first non-"uncategorized" category name, fallback to "Article"

$categories   = get_the_category();
$cat_names    = array();

foreach ( $categories as $cat ) {
    $cat_names[] = ( 'uncategorized' === strtolower( $cat->slug ) )
        ? 'Article'
        : $cat->name;
}

if ( empty( $cat_names ) ) {
    $cat_names[] = 'Article';
}

// Reading time estimate (words ÷ 200 wpm, minimum 1)
$content_text = get_the_content();
$word_count   = str_word_count( wp_strip_all_tags( $content_text ) );
$reading_time = max( 1, (int) round( $word_count / 200 ) );
?>


<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( is_singular() ) : ?>
		<?php /* =====================================================
		       SINGLE POST LAYOUT
		       ===================================================== */ ?>
		

		<header class="entry-header entry-header--single">

			<div class="container_medium">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

				<div class="entry-meta entry-meta--single">
					<span class="author-icon" aria-hidden="true">
						<svg width="36" height="36" viewBox="0 0 583 583" fill="none" xmlns="http://www.w3.org/2000/svg">
							<circle cx="291" cy="291" r="291" fill="#F14E23"/>
							<path d="M118.915 421.992L72 421.621V290.909L202.782 290.728C210.955 290.72 217.097 283.626 220.011 277.134L252.182 200.157C262.708 175.852 285.46 160.206 312.562 160L509.586 160.033L509.462 291.066H229.315C220.11 291.222 212.854 295.853 209.486 303.919L177.355 380.78C167.391 404.624 145.721 420.451 118.915 422V421.992Z" fill="white"/>
						</svg>
					</span>
					<div class="author-meta-text">
						<span class="author-name"><?php echo esc_html( $author_display ); ?></span>
						<span class="meta-details">
							<?php esc_html_e( 'Published in', 'shri-shanti' ); ?>
							<span class="post-type-badge">
								<?php 
									foreach($cat_names as $cat){
										echo '<span>' . esc_html( $cat ) . '</span>';
									}
								?>
							</span>
							&middot;
							<?php
							/* translators: %d: estimated reading time in minutes */
							echo esc_html( sprintf( _n( '%d min read', '%d min read', $reading_time, 'shri-shanti' ), $reading_time ) );
							?>
							&middot;
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
								<?php echo esc_html( get_the_date( 'F j, Y' ) ); ?>
							</time>
						</span>
					</div>
				</div><!-- .entry-meta--single -->
			</div>
		</header><!-- .entry-header--single -->
		<div class="container">
			<div class="bordered_separator single-post--separator"></div>
		</div>
		
		<div class="container_medium">
			<?php shri_shanti_post_thumbnail(); ?>
		

			<div class="entry-content">
					<?php
						the_content(
							sprintf(
								wp_kses(
									/* translators: %s: Name of current post. Only visible to screen readers */
									__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'shri-shanti' ),
									array( 'span' => array( 'class' => array() ) )
								),
								wp_kses_post( get_the_title() )
							)
						);
						
					?>
			</div><!-- .entry-content -->
		</div>

	<?php else : ?>
		<?php /* =====================================================
		       ARCHIVE / LISTING LAYOUT
		       ===================================================== */ ?>

		<a class="entry-card-link" href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" aria-label="<?php the_title_attribute(); ?>">

			<figure class="post-thumbnail">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php
					the_post_thumbnail(
						'large',
						array(
							'alt' => the_title_attribute(
								array(
									'echo' => false,
								)
							),
						)
					);
					?>
				<?php else : ?>
					<img
						src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/post_placeholder.webp' ); ?>"
						alt="<?php the_title_attribute(); ?>"
						class="wp-post-image"
					/>
				<?php endif; ?>
			</figure>

			<header class="entry-header entry-header--archive">
				<div class="entry-meta entry-meta--archive">
					<span class="post-type-badge">
						<?php 
							foreach($cat_names as $cat){
								echo '<span>' . esc_html( $cat ) . '</span>';
							}
						?>
					</span>
					<time class="posted-on" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
						<?php echo esc_html( get_the_date( 'F j, Y' ) ); ?>
					</time>
				</div><!-- .entry-meta--archive -->

				<h4 class="entry-title"><?php the_title(); ?></h4>

				<div class="author-row">
					<span class="author-icon" aria-hidden="true">
						<svg width="36" height="36" viewBox="0 0 583 583" fill="none" xmlns="http://www.w3.org/2000/svg">
							<circle cx="291" cy="291" r="291" fill="#F14E23"/>
							<path d="M118.915 421.992L72 421.621V290.909L202.782 290.728C210.955 290.72 217.097 283.626 220.011 277.134L252.182 200.157C262.708 175.852 285.46 160.206 312.562 160L509.586 160.033L509.462 291.066H229.315C220.11 291.222 212.854 295.853 209.486 303.919L177.355 380.78C167.391 404.624 145.721 420.451 118.915 422V421.992Z" fill="white"/>
						</svg>

					</span>
					<span class="author-name"><?php echo esc_html( $author_display ); ?></span>
				</div><!-- .author-row -->
			</header><!-- .entry-header--archive -->

		</a><!-- .entry-card-link -->

	<?php endif; ?>

</article><!-- #post-<?php the_ID(); ?> -->
<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Shri_Shanti
 */

get_header();
?>

	<main id="primary" class="site-main">
        <div class="newsroom_header">
            <div class="container">
                <div class="container_medium">
                    <div class="shri_text_sm p0 mask-up">/ Newsroom</div>
                    <h1 class="page-title mask-up">Latest news, announcements and insights.</h1>
                </div>
                <div class="bordered_separator"></div>
            </div>
        </div>
        <div class="container">
            
                <?php
                    if ( have_posts() ) :
                        echo '<div class="grid posts__grid">';
                            /* Start the Loop */
                            while ( have_posts() ) :
                                the_post();

                                /*
                                * Include the Post-Type-specific template for the content.
                                * If you want to override this in a child theme, then include a file
                                * called content-___.php (where ___ is the Post Type name) and that will be used instead.
                                */
                                get_template_part( 'template-parts/content', get_post_type() );

                            endwhile;
                        echo '</div>';

                        $arrow = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 13" width="16" height="13" aria-hidden="true">
                            <path fill="currentColor" d="M9.86 0L15.172 5.312V6.8L9.86 12.112L8.351 10.625L11.836 7.119H0V4.994H11.836L8.33 1.487L9.86 0Z"/>
                        </svg>';
                        the_posts_pagination([
                            'mid_size'  => 1,
                            'end_size'  => 1,
                            'prev_text' => str_replace('<svg', '<svg style="transform:rotate(180deg);"', $arrow),
                            'next_text' => $arrow,
                        ]);

                    else :

                        get_template_part( 'template-parts/content', 'none' );

                    endif;
                    ?>
            
        </div>
	</main><!-- #main -->

<?php
get_footer();

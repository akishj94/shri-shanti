<?php 
if ( get_post_type() === 'post' ) :

    $related_posts = get_posts(
        array(
            'post_type'      => 'post',
            'posts_per_page' => 2,
            'orderby'        => 'rand',
            'post__not_in'   => array( get_the_ID() ),
            'post_status'    => 'publish',
        )
    );

    if ( ! empty( $related_posts ) ) :
?>
<div class="single-post-footer">
    <div class="container">
        <!-- <div class="bordered_separator"></div> -->
    </div>

    <div class="container_lg">
        <h2>Explore related</h2>

        <div class="post-navigation-cards grid">

            <?php
            foreach ( $related_posts as $index => $adjacent_post ) :

                $direction = ( $index === 0 ) ? 'prev' : 'next';

                // Categories.
                $adj_categories = get_the_category( $adjacent_post->ID );
                $adj_cat_names  = array();

                if ( ! empty( $adj_categories ) ) {
                    foreach ( $adj_categories as $cat ) {
                        $adj_cat_names[] = (
                            'uncategorized' === strtolower( $cat->slug )
                        ) ? 'Article' : $cat->name;
                    }
                }

                if ( empty( $adj_cat_names ) ) {
                    $adj_cat_names[] = 'Article';
                }

                // Author label.
                $adj_author_user = get_userdata( (int) $adjacent_post->post_author );

                $adj_author_display = (
                    $adj_author_user &&
                    $adj_author_user->has_cap( 'administrator' )
                ) ? 'Admin' : 'Editorial Team';

                // Thumbnail.
                $adj_thumbnail = get_the_post_thumbnail(
                    $adjacent_post->ID,
                    'large',
                    array(
                        'alt' => esc_attr( $adjacent_post->post_title ),
                    )
                );

                $adj_permalink = get_permalink( $adjacent_post->ID );
            ?>

            <a
                class="entry-card-link entry-card-link--<?php echo esc_attr( $direction ); ?>"
                href="<?php echo esc_url( $adj_permalink ); ?>"
                rel="next"
                aria-label="<?php echo esc_attr( $adjacent_post->post_title ); ?>"
            >

                <figure class="post-thumbnail">
                    <?php if ( $adj_thumbnail ) : ?>
                        <?php echo $adj_thumbnail; ?>
                    <?php else : ?>
                        <img
                            src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/post_placeholder.webp' ); ?>"
                            alt="<?php echo esc_attr( $adjacent_post->post_title ); ?>"
                            class="wp-post-image"
                        />
                    <?php endif; ?>
                </figure>

                <header class="entry-header entry-header--archive">

                    <div class="entry-meta entry-meta--archive">

                        <span class="post-type-badge">
                            <?php foreach ( $adj_cat_names as $cat_name ) : ?>
                                <span><?php echo esc_html( $cat_name ); ?></span>
                            <?php endforeach; ?>
                        </span>

                        <time
                            class="posted-on"
                            datetime="<?php echo esc_attr( get_the_date( 'c', $adjacent_post ) ); ?>"
                        >
                            <?php echo esc_html( get_the_date( 'F j, Y', $adjacent_post ) ); ?>
                        </time>

                    </div>

                    <h4 class="entry-title">
                        <?php echo esc_html( get_the_title( $adjacent_post ) ); ?>
                    </h4>

                    <div class="author-row">

                        <span class="author-icon" aria-hidden="true">
                            <svg width="36" height="36" viewBox="0 0 583 583" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="291" cy="291" r="291" fill="#F14E23"/>
                                <path d="M118.915 421.992L72 421.621V290.909L202.782 290.728C210.955 290.72 217.097 283.626 220.011 277.134L252.182 200.157C262.708 175.852 285.46 160.206 312.562 160L509.586 160.033L509.462 291.066H229.315C220.11 291.222 212.854 295.853 209.486 303.919L177.355 380.78C167.391 404.624 145.721 420.451 118.915 422V421.992Z" fill="white"/>
                            </svg>
                        </span>

                        <span class="author-name">
                            <?php echo esc_html( $adj_author_display ); ?>
                        </span>

                    </div>

                </header>

            </a>

            <?php endforeach; ?>

        </div>
    </div>
</div>
<?php
    endif;

endif;
?>
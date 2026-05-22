<?php
/**
 * Shri Catalog Shortcode
 *
 * Displays products from the 'shri_catalog' custom post type.
 * On mobile: horizontal scroll snap.
 * On tablet/desktop (≥992px): pinned section with horizontal scroll driven by vertical scrolling.
 *
 * Usage:
 *   [shri_catalog]
 *   [shri_catalog title="Our Collection"]
 *   [shri_catalog title="false"]   ← hides the title
 *   [shri_catalog posts_per_page="12"]
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* -----------------------------------------------------------------------
 * 2. SHORTCODE
 * --------------------------------------------------------------------- */
add_shortcode( 'shri_catalog', 'shri_catalog_shortcode' );

function shri_catalog_shortcode( $atts ) {

    $atts = shortcode_atts(
        array(
            'title'          => 'Our Catalog',  // Default section title
            'posts_per_page' => -1,             // -1 = all products
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ),
        $atts,
        'shri_catalog'
    );

    /* ---- Enqueue stylesheet (safe to call multiple times) ---- */
    wp_enqueue_style( 'shri-catalog' );

    /* ---- Query ---- */
    $query = new WP_Query( array(
        'post_type'      => 'shri_catalog',
        'posts_per_page' => intval( $atts['posts_per_page'] ),
        'orderby'        => sanitize_text_field( $atts['orderby'] ),
        'order'          => sanitize_text_field( $atts['order'] ),
        'post_status'    => 'publish',
    ) );

    if ( ! $query->have_posts() ) {
        return '<p class="shri-catalog-empty">No catalog items found.</p>';
    }

    /* ---- Unique instance ID (supports multiple shortcodes per page) ---- */
    static $instance = 0;
    $instance++;
    $uid = 'shri-catalog-' . $instance;

    /* ---- Build HTML ---- */
    ob_start();
    ?>

    <section class="shri-catalog-section" id="<?php echo esc_attr( $uid ); ?>" aria-label="<?php echo esc_attr( $atts['title'] ); ?>">
    
        <div class="container_fluid">

        
            <?php if ( ! empty( $atts['title'] ) && strtolower( $atts['title'] ) !== 'false' ) : ?>
                <div class="shri-catalog-header grid">
                    <div class="bordered_separator"></div>
                    <span class="shri-catalog-label">SOLUTIONS </span>
                    <span class="shri-catalog-label">/ By use case </span>
                    <h2 class="shri-catalog-title"><?php echo wp_kses_post( $atts['title'] ); ?></h2>
                </div>
            <?php endif; ?>
        </div>
            <!-- Sticky wrapper (desktop only) -->
            <div class="shri-catalog-sticky-outer">
                <div class="shri-catalog-sticky-inner">
                    <div class="shri-catalog-track" role="list">

                        <?php
                        while ( $query->have_posts() ) :
                            $query->the_post();
                            $thumb = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                            $title = get_the_title();
                            $link  = get_permalink();
                        ?>

                        <article class="shri-catalog-item" role="listitem">
                            <!-- <a href="<@?php echo esc_url( $link ); ?>" class="shri-catalog-item-inner"> -->
                                <div class="shri-catalog-image-wrap">
                                    <?php if ( $thumb ) : ?>
                                        <img
                                            src="<?php echo esc_url( $thumb ); ?>"
                                            alt="<?php echo esc_attr( $title ); ?>"
                                            loading="lazy"
                                        />
                                    <?php else : ?>
                                        <div class="shri-catalog-placeholder" aria-hidden="true"></div>
                                    <?php endif; ?>
                                </div>
                                <h5 class="shri-catalog-item-name"><?php echo esc_html( $title ); ?></h5>
                            <!-- </a> -->
                        </article>

                        <?php endwhile; ?>
                        <?php wp_reset_postdata(); ?>

                    </div><!-- /.shri-catalog-track -->
                </div><!-- /.shri-catalog-sticky-inner -->
            </div><!-- /.shri-catalog-sticky-outer -->
        
    </section>

    <?php
    return ob_get_clean();
}
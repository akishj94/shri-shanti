<?php
/**
 * Shri Catalog Shortcode
 *
 * Displays products from the 'shri_catalog' custom post type.
 *
 * Layout options:
 *   - grid   → 3-2-3 repeating row pattern (default)
 *   - scroll → pinned section with horizontal scroll driven by vertical scrolling
 *
 * Usage:
 *   [shri_catalog]
 *   [shri_catalog layout="scroll"]
 *   [shri_catalog title="Our Collection"]
 *   [shri_catalog title="false"]
 *   [shri_catalog posts_per_page="12"]
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode( 'shri_catalog', 'shri_catalog_shortcode' );

function shri_catalog_shortcode( $atts ) {

    $atts = shortcode_atts(
        array(
            'title'          => 'Our Catalog',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'layout'         => 'grid',  // 'grid' | 'scroll'
        ),
        $atts,
        'shri_catalog'
    );

    wp_enqueue_style( 'shri-catalog' );

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

    static $instance = 0;
    $instance++;
    $uid    = 'shri-catalog-' . $instance;
    $layout = sanitize_key( $atts['layout'] );

    ob_start();

    /* ------------------------------------------------------------------ */
    /* GRID LAYOUT                                                          */
    /* ------------------------------------------------------------------ */
    if ( $layout === 'grid' ) :

        // Collect all items first
        $items = [];
        while ( $query->have_posts() ) {
            $query->the_post();
            $items[] = [
                'thumb' => get_the_post_thumbnail_url( get_the_ID(), 'large' ),
                'title' => get_the_title(),
                'desc'  => get_the_excerpt(),
            ];
        }
        wp_reset_postdata();

        // 3-2-3 repeating row pattern
        $row_pattern = [ 3, 2, 3 ];
        $total       = count( $items );
        $index       = 0;
        $row_num     = 0;

    ?>

    <section class="solutions_grid" id="<?php echo esc_attr( $uid ); ?>" aria-label="<?php echo esc_attr( $atts['title'] ); ?>">

        <div class="shri-catalog-grid">

            <?php while ( $index < $total ) :
                $cols      = $row_pattern[ $row_num % count( $row_pattern ) ];
                $row_items = array_slice( $items, $index, $cols );
                $count     = count( $row_items );
            ?>

            <div class="shri-catalog-row shri-catalog-row--<?php echo esc_attr( $count ); ?>col">

                <?php foreach ( $row_items as $item ) : ?>
                <div class="shri-catalog-grid__item">
                    <figure>
                        <?php if ( $item['thumb'] ) : ?>
                            <img
                                src="<?php echo esc_url( $item['thumb'] ); ?>"
                                alt="<?php echo esc_attr( $item['title'] ); ?>"
                                loading="lazy"
                            />
                        <?php else : ?>
                            <div class="shri-catalog-placeholder" aria-hidden="true"></div>
                        <?php endif; ?>
                    </figure>
                    <h5><?php echo esc_html( $item['title'] ); ?></h5>
                    <?php if ( $item['desc'] ) : ?>
                        <p><?php echo esc_html( $item['desc'] ); ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>

            </div><!-- /.shri-catalog-row -->

            <?php
                $index += $cols;
                $row_num++;
            endwhile; ?>

        </div><!-- /.shri-catalog-grid -->

    </section>

    <?php
    /* ------------------------------------------------------------------ */
    /* SCROLL LAYOUT (untouched)                                           */
    /* ------------------------------------------------------------------ */
    else :
    ?>

    <section class="shri-catalog-section header--theme-light" id="<?php echo esc_attr( $uid ); ?>" aria-label="<?php echo esc_attr( $atts['title'] ); ?>">

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

        <div class="shri-catalog-sticky-outer">
            <div class="shri-catalog-sticky-inner">
                <div class="shri-catalog-track" role="list">

                    <?php
                    while ( $query->have_posts() ) :
                        $query->the_post();
                        $thumb = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                        $title = get_the_title();
                    ?>

                    <article class="shri-catalog-item" role="listitem">
                        <div class="sc-item-wrap">
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
                        </div>
                    </article>

                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>

                </div><!-- /.shri-catalog-track -->
            </div><!-- /.shri-catalog-sticky-inner -->
        </div><!-- /.shri-catalog-sticky-outer -->

    </section>

    <?php
    endif;

    return ob_get_clean();
}
<?php
/**
 * Shri Marquee Grid Shortcode
 *
 * Usage:
 * [shri_marquee_grid]
 * [shri_marquee_grid groups="4"]
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* -----------------------------------------------------------------------
 * SHORTCODE
 * --------------------------------------------------------------------- */
add_shortcode( 'shri_marquee_grid', 'shri_marquee_grid_shortcode' );

function shri_marquee_grid_shortcode( $atts ) {
   

    /* -------------------------------------------------------------------
     * IMAGE DIRECTORY
     * ----------------------------------------------------------------- */
    $images_dir = get_template_directory() . '/assets/images/industries';

    $images = glob(
        $images_dir . '/*.{jpg,jpeg,png,webp,avif,svg}',
        GLOB_BRACE
    );

    if ( empty( $images ) ) {
        return '<p class="shri-marquee-empty">No images found.</p>';
    }

     $atts = shortcode_atts(
        array(
            'groups' => 2,
        ),
        $atts,
        'shri_marquee_grid'
    );

    $groups = max( 1, intval( $atts['groups'] ) );


    /* -------------------------------------------------------------------
     * UNIQUE ID
     * ----------------------------------------------------------------- */
    static $instance = 0;
    $instance++;

    $uid = 'shri-marquee-grid-' . $instance;

    /* -------------------------------------------------------------------
     * BUILD HTML
     * ----------------------------------------------------------------- */
    ob_start();
    ?>

    <div
        class="shri_marquee_grid"
        id="<?php echo esc_attr( $uid ); ?>"
    >

        <?php for ( $i = 0; $i < $groups; $i++ ) : ?>

            <div class="group">

                    <?php
                        $image_count = count( $images );

                        if ( $image_count < 6 ) {

                            $repeat_count = ceil( 6 / $image_count );

                            $images = array_slice(
                                array_merge( ...array_fill( 0, $repeat_count, $images ) ),
                                0,
                                6
                            );
                        }

                        foreach ( $images as $image ) :

                            $image_url = get_template_directory_uri() . str_replace(
                                get_template_directory(),
                                '',
                                $image
                            );

                            $filename = pathinfo( $image, PATHINFO_FILENAME );
                        ?>

                            <div class="grid__item">

                                <img
                                    src="<?php echo esc_url( $image_url ); ?>"
                                    alt="<?php echo esc_attr( $filename ); ?>"
                                    loading="lazy"
                                    decoding="async"
                                >

                            </div>

                        <?php endforeach; ?>

            </div>

        <?php endfor; ?>

    </div>

    <?php
    return ob_get_clean();
}
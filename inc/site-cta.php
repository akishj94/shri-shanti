<?php
/**
 * Shri CTA Shortcode
 *
 * Displays a call-to-action section with a label, heading, and link button.
 *
 * Usage:
 *   [shri_cta label="Solutions" heading="Ready to get started?" link="https://example.com"]
 *   [shri_cta label="Solutions" heading="Let's build together" link="/contact" link_text="Talk to us" align="center" button="filled"]
 *   [shri_cta label="false" heading="Our next chapter" link="/about" align="center"]
 *
 * Attributes:
 *   label        — small eyebrow label above the heading (set "false" to hide)
 *   heading      — main CTA heading text
 *   link         — button/CTA URL
 *   link_text    — button label (default: "Get Started")
 *   align        — "left" (default) or "center"
 *   button       — "arrow" (default, outlined with →) or "filled" (solid background)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* -----------------------------------------------------------------------
 * Shortcode
 * --------------------------------------------------------------------- */
add_shortcode( 'shri_cta', 'shri_cta_shortcode' );

function shri_cta_shortcode( $atts ) {

    $atts = shortcode_atts(
        array(
            'label'     => '',            // Eyebrow label; set "false" to hide
            'heading'   => '',            // Required — main CTA heading
            'link'      => 'quote-form',           // Button/CTA URL
            'button_text' => 'Get Started', // Button label
            'align'     => 'left',        // "left" or "center"
            'button'    => 'arrow',       // "arrow" or "filled"
        ),
        $atts,
        'shri_cta'
    );

    /* ---- Bail gracefully if no heading ---- */
    if ( empty( $atts['heading'] ) ) {
        return '<!-- [shri_cta] shortcode requires a heading attribute -->';
    }

    /* ---- Sanitise ---- */
    $label     = sanitize_text_field( $atts['label'] );
    $heading   = wp_kses_post( $atts['heading'] );
    $link      = sanitize_text_field( $atts['link'] );
    $button_text = sanitize_text_field( $atts['button_text'] );
    $align     = in_array( $atts['align'], array( 'left', 'center' ), true ) ? $atts['align'] : 'left';
    $button = in_array( $atts['button'], array( 'arrow', 'filled' ), true )
    ? ( 'arrow' === $atts['button'] ? 'site_btn_link' : 'cta_xl' )
    : 'cta_xl';

    /* ---- CSS modifier classes ---- */
    $section_classes = implode( ' ', array_filter( array(
        'shri-cta-section',
        'shri-cta-section--align-' . $align,
        'shri-cta-section--btn-' . $button,
        'container'
    ) ) );

    /* ---- Build HTML ---- */
    ob_start();
    ?>

    <section class="<?php echo esc_attr( $section_classes ); ?>" aria-label="<?php echo esc_attr( wp_strip_all_tags( $heading ) ); ?>">
        

        <div class="shri-cta-wrapper">
            <div class="shri-cta-inner">

                <?php if ( ! empty( $label ) && strtolower( $label ) !== 'false' ) : ?>
                    <p class="shri-cta-label"><?php echo esc_html( $label ); ?></p>
                <?php endif; ?>

                <h2 class="shri-cta-heading"><?php echo $heading; ?></h2>

                <button
                    class="shri-cta-btn <?php echo esc_attr( $button ); ?>"  data-link="#<?php echo esc_html( $link ); ?>"
                >
                    <?php echo esc_html( $button_text ); ?>
                </button>

            </div><!-- /.shri-cta-inner -->
        </div><!-- /.container -->
    </section>

    <?php
    return ob_get_clean();
}
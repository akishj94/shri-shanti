<?php
/**
 * Registers and renders the Contact Options admin page.
 *
 * Sections
 *   1. Contact Numbers  — repeatable phone fields
 *   2. Email Addresses  — repeatable email fields
 *   3. Location         — address textarea, Google Maps URL
 *   4. WhatsApp         — wa.me link
 *
 * All data is stored in a single wp_options row: `contact_options`.
 *
 * @package ContactOptions
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Contact_Options_Page {

    private const OPTION_KEY   = 'contact_options';
    private const MENU_SLUG    = 'contact-options';
    private const NONCE_ACTION = 'contact_options_save';
    private const NONCE_FIELD  = '_contact_options_nonce';
    private const CAPABILITY   = 'manage_options';

    private Contact_Options_Sanitizer $sanitizer;

    public function __construct() {
        $this->sanitizer = new Contact_Options_Sanitizer();
    }

    public function init(): void {
        add_action( 'admin_menu',       [ $this, 'register_page' ] );
        add_action( 'admin_init',       [ $this, 'register_settings' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    // -------------------------------------------------------------------------
    // Admin menu
    // -------------------------------------------------------------------------

    public function register_page(): void {
        add_options_page(
            __( 'Contact Options', 'contact-options' ),
            __( 'Contact Options', 'contact-options' ),
            self::CAPABILITY,
            self::MENU_SLUG,
            [ $this, 'render_page' ]
        );
    }

    // -------------------------------------------------------------------------
    // Settings API registration
    // -------------------------------------------------------------------------

    public function register_settings(): void {
        register_setting(
            self::OPTION_KEY,           // option group
            self::OPTION_KEY,           // option name
            [
                'sanitize_callback' => [ $this, 'sanitize' ],
                'default'           => [],
            ]
        );

        // ── Section: Contact Numbers ─────────────────────────────────────────
        add_settings_section(
            'contact_section_phones',
            __( 'Contact Numbers', 'contact-options' ),
            '__return_false',
            self::MENU_SLUG
        );

        add_settings_field(
            'contact_field_phones',
            __( 'Phone Numbers', 'contact-options' ),
            [ $this, 'render_field_phones' ],
            self::MENU_SLUG,
            'contact_section_phones'
        );

        // ── Section: Email Addresses ─────────────────────────────────────────
        add_settings_section(
            'contact_section_emails',
            __( 'Email Addresses', 'contact-options' ),
            '__return_false',
            self::MENU_SLUG
        );

        add_settings_field(
            'contact_field_emails',
            __( 'Emails', 'contact-options' ),
            [ $this, 'render_field_emails' ],
            self::MENU_SLUG,
            'contact_section_emails'
        );

        // ── Section: Location ────────────────────────────────────────────────
        add_settings_section(
            'contact_section_location',
            __( 'Location', 'contact-options' ),
            '__return_false',
            self::MENU_SLUG
        );

        add_settings_field(
            'contact_field_address',
            __( 'Address', 'contact-options' ),
            [ $this, 'render_field_address' ],
            self::MENU_SLUG,
            'contact_section_location'
        );

        add_settings_field(
            'contact_field_maps_url',
            __( 'Google Directions Link', 'contact-options' ),
            [ $this, 'render_field_maps_url' ],
            self::MENU_SLUG,
            'contact_section_location'
        );

        // ── Section: WhatsApp ────────────────────────────────────────────────
        add_settings_section(
            'contact_section_whatsapp',
            __( 'WhatsApp', 'contact-options' ),
            '__return_false',
            self::MENU_SLUG
        );

        add_settings_field(
            'contact_field_whatsapp',
            __( 'WhatsApp Link', 'contact-options' ),
            [ $this, 'render_field_whatsapp' ],
            self::MENU_SLUG,
            'contact_section_whatsapp'
        );
    }

    // -------------------------------------------------------------------------
    // Sanitization
    // -------------------------------------------------------------------------

    /**
     * Called by the Settings API before saving.
     * All untrusted input passes through the sanitizer — nothing raw is stored.
     *
     * @param  mixed $raw  The raw $_POST data for this option.
     * @return array       Clean, safe data ready for the database.
     */
    public function sanitize( mixed $raw ): array {
        if ( ! is_array( $raw ) ) {
            return [];
        }

        return [
            'phones'    => $this->sanitizer->repeatable( $raw['phones']    ?? [], 'phone' ),
            'emails'    => $this->sanitizer->repeatable( $raw['emails']    ?? [], 'email' ),
            'address'   => $this->sanitizer->textarea( $raw['address']     ?? '' ),
            'maps_url'  => $this->sanitizer->url( $raw['maps_url']         ?? '' ),
            'whatsapp'  => $this->sanitizer->url( $raw['whatsapp']         ?? '', [ 'https', 'http' ] ),
        ];
    }

    // -------------------------------------------------------------------------
    // Assets
    // -------------------------------------------------------------------------

    public function enqueue_assets( string $hook ): void {
        if ( 'settings_page_' . self::MENU_SLUG !== $hook ) {
            return;
        }

        // Inline JS for the repeatable field add/remove logic.
        // No external file needed — keeps the feature self-contained.
        wp_add_inline_script( 'jquery', $this->repeatable_field_js() );
    }

    // -------------------------------------------------------------------------
    // Page render
    // -------------------------------------------------------------------------

    public function render_page(): void {
        if ( ! current_user_can( self::CAPABILITY ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Contact Options', 'contact-options' ); ?></h1>

            <?php settings_errors( self::OPTION_KEY ); ?>

            <form method="post" action="options.php" novalidate>
                <?php
                settings_fields( self::OPTION_KEY );
                do_settings_sections( self::MENU_SLUG );
                submit_button( __( 'Save Changes', 'contact-options' ) );
                ?>
            </form>
        </div>
        <?php
    }

    // -------------------------------------------------------------------------
    // Field renderers
    // -------------------------------------------------------------------------

    public function render_field_phones(): void {
        $options = get_option( self::OPTION_KEY, [] );
        $phones  = ! empty( $options['phones'] ) ? (array) $options['phones'] : [ '' ];
        $this->render_repeatable_field( 'phones', $phones, 'tel', __( 'Add Number', 'contact-options' ) );
    }

    public function render_field_emails(): void {
        $options = get_option( self::OPTION_KEY, [] );
        $emails  = ! empty( $options['emails'] ) ? (array) $options['emails'] : [ '' ];
        $this->render_repeatable_field( 'emails', $emails, 'email', __( 'Add Email', 'contact-options' ) );
    }

    public function render_field_address(): void {
        $options = get_option( self::OPTION_KEY, [] );
        $value   = $options['address'] ?? '';
        ?>
        <textarea
            id="contact_address"
            name="<?php echo esc_attr( self::OPTION_KEY ); ?>[address]"
            rows="4"
            cols="50"
            class="large-text"
        ><?php echo esc_textarea( $value ); ?></textarea>
        <p class="description"><?php esc_html_e( 'Full postal address of the contact.', 'contact-options' ); ?></p>
        <?php
    }

    public function render_field_maps_url(): void {
        $options = get_option( self::OPTION_KEY, [] );
        $value   = $options['maps_url'] ?? '';
        ?>
        <input
            type="url"
            id="contact_maps_url"
            name="<?php echo esc_attr( self::OPTION_KEY ); ?>[maps_url]"
            value="<?php echo esc_attr( $value ); ?>"
            class="regular-text"
            placeholder="https://maps.google.com/?q=..."
        >
        <p class="description"><?php esc_html_e( 'Paste the Google Maps directions URL here.', 'contact-options' ); ?></p>
        <?php
    }

    public function render_field_whatsapp(): void {
        $options = get_option( self::OPTION_KEY, [] );
        $value   = $options['whatsapp'] ?? '';
        ?>
        <input
            type="url"
            id="contact_whatsapp"
            name="<?php echo esc_attr( self::OPTION_KEY ); ?>[whatsapp]"
            value="<?php echo esc_attr( $value ); ?>"
            class="regular-text"
            placeholder="https://wa.me/911234567890"
        >
        <p class="description">
            <?php esc_html_e( 'Format: ', 'contact-options' ); ?>
            <code>https://wa.me/[country-code][number]</code>
            <?php esc_html_e( ' — no spaces or dashes.', 'contact-options' ); ?>
        </p>
        <?php
    }

    // -------------------------------------------------------------------------
    // Repeatable field helper
    // -------------------------------------------------------------------------

    /**
     * Renders a list of text inputs with Add / Remove buttons.
     *
     * @param string   $key         Option sub-key (e.g. 'phones').
     * @param string[] $values      Current saved values.
     * @param string   $input_type  HTML input type ('tel', 'email').
     * @param string   $add_label   Label for the Add button.
     */
    private function render_repeatable_field(
        string $key,
        array $values,
        string $input_type,
        string $add_label
    ): void {
        $name = esc_attr( self::OPTION_KEY ) . '[' . esc_attr( $key ) . '][]';
        ?>
        <div class="contact-repeatable" data-key="<?php echo esc_attr( $key ); ?>">
            <?php foreach ( $values as $value ) : ?>
            <div class="contact-repeatable__row">
                <input
                    type="<?php echo esc_attr( $input_type ); ?>"
                    name="<?php echo esc_attr( $name ); ?>"
                    value="<?php echo esc_attr( $value ); ?>"
                    class="regular-text"
                >
                <button
                    type="button"
                    class="button contact-repeatable__remove"
                    aria-label="<?php esc_attr_e( 'Remove row', 'contact-options' ); ?>"
                >&minus;</button>
            </div>
            <?php endforeach; ?>

            <button
                type="button"
                class="button contact-repeatable__add"
                data-type="<?php echo esc_attr( $input_type ); ?>"
                data-name="<?php echo esc_attr( $name ); ?>"
            ><?php echo esc_html( $add_label ); ?></button>
        </div>

        <style>
            .contact-repeatable__row { display:flex; gap:8px; margin-bottom:6px; }
            .contact-repeatable__row input { flex:1; max-width:360px; }
        </style>
        <?php
    }

    // -------------------------------------------------------------------------
    // Inline JS for repeatable fields
    // -------------------------------------------------------------------------

    private function repeatable_field_js(): string {
        return <<<JS
        jQuery( function( $ ) {

            // Add a new row
            $( document ).on( 'click', '.contact-repeatable__add', function() {
                var btn   = $( this );
                var type  = btn.data( 'type' );
                var name  = btn.data( 'name' );
                var row   = $(
                    '<div class="contact-repeatable__row">' +
                        '<input type="' + type + '" name="' + name + '" value="" class="regular-text">' +
                        '<button type="button" class="button contact-repeatable__remove" aria-label="Remove row">&minus;</button>' +
                    '</div>'
                );
                btn.before( row );
                row.find( 'input' ).trigger( 'focus' );
            } );

            // Remove a row — keep at least one
            $( document ).on( 'click', '.contact-repeatable__remove', function() {
                var wrap = $( this ).closest( '.contact-repeatable' );
                if ( wrap.find( '.contact-repeatable__row' ).length > 1 ) {
                    $( this ).closest( '.contact-repeatable__row' ).remove();
                }
            } );

        } );
        JS;
    }
}

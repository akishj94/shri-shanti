<?php
/**
 * Sanitizes and validates all Contact Options values before they are saved.
 *
 * Each method corresponds to one field group and returns a clean value
 * of the expected type. Called from Contact_Options_Page::sanitize().
 *
 * @package ContactOptions
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Contact_Options_Sanitizer {

    /**
     * Sanitize a repeatable list of plain-text items (phones or emails).
     *
     * - Strips empty entries.
     * - Validates email format when $type === 'email'.
     * - Returns a re-indexed array of strings.
     *
     * @param  mixed  $raw   Raw POST value — expected array of strings.
     * @param  string $type  'phone' or 'email'.
     * @return string[]
     */
    public function repeatable( mixed $raw, string $type ): array {
        if ( ! is_array( $raw ) ) {
            return [];
        }

        $clean = [];

        foreach ( $raw as $item ) {
            $item = sanitize_text_field( wp_unslash( (string) $item ) );

            if ( '' === $item ) {
                continue;
            }

            if ( 'email' === $type && ! is_email( $item ) ) {
                continue; // silently drop invalid emails
            }

            $clean[] = $item;
        }

        return array_values( $clean );
    }

    /**
     * Sanitize a plain textarea (address).
     *
     * @param  mixed $raw
     * @return string
     */
    public function textarea( mixed $raw ): string {
        return sanitize_textarea_field( wp_unslash( (string) $raw ) );
    }

    /**
     * Sanitize a URL field (maps_url, whatsapp).
     *
     * Only allows http/https and the whatsapp:// scheme.
     * Returns an empty string for anything that doesn't pass esc_url_raw().
     *
     * @param  mixed    $raw
     * @param  string[] $allowed_schemes
     * @return string
     */
    public function url( mixed $raw, array $allowed_schemes = [ 'https', 'http' ] ): string {
        $url = esc_url_raw( wp_unslash( (string) $raw ), $allowed_schemes );
        return $url;
    }
}

<?php
/**
 * Contact Options — template helper functions.
 *
 * Require this file from functions.php alongside contact-options.php:
 *
 *   require_once get_template_directory() . '/includes/contact-options/contact-options-helpers.php';
 *
 * Then use anywhere in your templates:
 *
 *   contact_phones();
 *   contact_emails();
 *   contact_address();
 *   contact_maps_url();
 *   contact_whatsapp_url();
 *
 * @package ContactOptions
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Returns all saved contact options as an array.
 *
 * @return array<string, mixed>
 */
function contact_options(): array {
    static $cache = null;
    if ( null === $cache ) {
        $cache = (array) get_option( 'contact_options', [] );
    }
    return $cache;
}

/**
 * Returns the list of phone numbers.
 *
 * @return string[]
 */
function contact_phones(): array {
    return (array) ( contact_options()['phones'] ?? [] );
}

/**
 * Returns the list of email addresses.
 *
 * @return string[]
 */
function contact_emails(): array {
    return (array) ( contact_options()['emails'] ?? [] );
}

/**
 * Returns the contact address.
 *
 * @return string
 */
function contact_address(): string {
    return (string) ( contact_options()['address'] ?? '' );
}

/**
 * Returns the Google Directions URL.
 *
 * @return string
 */
function contact_maps_url(): string {
    return (string) ( contact_options()['maps_url'] ?? '' );
}

/**
 * Returns the WhatsApp link.
 *
 * @return string
 */
function contact_whatsapp_url(): string {
    return (string) ( contact_options()['whatsapp'] ?? '' );
}

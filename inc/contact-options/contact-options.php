<?php
/**
 * Contact Options
 *
 * Registers a Theme Options admin page for storing contact/account information:
 * contact numbers, emails, address, Google Directions link, and WhatsApp link.
 *
 * Usage — retrieve any value anywhere in your theme:
 *
 *   $options = get_option( 'contact_options', [] );
 *
 *   // Single values
 *   $address   = $options['address']    ?? '';
 *   $maps_url  = $options['maps_url']   ?? '';
 *   $whatsapp  = $options['whatsapp']   ?? '';
 *
 *   // Repeatable fields
 *   $phones = $options['phones'] ?? [];
 *   $emails = $options['emails'] ?? [];
 *
 * Drop this file in your theme's /includes/ directory and require it
 * from functions.php:
 *
 *   require_once get_template_directory() . '/includes/contact-options/contact-options.php';
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/includes/class-contact-options-page.php';
require_once __DIR__ . '/includes/class-contact-options-sanitizer.php';

( new Contact_Options_Page() )->init();

<?php

defined( 'ABSPATH' ) || exit;

// ─── Constants ────────────────────────────────────────────────────────────────

define( 'THEME_DIR', get_template_directory() );
define( 'THEME_URI', get_template_directory_uri() );
define( 'VITE_DEV_SERVER', 'http://localhost:5173' );

// Toggle: set to true locally, false on staging/production.
// Better: drive this from an env var or wp-config constant.
define( 'VITE_DEV_MODE', defined( 'WP_VITE_DEV' ) ? WP_VITE_DEV : false );

// ─── Assets ───────────────────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', 'theme_enqueue_assets' );

function theme_enqueue_assets(): void {

    if ( VITE_DEV_MODE ) {
        theme_enqueue_dev();
    } else {
        theme_enqueue_prod();
    }
}

/**
 * DEV: point directly at the Vite dev server.
 * Vite's HMR client handles CSS + JS updates without a full reload.
 */
function theme_enqueue_dev(): void {
    // Vite HMR client — must load as a module
    wp_enqueue_script(
        'vite-client',
        VITE_DEV_SERVER . '/@vite/client',
        [],
        null,
        false
    );

    // Our entry point
    wp_enqueue_script(
        'theme-main',
        VITE_DEV_SERVER . '/js/main.js',
        [],
        null,
        true
    );

    // Mark both as ES modules (required by Vite)
    add_filter( 'script_loader_tag', 'theme_add_module_type', 10, 2 );
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap',
        array(),
        null
    );
}

/**
 * PROD: read the Vite manifest and enqueue hashed asset paths.
 */
function theme_enqueue_prod(): void {
    $manifest_path = THEME_DIR . '/dist/.vite/manifest.json';

    if ( ! file_exists( $manifest_path ) ) {
        // Hard error in dev-adjacent environments; silent in production.
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            wp_die( 'Vite manifest not found. Run <code>npm run build</code>.' );
        }
        return;
    }

    $manifest = json_decode( file_get_contents( $manifest_path ), true );
    $entry    = $manifest['js/main.js'] ?? null;

    if ( ! $entry ) {
        return;
    }

    // Enqueue hashed JS
    wp_enqueue_script(
        'theme-main',
        THEME_URI . '/dist/' . $entry['file'],
        [],
        null,
        true
    );
    add_filter( 'script_loader_tag', 'theme_add_module_type', 10, 2 );

    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap',
        array(),
        null
    );

    // Enqueue hashed CSS (Vite extracts CSS from JS in prod)
    if ( ! empty( $entry['css'] ) ) {
        foreach ( $entry['css'] as $i => $css_file ) {
            wp_enqueue_style(
                'theme-main-' . $i,
                THEME_URI . '/dist/' . $css_file,
                [],
                null
            );
        }
    }
}

/**
 * Add type="module" to Vite scripts — required for ES module imports.
 */
function theme_add_module_type( string $tag, string $handle ): string {
    $module_handles = [ 'vite-client', 'theme-main' ];

    if ( in_array( $handle, $module_handles, true ) ) {
        $tag = str_replace( '<script ', '<script type="module" ', $tag );
    }

    return $tag;
}

// ─── Theme setup ──────────────────────────────────────────────────────────────

add_action( 'after_setup_theme', 'theme_setup' );

function theme_setup(): void {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'gallery', 'caption' ] );

    register_nav_menus( [
        'primary' => __( 'Primary Menu', 'wp-theme' ),
        'legal' => __( 'Legal Menu', 'wp-theme' ),
    ] );
}

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

require_once THEME_DIR . '/inc/class-nav-walker.php';


/**
 * Remove WordPress block styles completely.
 */
add_action('wp_enqueue_scripts', function() {
    // Remove all WordPress block styles
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-blocks-style'); // WooCommerce blocks
    wp_dequeue_style('classic-theme-styles');
    wp_dequeue_style('global-styles');
    wp_dequeue_style( 'wp-emoji-styles' );
}, 100);

/**
 * Disable WordPress global styles (theme.json output).
 */
add_filter('wp_theme_json_get_style_nodes', '__return_empty_array');

/**
 * Remove global styles inline CSS completely.
 */
remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
add_filter( 'should_load_separate_core_block_assets', '__return_false' );

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
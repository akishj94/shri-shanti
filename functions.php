<?php

defined('ABSPATH') || exit;

require_once get_template_directory() . '/inc/disable-comments.php';

// ─── Constants ─────────────────────────────────────────────

define('THEME_DIR', get_template_directory());
define('THEME_URI', get_template_directory_uri());

// ─── Assets ────────────────────────────────────────────────

add_action('wp_enqueue_scripts', 'theme_enqueue_assets');

function theme_enqueue_assets(): void {

    $css_path = THEME_DIR . '/assets/css/main.css';
    $js_path  = THEME_DIR . '/assets/js/main.js';

    // CSS
    if (file_exists($css_path)) {
        wp_enqueue_style(
            'theme-style',
            THEME_URI . '/assets/css/main.css',
            [],
            filemtime($css_path)
        );
    }

    // Google Fonts
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:ital,wght@0,400..900;1,400..900&family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap',
        [],
        null
    );

    // JS
    if (file_exists($js_path)) {
        wp_enqueue_script(
            'theme-main',
            THEME_URI . '/assets/js/main.js',
            [],
            filemtime($js_path),
            true
        );
    }
}

// ─── Theme setup ───────────────────────────────────────────

add_action('after_setup_theme', 'theme_setup');

function theme_setup(): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'gallery', 'caption']);

    register_nav_menus([
        'primary' => __('Primary Menu', 'wp-theme'),
        'legal'   => __('Legal Menu', 'wp-theme'),
    ]);
}

// ─── Includes ──────────────────────────────────────────────

require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/customizer.php';

if (defined('JETPACK__VERSION')) {
    require get_template_directory() . '/inc/jetpack.php';
}

require_once THEME_DIR . '/inc/class-nav-walker.php';

// ─── Cleanup ───────────────────────────────────────────────

// Remove block styles
add_action('wp_enqueue_scripts', function () {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-blocks-style');
    wp_dequeue_style('classic-theme-styles');
    wp_dequeue_style('global-styles');
    wp_dequeue_style('wp-emoji-styles');
}, 100);

// Disable theme.json styles
add_filter('wp_theme_json_get_style_nodes', '__return_empty_array');

// Remove global styles
remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
add_filter('should_load_separate_core_block_assets', '__return_false');

// Remove emojis
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// Allow SVG upload (admin only)
add_filter('upload_mimes', function ($mimes) {
    if (!current_user_can('manage_options')) {
        return $mimes;
    }
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});

// Fix admin SVG preview
add_action('admin_head', function () {
    echo '<style>
        .attachment-266x266, .thumbnail img {
            width: 100% !important;
            height: auto !important;
        }
    </style>';
});

// Debug block rendering (optional)
add_filter('render_block', function ($content, $block) {
    error_log('BLOCK: ' . $block['blockName']);
    return $content;
}, 10, 2);
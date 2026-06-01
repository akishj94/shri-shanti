<?php

defined('ABSPATH') || exit;

require_once get_template_directory() . '/inc/disable-comments.php';
require_once get_template_directory() . '/inc/custom-catalog.php';
require_once get_template_directory() . '/inc/class-social-walker.php';
require_once get_template_directory() . '/inc/site-cta.php';
require_once get_template_directory() . '/inc/shri-catalog-post.php';
require_once get_template_directory() . '/inc/shri-industry-folio.php';

require_once get_template_directory() . '/inc/contact-options/contact-options.php';
require_once get_template_directory() . '/inc/contact-options/contact-options-helpers.php';

// ─── Constants ─────────────────────────────────────────────

define('THEME_DIR', get_template_directory());
define('THEME_URI', get_template_directory_uri());

// ─── Assets ────────────────────────────────────────────────

add_action('wp_enqueue_scripts', 'theme_enqueue_assets');

function theme_enqueue_assets(): void {

    $css_path = THEME_DIR . '/assets/css/main.css';
    $js_path  = THEME_DIR . '/assets/js/main.js';
    $blog_css_path = THEME_DIR . '/assets/css/blog.css';

    // CSS
    if (file_exists($css_path)) {
        wp_enqueue_style(
            'theme-style',
            THEME_URI . '/assets/css/main.css',
            [],
            filemtime($css_path)
        );
    }
    // Blog post single page only
    if (is_singular('post') && file_exists($blog_css_path)) {
        wp_enqueue_style(
            'theme-blog',
            THEME_URI . '/assets/css/blog.css',
            ['theme-style'],
            filemtime($blog_css_path)
        );
    }
    // Google Fonts
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Onest:wght@100..900&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap',
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
        'footer' => __('Footer Menu', 'wp-theme'),
        'legal'   => __('Legal Menu', 'wp-theme'),
        'social-menu'   => __('Social Menu', 'wp-theme'),
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

add_action( 'wp_body_open', function() {
    include get_template_directory() . '/assets/images/social-sprite.svg';
});

add_filter('render_block', function ($block_content, $block) {

    if ($block['blockName'] !== 'core/paragraph') {
        return $block_content;
    }

    $trimmed = trim($block_content);

    // Match paragraph containing only an anchor tag
    if (preg_match('#^<p>\s*(<a[^>]+>.*?</a>)\s*</p>$#is', $trimmed, $matches)) {
        return $matches[1];
    }

    return $block_content;

}, 10, 2);

add_filter( 'wpcf7_autop_or_not', '__return_false' );



/**
 * Load Gutenberg / WP styles ONLY on blog-related pages.
 * Everywhere else -> remove WP block/theme global CSS.
 */

function shri_is_blog_related() {

    // Blog archives
    if (
        is_archive() ||
        is_category() ||
        is_tag() ||
        is_author() ||
        is_date()
    ) {
        return true;
    }

    // Singles (posts + CPT)
    if ( is_singular('post') || is_post_type_archive() ) {
        return true;
    }

    return false;
}


/**
 * REMOVE WP styles on NON-blog pages only
 */
add_action('wp_enqueue_scripts', function () {

    // Keep WP styles on blog-related pages
    if ( shri_is_blog_related() ) {
        return;
    }

    // Remove block styles
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-blocks-style');
    wp_dequeue_style('classic-theme-styles');
    wp_dequeue_style('global-styles');
    wp_dequeue_style('wp-emoji-styles');

}, 100);


/**
 * Disable theme.json styles on NON-blog pages
 */
add_filter('wp_theme_json_get_style_nodes', function ($nodes) {

    if ( ! shri_is_blog_related() ) {
        return [];
    }

    return $nodes;
});


/**
 * Remove global styles on NON-blog pages
 */
add_action('wp', function () {

    // Keep WP styles on blog pages
    if ( shri_is_blog_related() ) {
        return;
    }

    remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
    remove_action('wp_footer', 'wp_enqueue_global_styles', 1);

    // Remove emojis
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');

});


/**
 * Disable separate block assets on NON-blog pages
 */
add_filter('should_load_separate_core_block_assets', function ($load) {

    if ( ! shri_is_blog_related() ) {
        return false;
    }

    return $load;
});

function disable_wordpress_search($query, $error = true) {
    if (is_search()) {
        $query->is_search = false;
        $query->query_vars['s'] = false;
        $query->query['s'] = false;

        if ($error == true)
            $query->is_404 = true;
    }
}

add_action('parse_query', 'disable_wordpress_search');
add_filter('get_search_form', '__return_empty_string');
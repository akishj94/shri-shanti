<?php
/**
 * WordPress Navigation Menu
 * Drop-down: hover on desktop, click on mobile
 * Staggered animation on open, resets on resize
 *
 * Usage: include this file in your header.php
 * Make sure to register 'primary' menu in functions.php:
 *   register_nav_menus(['primary' => __('Primary Menu', 'wp-theme')]);
 */
?>

<nav class="site-nav" id="site-nav" role="navigation" aria-label="Primary Navigation">
    <!-- WordPress menu output -->
    <?php
    
    Nav_Walker::render([
        'theme_location'  => 'primary',
        'container'       => false,
        'container_class' => 'site-nav',
        'menu_class'      => 'nav-list',
    ]);
    ?>
    <button class="site_cta">Connect</button>
</nav>

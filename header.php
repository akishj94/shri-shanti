<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" data-animate>
  <div class="menuBg"></div>
  <div class="container">
    <div class="flex space-between align-center">
      <!-- Site Logo -->
      <div class="site_branding">
        <a href="<?php echo esc_url( home_url('/') ); ?>">
          <img src="<?php echo get_template_directory_uri().'/assets/images/shri_logo.svg'; ?>" alt="Shri Shanti Engineering Logo">
        </a>
      </div>

      <!-- Nav Menu -->
      <?php get_template_part('template-parts/nav-menu'); ?>
    </div>
  </div>
</header>

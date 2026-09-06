<!DOCTYPE html>
<html <?php language_attributes(); ?> class="is-loading">
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <?php wp_head(); ?>
  <script>
     window.isFirstLoad = !sessionStorage.getItem('preloaderShown');
    if (window.isFirstLoad) {
        sessionStorage.setItem('preloaderShown', '1');
        document.documentElement.classList.add('is-first-load');
    }
  </script>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" <?php if ( is_front_page() || is_page(207) ) echo 'data-theme="light"'; ?>>
  <div class="navbar_background-mobile"></div>
  <div class="container">
    <div class="flex space-between align-center">
      <div class="flex space-between align-center">
        <!-- Site Logo -->
        <div class="site_branding">
          <a href="<?php echo esc_url( home_url('/') ); ?>">
            <img src="<?php echo get_template_directory_uri().'/assets/images/shri_logo.svg'; ?>" alt="Shri Shanti Engineering Logo">
          </a>
        </div>
        
        <!-- Mobile hamburger -->
        <button class="nav-toggler" id="nav-toggler" aria-label="Toggle menu" aria-expanded="false">
            <span></span><span></span>
        </button>
      </div>
      

      <!-- Nav Menu -->
      <?php get_template_part('template-parts/nav-menu'); ?>
    </div>
  </div>  
</header>
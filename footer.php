<footer class="site-footer" data-animate>
  
  <div class="container">
    <div class="footer_branding flex align-center space-between">
      <div class="footerLogoIcon">
        <img src="<?php echo get_template_directory_uri().'/assets/images/shri_logo_icon.svg'; ?>" alt="Shri Shanti Engineering">
      </div>

      <div class="footerLogoText">
        <img src="<?php echo get_template_directory_uri().'/assets/images/shri_logo_text.svg'; ?>" alt="Shri Shanti Engineering">
      </div>
    </div>

    <div class="footerNav">
      <?php
        wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'nav-list',
            'menu_id'        => 'nav-list',
            'fallback_cb'    => false,
        ]);
        ?>
    </div>
    <div class="footerSocial">
      <?php
        wp_nav_menu([
            'theme_location' => 'social',
            'menu_class'     => 'social-menu',
            'walker'         => new Social_Nav_Walker(),
            'depth'          => 1,
        ]);
      ?>
    </div>
    <div class="bordered_separator"></div>
    
    <div class="footerBottom">
      <div class="flex align-end space-between">
          <div class="footerSiteTitle bottom-pd">
            <h4>Where Every <br> Roll Shapes Reliability. <sup>TM</sup></h4>
          </div>
          <div class="footerLegalMenu bottom-pd">
            <?php
              wp_nav_menu([
                  'theme_location' => 'legal',
                  'container'      => false,
                  'menu_class'     => 'nav-list',
                  'menu_id'        => 'nav-list',
                  'fallback_cb'    => false,
              ]);
            ?>
          </div>
          <div class="footerSocialMenu bottom-pd"></div>
          <div class="siteCredits flex align-end">
            <span class="bottom-pd">Branding & Website by</span>
              <a href="" target="_blank">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 29 38.337">
									<defs>
										<clipPath id="clip-path">
											<rect id="Rectangle_4883" data-name="Rectangle 4883" width="29" height="38.337" fill="#fff"></rect>
										</clipPath>
									</defs>
									<g id="Group_13548" data-name="Group 13548" transform="translate(0 0)">
										<g id="Group_13547" data-name="Group 13547" transform="translate(0 0)" clip-path="url(#clip-path)">
										<path id="Path_28" data-name="Path 28" d="M2.352,24.966a11.134,11.134,0,0,0,3.393,8.193Q9.035,36.3,14.8,36.3a13.775,13.775,0,0,0,4.874-.763,9.406,9.406,0,0,0,3.245-1.939,10.063,10.063,0,0,0,1.862-2.3,17.58,17.58,0,0,0,1.152-2.448h2.347A13.6,13.6,0,0,1,23.26,36.02a15.123,15.123,0,0,1-8.7,2.318q-6.691,0-10.619-3.8T0,24.251a14.874,14.874,0,0,1,.768-4.6,13.736,13.736,0,0,1,2.474-4.44,12.186,12.186,0,0,1,4.572-3.367A16.547,16.547,0,0,1,14.6,10.566q6.691,0,10.521,3.984A14.587,14.587,0,0,1,29,24.966Zm23.994-2.042a11.417,11.417,0,0,0-3.957-7.735,12.492,12.492,0,0,0-8.039-2.58,11.748,11.748,0,0,0-8.092,2.784,12.3,12.3,0,0,0-3.9,7.531ZM21.289,0l-8.01,7.25h-2.3L18.33,0Z" transform="translate(0 -0.001)" fill="#fff"></path>
										</g>
									</g>
								</svg>
							</a>
          </div>
      </div>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

<?php
/**
 * Social Nav Walker
 *
 * Matches menu item URLs to icon IDs in the static SVG sprite
 * (assets/svg/social-sprite.svg) and renders accessible icon links.
 *
 * Usage in functions.php:
 *   require_once get_template_directory() . '/includes/class-social-walker.php';
 *
 * Usage in template:
 *   wp_nav_menu([
 *       'theme_location' => 'social',
 *       'walker'         => new Social_Nav_Walker(),
 *       'container'      => 'nav',
 *       'menu_class'     => 'shri_social_menu',
 *   ]);
 */

declare( strict_types=1 );

class Social_Nav_Walker extends Walker_Nav_Menu {

    /**
     * Domain fragments → sprite symbol ID.
     * Matched via str_contains() so partial strings work (e.g. youtu.be).
     */
    private const DOMAIN_MAP = [
        'facebook.com'  => 'icon-facebook',
        'fb.com'        => 'icon-facebook',
        'x.com'         => 'icon-x',
        'twitter.com'   => 'icon-x',
        'instagram.com' => 'icon-instagram',
        'youtube.com'   => 'icon-youtube',
        'youtu.be'      => 'icon-youtube',
        'linkedin.com'  => 'icon-linkedin',
        'tiktok.com'    => 'icon-tiktok',
        'pinterest.com' => 'icon-pinterest',
        'reddit.com'    => 'icon-reddit',
        'discord.com'   => 'icon-discord',
        'discord.gg'    => 'icon-discord',
        'github.com'    => 'icon-github',
        'whatsapp.com'  => 'icon-whatsapp',
        'wa.me'         => 'icon-whatsapp',
        'telegram.me'   => 'icon-telegram',
        't.me'          => 'icon-telegram',
        'spotify.com'   => 'icon-spotify',
        'snapchat.com'  => 'icon-snapchat',
        'mailto:'       => 'icon-email',
    ];

    /**
     * Resolve a URL to its sprite symbol ID.
     */
    private function get_icon_id( string $url ): ?string {
        $url = strtolower( $url );
        foreach ( self::DOMAIN_MAP as $fragment => $id ) {
            if ( str_contains( $url, $fragment ) ) {
                return $id;
            }
        }
        return null;
    }

    /**
     * Render each menu item as an accessible icon link.
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ): void {
        $icon_id = $this->get_icon_id( (string) $item->url );
        $url     = esc_url( $item->url );
        $label   = esc_attr( $item->title );

        $output .= '<li class="shri_social_menu__item">';
        $output .= '<a href="' . $url . '"'
                 . ' class="shri_social_menu__link"'
                 . ' target="_blank"'
                 . ' rel="noopener noreferrer"'
                 . ' aria-label="' . $label . '"'
                 . ' title="' . $label . '"'
                 . '>';

        if ( $icon_id ) {
            // Reference into the static sprite — zero extra HTTP requests.
            $output .= '<svg class="shri_social_menu__icon" aria-hidden="true" focusable="false" viewBox="0 0 24 24">';
            $output .= '<use href="' . esc_attr( '#' . $icon_id ) . '"></use>';
            $output .= '</svg>';
        }

        // Always include the label as visually hidden text for screen readers.
        $output .= '<span class="screen-reader-text">' . esc_html( $item->title ) . '</span>';
        $output .= '</a>';
        $output .= '</li>';
    }

    /**
     * Close the list item.
     */
    public function end_el( &$output, $item, $depth = 0, $args = null ): void {
        $output .= '</li>';
    }
}

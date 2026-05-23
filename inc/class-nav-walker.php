<?php
/**
 * Nav_Walker
 *
 * - Top-level items: standard link with SVG arrow if children exist.
 * - Sub-menu items:  nested inside the main <ul>, with featured image if set.
 * - Only one level of sub-menus is supported. Any deeper nesting is ignored.
 * - Use Nav_Walker::render( $args ) instead of wp_nav_menu().
 */

class Nav_Walker extends Walker_Nav_Menu {

    // ── Opening <li> ──────────────────────────────────────────────────────────

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {

        if ( $depth === 0 ) {
            $has_children = in_array( 'menu-item-has-children', (array) $item->classes );

            $output .= '<li class="nav-item' . ( $has_children ? ' has-dropdown' : '' ) . '">';

            if ( $has_children ) {
                $output .= '<a'
                    . ' href="' . esc_url( $item->url ) . '"'
                    . ' class="nav-link"'
                    . ' aria-haspopup="true"'
                    . ' aria-expanded="false"'
                    . '>';
            } else {
                $output .= '<a href="' . esc_url( $item->url ) . '" class="nav-link">';
            }

            $output .= esc_html( $item->title );

            if ( $has_children ) {
                $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" fill="none">'
                         . '<path fill="#fff" stroke="#fff" d="M6.548.579 3.75 3.305.95.577A.268.268 0 0 0 .52.66a.255.255 0 0 0 .056.281l2.977 2.9a.28.28 0 0 0 .391 0l2.977-2.9a.255.255 0 0 0 0-.365.27.27 0 0 0-.374 0z"/>'
                         . '</svg>';
            }

            $output .= '</a>';

        } elseif ( $depth === 1 ) {
            $thumb_url = $this->get_item_thumbnail( $item );

            $output .= '<li class="nav-dropdown-item">';
            $output .= '<a href="' . esc_url( $item->url ) . '" class="nav-dropdown-link' . ( $thumb_url ? ' has-thumbnail' : '' ) . '">';

            if ( $thumb_url ) {
                $output .= '<img src="' . esc_url( $thumb_url ) . '"'
                         . ' alt="' . esc_attr( $item->title ) . '"'
                         . ' width="52" height="40" loading="lazy" />';
            }

            $output .= '<span class="nav-dropdown-label">' . esc_html( $item->title ) . '</span>';
            $output .= '<svg class="item-arrow" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">'
                     . '<path d="M1 1L7 7L1 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>'
                     . '</svg>';
            $output .= '</a>';
        }

        // depth > 1: intentionally ignored — one level only
    }

    // ── Opening <ul> (sub-menu) ───────────────────────────────────────────────

    public function start_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            $output .= '<ul class="nav-dropdown nav-dropdown--default" role="menu">';
        }
    }

    // ── Closing <ul> ─────────────────────────────────────────────────────────

    public function end_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            $output .= '</ul>';
        }
    }

    // ── Closing <li> ─────────────────────────────────────────────────────────

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        if ( $depth === 0 || $depth === 1 ) {
            $output .= '</li>';
        }
    }

    // ── Static render helper ──────────────────────────────────────────────────

    /**
     * Drop-in replacement for wp_nav_menu().
     *
     * Usage:
     *   Nav_Walker::render([
     *       'theme_location' => 'primary',
     *       'container'      => false,
     *       'menu_class'     => 'nav-list',
     *   ]);
     */
    public static function render( array $args = [] ): void {
        wp_nav_menu( array_merge( $args, [ 'walker' => new self() ] ) );
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Fetch the featured image URL for a menu item's linked post/page.
     * Returns null if not set — caller handles the fallback.
     */
    protected function get_item_thumbnail( $item ): ?string {
        if ( empty( $item->object_id ) || $item->type !== 'post_type' ) {
            return null;
        }

        $url = get_the_post_thumbnail_url( (int) $item->object_id, 'thumbnail' );

        return $url ?: null;
    }
}
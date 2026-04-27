<?php
/**
 * Nav_Walker
 *
 * - Top-level items: standard link with optional SVG arrow if children exist
 * - Sub-menu items:  auto-pulls the linked page's featured image (if set)
 *                    Falls back gracefully — no broken layout if image is missing
 * - For archive/CPT pages without a featured image, assign a custom page
 *   template and set a featured image on that template's page in WordPress.
 */

class Nav_Walker extends Walker_Nav_Menu {

    // ── Opening <li> ──────────────────────────────────────────────────────────

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $has_children = in_array( 'menu-item-has-children', (array) $item->classes );

        if ( $depth === 0 ) {
            // ── Top-level item ─────────────────────────────────────────────
            $li_class = $has_children ? ' has-dropdown' : '';
            $output  .= '<li class="nav-item' . $li_class . '">';

            $aria = $has_children
                ? ' aria-haspopup="true" aria-expanded="false"'
                : '';

            $output .= '<a href="' . esc_url( $item->url ) . '" class="nav-link"' . $aria . '>';
            $output .= esc_html( $item->title );

             if ( $has_children ) {
                $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" fill="none"><path fill="#fff" stroke="#fff" d="M6.548.579 3.75 3.305.95.577A.268.268 0 0 0 .52.66a.255.255 0 0 0 .056.281l2.977 2.9a.28.28 0 0 0 .391 0l2.977-2.9a.255.255 0 0 0 0-.365.27.27 0 0 0-.374 0z"/></svg>';
            }

            $output .= '</a>';

        } else {
            // ── Sub-menu item ──────────────────────────────────────────────
            $output .= '<li class="nav-dropdown-item">';

            // Auto-pull featured image from the linked post/page
            $thumb_url = $this->get_item_thumbnail( $item );

            $output .= '<a href="' . esc_url( $item->url ) . '" class="nav-dropdown-link">';

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
    }

    // ── Opening <ul> (sub-menu) ───────────────────────────────────────────────

    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '<ul class="nav-dropdown" role="menu">';
    }

    // ── Closing <ul> ─────────────────────────────────────────────────────────

    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '</ul>';
    }

    // ── Closing <li> ─────────────────────────────────────────────────────────

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= '</li>';
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Fetch the featured image URL for a menu item's linked post/page.
     * Returns null if not set — caller handles the fallback.
     */
    protected function get_item_thumbnail( $item ): ?string {
        // Only works for internal post/page objects
        if ( empty( $item->object_id ) || $item->type !== 'post_type' ) {
            return null;
        }

        $url = get_the_post_thumbnail_url( (int) $item->object_id, 'thumbnail' );

        return $url ?: null;
    }

}
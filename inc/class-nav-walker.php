<?php
/**
 * Nav_Walker
 *
 * - Top-level items: standard link with SVG arrow if children exist.
 *                    Each parent gets a unique data-dropdown-id to target
 *                    its submenu, which is ALSO rendered outside the main <ul>.
 * - Two submenu renders:
 *     1. Default (inline/mobile): nested inside the main <ul>, no featured images.
 *     2. Hoisted (desktop): rendered outside the main <ul>, with featured images.
 * - Only one level of sub-menus is supported. Any deeper nesting is ignored.
 * - Call Nav_Walker::render( $args ) instead of wp_nav_menu().
 */

class Nav_Walker extends Walker_Nav_Menu {

    /** Collects hoisted submenu HTML keyed by dropdown-id. */
    private array $submenu_buffer = [];

    /** Tracks the dropdown-id of the current open parent item. */
    private ?string $current_dropdown_id = null;

    /** Whether we are currently inside a depth-1 sub-menu. */
    private bool $in_submenu = false;

    // ── Opening <li> ──────────────────────────────────────────────────────────

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {

        if ( $depth === 0 ) {
            $has_children = in_array( 'menu-item-has-children', (array) $item->classes );

            $li_class = $has_children ? ' has-dropdown' : '';
            $output  .= '<li class="nav-item' . $li_class . '">';

            if ( $has_children ) {
                $dropdown_id              = 'dropdown-' . (int) $item->ID;
                $this->current_dropdown_id = $dropdown_id;

                $output .= '<a'
                    . ' href="' . esc_url( $item->url ) . '"'
                    . ' class="nav-link"'
                    . ' aria-haspopup="true"'
                    . ' aria-expanded="false"'
                    . ' data-dropdown-target="' . esc_attr( $dropdown_id ) . '"'
                    . '>';
            } else {
                $this->current_dropdown_id = null;
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
            // ── Default inline sub-menu item (no featured image) ──────────────
            $output .= '<li class="nav-dropdown-item">';
            $output .= '<a href="' . esc_url( $item->url ) . '" class="nav-dropdown-link">';
            $output .= '<span class="nav-dropdown-label">' . esc_html( $item->title ) . '</span>';
            $output .= '<svg class="item-arrow" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">'
                     . '<path d="M1 1L7 7L1 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>'
                     . '</svg>';
            $output .= '</a>';

            // ── Hoisted submenu item (with featured image) ────────────────────
            if ( $this->current_dropdown_id ) {
                $thumb_url = $this->get_item_thumbnail( $item );
                $html      = '<li class="nav-dropdown-item">';
                $html     .= '<a href="' . esc_url( $item->url ) . '" class="nav-dropdown-link">';

                if ( $thumb_url ) {
                    $html .= '<img src="' . esc_url( $thumb_url ) . '"'
                           . ' alt="' . esc_attr( $item->title ) . '"'
                           . ' width="52" height="40" loading="lazy" />';
                }

                $html .= '<span class="nav-dropdown-label">' . esc_html( $item->title ) . '</span>';
                $html .= '<svg class="item-arrow" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">'
                       . '<path d="M1 1L7 7L1 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>'
                       . '</svg>';
                $html .= '</a>';

                $this->submenu_buffer[ $this->current_dropdown_id ] =
                    ( $this->submenu_buffer[ $this->current_dropdown_id ] ?? '' ) . $html;
            }
        }

        // depth > 1: intentionally ignored — one level only
    }

    // ── Opening <ul> (sub-menu) ───────────────────────────────────────────────

    public function start_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            // Default inline submenu — render normally into $output
            $this->in_submenu = true;
            $output .= '<ul class="nav-dropdown nav-dropdown--default" role="menu">';

            // Initialise the hoisted bucket for this parent
            if ( $this->current_dropdown_id ) {
                $this->submenu_buffer[ $this->current_dropdown_id ] =
                    $this->submenu_buffer[ $this->current_dropdown_id ] ?? '';
            }
        }
    }

    // ── Closing <ul> ─────────────────────────────────────────────────────────

    public function end_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            $this->in_submenu = false;
            $output .= '</ul>';
        }
    }

    // ── Closing <li> ─────────────────────────────────────────────────────────

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            $output .= '</li>';
        } elseif ( $depth === 1 ) {
            // Close inline item
            $output .= '</li>';

            // Close hoisted item
            if ( $this->current_dropdown_id ) {
                $this->submenu_buffer[ $this->current_dropdown_id ] .= '</li>';
            }
        }
    }

    // ── Flush buffered hoisted submenus ───────────────────────────────────────

    /**
     * Returns all hoisted submenus as a single HTML string.
     * Each submenu is wrapped in <ul class="nav-dropdown nav-dropdown--hoisted">
     * with an id matching the parent's data-dropdown-target.
     */
    public function flush_submenus(): string {
        $html = '';
        foreach ( $this->submenu_buffer as $id => $items ) {
            $html .= '<ul'
                   . ' id="' . esc_attr( $id ) . '"'
                   . ' class="nav-dropdown nav-dropdown--hoisted"'
                   . ' role="menu"'
                   . ' hidden'
                   . '>';
            $html .= $items;
            $html .= '</ul>';
        }
        $this->submenu_buffer = [];
        return $html;
    }

    // ── Static render helper ──────────────────────────────────────────────────

    /**
     * Drop-in replacement for wp_nav_menu().
     * Outputs the main <nav> (with inline default submenus) followed by
     * all hoisted <ul class="nav-dropdown--hoisted"> panels.
     *
     * Usage:
     *   Nav_Walker::render([
     *       'theme_location' => 'primary',
     *       'container'      => 'nav',
     *       'container_class'=> 'site-nav',
     *   ]);
     */
    public static function render( array $args = [] ): void {
        $walker = new self();

        $args = array_merge( $args, [
            'walker' => $walker,
            'echo'   => false,
        ] );

        $nav_html = wp_nav_menu( $args );

        echo $nav_html;                  // Main nav with inline default submenus
        echo $walker->flush_submenus();  // Hoisted desktop submenu panels
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
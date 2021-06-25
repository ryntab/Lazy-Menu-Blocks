<?php
/**
   * Lazy Menu Nav Walker 😴
   * 
   * 
   * @package    Lazy Menus
   * @author     Ryan Taber
   * @description Custom Flatsome Nav Walker.
   */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class lazyMenuNav extends FlatsomeNavDropdown {

/**
 * Starts the element output.
 *
 * @since 3.0.0
 * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
 *
 * @see Walker::start_el()
 *
 * @param string   $output Used to append additional content (passed by reference).
 * @param WP_Post  $item   Menu item data object.
 * @param int      $depth  Depth of menu item. Used for padding.
 * @param stdClass $args   An object of wp_nav_menu() arguments.
 * @param int      $id     Current item ID.
 */
public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
    if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
        $t = '';
        $n = '';
    } else {
        $t = "\t";
        $n = "\n";
    }
    $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

    $classes   = empty( $item->classes ) ? array() : (array) $item->classes;
    $classes[] = 'menu-item-' . $item->ID;

    // Set Active Class.
    if ( in_array( 'current-menu-ancestor', $classes, true ) || in_array( 'current-menu-item', $classes, true ) || in_array( 'current-menu-parent', $classes, true ) ) {
        $classes[] = 'active';
    }

    $design        = get_post_meta( $item->ID, '_menu_item_design', true );
    $width         = get_post_meta( $item->ID, '_menu_item_width', true );
    $height        = get_post_meta( $item->ID, '_menu_item_height', true );
    $block         = get_post_meta( $item->ID, '_menu_item_block', true );
    $behavior      = get_post_meta( $item->ID, '_menu_item_behavior', true );
    $icon_type     = get_post_meta( $item->ID, '_menu_item_icon-type', true );
    $icon_id       = get_post_meta( $item->ID, '_menu_item_icon-id', true );
    $icon_width    = get_post_meta( $item->ID, '_menu_item_icon-width', true );
    $icon_height   = get_post_meta( $item->ID, '_menu_item_icon-height', true );
    $icon_html     = get_post_meta( $item->ID, '_menu_item_icon-html', true );
    $is_top_level  = $depth == 0;
    $is_block_menu = ! empty( $block );

    if ( empty( $design ) ) {
        $design = 'default';
    }

    if ( $is_top_level ) {
        $classes[] = 'menu-item-design-' . $design;

        if ( $is_block_menu ) {
            $classes[] = 'menu-item-has-block';
        }
    }

    if ( $is_top_level && ( $is_block_menu || $item->has_children ) ) {
        $classes[] = 'has-dropdown';

        if ( 'click' === $behavior ) {
            $classes[] = 'nav-dropdown-toggle';
        }
    }

    if ( $item->has_children && $depth == 1 ) {
        $classes[] = 'nav-dropdown-col';
    }

    // LEGACY Add flatsome Icons.
    $menu_icon = '';
    if ( strpos( $classes[0], 'icon-' ) !== false ) {
        $menu_icon  = get_flatsome_icon( $classes[0] );
        $classes[0] = 'has-icon-left';
    }

    if ( $icon_type === 'media' && ! empty( $icon_id )
        || $icon_type === 'html' && ! empty( $icon_html ) ) {
        $classes[] = 'has-icon-left';
    }

    /**
     * Filters the arguments for a single nav menu item.
     *
     * @since 4.4.0
     *
     * @param stdClass $args  An object of wp_nav_menu() arguments.
     * @param WP_Post  $item  Menu item data object.
     * @param int      $depth Depth of menu item. Used for padding.
     */
    $args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

    /**
     * Filters the CSS classes applied to a menu item's list item element.
     *
     * @since 3.0.0
     * @since 4.1.0 The `$depth` parameter was added.
     *
     * @param string[] $classes Array of the CSS classes that are applied to the menu item's `<li>` element.
     * @param WP_Post  $item    The current menu item.
     * @param stdClass $args    An object of wp_nav_menu() arguments.
     * @param int      $depth   Depth of menu item. Used for padding.
     */
    $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
    $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

    /**
     * Filters the ID applied to a menu item's list item element.
     *
     * @since 3.0.1
     * @since 4.1.0 The `$depth` parameter was added.
     *
     * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
     * @param WP_Post  $item    The current menu item.
     * @param stdClass $args    An object of wp_nav_menu() arguments.
     * @param int      $depth   Depth of menu item. Used for padding.
     */
    $id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
    $id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

    $output .= $indent . '<li' . $id . $class_names . '>';

    $atts           = array();
    $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
    $atts['target'] = ! empty( $item->target ) ? $item->target : '';
    if ( '_blank' === $item->target && empty( $item->xfn ) ) {
        $atts['rel'] = 'noopener noreferrer';
    } else {
        $atts['rel'] = $item->xfn;
    }
    $atts['href']         = ! empty( $item->url ) ? $item->url : '';
    $atts['aria-current'] = $item->current ? 'page' : '';

    /**
     * Filters the HTML attributes applied to a menu item's anchor element.
     *
     * @since 3.6.0
     * @since 4.1.0 The `$depth` parameter was added.
     *
     * @param array $atts {
     *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
     *
     *     @type string $title        Title attribute.
     *     @type string $target       Target attribute.
     *     @type string $rel          The rel attribute.
     *     @type string $href         The href attribute.
     *     @type string $aria_current The aria-current attribute.
     * }
     * @param WP_Post  $item  The current menu item.
     * @param stdClass $args  An object of wp_nav_menu() arguments.
     * @param int      $depth Depth of menu item. Used for padding.
     */
    $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

    $attributes = '';
    foreach ( $atts as $attr => $value ) {
        if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
            $value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
            $attributes .= ' ' . $attr . '="' . $value . '"';
        }
    }

    /** This filter is documented in wp-includes/post-template.php */
    $title = apply_filters( 'the_title', $item->title, $item->ID );

    /**
     * Filters a menu item's title.
     *
     * @since 4.4.0
     *
     * @param string   $title The menu item's title.
     * @param WP_Post  $item  The current menu item.
     * @param stdClass $args  An object of wp_nav_menu() arguments.
     * @param int      $depth Depth of menu item. Used for padding.
     */
    $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

    // Check if menu item is in main menu.
    if ( $depth == 0 ) {
        // These lines adds your custom class and attribute.
        $attributes .= ' id="so-lazy"';
    }

    // Image Column.
    if ( strpos( $class_names, 'image-column' ) !== false ) {
        $item_output  = '';
        $item_output .= '<a' . $attributes . ' class="dropdown-image-column">';
        $item_output .= '<img width="180" height="480" src="' . $item->description . '" title="' . apply_filters( 'the_title', $item->title, $item->ID ) . '" alt="' . apply_filters( 'the_title', $item->title, $item->ID ) . '"/>';
        $item_output .= '</a>';
    } elseif ( strpos( $class_names, 'category-column' ) !== false ) { // Category Image.
        $item_output = '<div class="category-images-preview">Loading</div>';

    } else {
        // Normal Items.
        $item_output  = $args->before;
        $item_output .= '<a' . $attributes . '>';

        // LEGACY Add menu icon.
        if ( $menu_icon ) {
            $item_output .= $menu_icon;
        }

        switch ( $icon_type ) {
            case 'media':
                if ( ! empty( $icon_id ) ) {
                    $item_output .= sprintf( '<img class="%s" width="%s" height="%s" src="%s" alt="%s" />',
                        'ux-menu-icon',
                        $icon_width ? $icon_width : 20,
                        $icon_height ? $icon_height : 20,
                        wp_get_attachment_image_src( $icon_id )[0],
                        get_post_meta( $icon_id, '_wp_attachment_image_alt', true )
                    );
                }
                break;
            case 'html':
                if ( ! empty( $icon_html ) ) {
                    $item_output .= do_shortcode( $icon_html );
                }
                break;
        }

        $item_output .= $args->link_before . $title . $args->link_after;

        // Add down arrow.
        $arrow_icon = '';
        if ( $is_top_level && ( $is_block_menu || $item->has_children ) ) {
            $arrow_icon = get_flatsome_icon( 'icon-angle-down' );
        }

        $item_output .= $arrow_icon . '</a>';
        $item_output .= $args->after;

        $css = '';
        if ( $is_top_level && $is_block_menu ) {
            $dropdown_classes = array( 'sub-menu', 'nav-dropdown', 'lazy-skeleton' );
            $dropdown_classes = implode( ' ', $dropdown_classes );

            $item_output .= '<div style="min-height: 400px;" class="' . esc_attr( $dropdown_classes ) . '">
            <div class="bg section-bg fill bg-fill  bg-loaded">
                <div class="loading-spin centered">
                </div>
            </div>';
            $item_output .= '</div>';
        }
        if ( $design == 'custom-size' && ! empty( $width ) ) {
            $css .= '#menu-item-' . $item->ID . ' > .nav-dropdown {';
            $css .= 'width: ' . $width . 'px;';
            if ( ! empty( $height ) ) {
                $css .= 'min-height: ' . $height . 'px;';
            }
            $css .= '}';
        }

        if ( $css != '' ) {
            $item_output .= '<style>';
            $item_output .= $css;
            $item_output .= '</style>';
        }
    }

    /**
     * Filters a menu item's starting output.
     *
     * The menu item's starting output only includes `$args->before`, the opening `<a>`,
     * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
     * no filter for modifying the opening and closing `<li>` for a menu item.
     *
     * @since 3.0.0
     *
     * @param string   $item_output The menu item's starting HTML output.
     * @param WP_Post  $item        Menu item data object.
     * @param int      $depth       Depth of menu item. Used for padding.
     * @param stdClass $args        An object of wp_nav_menu() arguments.
     */
    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
}

}

new lazyMenuNav();

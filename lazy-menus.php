<?php
/**
 * Plugin Name: Lazy Menu Blocks 😴
 * Version: 1.0.0
 * Plugin URI: http://www.ryntab.com
 * Description: Lazy Load Flatsome UX Blocks as Mega Menu Items, save bandwith with style.
 * Author: Ryan Taber
 * Author URI: http://www.ryntab.com
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * @package Lazy Menus
 * @author Ryan Taber
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class lazyMenu
{
    public function get_ux_block ($blockID)
    {
        $data['block'] = flatsome_apply_shortcode( 'block', array( 'id' => $blockID) );
        return $data;
    }

    public function lazyMenuAPI($slug)
    {
        return lazyMenu::get_ux_block(intval($slug['id']));
    }

    public static function init()
    {
        add_action('rest_api_init', array(get_called_class(), 'set_endpoint'));
        add_action('nav_menu_link_attributes', array(get_called_class(), 'menu_block_data'), 10, 3 );
        add_action('wp_enqueue_scripts', array(get_called_class(), 'inline_control_script'));
        add_action('wp_rest_cache/allowed_endpoints', array(get_called_class(), 'lazyMenu_endpoint_cache'), 10, 1);
    }

    public static function set_endpoint()
    {
        register_rest_route(
            'lazyMenu/UX','block/(?P<id>[a-zA-Z0-9-]+)',
            array(
                'method' => 'GET',
                'callback' => __CLASS__ . '::lazyMenuAPI'
            )
        );
    }

    public static function menu_block_data($atts, $item, $args)
    {
        $atts['data-block'] = get_post_meta( $item->ID, '_menu_item_block', true );
        return $atts;
    }

    public static function menu_output_filter($menu_obj, $menu)
    {
        var_dump($args);

        return $item_output;
    }

    function lazyMenu_endpoint_cache($allowed_endpoints){
        if (!isset($allowed_endpoints['lazyMenu/UX']) || !in_array('block', $allowed_endpoints['lazyMenu/UX'])) {
            $allowed_endpoints[ 'lazyMenu/UX' ][] = 'block';
        }
        return $allowed_endpoints;
    }


    public static function inline_control_script(){
        wp_enqueue_script( 'Menu-Actions', plugin_dir_url( __FILE__ ) . 'assets/js/actions.js', 'jquery');
    }

}

add_action('after_setup_theme', 'lazy_Flatsome_Nav_Dropdown', 20);
function lazy_Flatsome_Nav_Dropdown(){
    if ( 'Flatsome' == wp_get_theme()->name || 'Flatsome' == wp_get_theme()->parent_theme ) {
        lazyMenu::init();

        require 'nav-walker.php';
    }
};

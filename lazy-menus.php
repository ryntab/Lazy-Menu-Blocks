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
    function __construct() {
        add_filter('wp_nav_menu_args', array(get_called_class(), 'set_Walker' ));
        add_action('nav_menu_link_attributes', array(get_called_class(), 'menu_block_data'), 10, 3 );
        add_action('wp_enqueue_scripts', array(get_called_class(), 'inline_control_script'));
        add_action('rest_api_init', array(get_called_class(), 'set_endpoint'));
        add_action('wp_rest_cache/allowed_endpoints', array(get_called_class(), 'set_endpoint_cache'), 10, 1);
        add_filter('init', array(get_called_class(), 'flatsome_lazy_menu_settings'));
    }


    public static function get_wp_menus()
    {
        $menusArray = array();
        $menus = get_terms('nav_menu');
        foreach ($menus as $menu) {
            array_push($menusArray, $menu->name);
        }
        return $menusArray;
    }


    public static function set_Walker($args)
    {
        $lazyMenus = get_theme_mod('lazy_menus');
        $menus = lazyMenu::get_wp_menus();
        foreach ($lazyMenus as $lazyMenu) {
            if ($lazyMenu = $menus[$lazyMenu]) {
                $args['walker'] = new lazyMenuNav();
            }
        }
        return $args; 
    }
    
    public function lazyMenuAPI($slug)
    {
        return lazyMenu::get_ux_block(intval($slug['id']));
    }

    
    public function get_ux_block ($blockID)
    {
        $data['block'] = flatsome_apply_shortcode( 'block', array( 'id' => $blockID) );
        return $data;
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

    function set_endpoint_cache($allowed_endpoints){
        if (!isset($allowed_endpoints['lazyMenu/UX']) || !in_array('block', $allowed_endpoints['lazyMenu/UX'])) {
            $allowed_endpoints[ 'lazyMenu/UX' ][] = 'block';
        }
        return $allowed_endpoints;
    }

    public static function inline_control_script(){
        $type = get_theme_mod('lazy_js_type');
        $format = get_theme_mod('lazy_js_format');
        $script = $type  ? 'actions' : 'actions.vanilla';
        $scriptformat = $format ? '.min' : '';
        wp_enqueue_script( 'Menu-Actions', plugin_dir_url( __FILE__ ) . 'assets/js/'.$script . $scriptformat.'.js',  array('jquery'));
    }


    public function flatsome_lazy_menu_settings()
    {
        if (current_user_can('manage_options')) {
            if (is_customize_preview()) {
                include_once(dirname(__FILE__) . '/inc/settings/options-flatsome-lazy-menus.php');
            }
        }
    }

}

new lazyMenu();

add_action('after_setup_theme', function(){
    if ( 'Flatsome' == wp_get_theme()->name || 'Flatsome' == wp_get_theme()->parent_theme ) {
        require 'lazy-walker.php';
    }
});

<?php

/*************
 * - lazy-menu
 *************/

Flatsome_Option::add_section( 'lazy_menu', array(
	'title'       => __( 'Lazy Menus 😴', 'flatsome-admin' ),
	'panel'       => 'header',
	'description' => __( 'Display the lazy at your store location.', 'flatsome-admin' ),
) );

Flatsome_Option::add_field( 'option',  array(
	'type'        => 'select',
	'settings'    => 'lazy_menus',
	'transport'   => flatsome_customizer_transport(),
	'label'       => __( 'Enable Lazy Menus', 'flatsome-admin' ),
	'description' => __( 'To make a menu lazy select it from the drop down below. A menu will be lazy regardless of its position in the Flatsome or Flatsome child theme.', 'flatsome-admin' ),
	'section'     => 'lazy_menu',
	'multiple'    => 999,
	'choices'     => lazyMenu::get_wp_menus(),
));


Flatsome_Option::add_field( 'option',  array(
	'type'        => 'switch',
	'settings'    => 'lazy_js_type',
	'label'       => esc_html__( 'Javascript Type.', 'kirki' ),
	'description' => __( 'If you are not using Jquery in your Flatsome theme you can enable a no-dependecy version of this plugin.', 'flatsome-admin' ),
	'section'     => 'lazy_menu',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_html__( 'Jquery', 'kirki' ),
		'off' => esc_html__( 'Vanilla', 'kirki' ),
	],
));


Flatsome_Option::add_field( 'option',  array(
	'type'        => 'switch',
	'settings'    => 'lazy_js_format',
	'label'       => esc_html__( 'Javascript Format.', 'kirki' ),
	'description' => __( 'You can load a minified version of this script or a non-minified version. In most cases it is recomended to always load the minified javascript files.', 'flatsome-admin' ),
	'section'     => 'lazy_menu',
	'default'     => 'on',
	'priority'    => 10,
	'choices'     => [
		'on'  => esc_html__( 'Minify', 'kirki' ),
		'off' => esc_html__( 'Non-minify', 'kirki' ),
	],
));
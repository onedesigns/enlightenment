<?php

function enlightenment_elementor_styles() {
	wp_enqueue_style( 'enlightenment-elementor', get_theme_file_uri( 'assets/css/elementor.css' ), array( 'elementor-frontend' ), null );
}
add_action( 'wp_enqueue_scripts', 'enlightenment_elementor_styles', 30 );

function enlightenment_override_elementor_styles() {
	$suffix = wp_scripts_get_suffix();

	wp_deregister_style( 'font-awesome' );
	wp_register_style( 'font-awesome', enlightenment_styles_directory_uri() . "/fontawesome{$suffix}.css", false, null );
}
add_action( 'wp_enqueue_scripts', 'enlightenment_override_elementor_styles', 1000000 );

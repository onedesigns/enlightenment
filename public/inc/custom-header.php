<?php
/**
 * Sample implementation of the Custom Header feature
 *
 * You can add an optional custom header image to header.php like so ...
 *
 * <?php the_header_image_tag(); ?>
 *
 * @link https://developer.wordpress.org/themes/functionality/custom-headers/
 *
 * @package Enlightenment_Framework
 * @subpackage Enlightenment_Theme
 */

/**
 * Set up the WordPress core custom header feature.
 */
function enlightenment_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'enlightenment_custom_header_args', array(
		'default-image'      => '',
		'default-text-color' => '333333',
		'width'              => 1440,
		'height'             => 340,
		'flex-width'         => true,
		'flex-height'        => true,
		'video'              => true,
	) ) );
}
add_action( 'after_setup_theme', 'enlightenment_custom_header_setup' );

function enlightenment_theme_post_header_image( $url ) {
	if (
		( is_singular( 'post' ) || is_page() ) &&
		has_post_thumbnail() &&
		! in_array( get_theme_mod( 'single_post_thumbnail' ), array( 'large', 'cover', 'full-screen' ) )
	) {
		$url = false;
	}

	return $url;
}
add_filter( 'theme_mod_header_image', 'enlightenment_theme_post_header_image', 12 );

/**
 * Styles the header image displayed on the blog.
 */
function enlightenment_custom_header_image_overlay( $css ) {
    if ( ! has_header_image() ) {
		return $css;
	}

	$header_image = get_header_image();

	/*
	 * If no custom options for header image are set, let's bail.
	 * get_header_image() options: Any hex value, 'blank' to hide text. Default: add_theme_support( 'custom-header' ).
	 */
	if ( get_theme_support( 'custom-header', 'default-image' ) === $header_image ) {
		return $css;
	}

	// If we get this far, we have custom styles. Let's do this.
	$css .= sprintf( "\n:root {\n\t--enlightenment-custom-header-image: url(%s);\n}\n", $header_image );

	$defaults = enlightenment_default_theme_mods();
	$bg_color = get_theme_mod( 'header_overlay_color' );

	if ( $bg_color != $defaults['header_overlay_color'] ) {
		$css .= sprintf( "\n:root {\n\t--enlightenment-custom-header-bg: %s;\n}\n", $bg_color );
	}

    return $css;
}
add_filter( 'enlightenment_theme_custom_css', 'enlightenment_custom_header_image_overlay' );

/**
 * Styles the header text displayed on the blog.
 */
function enlightenment_custom_header_textcolor( $css ) {
	$header_text_color = get_header_textcolor();

	/*
	 * If no custom options for text are set, let's bail.
	 * get_header_textcolor() options: Any hex value, 'blank' to hide text. Default: add_theme_support( 'custom-header' ).
	 */
	if ( get_theme_support( 'custom-header', 'default-text-color' ) === $header_text_color ) {
		return $css;
	}

	// If we get this far, we have custom styles. Let's do this.
	// Has the text been hidden?
	if ( ! display_header_text() ) {
		$css .= "
		.site-title,
		.site-description {
			position: absolute;
			clip: rect(1px, 1px, 1px, 1px);
		}";

	// If the user has set a custom color for the text use that.
    } else {
		$css .= "
		.site-title a,
		.site-description {
			color: #<?php echo esc_attr( $header_text_color ); ?>;
		}";
	}

    return $css;
}
add_filter( 'enlightenment_theme_custom_css', 'enlightenment_custom_header_textcolor' );

function enlightenment_custom_header_container() {
    if ( ! has_header_image() ) {
		return;
	}

	global $wp_query;

	if ( 'ce-list-route' == $wp_query->get( 'WP_Route' ) ) {
		return;
	}

	$has_action = has_action( 'enlightenment_page_header' );

    if ( is_singular() && ! $has_action ) {
		return;
	}

	// If we get this far, we have a header image. Let's do this.
	if ( $has_action ) {
		add_action( 'enlightenment_page_header', 'enlightenment_custom_header_markup', 6 );
	    add_action( 'enlightenment_page_header', 'enlightenment_open_container', 8 );
	    add_action( 'enlightenment_page_header', 'enlightenment_close_container', 12 );
	} else {
		add_action( 'enlightenment_after_page_header', 'enlightenment_custom_header_markup', 2 );
	}
}
add_action( 'enlightenment_before_page_header', 'enlightenment_custom_header_container', 999 );

function enlightenment_filter_custom_header_markup_args( $args ) {
	$args['container_class'] .= sprintf( ' header-image-position-%s', esc_attr( get_theme_mod( 'header_image_position' ) ) );

	return $args;
}
add_filter( 'enlightenment_custom_header_markup_args', 'enlightenment_filter_custom_header_markup_args' );

function enlightenment_filter_header_video_settings( $args ) {
	$args['l10n']['pause'] = sprintf( '<i class="fas fa-pause"></i> <span class="screen-reader-text visually-hidden">%s</span>', $args['l10n']['pause'] );
	$args['l10n']['play']  = sprintf( '<i class="fas fa-play"></i> <span class="screen-reader-text visually-hidden">%s</span>', $args['l10n']['play'] );
	return $args;
}
add_filter( 'header_video_settings', 'enlightenment_filter_header_video_settings' );

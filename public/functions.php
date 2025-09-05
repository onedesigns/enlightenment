<?php
/**
 * Enlightenment functions and definitions
 *
 * @package Enlightenment_Framework
 * @subpackage Enlightenment_Theme
 */

/**
 * Initialize Enlightenment Framework.
 *
 * Loading the framework with require_once allows child themes to load
 * the framework functions and modules independently and have them
 * readily available for use and customization.
 */
require_once( get_template_directory() . '/core/init.php' );

if ( ! function_exists( 'enlightenment_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function enlightenment_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on _s, use a find and replace
	 * to change '_s' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'enlightenment', get_template_directory() . '/languages' );

	// Set up the Site Logo feature
	add_theme_support( 'custom-logo', array(
		'width'  => 80,
		'height' => 80,
		'flex-height' => true,
		'flex-width'  => true,
		'header-text' => array( 'site-title', 'site-description' ),
	) );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	/**
	 * Add support for Post Formats
	 */
	add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ) );

	// Enable support for wide and full width image alignment in Gutenberg
	add_theme_support( 'align-wide' );

	// Enable custom styling of the block editor
	if ( enlightenment_is_gutenberg_active() ) {
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/editor-style.css' );
	}

	// Show featured images as cover photos on single posts.
	add_theme_support( 'enlightenment-post-thumbnail-header', array(
		'post_types' => array( 'post', 'page' ),
	) );

	// Set default post thumbnail size
	add_image_size( 'enlightenment-post-thumb', 1440, 960, 1 );

	// Set teaser post thumbnail size
	add_image_size( 'enlightenment-teaser-thumb', 720, 480, 1 );

	// Extra Navigation Menus
	register_nav_menus( array(
		'blog'   => __( 'Blog',   'enlightenment' ),
		'footer' => __( 'Footer', 'enlightenment' ),
	) );

	// Set up the Customizer API feature
	add_theme_support( 'enlightenment-customizer-api' );

	// Set up the Google Fonts feature
	add_theme_support( 'enlightenment-web-fonts', array(
		'provider'       => 'bunny-fonts',
		'host_locally'   => true,
		'theme_defaults' => array(
			'Hind'     => array( '300', '400', '500', '600', '700' ),
			'PT Serif' => array( '400', 'italic', '700' ),
		),
	) );

	// Set up the Accessibility feature
	add_theme_support( 'enlightenment-accessibility' );

	// Set up the Bootstrap Framework feature
	add_theme_support( 'enlightenment-bootstrap', array(
		'color-mode'        => get_theme_mod( 'color_mode' ),
		'navbar-position'   => get_theme_mod( 'site_header_position' ),
		'navbar-expand'     => get_theme_mod( 'navbar_expand' ),
		'navbar-color'      => get_theme_mod( 'site_header_style' ),
		'navbar-background' => get_theme_mod( 'site_header_style' ),
	) );

	// Set up the Schema Markup feature
	add_theme_support( 'enlightenment-schema-markup' );

	// Set up the Menu Icons feature
	add_theme_support( 'enlightenment-menu-icons' );

	// Set up the Menu Description feature
	add_theme_support( 'enlightenment-menu-descriptions' );

	// Set up the Lightbox feature
	add_theme_support( 'enlightenment-lightbox', array(
		'script'      => 'fluidbox',
		'script_args' => array(
			'selector'   => 'a[href$=".jpg"], a[href$=".jpeg"], a[href$=".png"], a[href$=".gif"], a[href$=".webp"]',
			'stackIndex' => 1019,
		),
	) );

	if ( 'infinite' == get_theme_mod( 'posts_nav_style' ) ) {
		// Set up the Infinite Scroll feature
		add_theme_support( 'enlightenment-infinite-scroll', array(
			'loading' => array(
				'spinner'      => '<i class="fa fa-spinner fa-pulse"></i>',
				'msgTextClass' => 'infscr-loading__text screen-reader-text visually-hidden',
				'speed'        => 0,
			),
		) );
	} elseif ( 'ajax' == get_theme_mod( 'posts_nav_style' ) ) {
		add_theme_support( 'enlightenment-ajax-navigation', array(
			'spinner'      => '<i class="fa fa-spinner fa-pulse"></i>',
			'loadingClass' => 'ajax-navigation__loading screen-reader-text visually-hidden',
		) );
	}

	// Set up the Custom Layouts feature
	add_theme_support( 'enlightenment-custom-layouts', array(
		'content-sidebar',
		'sidebar-content',
		'full-width',
	) );

	// Set up the Grid Loop feature
	add_theme_support( 'enlightenment-grid-loop', array(
		'masonry' => get_theme_mod( 'masonry' ),
	) );

	// Set up the Unlimited Sidebars feature
	add_theme_support( 'enlightenment-unlimited-sidebars', array(
		'custom_sidebar_background' => true,
		'custom_widgets_background' => true,
		'sidebar_title_color'       => '#333333',
		'sidebar_text_color'        => '#333333',
		'widgets_title_color'       => '#333333',
		'widgets_text_color'        => '#333333',
		'widgets_link_color'        => '#0d6efd',
		'widgets_link_hover_color'  => '#0a58ca',
		'widgets_link_active_color' => '#0a58ca',
	) );

	// Set up the Template Editor feature
	// add_theme_support( 'enlightenment-template-editor' );

	// Set up the Custom Queries feature
	add_theme_support( 'enlightenment-custom-queries' );

	// Add support for Jetpack plugin
	add_theme_support( 'enlightenment-jetpack' );

	// Add support for WooCommerce plugin
	add_theme_support( 'enlightenment-woocommerce', array(
        'product_gallery_zoom'          => true,
    	'product_gallery_lightbox'      => true,
    	'product_gallery_slider'        => true,
		'thumbnail_image_width'         => 500,
		'gallery_thumbnail_image_width' => 150,
		'single_image_width'            => 720,
	) );

	// Add support for The Events Calendar plugin
	add_theme_support( 'enlightenment-events-calendar' );

	// Add support for BuddyPress plugin
	add_theme_support( 'enlightenment-buddypress' );

	// Add support for bbPress plugin
	add_theme_support( 'enlightenment-bbpress' );
}
endif; // enlightenment_setup
add_action( 'after_setup_theme', 'enlightenment_setup' );

function enlightenment_classic_editor_style() {
	if ( enlightenment_is_gutenberg_active() ) {
		return;
	}

	$suffix = wp_scripts_get_suffix();

	// Enable custom styling of the classic editor
	$editor_styles     = array();
	$editor_styles[-2] = "core/assets/css/bootstrap{$suffix}.css";
	$editor_styles[-1] = str_replace( ',', '%2C', enlightenment_get_web_fonts_stylesheet_uri() );
	$editor_styles[0]  = 'assets/css/editor-style.css';

	add_editor_style( $editor_styles );
}
add_action( 'admin_init', 'enlightenment_classic_editor_style' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function enlightenment_content_width() {
	enlightenment_set_content_width( apply_filters( 'enlightenment_content_width', 720 ) );
}
add_action( 'after_setup_theme', 'enlightenment_content_width', 0 );

/**
 * Enqueue scripts and styles.
 */
function enlightenment_scripts() {
	wp_enqueue_style( 'font-awesome' );

	// Main theme script. Calls all theme dependent functions.
	$deps = apply_filters( 'enlightenment_main_script_deps', array( 'jquery', 'bootstrap', 'gemini-scrollbar' ) );
	wp_enqueue_script( 'enlightenment-main', get_theme_file_uri( 'assets/js/main.js' ), $deps, null, true );

	wp_localize_script( 'enlightenment-main', 'enlightenment_main_args', apply_filters( 'enlightenment_main_script_args', array(
		'color_mode'          => esc_attr( enlightenment_bootstrap_get_current_color_mode() ),
		'navbar_color'        => esc_attr( current_theme_supports( 'enlightenment-bootstrap', 'navbar-color' ) ),
		'navbar_nav_separate' => get_theme_mod( 'navbar_nav_separate' ),
	) ) );
}
add_action( 'wp_enqueue_scripts', 'enlightenment_scripts' );

function enlightenment_call_js( $deps ) {
	$deps[] = 'fitvids';

	return $deps;
}
add_filter( 'enlightenment_call_js', 'enlightenment_call_js' );

/**
 * Implement the Custom Header feature.
 */
require_once get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require_once get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require_once get_template_directory() . '/inc/extras.php';

/**
 * Typography options and settings.
 */
require_once get_template_directory() . '/inc/typography.php';

/**
 * Customizer additions.
 */
require_once get_template_directory() . '/inc/customizer.php';

/**
 * Load Gutenberg compatibility file.
 */
if ( enlightenment_is_gutenberg_active() ) {
	require_once get_template_directory() . '/inc/gutenberg.php';
}

/**
 * Load Jetpack compatibility file.
 */
if ( class_exists( 'Jetpack' ) ) {
	require_once get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load Elementor compatibility file.
 */
if ( class_exists( 'Elementor\Plugin' ) ) {
	require_once get_template_directory() . '/inc/elementor.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require_once get_template_directory() . '/inc/woocommerce.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'Tribe__Events__Main' ) ) {
	require_once get_template_directory() . '/inc/events-calendar.php';
}

/**
 * Load bbPress compatibility file.
 */
if ( class_exists( 'bbPress' ) ) {
	require_once get_template_directory() . '/inc/bbpress.php';
}

/**
 * Load BuddyPress compatibility file.
 */
if ( class_exists( 'BuddyPress' ) ) {
	require_once get_template_directory() . '/inc/buddypress.php';
}

/**
 * Load Meta Boxes file.
 */
if ( is_admin() ) {
	require_once get_template_directory() . '/inc/meta-boxes.php';
	require_once get_template_directory() . '/inc/term-meta.php';
}

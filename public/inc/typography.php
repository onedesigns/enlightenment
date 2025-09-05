<?php

function enlightenment_theme_default_typography_theme_mods( $mods ) {
	return array_merge( $mods, array(
		'default_typography'             => array(
			'font_color'     => '',
			'font_family'    => 'Hind',
			'font_size'      => '1rem',
			'font_variant'   => 400,
			'line_height'    => 1.5,
			'text_align'     => 'start',
		),
		'accent_color'                   => '',
		'link_color'                     => '',
		'link_hover_color'               => '',
		'link_color_dark'                => '',
		'link_hover_color_dark'          => '',
		'navbar_brand_color'             => '',
		'navbar_brand_hover_color'       => '',
		'navbar_brand_active_color'      => '',
		'navbar_brand_typography'        => array(
			'font_size'      => '1.25rem',
			'font_variant'   => 500,
		),
		'menu_items_color'               => '',
		'menu_items_hover_color'         => '',
		'menu_items_active_color'        => '',
		'navbar_collapse_typography'     => array(
			'font_size'      => '1rem',
			'font_variant'   => 400,
			'text_transform' => 'none',
		),
		'navbar_expand_typography'       => array(
			'font_size'      => '0.875rem',
			'font_variant'   => 400,
			'text_transform' => 'none',
		),
		'navbar_collapse_active_link'    => array(
			'font_variant'   => 600,
		),
		'navbar_expand_active_link'      => array(
			'font_variant'   => 600,
		),
		'header_icons_color'             => '',
		'header_icons_hover_color'       => '',
		'header_icons_active_color'      => '',
		'page_title_typography'          => array(
			'font_color'     => '',
			'font_size'      => '2.75rem',
		),
		'page_title_desktop_typography'  => array(
			'font_size'      => '3rem',
		),
		'page_title_author_typography'  => array(
			'font_size'      => '2rem',
		),
		'page_description_typography'    => array(
			'font_color'     => '',
			'font_size'      => '1.25rem',
			'font_variant'   => 300,
		),
		'single_post_title_typography'  => array(
			'font_size'      => '2rem',
			'line_height'    => 1.2,
		),
		'single_post_title_desktop_typography'  => array(
			'font_size'      => '3rem',
		),
		'single_post_excerpt_typography'  => array(
			'font_size'      => '1.25rem',
			'font_variant'   => 300,
		),
		'single_post_excerpt_desktop_typography'  => array(
			'font_size'      => '1.5rem',
		),
		'single_post_content_typography'  => array(
			'font_family'    => 'PT Serif',
			'font_size'      => '1.125rem',
			'line_height'    => 1.6,
		),
		'single_post_content_desktop_typography'  => array(
			'font_size'      => '1.25rem',
		),
		'breadcrumbs_color'              => '',
		'post_title_color'               => '',
		'post_meta_color'                => '',
		'post_excerpt_color'             => '',
		'post_content_color'             => '',
		'headings_color'                 => '',
		'comments_title_color'           => '',
		'commenter_name_color'           => '',
		'comment_meta_color'             => '',
		'comment_content_color'          => '',
		'reply_title_color'              => '',
		'comment_form_labels_color'      => '',
		'widgets_title_color'            => '',
		'widgets_content_color'          => '',
		'site_footer_color'              => '',
	) );
}
add_filter( 'enlightenment_default_theme_mods', 'enlightenment_theme_default_typography_theme_mods' );

function enlightenment_theme_enqueue_web_fonts() {
	enlightenment_enqueue_theme_options_font( 'default_typography' );
}
add_action( 'enlightenment_enqueue_fonts', 'enlightenment_theme_enqueue_web_fonts' );

function enlightenment_theme_custom_typography_css( $output ) {
	$output .= enlightenment_print_typography_options( array(
		'css_variable' => 'enlightenment-body',
		'option'       => 'default_typography',
		'echo'         => false,
	) );

	$output .= enlightenment_print_color_option( array(
		'css_variable' => 'enlightenment-light-link-color-rgb',
		'option'       => 'link_color',
		'format'       => 'rgb',
		'rgb_wrapper'  => false,
		'echo'         => false,
	) );

	$output .= enlightenment_print_color_option( array(
		'css_variable' => 'enlightenment-light-link-hover-color-rgb',
		'option'       => 'link_hover_color',
		'format'       => 'rgb',
		'rgb_wrapper'  => false,
		'echo'         => false,
	) );

	$output .= enlightenment_print_color_option( array(
		'css_variable' => 'enlightenment-dark-link-color-rgb',
		'option'       => 'link_color_dark',
		'format'       => 'rgb',
		'rgb_wrapper'  => false,
		'echo'         => false,
	) );

	$output .= enlightenment_print_color_option( array(
		'css_variable' => 'enlightenment-dark-link-hover-color-rgb',
		'option'       => 'link_hover_color_dark',
		'format'       => 'rgb',
		'rgb_wrapper'  => false,
		'echo'         => false,
	) );

	$output .= enlightenment_print_color_option( array(
		'css_variable' => 'enlightenment-navbar-brand-color',
		'option'       => 'navbar_brand_color',
		'echo'         => false,
	) );

	$output .= enlightenment_print_color_option( array(
		'css_variable' => 'enlightenment-navbar-brand-hover-color',
		'option'       => 'navbar_brand_hover_color',
		'echo'         => false,
	) );

	$output .= enlightenment_print_color_option( array(
		'css_variable' => 'enlightenment-navbar-brand-active-color',
		'option'       => 'navbar_brand_active_color',
		'echo'         => false,
	) );

	$output .= enlightenment_print_typography_options( array(
		'css_variable' => 'enlightenment-navbar-brand',
		'option'       => 'navbar_brand_typography',
		'echo'         => false,
	) );

	$output .= enlightenment_print_color_option( array(
		'css_variable' => 'enlightenment-navbar-nav-link-color',
		'option'       => 'menu_items_color',
		'echo'         => false,
	) );

	$output .= enlightenment_print_color_option( array(
		'css_variable' => 'enlightenment-navbar-nav-link-hover-color',
		'option'       => 'menu_items_hover_color',
		'echo'         => false,
	) );

	$output .= enlightenment_print_color_option( array(
		'css_variable' => 'enlightenment-navbar-nav-link-active-color',
		'option'       => 'menu_items_active_color',
		'echo'         => false,
	) );

	$output .= enlightenment_print_typography_options( array(
		'css_variable' => 'enlightenment-navbar-collapse',
		'option'       => 'navbar_collapse_typography',
		'echo'         => false,
	) );

	$output .= enlightenment_print_typography_options( array(
		'css_variable' => 'enlightenment-navbar-expand',
		'option'       => 'navbar_expand_typography',
		'echo'         => false,
	) );

	$output .= enlightenment_print_typography_options( array(
		'css_variable' => 'enlightenment-navbar-collapse-active-link',
		'option'       => 'navbar_collapse_active_link',
		'echo'         => false,
	) );

	$output .= enlightenment_print_typography_options( array(
		'css_variable' => 'enlightenment-navbar-expand-active-link',
		'option'       => 'navbar_expand_active_link',
		'echo'         => false,
	) );

	$output .= enlightenment_print_color_option( array(
		'css_variable' => 'enlightenment-navbar-nav-icons-color',
		'option'       => 'header_icons_color',
		'echo'         => false,
	) );

	$output .= enlightenment_print_color_option( array(
		'css_variable' => 'enlightenment-navbar-nav-icons-hover-color',
		'option'       => 'header_icons_hover_color',
		'echo'         => false,
	) );

	$output .= enlightenment_print_color_option( array(
		'css_variable' => 'enlightenment-navbar-nav-icons-active-color',
		'option'       => 'header_icons_active_color',
		'echo'         => false,
	) );

	$output .= enlightenment_print_typography_options( array(
		'css_variable' => 'enlightenment-page-title',
		'option'       => 'page_title_typography',
		'echo'         => false,
	) );

	$output .= enlightenment_print_typography_options( array(
		'css_variable' => 'enlightenment-desktop-page-title',
		'option'       => 'page_title_desktop_typography',
		'echo'         => false,
	) );

	$output .= enlightenment_print_typography_options( array(
		'css_variable' => 'enlightenment-author-page-title',
		'option'       => 'page_title_author_typography',
		'echo'         => false,
	) );

	$output .= enlightenment_print_typography_options( array(
		'css_variable' => 'enlightenment-page-description',
		'option'       => 'page_description_typography',
		'echo'         => false,
	) );

	$output .= enlightenment_print_typography_options( array(
		'css_variable' => 'enlightenment-single-post-title',
		'option'       => 'single_post_title_typography',
		'echo'         => false,
	) );

	$output .= enlightenment_print_typography_options( array(
		'css_variable' => 'enlightenment-desktop-single-post-title',
		'option'       => 'single_post_title_desktop_typography',
		'echo'         => false,
	) );

	$output .= enlightenment_print_typography_options( array(
		'css_variable' => 'enlightenment-single-post-excerpt',
		'option'       => 'single_post_excerpt_typography',
		'echo'         => false,
	) );

	$output .= enlightenment_print_typography_options( array(
		'css_variable' => 'enlightenment-desktop-single-post-excerpt',
		'option'       => 'single_post_excerpt_desktop_typography',
		'echo'         => false,
	) );

	$output .= enlightenment_print_typography_options( array(
		'css_variable' => 'enlightenment-single-post-content',
		'option'       => 'single_post_content_typography',
		'echo'         => false,
	) );

	$output .= enlightenment_print_typography_options( array(
		'css_variable' => 'enlightenment-desktop-single-post-content',
		'option'       => 'single_post_content_desktop_typography',
		'echo'         => false,
	) );

	return $output;
}
add_filter( 'enlightenment_theme_custom_css', 'enlightenment_theme_custom_typography_css' );

function enlightenment_custom_typography_editor_styles() {
	$output  = '';

	$output .= enlightenment_print_color_option( array(
		'css_variable' => 'enlightenment-light-link-color-rgb',
		'option'       => 'link_color',
		'format'       => 'rgb',
		'rgb_wrapper'  => false,
		'echo'         => false,
	) );

	$output .= enlightenment_print_color_option( array(
		'css_variable' => 'enlightenment-light-link-hover-color-rgb',
		'option'       => 'link_hover_color',
		'format'       => 'rgb',
		'rgb_wrapper'  => false,
		'echo'         => false,
	) );

	$output .= enlightenment_print_color_option( array(
		'css_variable' => 'enlightenment-dark-link-color-rgb',
		'option'       => 'link_color_dark',
		'format'       => 'rgb',
		'rgb_wrapper'  => false,
		'echo'         => false,
	) );

	$output .= enlightenment_print_color_option( array(
		'css_variable' => 'enlightenment-dark-link-hover-color-rgb',
		'option'       => 'link_hover_color_dark',
		'format'       => 'rgb',
		'rgb_wrapper'  => false,
		'echo'         => false,
	) );

	return $output;
}

function enlightenment_custom_typography_tinymce_styles( $mce_init ) {
	$output = enlightenment_custom_typography_editor_styles();

	if ( ! empty( $output ) ) {
		$output  = str_replace( array( "\n", "\t" ), array( '', '' ), $output );
		$output .= ' ';

		if ( ! isset( $mce_init['content_style'] ) ) {
			$mce_init['content_style'] = $output;
		} else {
			$mce_init['content_style'] .= ' ' . $output;
		}
	}

	return $mce_init;
}
add_filter( 'tiny_mce_before_init', 'enlightenment_custom_typography_tinymce_styles' );

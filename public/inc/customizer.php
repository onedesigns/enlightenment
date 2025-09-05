<?php
/**
 * _s Theme Customizer
 *
 * @package _s
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function enlightenment_customize_register( $wp_customize ) {
    $type     = 'theme_mod';
	$cap      = 'edit_theme_options';
    $defaults = enlightenment_default_theme_mods();

	/*$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.site-title a',
			'render_callback' => 'enlightenment_customize_partial_blogname',
		) );

		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.site-description',
			'render_callback' => 'enlightenment_customize_partial_blogdescription',
		) );
	}*/

    $wp_customize->get_control( 'custom_logo' )->label = __( 'Dark Logo', 'enlightenment' );
    $wp_customize->get_control( 'custom_logo' )->description = __( 'Displays when the light navbar option is active or when no light logo is selected.', 'enlightenment' );

    $wp_customize->add_setting( 'custom_logo_alt', array(
        'type'           => $type,
        'capability'     => $cap,
        'theme_supports' => array( 'custom-logo' ),
        'transport'      => 'postMessage',
    ) );

    $custom_logo_args = get_theme_support( 'custom-logo' );
    $wp_customize->add_control( new WP_Customize_Cropped_Image_Control( $wp_customize, 'custom_logo_alt', array(
        'label'         => __( 'Light Logo', 'enlightenment' ),
        'description'   => __( 'Displays when the dark navbar option is active.', 'enlightenment' ),
        'section'       => 'title_tagline',
        'priority'      => 8,
        'height'        => $custom_logo_args[0]['height'],
        'width'         => $custom_logo_args[0]['width'],
        'flex_height'   => $custom_logo_args[0]['flex-height'],
        'flex_width'    => $custom_logo_args[0]['flex-width'],
        'button_labels' => array(
            'select'       => __( 'Select logo',      'enlightenment' ),
            'change'       => __( 'Change logo',      'enlightenment' ),
            'remove'       => __( 'Remove',           'enlightenment' ),
            'default'      => __( 'Default',          'enlightenment' ),
            'placeholder'  => __( 'No logo selected', 'enlightenment' ),
            'frame_title'  => __( 'Select logo',      'enlightenment' ),
            'frame_button' => __( 'Choose logo',      'enlightenment' ),
        ),
    ) ) );

    $wp_customize->selective_refresh->add_partial( 'custom_logo_alt', array(
        'settings'            => array( 'custom_logo_alt' ),
        'selector'            => '.custom-logo-link',
        'render_callback'     => 'get_custom_logo',
        'container_inclusive' => true,
    ) );

    $wp_customize->remove_control( 'display_header_text' );

    $wp_customize->add_setting( 'hide_navbar_brand_on', array(
        'type'           => $type,
        'capability'     => $cap,
        'default'        => $defaults['hide_navbar_brand_on'],
        'theme_supports' => array( 'custom-logo' ),
        'transport'      => 'postMessage',
    ) );

    $wp_customize->add_control( new Enlightenment_Customize_Multiple_Checkboxes_Control( $wp_customize, 'hide_navbar_brand_on', array(
        'section'         => 'title_tagline',
		'label'           => __( 'Hide site title on', 'enlightenment' ),
        'description'     => __( 'The site title is always visible if no logo has been set.', 'enlightenment' ),
		'choices'         => array(
			'smartphone-portrait'  => __( 'Smartphone Portrait',  'enlightenment' ),
			'smartphone-landscape' => __( 'Smartphone Landscape', 'enlightenment' ),
			'tablet-portrait'      => __( 'Tablet Portrait',      'enlightenment' ),
			'tablet-landscape'     => __( 'Tablet Landscape',     'enlightenment' ),
			'desktop-laptop'       => __( 'Desktop Laptop',       'enlightenment' ),
		),
    ) ) );

    $wp_customize->add_setting( 'color_mode', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['color_mode'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control(
        'color_mode',
        array(
			'priority' => 8,
            'section'  => 'colors',
            'type'     => 'select',
            'label'    => __( 'Color Mode', 'enlightenment' ),
            'choices'  => array(
                'light' => _x( 'Light', 'Color Mode', 'enlightenment' ),
                'dark'  => _x( 'Dark',  'Color Mode', 'enlightenment' ),
                'auto'  => _x( 'Auto',  'Color Mode', 'enlightenment' ),
            ),
        )
    );

	$wp_customize->add_setting( 'accent_color', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['accent_color'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Enlightenment_Customize_Alpha_Color_Control( $wp_customize, 'accent_color', array(
        'section' => 'colors',
        'label'   => __( 'Accent Color', 'enlightenment' ),
    ) ) );

	$wp_customize->add_setting( 'link_color', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['link_color'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_color', array(
        'section' => 'colors',
        'label'   => __( 'Link Color', 'enlightenment' ),
    ) ) );

	$wp_customize->add_setting( 'link_hover_color', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['link_hover_color'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_hover_color', array(
        'section' => 'colors',
        'label'   => __( 'Link Hover Color', 'enlightenment' ),
    ) ) );

	$wp_customize->add_setting( 'link_color_dark', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['link_color_dark'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_color_dark', array(
        'section' => 'colors',
        'label'   => __( 'Dark Link Color', 'enlightenment' ),
    ) ) );

	$wp_customize->add_setting( 'link_hover_color_dark', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['link_hover_color_dark'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_hover_color_dark', array(
        'section' => 'colors',
        'label'   => __( 'Dark Link Hover Color', 'enlightenment' ),
    ) ) );

	$wp_customize->add_panel( 'typography', array(
		'title'    => __( 'Typography', 'enlightenment' ),
		'priority' => 48,
	) );

	enlightenment_customize_add_typography_section( $wp_customize, array(
		'id'          => 'default_typography',
		'panel'       => 'typography',
		'title'       => _x( 'Defaults', 'Typography Options', 'enlightenment' ),
		'description' => sprintf( __( 'Styles applied to the %s tag and inherited by most elements.', 'enlightenment' ), '<code>&lt;body&gt;</code>' ),
		'default'     => $defaults['default_typography'],
		'transport'   => 'postMessage',
	) );

    $wp_customize->add_section( 'navbar_brand_typography', array(
		'panel' => 'typography',
        'title' => __( 'Site Title', 'enlightenment' ),
    ) );

	$wp_customize->add_setting( 'navbar_brand_color', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['navbar_brand_color'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Enlightenment_Customize_Alpha_Color_Control( $wp_customize, 'navbar_brand_color', array(
        'section'         => 'navbar_brand_typography',
        'label'           => __( 'Color', 'enlightenment' ),
    ) ) );

	$wp_customize->add_setting( 'navbar_brand_hover_color', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['navbar_brand_hover_color'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Enlightenment_Customize_Alpha_Color_Control( $wp_customize, 'navbar_brand_hover_color', array(
        'section'         => 'navbar_brand_typography',
        'label'           => __( 'Hover Color', 'enlightenment' ),
    ) ) );

	$wp_customize->add_setting( 'navbar_brand_active_color', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['navbar_brand_active_color'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Enlightenment_Customize_Alpha_Color_Control( $wp_customize, 'navbar_brand_active_color', array(
        'section'         => 'navbar_brand_typography',
        'label'           => __( 'Active Color', 'enlightenment' ),
    ) ) );
    $wp_customize->add_setting( 'navbar_brand_typography[font_size]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['navbar_brand_typography']['font_size'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

	$wp_customize->add_control( new Enlightenment_Customize_Font_Size_Control( $wp_customize, 'navbar_brand_typography[font_size]', array(
        'section' => 'navbar_brand_typography',
        'label'   => __( 'Font Size', 'enlightenment' ),
    ) ) );

    $wp_customize->add_setting( 'navbar_brand_typography[font_variant]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['navbar_brand_typography']['font_variant'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Enlightenment_Customize_Font_Variant_Control( $wp_customize, 'navbar_brand_typography[font_variant]', array(
        'section' => 'navbar_brand_typography',
        'label'   => __( 'Font Style', 'enlightenment' ),
    ) ) );

	$wp_customize->add_section( 'navbar_collapse_typography', array(
		'panel' => 'typography',
		'title' => __( 'Mobile Navigation', 'enlightenment' ),
	) );

	$wp_customize->add_setting( 'navbar_collapse_typography[font_size]', array(
		'type'              => $type,
		'capability'        => $cap,
		'default'           => $defaults['navbar_collapse_typography']['font_size'],
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( new Enlightenment_Customize_Font_Size_Control( $wp_customize, 'navbar_collapse_typography[font_size]', array(
		'section' => 'navbar_collapse_typography',
		'label'   => __( 'Font Size', 'enlightenment' ),
	) ) );

	$wp_customize->add_setting( 'navbar_collapse_typography[font_variant]', array(
		'type'              => $type,
		'capability'        => $cap,
		'default'           => $defaults['navbar_collapse_typography']['font_variant'],
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( new Enlightenment_Customize_Font_Variant_Control( $wp_customize, 'navbar_collapse_typography[font_variant]', array(
		'section' => 'navbar_collapse_typography',
		'label'   => __( 'Regular Link Font Style', 'enlightenment' ),
	) ) );

	$wp_customize->add_setting( 'navbar_collapse_active_link[font_variant]', array(
		'type'              => $type,
		'capability'        => $cap,
		'default'           => $defaults['navbar_collapse_active_link']['font_variant'],
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( new Enlightenment_Customize_Font_Variant_Control( $wp_customize, 'navbar_collapse_active_link[font_variant]', array(
		'section' => 'navbar_collapse_typography',
		'label'   => __( 'Current Link Font Style', 'enlightenment' ),
	) ) );

	$wp_customize->add_setting( 'navbar_collapse_typography[text_transform]', array(
		'type'              => $type,
		'capability'        => $cap,
		'default'           => $defaults['navbar_collapse_typography']['text_transform'],
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( new Enlightenment_Customize_Text_Transform_Control( $wp_customize, 'navbar_collapse_typography[text_transform]', array(
		'section' => 'navbar_collapse_typography',
		'label'   => __( 'Text Transform', 'enlightenment' ),
	) ) );

	$wp_customize->add_section( 'navbar_expand_typography', array(
		'panel' => 'typography',
		'title' => __( 'Desktop Navigation', 'enlightenment' ),
	) );

	$wp_customize->add_setting( 'navbar_expand_typography[font_size]', array(
		'type'              => $type,
		'capability'        => $cap,
		'default'           => $defaults['navbar_expand_typography']['font_size'],
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( new Enlightenment_Customize_Font_Size_Control( $wp_customize, 'navbar_expand_typography[font_size]', array(
		'section' => 'navbar_expand_typography',
		'label'   => __( 'Font Size', 'enlightenment' ),
	) ) );

	$wp_customize->add_setting( 'navbar_expand_typography[font_variant]', array(
		'type'              => $type,
		'capability'        => $cap,
		'default'           => $defaults['navbar_expand_typography']['font_variant'],
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( new Enlightenment_Customize_Font_Variant_Control( $wp_customize, 'navbar_expand_typography[font_variant]', array(
		'section' => 'navbar_expand_typography',
		'label'   => __( 'Regular Link Font Style', 'enlightenment' ),
	) ) );

	$wp_customize->add_setting( 'navbar_expand_active_link[font_variant]', array(
		'type'              => $type,
		'capability'        => $cap,
		'default'           => $defaults['navbar_expand_active_link']['font_variant'],
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( new Enlightenment_Customize_Font_Variant_Control( $wp_customize, 'navbar_expand_active_link[font_variant]', array(
		'section' => 'navbar_expand_typography',
		'label'   => __( 'Current Link Font Style', 'enlightenment' ),
	) ) );

	$wp_customize->add_setting( 'navbar_expand_typography[text_transform]', array(
		'type'              => $type,
		'capability'        => $cap,
		'default'           => $defaults['navbar_expand_typography']['text_transform'],
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( new Enlightenment_Customize_Text_Transform_Control( $wp_customize, 'navbar_expand_typography[text_transform]', array(
		'section' => 'navbar_expand_typography',
		'label'   => __( 'Text Transform', 'enlightenment' ),
	) ) );

	$wp_customize->add_section( 'navbar_typography', array(
		'panel' => 'typography',
		'title' => __( 'Menu Items', 'enlightenment' ),
	) );

	$wp_customize->add_setting( 'menu_items_color', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['menu_items_color'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Enlightenment_Customize_Alpha_Color_Control( $wp_customize, 'menu_items_color', array(
        'section'         => 'navbar_typography',
        'label'           => __( 'Color', 'enlightenment' ),
    ) ) );

	$wp_customize->add_setting( 'menu_items_hover_color', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['menu_items_hover_color'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Enlightenment_Customize_Alpha_Color_Control( $wp_customize, 'menu_items_hover_color', array(
        'section'         => 'navbar_typography',
        'label'           => __( 'Hover Color', 'enlightenment' ),
    ) ) );

	$wp_customize->add_setting( 'menu_items_active_color', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['menu_items_active_color'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Enlightenment_Customize_Alpha_Color_Control( $wp_customize, 'menu_items_active_color', array(
        'section'         => 'navbar_typography',
        'label'           => __( 'Active Color', 'enlightenment' ),
    ) ) );

	$wp_customize->add_section( 'header_icons', array(
		'panel' => 'typography',
		'title' => __( 'Site Header Icons', 'enlightenment' ),
	) );

	$wp_customize->add_setting( 'header_icons_color', array(
		'type'              => $type,
		'capability'        => $cap,
		'default'           => $defaults['header_icons_color'],
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( new Enlightenment_Customize_Alpha_Color_Control( $wp_customize, 'header_icons_color', array(
		'section'         => 'header_icons',
		'label'           => __( 'Color', 'enlightenment' ),
	) ) );

	$wp_customize->add_setting( 'header_icons_hover_color', array(
		'type'              => $type,
		'capability'        => $cap,
		'default'           => $defaults['header_icons_hover_color'],
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( new Enlightenment_Customize_Alpha_Color_Control( $wp_customize, 'header_icons_hover_color', array(
		'section'         => 'header_icons',
		'label'           => __( 'Hover Color', 'enlightenment' ),
	) ) );

	$wp_customize->add_setting( 'header_icons_active_color', array(
		'type'              => $type,
		'capability'        => $cap,
		'default'           => $defaults['header_icons_active_color'],
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( new Enlightenment_Customize_Alpha_Color_Control( $wp_customize, 'header_icons_active_color', array(
		'section'         => 'header_icons',
		'label'           => __( 'Active Color', 'enlightenment' ),
	) ) );

    $wp_customize->add_section( 'page_header_typography', array(
		'panel' => 'typography',
        'title' => __( 'Page Header', 'enlightenment' ),
    ) );

	$wp_customize->add_setting( 'page_title_typography[font_color]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['page_title_typography']['font_color'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'page_title_typography[font_color]', array(
        'section' => 'page_header_typography',
        'label'   => __( 'Page Title Color', 'enlightenment' ),
    ) ) );

    $wp_customize->add_setting( 'page_title_typography[font_size]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['page_title_typography']['font_size'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

	$wp_customize->add_control( new Enlightenment_Customize_Font_Size_Control( $wp_customize, 'page_title_typography[font_size]', array(
        'section' => 'page_header_typography',
        'label'   => __( 'Mobile Page Title Font Size', 'enlightenment' ),
    ) ) );

    $wp_customize->add_setting( 'page_title_desktop_typography[font_size]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['page_title_desktop_typography']['font_size'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

	$wp_customize->add_control( new Enlightenment_Customize_Font_Size_Control( $wp_customize, 'page_title_desktop_typography[font_size]', array(
        'section' => 'page_header_typography',
        'label'   => __( 'Desktop Page Title Font Size', 'enlightenment' ),
    ) ) );

    $wp_customize->add_setting( 'page_title_author_typography[font_size]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['page_title_author_typography']['font_size'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

	$wp_customize->add_control( new Enlightenment_Customize_Font_Size_Control( $wp_customize, 'page_title_author_typography[font_size]', array(
        'section' => 'page_header_typography',
        'label'   => __( 'Author Page Title Font Size', 'enlightenment' ),
    ) ) );

	$wp_customize->add_setting( 'page_description_typography[font_color]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['page_description_typography']['font_color'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'page_description_typography[font_color]', array(
        'section' => 'page_header_typography',
        'label'   => __( 'Page Description Color', 'enlightenment' ),
    ) ) );

    $wp_customize->add_setting( 'page_description_typography[font_size]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['page_description_typography']['font_size'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

	$wp_customize->add_control( new Enlightenment_Customize_Font_Size_Control( $wp_customize, 'page_description_typography[font_size]', array(
        'section' => 'page_header_typography',
        'label'   => __( 'Page Description Font Size', 'enlightenment' ),
    ) ) );

    $wp_customize->add_setting( 'page_description_typography[font_variant]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['page_description_typography']['font_variant'],
        'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
    ) );

	$wp_customize->add_control( new Enlightenment_Customize_Font_Variant_Control( $wp_customize, 'page_description_typography[font_variant]', array(
        'section' => 'page_header_typography',
        'label'   => __( 'Page Description Font Style', 'enlightenment' ),
    ) ) );

    $wp_customize->add_section( 'single_post_typography', array(
		'panel' => 'typography',
        'title' => __( 'Single Post', 'enlightenment' ),
    ) );

    $wp_customize->add_setting( 'single_post_title_typography[font_size]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['single_post_title_typography']['font_size'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

	$wp_customize->add_control( new Enlightenment_Customize_Font_Size_Control( $wp_customize, 'single_post_title_typography[font_size]', array(
        'section' => 'single_post_typography',
        'label'   => __( 'Mobile Post Title Font Size', 'enlightenment' ),
    ) ) );

    $wp_customize->add_setting( 'single_post_title_desktop_typography[font_size]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['single_post_title_desktop_typography']['font_size'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

	$wp_customize->add_control( new Enlightenment_Customize_Font_Size_Control( $wp_customize, 'single_post_title_desktop_typography[font_size]', array(
        'section' => 'single_post_typography',
        'label'   => __( 'Desktop Post Title Font Size', 'enlightenment' ),
    ) ) );

    $wp_customize->add_setting( 'single_post_title_typography[line_height]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['single_post_title_typography']['line_height'],
        'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
    ) );

	$wp_customize->add_control( new Enlightenment_Customize_Line_Height_Control( $wp_customize, 'single_post_title_typography[line_height]', array(
        'section' => 'single_post_typography',
        'label'   => __( 'Post Title Line Height', 'enlightenment' ),
    ) ) );

    $wp_customize->add_setting( 'single_post_excerpt_typography[font_size]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['single_post_excerpt_typography']['font_size'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

	$wp_customize->add_control( new Enlightenment_Customize_Font_Size_Control( $wp_customize, 'single_post_excerpt_typography[font_size]', array(
        'section' => 'single_post_typography',
        'label'   => __( 'Mobile Post Excerpt Font Size', 'enlightenment' ),
    ) ) );

    $wp_customize->add_setting( 'single_post_excerpt_desktop_typography[font_size]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['single_post_excerpt_desktop_typography']['font_size'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

	$wp_customize->add_control( new Enlightenment_Customize_Font_Size_Control( $wp_customize, 'single_post_excerpt_desktop_typography[font_size]', array(
        'section' => 'single_post_typography',
        'label'   => __( 'Desktop Post Excerpt Font Size', 'enlightenment' ),
    ) ) );

    $wp_customize->add_setting( 'single_post_excerpt_typography[font_variant]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['single_post_excerpt_typography']['font_variant'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Enlightenment_Customize_Font_Variant_Control( $wp_customize, 'single_post_excerpt_typography[font_variant]', array(
        'section' => 'single_post_typography',
        'label'   => __( 'Post Excerpt Font Style', 'enlightenment' ),
    ) ) );

    $wp_customize->add_setting( 'single_post_content_typography[font_family]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['single_post_content_typography']['font_family'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Enlightenment_Customize_Font_Family_Control( $wp_customize,  'single_post_content_typography[font_family]', array(
        'section' => 'single_post_typography',
        'label'   => __( 'Post Content Font Family', 'enlightenment' ),
    ) ) );

    $wp_customize->add_setting( 'single_post_content_typography[font_size]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['single_post_content_typography']['font_size'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

	$wp_customize->add_control( new Enlightenment_Customize_Font_Size_Control( $wp_customize, 'single_post_content_typography[font_size]', array(
        'section' => 'single_post_typography',
        'label'   => __( 'Mobile Post Content Font Size', 'enlightenment' ),
    ) ) );

    $wp_customize->add_setting( 'single_post_content_desktop_typography[font_size]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['single_post_content_desktop_typography']['font_size'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

	$wp_customize->add_control( new Enlightenment_Customize_Font_Size_Control( $wp_customize, 'single_post_content_desktop_typography[font_size]', array(
        'section' => 'single_post_typography',
        'label'   => __( 'Desktop Post Content Font Size', 'enlightenment' ),
    ) ) );

    $wp_customize->add_setting( 'single_post_content_typography[line_height]', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['single_post_content_typography']['line_height'],
        'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
    ) );

	$wp_customize->add_control( new Enlightenment_Customize_Line_Height_Control( $wp_customize, 'single_post_content_typography[line_height]', array(
        'section' => 'single_post_typography',
        'label'   => __( 'Post Content Line Height', 'enlightenment' ),
    ) ) );

    $wp_customize->add_section( 'header-style', array(
        'title'    => __( 'Header Style', 'enlightenment' ),
        'priority' => 58,
    ) );

    $wp_customize->add_setting( 'single_post_thumbnail', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['single_post_thumbnail'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Enlightenment_Customize_Image_Radio_Control( $wp_customize, 'single_post_thumbnail', array(
        'section'         => 'header-style',
        'label'           => __( 'Featured image style', 'enlightenment' ),
        'choices'         => array(
            'normal'      => array(
                'alt' => __( 'Normal', 'enlightenment' ),
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="currentColor" d="M25 9v3.5h50V9H25zm2.764 9.1A2.8 2.8 0 0025 20.9a2.8 2.8 0 002.8 2.8 2.8 2.8 0 002.8-2.8 2.8 2.8 0 00-2.8-2.8 2.8 2.8 0 00-.036 0zM34 19.7v2.2h14.6v-2.2H34zm-9 9.6v30.9h50V29.3H25zm0 36.5V68h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h30.9V73H25zm0 5.7v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h30.9v-2.2H25zm0 5.7v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h30.9v-2.2H25z"/></svg>',
            ),
            'medium'      => array(
                'alt' => __( 'Medium', 'enlightenment' ),
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="currentColor" d="M25 9v3.5h50V9H25zm2.764 9.1A2.8 2.8 0 0025 20.9a2.8 2.8 0 002.8 2.8 2.8 2.8 0 002.8-2.8 2.8 2.8 0 00-2.8-2.8 2.8 2.8 0 00-.036 0zM34 19.7v2.2h14.6v-2.2H34zm-22.5 9.6v47.6h77V29.3h-77zM25 82.5v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h30.9v-2.2H25zm0 5.7v2.2h50v-2.2H25zm0 3.6v2.2h50V99H25z"/></svg>',
            ),
            'large'       => array(
                'alt' => __( 'Large', 'enlightenment' ),
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="currentColor" d="M0 0v38.82h100V0H0zm25 47.2v3.5h50v-3.5H25zm2.764 9.1A2.8 2.8 0 0025 59.1a2.8 2.8 0 002.8 2.8 2.8 2.8 0 002.8-2.8 2.8 2.8 0 00-2.8-2.8 2.8 2.8 0 00-.036 0zM34 58v2.2h14.6V58H34zm-9 9.5v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h30.9v-2.2H25zm0 5.7v2.2h50v-2.2H25zm0 3.6v2.2h50V84H25zm0 3.6v2.2h30.9v-2.2H25zm0 5.7v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25z"/></svg>',
            ),
            'cover'       => array(
                'alt' => __( 'Cover', 'enlightenment' ),
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="currentColor" d="M0 0v61.8h100V0H0zm25 38.2h50v3.5H25v-3.5zm2.764 9a2.8 2.8 0 01.037 0A2.8 2.8 0 0130.6 50a2.8 2.8 0 01-2.8 2.8A2.8 2.8 0 0125 50a2.8 2.8 0 012.764-2.8zM34 48.7h14.6v2.2H34v-2.2zm-9 22.1V73h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h30.9V78H25zm0 5.7v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h30.9v-2.2H25zm0 5.7v2.2h50v-2.2H25z"/></svg>',
            ),
            'full-screen' => array(
                'alt' => __( 'Full Screen', 'enlightenment' ),
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="currentColor" d="M0 0v100h100V0H0zm25 65h50v3.5H25V65zm0 9h50v2.2H25V74zm0 3.6h30.9v2.2H25v-2.2zm2.764 7.8a2.8 2.8 0 01.037 0 2.8 2.8 0 012.799 2.8 2.8 2.8 0 01-2.8 2.8 2.8 2.8 0 01-2.8-2.8 2.8 2.8 0 012.764-2.8zM34 87.1h14.6v2.2H34v-2.2z"/></svg>',
            ),
            'none'        => array(
                'alt' => __( 'None', 'enlightenment' ),
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="currentColor" d="M25 9v3.5h50V9H25zm2.764 9.1A2.8 2.8 0 0025 20.9a2.8 2.8 0 002.8 2.8 2.8 2.8 0 002.8-2.8 2.8 2.8 0 00-2.8-2.8 2.8 2.8 0 00-.036 0zM34 19.7v2.2h14.6v-2.2H34zm-9 9.6v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h30.9v-2.2H25zm0 5.7V48h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h50V53H25zm0 3.6v2.2h30.9v-2.2H25zm0 5.7v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h30.9v-2.2H25zm0 5.7V81h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h50V86H25zm0 3.6v2.2h30.9v-2.2H25zm0 5.7v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25z"/></svg>',
            ),
        ),
        'active_callback' => 'enlightenment_is_single_post_or_page',
    ) ) );

    $wp_customize->add_setting( 'header_image_position', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['header_image_position'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

	$video = current_theme_supports( 'custom-header', 'video' );

    $wp_customize->add_control( new Enlightenment_Customize_Position_Control( $wp_customize, 'header_image_position', array(
        'section' => 'header-style',
        'label'   => $video ? __( 'Header media position', 'enlightenment' ) : __( 'Header image position', 'enlightenment' ),
    ) ) );

    $wp_customize->add_setting( 'header_overlay_color', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['header_overlay_color'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Enlightenment_Customize_Alpha_Color_Control( $wp_customize, 'header_overlay_color', array(
        'section' => 'header-style',
        'label'   => $video ? __( 'Header media overlay color', 'enlightenment' ) : __( 'Header image overlay color', 'enlightenment' ),
    ) ) );

    $wp_customize->add_section( 'navigation', array(
        'title'    => __( 'Navigation', 'enlightenment' ),
        'priority' => 101,
    ) );

    $wp_customize->add_setting( 'site_header_size', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['site_header_size'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control(
        'site_header_size',
        array(
            'section' => 'navigation',
            'type'    => 'select',
            'label'   => __( 'Navbar Size', 'enlightenment' ),
            'choices' => array(
                'small'  => _x( 'Small',  'Navbar Size', 'enlightenment' ),
                'medium' => _x( 'Medium', 'Navbar Size', 'enlightenment' ),
                'large'  => _x( 'Large',  'Navbar Size', 'enlightenment' ),
            ),
        )
    );

    $wp_customize->add_setting( 'site_header_style', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['site_header_style'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control(
        'site_header_style',
        array(
            'section' => 'navigation',
            'type'    => 'select',
            'label'   => __( 'Navbar Color Mode', 'enlightenment' ),
            'choices' => array(
				'body'  => _x( 'Inherit', 'Navbar Color Mode', 'enlightenment' ),
                'light' => _x( 'Light',   'Navbar Color Mode', 'enlightenment' ),
                'dark'  => _x( 'Dark',    'Navbar Color Mode', 'enlightenment' ),
            ),
        )
    );

    $wp_customize->add_setting( 'site_header_position', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['site_header_position'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control(
        'site_header_position',
        array(
            'label'   => __( 'Navbar Position', 'enlightenment' ),
            'section' => 'navigation',
            'type'    => 'select',
            'choices' => array(
                'static-top' => _x( 'Static', 'Navbar Position', 'enlightenment' ),
                'fixed-top'  => _x( 'Fixed to top', 'Navbar Position', 'enlightenment' ),
            ),
        )
    );

    $wp_customize->add_setting( 'navbar_expand', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['navbar_expand'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'navbar_expand', array(
        'label'   => __( 'Navigation menu expand breakpoint', 'enlightenment' ),
        'section' => 'navigation',
        'type'    => 'select',
        'choices' => array(
            ''     => __( 'Always show',          'enlightenment' ),
			'sm'   => __( 'Smartphone Landscape', 'enlightenment' ),
			'md'   => __( 'Tablet Portrait',      'enlightenment' ),
			'lg'   => __( 'Tablet Landscape',     'enlightenment' ),
			'xl'   => __( 'Desktop Laptop',       'enlightenment' ),
            'hide' => __( 'Always hide',          'enlightenment' ),
        ),
    ) );

    $wp_customize->add_setting( 'nav_menu_dropdown_toggle', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['nav_menu_dropdown_toggle'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control(
        'nav_menu_dropdown_toggle',
        array(
            'label'   => __( 'Menu items dropdown trigger', 'enlightenment' ),
            'section' => 'navigation',
            'type'    => 'select',
            'choices' => array(
                'click'    => _x( 'Click',    'Menu items dropdown trigger', 'enlightenment' ),
                'hover'    => _x( 'Hover',    'Menu items dropdown trigger', 'enlightenment' ),
                'separate' => _x( 'Separate', 'Menu items dropdown trigger', 'enlightenment' ),
            ),
        )
    );

    $wp_customize->add_setting( 'navbar_nav_overflow', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['navbar_nav_overflow'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control(
        'navbar_nav_overflow',
        array(
            'section'     => 'navigation',
            'type'        => 'select',
            'label'       => __( 'Navigation menu overflow', 'enlightenment' ),
			'description' => __( 'The navbar position will be forced to static when wrapping menu items.', 'enlightenment' ),
            'choices'     => array(
                'scroll' => _x( 'Scroll', 'Navigation menu overflow', 'enlightenment' ),
                'wrap'   => _x( 'Wrap',   'Navigation menu overflow', 'enlightenment' ),
            ),
        )
    );

	$wp_customize->add_setting( 'navbar_nav_separate', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['navbar_nav_separate'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'navbar_nav_separate', array(
        'section'     => 'navigation',
        'type'        => 'select',
        'label'       => __( 'Show navigation menu on a separate row', 'enlightenment' ),
		'description' => __( 'On smaller screens the navigation menu is always displayed on a separate row.', 'enlightenment' ),
		'choices'     => array(
			'always' => _x( 'Always',        'Show navigation menu on a separate row', 'enlightenment' ),
			'auto'   => _x( 'Automatically', 'Show navigation menu on a separate row', 'enlightenment' ),
			'never'  => _x( 'Never',         'Show navigation menu on a separate row', 'enlightenment' ),
		),
    ) );

    $wp_customize->add_section( 'general', array(
        'title'    => __( 'General', 'enlightenment' ),
        'priority' => 101,
    ) );

    $wp_customize->add_setting( 'smooth_scroll', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['smooth_scroll'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'enlightenment_sanitize_checkbox',
    ) );

    $wp_customize->add_control( 'smooth_scroll', array(
        'section'     => 'general',
        'type'        => 'checkbox',
        'label'       => __( 'Enable smooth scrolling', 'enlightenment' ),
        'description' => __( 'Animates scrolling triggered by clicking. Does not affect user scroll.', 'enlightenment' ),
    ) );

    $wp_customize->add_setting( 'underline_links', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['underline_links'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'enlightenment_sanitize_checkbox',
    ) );

    $wp_customize->add_control( 'underline_links', array(
        'section'     => 'general',
        'type'        => 'checkbox',
        'label'       => __( 'Underline links in content blocks', 'enlightenment' ),
        'description' => __( 'Links within large blocks of text like post content will be underlined.', 'enlightenment' ),
    ) );

    $wp_customize->add_setting( 'masonry', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['masonry'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'enlightenment_sanitize_checkbox',
    ) );

    $wp_customize->add_control( 'masonry', array(
        'section'     => 'general',
        'type'        => 'checkbox',
        'label'       => __( 'Enable masonry in grid loops', 'enlightenment' ),
        'description' => __( 'Posts in multicolumn grids will be arranged in a cascading layout.', 'enlightenment' ),
    ) );

    $wp_customize->add_setting( 'posts_nav_style', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['posts_nav_style'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'posts_nav_style', array(
        'section' => 'general',
        'type'    => 'select',
        'label'   => __( 'Posts Navigation Style', 'enlightenment' ),
        'choices' => array(
            'static'    => _x( 'Static Links',    'Posts Navigation Style', 'enlightenment' ),
            'paginated' => _x( 'Paginated',       'Posts Navigation Style', 'enlightenment' ),
            'infinite'  => _x( 'Infinite Scroll', 'Posts Navigation Style', 'enlightenment' ),
            'ajax'      => _x( 'AJAX Navigation', 'Posts Navigation Style', 'enlightenment' ),
        ),
    ) );

    $wp_customize->add_setting( 'posts_nav_labels', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['posts_nav_labels'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'posts_nav_labels', array(
        'section' => 'general',
        'type'    => 'select',
        'label'   => __( 'Posts Navigation Labels', 'enlightenment' ),
        'choices' => array(
            'next-prev'     => _x( 'Next page / Previous page',   'Posts Navigation Labels', 'enlightenment' ),
            'older-newer'   => _x( 'Older posts / Newer posts',   'Posts Navigation Labels', 'enlightenment' ),
            'earlier-later' => _x( 'Earlier posts / Later posts', 'Posts Navigation Labels', 'enlightenment' ),
        ),
    ) );

    $wp_customize->add_setting( 'comments_nav_style', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['comments_nav_style'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'comments_nav_style', array(
        'section' => 'general',
        'type'    => 'select',
        'label'   => __( 'Comments Navigation Style', 'enlightenment' ),
        'choices' => array(
            'static'    => _x( 'Static Links', 'Comments Navigation Style', 'enlightenment' ),
            'paginated' => _x( 'Paginated',    'Comments Navigation Style', 'enlightenment' ),
        ),
    ) );

    $wp_customize->add_setting( 'comments_nav_labels', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['comments_nav_labels'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'comments_nav_labels', array(
        'section' => 'general',
        'type'    => 'select',
        'label'   => __( 'Comments Navigation Labels', 'enlightenment' ),
        'choices' => array(
            'next-prev'     => _x( 'Next page / Previous page',         'Comments Navigation Labels', 'enlightenment' ),
            'older-newer'   => _x( 'Older comments / Newer comments',   'Comments Navigation Labels', 'enlightenment' ),
            'earlier-later' => _x( 'Earlier comments / Later comments', 'Comments Navigation Labels', 'enlightenment' ),
        ),
    ) );

    $wp_customize->add_setting( 'site_footer_style', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['site_footer_style'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'site_footer_style', array(
        'section' => 'general',
        'type'    => 'select',
        'label'   => __( 'Site Footer Color Mode', 'enlightenment' ),
        'choices' => array(
			'body'  => _x( 'Inherit', 'Site Footer Color Mode', 'enlightenment' ),
            'light' => _x( 'Light',   'Site Footer Color Mode', 'enlightenment' ),
            'dark'  => _x( 'Dark',    'Site Footer Color Mode', 'enlightenment' ),
        ),
    ) );

    $wp_customize->add_setting( 'theme_credit_link', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['theme_credit_link'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'enlightenment_sanitize_checkbox',
    ) );

    $wp_customize->add_control( 'theme_credit_link', array(
        'section'     => 'general',
        'type'        => 'checkbox',
        'label'       => __( 'Show theme credit link', 'enlightenment' ),
    ) );

    $wp_customize->add_setting( 'author_credit_link', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['author_credit_link'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'enlightenment_sanitize_checkbox',
    ) );

    $wp_customize->add_control( 'author_credit_link', array(
        'section'     => 'general',
        'type'        => 'checkbox',
        'label'       => __( 'Show theme designer credit link', 'enlightenment' ),
    ) );

    $wp_customize->add_setting( 'wordpress_credit_link', array(
        'type'              => $type,
        'capability'        => $cap,
        'default'           => $defaults['wordpress_credit_link'],
        'transport'         => 'postMessage',
        'sanitize_callback' => 'enlightenment_sanitize_checkbox',
    ) );

    $wp_customize->add_control( 'wordpress_credit_link', array(
        'section'     => 'general',
        'type'        => 'checkbox',
        'label'       => __( 'Show WordPress credit link', 'enlightenment' ),
    ) );
}
add_action( 'customize_register', 'enlightenment_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function enlightenment_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function enlightenment_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

function enlightenment_is_single_post_or_page() {
    return ( is_singular( 'post' ) || is_page() );
}

function enlightenment_customize_controls_scripts() {
    wp_enqueue_style( 'enlightenment-theme-customize-controls', get_theme_file_uri( 'assets/css/customize-controls.css' ), false, null );

    wp_enqueue_script( 'enlightenment-theme-customize-controls', get_theme_file_uri( 'assets/js/customize-controls.js' ), array( 'jquery', 'customize-controls' ), null, true );
}
add_action( 'customize_controls_enqueue_scripts', 'enlightenment_customize_controls_scripts' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function enlightenment_customize_preview_scripts() {
    wp_enqueue_style( 'enlightenment-theme-customize-preview', get_theme_file_uri( 'assets/css/customize-preview.css' ), false, null );
}
add_action( 'customize_preview_init', 'enlightenment_customize_preview_scripts' );

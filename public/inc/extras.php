<?php

function enlightenment_theme_default_theme_mods( $mods ) {
	return array_merge( $mods, array(
		'color_mode'               => 'auto',
		'hide_navbar_brand_on'     => array(),
		'site_header_size'         => 'large',
		'site_header_style'        => 'body',
		'site_header_position'     => 'fixed-top',
		'navbar_expand'            => 'lg',
		'nav_menu_dropdown_toggle' => 'click',
		'navbar_nav_overflow'      => 'scroll',
		'navbar_nav_align'         => 'justify',
		'navbar_nav_separate'      => 'auto',
		'single_post_thumbnail'    => 'normal',
		'header_image_position'    => 'center-center',
		'header_overlay_color'     => 'rgba(0,0,0,0.5)',
		'smooth_scroll'            => false,
		'underline_links'          => true,
		'masonry'                  => false,
		'posts_nav_style'          => 'static',
		'posts_nav_labels'         => 'older-newer',
		'comments_nav_style'       => 'paginated',
		'comments_nav_labels'      => 'older-newer',
		'site_footer_style'        => 'body',
		'theme_credit_link'        => true,
		'author_credit_link'       => false,
		'wordpress_credit_link'    => true,
	) );
}
add_filter( 'enlightenment_default_theme_mods', 'enlightenment_theme_default_theme_mods' );

function enlightenment_theme_register_post_meta() {
	$meta = array(
		'_enlightenment_navbar_transparent',
		'_enlightenment_single_post_thumbnail',
		'_enlightenment_header_image_position',
		'_enlightenment_header_overlay_color',
	);

	foreach ( $meta as $key ) {
		foreach ( array( 'post', 'page' ) as $post_type ) {
			register_post_meta( $post_type, $key, array(
				'single'        => true,
				'type'          => 'string',
				'show_in_rest'  => true,
				'auth_callback' => 'enlightenment_can_edit_post_type',
			) );
		}
	}
}
add_action( 'init', 'enlightenment_theme_register_post_meta' );

function enlightenment_theme_404_template_hierarchy( $templates ) {
	return array_merge( $templates, array( 'index.php' ) );
}
add_filter( '404_template_hierarchy', 'enlightenment_theme_404_template_hierarchy' );

function enlightenment_filter_theme_custom_css( $output ) {
	$defaults = enlightenment_default_theme_mods();

	if ( false == get_theme_mod( 'smooth_scroll' ) ) {
		$output .= "\n:root {\n\tscroll-behavior: auto;\n}\n";
	}

	if ( false == get_theme_mod( 'underline_links' ) ) {
		$output .= "\n:root {\n\t--enlightenment-link-text-decoration-line: none;\n}\n";
	}

	return $output;
}
add_filter( 'enlightenment_theme_custom_css', 'enlightenment_filter_theme_custom_css' );

function enlightenment_filter_theme_custom_editor_styles( $output ) {
	if ( false == get_theme_mod( 'underline_links' ) ) {
		$output .= "\n:root {\n\t--enlightenment-link-text-decoration-line: none;\n}\n";
	}

	return $output;
}
add_filter( 'enlightenment_theme_custom_editor_styles', 'enlightenment_filter_theme_custom_editor_styles' );

function enlightenment_print_theme_custom_editor_styles() {
	header('Content-Type: text/css');

	echo apply_filters( 'enlightenment_theme_custom_editor_styles', '' );

	die();
}
add_action( 'wp_ajax_enlightenment_print_theme_custom_editor_styles', 'enlightenment_print_theme_custom_editor_styles' );

function enlightenment_add_theme_custom_editor_styles( $stylesheets ) {
	if ( ! empty( apply_filters( 'enlightenment_theme_custom_editor_styles', '' ) ) ) {
		$stylesheets[] = admin_url( 'admin-ajax.php?action=enlightenment_print_theme_custom_editor_styles' );
	}

	return $stylesheets;
}
add_filter( 'editor_stylesheets', 'enlightenment_add_theme_custom_editor_styles' );

function enlightenment_maybe_force_static_site_header( $option ) {
	if ( 'wrap' == get_theme_mod( 'navbar_nav_overflow' ) ) {
		$option = 'static-top';
	}

	return $option;
}
add_filter( 'theme_mod_site_header_position', 'enlightenment_maybe_force_static_site_header' );

function enlightenment_taxonomy_header_image( $option ) {
	if ( ! is_category() && ! is_tag() && ! is_tax() ) {
		return $option;
	}

	/**
	 * Categories Images
	 *
	 * @link https://wordpress.org/plugins/categories-images/
	**/
	if ( function_exists( 'z_taxonomy_image_url' ) ) {
		$url = z_taxonomy_image_url();

		if ( ! empty( $url ) ) {
			return $url;
		}
	}

	/**
	 * Category and Taxonomy Image
	 *
	 * @link https://wordpress.org/plugins/wp-custom-taxonomy-image/
	**/
	if ( function_exists( 'get_wp_term_image' ) ) {
		$url = get_wp_term_image( get_queried_object()->term_id );

		if ( ! empty( $url ) ) {
			return $url;
		}
	}

	/**
	 * Taxonomy Images
	 *
	 * @link https://wordpress.org/plugins/taxonomy-images/
	**/
	if ( class_exists( 'Taxonomy_Images_Supported' ) ) {
		$url = apply_filters( 'taxonomy-images-queried-term-image-url', '' );

		if ( ! empty( $url ) ) {
			return $url;
		}
	}

	/**
	 * WP Term Images
	 *
	 * @link https://wordpress.org/plugins/wp-term-images/
	**/
	if ( class_exists( 'WP_Term_Images' ) ) {
		$image_id   = get_term_meta( get_queried_object()->term_id, 'image', true );
		$image_data = wp_get_attachment_image_src( $image_id, 'full' );

		if ( ! empty( $image_data[0] ) ) {
			return $image_data[0];
		}
	}

	return $option;
}
add_filter( 'theme_mod_header_image', 'enlightenment_taxonomy_header_image' );

function enlightenment_filter_theme_mod_single_post_thumbnail( $option ) {
	if ( ! is_singular( 'post' ) && ! is_page() ) {
		return $option;
	}

	$override = get_post_meta( get_the_ID(), '_enlightenment_single_post_thumbnail', true );

	if ( in_array( $override, array( 'normal', 'medium', 'large', 'cover', 'full-screen', 'none' ) ) ) {
		$option = $override;
	}

	return $option;
}
add_filter( 'theme_mod_single_post_thumbnail', 'enlightenment_filter_theme_mod_single_post_thumbnail' );

function enlightenment_filter_theme_mod_header_image_position( $option ) {
	$override  = '';
	$positions = array(
		'left-top',
		'left-center',
		'left-bottom',
		'center-top',
		'center-center',
		'center-bottom',
		'right-top',
		'right-center',
		'right-bottom',
	);

	if ( is_category() || is_tag() || is_tax() ) {
		$override = get_term_meta( get_queried_object()->term_id, '_enlightenment_header_image_position', true );
	} elseif ( is_singular( 'post' ) || is_page() ) {
		$override = get_post_meta( get_the_ID(), '_enlightenment_header_image_position', true );
	}

	if ( in_array( $override, $positions ) ) {
		$option = $override;
	}

	return $option;
}
add_filter( 'theme_mod_header_image_position', 'enlightenment_filter_theme_mod_header_image_position' );

function enlightenment_filter_theme_mod_header_overlay_color( $option ) {
	$override = '';

	if ( is_category() || is_tag() || is_tax() ) {
		$override = get_term_meta( get_queried_object()->term_id, '_enlightenment_header_overlay_color', true );
	} elseif ( is_singular( 'post' ) || is_page() ) {
		$override = get_post_meta( get_the_ID(), '_enlightenment_header_overlay_color', true );
	}

	if ( ! empty( $override ) ) {
		$option = $override;
	}

	return $option;
}
add_filter( 'theme_mod_header_overlay_color', 'enlightenment_filter_theme_mod_header_overlay_color' );

function enlightenment_filter_theme_stylesheet_deps( $deps ) {
	$deps[] = 'font-awesome';
	$deps[] = 'select2';
	$deps[] = 'gemini-scrollbar';

	return $deps;
}
add_filter( 'enlightenment_theme_stylesheet_deps', 'enlightenment_filter_theme_stylesheet_deps' );

function enlightenment_filter_main_script_deps( $deps ) {
   $deps[] = 'select2';

   return $deps;
}
add_filter( 'enlightenment_main_script_deps', 'enlightenment_filter_main_script_deps' );

function enlightenment_theme_archive_layouts( $layouts ) {
	$layouts['wp_router_page'] = array(
		'smartphone-portrait'  => 'full-width',
		'smartphone-landscape' => 'inherit',
		'tablet-portrait'      => 'inherit',
		'tablet-landscape'     => 'inherit',
		'desktop-laptop'       => 'inherit',
	);

	return $layouts;
}
add_filter( 'enlightenment_archive_layouts', 'enlightenment_theme_archive_layouts' );

function enlightenment_filter_current_layout( $layout ) {
	if( is_singular() ) {
		return $layout;
	}

	$grids = enlightenment_current_grid();

	foreach( $grids as $breakpoint => $grid ) {
		if( 'inherit' == $grid ) {
			continue;
		}

		$atts = enlightenment_get_grid( $grid );

		if( 2 < $atts['content_columns'] ) {
			$layout[ $breakpoint ] = 'full-width';
		}
	}

	return $layout;
}
add_filter( 'enlightenment_current_layout', 'enlightenment_filter_current_layout' );

function enlightenment_filter_site_header_hooks( $hooks ) {
	if ( isset( $hooks['enlightenment_site_header'] ) ) {
		$hooks['enlightenment_site_header']['functions'][] = 'enlightenment_search_form';
	}

	return $hooks;
}
add_filter( 'enlightenment_site_header_hooks', 'enlightenment_filter_site_header_hooks' );

function enlightenment_filter_page_content_hooks( $hooks ) {
	if ( isset( $hooks['enlightenment_before_page_content'] ) ) {
		$hooks['enlightenment_before_page_content']['functions'][] = 'enlightenment_nav_menu';
	}

	return $hooks;
}
add_filter( 'enlightenment_page_content_hooks', 'enlightenment_filter_page_content_hooks' );

function enlightenment_theme_entry_hooks( $hooks ) {
	$hooks['enlightenment_entry_footer']['functions'][] = 'enlightenment_author_hcard';

	return $hooks;
}
add_filter( 'enlightenment_entry_hooks', 'enlightenment_theme_entry_hooks' );

function enlightenment_filter_body_class( $classes ) {
	if ( 'large' == get_theme_mod( 'site_header_size' ) ) {
		$classes[] = 'has-navbar-lg';
	} elseif ( 'medium' == get_theme_mod( 'site_header_size' ) ) {
		$classes[] = 'has-navbar-md';
	}

	if ( enlightenment_is_navbar_transparent() ) {
		$classes[] = 'has-navbar-transparent';
	}

	if ( 'wrap' == get_theme_mod( 'navbar_nav_overflow' ) ) {
		$classes[] = 'has-navbar-nav-wrap';
	}

	if ( 'always' == get_theme_mod( 'navbar_nav_separate' ) ) {
		$classes[] = 'has-navbar-nav-separate';
	} elseif ( 'auto' == get_theme_mod( 'navbar_nav_separate' ) ) {
		$classes[] = 'has-navbar-nav-auto';
	}

	$option = get_theme_mod( 'navbar_nav_align' );
	if ( in_array( $option, array( 'start', 'center', 'end', 'justify' ) ) ) {
		$classes[] = sprintf( 'has-navbar-nav-align-%s', $option );
	}

	if ( ( is_singular( 'post' ) || is_page() ) && has_post_thumbnail() ) {
		$option = get_theme_mod( 'single_post_thumbnail' );

		if ( in_array( $option, array( 'large', 'cover', 'full-screen' ) ) ) {
			$classes[] = sprintf( 'single-post-has-thumbnail-%s', $option );
		}
	}

    return $classes;
}
add_filter( 'body_class', 'enlightenment_filter_body_class' );

function enlightenment_is_navbar_transparent() {
	$is_transparent = false;

	if (
		'large' == get_theme_mod( 'site_header_size' )
		&&
		(
			(
				'fixed-top' == get_theme_mod( 'site_header_position' )
			)
			||
			(
				'static-top' == get_theme_mod( 'site_header_position' )
				&&
				'wrap' != get_theme_mod( 'navbar_nav_overflow' )
			)
		)
		&&
		(
			(
				! is_singular() && has_header_image()
			)
			||
			(
				( is_singular( 'post' ) || is_page() )
				&&
				has_post_thumbnail()
				&&
				(
					in_array( get_post_meta( get_the_ID(), '_enlightenment_single_post_thumbnail', true ), array( 'large', 'cover', 'full-screen' ) )
					||
					(
						empty( get_post_meta( get_the_ID(), '_enlightenment_single_post_thumbnail', true ) )
						&&
						in_array( get_theme_mod( 'single_post_thumbnail' ), array( 'large', 'cover', 'full-screen' ) )
					)
				)
			)
			||
			(
				is_page()
				&&
				'1' == get_post_meta( get_the_ID(), '_enlightenment_navbar_transparent', true )
			)
			||
			(
				function_exists( 'is_buddypress' ) && is_buddypress() && has_header_image()
			)
			||
			(
				function_exists( 'is_bbpress' ) && is_bbpress() && has_header_image()
			)
			||
			(
				function_exists( 'bbp_is_forum_edit' ) && bbp_is_forum_edit() && has_header_image()
			)
		)
	) {
		$is_transparent = true;
	}

	return apply_filters( 'enlightenment_is_navbar_transparent', $is_transparent );
}

function enlightenment_is_navbar_inversed() {
	$is_inversed = false;

	if (
		enlightenment_is_navbar_transparent()
		&&
		(
			'light' == get_theme_mod( 'site_header_style' )
			||
			(
				'light' == enlightenment_bootstrap_get_current_color_mode()
				&&
				'body' == get_theme_mod( 'site_header_style' )
			)
		)
	) {
		$is_inversed = true;
	}

	return apply_filters( 'enlightenment_is_navbar_inversed', $is_inversed );
}

function enlightenment_filter_site_header_class_args( $args ) {
	$args['class'] = str_replace( ' navbar ', ' ', $args['class'] );

	$offset = strrpos( $args['class'], ' navbar' );
	$length = strlen( ' navbar' );

	if ( false !== $offset && strlen( $args['class'] ) - $length == $offset ) {
		$args['class'] = substr_replace( $args['class'], '', $offset, $length );
	}

	switch ( get_theme_mod( 'site_header_size' ) ) {
		case 'large':
		    $args['class'] .= ' navbar-lg';
			break;

		case 'medium':
			$args['class'] .= ' navbar-md';
			break;
	}

	if ( enlightenment_is_navbar_transparent() ) {
		$background = esc_attr( current_theme_supports( 'enlightenment-bootstrap', 'navbar-background' ) );

		$args['class']  = str_replace( " bg-{$background}", ' bg-transparent', $args['class'] );

		$args['class'] .= ' is-navbar-transparent';

		if ( enlightenment_is_navbar_inversed() ) {
			$args['class'] .= ' is-navbar-inversed';
		}
	}

	if ( 'wrap' == get_theme_mod( 'navbar_nav_overflow' ) ) {
		$args['class'] .= ' navbar-nav-wrap';
	}

	if ( 'always' == get_theme_mod( 'navbar_nav_separate' ) ) {
		$args['class'] .= ' navbar-nav-separate';
	} elseif ( 'auto' == get_theme_mod( 'navbar_nav_separate' ) ) {
		$args['class'] .= ' navbar-nav-auto';
	}

	$option = get_theme_mod( 'navbar_nav_align' );
	if ( in_array( $option, array( 'start', 'center', 'end', 'justify' ) ) ) {
		$args['class'] .= sprintf( ' navbar-nav-align-%s', $option );
	}

    return $args;
}
add_filter( 'enlightenment_site_header_class_args', 'enlightenment_filter_site_header_class_args', 12 );

function enlightenment_filter_site_header_extra_atts_args( $args ) {
    if ( enlightenment_is_navbar_transparent() ) {
		$args['atts']['data-bs-theme'] = 'dark';
	}

	return $args;
}
add_filter( 'enlightenment_site_header_extra_atts_args', 'enlightenment_filter_site_header_extra_atts_args', 12 );

function enlightenment_theme_add_header_container() {
	$theme_support = get_theme_support( 'enlightenment-bootstrap' );
	$header_class  = enlightenment_site_header_class( array( 'echo' => false ) );

	if ( false === strpos( $header_class, 'sticky-' ) && false === strpos( $header_class, 'fixed-' ) ) {
		add_action( 'enlightenment_site_header', 'enlightenment_open_container', 1 );
		add_action( 'enlightenment_site_header', 'enlightenment_close_container', 999 );
	}
}
add_action( 'init', 'enlightenment_theme_add_header_container' );

function enlightenment_theme_filter_site_title_home_link_args( $args ) {
	if ( ! has_custom_logo() ) {
		return $args;
	}

	$options   = get_theme_mod( 'hide_navbar_brand_on' );
	$class     = '';
	$hide_prev = false;

	$breakpoints = array(
		'smartphone-portrait'  => '',
		'smartphone-landscape' => '-sm',
		'tablet-portrait'      => '-md',
		'tablet-landscape'     => '-lg',
		'desktop-laptop'       => '-xl',
	);

	foreach ( $breakpoints as $breakpoint => $prefix ) {
		$hide = in_array( $breakpoint, $options );

		if ( $hide ) {
			$class .= sprintf( ' d%s-none', $prefix );
		} elseif ( $hide_prev ) {
			$class .= sprintf( ' d%s-inline-block', $prefix );
		}

		$hide_prev = $hide;
	}

	$args['container_class'] .= $class;

	return $args;
}
add_filter( 'enlightenment_site_title_home_link_args', 'enlightenment_theme_filter_site_title_home_link_args', 12 );

function enlightenment_hook_secondary_navigation() {
	if ( is_home() || is_archive() ) {
		add_action( 'enlightenment_before_page_content', 'enlightenment_nav_menu' );
	}
}
add_action( 'wp', 'enlightenment_hook_secondary_navigation' );

function enlightenment_theme_default_hooks() {
	remove_action( 'enlightenment_site_footer', 'enlightenment_copyright_notice' );
	remove_action( 'enlightenment_site_footer', 'enlightenment_credit_links' );

	add_action( 'enlightenment_site_header', 'enlightenment_social_nav_menu' );
	add_action( 'enlightenment_site_header', 'enlightenment_bootstrap_color_mode_switcher' );
	add_action( 'enlightenment_site_header', 'enlightenment_search_form' );

	add_action( 'enlightenment_site_footer', 'enlightenment_nav_menu' );
	add_action( 'enlightenment_site_footer', 'enlightenment_copyright_notice' );
	add_action( 'enlightenment_site_footer', 'enlightenment_credit_links' );
}
add_action( 'after_setup_theme', 'enlightenment_theme_default_hooks', 0 );

/**
 * Returns a custom logo, linked to home.
 *
 * @since 2.0.0
 *
 * @param int $blog_id Optional. ID of the blog in question. Default is the ID of the current blog.
 * @return string Custom logo markup.
 */
function enlightenment_custom_logo( $html, $blog_id = 0 ) {
	if ( doing_filter( 'the_content' ) ) {
		return $html;
	}

	$html = '';
	$switched_blog = false;

	if ( is_multisite() && ! empty( $blog_id ) && (int) $blog_id !== get_current_blog_id() ) {
		switch_to_blog( $blog_id );
		$switched_blog = true;
	}

	$custom_logo_id     = get_theme_mod( 'custom_logo' );
	$custom_logo_alt_id = get_theme_mod( 'custom_logo_alt' );

	// We have a logo. Logo is go.
	if ( $custom_logo_id ) {
		$custom_logo_attr = $custom_logo_alt_attr = array(
			'class'    => 'custom-logo',
			'itemprop' => 'logo',
		);

		if ( $custom_logo_alt_id ) {
			$custom_logo_attr['class']     .= ' custom-logo-dark';
			$custom_logo_alt_attr['class'] .= ' custom-logo-light';
		}

		/*
		 * If the logo alt attribute is empty, get the site title and explicitly
		 * pass it to the attributes used by wp_get_attachment_image().
		 */
		$image_alt = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
		if ( empty( $image_alt ) ) {
			$custom_logo_attr['alt'] = get_bloginfo( 'name', 'display' );
		}

		/*
		 * If the alt attribute is not empty, there's no need to explicitly pass
		 * it because wp_get_attachment_image() already adds the alt attribute.
		 */
		$html = sprintf( '<a href="%1$s" class="custom-logo-link" rel="home" itemprop="url">%2$s%3$s</a>',
			esc_url( home_url( '/' ) ),
			wp_get_attachment_image( $custom_logo_id,     'full', false, $custom_logo_attr ),
			wp_get_attachment_image( $custom_logo_alt_id, 'full', false, $custom_logo_alt_attr )
		);
	}

	// If no logo is set but we're in the Customizer, leave a placeholder (needed for the live preview).
	elseif ( is_customize_preview() ) {
		$html = sprintf( '<a href="%1$s" class="custom-logo-link" style="display:none;"><img class="custom-logo custom-logo-dark"/><img class="custom-logo custom-logo-light"/></a>',
			esc_url( home_url( '/' ) )
		);
	}

	if ( $switched_blog ) {
		restore_current_blog();
	}

	return $html;
}
add_filter( 'get_custom_logo', 'enlightenment_custom_logo', 10, 2 );

function enlightenment_filter_wp_nav_menu_args( $args ) {
	$args['fallback_cb'] = '__return_false';

	if ( doing_action( 'enlightenment_site_header' ) ) {
		$args['theme_location'] = 'primary';
	} elseif ( doing_action( 'enlightenment_before_page_content' ) ) {
		$args['theme_location'] = 'blog';
	} elseif ( doing_action( 'enlightenment_site_footer' ) ) {
		$args['theme_location'] = 'footer';
	}

	return $args;
}
add_filter( 'enlightenment_wp_nav_menu_args', 'enlightenment_filter_wp_nav_menu_args' );

function enlightenment_bootstrap_filter_wp_nav_menu_args( $args ) {
	if ( ! doing_action( 'enlightenment_site_header' ) ) {
		$args['container_class'] = str_replace( ' collapse navbar-collapse', '',     $args['container_class'] );
		$args['menu_class']      = str_replace( ' navbar-nav ms-auto',       ' nav', $args['menu_class'] );

		$expand = current_theme_supports( 'enlightenment-bootstrap', 'navbar-expand' );

		switch ( $expand ) {
			case '':
				$args['menu_class'] = str_replace( ' container px-0', '', $args['menu_class'] );
				break;

			case 'sm':
			case 'md':
			case 'lg':
			case 'xl':
				$args['menu_class'] = str_replace( sprintf( ' container px-%s-0', esc_attr( $expand ) ), '', $args['menu_class'] );
				break;

			default:
				$args['menu_class'] = str_replace( ' container', '', $args['menu_class'] );
				break;
		}
	}

	return $args;
}
add_filter( 'enlightenment_wp_nav_menu_args', 'enlightenment_bootstrap_filter_wp_nav_menu_args', 24 );

function enlightenment_bootstrap_filter_nav_menu_args( $args ) {
	if ( doing_action( 'enlightenment_after_page_header' ) || doing_action( 'enlightenment_before_page_content' ) ) {
		$args['container_class'] = 'secondary-navigation';
		$args['collapse'] = false;
	} elseif ( doing_action( 'enlightenment_site_footer' ) ) {
		$args['container_class'] = 'footer-navigation';
		$args['collapse'] = false;
	}

	return $args;
}
add_filter( 'enlightenment_nav_menu_args', 'enlightenment_bootstrap_filter_nav_menu_args', 24 );

function enlightenment_filter_nav_menu_link_attributes( $atts, $item ) {
	if ( doing_action( 'get_sidebar' ) ) {
		return $atts;
	}

	if ( defined( 'IFRAME_REQUEST' ) && true === IFRAME_REQUEST ) {
		return $atts;
	}

	$option = get_theme_mod( 'nav_menu_dropdown_toggle' );

	if ( 'click' != $option && isset( $atts['data-bs-toggle'] ) ) {
		unset( $atts['data-bs-toggle'] );
	}

	if ( 'separate' == $option ) {
		$atts['class'] = str_replace( ' dropdown-toggle', '', $atts['class'] );
	} elseif ( in_array( 'mega-menu', $item->classes ) ) {
		$atts['data-bs-display'] = 'static';
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'enlightenment_filter_nav_menu_link_attributes', 12, 2 );

function enlightenment_filter_submenu_class( $classes, $args, $depth ) {
	if ( doing_action( 'get_sidebar' ) ) {
		return $classes;
	}

	if ( defined( 'IFRAME_REQUEST' ) && true === IFRAME_REQUEST ) {
		return $classes;
	}

	if ( 0 === $depth ) {
		$classes[] = 'dropdown-menu-end';
	}

	return $classes;
}
add_filter( 'nav_menu_submenu_css_class', 'enlightenment_filter_submenu_class', 12, 3 );

function enlightenment_filter_submenu_extra_atts( $atts ) {
	if ( doing_action( 'get_sidebar' ) ) {
		return $atts;
	}

	if ( defined( 'IFRAME_REQUEST' ) && true === IFRAME_REQUEST ) {
		return $atts;
	}

	if ( ! enlightenment_is_navbar_inversed() ) {
		return $atts;
	}

	$atts .= ' data-bs-theme="light"';

	return $atts;
}
add_filter( 'enlightenment_submenu_extra_atts', 'enlightenment_filter_submenu_extra_atts' );

function enlightenment_filter_navicon_args( $args ) {
	$args['text'] = str_replace( '<span class="navbar-toggler-icon"></span>', '<i class="fas fa-bars" aria-hidden="true" role="presentation"></i>', $args['text'] );

	return $args;
}
add_filter( 'enlightenment_navicon_args', 'enlightenment_filter_navicon_args', 12 );

function enlightenment_filter_wp_nav_menu( $output, $args ) {
	if (
		doing_action( 'enlightenment_site_header' )
		&&
		enlightenment_has_in_call_stack( 'enlightenment_nav_menu' )
	) {
		$output .= '<div class="navbar-collapse-overlay"></div>';
	}

	return $output;
}
add_filter( 'wp_nav_menu', 'enlightenment_filter_wp_nav_menu', 10, 2 );

function enlightenment_filter_menu_parent_mega_menu_class( $items ) {
	if ( ! doing_action( 'enlightenment_site_header' ) ) {
		return $items;
	}

	if ( defined( 'IFRAME_REQUEST' ) && true === IFRAME_REQUEST ) {
		return $items;
	}

	$id_map = array();

	foreach ( $items as $key => $item ) {
		$id_map[ $item->ID ] = $key;
	}

	$parents = array();

	foreach ( $items as $key => $item ) {
		if ( $item->menu_item_parent && $item->menu_item_parent > 0 ) {
			$parents[ $id_map[ $item->menu_item_parent ] ] = get_post( $item->menu_item_parent );
		}
	}

	foreach ( $parents as $key => $parent ) {
		if( ! $parent->menu_item_parent || $parent->menu_item_parent == 0 ) {
			$children = array();

			foreach ( $items as $item ) {
				if( $item->menu_item_parent && $item->menu_item_parent == $parent->ID ) {
					$children[] = $item;
				}
			}

			$found = false;

			foreach ( $children as $child ) {
				foreach ( $items as $item ) {
					if( $item->menu_item_parent && $item->menu_item_parent == $child->ID ) {
						$items[ $key ]->classes[] = 'mega-menu';
						$found = true;
						break;
					}
				}

				if ( $found ) {
					break;
				}
			}
		}
	}

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'enlightenment_filter_menu_parent_mega_menu_class', 12 );

function enlightenment_filter_menu_parent_dropdown_toggle_class( $items ) {
	if ( doing_action( 'get_sidebar' ) ) {
		return $items;
	}

	if ( defined( 'IFRAME_REQUEST' ) && true === IFRAME_REQUEST ) {
		return $items;
	}

	$option = get_theme_mod( 'nav_menu_dropdown_toggle' );

	switch ( $option ) {
		case 'hover':
		case 'separate':
			foreach ( $items as $item ) {
				if ( in_array( 'dropdown', $item->classes ) ) {
					$item->classes[] = sprintf( 'dropdown-%s', $option );
				}
			}

			break;
	}

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'enlightenment_filter_menu_parent_dropdown_toggle_class', 12 );

function enlightenment_filter_walker_nav_menu_start_el( $output, $item, $depth, $args ) {
	if ( 0 == $depth && in_array( 'dropdown', $item->classes ) ) {
		$option = get_theme_mod( 'nav_menu_dropdown_toggle' );

		if ( 'separate' == $option ) {
			$output .= "\n";
			$output .= sprintf( '<a href="#" class="nav-link dropdown-toggle dropdown-toggle-split" role="button" aria-expanded="false" data-bs-toggle="dropdown"%s aria-haspopup="true">', ( in_array( 'mega-menu', $item->classes ) ? ' data-bs-display="static"' : '' ) );
			$output .= sprintf( '<span class="screen-reader-text visually-hidden">%s</span>', __( 'Toggle Dropdown', 'enlightenment' ) );
			$output .= '</a>';
		}
	}

	return $output;
}
add_filter( 'walker_nav_menu_start_el', 'enlightenment_filter_walker_nav_menu_start_el', 10, 4 );

function enlightenment_menu_item_icon_placeholder( $output, $item ) {
	if( empty( $item->icon ) && ! empty( $item->description ) ) {
		$output = sprintf( '<span class="menu-item-icon-placeholder"></span> %s', $output );
	}

	return $output;
}
add_filter( 'nav_menu_item_title', 'enlightenment_menu_item_icon_placeholder', 12, 2 );

function enlightenment_filter_search_form( $form ) {
	$search_form_template = locate_template( 'searchform.php' );

	if ( '' != $search_form_template ) {
		return $form;
	}

	return enlightenment_search_form( array( 'echo' => false ) );
}
add_filter( 'get_search_form', 'enlightenment_filter_search_form' );

function enlightenment_filter_search_form_args( $args ) {
	$args['placeholder']  = __( 'Type and press enter to search &#8230;', 'enlightenment' );
	$args['submit']       = sprintf( '<i class="fas fa-search" aria-hidden="true" role="presentation"></i> <span class="screen-reader-text visually-hidden">%s</span>', __( 'Search', 'enlightenment' ) );
	$args['submit_class'] = str_replace( ' btn-light', ' btn-theme-inverse', $args['submit_class'] );

	return $args;
}
add_filter( 'enlightenment_search_form_args', 'enlightenment_filter_search_form_args', 12 );

function enlightenment_filter_search_form_dropdown( $output ) {
	$output = str_replace( 'data-bs-toggle="dropdown"', 'data-bs-toggle="dropdown" data-bs-display="static"', $output );

	if ( enlightenment_is_navbar_inversed() ) {
		$output = str_replace(
			'<ul class="dropdown-menu dropdown-menu-end" role="menu" aria-labelledby="toggle-search-form">',
			'<ul class="dropdown-menu dropdown-menu-end" role="menu" aria-labelledby="toggle-search-form" data-bs-theme="light">',
			$output
		);
	}

	return $output;
}
add_filter( 'enlightenment_search_form', 'enlightenment_filter_search_form_dropdown', 12 );

function enlightenment_filter_color_mode_switcher_args( $args ) {
	$args['toggle_extra_atts']['data-bs-display'] = 'static';

	if ( enlightenment_is_navbar_inversed() ) {
		$args['dropdown_menu_extra_atts']['data-bs-theme'] = 'light';
	}

	return $args;
}
add_filter( 'enlightenment_bootstrap_color_mode_switcher_args', 'enlightenment_filter_color_mode_switcher_args' );

add_action( 'enlightenment_after_site_content', 'get_sidebar' );
add_action( 'enlightenment_before_site_footer', 'get_sidebar' );

function enlightenment_theme_template_functions( $functions ) {
	$functions['get_sidebar'] = __( 'Sidebar', 'enlightenment' );

	return $functions;
}
add_filter( 'enlightenment_template_functions', 'enlightenment_theme_template_functions' );

function enlightenment_theme_page_content_hooks( $hooks ) {
	$hooks['enlightenment_after_site_content']['functions'][] = 'get_sidebar';

	return $hooks;
}
add_filter( 'enlightenment_page_content_hooks', 'enlightenment_theme_page_content_hooks' );

function enlightenment_theme_site_footer_hooks( $hooks ) {
	$hooks['enlightenment_before_site_footer']['functions'][] = 'get_sidebar';

	$hooks['enlightenment_site_footer']['functions'][] = 'enlightenment_nav_menu';

	return $hooks;
}
add_filter( 'enlightenment_site_footer_hooks', 'enlightenment_theme_site_footer_hooks' );

function enlightenment_filter_default_archive_layouts( $layouts ) {
	$layouts['post'] = array(
		'smartphone-portrait'  => 'full-width',
		'smartphone-landscape' => 'inherit',
		'tablet-portrait'      => 'inherit',
		'tablet-landscape'     => 'inherit',
		'desktop-laptop'       => 'inherit',
	);

	return $layouts;
}
add_filter( 'enlightenment_archive_layouts', 'enlightenment_filter_default_archive_layouts' );

function enlightenment_bootstrap_remove_archive_title_prefix_filter() {
	remove_filter( 'get_the_archive_title', 'enlightenment_bootstrap_archive_title_prefix' );
}
add_action( 'after_setup_theme', 'enlightenment_bootstrap_remove_archive_title_prefix_filter', 40 );

function enlightenment_theme_singular_layout_hooks() {
	if ( ( is_singular( 'post' ) || is_page() ) && has_post_thumbnail() ) {
		if ( 'large' == get_theme_mod( 'single_post_thumbnail' ) ) {
			add_action( 'enlightenment_page_header', '__return_false', 999 );
		} elseif ( in_array( get_theme_mod( 'single_post_thumbnail' ), array( 'cover', 'full-screen' ) ) ) {
			remove_action( 'enlightenment_before_site_content', 'enlightenment_open_row', 998 );
			remove_action( 'enlightenment_before_site_content', 'enlightenment_bootstrap_open_page_content_container', 999 );

			remove_action( 'enlightenment_after_site_content', 'enlightenment_bootstrap_close_page_content_container', 1 );
			remove_action( 'enlightenment_after_site_content', 'get_sidebar' );
			remove_action( 'enlightenment_after_site_content', 'enlightenment_close_row', 11 );

			add_action( 'enlightenment_after_entry_header', 'enlightenment_open_row', 997 );
			add_action( 'enlightenment_after_entry_header', 'enlightenment_bootstrap_open_page_content_container', 998 );
			add_action( 'enlightenment_after_entry_header', 'enlightenment_entry_inner_open_container', 999 );

			add_action( 'enlightenment_after_entry_footer', 'enlightenment_close_div', 11 );
			add_action( 'enlightenment_after_entry_footer', 'enlightenment_bootstrap_close_page_content_container', 12 );
			add_action( 'enlightenment_after_entry_footer', 'get_sidebar', 13 );
			add_action( 'enlightenment_after_entry_footer', 'enlightenment_close_row', 14 );
		}
	}
}
add_action( 'wp', 'enlightenment_theme_singular_layout_hooks', 8 );

function enlightenment_theme_single_post_entry_hooks() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	remove_action( 'enlightenment_entry_header', 'enlightenment_entry_title' );
	remove_action( 'enlightenment_entry_header', 'enlightenment_entry_meta' );

	add_action( 'enlightenment_entry_header', 'enlightenment_entry_title' );
	add_action( 'enlightenment_entry_header', 'enlightenment_post_excerpt' );
	add_action( 'enlightenment_entry_header', 'enlightenment_entry_meta' );
	add_action( 'enlightenment_entry_header', 'enlightenment_post_thumbnail' );

	add_action( 'enlightenment_entry_footer', 'enlightenment_author_hcard' );
}
add_action( 'wp', 'enlightenment_theme_single_post_entry_hooks', 8 );

function enlightenment_theme_page_entry_hooks() {
	if ( ! is_page() ) {
		return;
	}

	remove_action( 'enlightenment_entry_header', 'enlightenment_entry_title' );

	add_action( 'enlightenment_entry_header', 'enlightenment_post_thumbnail' );
	add_action( 'enlightenment_entry_header', 'enlightenment_entry_title' );
	add_action( 'enlightenment_entry_header', 'enlightenment_post_excerpt' );
}
add_action( 'wp', 'enlightenment_theme_page_entry_hooks', 8 );

function enlightenment_theme_maybe_ob_start_page_header() {
	if ( has_action( 'enlightenment_page_header' ) ) {
		return;
	}

	ob_start();
}
add_action( 'enlightenment_before_page_header', 'enlightenment_theme_maybe_ob_start_page_header', 999 );

function enlightenment_theme_maybe_ob_end_clean_page_header() {
	if ( has_action( 'enlightenment_page_header' ) ) {
		return;
	}

	ob_end_clean();
}
add_action( 'enlightenment_after_page_header', 'enlightenment_theme_maybe_ob_end_clean_page_header', 0 );

add_filter( 'get_the_archive_title_prefix', '__return_false' );

function enlightenment_theme_entry_layout_hooks() {
	if (
		! is_singular()
		&&
		'post' == get_post_type()
		&&
		has_action( 'enlightenment_before_entry_header', 'enlightenment_post_thumbnail' )
	) {
		add_action( 'enlightenment_before_entry_header', 'enlightenment_open_row', 6 );
		add_action( 'enlightenment_before_entry_header', 'enlightenment_post_thumbnail_wrap_open_container', 8 );
		add_action( 'enlightenment_before_entry_header', 'enlightenment_close_div', 12 );
		add_action( 'enlightenment_before_entry_header', 'enlightenment_post_content_wrap_open_container', 14 );
		add_action( 'enlightenment_after_entry_footer', 'enlightenment_close_div', 12 );
		add_action( 'enlightenment_after_entry_footer', 'enlightenment_close_row', 14 );
	}
}
add_action( 'enlightenment_before_entry', 'enlightenment_theme_entry_layout_hooks', 999 );

function enlightenment_post_thumbnail_wrap_open_container() {
	$class   = '';
	$layouts = array_reverse( enlightenment_current_layout() );

	foreach ( $layouts as $breakpoint => $layout ) {
		if ( 'desktop-laptop' == $breakpoint ) {
			continue;
		}

		if ( 'tablet-landscape' == $breakpoint ) {
			continue;
		}

		if ( 'inherit' == $layout ) {
			continue;
		}

		break;
	}

	if ( 'full-width' == $layout ) {
		$class .= ' col-md-4';
	} else {
		$class .= ' col-md-5';
	}

	if ( 'inherit' != $layouts['tablet-landscape'] ) {
		if ( 'full-width' == $layouts['tablet-landscape'] ) {
			$class .= ' col-lg-4';
		} else {
			$class .= ' col-lg-5';
		}
	}

	if ( 'inherit' != $layouts['desktop-laptop'] ) {
		if ( 'full-width' == $layouts['desktop-laptop'] ) {
			$class .= ' col-xl-4';
		} else {
			$class .= ' col-xl-5';
		}
	}

	echo enlightenment_open_tag( 'div', $class );
}

function enlightenment_post_content_wrap_open_container() {
	$class   = '';
	$layouts = array_reverse( enlightenment_current_layout() );

	foreach ( $layouts as $breakpoint => $layout ) {
		if ( 'desktop-laptop' == $breakpoint ) {
			continue;
		}

		if ( 'tablet-landscape' == $breakpoint ) {
			continue;
		}

		if ( 'inherit' == $layout ) {
			continue;
		}

		break;
	}

	if ( 'full-width' == $layout ) {
		$class .= ' col-md-8';
	} else {
		$class .= ' col-md-7';
	}

	if ( 'inherit' != $layouts['tablet-landscape'] ) {
		if ( 'full-width' == $layouts['tablet-landscape'] ) {
			$class .= ' col-lg-8';
		} else {
			$class .= ' col-lg-7';
		}
	}

	if ( 'inherit' != $layouts['desktop-laptop'] ) {
		if ( 'full-width' == $layouts['desktop-laptop'] ) {
			$class .= ' col-xl-8';
		} else {
			$class .= ' col-xl-7';
		}
	}

	echo enlightenment_open_tag( 'div', $class );
}

function enlightenment_filter_post_class( $classes, $class, $post_id ) {
	if ( ( is_singular( 'post' ) || is_page() ) && has_post_thumbnail( $post_id ) ) {
		$option = get_theme_mod( 'single_post_thumbnail' );

		$classes[] = sprintf( 'single-post-thumbnail-%s', esc_attr( $option ) );

		if ( in_array( $option, array( 'large', 'cover', 'full-screen' ) ) ) {
			$classes[] = sprintf( 'cover-object-position-%s', esc_attr( get_theme_mod( 'header_image_position' ) ) );
		}
	}

	return $classes;
}
add_filter( 'post_class', 'enlightenment_filter_post_class', 10, 3 );

function enlightenment_entry_inner_open_container() {
	echo enlightenment_open_tag( 'div', 'entry-inner' );
}

function enlightenment_filter_post_thumbnail_args( $args ) {
	if ( is_page() ) {
		$args['size'] = 'full';
	}
	return $args;
}
add_filter( 'enlightenment_post_thumbnail_args', 'enlightenment_filter_post_thumbnail_args' );

function enlightenment_filter_post_thumbnail( $output ) {
	if ( function_exists( 'is_buddypress' ) && is_buddypress() ) {
		return $output;
	}

	if ( ! is_singular( 'post' ) && ! is_page() ) {
		return $output;
	}

	if ( ! ( in_the_loop() && get_queried_object()->ID == get_post()->ID ) ) {
		return $output;
	}

	if (
		has_post_thumbnail()
		&&
		(
			'large' == get_theme_mod( 'single_post_thumbnail' )
			||
			'none' == get_theme_mod( 'single_post_thumbnail' )
		)
	) {
		$output = '';
	}

	return $output;
}
add_filter( 'enlightenment_post_thumbnail', 'enlightenment_filter_post_thumbnail' );

function enlightenment_single_post_custom_excerpt( $output ) {
	if ( is_singular() && ( in_the_loop() && get_queried_object()->ID == get_post()->ID ) && ! has_excerpt() ) {
		return '';
	}

	return $output;
}
add_filter( 'enlightenment_post_excerpt', 'enlightenment_single_post_custom_excerpt' );

function enlightenment_filter_entry_meta_args( $args ) {
	if ( 'post' == get_post_type() ) {
		if ( is_singular( 'post' ) ) {
			if ( doing_action( 'enlightenment_entry_header' ) ) {
				$args['format'] = '%8$s %2$s %1$s';
			} elseif ( doing_action( 'enlightenment_entry_footer' ) ) {
				$args['format'] = __( 'Filed under %3$s %4$s', 'enlightenment' );
			}
		} else {
			if ( enlightenment_is_lead_post() ) {
				$args['format'] = __( 'Posted on %1$s by %2$s', 'enlightenment' );
			} else {
				$args['format'] = __( 'On %1$s by %2$s', 'enlightenment' );
			}
		}
	} elseif ( is_search() && 'page' == get_post_type() ) {
		$args['format'] = '';
		return $args;
	}

	return $args;
}
add_filter( 'enlightenment_entry_meta_args', 'enlightenment_filter_entry_meta_args' );

function enlightenment_filter_entry_meta_output( $output ) {
	if ( doing_action( 'enlightenment_entry_footer' ) ) {
		$output = sprintf( '<footer class="entry-footer">%s</footer>', $output );
	}

	return $output;
}
add_filter( 'enlightenment_entry_meta_output', 'enlightenment_filter_entry_meta_output' );

function enlightenment_filter_content_more_link( $output, $more_link_text ) {
	return str_replace( $more_link_text, $more_link_text . ' &rarr;', $output );
}
add_filter( 'the_content_more_link', 'enlightenment_filter_content_more_link', 10, 2 );

function enlightenment_filter_link_pages_args( $args ) {
    $args['previouspagelink'] = sprintf( '&larr; %s', __( 'Previous page', 'enlightenment' ) );
	$args['nextpagelink']     = sprintf( '%s &rarr;', __( 'Next page', 'enlightenment' ) );

	return $args;
}
add_filter( 'enlightenment_link_pages_args', 'enlightenment_filter_link_pages_args' );

function enlightenment_filter_author_avatar_args( $args ) {
	if ( doing_action( 'enlightenment_page_header' ) ) {
		$args['avatar_size']     = 128;
		$args['link_to_archive'] = false;
	} else {
		$args['avatar_size'] = 64;
	}

	return $args;
}
add_filter( 'enlightenment_author_avatar_args', 'enlightenment_filter_author_avatar_args' );

function enlightenment_filter_post_nav_args( $args ) {
	$args['prev_pointer']   = '<i class="fas fa-arrow-left"></i>';
	$args['next_pointer']   = '<i class="fas fa-arrow-right"></i>';
	$args['date_tag']       = 'span';
	$args['date_class']     = 'nav-meta nav-date';
	$args['prev_date_text'] = '%date';
	$args['next_date_text'] = '%date';
	$args['prev_format']    = '%2$s %3$s %4$s %1$s';
	$args['next_format']    = '%2$s %3$s %4$s %1$s';

	return $args;
}
add_filter( 'enlightenment_post_nav_args', 'enlightenment_filter_post_nav_args' );

function enlightenment_filter_list_comments_args( $args ) {
	$args['avatar_size'] = 48;

	return $args;
}
add_filter( 'wp_list_comments_args', 'enlightenment_filter_list_comments_args' );

function enlightenment_filter_cancel_comment_reply_link( $output ) {
	return str_replace( 'id="cancel-comment-reply-link"', 'id="cancel-comment-reply-link" class="btn btn-outline-secondary"', $output );
}
add_filter( 'cancel_comment_reply_link', 'enlightenment_filter_cancel_comment_reply_link' );

function enlightenment_filter_posts_nav_args( $args ) {
	$style = get_theme_mod( 'posts_nav_style' );

	if ( 'paginated' == $style ) {
		$args['label_class'] .= ' screen-reader-text visually-hidden';
		$args['paged']        = true;
	}

	$labels = get_theme_mod( 'posts_nav_labels' );

	switch ( $labels ) {
		case 'next-prev':
			$args['prev_text'] = __( 'Next page',     'enlightenment' );
			$args['next_text'] = __( 'Previous page', 'enlightenment' );

			break;

		case 'earlier-later':
			$args['prev_text'] = __( 'Earlier posts', 'enlightenment' );
			$args['next_text'] = __( 'Later posts',   'enlightenment' );

			break;
	}

	return $args;
}
add_filter( 'enlightenment_posts_nav_args', 'enlightenment_filter_posts_nav_args' );

function enlightenment_filter_comments_nav_args( $args ) {
	$args['paged'] = ( 'paginated' == get_theme_mod( 'comments_nav_style' ) );

	if ( $args['paged'] ) {
		$args['label_class'] .= ' screen-reader-text visually-hidden';
	}

	$labels = get_theme_mod( 'comments_nav_labels' );

	switch ( $labels ) {
		case 'next-prev':
			$args['prev_text'] = __( 'Next comments',     'enlightenment' );
			$args['next_text'] = __( 'Previous comments', 'enlightenment' );

			break;

		case 'earlier-later':
			$args['prev_text'] = __( 'Earlier comments', 'enlightenment' );
			$args['next_text'] = __( 'Later comments',   'enlightenment' );

			break;
	}

	return $args;
}
add_filter( 'enlightenment_comments_nav_args', 'enlightenment_filter_comments_nav_args' );

function enlightenment_filter_the_password_form( $output ) {
	return str_replace( '<input type="submit" class="btn btn-light"', '<input type="submit" class="btn btn-theme-inverse"', $output );
}
add_filter( 'the_password_form', 'enlightenment_filter_the_password_form', 12 );

function enlightenment_set_theme_local_web_fonts( $fonts ) {
	if ( isset( $fonts['Hind'] ) && empty( $fonts['Hind']['src'] ) ) {
		$fonts['Hind'] = array_merge( $fonts['Hind'], array(
			'subsetMap'    => array( 'latin' ),
			'lastModified' => '2022-04-26',
			'version'      => 'v16',
			'src'          => array(
				300 => array(
					'woff2' => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-300.woff2' ),
					'eot'   => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-300.eot' ),
					'woff'  => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-300.woff' ),
					'svg'   => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-300.svg' ),
					'ttf'   => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-300.ttf' ),
				),
				400 => array(
					'woff2' => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-regular.woff2' ),
					'eot'   => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-regular.eot' ),
					'woff'  => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-regular.woff' ),
					'svg'   => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-regular.svg' ),
					'ttf'   => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-regular.ttf' ),
				),
				500 => array(
					'woff2' => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-500.woff2' ),
					'eot'   => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-500.eot' ),
					'woff'  => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-500.woff' ),
					'svg'   => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-500.svg' ),
					'ttf'   => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-500.ttf' ),
				),
				600 => array(
					'woff2' => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-600.woff2' ),
					'eot'   => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-600.eot' ),
					'woff'  => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-600.woff' ),
					'svg'   => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-600.svg' ),
					'ttf'   => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-600.ttf' ),
				),
				700 => array(
					'woff2' => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-700.woff2' ),
					'eot'   => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-700.eot' ),
					'woff'  => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-700.woff' ),
					'svg'   => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-700.svg' ),
					'ttf'   => get_theme_file_uri( 'assets/fonts/hind/hind-v16-latin-700.ttf' ),
				),
			),
		) );
	}

	if ( isset( $fonts['PT Serif'] ) && empty( $fonts['PT Serif']['src'] ) ) {
		$fonts['PT Serif'] = array_merge( $fonts['PT Serif'], array(
			'subsetMap'    => array( 'latin' ),
			'lastModified' => '2022-04-27',
			'version'      => 'v17',
			'src'          => array(
				400 => array(
					'woff2' => get_theme_file_uri( 'assets/fonts/pt-serif/pt-serif-v17-latin-regular.woff2' ),
					'eot'   => get_theme_file_uri( 'assets/fonts/pt-serif/pt-serif-v17-latin-regular.eot' ),
					'woff'  => get_theme_file_uri( 'assets/fonts/pt-serif/pt-serif-v17-latin-regular.woff' ),
					'svg'   => get_theme_file_uri( 'assets/fonts/pt-serif/pt-serif-v17-latin-regular.svg' ),
					'ttf'   => get_theme_file_uri( 'assets/fonts/pt-serif/pt-serif-v17-latin-regular.ttf' ),
				),
				'italic' => array(
					'woff2' => get_theme_file_uri( 'assets/fonts/pt-serif/pt-serif-v17-latin-italic.woff2' ),
					'eot'   => get_theme_file_uri( 'assets/fonts/pt-serif/pt-serif-v17-latin-italic.eot' ),
					'woff'  => get_theme_file_uri( 'assets/fonts/pt-serif/pt-serif-v17-latin-italic.woff' ),
					'svg'   => get_theme_file_uri( 'assets/fonts/pt-serif/pt-serif-v17-latin-italic.svg' ),
					'ttf'   => get_theme_file_uri( 'assets/fonts/pt-serif/pt-serif-v17-latin-italic.ttf' ),
				),
				700 => array(
					'woff2' => get_theme_file_uri( 'assets/fonts/pt-serif/pt-serif-v17-latin-700.woff2' ),
					'eot'   => get_theme_file_uri( 'assets/fonts/pt-serif/pt-serif-v17-latin-700.eot' ),
					'woff'  => get_theme_file_uri( 'assets/fonts/pt-serif/pt-serif-v17-latin-700.woff' ),
					'svg'   => get_theme_file_uri( 'assets/fonts/pt-serif/pt-serif-v17-latin-700.svg' ),
					'ttf'   => get_theme_file_uri( 'assets/fonts/pt-serif/pt-serif-v17-latin-700.ttf' ),
				),
			),
		) );
	}

	return $fonts;
}
add_filter( 'enlightenment_web_fonts', 'enlightenment_set_theme_local_web_fonts', 12 );

function enlightenment_filter_sidebar_locations( $locations ) {
	$locations['footer'] = array(
		'name'      => __( 'Footer Sidebar', 'enlightenment' ),
		'contained' => false,
		'sidebar'   => '',
	);

	return $locations;
}
add_filter( 'enlightenment_sidebar_locations', 'enlightenment_filter_sidebar_locations', 0 );

function enlightenment_filter_current_sidebar_name( $sidebar ) {
	if ( doing_action( 'enlightenment_before_site_footer' ) ) {
		$sidebar = 'footer';
	}

	return $sidebar;
}
add_filter( 'enlightenment_current_sidebar_name', 'enlightenment_filter_current_sidebar_name' );

function enlightenment_theme_custom_query_widget_slide_permalink() {
	printf( '<a class="custom-query-slide-permalink" href="%s" aria-hidden="true"></a>', get_permalink() );
}

function enlightenment_theme_custom_query_widget_spinner() {
	echo '<i class="custom-query-slider-load-spinner fas fa-spinner fa-pulse" aria-hidden="true" role="presentation"></i>';
}

function enlightenment_theme_custom_query_widget_hooks( $query_name ) {
	if ( 0 !== strpos( $query_name, 'custom_query_widget_' ) ) {
		return;
	}

	global $enlightenment_custom_widget_instance;

	$instance = $enlightenment_custom_widget_instance;

	if ( 'list' == $instance['type'] && 'page' != $instance['query'] && 'pages' != $instance['query'] ) {
		remove_action( 'enlightenment_custom_entry_header', 'enlightenment_post_thumbnail' );

		add_action( 'enlightenment_custom_before_entry_header', 'enlightenment_post_thumbnail' );
	}

	if ( 'gallery' != $instance['query'] && ( 'slider' == $instance['type'] || 'carousel' == $instance['type'] ) ) {
		add_action( 'enlightenment_custom_after_entry_footer', 'enlightenment_theme_custom_query_widget_slide_permalink', 999 );
	}

	if ( 'slider' == $instance['type'] ) {
		add_action( 'enlightenment_custom_after_entries_list', 'enlightenment_theme_custom_query_widget_spinner', 999 );
	}
}
add_action( 'enlightenment_before_custom_loop', 'enlightenment_theme_custom_query_widget_hooks', 12 );

function enlightenment_theme_custom_query_small_thumb_size( $size ) {
	return 160;
}
add_filter( 'enlightenment_custom_query_small_thumb_size', 'enlightenment_theme_custom_query_small_thumb_size' );

function enlightenment_filter_widget_rss( $output ) {
    return str_replace(
		sprintf(
			'<img class="rss-widget-icon" style="border:0" width="14" height="14" src="%s" alt="RSS" />',
			esc_url( includes_url( 'images/rss.png' ) )
		),
		'<i class="fas fa-rss"></i>',
		$output
	);
}
add_filter( 'enlightenment_widget_rss', 'enlightenment_filter_widget_rss' );

function enlightenment_filter_site_footer_extra_atts_args( $args ) {
	$style = get_theme_mod( 'site_footer_style' );

	if ( in_array( $style, array( 'light', 'dark' ) ) ) {
	    $args['atts'] .= sprintf( ' data-bs-theme="%s"', esc_attr( $style ) );
	}

    return $args;
}
add_filter( 'enlightenment_site_footer_extra_atts_args', 'enlightenment_filter_site_footer_extra_atts_args' );

function enlightenment_filter_copyright_notice_args( $args ) {
	$args['format'] = 'Copyright &copy; %1$s %2$s';

	return $args;
}
add_filter( 'enlightenment_copyright_notice_args', 'enlightenment_filter_copyright_notice_args' );

function enlightenment_filter_copyright_notice( $output ) {
	$output = sprintf( '<div class="site-footer-sep flex-fill w-100"></div>%s', $output );
	return $output;
}
add_filter( 'enlightenment_copyright_notice', 'enlightenment_filter_copyright_notice' );

function enlightenment_filter_credit_links_args( $args ) {
	$theme_credit_link     = get_theme_mod( 'theme_credit_link' );
	$author_credit_link    = get_theme_mod( 'author_credit_link' );
	$wordpress_credit_link = get_theme_mod( 'wordpress_credit_link' );

	if ( $theme_credit_link && $author_credit_link && $wordpress_credit_link ) {
		$format = __( 'Powered by %1$s by %2$s and %3$s', 'enlightenment' );
	} elseif ( $theme_credit_link && $author_credit_link && ! $wordpress_credit_link ) {
		$format = __( 'Powered by %1$s by %2$s', 'enlightenment' );
	} elseif ( $theme_credit_link && ! $author_credit_link && $wordpress_credit_link ) {
		$format = __( 'Powered by %1$s and %3$s', 'enlightenment' );
	} elseif ( ! $theme_credit_link && $author_credit_link && $wordpress_credit_link ) {
		$format = __( 'Designed by %2$s and powered by %3$s', 'enlightenment' );
	} elseif ( $theme_credit_link && ! $author_credit_link && ! $wordpress_credit_link ) {
		$format = __( 'Powered by %1$s', 'enlightenment' );
	} elseif ( ! $theme_credit_link && $author_credit_link && ! $wordpress_credit_link ) {
		$format = __( 'Designed by %2$s', 'enlightenment' );
	} elseif ( ! $theme_credit_link && ! $author_credit_link && $wordpress_credit_link ) {
		$format = __( 'Powered by %3$s', 'enlightenment' );
	} else {
		$format = '';
	}

	$args['format'] = sprintf(
		$format,
		sprintf(
			'<a href="%s" rel="designer">%s</a>',
			esc_url( __( 'https://onedesigns.com/themes/enlightenment/', 'enlightenment' ) ),
			__( 'Enlightenment Theme', 'enlightenment' )
		),
		sprintf(
			'<a href="%s" rel="designer">%s</a>',
			esc_url( __( 'https://onedesigns.com/', 'enlightenment' ) ),
			__( 'One Designs', 'enlightenment' )
		),
		sprintf(
			'<a href="%s" rel="generator">%s</a>',
			esc_url( __( 'https://wordpress.org/', 'enlightenment' ) ),
			__( 'WordPress', 'enlightenment' )
		)
	);

	return $args;
}
add_filter( 'enlightenment_credit_links_args', 'enlightenment_filter_credit_links_args' );

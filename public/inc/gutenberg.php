<?php

function enlightenment_gutenberg_theme_stylesheet_deps( $deps ) {
	if ( isset( wp_styles()->registered['wp-block-library'] ) ) {
		$deps[] = 'wp-block-library';
	}

	if ( isset( wp_styles()->registered['wc-block-style'] ) ) {
		$deps[] = 'wc-block-style';
	}

	return $deps;
}
add_filter( 'enlightenment_theme_stylesheet_deps', 'enlightenment_gutenberg_theme_stylesheet_deps' );

function enlightenment_theme_register_gutenberg_styles() {
    wp_register_style( 'enlightenment-core-blocks', get_theme_file_uri( 'assets/css/core-blocks.css' ), array( 'enlightenment-theme-stylesheet' ), null );
}
add_action( 'wp_enqueue_scripts', 'enlightenment_theme_register_gutenberg_styles', 8 );

function enlightenment_enqueue_gutenberg_styles() {
	wp_enqueue_style( 'enlightenment-core-blocks' );
}
add_action( 'wp_enqueue_scripts', 'enlightenment_enqueue_gutenberg_styles', 12 );

function enlightenment_gutenberg_post_excerpt( $output, $post ) {
	if (
		has_filter( 'excerpt_length', 'block_core_latest_posts_get_excerpt_length' ) &&
		55 >= block_core_latest_posts_get_excerpt_length()
	) {
		$output .= sprintf( ' <a href="%s">%s</a>', get_permalink( $post ), __( 'Read more', 'enlightenment' ) );
	}

	return $output;
}
add_filter( 'get_the_excerpt', 'enlightenment_gutenberg_post_excerpt', 10, 2 );

function enlightenment_filter_cover_block( $output ) {
	$output = str_replace( 'class="wp-block-button btn-theme ', 'class="wp-block-button btn-light ', $output );
	$output = str_replace( 'class="wp-block-button btn-theme"', 'class="wp-block-button btn-light"', $output );
	$output = str_replace( 'class="btn btn-theme ', 'class="btn btn-light ', $output );
	$output = str_replace( 'class="btn btn-theme"', 'class="btn btn-light"', $output );
	$output = str_replace( 'class="wp-block-button btn-outline-theme ', 'class="wp-block-button btn-outline-light ', $output );
	$output = str_replace( 'class="wp-block-button btn-outline-theme"', 'class="wp-block-button btn-outline-light"', $output );
	$output = str_replace( 'class="btn btn-outline-theme ', 'class="btn btn-outline-light ', $output );
	$output = str_replace( 'class="btn btn-outline-theme"', 'class="btn btn-outline-light"', $output );

	return $output;
}
add_filter( 'enlightenment_render_block_core_cover', 'enlightenment_filter_cover_block', 12 );

function enlightenment_filter_button_block( $output, $block ) {
	if ( isset( $block['attrs'] ) && isset( $block['attrs']['fontSize'] ) && 'small' == $block['attrs']['fontSize'] ) {
		return $output;
	}

	$output = str_replace( 'class="wp-block-button btn-secondary ', 'class="wp-block-button btn-theme ', $output );
	$output = str_replace( 'class="wp-block-button btn-secondary"', 'class="wp-block-button btn-theme"', $output );
	$output = str_replace( 'class="btn btn-secondary ', 'class="btn btn-theme ', $output );
	$output = str_replace( 'class="btn btn-secondary"', 'class="btn btn-theme"', $output );
	$output = str_replace( 'class="wp-block-button btn-outline-secondary ', 'class="wp-block-button btn-outline-theme ', $output );
	$output = str_replace( 'class="wp-block-button btn-outline-secondary"', 'class="wp-block-button btn-outline-theme"', $output );
	$output = str_replace( 'class="btn btn-outline-secondary ', 'class="btn btn-outline-theme ', $output );
	$output = str_replace( 'class="btn btn-outline-secondary"', 'class="btn btn-outline-theme"', $output );

	if ( ! isset( $block['attrs'] ) || ! isset( $block['attrs']['fontSize'] ) || 'small' != $block['attrs']['fontSize'] ) {
		$output = str_replace( 'class="btn btn-theme ', 'class="btn btn-theme btn-lg ', $output );
		$output = str_replace( 'class="btn btn-theme"', 'class="btn btn-theme btn-lg"', $output );
		$output = str_replace( 'class="btn btn-outline-theme ', 'class="btn btn-outline-theme btn-lg ', $output );
		$output = str_replace( 'class="btn btn-outline-theme"', 'class="btn btn-outline-theme btn-lg"', $output );
	}

	return $output;
}
add_filter( 'enlightenment_render_block_core_button', 'enlightenment_filter_button_block', 12, 2 );

function enlightenment_filter_bootstrap_search_block( $output ) {
	$output = str_replace( 'class="wp-block-search__button ', 'class="wp-block-search__button btn btn-theme-inverse ', $output );
    $output = str_replace( 'class="wp-block-search__button"', 'class="wp-block-search__button btn btn-theme-inverse"', $output );

	return $output;
}
add_filter( 'enlightenment_render_block_core_search', 'enlightenment_filter_bootstrap_search_block', 12 );

function enlightenment_filter_audio_block( $output ) {
    $start = strpos( $output, '<audio ' );

	if ( false !== $start ) {
		$offset = strpos( $output, 'class="', $start );
		$end    = strpos( $output, '>', $start );

		if ( false !== $offset ) {
			$output = substr_replace( $output, 'wp-audio-shortcode ', $offset + 7, 0 );
		} else {
			$output = substr_replace( $output, 'class="wp-audio-shortcode" ', $start + 7, 0 );
		}

		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );
	}

    return $output;
}
add_filter( 'enlightenment_render_block_core_audio', 'enlightenment_filter_audio_block', 12 );

function enlightenment_filter_video_block( $output ) {
    $start = strpos( $output, '<video ' );

	if ( false !== $start ) {
		$offset = strpos( $output, 'class="', $start );
		$end    = strpos( $output, '>', $start );

		if ( false !== $offset ) {
			$output = substr_replace( $output, 'wp-video-shortcode ', $offset + 7, 0 );
		} else {
			$output = substr_replace( $output, 'class="wp-video-shortcode" ', $start + 7, 0 );
		}

		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );
	}

    return $output;
}
add_filter( 'enlightenment_render_block_core_video', 'enlightenment_filter_video_block', 12 );

function enlightenment_filter_bootstrap_column_block( $output ) {
	if ( false !== strpos( $output, 'class="wp-block-column-inner"' ) ) {
		return $output;
	}

	if ( false !== strpos( $output, 'class="wp-block-column-inner ' ) ) {
		return $output;
	}

	if ( false !== strpos( $output, ' wp-block-column-inner"' ) ) {
		return $output;
	}

	if ( false !== strpos( $output, ' wp-block-column-inner ' ) ) {
		return $output;
	}

	$offset = strpos( $output, 'class="wp-block-column"' );
	if ( false === $offset ) {
		$offset = strpos( $output, 'class="wp-block-column ' );

		if ( false === $offset ) {
			$offset = strpos( $output, ' wp-block-column"' );

			if ( false === $offset ) {
				$offset = strpos( $output, ' wp-block-column ' );
			}
		}
	}
	if ( false !== $offset ) {
		$offset = strpos( $output, '>', $offset );
		$output = substr_replace( $output, '<div class="wp-block-column-inner">', $offset + 1, 0 );
		$offset = strrpos( $output, '</', $offset );
		$output = substr_replace( $output, '</div>', $offset, 0 );
	}

    return $output;
}
add_filter( 'enlightenment_render_block_core_column', 'enlightenment_filter_bootstrap_column_block', 8 );

function enlightenment_filter_latest_posts_block( $output, $block ) {
	if (
		isset( $block['attrs'] ) &&
		isset( $block['attrs']['displayPostDate'] ) && true === $block['attrs']['displayPostDate'] &&
		isset( $block['attrs']['displayAuthor'] )   && true === $block['attrs']['displayAuthor']
	) {
		$offset = strpos( $output, '<div class="wp-block-latest-posts__post-author">' );
		while ( false !== $offset ) {
			$output = substr_replace( $output, '<div class="wp-block-latest-posts__post-author-date-wrap">', $offset, 0 );
			$offset = strpos( $output, '</time>', $offset );
			$output = substr_replace( $output, '</div>', $offset + 7, 0 );

			$offset = strpos( $output, '<div class="wp-block-latest-posts__post-author">', $offset );
		}
	}

	return $output;
}
add_filter( 'enlightenment_render_block_core_latest_posts', 'enlightenment_filter_latest_posts_block', 12, 2 );

function enlightenment_theme_filter_navigation_link_block( $output, $block ) {
	if ( isset( $block['attrs'] ) && isset( $block['attrs']['label'] ) && false === strpos( $output, '<span class="wp-block-navigation-item__label">' ) ) {
		$label  = trim( strip_tags( $block['attrs']['label'] ) );
		$output = str_replace( $label, sprintf( '<span class="wp-block-navigation-item__label">%s</span>', $label ), $output );

		$offset = strpos( $output, 'aria-label="<span class="wp-block-navigation-item__label">' );
		if ( false !== $offset ) {
			$output = substr_replace( $output, '', $offset + 12, 46 );
			$offset = strpos( $output, '</span>', $offset );
			$output = substr_replace( $output, '', $offset, 7 );
		}
	}

	return $output;
}
add_filter( 'enlightenment_render_block_core_navigation_link', 'enlightenment_theme_filter_navigation_link_block', 12, 2 );
add_filter( 'enlightenment_render_block_core_navigation_submenu', 'enlightenment_theme_filter_navigation_link_block', 12, 2 );

function enlightenment_filter_block_yoast_seo_breadcrumbs( $output ) {
    $offset = strpos( $output, 'class="col-' );
	if ( false === $offset ) {
		$offset = strpos( $output, 'class="wp-container-' );
		if ( false !== $offset ) {
			$end    = strpos( $output, '"', $offset + 7 );
			$offset = strpos( $output, ' col-', $offset + 7 );

			if ( $offset > $end ) {
				$offset = false;
			}
		}
	}
	if ( false !== $offset ) {
		$offset  = strpos( $output, '>', $offset );
		$output  = substr_replace( $output, '<div class="wp-block-column-inner">', $offset + 1, 0 );
		$output .= '</div>';
	}

    return sprintf( '<div class="wp-block-yoast-breadcrumbs">%s</div>', $output );
}
add_filter( 'enlightenment_render_block_yoast_seo_breadcrumbs', 'enlightenment_filter_block_yoast_seo_breadcrumbs', 12 );

function enlightenment_enqueue_block_assets() {
	global $post;

	if ( ! is_admin() ) {
		return;
	}

	// We are not editing a post
	if ( ! $post instanceof WP_Post ) {
		return;
	}

	if ( ! post_type_supports( $post->post_type, 'custom-fields' ) ) {
		return;
	}

	// Elementor landing pages short circuit the theme's template system so
	// there's no need for a header style panel.
	if ( 'e-landing-page' == $post->post_type ) {
		return;
	}

	// Elementor floating elements are displayed off-canvas so
	// there's no need for a header style panel.
	if ( 'e-floating-buttons' == $post->post_type ) {
		return;
	}

	if ( ! get_post_type_object( $post->post_type )->public ) {
		return;
	}

	wp_enqueue_style(
		'enlightenment-theme-block-editor',
		get_theme_file_uri( 'assets/css/block-editor.css' )
	);
}
add_action( 'enqueue_block_assets', 'enlightenment_enqueue_block_assets' );

function enlightenment_enqueue_block_editor_assets() {
	global $post;

	// We are not editing a post
	if ( ! $post instanceof WP_Post ) {
		return;
	}

	if ( ! post_type_supports( $post->post_type, 'custom-fields' ) ) {
		return;
	}

	// Elementor landing pages short circuit the theme's template system so
	// there's no need for a header style panel.
	if ( 'e-landing-page' == $post->post_type ) {
		return;
	}

	// Elementor floating elements are displayed off-canvas so
	// there's no need for a header style panel.
	if ( 'e-floating-buttons' == $post->post_type ) {
		return;
	}

	if ( ! get_post_type_object( $post->post_type )->public ) {
		return;
	}

	if ( in_array( $post->post_type, array( 'post', 'page' ) ) ) {
		wp_enqueue_style(
			'enlightenment-theme-editor-panels',
			get_theme_file_uri( 'assets/css/editor-panels.css' ),
	        array( 'enlightenment-editor-panels' )
		);

		wp_enqueue_script(
	        'enlightenment-theme-editor-panels',
	        get_theme_file_uri( 'assets/js/editor-panels.js' ),
	        array( 'wp-plugins', 'wp-edit-post', 'wp-components', 'wp-compose', 'wp-data', 'wp-element' )
	    );

		$image = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="currentColor" d="M0 0h100v100H0z"/></svg>';

		wp_localize_script( 'enlightenment-theme-editor-panels', 'enlightenment_editor_panels_args', array(
			'header_style' => array(
				'panel_title'        => __( 'Header Style', 'enlightenment' ),
				'post_type'          => $post->post_type,
				'navbar_transparent' => array(
					'label'       => __( 'Force transparent navbar', 'enlightenment' ),
					'description' => __( 'If the first item in the content is aligned full, it will slide under the navbar.', 'enlightenment' ),
				),
				'image_style'        => array(
					'label'       => __( 'Featured image style', 'enlightenment' ),
					'options'     => array(
						array(
							'value' => '',
							'label' => __( 'Use global image style', 'enlightenment' ),
							'image' => '',
						),
			            array(
			                'value' => 'normal',
			                'label' => __( 'Normal', 'enlightenment' ),
			                'image' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="currentColor" d="M25 9v3.5h50V9H25zm2.764 9.1A2.8 2.8 0 0025 20.9a2.8 2.8 0 002.8 2.8 2.8 2.8 0 002.8-2.8 2.8 2.8 0 00-2.8-2.8 2.8 2.8 0 00-.036 0zM34 19.7v2.2h14.6v-2.2H34zm-9 9.6v30.9h50V29.3H25zm0 36.5V68h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h30.9V73H25zm0 5.7v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h30.9v-2.2H25zm0 5.7v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h30.9v-2.2H25z"/></svg>',

			            ),
			            array(
			                'value' => 'medium',
			                'label' => __( 'Medium', 'enlightenment' ),
			                'image' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="currentColor" d="M25 9v3.5h50V9H25zm2.764 9.1A2.8 2.8 0 0025 20.9a2.8 2.8 0 002.8 2.8 2.8 2.8 0 002.8-2.8 2.8 2.8 0 00-2.8-2.8 2.8 2.8 0 00-.036 0zM34 19.7v2.2h14.6v-2.2H34zm-22.5 9.6v47.6h77V29.3h-77zM25 82.5v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h30.9v-2.2H25zm0 5.7v2.2h50v-2.2H25zm0 3.6v2.2h50V99H25z"/></svg>',
			            ),
			            array(
			                'value' => 'large',
			                'label' => __( 'Large', 'enlightenment' ),
			                'image' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="currentColor" d="M0 0v38.82h100V0H0zm25 47.2v3.5h50v-3.5H25zm2.764 9.1A2.8 2.8 0 0025 59.1a2.8 2.8 0 002.8 2.8 2.8 2.8 0 002.8-2.8 2.8 2.8 0 00-2.8-2.8 2.8 2.8 0 00-.036 0zM34 58v2.2h14.6V58H34zm-9 9.5v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h30.9v-2.2H25zm0 5.7v2.2h50v-2.2H25zm0 3.6v2.2h50V84H25zm0 3.6v2.2h30.9v-2.2H25zm0 5.7v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25z"/></svg>',
			            ),
			            array(
			                'value' => 'cover',
			                'label' => __( 'Cover', 'enlightenment' ),
			                'image' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="currentColor" d="M0 0v61.8h100V0H0zm25 38.2h50v3.5H25v-3.5zm2.764 9a2.8 2.8 0 01.037 0A2.8 2.8 0 0130.6 50a2.8 2.8 0 01-2.8 2.8A2.8 2.8 0 0125 50a2.8 2.8 0 012.764-2.8zM34 48.7h14.6v2.2H34v-2.2zm-9 22.1V73h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h30.9V78H25zm0 5.7v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h30.9v-2.2H25zm0 5.7v2.2h50v-2.2H25z"/></svg>',
			            ),
			            array(
			                'value' => 'full-screen',
			                'label' => __( 'Full Screen', 'enlightenment' ),
			                'image' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="currentColor" d="M0 0v100h100V0H0zm25 65h50v3.5H25V65zm0 9h50v2.2H25V74zm0 3.6h30.9v2.2H25v-2.2zm2.764 7.8a2.8 2.8 0 01.037 0 2.8 2.8 0 012.799 2.8 2.8 2.8 0 01-2.8 2.8 2.8 2.8 0 01-2.8-2.8 2.8 2.8 0 012.764-2.8zM34 87.1h14.6v2.2H34v-2.2z"/></svg>',
			            ),
			            array(
			                'value' => 'none',
			                'label' => __( 'None', 'enlightenment' ),
			                'image' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="currentColor" d="M25 9v3.5h50V9H25zm2.764 9.1A2.8 2.8 0 0025 20.9a2.8 2.8 0 002.8 2.8 2.8 2.8 0 002.8-2.8 2.8 2.8 0 00-2.8-2.8 2.8 2.8 0 00-.036 0zM34 19.7v2.2h14.6v-2.2H34zm-9 9.6v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h30.9v-2.2H25zm0 5.7V48h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h50V53H25zm0 3.6v2.2h30.9v-2.2H25zm0 5.7v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h30.9v-2.2H25zm0 5.7V81h50v-2.2H25zm0 3.6v2.2h50v-2.2H25zm0 3.6v2.2h50V86H25zm0 3.6v2.2h30.9v-2.2H25zm0 5.7v2.2h50v-2.2H25zm0 3.6v2.2h50v-2.2H25z"/></svg>',
			            ),
			        ),
				),
				'image_position'     => array(
					'label'             => __( 'Header image position',     'enlightenment' ),
					'toggle'            => __( 'Use global image position', 'enlightenment' ),
					'template_position' => get_theme_mod( 'header_image_position' ),
				),
				'header_overlay'     => array(
					'label'            => __( 'Header image overlay', 'enlightenment' ),
					'toggle'           => __( 'Use global overlay settings', 'enlightenment' ),
					'template_overlay' => get_theme_mod( 'header_overlay_color' ),
				),
			),
		) );
	}
}
add_action( 'enqueue_block_editor_assets', 'enlightenment_enqueue_block_editor_assets' );

function enlightenment_theme_block_editor_custom_css( $custom_css ) {
	if ( true == get_theme_mod( 'underline_links' ) ) {
		$custom_css .= "\n:root {\n\t--enlightenment-link-text-decoration-line: underline;\n}\n";
	}

	$custom_css .= enlightenment_custom_typography_editor_styles();

	return $custom_css;
}
add_filter( 'enlightenment_block_editor_custom_css', 'enlightenment_theme_block_editor_custom_css' );

<?php
/**
 * Jetpack Compatibility File
 *
 * @link https://jetpack.com/
 *
 * @package Enlightenment_Framework
 * @subpackage Enlightenment_Theme
 */

function enlightenment_jetpack_default_theme_mods( $mods ) {
	return array_merge( $mods, array(
		'portfolio_hover_effect' => 'fade',
	) );
}
add_filter( 'enlightenment_default_theme_mods', 'enlightenment_jetpack_default_theme_mods' );

function enlightenment_jetpack_styles() {
	wp_enqueue_style( 'enlightenment-jetpack', get_theme_file_uri( 'assets/css/jetpack.css' ), array( 'enlightenment-theme-stylesheet' ), null );
	wp_enqueue_style( 'enlightenment-jetpack-blocks', get_theme_file_uri( 'assets/css/jetpack-blocks.css' ), array( 'enlightenment-core-blocks' ), null );
}
add_action( 'wp_enqueue_scripts', 'enlightenment_jetpack_styles', 30 );

function enlightenment_jetpack_remove_sharing_display_on_singular() {
	if ( ! is_singular() ) {
		return;
	}

	if ( 19 != has_filter( 'the_excerpt', 'sharing_display' ) ) {
		return;
	}

	remove_filter( 'the_excerpt', 'sharing_display', 19 );
}
add_action( 'wp', 'enlightenment_jetpack_remove_sharing_display_on_singular' );

function enlightenment_filter_portfolio_body_class( $classes ) {
	if (
		(
			is_post_type_archive( 'jetpack-portfolio' ) ||
			is_tax( 'jetpack-portfolio-type' ) ||
			is_tax( 'jetpack-portfolio-tag' )
		)
		&&
		'fade' == get_theme_mod( 'portfolio_hover_effect' )
	) {
		$classes[] = 'entry-elements-fade';
	}

	return $classes;
}
add_filter( 'body_class', 'enlightenment_filter_portfolio_body_class' );

function enlightenment_filter_project_types_args( $args ) {
	if ( ! is_singular( 'jetpack-portfolio' ) ) {
		$args['format'] = '%s';
	}

	return $args;
}
add_filter( 'enlightenment_project_types_args', 'enlightenment_filter_project_types_args' );

function enlightenment_filter_portfolio_nav_args( $args ) {
	if ( ! is_post_type_archive( 'jetpack-portfolio' ) && ! is_tax( 'jetpack-portfolio-type' ) && ! is_tax( 'jetpack-portfolio-tag' ) ) {
		return $args;
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
add_filter( 'enlightenment_posts_nav_args', 'enlightenment_filter_portfolio_nav_args', 12 );

function enlightenment_custom_query_widget_project_types_args( $args ) {
	$args['format'] = '%s';

	return $args;
}

function enlightenment_hook_custom_project_types_args_filter() {
	add_filter( 'enlightenment_project_types_args', 'enlightenment_custom_query_widget_project_types_args' );
}
add_action( 'enlightenment_custom_entry_header', 'enlightenment_hook_custom_project_types_args_filter', 8 );

function enlightenment_unhook_custom_project_types_args_filter() {
	remove_filter( 'enlightenment_project_types_args', 'enlightenment_custom_query_widget_project_types_args' );
}
add_action( 'enlightenment_custom_entry_header', 'enlightenment_unhook_custom_project_types_args_filter', 12 );

function enlightenment_filter_jetpack_testimonials_shortcode_tag( $output, $atts ) {
	if ( ! is_array( $atts ) ) {
		$atts = array();
	}

	$image = isset( $atts['image'] ) && 'true' != $atts['image'] ? false : true;

    if ( ! $image ) {
		return $output;
	}

    $offset = strpos( $output, '<div class="testimonial-entry has-testimonial-thumbnail">' );
    while ( false !== $offset ) {
        $output = substr_replace( $output, "\n" . '<div class="d-flex align-items-lg-center">' . "\n" . '<div class="flex-grow-0 flex-shrink-1">', $offset + 57, 0 );
        $offset = strpos( $output, '<a class="testimonial-featured-image"', $offset );
        $output = substr_replace( $output, '</div>' . "\n" . '<div class="flex-grow-1 flex-shrink-0 order-first">' . "\n", $offset, 0 );
        $offset = strpos( $output, '</a>', $offset );
        $output = substr_replace( $output, "\n" . '</div>' . "\n" . '</div>', $offset + 4, 0 );

        $offset = strpos( $output, '<div class="testimonial-entry has-testimonial-thumbnail">', $offset );
    }

	return $output;
}
add_filter( 'enlightenment_filter_shortcode_tag_testimonials', 'enlightenment_filter_jetpack_testimonials_shortcode_tag', 12, 2 );
add_filter( 'enlightenment_filter_shortcode_tag_jetpack_testimonials', 'enlightenment_filter_jetpack_testimonials_shortcode_tag', 12, 2 );

function enlightenment_filter_project_nav_link_args( $args ) {
	$args['project_type_tag']   = 'span';
	$args['project_type_class'] = 'nav-meta nav-project-type';

	return $args;
}
add_filter( 'enlightenment_project_nav_link_args', 'enlightenment_filter_project_nav_link_args' );

function enlightenment_filter_jetpack_relatedposts_thumbnail_size( $size ) {
	return array(
		'width'  => 454,
		'height' => 260,
	);
}
add_filter( 'jetpack_relatedposts_filter_thumbnail_size', 'enlightenment_filter_jetpack_relatedposts_thumbnail_size' );

function enlightenment_filter_jetpack_display_posts_widget( $output, $widget, $instance ) {
	$image   = ( isset( $instance['featured_image'] ) && true == $instance['featured_image'] );
	$excerpt = ( isset( $instance['show_excerpts'] )  && true == $instance['show_excerpts'] );

	$start = strpos( $output, '<div class="jetpack-display-remote-posts">' );
	if ( false !== $start ) {
		$end    = strpos( $output, '</div><!-- .jetpack-display-remote-posts -->', $start );
		$offset = strpos( $output, '<h4>', $start );

		while ( false !== $offset && $offset < $end ) {
			$output = substr_replace( $output, ' class="jetpack-display-remote-post-title"', $offset + 3, 0 );
			$output = substr_replace( $output, '<div class="jetpack-display-remote-post">' . "\n", $offset, 0 );
			$offset = strpos( $output, '</h4>', $offset ) + 5;

			if ( $image ) {
				$end_a = strpos( $output, '<h4>', $offset );
				$end   = strpos( $output, '</div><!-- .jetpack-display-remote-posts -->', $start );

				if ( false === $end_a || $end_a > $end ) {
					$end_a = $end;
				}

				$offset_a = strpos( $output, '<img ', $offset );

				if ( false !== $offset_a && $offset_a < $end_a ) {
					$offset  = strrpos( $output, '<a ', $offset_a - strlen( $output ) );
					$output  = substr_replace( $output, '<div class="jetpack-display-remote-post-image">' . "\n", $offset, 0 );
					$offset  = strpos( $output, '</a>', $offset ) + 4;
					$output  = substr_replace( $output, "\n" . '</div>', $offset, 0 );
					$offset += 7;
				}
			}

			if ( $excerpt ) {
				$output = substr_replace( $output, "\n" . '<div class="jetpack-display-remote-post-except">', $offset, 0 );

				$offset_a = strpos( $output, '<h4>', $offset );
				$end_a    = strpos( $output, '</div><!-- .jetpack-display-remote-posts -->', $offset );
				if ( false !== $offset_a && $offset_a < $end_a ) {
					$output = substr_replace( $output, '</div>' . "\n", $offset_a, 0 );
					$offset = $offset_a + 7;
				} else {
					$output = substr_replace( $output, '</div>' . "\n", $end_a, 0 );
					$offset = $end_a + 7;
				}
			}

			$output = substr_replace( $output, '</div>' . "\n", $offset, 0 );

			$end    = strpos( $output, '</div><!-- .jetpack-display-remote-posts -->', $start );
			$offset = strpos( $output, '<h4>', $offset );
		}
	}

	return $output;
}
add_filter( 'enlightenment_widget_jetpack_display_posts_widget', 'enlightenment_filter_jetpack_display_posts_widget', 10, 3 );

function enlightenment_filter_blog_subscription_widget( $output ) {
	return str_replace( 'class="btn btn-light"', 'class="btn btn-theme-inverse"', $output );
}
add_filter( 'enlightenment_widget_blog_subscription', 'enlightenment_filter_blog_subscription_widget', 12 );

function enlightenment_filter_grofile_widget( $output ) {
	$output = str_replace( '?s=320', '?s=600', $output );
	$output = str_replace( 'class="grofile-full-link btn btn-secondary"', 'class="grofile-full-link btn btn-outline-secondary"', $output );

	$target = strpos( $output, '<ul class="grofile-urls grofile-accounts">' );
	if ( false !== $target ) {
		$pos    = false;
		$offset = strpos( $output, '<h4>' );
		while ( false !== $offset ) {
			$pos    = $offset;
			$offset = strpos( $output, '<h4>', $offset + 1 );

			if ( $offset > $target ) {
				break;
			}
		}

		if ( false !== $pos ) {
			$output = substr_replace( $output, ' class="screen-reader-text visually-hidden"', $pos + 3, 0 );
		}
	}

	return $output;
}
add_filter( 'enlightenment_widget_grofile', 'enlightenment_filter_grofile_widget', 12 );

function enlightenment_filter_rss_links_widget( $output, $widget, $instance ) {
	$output = str_replace(
		'class="widget widget_rss_links"',
		sprintf( 'class="widget widget_rss_links format-%s"', esc_attr( $instance['format'] ) ),
		$output
	);

	if ( isset( $instance['format'] ) && ( 'image' == $instance['format'] || 'text-image' == $instance['format'] ) ) {
		$output = str_replace(
			sprintf( 'class="widget widget_rss_links format-%s"', esc_attr( $instance['format'] ) ),
			sprintf(
				'class="widget widget_rss_links format-%s imagesize-%s imagecolor-%s"',
				esc_attr( $instance['format'] ),
				esc_attr( $instance['imagesize'] ),
				esc_attr( $instance['imagecolor'] )
			),
			$output
		);

		$start = strpos( $output, '<img ' );
		while ( false !== $start ) {
			$end    = strpos( $output, '/>', $start ) + 2;
			$length = $end - $start;

			$start_a  = strpos( $output, 'alt="', $start ) + 5;
			$end_a    = strpos( $output, '"', $start_a );
			$length_a = $end_a - $start_a;
			$alt      = substr( $output, $start_a, $length_a );

			$output = substr_replace(
				$output,
				sprintf( '<i class="fas fa-rss-square" aria-hidden="true"></i><span class="screen-reader-text visually-hidden">%s</span>', $alt ),
				$start,
				$length
			);

			$start  = strpos( $output, '<img ', $start );
		}
	}

	return $output;
}
add_filter( 'enlightenment_widget_rss_links', 'enlightenment_filter_rss_links_widget', 10, 3 );

function enlightenment_filter_jetpack_top_posts_widget_image_options( $args ) {
	if ( 200 == $args['avatar_size'] ) {
		$args['avatar_size'] = 296;
		$args['width']       = 296;
		$args['height']      = 296;
	} else {
		$args['avatar_size'] = 150;
		$args['width']       = 150;
		$args['height']      = 150;
	}


	return $args;
}
add_filter( 'jetpack_top_posts_widget_image_options', 'enlightenment_filter_jetpack_top_posts_widget_image_options' );

add_action( 'jetpack_widget_top_posts_before_post', 'enlightenment_ob_start', 9999 );

function enlightenment_jetpack_widget_top_posts_filter_post( $post_id ) {
	$output = ob_get_clean();

	$offset = strpos( $output, '<div class="widgets-list-layout-links">' );
	if ( false !== $offset ) {
		$offset = strpos( $output, '</div>', $offset );
		$output = substr_replace(
			$output,
			sprintf(
				'<span class="widgets-list-layout-date"><a href="%s" class="bump-view" data-bump-view="tp">%s</a></span>',
				get_permalink( $post_id ),
				get_the_date( '', $post_id )
			) . "\n",
			$offset,
			0
		);
	}

	echo $output;
}
add_action( 'jetpack_widget_top_posts_after_post', 'enlightenment_jetpack_widget_top_posts_filter_post', 1 );

function enlightenment_filter_widget_jetpack_widget_social_icons( $output, $widget, $instance ) {
	// $output = str_replace( '<svg class="icon icon-facebook" aria-hidden="true" role="presentation"> <use href="#icon-facebook" xlink:href="#icon-facebook"></use> </svg>', '<i class="fab fa-facebook-f" aria-hidden="true" role="presentation"></i>', $output );

	if ( ! empty( $instance['icons'] ) ) {
		$social_icons = $widget['callback'][0]->get_supported_icons();

		foreach( $instance['icons'] as $icon ) {
			if ( empty( $icon['url'] ) ) {
				continue;
			}

			$output = str_replace(
				sprintf( '<a href="%s"', $icon['url'] ),
				sprintf( '<a href="%s" class="jetpack-social-widget-icon"', $icon['url'] ),
				$output
			);

			foreach ( $social_icons as $social_icon ) {
				foreach ( $social_icon['url'] as $url_fragment ) {
					if ( false !== stripos( $icon['url'], $url_fragment ) ) {
						$output = str_replace(
							sprintf( '<a href="%s" class="jetpack-social-widget-icon"', $icon['url'] ),
							sprintf( '<a href="%s" class="jetpack-social-widget-icon jetpack-social-widget-icon-%s"', $icon['url'], $social_icon['icon'] ),
							$output
						);

						break;
					}
				}
			}
		}
	}

	return $output;
}
add_filter( 'enlightenment_widget_jetpack_widget_social_icons', 'enlightenment_filter_widget_jetpack_widget_social_icons', 10, 3 );

function enlightenment_filter_upcoming_events_widget( $output ) {
	$output = str_replace( '<span class="event-when">', '<span class="event-when"><i class="far fa-clock fa-fw" aria-hidden="true"></i> ', $output );
	$output = str_replace( '<span class="event-location">', '<span class="event-location"><i class="fas fa-map-marker-alt fa-fw" aria-hidden="true"></i> ', $output );

	return $output;
}
add_filter( 'enlightenment_widget_upcoming_events_widget', 'enlightenment_filter_upcoming_events_widget' );

function enlightenment_filter_jetpack_button_block( $output, $block ) {
	$output = str_replace( 'class="wp-block-jetpack-button btn-outline-secondary ', 'class="wp-block-jetpack-button btn-outline-theme ', $output );
	$output = str_replace( 'class="wp-block-jetpack-button btn-outline-secondary"', 'class="wp-block-jetpack-button btn-outline-theme"', $output );
	$output = str_replace( 'class="btn btn-outline-secondary ', 'class="btn btn-outline-theme ',  $output );
	$output = str_replace( 'class="btn btn-outline-secondary"', 'class="btn btn-outline-theme"',  $output );
    $output = str_replace( 'class="wp-block-jetpack-button btn-secondary ', 'class="wp-block-jetpack-button btn-theme ', $output );
    $output = str_replace( 'class="wp-block-jetpack-button btn-secondary"', 'class="wp-block-jetpack-button btn-theme"', $output );
	$output = str_replace( 'class="btn btn-secondary ', 'class="btn btn-theme ',  $output );
	$output = str_replace( 'class="btn btn-secondary"', 'class="btn btn-theme"',  $output );

	if ( ! isset( $block['attrs'] ) || ! isset( $block['attrs']['fontSize'] ) || 'small' != $block['attrs']['fontSize'] ) {
		$output = str_replace( 'class="btn btn-outline-theme ', 'class="btn btn-outline-theme btn-lg ',  $output );
		$output = str_replace( 'class="btn btn-outline-theme"', 'class="btn btn-outline-theme btn-lg"',  $output );
		$output = str_replace( 'class="btn btn-theme ', 'class="btn btn-theme btn-lg ',  $output );
		$output = str_replace( 'class="btn btn-theme"', 'class="btn btn-theme btn-lg"',  $output );
	}

	return $output;
}
add_filter( 'enlightenment_render_block_jetpack_button',   'enlightenment_filter_jetpack_button_block', 12, 2 );
add_filter( 'enlightenment_render_block_jetpack_calendly', 'enlightenment_filter_jetpack_button_block', 12, 2 );

function enlightenment_filter_jetpack_contact_form_block( $output ) {
	$output = str_replace( '<span class="text-body-secondary ms-1">', '<span class="required"><span class="asterisk" role="presentation" aria-hidden-"true">*</span> <span class="screen-reader-text visually-hidden">', $output );
	$output = str_replace( '</span>', '</span></span>', $output );

	$offset = strpos( $output, "class='select " );
	if ( false !== $offset ) {
		$start  = $offset + 7;
		$end    = strpos( $output, "'", $start );

		$offset = strpos( $output, ' contact-form-dropdown ', $start );
		if ( false === $offset || $offset > $end ) {
			$offset = strpos( $output, " contact-form-dropdown'", $start );
		}
		if ( false !== $offset && $offset < $end ) {
			$output = substr_replace( $output, '', $offset, 22 );
		}
	}

	return $output;
}
add_filter( 'enlightenment_render_block_jetpack_contact_form', 'enlightenment_filter_jetpack_contact_form_block', 12 );

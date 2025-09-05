<?php

function enlightenment_theme_events_default_theme_mods( $mods ) {
	return array_merge( $mods, array(
		'filter_bar_position'    => 'left',
		'filter_bar_always_open' => true,
	) );
}
add_filter( 'enlightenment_default_theme_mods', 'enlightenment_theme_events_default_theme_mods' );

add_filter( 'tribe_asset_enqueue_tribe-events-v2-single-blocks', '__return_false' );
add_filter( 'tribe_asset_enqueue_tribe-admin-v2-single-blocks', '__return_false' );

remove_action( 'tribe_events_pro_widget_render', array( tribe( 'customizer' ), 'inline_style' ),          101 );
remove_action( 'wp_print_footer_scripts',        array( tribe( 'customizer' ), 'shortcode_inline_style' ), 10 );
remove_action( 'wp_print_footer_scripts',        array( tribe( 'customizer' ), 'widget_inline_style' ),    10 );

remove_action(
	apply_filters( 'tribe_customizer_print_styles_action', 'wp_enqueue_scripts' ),
	array( tribe( 'customizer' ), 'inline_style' ),
	15
);

function enlightenment_tribe_events_styles() {
	wp_deregister_style( 'widget-calendar-pro-style' );

	// wp_dequeue_style( 'tribe-events-block-event-venue' );
	// wp_dequeue_style( 'tribe-events-block-classic-event-details' );
	// wp_dequeue_style( 'tribe-events-block-event-datetime' );
	// wp_dequeue_style( 'tribe-events-block-event-links' );

	wp_enqueue_style( 'enlightenment-tribe-events', get_theme_file_uri( 'assets/css/the-events-calendar.css' ), array( 'enlightenment-theme-stylesheet' ), null );
}
add_action( 'wp_enqueue_scripts', 'enlightenment_tribe_events_styles', 30 );

function enlightenment_tribe_events_admin_styles() {
	wp_dequeue_style( 'tribe-admin-v2-single-blocks' );
}
add_action( 'admin_enqueue_scripts', 'enlightenment_tribe_events_admin_styles', 12 );

function enlightenment_tribe_filter_header_image( $url ) {
    if ( is_singular( 'tribe_venue' ) ) {
		$venue = tribe_get_venue_object();

		if ( has_post_thumbnail( $venue ) ) {
			$url = get_the_post_thumbnail_url( $venue, 'full' );
		}
    }

    return $url;
}
add_filter( 'theme_mod_header_image', 'enlightenment_tribe_filter_header_image' );

function enlightenment_theme_events_layout_hooks() {
	if (
		is_post_type_archive( 'tribe_events' )
		||
		is_tax( 'tribe_events_cat' )
		||
		is_singular( 'tribe_venue' )
		||
		is_tax( 'tec_venue_category' )
		||
		is_singular( 'tribe_organizer' )
		||
		is_tax( 'tec_organizer_category' )
		||
		is_singular( 'tribe_event_series' )
	) {
		remove_action( 'enlightenment_page_header', 'enlightenment_breadcrumbs' );
		remove_action( 'enlightenment_before_page_content', 'enlightenment_tribe_events_open_container', 1 );
		remove_action( 'enlightenment_before_page_content', 'enlightenment_events_content_title' );
		remove_action( 'enlightenment_before_page_content', 'enlightenment_events_bar' );
		remove_action( 'enlightenment_after_page_content', 'enlightenment_tribe_events_close_container', 20 );

		add_action( 'enlightenment_before_page_header', 'enlightenment_tribe_events_open_container', 1 );

		add_action( 'enlightenment_page_header', 'enlightenment_events_bar' );

		if ( 'map' == enlightenment_tribe_get_view() ) {
			add_action( 'enlightenment_before_page_content', 'enlightenment_tribe_map_open_container', 3 );
			add_action( 'enlightenment_after_page_content', 'enlightenment_tribe_map_close_container', 17 );
		}

		add_action( 'enlightenment_after_site_content', 'enlightenment_tribe_events_close_container', 20 );
	}
}
add_action( 'wp', 'enlightenment_theme_events_layout_hooks', 12 );

function enlightenment_theme_tribe_page_content_hooks( $hooks ) {
	$hooks['enlightenment_page_header']['functions'][] = 'enlightenment_events_bar';

	return $hooks;
}
add_filter( 'enlightenment_page_content_hooks', 'enlightenment_theme_tribe_page_content_hooks' );

function enlightenment_tribe_map_open_container() {
	if ( ! have_posts() ) {
		return;
	}

	global $wp_query;

	$class = 'tribe-events-view--map';

	if ( 0 === $wp_query->post_count ) {
		$class .= ' tribe-events-view--map__no-events';
	}

	echo enlightenment_open_tag( 'div', 'tribe-events-view--map' );
}

function enlightenment_tribe_map_close_container() {
	if ( ! have_posts() ) {
		return;
	}

	echo enlightenment_close_tag( 'div' );
}

function enlightenment_tribe_events_views_v2_map_view_html_classes( $classes ) {
	global $wp_query;

	if ( 0 === $wp_query->post_count ) {
		$classes[] = 'tribe-events-view--map__no-events';
	}

	return $classes;
}
// add_filter( 'tribe_events_views_v2_map_view_html_classes', 'enlightenment_tribe_events_views_v2_map_view_html_classes' );

function enlightenment_tribe_events_views_v2_view_html_classes( $classes, $view_slug ) {
	switch ( $view_slug ) {
		case 'map':
			global $wp_query;

			if ( $wp_query->post_count ) {
				if ( in_array( 'tribe-events-view--shortcode', $classes ) ) {
					$classes[] = 'alignfull';
				}
			} else {
				$classes[] = 'tribe-events-view--map__no-events';
			}

			break;

		default:
			if ( in_array( 'tribe-events-view--shortcode', $classes ) ) {
				$classes[] = 'alignwide';
			}
	}

	return $classes;
}
add_filter( 'tribe_events_views_v2_view_html_classes', 'enlightenment_tribe_events_views_v2_view_html_classes', 12, 2 );

// [tribe_events] shortcode only
function enlightenment_tribe_filter_template_view_output( $output ) {
	$output = str_replace( 'class="tribe-events-header row ', 'class="tribe-events-header ', $output );
	$output = str_replace( 'class="tribe-events-header row"', 'class="tribe-events-header"', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_list_output',    'enlightenment_tribe_filter_template_view_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_month_output',   'enlightenment_tribe_filter_template_view_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_day_output',     'enlightenment_tribe_filter_template_view_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_summary_output', 'enlightenment_tribe_filter_template_view_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_week_output',    'enlightenment_tribe_filter_template_view_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_photo_output',   'enlightenment_tribe_filter_template_view_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_map_output',     'enlightenment_tribe_filter_template_view_output', 12 );

function enlightenment_theme_tribe_filter_template_components_events_bar_output( $output, $template_name, $file, $template ) {
	ob_start();
	$template->template( 'components/events-bar/views' );
	$views = ob_get_clean();

	$output = str_replace( $views, '', $output );
	$output = str_replace( 'class="tribe-events-header__events-bar col flex-shrink-1 col-lg-12 flex-lg-shrink-0 navbar navbar-expand-lg py-0 shadow-none ', 'class="tribe-events-header__events-bar flex-shrink-1 flex-lg-shrink-0 navbar navbar-expand-lg p-0 shadow-none ', $output );

	if ( doing_action( 'enlightenment_page_header' ) && has_header_image() ) {
		$offset = strpos( $output, 'class="tribe-events-header__events-bar ' );
		if ( false !== $offset ) {
			$offset = strpos( $output, '"', $offset + 7 );
			$output = substr_replace( $output, ' data-bs-theme="light"', $offset + 1, 0 );
		}
	}

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_components_events_bar_output', 'enlightenment_theme_tribe_filter_template_components_events_bar_output', 12, 4 );

function enlightenment_tribe_filter_template_components_events_bar_search_submit_output( $output ) {
	return str_replace( 'class="tribe-common-c-btn tribe-events-c-search__button btn btn-light"', 'class="tribe-common-c-btn tribe-events-c-search__button btn btn-theme-inverse"', $output );
}
add_filter( 'enlightenment_tribe_filter_template_components_events_bar_search_submit_output', 'enlightenment_tribe_filter_template_components_events_bar_search_submit_output', 12 );

function enlightenment_events_bar_views( $file, $name, $template ) {
	$context = $template->get_values();

	if ( ! $context['display_events_bar'] ) {
		return;
	}

	$template->template( 'components/events-bar/views' );
}
add_action( 'tribe_template_after_include:events/v2/components/top-bar/actions', 'enlightenment_events_bar_views', 10, 3 );

function enlightenment_tribe_filter_template_components_events_bar_views_output( $output ) {
	return str_replace( 'class="tribe-events-c-view-selector__button btn btn-secondary ', 'class="tribe-events-c-view-selector__button btn btn-outline-secondary ', $output );
}
add_filter( 'enlightenment_tribe_filter_template_components_events_bar_views_output', 'enlightenment_tribe_filter_template_components_events_bar_views_output', 12 );

function enlightenment_tribe_filter_events_header_container_class( $class ) {
	return str_replace( ' row', '', $class );
}
add_filter( 'enlightenment_tribe_events_header_container_class', 'enlightenment_tribe_filter_events_header_container_class', 12 );

function enlightenment_tribe_filter_template_components_messages_output( $output ) {
	return str_replace( 'class="tribe-events-header__messages col-12 ', 'class="tribe-events-header__messages ', $output );
}
add_filter( 'enlightenment_tribe_filter_template_components_messages_output', 'enlightenment_tribe_filter_template_components_messages_output', 12 );

function enlightenment_theme_tribe_filter_template_organizer_meta_output( $output, $template_name, $file, $template ) {
	$context = $template->get_values();

	$classes = [ 'tribe-events-pro-organizer__meta' ];

	ob_start();
	tribe_classes( $classes );
	$class = ob_get_clean();

	if (
		false !== strpos( $output, sprintf( '<div %s>', $class ) )
		&&
		has_post_thumbnail( $context['organizer'] )
	) {
		$image  = '<div class="row">';
		$image .= '<div class="col-3">';
		$image .= '<div class="tribe-events-pro-organizer__meta-image">';
		$image .= get_the_post_thumbnail( $context['organizer'], 'full' );
		$image .= '</div>';
		$image .= '</div>';

		$classes = [ 'tribe-events-pro-organizer__meta' ];

		ob_start();
		tribe_classes( $classes );
		$class = ob_get_clean();

		$output  = str_replace( sprintf( '<div %s>', $class ), sprintf( '<div %s>', $class ) . $image . '<div class="col-9">', $output );
		$output .= '</div></div>';
	}

	$categories = tec_events_pro_get_organizer_categories( $context['organizer']->ID );

	if ( ! empty( $categories ) ) {
		$content   = tribe_get_the_content( null, false, $context['organizer']->ID );
		$url       = tribe_get_organizer_website_url( $context['organizer']->ID );
		$email     = tribe_get_organizer_email( $context['organizer']->ID );
		$phone     = tribe_get_organizer_phone( $context['organizer']->ID );
		$cats_tmpl = $template->template( 'organizer/meta/categories', array(
			'organizer'          => $context['organizer'],
			'has_content'        => ! empty( $content ),
			'has_details'        => ! empty( $url ) || ! empty( $email ) || ! empty( $phone ),
			'has_featured_image' => $context['organizer']->thumbnail->exists,
			'has_taxonomy'       => ! empty( $categories ),
		), false );

		$offset = strrpos( $output, $cats_tmpl );
		if ( false !== $offset ) {
			$output = substr_replace( $output, '', $offset, strlen( $cats_tmpl ) );
		}
	}

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_organizer_meta_output', 'enlightenment_theme_tribe_filter_template_organizer_meta_output', 12, 4 );

function enlightenment_tribe_filter_template_organizer_meta_details_output( $output, $template_name, $file, $template ) {
	$context    = $template->get_values();
	$categories = tec_events_pro_get_organizer_categories( $context['organizer']->ID );

	if ( empty( $categories ) ) {
		return $output;
	}

	if ( ! $context['has_details'] ) {
		$content = tribe_get_the_content( null, false, $context['organizer']->ID );
		$colspan = empty( $content ) ? 'col-12' : 'col-12 col-lg-4';
		$output  = sprintf( '<div class="tribe-events-pro-organizer__meta-details %s"></div>', $colspan );
	}

	$content = tribe_get_the_content( null, false, $context['organizer']->ID );
	$url     = tribe_get_organizer_website_url( $context['organizer']->ID );
	$email   = tribe_get_organizer_email( $context['organizer']->ID );
	$phone   = tribe_get_organizer_phone( $context['organizer']->ID );

	$offset = strrpos( $output, '</div>' );
	if ( false !== $offset ) {
		$output = substr_replace( $output, $template->template( 'organizer/meta/categories', array(
			'organizer'          => $context['organizer'],
			'has_content'        => ! empty( $content ),
			'has_details'        => ! empty( $url ) || ! empty( $email ) || ! empty( $phone ),
			'has_featured_image' => $context['organizer']->thumbnail->exists,
			'has_taxonomy'       => ! empty( $categories ),
		), false ), $offset, 0 );
	}

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_organizer_meta_details_output', 'enlightenment_tribe_filter_template_organizer_meta_details_output', 12, 4 );

function enlightenment_tribe_filter_template_organizer_meta_categories_output( $output, $template_name, $file, $template ) {
	$context    = $template->get_values();
	$categories = tec_events_pro_get_organizer_categories( $context['organizer']->ID );

	if ( empty( $categories ) ) {
		return $output;
	}

	$output = str_replace( 'class="tribe-events-pro-organizer__meta-categories col-12 ', 'class="tribe-events-pro-organizer__meta-categories ', $output );
	$output = str_replace( '<span class="tribe-events-pro-organizer__meta-categories-label">', '<i class="tribe-common-c-svgicon fas fa-tags" role="presentation" aria-hidden="true"></i> <span class="tribe-events-pro-organizer__meta-categories-label screen-reader-text visually-hidden">', $output );

	$offset = strpos( $output, 'class="tribe-events-pro-organizer__meta-categories-term-name ' );
	if ( false !== $offset ) {
		$offset = strrpos( $output, '<span ', $offset - strlen( $output ) );
		$output = substr_replace( $output, '<span class="tribe-events-pro-organizer__meta-categories-terms">', $offset, 0 );
		$offset = strrpos( $output, '</span>' );
		$output = substr_replace( $output, '</span>', $offset, 0 );
	}

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_organizer_meta_categories_output', 'enlightenment_tribe_filter_template_organizer_meta_categories_output', 12, 4 );

function enlightenment_tribe_filter_template_top_bar_output( $output ) {
	return str_replace( 'class="tribe-events-c-top-bar tribe-events-header__top-bar col flex-grow-1 col-lg-12 flex-lg-grow-0 order-first order-lg-0 d-flex align-items-center"', 'class="tribe-events-c-top-bar tribe-events-header__top-bar d-flex align-items-center"', $output );
}
add_filter( 'enlightenment_tribe_filter_template_list_top_bar_output',    'enlightenment_tribe_filter_template_top_bar_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_month_top_bar_output',   'enlightenment_tribe_filter_template_top_bar_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_day_top_bar_output',     'enlightenment_tribe_filter_template_top_bar_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_summary_top_bar_output', 'enlightenment_tribe_filter_template_top_bar_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_week_top_bar_output',    'enlightenment_tribe_filter_template_top_bar_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_photo_top_bar_output',   'enlightenment_tribe_filter_template_top_bar_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_map_top_bar_output',     'enlightenment_tribe_filter_template_top_bar_output', 12 );

function enlightenment_tribe_filter_template_top_bar_nav_prev_output( $output ) {
	if ( ! defined( 'Tribe__Events__Pro__Main::VERSION' ) ) {
		return $output;
	}

	if ( version_compare( \Tribe__Events__Main::VERSION, '5.3.0', '>=' ) ) {
		return $output;
	}

	return str_replace( 'class="tribe-common-c-btn-icon ', 'class="tribe-common-c-btn-icon fas fa-chevron-left p-0 border-0 ', $output );
}
add_filter( 'enlightenment_tribe_filter_template_list_top_bar_nav_prev_output', 'enlightenment_tribe_filter_template_top_bar_nav_prev_output' );
add_filter( 'enlightenment_tribe_filter_template_list_top_bar_nav_prev_disabled_output', 'enlightenment_tribe_filter_template_top_bar_nav_prev_output' );
add_filter( 'enlightenment_tribe_filter_template_month_top_bar_nav_prev_output', 'enlightenment_tribe_filter_template_top_bar_nav_prev_output' );
add_filter( 'enlightenment_tribe_filter_template_month_top_bar_nav_prev_disabled_output', 'enlightenment_tribe_filter_template_top_bar_nav_prev_output' );
add_filter( 'enlightenment_tribe_filter_template_day_top_bar_nav_prev_output', 'enlightenment_tribe_filter_template_top_bar_nav_prev_output' );
add_filter( 'enlightenment_tribe_filter_template_day_top_bar_nav_prev_disabled_output', 'enlightenment_tribe_filter_template_top_bar_nav_prev_output' );
add_filter( 'enlightenment_tribe_filter_template_summary_top_bar_nav_prev_output', 'enlightenment_tribe_filter_template_top_bar_nav_prev_output' );
add_filter( 'enlightenment_tribe_filter_template_summary_top_bar_nav_prev_disabled_output', 'enlightenment_tribe_filter_template_top_bar_nav_prev_output' );
add_filter( 'enlightenment_tribe_filter_template_week_top_bar_nav_prev_output', 'enlightenment_tribe_filter_template_top_bar_nav_prev_output' );
add_filter( 'enlightenment_tribe_filter_template_week_top_bar_nav_prev_disabled_output', 'enlightenment_tribe_filter_template_top_bar_nav_prev_output' );
add_filter( 'enlightenment_tribe_filter_template_photo_top_bar_nav_prev_output', 'enlightenment_tribe_filter_template_top_bar_nav_prev_output' );
add_filter( 'enlightenment_tribe_filter_template_photo_top_bar_nav_prev_disabled_output', 'enlightenment_tribe_filter_template_top_bar_nav_prev_output' );
add_filter( 'enlightenment_tribe_filter_template_map_top_bar_nav_prev_output', 'enlightenment_tribe_filter_template_top_bar_nav_prev_output' );
add_filter( 'enlightenment_tribe_filter_template_map_top_bar_nav_prev_disabled_output', 'enlightenment_tribe_filter_template_top_bar_nav_prev_output' );

function enlightenment_tribe_filter_template_top_bar_nav_next_output( $output ) {
	if ( ! defined( 'Tribe__Events__Pro__Main::VERSION' ) ) {
		return $output;
	}

	if ( version_compare( \Tribe__Events__Main::VERSION, '5.3.0', '>=' ) ) {
		return $output;
	}

	return str_replace( 'class="tribe-common-c-btn-icon ', 'class="tribe-common-c-btn-icon fas fa-chevron-right p-0 border-0 ', $output );
}
add_filter( 'enlightenment_tribe_filter_template_list_top_bar_nav_next_output', 'enlightenment_tribe_filter_template_top_bar_nav_next_output' );
add_filter( 'enlightenment_tribe_filter_template_list_top_bar_nav_next_disabled_output', 'enlightenment_tribe_filter_template_top_bar_nav_next_output' );
add_filter( 'enlightenment_tribe_filter_template_month_top_bar_nav_next_output', 'enlightenment_tribe_filter_template_top_bar_nav_next_output' );
add_filter( 'enlightenment_tribe_filter_template_month_top_bar_nav_next_disabled_output', 'enlightenment_tribe_filter_template_top_bar_nav_next_output' );
add_filter( 'enlightenment_tribe_filter_template_day_top_bar_nav_next_output', 'enlightenment_tribe_filter_template_top_bar_nav_next_output' );
add_filter( 'enlightenment_tribe_filter_template_day_top_bar_nav_next_disabled_output', 'enlightenment_tribe_filter_template_top_bar_nav_next_output' );
add_filter( 'enlightenment_tribe_filter_template_summary_top_bar_nav_next_output', 'enlightenment_tribe_filter_template_top_bar_nav_next_output' );
add_filter( 'enlightenment_tribe_filter_template_summary_top_bar_nav_next_disabled_output', 'enlightenment_tribe_filter_template_top_bar_nav_next_output' );
add_filter( 'enlightenment_tribe_filter_template_week_top_bar_nav_next_output', 'enlightenment_tribe_filter_template_top_bar_nav_next_output' );
add_filter( 'enlightenment_tribe_filter_template_week_top_bar_nav_next_disabled_output', 'enlightenment_tribe_filter_template_top_bar_nav_next_output' );
add_filter( 'enlightenment_tribe_filter_template_photo_top_bar_nav_next_output', 'enlightenment_tribe_filter_template_top_bar_nav_next_output' );
add_filter( 'enlightenment_tribe_filter_template_photo_top_bar_nav_next_disabled_output', 'enlightenment_tribe_filter_template_top_bar_nav_next_output' );
add_filter( 'enlightenment_tribe_filter_template_map_top_bar_nav_next_output', 'enlightenment_tribe_filter_template_top_bar_nav_next_output' );
add_filter( 'enlightenment_tribe_filter_template_map_top_bar_nav_next_disabled_output', 'enlightenment_tribe_filter_template_top_bar_nav_next_output' );

function enlightenment_tribe_filter_template_components_top_bar_today_output( $output ) {
	$output = str_replace( 'class="tribe-common-c-btn-border tribe-events-c-top-bar__today-button btn btn-secondary ', 'class="tribe-common-c-btn-border tribe-events-c-top-bar__today-button btn btn-outline-secondary ', $output );
	$output = str_replace( 'class="tribe-common-c-btn-border-small tribe-events-c-top-bar__today-button btn btn-secondary ', 'class="tribe-common-c-btn-border tribe-events-c-top-bar__today-button btn btn-outline-secondary ', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_components_top_bar_today_output', 'enlightenment_tribe_filter_template_components_top_bar_today_output', 12 );

function enlightenment_theme_tribe_filter_template_top_bar_datepicker_output( $output ) {
	$output = str_replace( 'class="tribe-common-c-btn__clear tribe-common-h3 tribe-common-h--alt tribe-events-c-top-bar__datepicker-button"', 'class="tribe-common-c-btn__clear tribe-common-h3 tribe-common-h--alt tribe-events-c-top-bar__datepicker-button btn btn-theme-inverse"', $output );
	$output = str_replace( 'class="tribe-common-h3 tribe-common-h--alt tribe-events-c-top-bar__datepicker-button"', 'class="tribe-common-h3 tribe-common-h--alt tribe-events-c-top-bar__datepicker-button btn btn-theme-inverse"', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_list_top_bar_datepicker_output', 'enlightenment_theme_tribe_filter_template_top_bar_datepicker_output' );
add_filter( 'enlightenment_tribe_filter_template_month_top_bar_datepicker_output', 'enlightenment_theme_tribe_filter_template_top_bar_datepicker_output' );
add_filter( 'enlightenment_tribe_filter_template_day_top_bar_datepicker_output', 'enlightenment_theme_tribe_filter_template_top_bar_datepicker_output' );
add_filter( 'enlightenment_tribe_filter_template_summary_top_bar_datepicker_output', 'enlightenment_theme_tribe_filter_template_top_bar_datepicker_output' );
add_filter( 'enlightenment_tribe_filter_template_week_top_bar_datepicker_output', 'enlightenment_theme_tribe_filter_template_top_bar_datepicker_output' );
add_filter( 'enlightenment_tribe_filter_template_photo_top_bar_datepicker_output', 'enlightenment_theme_tribe_filter_template_top_bar_datepicker_output' );
add_filter( 'enlightenment_tribe_filter_template_map_top_bar_datepicker_output', 'enlightenment_theme_tribe_filter_template_top_bar_datepicker_output' );

function enlightenment_tribe_filter_events_filters_layout( $option, $optionName ) {
	if ( 'events_filters_layout' != $optionName ) {
		return $option;
	}

	if ( 'map' == enlightenment_tribe_get_view() ) {
		$option = 'horizontal';
	}

	return $option;
}
add_filter( 'tribe_get_option', 'enlightenment_tribe_filter_events_filters_layout', 10, 2 );

function enlightenment_theme_tribe_filter_bar( $output ) {
	if ( 'right' === get_theme_mod( 'filter_bar_position' ) ) {
		$output = str_replace( 'class="tribe-events-filter-bar-wrapper col-lg-3"', 'class="tribe-events-filter-bar-wrapper col-lg-3 order-1"', $output );
	}

	if ( class_exists( 'Tribe\Events\Filterbar\Views\V2_1\Filters' ) ) {
		$output = str_replace( 'class="tribe-filter-bar tribe-filter-bar--horizontal navbar ', 'class="tribe-filter-bar tribe-filter-bar--horizontal ', $output );

		if ( true === get_theme_mod( 'filter_bar_always_open' ) ) {
		    $output = str_replace( 'class="tribe-filter-bar tribe-filter-bar--vertical ', 'class="tribe-filter-bar tribe-filter-bar--vertical tribe-filter-bar--filters-always-open ', $output );
			$output = str_replace( 'class="tribe-filter-bar tribe-filter-bar--vertical"', 'class="tribe-filter-bar tribe-filter-bar--vertical tribe-filter-bar--filters-always-open"', $output );
		}

		$output = str_replace( 'class="tribe-filter-bar__action-done tribe-common-c-btn-border tribe-common-c-btn-border--secondary btn btn-secondary"', 'class="tribe-filter-bar__action-done tribe-common-c-btn-border tribe-common-c-btn-border--secondary btn btn-outline-secondary"', $output );
		$output = str_replace( 'class="tribe-filter-bar-c-clear-button btn btn-secondary ', 'class="tribe-filter-bar-c-clear-button btn btn-outline-secondary ', $output );
	} else {
		if ( true === get_theme_mod( 'filter_bar_always_open' ) ) {
		    $output = str_replace( 'class="tribe-events-filters-vertical ', 'class="tribe-events-filters-vertical tribe-events-filters-always-open ', $output );
		}

		$output = str_replace( 'class="tribe-events-filters-label px-3 py-2 px-lg-2 h5"', 'class="tribe-events-filters-label"', $output );
		$output = str_replace( '<div class="tribe-events-filters-buttons-wrap px-3 py-2 px-lg-2"><input type="submit"', '<div class="tribe-events-filters-buttons-wrap"><input type="submit"', $output );
		$output = str_replace( 'class="tribe_events_filters_form_submit btn btn-secondary"', 'class="tribe_events_filters_form_submit btn btn-outline-secondary"', $output );
		$output = str_replace( '<span class="dashicons dashicons-image-rotate tribe-reset-icon"></span>', '<span class="tribe-reset-icon fas fa-undo"></span> ', $output );
	}

	return $output;
}
add_filter( 'enlightenment_events_filter_bar', 'enlightenment_theme_tribe_filter_bar', 12 );

function enlightenment_tribe_filter_events_recurrence_tooltip( $output, $post_id ) {
	if ( ! tribe_is_recurring_event( $post_id ) ) {
		return $output;
	}

	$output = str_replace( '<span class="tribe-events-divider">|</span> ', '', $output );

	$from_related = false;

	if (
		doing_filter( 'enlightenment_render_block_tribe_related_events' )
		||
		enlightenment_has_in_call_stack( 'tribe_single_related_events' )
		||
		enlightenment_has_in_call_stack( array(
			array(
				'key'   => 'class',
				'value' => 'Tribe__Events__Pro__Editor__Blocks__Related_Events',
			),
		) )
	) {
		$from_related = true;
	}

	if ( $from_related ) {
		$offset = strrpos( $output, '<span class="recurring-event" ' );
		if ( false !== $offset ) {
			$offset = strpos( $output, '>', $offset );
			$output = substr_replace( $output, '<span class="screen-reader-text visually-hidden">', $offset + 1, 0 );
			$offset = strpos( $output, '<a ', $offset );
			$offset = strpos( $output, '>', $offset );
			$output = substr_replace( $output, '<i class="fas fa-sync" aria-hidden="true" role="presentation"></i> <span class="screen-reader-text visually-hidden">', $offset + 1, 0 );
			$offset = strpos( $output, '</a>', $offset );
			$output = substr_replace( $output, '</span>', $offset, 0 );
			$output = substr_replace( $output, '</span>', $offset, 0 );
		}
	} else {
		$offset = strrpos( $output, '<span class="recurring-event" ' );
		if ( false !== $offset ) {
			$offset = strpos( $output, '>', $offset );
			$output = substr_replace( $output, '<i class="fas fa-sync" aria-hidden="true" role="presentation"></i> ', $offset + 1, 0 );
		}
	}

	return $output;
}
add_filter( 'tribe_events_recurrence_tooltip', 'enlightenment_tribe_filter_events_recurrence_tooltip', 12, 2 );

function enlightenment_tribe_filter_events_views_v2_view_template_vars( $template_vars ) {
	if ( ! isset( $template_vars['map_provider'] ) || ! isset( $template_vars['map_provider']->map_pin_url ) ) {
		return $template_vars;
	}

	$template_vars['map_provider']->map_pin_url = get_theme_file_uri( 'assets/images/map-pin.svg' );

	return $template_vars;
}
add_filter( 'tribe_events_views_v2_view_template_vars', 'enlightenment_tribe_filter_events_views_v2_view_template_vars', 10, 2 );

function enlightenment_tribe_filter_template_map_map_google_maps_output( $output ) {
	$output .= "\n";
	$output .= '<div class="tribe-events-pro-map__map-mobile-close-button-wrapper d-lg-none">';
	$output .= '<button class="tribe-events-pro-map__map-mobile-close-button">';
	$output .= '<i class="tribe-events-pro-map__map-mobile-close-button-icon"></i> ';
	$output .= '<span class="tribe-events-pro-map__map-mobile-close-button-label tribe-common-a11y-hidden screen-reader-text visually-hidden">';
	$output .= __( 'Close Map', 'enlightenment' );
	$output .= '</span>';
	$output .= '</button>';
	$output .= '</div>';

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_map_map_google_maps_output', 'enlightenment_tribe_filter_template_map_map_google_maps_output', 12 );

function enlightenment_tribe_filter_template_map_map_no_venue_modal_output( $output ) {
	return str_replace( 'class="tribe-events-pro-map__no-venue-modal-link btn btn-secondary ', 'class="tribe-events-pro-map__no-venue-modal-link btn btn-outline-secondary ', $output );
}
add_filter( 'enlightenment_tribe_filter_template_map_map_no_venue_modal_output', 'enlightenment_tribe_filter_template_map_map_no_venue_modal_output', 12 );

function enlightenment_tribe_filter_template_part_modules_meta_organizer_output( $output ) {
	return str_replace( 'class="btn btn-secondary"', 'class="btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_tribe_filter_template_part_modules_meta_organizer_output', 'enlightenment_tribe_filter_template_part_modules_meta_organizer_output', 12 );

function enlightenment_tribe_filter_template_part_modules_meta_venue_output( $output ) {
	$output = str_replace( ' btn btn-secondary"', ' btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="btn btn-secondary"', 'class="btn btn-outline-secondary"', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_part_modules_meta_venue_output', 'enlightenment_tribe_filter_template_part_modules_meta_venue_output', 12 );

function enlightenment_tribe_filter_template_map_event_cards_event_card_actions_output( $output ) {
	$output = str_replace( 'class="tribe-events-c-small-cta__link tribe-common-cta tribe-common-cta--thin-alt btn btn-secondary btn-sm ', 'class="tribe-events-c-small-cta__link tribe-common-cta tribe-common-cta--thin-alt btn btn-outline-secondary btn-sm ', $output );
	$output = str_replace( 'class="tribe-events-c-small-cta__link tribe-common-cta tribe-common-cta--thin-alt btn btn-secondary btn-sm"', 'class="tribe-events-c-small-cta__link tribe-common-cta tribe-common-cta--thin-alt btn btn-outline-secondary btn-sm"', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_map_event_cards_event_card_actions_output', 'enlightenment_tribe_filter_template_map_event_cards_event_card_actions_output', 12 );

function enlightenment_tribe_filter_template_map_event_cards_event_card_actions_details_spacer_output( $output ) {
	$cta  = '<span class="tribe-events-c-small-cta__link tribe-common-cta tribe-common-cta--thin-alt d-lg-none">';
	$cta .= __( 'View on Map', 'enlightenment' );
	$cta .= '</span>';

	return sprintf( '%s%s%s', $cta, "\n", $output );
}
add_filter( 'enlightenment_tribe_filter_template_map_event_cards_event_card_actions_details_spacer_output', 'enlightenment_tribe_filter_template_map_event_cards_event_card_actions_details_spacer_output', 12 );

function enlightenment_tribe_filter_template_map_event_cards_event_card_actions_details_output( $output ) {
	$cta  = '<button class="tribe-events-c-small-cta__link tribe-common-cta tribe-common-cta--thin-alt tribe-events-pro-map__map-mobile-trigger-show-map d-lg-none">';
	$cta .= __( 'View on Map', 'enlightenment' );
	$cta .= '</button>';

	return sprintf( '%s%s%s', $cta, "\n", $output );
}
add_filter( 'enlightenment_tribe_filter_template_map_event_cards_event_card_actions_details_output', 'enlightenment_tribe_filter_template_map_event_cards_event_card_actions_details_output', 12 );

function enlightenment_tribe_filter_template_map_event_cards_nav_output( $output ) {
	$output .= "\n";
	$output .= '<div class="tribe-events-pro-map__map-mobile-show-map-button-wrapper d-lg-none">';
	$output .= '<button class="tribe-events-pro-map__map-mobile-show-map-button tribe-events-pro-map__map-mobile-trigger-show-map btn btn-theme">';
	$output .= '<i class="tribe-events-pro-map__map-mobile-show-map-button-icon fas fa-map-marked-alt"></i> ';
	$output .= '<span class="tribe-events-pro-map__map-mobile-show-map-button-label tribe-common-a11y-hidden">';
	$output .= __( 'Show Map', 'enlightenment' );
	$output .= '</span>';
	$output .= '</button>';
	$output .= '</div>';

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_map_event_cards_nav_output', 'enlightenment_tribe_filter_template_map_event_cards_nav_output', 12 );

function enlightenment_tribe_filter_template_list_month_separator_output( $output, $template_name, $file, $template ) {
	$context   = $template->get_view()->get_context();
	$widget    = $context->get( 'is-widget', false );
	$shortcode = $context->get( 'shortcode', false );
	$event     = ( doing_filter( 'the_content' ) || $widget || $shortcode ) ? get_post() : enlightenment_tribe_event();
	$values    = $template->get_values();

	if ( empty( $event->is_past ) && ! empty ( $values['request_date'] ) ) {
		$should_have_month_separator = Tribe\Events\Views\V2\Utils\Separators::should_have_month( $template->get( 'events' ), $event, $values['request_date'] );
	} else {
		$should_have_month_separator = Tribe\Events\Views\V2\Utils\Separators::should_have_month( $template->get( 'events' ), $event );
	}

	if ( ! $should_have_month_separator ) {
		return $output;
	}

	$sep_date = empty( $values['is_past'] ) && ! empty( $values['request_date'] )
		? max( $event->dates->start_display, $values['request_date'] )
		: $event->dates->start_display;

	$date_str = esc_html( $sep_date->format_i18n( 'F Y' ) );
	$date_arr = explode( ' ', $date_str );
	$date_new = sprintf( '<span>%s</span>', join( '</span> <span>', $date_arr ) );

	$output = str_replace( $date_str, $date_new, $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_list_month_separator_output', 'enlightenment_tribe_filter_template_list_month_separator_output', 12, 4 );

function enlightenment_tribe_filter_template_summary_month_separator_output( $output, $template_name, $file, $template ) {
	$values   = $template->get_values();

	$date_str = esc_html( $values['group_date']->format_i18n( 'M Y' ) );
	$date_arr = explode( ' ', $date_str );
	$date_new = sprintf( '<span>%s</span>', join( '</span> <span>', $date_arr ) );

	$output = str_replace( $date_str, $date_new, $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_summary_month_separator_output', 'enlightenment_tribe_filter_template_summary_month_separator_output', 12, 4 );

function enlightenment_tribe_filter_template_summary_date_group_event_cost_output( $output ) {
	return str_replace( 'class="tribe-events-c-small-cta__text btn btn-secondary ', 'class="tribe-events-c-small-cta__text btn btn-outline-secondary ', $output );
}
add_filter( 'enlightenment_tribe_filter_template_summary_date_group_event_cost_output', 'enlightenment_tribe_filter_template_summary_date_group_event_cost_output', 12 );

function enlightenment_tribe_filter_template_event_cost_output( $output ) {
	return str_replace( 'class="tribe-events-c-small-cta__link tribe-common-cta tribe-common-cta--thin-alt btn btn-secondary"', 'class="tribe-events-c-small-cta__link tribe-common-cta tribe-common-cta--thin-alt btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_tribe_filter_template_list_event_cost_output', 'enlightenment_tribe_filter_template_event_cost_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_day_event_cost_output', 'enlightenment_tribe_filter_template_event_cost_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_summary_event_cost_output', 'enlightenment_tribe_filter_template_event_cost_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_month_calendar_body_day_calendar_events_calendar_event_tooltip_cost_output', 'enlightenment_tribe_filter_template_event_cost_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_month_mobile_events_mobile_day_mobile_event_cost_output', 'enlightenment_tribe_filter_template_event_cost_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_week_grid_body_events_day_event_tooltip_cost_output', 'enlightenment_tribe_filter_template_event_cost_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_week_mobile_events_day_event_cost_output', 'enlightenment_tribe_filter_template_event_cost_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_photo_event_cost_output', 'enlightenment_tribe_filter_template_event_cost_output', 12 );

function enlightenment_tribe_filter_template_components_view_more_output( $output ) {
	$output = str_replace( 'class="tribe-common-anchor-thin tribe-events-widget-events-month__view-more-link btn btn-secondary"', 'class="tribe-common-anchor-thin tribe-events-widget-events-month__view-more-link btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="tribe-common-anchor-thin tribe-events-widget-events-week__view-more-link btn btn-secondary"', 'class="tribe-common-anchor-thin tribe-events-widget-events-week__view-more-link btn btn-outline-secondary"', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_components_view_more_output', 'enlightenment_tribe_filter_template_components_view_more_output', 12 );

function enlightenment_filter_single_event_back_link( $output ) {
	return str_replace( '&laquo;', '&larr;', $output );
}
add_filter( 'enlightenment_single_event_back_link', 'enlightenment_filter_single_event_back_link' );

function enlightenment_tribe_filter_single_event_links( $output ) {
	$output = str_replace( 'class="tribe-events-gcal tribe-events-button btn btn-secondary"', 'class="tribe-events-gcal tribe-events-button btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="tribe-events-ical tribe-events-button btn btn-secondary', 'class="tribe-events-ical tribe-events-button btn btn-outline-secondary', $output );
	$output = str_replace( 'class="tribe-events-button btn btn-secondary ', 'class="tribe-events-button btn btn-outline-secondary ', $output );

	return $output;
}
add_filter( 'tribe_events_ical_single_event_links', 'enlightenment_tribe_filter_single_event_links', 24 );

function enlightenment_tribe_filter_badge_text_bg( $output ) {
	$output = str_replace( ' badge text-bg-light ', ' badge text-bg-theme-inverse ', $output );
	$output = str_replace( ' badge text-bg-light"', ' badge text-bg-theme-inverse"', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_single_virtual_marker_mobile_output', 'enlightenment_tribe_filter_badge_text_bg', 12 );
add_filter( 'enlightenment_tribe_filter_template_single_virtual_marker_output', 'enlightenment_tribe_filter_badge_text_bg', 12 );
add_filter( 'enlightenment_tribe_filter_template_single_hybrid_marker_mobile_output', 'enlightenment_tribe_filter_badge_text_bg', 12 );
add_filter( 'enlightenment_tribe_filter_template_single_hybrid_marker_output', 'enlightenment_tribe_filter_badge_text_bg', 12 );
add_filter( 'enlightenment_tribe_filter_template_part_pro_widgets_this_week_single_event_output', 'enlightenment_tribe_filter_badge_text_bg', 12 );
add_filter( 'enlightenment_tribe_filter_template_components_virtual_event_output', 'enlightenment_tribe_filter_badge_text_bg', 12 );
add_filter( 'enlightenment_tribe_filter_template_components_hybrid_event_output', 'enlightenment_tribe_filter_badge_text_bg', 12 );

function enlightenment_tribe_filter_template_components_ical_link_output( $output ) {
	return str_replace( 'class="tribe-events-c-ical__link btn btn-primary ', 'class="tribe-events-c-ical__link btn btn-outline-primary ', $output );
}
add_filter( 'enlightenment_tribe_filter_template_components_ical_link_output', 'enlightenment_tribe_filter_template_components_ical_link_output', 12 );

function enlightenment_tribe_filter_template_components_subscribe_links_list_output( $output ) {
	$output = str_replace( 'class="tribe-events-c-subscribe-dropdown__button-text tribe-common-c-btn--clear btn btn-primary ', 'class="tribe-events-c-subscribe-dropdown__button-text tribe-common-c-btn--clear btn btn-outline-primary ', $output );
	$output = str_replace( 'class="tribe-events-c-subscribe-dropdown__button-text btn btn-primary ', 'class="tribe-events-c-subscribe-dropdown__button-text btn btn-outline-primary ', $output );
	$output = str_replace( 'class="tribe-events-c-subscribe-dropdown__content dropdown-menu"', 'class="tribe-events-c-subscribe-dropdown__content dropdown-menu dropdown-menu-end"', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_components_subscribe_links_list_output', 'enlightenment_tribe_filter_template_components_subscribe_links_list_output', 12 );

function enlightenment_tribe_filter_template_components_subscribe_links_single_event_list_output( $output ) {
	$output = str_replace( 'class="tribe-events-c-subscribe-dropdown__button-text tribe-common-c-btn--clear btn btn-secondary ', 'class="tribe-events-c-subscribe-dropdown__button-text tribe-common-c-btn--clear btn btn-outline-secondary ', $output );
	$output = str_replace( 'class="tribe-events-c-subscribe-dropdown__button-text btn btn-secondary ', 'class="tribe-events-c-subscribe-dropdown__button-text btn btn-outline-secondary ', $output );
	$output = str_replace( 'class="tribe-events-c-subscribe-dropdown__content dropdown-menu"', 'class="tribe-events-c-subscribe-dropdown__content dropdown-menu dropdown-menu-end"', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_components_subscribe_links_single_event_list_output', 'enlightenment_tribe_filter_template_components_subscribe_links_single_event_list_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_blocks_parts_subscribe_list_output', 'enlightenment_tribe_filter_template_components_subscribe_links_single_event_list_output', 12 );

function enlightenment_tribe_filter_template_single_event_back_link_output( $output ) {
	return str_replace( '&laquo;', '&larr;', $output );
}
add_filter( 'enlightenment_tribe_filter_template_single_event_back_link_output', 'enlightenment_tribe_filter_template_single_event_back_link_output', 12 );

function enlightenment_filter_event_nav_link_args( $args ) {
	$args['event_date_tag']   = 'span';
	$args['event_date_class'] = 'nav-meta nav-event-date';

	return $args;
}
add_filter( 'enlightenment_event_nav_link_args', 'enlightenment_filter_event_nav_link_args' );

add_filter( 'enlightenment_tribe_filter_template_components_icons_plus_output', '__return_false', 12 );

function enlightenment_theme_tribe_filter_template_venue_meta_output( $output, $template_name, $file, $template ) {
	$context    = $template->get_values();
	$categories = tec_events_pro_get_venue_categories( $context['venue']->ID );

	if ( ! empty( $categories ) ) {
		$content   = tribe_get_the_content( null, false, $context['venue']->ID );
		$address   = tribe_address_exists( $context['venue']->ID );
		$phone     = tribe_get_phone( $context['venue']->ID );
		$url       = tribe_get_venue_website_url( $context['venue']->ID );
		$cats_tmpl = $template->template( 'venue/meta/categories', array(
			'venue'              => $context['venue'],
			'has_content'        => ! empty( $content ),
			'has_details'        => ! empty( $address ) || ! empty( $phone ) || ! empty( $url ),
			'has_featured_image' => $context['venue']->thumbnail->exists,
			'has_taxonomy'       => ! empty( $categories ),
			'has_map'            => ( $context['enable_maps'] && $context['show_map'] ),
		), false );

		$offset = strrpos( $output, $cats_tmpl );
		if ( false !== $offset ) {
			$output = substr_replace( $output, '', $offset, strlen( $cats_tmpl ) );
		}
	}

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_venue_meta_output', 'enlightenment_theme_tribe_filter_template_venue_meta_output', 12, 4 );

function enlightenment_tribe_filter_template_venue_meta_details_output( $output, $template_name, $file, $template ) {
	$context    = $template->get_values();
	$categories = tec_events_pro_get_venue_categories( $context['venue']->ID );

	if ( empty( $categories ) ) {
		return $output;
	}

	$address = tribe_address_exists( $context['venue']->ID );
	$phone   = tribe_get_phone( $context['venue']->ID );
	$url     = tribe_get_venue_website_url( $context['venue']->ID );

	if ( empty( $address ) && empty( $phone ) && empty( $url ) ) {
		$output = '<div class="tribe-events-pro-venue__meta-details"></div>';
	}

	$content = tribe_get_the_content( null, false, $context['venue']->ID );

	$offset = strrpos( $output, '</div>' );
	if ( false !== $offset ) {
		$output = substr_replace( $output, $template->template( 'venue/meta/categories', array(
			'venue'              => $context['venue'],
			'has_content'        => ! empty( $content ),
			'has_details'        => ! empty( $address ) || ! empty( $phone ) || ! empty( $url ),
			'has_featured_image' => $context['venue']->thumbnail->exists,
			'has_taxonomy'       => ! empty( $categories ),
			'has_map'            => ( $context['enable_maps'] && $context['show_map'] ),
		), false ), $offset, 0 );
	}

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_venue_meta_details_output', 'enlightenment_tribe_filter_template_venue_meta_details_output', 12, 4 );

function enlightenment_tribe_filter_template_venue_meta_categories_output( $output, $template_name, $file, $template ) {
	$context    = $template->get_values();
	$categories = tec_events_pro_get_venue_categories( $context['venue']->ID );

	if ( empty( $categories ) ) {
		return $output;
	}

	$output = str_replace( '<span class="tribe-events-pro-venue__meta-categories-label">', '<i class="tribe-common-c-svgicon fas fa-tags" role="presentation" aria-hidden="true"></i> <span class="tribe-events-pro-venue__meta-categories-label screen-reader-text visually-hidden">', $output );

	$offset = strpos( $output, 'class="tribe-events-pro-venue__meta-categories-term-name ' );
	if ( false !== $offset ) {
		$offset = strrpos( $output, '<span ', $offset - strlen( $output ) );
		$output = substr_replace( $output, '<span class="tribe-events-pro-venue__meta-categories-terms">', $offset, 0 );
		$offset = strrpos( $output, '</span>' );
		$output = substr_replace( $output, '</span>', $offset, 0 );
	}

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_venue_meta_categories_output', 'enlightenment_tribe_filter_template_venue_meta_categories_output', 12, 4 );

function enlightenment_tribe_filter_template_venue_meta_map_output( $output ) {
	return sprintf( '<div class="tribe-events-pro-venue__meta-map-iframe-wrap d-flex h-100">%s</div>', $output );
}
add_filter( 'enlightenment_tribe_filter_template_venue_meta_map_output', 'enlightenment_tribe_filter_template_venue_meta_map_output' );

function enlightenment_tribe_filter_event_list_search( $output ) {
	return str_replace( 'class="btn btn-light"', 'class="btn btn-theme-inverse"', $output );
}
add_filter( 'enlightenment_tribe_event_list_search', 'enlightenment_tribe_filter_event_list_search', 12 );

function enlightenment_theme_tribe_filter_community_event_list_template( $output ) {
    $output = str_replace( 'class="tribe-button tribe-button-small tribe-upcoming tribe-button-secondary btn btn-secondary"', 'class="tribe-button tribe-button-small tribe-upcoming tribe-button-secondary btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="tribe-button tribe-button-small tribe-upcoming tribe-button-tertiary btn btn-secondary"', 'class="tribe-button tribe-button-small tribe-upcoming tribe-button-tertiary btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="tribe-button tribe-button-small tribe-past tribe-button-secondary btn btn-secondary"', 'class="tribe-button tribe-button-small tribe-past tribe-button-secondary btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="tribe-button tribe-button-small tribe-past tribe-button-tertiary btn btn-secondary"', 'class="tribe-button tribe-button-small tribe-past tribe-button-tertiary btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="table-menu-btn button tribe-button tribe-button-tertiary tribe-button-activate btn btn-secondary ', 'class="table-menu-btn button tribe-button tribe-button-tertiary tribe-button-activate btn btn-outline-secondary ', $output );
	$output = str_replace( 'class="tribe-community-events-list my-events display responsive stripe table table-striped"', 'class="tribe-community-events-list my-events display responsive stripe table"', $output );

    return $output;
}
add_filter( 'enlightenment_tribe_filter_community_event_list_template', 'enlightenment_theme_tribe_filter_community_event_list_template', 12 );

add_action( 'tribe_pre_get_template_part_community/columns/status', 'enlightenment_ob_start', 8 );

function enlightenment_tribe_filter_community_columns_status_template( $slug, $name, $data ) {
	ob_end_clean();

	$post_status  = $data['event']->post_status;
	$status_label = get_post_status_object( $post_status );
	$status_icon  = '';

	switch ( $post_status ) {
		case 'publish':
			$status_icon = 'check';
			break;

		case 'draft':
			$status_icon = 'file-alt';
			break;

		case 'future':
		case 'pending':
			$status_icon = 'clock';
			break;
	}

	if ( ! empty( $status_label ) ) {
		$status_label = $status_label->label;

		printf(
			'<div class="event-status d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="%s">%s</div>',
			esc_html( $status_label ),
			empty( $status_icon ) ? '' : sprintf( '<i class="fas fa-%s"></i>', $status_icon )
		);
	}
}
add_action( 'tribe_post_get_template_part_community/columns/status', 'enlightenment_tribe_filter_community_columns_status_template', 12, 3 );

function enlightenment_tribe_filter_community_events_list_events_link( $output ) {
	return str_replace( 'class="tribe-button tribe-button-secondary btn btn-secondary"', 'class="tribe-button tribe-button-secondary btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_tribe_community_events_list_events_link', 'enlightenment_tribe_filter_community_events_list_events_link', 12 );

function enlightenment_tribe_filter_template_part_community_modules_datepickers_output( $output ) {
	$output = str_replace( 'class="tribe-add-recurrence button tribe-button tribe-button-secondary btn btn-secondary"', 'class="tribe-add-recurrence button tribe-button tribe-button-secondary btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="tribe-button-field btn btn-secondary ', 'class="tribe-button-field btn btn-outline-secondary ', $output );
	$output = str_replace( 'class="tribe-button-field btn btn-secondary"', 'class="tribe-button-field btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="button btn btn-secondary"', 'class="button btn btn-outline-secondary"', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_part_integrations_the_events_calendar_modules_datepickers_output', 'enlightenment_tribe_filter_template_part_community_modules_datepickers_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_part_community_modules_datepickers_output', 'enlightenment_tribe_filter_template_part_community_modules_datepickers_output', 12 );

function enlightenment_tribe_filter_template_part_community_modules_image_output( $output ) {
	$start = strpos( $output, '<div class="choose-file ' );
	if ( false !== $start ) {
		$end    = strpos( $output, '</div>', $start ) + 6;
		$length = $end - $start;
		$button = substr( $output, $start, $length );
		$output = substr_replace( $output, '', $start, $length );

		$offset = strpos( $output, 'id="event_image"' );
		if ( false !== $offset ) {
			$offset = strpos( $output, '>', $offset ) + 1;
		}

		$output = substr_replace( $output, "\n" . $button, $offset, 0 );
	}

	$output = str_replace( 'class="choose-file tribe-button tribe-button-secondary btn btn-secondary ', 'class="choose-file tribe-button tribe-button-secondary btn btn-outline-secondary ', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_part_community_modules_image_output', 'enlightenment_tribe_filter_template_part_community_modules_image_output', 12 );

function enlightenment_tribe_filter_template_part_community_modules_virtual_output( $output ) {
	$output = str_replace( 'class="tribe-configure-virtual-button button btn btn-secondary ', 'class="tribe-configure-virtual-button button btn btn-outline-secondary ', $output );
	$output = str_replace( 'class="tribe-configure-virtual-button button btn btn-secondary"', 'class="tribe-configure-virtual-button button btn btn-outline-secondary"', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_part_community_modules_virtual_output', 'enlightenment_tribe_filter_template_part_community_modules_virtual_output', 12 );

function enlightenment_tribe_filter_template_virtual_metabox_autodetect_components_button_output( $output ) {
	if ( isset( $_SERVER['HTTP_REFERER'] ) && false !== strpos( $_SERVER['HTTP_REFERER'], '/wp-admin/' ) ) {
		return $output;
	}

	return str_replace( 'class="button tribe-events-virtual-video-source-autodetect__button btn btn-secondary"', 'class="button tribe-events-virtual-video-source-autodetect__button btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_tribe_filter_template_virtual_metabox_autodetect_components_button_output', 'enlightenment_tribe_filter_template_virtual_metabox_autodetect_components_button_output', 12 );
add_filter( 'enlightenment_tribe_filter_template__virtual_metabox_autodetect_components_button_output', 'enlightenment_tribe_filter_template_virtual_metabox_autodetect_components_button_output', 12 );

function enlightenment_tribe_filter_template_virtual_metabox_api_accounts_output( $output ) {
	if ( isset( $_SERVER['HTTP_REFERER'] ) && false !== strpos( $_SERVER['HTTP_REFERER'], '/wp-admin/' ) ) {
		return $output;
	}

	return str_replace( 'class="button tec-events-virtual-meetings-api-action__account-select-link btn btn-secondary"', 'class="button tec-events-virtual-meetings-api-action__account-select-link btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_tribe_filter_template_virtual_metabox_api_accounts_output', 'enlightenment_tribe_filter_template_virtual_metabox_api_accounts_output', 12 );

function enlightenment_tribe_filter_template_virtual_metabox_api_setup_link_output( $output, $template_name, $file, $template ) {
	if ( isset( $_SERVER['HTTP_REFERER'] ) && false !== strpos( $_SERVER['HTTP_REFERER'], '/wp-admin/' ) ) {
		return $output;
	}

	$context = $template->get_values();

	return str_replace(
		sprintf( 'class="tribe-events-virtual-meetings-%s__connect-link btn btn-secondary"', esc_attr( $context['api_id'] ) ),
		sprintf( 'class="tribe-events-virtual-meetings-%s__connect-link btn btn-outline-secondary"', esc_attr( $context['api_id'] ) ),
		$output
	);
}
add_filter( 'enlightenment_tribe_filter_template_virtual_metabox_api_setup_link_output', 'enlightenment_tribe_filter_template_virtual_metabox_api_setup_link_output', 12, 4 );

function enlightenment_tribe_filter_template_virtual_metabox_zoom_accounts_output( $output ) {
	if ( isset( $_SERVER['HTTP_REFERER'] ) && false !== strpos( $_SERVER['HTTP_REFERER'], '/wp-admin/' ) ) {
		return $output;
	}

	$output = str_replace( 'class="tribe-dependent tribe-events-virtual-meetings-zoom-details__generate-zoom-button button btn btn-secondary text-nowrap"', 'class="tribe-dependent tribe-events-virtual-meetings-zoom-details__generate-zoom-button button btn btn-outline-secondary text-nowrap"', $output );
	$output = str_replace( 'class="button tribe-events-virtual-meetings-zoom-details__account-select-link btn btn-secondary"', 'class="button tribe-events-virtual-meetings-zoom-details__account-select-link btn btn-outline-secondary"', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_virtual_metabox_zoom_accounts_output', 'enlightenment_tribe_filter_template_virtual_metabox_zoom_accounts_output', 12 );

function enlightenment_tribe_filter_template_virtual_metabox_zoom_setup_output( $output ) {
	if ( isset( $_SERVER['HTTP_REFERER'] ) && false !== strpos( $_SERVER['HTTP_REFERER'], '/wp-admin/' ) ) {
		return $output;
	}

	$output = str_replace( 'class="tribe-dropdown form-select ', 'class="tribe-dropdown ', $output );
	$output = str_replace( 'class="button tribe-events-virtual-meetings-zoom-details__create-link btn btn-secondary"', 'class="button tribe-events-virtual-meetings-zoom-details__create-link btn btn-outline-secondary"', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_virtual_metabox_zoom_setup_output', 'enlightenment_tribe_filter_template_virtual_metabox_zoom_setup_output', 12 );

function enlightenment_tribe_filter_template_virtual_metabox_zoom_details_output( $output ) {
	if ( isset( $_SERVER['HTTP_REFERER'] ) && false !== strpos( $_SERVER['HTTP_REFERER'], '/wp-admin/' ) ) {
		return $output;
	}

	return str_replace( 'class="tribe-dropdown form-select ', 'class="tribe-dropdown ', $output );
}
add_filter( 'enlightenment_tribe_filter_template_virtual_metabox_zoom_details_output', 'enlightenment_tribe_filter_template_virtual_metabox_zoom_details_output', 12 );

function enlightenment_tribe_filter_template_virtual_metabox_zoom_components_multiselect_output( $output ) {
	if ( isset( $_SERVER['HTTP_REFERER'] ) && false !== strpos( $_SERVER['HTTP_REFERER'], '/wp-admin/' ) ) {
		return $output;
	}

	return str_replace( 'class="tribe-dropdown form-select ', 'class="tribe-dropdown ', $output );
}
add_filter( 'enlightenment_tribe_filter_template_virtual_metabox_zoom_components_multiselect_output', 'enlightenment_tribe_filter_template_virtual_metabox_zoom_components_multiselect_output', 12 );

function enlightenment_tribe_filter_template_virtual_metabox_zoom_controls_output( $output ) {
	if ( isset( $_SERVER['HTTP_REFERER'] ) && false !== strpos( $_SERVER['HTTP_REFERER'], '/wp-admin/' ) ) {
		return $output;
	}

	$output = str_replace( 'class="button tribe-events-virtual-meetings-zoom__create-link btn btn-secondary ', 'class="button tribe-events-virtual-meetings-zoom__create-link btn btn-outline-secondary ', $output );
	$output = str_replace( 'class="tribe-events-virtual-meetings-zoom__connect-link btn btn-secondary ', 'class="tribe-events-virtual-meetings-zoom__connect-link btn btn-outline-secondary ', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_virtual_metabox_zoom_controls_output', 'enlightenment_tribe_filter_template_virtual_metabox_zoom_controls_output', 12 );

function enlightenment_tribe_filter_template_virtual_metabox_zoom_multiple_controls_output( $output ) {
	if ( isset( $_SERVER['HTTP_REFERER'] ) && false !== strpos( $_SERVER['HTTP_REFERER'], '/wp-admin/' ) ) {
		return $output;
	}

	return str_replace( 'class="button tribe-events-virtual-meetings-zoom-controls__accordion-element tribe-events-virtual-meetings-zoom-controls__accordion-toggle btn btn-secondary ', 'class="button tribe-events-virtual-meetings-zoom-controls__accordion-element tribe-events-virtual-meetings-zoom-controls__accordion-toggle btn btn-outline-secondary ', $output );
}
add_filter( 'enlightenment_tribe_filter_template_virtual_metabox_zoom_multiple_controls_output', 'enlightenment_tribe_filter_template_virtual_metabox_zoom_multiple_controls_output', 12 );

function enlightenment_tribe_filter_template_virtual_metabox_zoom_account_disabled_details_output( $output ) {
	if ( isset( $_SERVER['HTTP_REFERER'] ) && false !== strpos( $_SERVER['HTTP_REFERER'], '/wp-admin/' ) ) {
		return $output;
	}

	return str_replace( 'class="tribe-events-virtual-meetings-zoom-error__link-connect btn btn-danger"', 'class="tribe-events-virtual-meetings-zoom-error__link-connect btn btn-outline-danger"', $output );
}
add_filter( 'enlightenment_tribe_filter_template_virtual_metabox_zoom_account_disabled_details_output', 'enlightenment_tribe_filter_template_virtual_metabox_zoom_account_disabled_details_output', 12 );

function enlightenment_tribe_filter_template_virtual_metabox_zoom_meeting_link_error_details_output( $output ) {
	if ( isset( $_SERVER['HTTP_REFERER'] ) && false !== strpos( $_SERVER['HTTP_REFERER'], '/wp-admin/' ) ) {
		return $output;
	}

	$output = str_replace( 'class="button button-secondary tribe-events-virtual-meetings-zoom__create-link btn btn-secondary"', 'class="button button-secondary tribe-events-virtual-meetings-zoom__create-link btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="tribe-events-virtual-meetings-zoom-error__link-connect btn btn-secondary"', 'class="tribe-events-virtual-meetings-zoom-error__link-connect btn btn-outline-secondary"', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_virtual_metabox_zoom_meeting_link_error_details_output', 'enlightenment_tribe_filter_template_virtual_metabox_zoom_meeting_link_error_details_output', 12 );

function enlightenment_tribe_filter_template_part_community_modules_venue_output( $output ) {
	$output = str_replace( 'class="dashicons dashicons-screenoptions move-linked-post-group"', 'class="move-linked-post-group fas fa-grip-vertical fa-fw"', $output );
	$output = str_replace( 'class="tribe-add-post tribe-button tribe-button-secondary btn btn-secondary"', 'class="tribe-add-post tribe-button tribe-button-secondary btn btn-outline-secondary"', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_part_integrations_the_events_calendar_modules_venue_output', 'enlightenment_tribe_filter_template_part_community_modules_venue_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_part_community_modules_venue_output', 'enlightenment_tribe_filter_template_part_community_modules_venue_output', 12 );

function enlightenment_tribe_filter_template_part_community_modules_organizer_output( $output ) {
	$output = str_replace( 'class="dashicons dashicons-screenoptions move-linked-post-group"', 'class="move-linked-post-group fas fa-grip-vertical fa-fw"', $output );
	$output = str_replace( 'class="tribe-add-post tribe-button tribe-button-secondary btn btn-secondary"', 'class="tribe-add-post tribe-button tribe-button-secondary btn btn-outline-secondary"', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_part_integrations_the_events_calendar_modules_organizer_output', 'enlightenment_tribe_filter_template_part_community_modules_organizer_output', 12 );
add_filter( 'enlightenment_tribe_filter_template_part_community_modules_organizer_output', 'enlightenment_tribe_filter_template_part_community_modules_organizer_output', 12 );

function enlightenment_tribe_filter_template_checkout_header_links_output( $output ) {
	return str_replace( 'class="tribe-common-anchor-alt btn btn-secondary ', 'class="tribe-common-anchor-alt btn btn-outline-secondary ', $output );
}
add_filter( 'enlightenment_tribe_filter_template_checkout_header_links_output', 'enlightenment_tribe_filter_template_checkout_header_links_output', 12 );

function enlightenment_theme_tribe_filter_checkout_links( $output ) {
	return str_replace( 'class="tribe-checkout-backlink btn btn-secondary"', 'class="tribe-checkout-backlink btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_tribe_filter_checkout_links', 'enlightenment_theme_tribe_filter_checkout_links', 12 );

function enlightenment_tribe_filter_template_order_footer_links_back_home_output( $output ) {
	return str_replace( 'class="tribe-common-anchor-alt tribe-tickets__commerce-order-footer-link btn btn-secondary ', 'class="tribe-common-anchor-alt tribe-tickets__commerce-order-footer-link btn btn-outline-secondary ', $output );
}
add_filter( 'enlightenment_tribe_filter_template_order_footer_links_back_home_output', 'enlightenment_tribe_filter_template_order_footer_links_back_home_output', 12 );

function enlightenment_tribe_filter_template_gateway_stripe_payment_element_output( $output ) {
	return str_replace( 'class="tribe-common-c-btn tribe-tickets__commerce-checkout-form-submit-button btn btn-primary ', 'class="tribe-common-c-btn tribe-tickets__commerce-checkout-form-submit-button btn btn-danger ', $output );
}
add_filter( 'enlightenment_tribe_filter_template_gateway_stripe_payment_element_output', 'enlightenment_tribe_filter_template_gateway_stripe_payment_element_output', 12 );

function enlightenment_tribe_filter_template_blocks_rsvp_icon_svg_output( $output ) {
	return '<i class="far fa-envelope"></i>';
}
add_filter( 'enlightenment_tribe_filter_template_blocks_rsvp_icon_svg_output', 'enlightenment_tribe_filter_template_blocks_rsvp_icon_svg_output' );

function enlightenment_tribe_filter_template_blocks_rsvp_status_not_going_output( $output ) {
	return str_replace( 'class="tribe-block__rsvp__status-button btn btn-secondary ', 'class="tribe-block__rsvp__status-button btn btn-outline-secondary ', $output );
}
add_filter( 'enlightenment_tribe_filter_template_blocks_rsvp_status_not_going_output', 'enlightenment_tribe_filter_template_blocks_rsvp_status_not_going_output', 12 );

function enlightenment_tribe_filter_template_v2_rsvp_actions_rsvp_not_going_output( $output ) {
	return str_replace( 'class="tribe-common-cta tribe-common-cta--alt tribe-tickets__rsvp-actions-button-not-going btn btn-secondary"', 'class="tribe-common-cta tribe-common-cta--alt tribe-tickets__rsvp-actions-button-not-going btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_tribe_filter_template_v2_rsvp_actions_rsvp_not_going_output', 'enlightenment_tribe_filter_template_v2_rsvp_actions_rsvp_not_going_output', 12 );

function enlightenment_tribe_filter_template_v2_rsvp_form_fields_cancel_output( $output ) {
	return str_replace( 'class="tribe-common-h7 tribe-tickets__rsvp-form-button tribe-tickets__rsvp-form-button--cancel btn btn-secondary ', 'class="tribe-common-h7 tribe-tickets__rsvp-form-button tribe-tickets__rsvp-form-button--cancel btn btn-outline-secondary ', $output );
}
add_filter( 'enlightenment_tribe_filter_template_v2_rsvp_form_fields_cancel_output', 'enlightenment_tribe_filter_template_v2_rsvp_form_fields_cancel_output', 12 );

function enlightenment_tribe_filter_template_v2_rsvp_ari_form_buttons_output( $output ) {
	$output = str_replace( 'class="tribe-common-h7 tribe-tickets__rsvp-form-button tribe-tickets__rsvp-form-button--cancel btn btn-secondary ', 'class="tribe-common-h7 tribe-tickets__rsvp-form-button tribe-tickets__rsvp-form-button--cancel btn btn-outline-secondary ', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_v2_rsvp_ari_form_buttons_output', 'enlightenment_tribe_filter_template_v2_rsvp_ari_form_buttons_output', 12 );

function enlightenment_tribe_filter_template_v2_tickets_footer_return_to_cart_output( $output ) {
	return str_replace( 'class="tribe-common-b2 tribe-tickets__tickets-footer-back-link btn btn-secondary ', 'class="tribe-common-b2 tribe-tickets__tickets-footer-back-link btn btn-outline-secondary ', $output );
}
add_filter( 'enlightenment_tribe_filter_template_v2_tickets_footer_return_to_cart_output', 'enlightenment_tribe_filter_template_v2_tickets_footer_return_to_cart_output', 12 );

function enlightenment_tribe_filter_template_blocks_rsvp_status_going_icon_output( $output ) {
	return '<i class="fas fa-check" role="presentation" aria-hidden="true"></i>';
}
add_filter( 'enlightenment_tribe_filter_template_blocks_rsvp_status_going_icon_output', 'enlightenment_tribe_filter_template_blocks_rsvp_status_going_icon_output' );

function enlightenment_tribe_filter_template_blocks_rsvp_status_not_going_icon_output( $output ) {
	return '<i class="fas fa-times"></i>';
}
add_filter( 'enlightenment_tribe_filter_template_blocks_rsvp_status_not_going_icon_output', 'enlightenment_tribe_filter_template_blocks_rsvp_status_not_going_icon_output' );

function enlightenment_tribe_bootstrap_filter_template_v2_tickets_item_quantity_manipulate_button( $output ) {
	return str_replace( 'class="btn btn-light"', 'class="btn btn-theme-inverse"', $output );
}
add_filter( 'enlightenment_tribe_filter_template_v2_tickets_item_quantity_remove_output', 'enlightenment_tribe_bootstrap_filter_template_v2_tickets_item_quantity_manipulate_button', 12 );
add_filter( 'enlightenment_tribe_filter_template_v2_tickets_item_quantity_add_output', 'enlightenment_tribe_bootstrap_filter_template_v2_tickets_item_quantity_manipulate_button', 12 );

function enlightenment_tribe_filter_events_tickets_attendee_registration_modal_content( $output ) {
	return str_replace( 'class="tribe-common-c-btn-link btn btn-secondary ', 'class="tribe-common-c-btn-link btn btn-outline-secondary ', $output );
}
add_filter( 'tribe_events_tickets_attendee_registration_modal_content', 'enlightenment_tribe_filter_events_tickets_attendee_registration_modal_content', 14 );

function enlightenment_tribe_filter_template_v2_modal_attendee_registration_footer_output( $output ) {
	return str_replace( 'class="tribe-common-c-btn-link btn btn-secondary ', 'class="tribe-common-c-btn-link btn btn-outline-secondary ', $output );
}
add_filter( 'enlightenment_tribe_filter_template_v2_modal_attendee_registration_footer_output', 'enlightenment_tribe_filter_template_v2_modal_attendee_registration_footer_output', 12 );

function enlightenment_tribe_filter_template_v2_attendee_registration_button_back_to_cart_output( $output ) {
	return str_replace( 'class="tribe-tickets__registration-back-to-cart btn btn-secondary"', 'class="tribe-tickets__registration-back-to-cart btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_tribe_filter_template_v2_attendee_registration_button_back_to_cart_output', 'enlightenment_tribe_filter_template_v2_attendee_registration_button_back_to_cart_output', 12 );

function enlightenment_tribe_filter_template_part_tickets_plus_orders_edit_meta_output( $output ) {
	return str_replace( 'class="attendee-meta toggle btn btn-secondary"', 'class="attendee-meta toggle btn btn-outline-secondary" href="javascript:void(0)"', $output );
}
add_filter( 'enlightenment_tribe_filter_template_part_tickets_plus_orders_edit_meta_output', 'enlightenment_tribe_filter_template_part_tickets_plus_orders_edit_meta_output', 12 );

function enlightenment_tribe_filter_template_components_pdf_button_output( $output ) {
	return str_replace( 'class="tribe-common-c-btn-border tec-tickets__wallet-plus-component-pdf-button-link btn btn-secondary"', 'class="tribe-common-c-btn-border tec-tickets__wallet-plus-component-pdf-button-link btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_tribe_filter_template_pdf_button_output', 'enlightenment_tribe_filter_template_components_pdf_button_output', 12 );

function enlightenment_tribe_filter_template_apple_wallet_button_output( $output ) {
	return str_replace( 'class="tribe-common-c-btn-border tec-tickets__wallet-plus-component-apple-wallet-button-link btn btn-secondary"', 'class="tribe-common-c-btn-border tec-tickets__wallet-plus-component-apple-wallet-button-link btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_tribe_filter_template_apple_wallet_button_output', 'enlightenment_tribe_filter_template_apple_wallet_button_output', 12 );

function enlightenment_theme_tribe_filter_template_blocks_attendees_output( $output ) {
	$offset = strpos( $output, '<img ' );
	if ( false !== $offset ) {
		$output = substr_replace( $output, '<div class="tribe-block__attendees__avatars">' . "\n", $offset, 0 );
	}

	$offset = strrpos( $output, '<img ' );
	if ( false !== $offset ) {
		$offset = strpos( $output, '>', $offset );
		$output = substr_replace( $output, "\n" . '</div>', $offset + 1, 0 );
	}

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_blocks_attendees_output', 'enlightenment_theme_tribe_filter_template_blocks_attendees_output' );

function enlightenment_theme_tribe_filter_woocommerce_event_info( $output ) {
	return str_replace( 'class="dropdown-menu"', 'class="dropdown-menu dropdown-menu-end"', $output );
}
add_filter( 'enlightenment_tribe_filter_woocommerce_event_info', 'enlightenment_theme_tribe_filter_woocommerce_event_info', 12 );

function enlightenment_tribe_list_widget_date_tag() {
	$start_date   = strtotime( tribe_get_start_date( null, false ) );
	$request_date = strtotime( Tribe__Date_Utils::build_date_object()->setTime( 0, 0, 0 )->format( Tribe__Date_Utils::DBDATETIMEFORMAT ) );
	$display_date = tribe_is_past_event() && ! empty( $request_date ) ? max( $start_date, $request_date ) : $start_date;

	$event_week_day  = date_i18n( 'D', $start_date );
	$event_day_num   = date_i18n( 'j', $start_date );
	$event_date_attr = date_i18n( Tribe__Date_Utils::DBDATEFORMAT, $start_date );

	?>
	<div class="tribe-event-date-tag">
		<time class="tribe-event-date-tag-datetime" datetime="<?php echo esc_attr( $event_date_attr ); ?>">
			<span class="tribe-event-date-tag-weekday">
				<?php echo esc_html( $event_week_day ); ?>
			</span>
			<span class="tribe-event-date-tag-daynum tribe-common-h5 tribe-common-h4--min-medium">
				<?php echo esc_html( $event_day_num ); ?>
			</span>
		</time>
	</div>
	<?php
}
add_action( 'tribe_events_list_widget_before_the_event_title', 'enlightenment_tribe_list_widget_date_tag' );

function enlightenment_theme_tribe_filter_list_widget( $output ) {
	$output = str_replace( 'class="btn btn-secondary"', 'class="btn btn-outline-secondary"', $output );

	if ( doing_filter( 'the_content' ) ) {
		$output = sprintf( '<div class="tribe-events-adv-list-widget">%s</div>', $output );
	}

	return $output;
}
add_filter( 'enlightenment_tribe_filter_list_widget', 'enlightenment_theme_tribe_filter_list_widget', 12 );

function enlightenment_tribe_filter_template_widgets_widget_events_list_view_more_output( $output ) {
	return str_replace( 'class="tribe-events-widget-events-list__view-more-link tribe-common-anchor-thin btn btn-secondary"', 'class="tribe-events-widget-events-list__view-more-link tribe-common-anchor-thin btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_tribe_filter_template_widgets_widget_events_list_view_more_output', 'enlightenment_tribe_filter_template_widgets_widget_events_list_view_more_output', 12 );

function enlightenment_theme_tribe_filter_venue_widget( $output ) {
	return str_replace( 'class="btn btn-secondary"', 'class="btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_tribe_filter_venue_widget', 'enlightenment_theme_tribe_filter_venue_widget', 12 );

function enlightenment_tribe_filter_template_widgets_widget_featured_venue_view_more_output( $output ) {
	return str_replace( 'class="tribe-common-anchor-thin tribe-events-widget-featured-venue__view-more-link btn btn-secondary"', 'class="tribe-common-anchor-thin tribe-events-widget-featured-venue__view-more-link btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_tribe_filter_template_widgets_widget_featured_venue_view_more_output', 'enlightenment_tribe_filter_template_widgets_widget_featured_venue_view_more_output', 12 );

function enlightenment_mini_helper_tribe_events_ajax_list_daynumber( $output, $post_date ) {
	return date_i18n( 'j', $post_date );
}
add_filter( 'tribe-mini_helper_tribe_events_ajax_list_daynumber', 'enlightenment_mini_helper_tribe_events_ajax_list_daynumber', 10, 2 );

function enlightenment_tribe_filter_shortcode( $output, $atts ) {
	if ( is_array( $atts ) && isset( $atts['layout'] ) && 'vertical' == $atts['layout'] ) {
		return $output;
	}

	$output = str_replace( 'class="widget widget_tribe-widget-events-week"', 'class="widget widget_tribe-widget-events-week alignwide"', $output );
	$output = str_replace( 'class="tribe-this-week-events-widget"', 'class="tribe-this-week-events-widget alignwide"', $output );

	return $output;
}
add_filter( 'enlightenment_filter_shortcode_tag_tribe_this_week', 'enlightenment_tribe_filter_shortcode', 10, 2 );

function enlightenment_theme_tribe_filter_event_links_block( $output ) {
	$output = str_replace( sprintf( '<img src="%ssrc/modules/icons/link.svg" />', Tribe__Main::instance()->plugin_url ), '', $output );
    $output = str_replace( '<a class="btn btn-secondary"', '<a class="btn btn-outline-secondary"', $output );

	return $output;
}
add_filter( 'enlightenment_render_block_tribe_event_links', 'enlightenment_theme_tribe_filter_event_links_block', 12 );

function enlightenment_tribe_bootstrap_template_editor_metabox_output( $output ) {
	return str_replace( 'class="spinner"', 'class="spinner fas fa-spinner fa-pulse"', $output );
}
add_filter( 'enlightenment_tribe_filter_template_editor_metabox_output', 'enlightenment_tribe_bootstrap_template_editor_metabox_output', 12 );

function enlightenment_tribe_filter_template_editor_panel_list_output( $output ) {
	$output = str_replace( 'class="btn btn-secondary ', 'class="btn btn-outline-secondary ', $output );
	$output = str_replace( 'class="btn btn-secondary"', 'class="btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="dashicons dashicons-screenoptions tribe-handle"', 'class="fas fa-grip-vertical fa-fw tribe-handle"', $output );
	$output = str_replace( 'class="dashicons dashicons-warning"', 'class="fas fa-exclamation-circle"', $output );
	$output = str_replace( 'class="dashicons dashicons-no"', 'class="fas fa-times"', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_editor_panel_list_output', 'enlightenment_tribe_filter_template_editor_panel_list_output', 12 );

function enlightenment_tribe_filter_template_editor_panel_ticket_output( $output ) {
	$output = str_replace( 'class="dashicons dashicons-editor-help ', 'class="fas fa-question-circle ', $output );
	$output = str_replace( 'class="dashicons dashicons-editor-help"', 'class="fas fa-question-circle"', $output );
	$output = str_replace( 'class="button-secondary btn btn-secondary ms-2"', 'class="button-secondary btn btn-outline-secondary ms-2"', $output );
	$output = str_replace( 'class="thickbox tribe-ticket-move-link btn btn-secondary me-2"', 'class="thickbox tribe-ticket-move-link btn btn-outline-secondary me-2"', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_editor_panel_ticket_output', 'enlightenment_tribe_filter_template_editor_panel_ticket_output', 12 );

function enlightenment_tribe_filter_template_editor_panel_settings_output( $output ) {
	$output = str_replace( 'class="button btn btn-secondary ', 'class="button btn btn-outline-secondary ', $output );
	$output = str_replace( 'class="dashicons dashicons-format-image"', 'class="far fa-image"', $output );
	$output = str_replace( 'class="button-secondary btn btn-secondary"', 'class="button-secondary btn btn-outline-secondary"', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_editor_panel_settings_output', 'enlightenment_tribe_filter_template_editor_panel_settings_output', 12 );

function enlightenment_tribe_filter_template_editor_fieldset_settings_capacity_output( $output ) {
	return str_replace( 'class="global_capacity_edit_button tribe-button-icon tribe-button-icon-edit"', 'class="global_capacity_edit_button tribe-button-icon tribe-button-icon-edit fas fa-edit"', $output );
}
add_filter( 'enlightenment_tribe_filter_template_editor_fieldset_settings_capacity_output', 'enlightenment_tribe_filter_template_editor_fieldset_settings_capacity_output', 12 );

function enlightenment_tribe_filter_template_attendees_output( $output ) {
	$output = str_replace( 'class="welcome-panel-column card welcome-panel-last alternate text-bg-light', 'class="welcome-panel-column card welcome-panel-last alternate text-bg-theme-inverse', $output );
	$output = str_replace( 'class="btn btn-secondary"', 'class="btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="dashicons dashicons-info ', 'class="fas fa-info-circle ', $output );
	$output = str_replace( 'class="button btn btn-secondary"', 'class="button btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="button action btn btn-secondary"', 'class="button action btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="print button action btn btn-secondary"', 'class="print button action btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="export button action btn btn-secondary"', 'class="export button action btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="email button action thickbox btn btn-secondary"', 'class="email button action thickbox btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="tablenav-pages-navspan button disabled btn btn-secondary"', 'class="tablenav-pages-navspan button disabled btn btn-outline-secondary"', $output );
	$output = str_replace( "class='first-page button btn btn-secondary'", "class='first-page button btn btn-outline-secondary'", $output );
	$output = str_replace( "class='prev-page button btn btn-secondary'", "class='prev-page button btn btn-outline-secondary'", $output );
	$output = str_replace( "class='next-page button btn btn-secondary'", "class='next-page button btn btn-outline-secondary'", $output );
	$output = str_replace( "class='last-page button btn btn-secondary'", "class='last-page button btn btn-outline-secondary'", $output );
	$output = str_replace( 'class="button-primary  tickets_checkin btn btn-secondary"', 'class="button-primary  tickets_checkin btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="button-secondary tickets_uncheckin btn btn-secondary"', 'class="button-secondary tickets_uncheckin btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="button-secondary tickets-checkin btn btn-secondary"', 'class="button-secondary tickets-checkin btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="components-button btn btn-secondary ', 'class="components-button btn btn-outline-secondary ', $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_template_attendees_output', 'enlightenment_tribe_filter_template_attendees_output', 12 );

function enlightenment_theme_tribe_filter_order_report( $output ) {
	$output = str_replace( 'class="welcome-panel-column card welcome-panel-last alternate text-bg-light', 'class="welcome-panel-column card welcome-panel-last alternate text-bg-theme-inverse', $output );
	$output = str_replace( 'class="dashicons dashicons-info ', 'class="fas fa-info-circle ', $output );
	$output = str_replace( 'class="btn btn-secondary"', 'class="btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="tablenav-pages-navspan button disabled btn btn-secondary"', 'class="tablenav-pages-navspan button disabled btn btn-outline-secondary"', $output );
	$output = str_replace( "class='first-page button btn btn-secondary'", "class='first-page button btn btn-outline-secondary'", $output );
	$output = str_replace( "class='prev-page button btn btn-secondary'", "class='prev-page button btn btn-outline-secondary'", $output );
	$output = str_replace( "class='next-page button btn btn-secondary'", "class='next-page button btn btn-outline-secondary'", $output );
	$output = str_replace( "class='last-page button btn btn-secondary'", "class='last-page button btn btn-outline-secondary'", $output );

	return $output;
}
add_filter( 'enlightenment_tribe_filter_order_report', 'enlightenment_theme_tribe_filter_order_report', 12 );

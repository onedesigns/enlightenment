<?php

function enlightenment_bbp_default_theme_mods( $mods ) {
	return array_merge( $mods, array(
		'bbp_header_style' => 'dark',
	) );
}
add_filter( 'enlightenment_default_theme_mods', 'enlightenment_bbp_default_theme_mods' );

function enlightenment_bbp_styles() {
    wp_dequeue_style( 'bbp-default' );
    wp_dequeue_style( 'bbp-default-rtl' );

	wp_enqueue_style( 'enlightenment-bbp', get_theme_file_uri( 'assets/css/bbpress.css' ), array( 'enlightenment-theme-stylesheet' ), null );
}
add_action( 'bbp_enqueue_scripts', 'enlightenment_bbp_styles', 12 );

function enlightenment_bbp_maybe_remove_header_image( $url ) {
	if ( bbp_has_shortcode() ) {
		$url = '';
	}

	return $url;
}
add_filter( 'theme_mod_header_image', 'enlightenment_bbp_maybe_remove_header_image' );

function enlightenment_bbp_filter_custom_header_markup( $output ) {
    if( bbp_is_single_user() ) {
        $output = '';
    }

    return $output;
}
add_filter( 'enlightenment_custom_header_markup', 'enlightenment_bbp_filter_custom_header_markup' );

function enlightenment_bbp_cover_image() {
    echo '<div class="cover-image"></div>';
}

function enlightenment_bbp_maybe_add_cover_image() {
    if ( ! bbp_is_single_user() ) {
        return;
    }

    if ( function_exists( 'is_buddypress' ) && is_buddypress() ) {
        return;
    }

    add_action( 'enlightenment_page_header', 'enlightenment_bbp_cover_image', 6 );
}
add_action( 'enlightenment_before_page_header', 'enlightenment_bbp_maybe_add_cover_image', 999 );

function enlightenment_theme_bbp_filter_breadcrumb_args( $args ) {
	if ( ! empty( $args['current_text'] ) ) {
		$offset = strpos( $args['current_before'], '>' );

		if ( false !== $offset ) {
			$args['current_before'] = substr_replace( $args['current_before'], sprintf( ' title="%s"', esc_attr( $args['current_text'] ) ), $offset, 0 );
		}
	}

	return $args;
}
add_filter( 'bbp_after_get_breadcrumb_parse_args', 'enlightenment_theme_bbp_filter_breadcrumb_args' );

function enlightenment_theme_bbp_layout_hooks() {
    if ( ! is_bbpress() && ! bbp_is_forum_edit() ) {
		return;
	}

    if ( function_exists( 'is_buddypress' ) && is_buddypress() ) {
        return;
    }

	if ( bbp_is_forum_archive() || bbp_is_topic_archive() || bbp_is_topic_tag() || bbp_is_single_view() || bbp_is_search() ) {
		remove_action( 'enlightenment_page_header', 'enlightenment_breadcrumbs' );
        remove_action( 'enlightenment_before_page_content', 'enlightenment_bbp_form_search' );

        add_action( 'enlightenment_page_header', 'enlightenment_bbp_form_search' );
	} elseif ( bbp_is_single_forum() ) {
		if ( has_header_image() ) {
	        remove_action( 'enlightenment_before_site_content', 'enlightenment_open_row', 998 );
			remove_action( 'enlightenment_before_site_content', 'enlightenment_bootstrap_open_page_content_container', 999 );
		}

		remove_action( 'enlightenment_before_entry_content', 'enlightenment_breadcrumbs' );
        remove_action( 'enlightenment_before_entry_content', 'enlightenment_bbp_forum_user_actions' );

		if ( has_header_image() ) {
			remove_action( 'enlightenment_after_site_content', 'enlightenment_bootstrap_close_page_content_container', 1 );
	        remove_action( 'enlightenment_after_site_content', 'get_sidebar' );
	        remove_action( 'enlightenment_after_site_content', 'enlightenment_close_row', 11 );
		}

        if ( has_header_image() ) {
            add_action( 'enlightenment_entry_header', 'enlightenment_open_container', 8 );
        }

		add_action( 'enlightenment_entry_header', 'enlightenment_breadcrumbs' );
        add_action( 'enlightenment_entry_header', 'enlightenment_bbp_forum_user_actions' );

        if ( has_header_image() ) {
            add_action( 'enlightenment_entry_header', 'enlightenment_close_container', 12 );
        }

		if ( has_header_image() ) {
	        add_action( 'enlightenment_after_entry_header', 'enlightenment_open_row', 998 );
			add_action( 'enlightenment_after_entry_header', 'enlightenment_bootstrap_open_page_content_container', 999 );

			add_action( 'enlightenment_after_entry_footer', 'enlightenment_bootstrap_close_page_content_container', 1 );
	        add_action( 'enlightenment_after_entry_footer', 'get_sidebar' );
	        add_action( 'enlightenment_after_entry_footer', 'enlightenment_close_row', 11 );
		}
    } elseif ( bbp_is_forum_edit() ) {
		// remove_action( 'enlightenment_before_entry_content', 'enlightenment_breadcrumbs' );
		if ( has_header_image() ) {
	        remove_action( 'enlightenment_before_site_content', 'enlightenment_open_row', 998 );
			remove_action( 'enlightenment_before_site_content', 'enlightenment_bootstrap_open_page_content_container', 999 );
			remove_action( 'enlightenment_after_site_content', 'enlightenment_bootstrap_close_page_content_container', 1 );
	        remove_action( 'enlightenment_after_site_content', 'get_sidebar' );
	        remove_action( 'enlightenment_after_site_content', 'enlightenment_close_row', 11 );

			add_action( 'enlightenment_entry_header', 'enlightenment_open_container', 8 );
		    add_action( 'enlightenment_entry_header', 'enlightenment_close_container', 12 );

			add_action( 'enlightenment_after_entry_header', 'enlightenment_open_row', 998 );
			add_action( 'enlightenment_after_entry_header', 'enlightenment_bootstrap_open_page_content_container', 999 );

			add_action( 'enlightenment_after_entry_footer', 'enlightenment_bootstrap_close_page_content_container', 1 );
	        add_action( 'enlightenment_after_entry_footer', 'get_sidebar' );
	        add_action( 'enlightenment_after_entry_footer', 'enlightenment_close_row', 11 );
		}
    } elseif ( bbp_is_single_topic() ) {
		if ( has_header_image() ) {
	        remove_action( 'enlightenment_before_site_content', 'enlightenment_open_row', 998 );
			remove_action( 'enlightenment_before_site_content', 'enlightenment_bootstrap_open_page_content_container', 999 );
		}

        remove_action( 'enlightenment_before_entry_content', 'enlightenment_breadcrumbs' );
		remove_action( 'enlightenment_before_entry_content', 'enlightenment_bbp_topic_user_actions' );
        remove_action( 'enlightenment_before_entry_content', 'enlightenment_bbp_topic_tag_list' );

		if ( has_header_image() ) {
			remove_action( 'enlightenment_after_site_content', 'enlightenment_bootstrap_close_page_content_container', 1 );
	        remove_action( 'enlightenment_after_site_content', 'get_sidebar' );
	        remove_action( 'enlightenment_after_site_content', 'enlightenment_close_row', 11 );
		}

        if ( has_header_image() ) {
            add_action( 'enlightenment_entry_header', 'enlightenment_open_container', 8 );
        }

        add_action( 'enlightenment_entry_header', 'enlightenment_breadcrumbs' );
		add_action( 'enlightenment_entry_header', 'enlightenment_bbp_topic_user_actions' );

        if ( has_header_image() ) {
            add_action( 'enlightenment_entry_header', 'enlightenment_close_container', 12 );
        }

		if ( has_header_image() ) {
	        add_action( 'enlightenment_after_entry_header', 'enlightenment_open_row', 998 );
			add_action( 'enlightenment_after_entry_header', 'enlightenment_bootstrap_open_page_content_container', 999 );
		}

		add_action( 'enlightenment_after_entry_content', 'enlightenment_bbp_topic_tag_list' );

		if ( has_header_image() ) {
			add_action( 'enlightenment_after_entry_footer', 'enlightenment_bootstrap_close_page_content_container', 1 );
	        add_action( 'enlightenment_after_entry_footer', 'get_sidebar' );
	        add_action( 'enlightenment_after_entry_footer', 'enlightenment_close_row', 11 );
		}
	} elseif ( bbp_is_topic_edit() ) {
		if ( has_header_image() ) {
	        remove_action( 'enlightenment_before_site_content', 'enlightenment_open_row', 998 );
			remove_action( 'enlightenment_before_site_content', 'enlightenment_bootstrap_open_page_content_container', 999 );
			remove_action( 'enlightenment_after_site_content', 'enlightenment_bootstrap_close_page_content_container', 1 );
	        remove_action( 'enlightenment_after_site_content', 'get_sidebar' );
	        remove_action( 'enlightenment_after_site_content', 'enlightenment_close_row', 11 );

			add_action( 'enlightenment_entry_header', 'enlightenment_open_container', 8 );
		    add_action( 'enlightenment_entry_header', 'enlightenment_close_container', 12 );

			add_action( 'enlightenment_after_entry_header', 'enlightenment_open_row', 998 );
			add_action( 'enlightenment_after_entry_header', 'enlightenment_bootstrap_open_page_content_container', 999 );

			add_action( 'enlightenment_after_entry_footer', 'enlightenment_bootstrap_close_page_content_container', 1 );
	        add_action( 'enlightenment_after_entry_footer', 'get_sidebar' );
	        add_action( 'enlightenment_after_entry_footer', 'enlightenment_close_row', 11 );
		}
    } elseif ( bbp_is_single_reply() ) {
		if ( has_header_image() ) {
	        remove_action( 'enlightenment_before_site_content', 'enlightenment_open_row', 998 );
			remove_action( 'enlightenment_before_site_content', 'enlightenment_bootstrap_open_page_content_container', 999 );
		}

        remove_action( 'enlightenment_before_entry_content', 'enlightenment_breadcrumbs' );

		if ( has_header_image() ) {
			remove_action( 'enlightenment_after_site_content', 'enlightenment_bootstrap_close_page_content_container', 1 );
	        remove_action( 'enlightenment_after_site_content', 'get_sidebar' );
	        remove_action( 'enlightenment_after_site_content', 'enlightenment_close_row', 11 );
		}

        if ( has_header_image() ) {
            add_action( 'enlightenment_entry_header', 'enlightenment_open_container', 8 );
        }

        add_action( 'enlightenment_entry_header', 'enlightenment_breadcrumbs' );

        if ( has_header_image() ) {
            add_action( 'enlightenment_entry_header', 'enlightenment_close_container', 12 );

	        add_action( 'enlightenment_after_entry_header', 'enlightenment_open_row', 998 );
			add_action( 'enlightenment_after_entry_header', 'enlightenment_bootstrap_open_page_content_container', 999 );
        }

		if ( has_header_image() ) {
			add_action( 'enlightenment_after_entry_footer', 'enlightenment_bootstrap_close_page_content_container', 1 );
	        add_action( 'enlightenment_after_entry_footer', 'get_sidebar' );
	        add_action( 'enlightenment_after_entry_footer', 'enlightenment_close_row', 11 );
		}
	} elseif ( bbp_is_reply_edit() ) {
		if ( has_header_image() ) {
	        remove_action( 'enlightenment_before_site_content', 'enlightenment_open_row', 998 );
			remove_action( 'enlightenment_before_site_content', 'enlightenment_bootstrap_open_page_content_container', 999 );
			remove_action( 'enlightenment_after_site_content', 'enlightenment_bootstrap_close_page_content_container', 1 );
	        remove_action( 'enlightenment_after_site_content', 'get_sidebar' );
	        remove_action( 'enlightenment_after_site_content', 'enlightenment_close_row', 11 );

			add_action( 'enlightenment_entry_header', 'enlightenment_open_container', 8 );
		}

		add_action( 'enlightenment_entry_header', 'enlightenment_breadcrumbs' );

		if ( has_header_image() ) {
		    add_action( 'enlightenment_entry_header', 'enlightenment_close_container', 12 );

			add_action( 'enlightenment_after_entry_header', 'enlightenment_open_row', 998 );
			add_action( 'enlightenment_after_entry_header', 'enlightenment_bootstrap_open_page_content_container', 999 );

			add_action( 'enlightenment_after_entry_footer', 'enlightenment_bootstrap_close_page_content_container', 1 );
	        add_action( 'enlightenment_after_entry_footer', 'get_sidebar' );
	        add_action( 'enlightenment_after_entry_footer', 'enlightenment_close_row', 11 );
		}
    }
}
add_action( 'wp', 'enlightenment_theme_bbp_layout_hooks', 12 );

function enlightenment_theme_bbp_layout_hooks_overrides() {
	if( ! is_bbpress() ) {
		return;
	}

	if ( function_exists( 'is_buddypress' ) && is_buddypress() ) {
		return;
	}

	if( bbp_is_single_forum() ) {
		if ( ! bbp_user_can_view_forum() ) {
			remove_action( 'enlightenment_entry_header', 'enlightenment_breadcrumbs' );
            remove_action( 'enlightenment_entry_header', 'bbp_forum_subscription_link' );
		}
	} elseif ( bbp_is_single_topic() ) {
		if ( ! bbp_user_can_view_forum( array( 'forum_id' => bbp_get_topic_forum_id() ) ) ) {
			remove_action( 'enlightenment_entry_header', 'enlightenment_breadcrumbs' );
            remove_action( 'enlightenment_entry_header', 'enlightenment_bbp_topic_user_actions' );
        } elseif ( post_password_required() ) {
            remove_action( 'enlightenment_after_entry_content', 'enlightenment_bbp_topic_tag_list' );
		}
    } elseif ( bbp_is_single_reply() ) {
        if ( ! bbp_user_can_view_forum( array( 'forum_id' => bbp_get_reply_forum_id() ) ) ) {
            remove_action( 'enlightenment_entry_header', 'enlightenment_breadcrumbs' );
        }
    }
}
add_action( 'wp', 'enlightenment_theme_bbp_layout_hooks_overrides', 40 );

function enlightenment_theme_bbp_page_content_hooks( $hooks ) {
	$hooks['enlightenment_page_header']['functions'][] = 'enlightenment_bbp_form_search';

	return $hooks;
}
add_filter( 'enlightenment_page_content_hooks', 'enlightenment_theme_bbp_page_content_hooks' );

function enlightenment_theme_bbp_entry_hooks( $hooks ) {
	$hooks['enlightenment_entry_header']['functions'][] = 'enlightenment_bbp_forum_user_actions';
	$hooks['enlightenment_entry_header']['functions'][] = 'enlightenment_bbp_topic_user_actions';
	$hooks['enlightenment_entry_header']['functions'][] = 'bbp_forum_subscription_link';

	$hooks['enlightenment_after_entry_content']['functions'][] = 'enlightenment_bbp_topic_tag_list';

	$hooks['enlightenment_after_entry_footer']['functions'][] = 'get_sidebar';

	return $hooks;
}
add_filter( 'enlightenment_entry_hooks', 'enlightenment_theme_bbp_entry_hooks' );

function enlightenment_bbp_filter_bootstrap_custom_layouts( $layouts ) {
    if ( bbp_is_forum_archive() || bbp_is_single_forum() || bbp_is_forum_edit() || bbp_is_topic_tag_edit() || bbp_is_topic_archive() || bbp_is_topic_tag() || bbp_is_single_view() || bbp_is_single_topic() || bbp_is_topic_edit() || bbp_is_single_reply() || bbp_is_reply_move() || bbp_is_reply_edit() || bbp_is_search() || bbp_is_single_user() ) {
        foreach ( $layouts as $layout => $atts ) {
            $atts['content_class'] = str_replace( 'col%s-8',    'col%s-9',    $atts['content_class'] );
            $atts['sidebar_class'] = str_replace( 'col%1$s-4',  'col%1$s-3',  $atts['sidebar_class'] );
            $atts['content_class'] = str_replace( 'push%1$s-4', 'push%1$s-3', $atts['content_class'] );
            $atts['sidebar_class'] = str_replace( 'pull%1$s-8', 'pull%1$s-9', $atts['sidebar_class'] );

            $layouts[ $layout ]['content_class'] = $atts['content_class'];
            $layouts[ $layout ]['sidebar_class'] = $atts['sidebar_class'];
        }
    }

    return $layouts;
}
add_filter( 'enlightenment_custom_layouts', 'enlightenment_bbp_filter_bootstrap_custom_layouts', 12 );

function enlightenment_bbp_filter_bootstrap_form_search( $output ) {
	if ( doing_action( 'enlightenment_page_header' ) && has_header_image() ) {
		$output = str_replace( 'class="bbp-search-form"', 'class="bbp-search-form" data-bs-theme="light"', $output );
	}

	return $output;
}
add_filter( 'enlightenment_bbp_form_search', 'enlightenment_bbp_filter_bootstrap_form_search', 12 );

function enlightenment_bbp_filter_bootstrap_bbp_header( $output ) {
	$style = get_theme_mod( 'bbp_header_style' );

	if ( in_array( $style, array( 'light', 'dark' ) ) ) {
		$output = str_replace(
			'class="bbp-header"',
			sprintf( 'class="bbp-header" data-bs-theme="%s"', esc_attr( $style ) ),
			$output
		);
	}

	return $output;
}
add_filter( 'enlightenment_bbp_forums_loop',  'enlightenment_bbp_filter_bootstrap_bbp_header', 12 );
add_filter( 'enlightenment_bbp_topics_loop',  'enlightenment_bbp_filter_bootstrap_bbp_header', 12 );
add_filter( 'enlightenment_bbp_replies_loop', 'enlightenment_bbp_filter_bootstrap_bbp_header', 12 );
add_filter( 'enlightenment_bbp_single_reply', 'enlightenment_bbp_filter_bootstrap_bbp_header', 12 );
add_filter( 'enlightenment_bbp_search_loop',  'enlightenment_bbp_filter_bootstrap_bbp_header', 12 );

function enlightenment_bbp_filter_topic_tag_description( $description ) {
	if ( ! doing_action( 'enlightenment_page_header' ) ) {
		return '';
	}

	return $description;
}
add_filter( 'bbp_get_topic_tag_description', 'enlightenment_bbp_filter_topic_tag_description' );

function enlightenment_bbp_filter_author_link_args( $args ) {
    // $args['size'] = 48;
	$args['size'] = 96;

    return $args;
}
add_filter( 'bbp_before_get_author_link_parse_args', 'enlightenment_bbp_filter_author_link_args' );

function enlightenment_bbp_filter_topic_author_link_args( $args ) {
	if ( isset( $args['size'] ) && 14 == $args['size'] ) {
		$args['size'] = 32;
	} else {
		$args['size'] = 160;
	}

    return $args;
}
add_filter( 'bbp_before_get_topic_author_link_parse_args', 'enlightenment_bbp_filter_topic_author_link_args' );

function enlightenment_bbp_filter_reply_author_link_args( $args ) {
	if ( isset( $args['size'] ) && 14 == $args['size'] ) {
		$args['size'] = 32;
	} else {
		$args['size'] = 160;
	}

    return $args;
}
add_filter( 'bbp_before_get_reply_author_link_parse_args', 'enlightenment_bbp_filter_reply_author_link_args' );

function enlightenment_bbp_filter_single_user_avatar_args( $args ) {
	$args['avatar_size'] = 384;

	return $args;
}
add_filter( 'enlightenment_bbp_single_user_avatar_args', 'enlightenment_bbp_filter_single_user_avatar_args' );

function enlightenment_bbp_bootstrap_topic_user_actions_args( $args ) {
	$args['container_class'] .= ' btn-group';

	return $args;
}
add_filter( 'enlightenment_bbp_topic_user_actions_args', 'enlightenment_bbp_bootstrap_topic_user_actions_args' );

function enlightenment_theme_bbp_filter_subscription_toggle( $output, $args, $user_id, $object_id ) {
    if ( ! bbp_is_subscriptions_active() ) {
        return $output;
    }

    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return $output;
    }

    if ( bbp_is_subscriptions() ) {
        return $output;
    }

    if ( has_header_image() ) {
        $output = str_replace( 'class="subscription-toggle btn btn-secondary"', 'class="subscription-toggle btn btn-light btn-lg"', $output );
    } else {
        $output = str_replace( 'class="subscription-toggle btn btn-secondary"', 'class="subscription-toggle btn btn-outline-secondary btn-lg"', $output );
    }

    $output = str_replace( 'id="subscription-toggle"', 'id="subscription-toggle" class="btn-group"', $output );
    $output = str_replace( sprintf( 'id="subscribe-%s"', $object_id ), sprintf( 'id="subscribe-%s" class="btn-group"', $object_id ), $output );

    if ( bbp_is_single_topic() ) {
        $output = str_replace( '</a>', '</a><i class="d-none"></i>', $output );
    }

    $offset = strpos( $output, '<a ' );
    if ( false !== $offset ) {
        $offset = strpos( $output, '>', $offset );
        $output = substr_replace( $output, '<i class="fas fa-rss"></i> ', $offset + 1, 0 );
    }

    return $output;
}
add_filter( 'bbp_get_user_subscribe_link', 'enlightenment_theme_bbp_filter_subscription_toggle', 12, 4 );

function enlightenment_theme_bbp_filter_user_favorites_link( $output, $args, $user_id, $object_id ) {
    if ( ! bbp_is_favorites_active() ) {
        return $output;
    }

    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return $output;
    }

    if ( bbp_is_favorites() ) {
        return $output;
    }

    $offset = strpos( $output, '<a ' );
    if ( false !== $offset ) {
        $offset = strpos( $output, '>', $offset );
        $output = substr_replace( $output, sprintf( '<i class="fa%s fa-heart"></i> ', bbp_is_user_favorite( $user_id, $object_id ) ? 's' : 'r' ), $offset + 1, 0 );
    }

    $output = str_replace( 'id="favorite-toggle"', 'id="favorite-toggle" class="dropdown-menu dropdown-menu-end"', $output );
    $output = str_replace( 'class="favorite-toggle btn btn-secondary"', 'class="favorite-toggle dropdown-item"', $output );

    if ( ! wp_doing_ajax() ) {
        $output = sprintf(
            '<span class="btn-group dropdown"><button class="btn btn-%s btn-lg dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="%s"></button>%s</span>',
            has_header_image() ? 'light' : 'outline-secondary',
            __( 'More Actions', 'enlightenment' ),
            $output
        );
    }

	return $output;
}
add_filter( 'bbp_get_user_favorites_link', 'enlightenment_theme_bbp_filter_user_favorites_link', 12, 4 );

add_action( 'bbp_theme_before_forum_freshness_link', 'enlightenment_ob_start', 0 );
add_action( 'bbp_theme_before_topic_freshness_link', 'enlightenment_ob_start', 0 );

function enlightenment_bbp_remove_freshness_link() {
	ob_end_clean();
}
add_action( 'bbp_theme_after_forum_freshness_link', 'enlightenment_bbp_remove_freshness_link', 999 );
add_action( 'bbp_theme_after_topic_freshness_link', 'enlightenment_bbp_remove_freshness_link', 999 );

add_action( 'bbp_theme_before_topic_author', 'enlightenment_ob_start', 999 );

function enlightenment_bbp_topic_author_add_forum_freshness_link() {
	$output = ob_get_clean();

    $link = '';

    $start = strpos( $output, '<a ' );
    if ( false !== $start ) {
        $end    = strpos( $output, '>', $start ) + 1;
        $length = $end - $start;
        $link   = substr( $output, $start, $length );
        $output = substr_replace( $output, '', $start, $length );
        $offset = strpos( $output, '</a>', $start );
        $output = substr_replace( $output, '', $offset, 4 );

        $offset = strpos( $output, '<img ' );
        if ( false !== $offset ) {
            $output = substr_replace( $output, $link, $offset, 0 );
            $offset = strpos( $output, '/>', $offset );
            $output = substr_replace( $output, '</a>', $offset + 2, 0 );
        }
    }

    $offset = strpos( $output, '<span  class="bbp-author-name">' );
    if ( false !== $offset ) {
        $output = substr_replace( $output, $link, $offset + 31, 0 );
        $offset = strpos( $output, '</span>', $offset );
        $output = substr_replace( $output, '</a>', $offset , 0 );
    }

    $offset = strpos( $output, '<span  class="bbp-author-name">' );
    if ( false !== $offset ) {
        $output = substr_replace( $output, '<span class="bbp-author-name-freshness-link-wrap">', $offset, 0 );
        $offset = strpos( $output, '</span>', $offset );
        $output = substr_replace( $output, '</span>', $offset + 7, 0 );

        $has_action = ( 0 === has_action( 'bbp_theme_before_forum_freshness_link', 'enlightenment_ob_start' ) );

        if ( $has_action ) {
            remove_action( 'bbp_theme_before_forum_freshness_link', 'enlightenment_ob_start', 0 );
            remove_action( 'bbp_theme_after_forum_freshness_link', 'enlightenment_bbp_remove_freshness_link', 999 );
        }

        ob_start();
        do_action( 'bbp_theme_before_forum_freshness_link' );
        bbp_forum_freshness_link();
        do_action( 'bbp_theme_after_forum_freshness_link' );
        $link = ob_get_clean();
        $link = sprintf( '<span class="topic-freshness-link">%s</span>', $link );

        if ( $has_action ) {
            add_action( 'bbp_theme_before_forum_freshness_link', 'enlightenment_ob_start', 0 );
            add_action( 'bbp_theme_after_forum_freshness_link', 'enlightenment_bbp_remove_freshness_link', 999 );
        }

        $output = substr_replace( $output, $link, $offset + 7, 0 );
    }

    $output = str_replace( 'class="bbp-topic-freshness-author"', 'class="bbp-topic-freshness-author d-flex align-items-center"', $output );
    $output = str_replace( 'class="bbp-author-avatar"', 'class="bbp-author-avatar flex-grow-0 flex-shrink-1"', $output );
    $output = str_replace( 'class="bbp-author-name-freshness-link-wrap"', 'class="bbp-author-name-freshness-link-wrap flex-grow-1 flex-shrink-0"', $output );

    echo $output;
}
add_action( 'bbp_theme_after_topic_author', 'enlightenment_bbp_topic_author_add_forum_freshness_link', 0 );

add_action( 'bbp_theme_before_topic_freshness_author', 'enlightenment_ob_start', 999 );

function enlightenment_bbp_topic_author_add_topic_freshness_link() {
	$output = ob_get_clean();

    $link = '';

    $start = strpos( $output, '<a ' );
    if ( false !== $start ) {
        $end    = strpos( $output, '>', $start ) + 1;
        $length = $end - $start;
        $link   = substr( $output, $start, $length );
        $output = substr_replace( $output, '', $start, $length );
        $offset = strpos( $output, '</a>', $start );
        $output = substr_replace( $output, '', $offset, 4 );

        $offset = strpos( $output, '<img ' );
        if ( false !== $offset ) {
            $output = substr_replace( $output, $link, $offset, 0 );
            $offset = strpos( $output, '/>', $offset );
            $output = substr_replace( $output, '</a>', $offset + 2, 0 );
        }
    }

    $offset = strpos( $output, '<span  class="bbp-author-name">' );
    if ( false !== $offset ) {
        $output = substr_replace( $output, $link, $offset + 31, 0 );
        $offset = strpos( $output, '</span>', $offset );
        $output = substr_replace( $output, '</a>', $offset , 0 );
    }

    $offset = strpos( $output, '<span  class="bbp-author-name">' );
    if ( false !== $offset ) {
        $output = substr_replace( $output, '<span class="bbp-author-name-freshness-link-wrap">', $offset, 0 );
        $offset = strpos( $output, '</span>', $offset );
        $output = substr_replace( $output, '</span>', $offset + 7, 0 );

        $has_action = ( 0 === has_action( 'bbp_theme_before_topic_freshness_link', 'enlightenment_ob_start' ) );

        if ( $has_action ) {
            remove_action( 'bbp_theme_before_topic_freshness_link', 'enlightenment_ob_start', 0 );
            remove_action( 'bbp_theme_after_topic_freshness_link', 'enlightenment_bbp_remove_freshness_link', 999 );
        }

        ob_start();
        do_action( 'bbp_theme_before_topic_freshness_link' );
        bbp_topic_freshness_link();
        do_action( 'bbp_theme_after_topic_freshness_link' );
        $link = ob_get_clean();
        $link = sprintf( '<span class="topic-freshness-link">%s</span>', $link );

        if ( $has_action ) {
            add_action( 'bbp_theme_before_topic_freshness_link', 'enlightenment_ob_start', 0 );
            add_action( 'bbp_theme_after_topic_freshness_link', 'enlightenment_bbp_remove_freshness_link', 999 );
        }

        $output = substr_replace( $output, $link, $offset + 7, 0 );
    }

    $output = str_replace( 'class="bbp-topic-freshness-author"', 'class="bbp-topic-freshness-author d-flex align-items-center"', $output );
    $output = str_replace( 'class="bbp-author-avatar"', 'class="bbp-author-avatar flex-grow-0 flex-shrink-1"', $output );
    $output = str_replace( 'class="bbp-author-name-freshness-link-wrap"', 'class="bbp-author-name-freshness-link-wrap flex-grow-1 flex-shrink-0"', $output );

    echo $output;
}
add_action( 'bbp_theme_after_topic_freshness_author', 'enlightenment_bbp_topic_author_add_topic_freshness_link', 0 );

add_action( 'bbp_theme_before_anonymous_form', 'enlightenment_ob_start' );

function enlightenment_bbp_bootstrap_anonymous_form() {
	$output = ob_get_clean();
	$output = str_replace( '<legend>', '<legend class="screen-reader-text visually-hidden">', $output );

	echo $output;
}
add_action( 'bbp_theme_after_anonymous_form', 'enlightenment_bbp_bootstrap_anonymous_form' );

function enlightenment_bbp_filter_cancel_reply_to_link( $output ) {
	return str_replace( 'class="btn btn-secondary btn-lg"', 'class="btn btn-outline-secondary btn-lg order-1 ms-2"', $output );
}
add_filter( 'bbp_get_cancel_reply_to_link', 'enlightenment_bbp_filter_cancel_reply_to_link', 12 );

function enlightenment_bbp_filter_alert_topic_lock( $output ) {
	return str_replace( 'class="bbp-alert-close btn btn-secondary"', 'class="bbp-alert-close btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_bbp_alert_topic_lock', 'enlightenment_bbp_filter_alert_topic_lock', 12 );

function enlightenment_bbp_filter_single_user_details( $output ) {
	$output = str_replace( '<div id="bbp-user-navigation">', '<nav id="bbp-user-navigation" class="secondary-navigation">' . "\n" . '<div class="bbp-nav-container">', $output );
	$output = str_replace( '</div>', '</div>' . "\n" . '</nav>', $output );

	return $output;
}
add_filter( 'enlightenment_bbp_single_user_details', 'enlightenment_bbp_filter_single_user_details' );

function enlightenment_theme_bbp_filter_single_user_body( $output ) {
    return str_replace( 'class="btn btn-secondary ', 'class="btn btn-outline-secondary ', $output );
}
add_filter( 'enlightenment_bbp_single_user_body', 'enlightenment_theme_bbp_filter_single_user_body', 12 );

function enlightenment_bbp_filter_bootstrap_bbp_search_btn( $output ) {
	return str_replace( 'class="button btn btn-light"', 'class="button btn btn-theme-inverse"', $output );
}
add_filter( 'enlightenment_widget_bbp_search_widget', 'enlightenment_bbp_filter_bootstrap_bbp_search_btn', 12 );
add_filter( 'enlightenment_bbp_display_shortcode_bbp_search', 'enlightenment_bbp_filter_bootstrap_bbp_search_btn', 12 );
add_filter( 'enlightenment_bbp_display_shortcode_bbp_search_form', 'enlightenment_bbp_filter_bootstrap_bbp_search_btn', 12 );
add_filter( 'enlightenment_bbp_display_shortcode_bbp_forum_archive', 'enlightenment_bbp_filter_bootstrap_bbp_search_btn', 12 );
add_filter( 'enlightenment_bbp_display_shortcode_bbp_topic_archive', 'enlightenment_bbp_filter_bootstrap_bbp_search_btn', 12 );
add_filter( 'enlightenment_bbp_display_shortcode_bbp_topic_tag', 'enlightenment_bbp_filter_bootstrap_bbp_search_btn', 12 );
add_filter( 'enlightenment_bbp_search_loop',  'enlightenment_bbp_filter_bootstrap_bbp_search_btn', 12 );
add_filter( 'enlightenment_bbp_single_user_body', 'enlightenment_bbp_filter_bootstrap_bbp_search_btn', 12 );
add_filter( 'enlightenment_bp_filter_template_member_plugins_output', 'enlightenment_bbp_filter_bootstrap_bbp_search_btn', 12 );

function enlightenment_bbp_filter_login_widget_title( $title ) {
	if ( empty( $title ) ) {
        if ( is_user_logged_in() ) {
            $title = __( 'My Account', 'enlightenment' );
        } else {
            $title = __( 'Log In', 'enlightenment' );;
        }
    }

	return $title;
}
add_filter( 'bbp_login_widget_title', 'enlightenment_bbp_filter_login_widget_title' );

function enlightenment_theme_bbp_filter_login_widget( $output ) {
    if ( is_user_logged_in() ) {
        $output = str_replace(
            get_avatar( bbp_get_current_user_id(), '40' ),
            get_avatar( bbp_get_current_user_id(), '128' ),
            $output
        );
        $output = str_replace( 'class="button logout-link btn btn-secondary btn-sm"', 'class="button logout-link btn btn-outline-secondary btn-sm"', $output );
    } else {
    	$output = str_replace( 'class="button submit user-submit btn btn-secondary btn-lg"', 'class="button submit user-submit btn btn-outline-secondary btn-lg"', $output );
    }

    return $output;
}
add_filter( 'enlightenment_widget_bbp_login_widget', 'enlightenment_theme_bbp_filter_login_widget', 12 );

function enlightenment_bbp_filter_topics_widget( $output ) {
	$start = strpos( $output, '<ul class="bbp-topics-widget ' );
	if ( false !== $start ) {
		$offset = strpos( $output, '<li>', $start );
		$end    = strpos( $output, '</ul>', $start );

		while ( false !== $offset && $offset < $end ) {
			$reply_link = '';
			$start_a    = strpos( $output, '<a class="bbp-forum-title"', $offset );
			$end_a      = strpos( $output, '</li>', $offset );
			if ( false !== $start_a && $start_a < $end_a ) {
				$end_a      = strpos( $output, '</a>', $start_a ) + 4;
				$length     = $end_a - $start_a;
				$reply_link = substr( $output, $start_a, $length );
			}

			$author_link = '';
			$start_a     = strpos( $output, '<span class="topic-author">', $offset );
			$end_a       = strpos( $output, '</li>', $offset );
			if ( false !== $start_a && $start_a < $end_a ) {
				$offset_a = $start_a;
				$start_b  = strpos( $output, 'class="bbp-author-link"', $offset );
				if ( false !== $start_b && $start_b < $end_a ) {
					$offset_a = strpos( $output, '</a>', $start_b );
				}


				$end_a       = strpos( $output, '</span>', $offset_a ) + 7;
				$length      = $end_a - $start_a;
				$author_link = substr( $output, $start_a, $length );
			}

			$show_date = '';
			$start_a   = strpos( $output, '<div>', $offset );
			$end_a     = strpos( $output, '</li>', $offset );
			if ( false !== $start_a && $start_a < $end_a ) {
				$end_a     = strpos( $output, '</div>', $start_a ) + 6;
				$length    = $end_a - $start_a;
				$show_date = substr( $output, $start_a, $length );
			}

			$meta_str = '';
			if ( ! empty( $author_link ) || ! empty( $show_date ) ) {
				$meta_str = sprintf( '<span class="bbp-topic-meta">%s %s</span>', $author_link, $show_date );
			}

			$item_str = sprintf( '%s %s', $reply_link, $meta_str );
			$start_a  = $offset + 4;
			$end_a    = strpos( $output, '</li>', $offset );
			$length   = $end_a - $start_a;
			$output   = substr_replace( $output, $item_str, $start_a, $length );

			$offset = strpos( $output, '<li>', $offset + 1 );
			$end    = strpos( $output, '</ul>', $start );
		}
	}

	return $output;
}
add_filter( 'enlightenment_widget_bbp_topics_widget', 'enlightenment_bbp_filter_topics_widget' );

function enlightenment_bbp_filter_replies_widget( $output ) {
	$start = strpos( $output, '<ul class="bbp-replies-widget">' );
	if ( false !== $start ) {
		$offset = strpos( $output, '<li>', $start );
		$end    = strpos( $output, '</ul>', $start );

		while ( false !== $offset && $offset < $end ) {
			$reply_link = '';
			$start_a    = strpos( $output, '<a class="bbp-reply-topic-title"', $offset );
			$end_a      = strpos( $output, '</li>', $offset );
			if ( false !== $start_a && $start_a < $end_a ) {
				$end_a      = strpos( $output, '</a>', $start_a ) + 4;
				$length     = $end_a - $start_a;
				$reply_link = substr( $output, $start_a, $length );
			}

			$author_link = '';
			$start_a     = strpos( $output, 'class="bbp-author-link"', $offset );
			$end_a       = strpos( $output, '</li>', $offset );
			if ( false !== $start_a && $start_a < $end_a ) {
				$start_a     = strrpos( $output, '<a ', $start_a - strlen( $output ) );
				$end_a       = strpos( $output, '</a>', $start_a ) + 4;
				$length      = $end_a - $start_a;
				$author_link = substr( $output, $start_a, $length );
			}

			$show_date = '';
			$start_a   = strpos( $output, '<time ', $offset );
			$end_a     = strpos( $output, '</li>', $offset );
			if ( false !== $start_a && $start_a < $end_a ) {
				$end_a     = strpos( $output, '</time>', $start_a ) + 7;
				$length    = $end_a - $start_a;
				$show_date = substr( $output, $start_a, $length );
			}

			$meta_str = '';
			if ( ! empty( $author_link ) || ! empty( $show_date ) ) {
				$meta_str = sprintf( '<span class="bbp-reply-topic-meta">%s %s</span>', $author_link, $show_date );
			}

			$item_str = sprintf( '%s %s', $reply_link, $meta_str );
			$start_a  = $offset + 4;
			$end_a    = strpos( $output, '</li>', $offset );
			$length   = $end_a - $start_a;
			$output   = substr_replace( $output, $item_str, $start_a, $length );

			$offset = strpos( $output, '<li>', $offset + 1 );
			$end    = strpos( $output, '</ul>', $start );
		}
	}

	return $output;
}
add_filter( 'enlightenment_widget_bbp_replies_widget', 'enlightenment_bbp_filter_replies_widget' );

function enlightenment_theme_bbp_filter_stats_widget( $output ) {
    $output = str_replace( '<dl role="main">', '<ul>', $output );
    $output = str_replace( '</dl>', '</ul>', $output );
    $output = str_replace( '<dt>', '<li>' . "\n" . '<dt>', $output );
    $output = str_replace( '</dd>', '</dd>' . "\n" . '</li>', $output );

    return $output;
}
add_filter( 'enlightenment_widget_bbp_stats_widget', 'enlightenment_theme_bbp_filter_stats_widget', 12 );

function enlightenment_bbp_filter_display_shortcode_bbp_stats( $output ) {
    return str_replace( '<dl role="main">', '<dl class="bbp-stats" role="main">', $output );
}
add_filter( 'enlightenment_bbp_display_shortcode_bbp_stats', 'enlightenment_bbp_filter_display_shortcode_bbp_stats' );

function enlightenment_bbp_filter_display_shortcode_bbp_topic_tags( $output ) {
    return sprintf( '<div class="bbp-topic-tags"><div class="bbp-tag-cloud">%s</div></div>', $output );
}
add_filter( 'enlightenment_bbp_display_shortcode_bbp_topic_tags', 'enlightenment_bbp_filter_display_shortcode_bbp_topic_tags' );

function enlightenment_bbp_bootstrap_display_shortcode_bbp_user_actions( $output ) {
    $offset = strpos( $output, '<span id="subscription-toggle" class="btn-group">' );
    if ( false !== $offset ) {
        $output = substr_replace( $output, '<nav class="user-actions btn-group">', $offset, 0 );

        $offset_a = strpos( $output, 'id="favorite-toggle"', $offset );
        if ( false !== $offset_a ) {
            $offset_a = strpos( $output, '</span>', $offset_a );
            $offset_a = strpos( $output, '</span>', $offset_a + 1 );
            $offset_a = strpos( $output, '</span>', $offset_a + 1 );
            $output   = substr_replace( $output, '</nav>', $offset_a + 7, 0 );
        } else {
            $offset = strpos( $output, '</span>', $offset );
            $offset = strpos( $output, '</span>', $offset + 1 );
            $output = substr_replace( $output, '</nav>', $offset + 7, 0 );
        }
    }

	return $output;
}
add_filter( 'enlightenment_bbp_display_shortcode_bbp_single_forum', 'enlightenment_bbp_bootstrap_display_shortcode_bbp_user_actions' );
add_filter( 'enlightenment_bbp_display_shortcode_bbp_single_topic', 'enlightenment_bbp_bootstrap_display_shortcode_bbp_user_actions' );

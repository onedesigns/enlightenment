<?php

function enlightenment_bp_set_avatar_constants() {
    if ( ! defined( 'BP_AVATAR_THUMB_WIDTH' ) ) {
		define( 'BP_AVATAR_THUMB_WIDTH', 96 );
    }

	if ( ! defined( 'BP_AVATAR_THUMB_HEIGHT' ) ) {
		define( 'BP_AVATAR_THUMB_HEIGHT', 96 );
    }

    if ( ! defined( 'BP_AVATAR_FULL_WIDTH' ) ) {
		define( 'BP_AVATAR_FULL_WIDTH', 384 );
    }

	if ( ! defined( 'BP_AVATAR_FULL_HEIGHT' ) ) {
		define( 'BP_AVATAR_FULL_HEIGHT', 384 );
    }
}
add_action( 'bp_init', 'enlightenment_bp_set_avatar_constants', 1 );

function enlightenment_bp_filter_cover_image_settings( $args ) {
    $args['width']  = 2880;
    $args['height'] = 680;

    return $args;
}
add_filter( 'bp_after_members_cover_image_settings_parse_args', 'enlightenment_bp_filter_cover_image_settings' );
add_filter( 'bp_after_groups_cover_image_settings_parse_args', 'enlightenment_bp_filter_cover_image_settings' );

function enlightenment_bp_filter_appearance_settings( $args ) {
    $args['global_alignment'] = 'alignnone';

	$front = enlightenment_has_in_call_stack( 'bp_nouveau_register_sidebars' );

    $args['user_front_page']  = $front;
    $args['group_front_page'] = $front;

    $args['group_front_description'] = true;

    return $args;
}
add_filter( 'bp_after_nouveau_appearance_settings_parse_args', 'enlightenment_bp_filter_appearance_settings' );

/**
 * Dequeue styles.
 */
function enlightenment_theme_bp_bootstrap_dequeue_styles() {
	wp_dequeue_style( 'bp-recent-posts-block' );
	wp_dequeue_style( 'bp-dynamic-groups-block' );
	wp_dequeue_style( 'bp-friends-block' );
	wp_dequeue_style( 'bp-dynamic-members-block' );
}
add_action( 'wp_enqueue_scripts', 'enlightenment_theme_bp_bootstrap_dequeue_styles' );
add_filter( 'wp_print_footer_scripts', 'enlightenment_theme_bp_bootstrap_dequeue_styles', 8 );

function enlightenment_bp_filter_customizer_controls( $controls ) {
    unset( $controls['bp_site_avatars'] );
    unset( $controls['user_front_page'] );

    unset( $controls['group_front_page'] );
    unset( $controls['group_front_description'] );
    unset( $controls['group_nav_display'] );
    unset( $controls['group_nav_tabs'] );
    unset( $controls['group_subnav_tabs'] );
    unset( $controls['groups_create_tabs'] );

    unset( $controls['user_nav_display'] );
    unset( $controls['user_nav_tabs'] );
    unset( $controls['user_subnav_tabs'] );

    unset( $controls['members_layout'] );
    unset( $controls['members_friends_layout'] );
    unset( $controls['groups_layout'] );
    unset( $controls['members_group_layout'] );

    unset( $controls['members_dir_layout'] );
    unset( $controls['members_dir_tabs'] );
    unset( $controls['act_dir_layout'] );
    unset( $controls['act_dir_tabs'] );
    unset( $controls['group_dir_layout'] );
    unset( $controls['group_dir_tabs'] );
    unset( $controls['sites_dir_layout'] );
    unset( $controls['sites_dir_tabs'] );

    return $controls;
}
add_filter( 'bp_nouveau_customizer_controls', 'enlightenment_bp_filter_customizer_controls', 20 );

function enlightenment_bp_remove_global_alignment_control( WP_Customize_Manager $wp_customize ) {
    $wp_customize->remove_control( 'global_alignment' );
}
add_action( 'bp_customize_register', 'enlightenment_bp_remove_global_alignment_control', 20 );

function enlightenment_bp_enqueue_scripts() {
    wp_enqueue_style( 'enlightenment-buddypress', get_theme_file_uri( 'assets/css/buddypress.css' ), array( 'enlightenment-theme-stylesheet', 'select2' ), null );
    wp_enqueue_style( 'enlightenment-buddypress-blocks', get_theme_file_uri( 'assets/css/bp-blocks.css' ), array( 'enlightenment-core-blocks' ), null );

    wp_enqueue_script( 'enlightenment-buddypress', get_theme_file_uri( 'assets/js/buddypress.js' ), array( 'jquery', 'bootstrap', 'select2', 'gemini-scrollbar', 'bp-nouveau' ), null, true );

    wp_localize_script( 'enlightenment-buddypress', 'enlightenment_buddypress_args', apply_filters( 'enlightenment_buddypress_script_args', array(
        'group_description_more' => _x( 'More', 'expand group description', 'enlightenment' ),
        'group_description_less' => _x( 'Less', 'collapse group description', 'enlightenment' ),
	) ) );

    wp_enqueue_script( 'enlightenment-rtmedia', get_theme_file_uri( 'assets/js/rtmedia.js' ), array( 'jquery', 'rtmedia-main' ), null, true );

    wp_localize_script( 'enlightenment-rtmedia', 'enlightenment_rtmedia_args', apply_filters( 'enlightenment_rtmedia_script_args', array(
        'rtm_comments_link_label' => array(
            'zero' => __( 'Leave a Comment', 'enlightement' ),
            'one'  => __( '1 Comment', 'enlightement' ),
            'more' => __( '%s Comments', 'enlightement' ),
        ),
		'close_label' => __( 'Close', 'enlightement' ),
	) ) );
}
add_action( 'bp_enqueue_scripts', 'enlightenment_bp_enqueue_scripts' );

function enlightenment_bp_filter_theme_stylesheet_deps( $deps ) {
   if ( is_buddypress() ) {
       $deps[] = 'select2';
   }

   return $deps;
}
add_filter( 'enlightenment_theme_stylesheet_deps', 'enlightenment_bp_filter_theme_stylesheet_deps' );

function enlightenment_theme_bp_cover_image_css( $css ) {
	$cover_image = enlightenment_bp_get_cover_image();

	if ( empty( $cover_image ) ) {
		return $css;
	}

	$cover_image = esc_url_raw( $cover_image );

	$css .= "\n.profile.change-avatar .bp-avatar .avatar-crop-management::before,\n.profile.change-avatar .standard-form > .row > .col:last-child::before,\n.group-admin.group-avatar .bp-avatar .avatar-crop-management::before,\n.group-admin.group-avatar .standard-form > .row > .col:last-child::before {\n\tbackground-image: url({$cover_image});\n}\n";

	return $css;
}
add_filter( 'enlightenment_theme_custom_css', 'enlightenment_theme_bp_cover_image_css' );

function enlightenment_theme_bp_filter_cover_image_settings( $settings ) {
	if ( enlightenment_has_in_call_stack( 'enlightenment_theme_bp_filter_header_image' ) ) {
		return $settings;
	}

	if ( empty( $settings['default_cover'] ) && has_header_image() ) {
		$settings['default_cover'] = get_header_image();
	}

	return $settings;
}

function enlightenment_theme_bp_hook_filter_cover_image_settings() {
	foreach ( array_diff( array_keys( bp_core_get_components() ), array( 'xprofile', 'profile' ) ) as $component ) {
		if ( ! bp_is_active( $component, 'cover_image' ) ) {
			continue;
		}

		add_filter( sprintf( 'bp_before_%s_cover_image_settings_parse_args', $component ), 'enlightenment_theme_bp_filter_cover_image_settings' );
	}
}
add_action( 'bp_after_setup_theme', 'enlightenment_theme_bp_hook_filter_cover_image_settings' );

function enlightenment_theme_bp_filter_header_image( $url ) {
	if ( $url && 'remove-header' != $url ) {
		return $url;
	}

	$cover_image = enlightenment_bp_get_cover_image();

	if ( ! empty( $cover_image ) ) {
		return $cover_image;
	}

	return $url;
}
add_filter( 'theme_mod_header_image', 'enlightenment_theme_bp_filter_header_image' );

function enlightenment_bp_filter_main_script_deps( $deps ) {
   if ( is_buddypress() ) {
       $deps[] = 'select2';
   }

   return $deps;
}
add_filter( 'enlightenment_main_script_deps', 'enlightenment_bp_filter_main_script_deps' );

remove_action( 'enlightenment_site_header', 'enlightenment_bootstrap_color_mode_switcher' );
remove_action( 'enlightenment_site_header', 'enlightenment_search_form' );
add_action( 'enlightenment_site_header', 'enlightenment_bp_notifications' );
add_action( 'enlightenment_site_header', 'enlightenment_bp_header_account_login' );
add_action( 'enlightenment_site_header', 'enlightenment_bootstrap_color_mode_switcher' );
add_action( 'enlightenment_site_header', 'enlightenment_search_form' );

function enlightenment_theme_bp_template_hooks() {
    if ( ! is_buddypress() ) {
		return;
	}

    if ( bp_is_activity_directory() ) {
        remove_action( 'enlightenment_before_page_content', 'enlightenment_bp_activity_post_form' );
        remove_action( 'enlightenment_before_page_content', 'enlightenment_bp_search_and_filters_bar' );
		remove_action( 'enlightenment_page_content', 'enlightenment_bp_activity_loop' );

        add_action( 'enlightenment_before_page_content', 'enlightenment_open_row', 4 );

        add_action( 'enlightenment_before_page_content', 'enlightenment_bp_activity_nav_wrap', 6 );
        add_action( 'enlightenment_before_page_content', 'enlightenment_close_div', 14 );

        add_action( 'enlightenment_page_content', 'enlightenment_bp_activity_wrap', 6 );

        add_action( 'enlightenment_page_content', 'enlightenment_bp_activity_post_form' );
		add_action( 'enlightenment_page_content', 'enlightenment_bp_search_and_filters_bar' );
		add_action( 'enlightenment_page_content', 'enlightenment_bp_activity_loop' );

        add_action( 'enlightenment_after_page_content', 'enlightenment_close_div', 16 );

        add_action( 'enlightenment_after_page_content', 'enlightenment_close_row', 18 );

    } elseif ( bp_is_user() ) {
        remove_action( 'enlightenment_bp_user_header_content', 'enlightenment_bp_member_meta' );
    } elseif ( bp_is_group() ) {
        remove_action( 'enlightenment_page_header', 'enlightenment_bp_group_header_actions' );
        remove_action( 'enlightenment_before_page_content', 'enlightenment_bp_group_description' );
    }
}
add_action( 'wp', 'enlightenment_theme_bp_template_hooks', 12 );

function enlightenment_theme_bp_page_content_hooks( $hooks ) {
	$hooks['enlightenment_page_content']['functions'][] = 'enlightenment_bp_activity_post_form';
	$hooks['enlightenment_page_content']['functions'][] = 'enlightenment_bp_search_and_filters_bar';

	return $hooks;
}
add_filter( 'enlightenment_page_content_hooks', 'enlightenment_theme_bp_page_content_hooks' );

function enlightenment_bp_filter_bootstrap_custom_layouts( $layouts ) {
    if ( is_buddypress() ) {
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
add_filter( 'enlightenment_custom_layouts', 'enlightenment_bp_filter_bootstrap_custom_layouts', 12 );

function enlightenment_bp_filter_notifications_args( $args ) {
	$args['toggle_extra_atts']['data-bs-display'] = 'static';

	$args['alert_no_count_class'] .= ' d-none';

	if ( enlightenment_is_navbar_inversed() ) {
		$args['dropdown_menu_extra_atts']['data-bs-theme'] = 'light';
	}

	return $args;
}
add_filter( 'enlightenment_bp_notifications_args', 'enlightenment_bp_filter_notifications_args' );

function enlightenment_bp_filter_header_account_login_args( $args ) {
	$args['toggle_extra_atts']['data-bs-display'] = 'static';

	if ( enlightenment_is_navbar_inversed() ) {
		$args['dropdown_menu_extra_atts']['data-bs-theme'] = 'light';
	}

	return $args;
}
add_filter( 'enlightenment_bp_header_account_login_args', 'enlightenment_bp_filter_header_account_login_args' );

function enlightenment_bp_filter_header_account_login( $output ) {
    $offset = strpos( $output, '<span class="user-avatar">' );
	if ( false !== $offset ) {
		$icon   = '<i class="far fa-user-circle" aria-hidden="true" role="presentation"></i>';
		$label  = sprintf(
			'<span class="screen-reader-text visually-hidden">%s</span>',
			__( 'My Account', 'enlightenment' )
		);

		$start  = $offset;
		$end    = strpos( $output, '</span>', $start ) + 7;
		$length = $end - $start;
		$output = substr_replace( $output, sprintf( '%s %s', $icon, $label ), $start, $length );
	}

    $offset = strpos( $output, '<span class="user-greeting">' );
	if ( false !== $offset ) {
		$start  = $offset;
		$end    = strpos( $output, '</span>', $start ) + 7;
		$length = $end - $start;
		$output = substr_replace( $output, '', $start, $length );
	}

    $output = str_replace( '<legend>', '<legend class="screen-reader-text visually-hidden">', $output );

	return $output;
}
add_filter( 'enlightenment_bp_header_account_login', 'enlightenment_bp_filter_header_account_login' );

function enlightenment_bp_filter_custom_header_markup( $output ) {
    if ( bp_is_user() || ( bp_is_group() && ! bp_is_group_create() ) ) {
        $output = '';
    }

    return $output;
}
add_filter( 'enlightenment_custom_header_markup', 'enlightenment_bp_filter_custom_header_markup' );

function enlightenment_bp_activity_nav_wrap() {
    echo enlightenment_open_tag( 'div', 'col-lg-4' );
}

function enlightenment_bp_activity_wrap() {
    echo enlightenment_open_tag( 'div', 'col-lg-8' );
}

function enlightenment_bp_filter_directory_nav( $output ) {
	// $output = str_replace( 'class="component-navigation nav ', 'class="component-navigation nav flex-lg-column ', $output );
    $output = str_replace( 'class="members-type-navs ', 'class="secondary-navigation members-type-navs ', $output );
    $output = str_replace( 'class="groups-type-navs ', 'class="secondary-navigation groups-type-navs ', $output );
    $output = str_replace( 'class="sites-type-navs ', 'class="secondary-navigation sites-type-navs ', $output );

	return $output;
}
add_filter( 'enlightenment_bp_directory_nav', 'enlightenment_bp_filter_directory_nav', 12 );

function enlightenment_bp_filter_group_creation_tabs( $output ) {
	return str_replace( 'class="bp-navs group-create-links ', 'class="secondary-navigation bp-navs group-create-links ', $output );
}
add_filter( 'enlightenment_bp_group_creation_tabs', 'enlightenment_bp_filter_group_creation_tabs', 12 );

function enlightenment_bp_filter_search_and_filters_bar( $output ) {
    $offset = strpos( $output, 'class="feed"' );
    if ( false !== $offset ) {
        $offset = strpos( $output, '<span ', $offset );
        $output = substr_replace( $output, '<i class="fas fa-rss"></i> ', $offset, 0 );
    }

	return $output;
}
add_filter( 'enlightenment_bp_search_and_filters_bar', 'enlightenment_bp_filter_search_and_filters_bar' );
add_filter( 'enlightenment_bp_filter_template_member_activity_output', 'enlightenment_bp_filter_search_and_filters_bar' );
add_filter( 'enlightenment_bp_filter_template_group_activity_output', 'enlightenment_bp_filter_search_and_filters_bar' );

function enlightenment_bp_filter_input_group_btn( $output ) {
	return str_replace( 'class="nouveau-search-submit btn btn-light"', 'class="nouveau-search-submit btn btn-theme-inverse"', $output );
}
add_filter( 'enlightenment_bp_search_and_filters_bar', 'enlightenment_bp_filter_input_group_btn', 12 );
add_filter( 'enlightenment_bp_filter_group_creation_group-invites_screen', 'enlightenment_bp_filter_input_group_btn', 12 );
add_filter( 'enlightenment_bp_filter_group_admin_group-invites_screen', 'enlightenment_bp_filter_input_group_btn', 12 );
add_filter( 'enlightenment_bp_filter_template_group_send-invites_output', 'enlightenment_bp_filter_input_group_btn', 12 );
add_filter( 'enlightenment_bp_filter_template_group_activity_output', 'enlightenment_bp_filter_input_group_btn', 12 );
add_filter( 'enlightenment_bp_filter_template_group_members_output', 'enlightenment_bp_filter_input_group_btn', 12 );

function enlightenment_bp_filter_member_header_actions( $output ) {
	$output = str_replace( 'class="btn btn-primary ', 'class="btn btn-outline-secondary ', $output );
    $output = sprintf( '<div id="item-header" data-bp-item-id="%s" data-bp-item-component="members">%s</div>', bp_displayed_user_id(), $output );

    return $output;
}
// add_filter( 'enlightenment_bp_member_header_actions', 'enlightenment_bp_filter_member_header_actions', 12 );

function enlightenment_bp_filter_members_buttons( $buttons ) {
	$keys  = array_keys( $buttons );
    $class = $buttons[ $keys[0] ]['button_attr']['class'];

	$buttons[ $keys[0] ]['button_attr']['class'] = str_replace( ' btn-primary', ' btn-outline-secondary', $class );

	return $buttons;
}
// add_filter( 'bp_nouveau_get_members_buttons', 'enlightenment_bp_filter_members_buttons', 12 );

// Filter separately for AJAX
function enlightenment_bp_filter_member_header_buttons( $args ) {
    if ( ! wp_doing_ajax() ) {
		return $args;
	}

	$args['link_class'] = str_replace( ' btn-primary', ' btn-outline-secondary', $args['link_class'] );

	return $args;
}
// add_filter( 'bp_get_add_friend_button', 'enlightenment_bp_filter_member_header_buttons', 12 );
// add_filter( 'bp_get_send_public_message_button', 'enlightenment_bp_filter_member_header_buttons' );

function enlightenment_bp_filter_main_nav( $output ) {
	$output = str_replace( '<nav class="', '<nav class="secondary-navigation ', $output );

    $offset = strpos( $output, '<nav class="' );
    if ( false !== $offset ) {
		$offset = strpos( $output, '>', $offset );
        $output = substr_replace( $output, "\n" . '<div class="bp-navs-container">', $offset + 1, 0 );
        $offset = strpos( $output, '</nav>', $offset );
        $output = substr_replace( $output, '</div>' . "\n", $offset, 0 );
    }

    return $output;
}
add_filter( 'enlightenment_bp_displayed_user_nav', 'enlightenment_bp_filter_main_nav' );
add_filter( 'enlightenment_bp_group_nav', 'enlightenment_bp_filter_main_nav' );

function enlightenment_bp_filter_ajax_loader( $output ) {
    $offset = strpos( $output, '<div id="bp-ajax-loader">' );
    if ( false !== $offset ) {
		$offset = strpos( $output, 'class="ms-2 mb-0"', $offset );
        $output = substr_replace( $output, 'screen-reader-text visually-hidden', $offset + 7, 9 );
    }

	return $output;
}
add_filter( 'enlightenment_bp_activity_loop', 'enlightenment_bp_filter_ajax_loader', 12 );
add_filter( 'enlightenment_bp_members_loop', 'enlightenment_bp_filter_ajax_loader', 12 );
add_filter( 'enlightenment_bp_groups_loop', 'enlightenment_bp_filter_ajax_loader', 12 );
add_filter( 'enlightenment_bp_filter_template_member_activity_output', 'enlightenment_bp_filter_ajax_loader', 12 );
add_filter( 'enlightenment_bp_filter_template_member_notifications_output', 'enlightenment_bp_filter_ajax_loader', 12 );
add_filter( 'enlightenment_bp_filter_template_member_friends_output', 'enlightenment_bp_filter_ajax_loader', 12 );
add_filter( 'enlightenment_bp_filter_template_member_groups_output', 'enlightenment_bp_filter_ajax_loader', 12 );
add_filter( 'enlightenment_bp_filter_template_member_blogs_output', 'enlightenment_bp_filter_ajax_loader', 12 );
add_filter( 'enlightenment_bp_filter_template_group_members_output', 'enlightenment_bp_filter_ajax_loader', 12 );
add_filter( 'enlightenment_bp_filter_group_admin_membership-requests_screen', 'enlightenment_bp_filter_ajax_loader', 12 );
add_filter( 'enlightenment_bp_blogs_loop', 'enlightenment_bp_filter_ajax_loader', 12 );

function enlightenment_bp_filter_template_member_activity_output( $output ) {
    $output = str_replace( 'class="bp-screen-title"', 'class="bp-screen-title screen-reader-text visually-hidden"', $output );

	$start = strpos( $output, '<ul id="member-secondary-nav" class="subnav bp-priority-subnav-nav-items">' );
	if ( false === $start ) {
		$start = strpos( $output, '<ul class="subnav">' );
	}
	if ( false !== $start ) {
        $offset = strpos( $output, 'class="bp-personal-sub-tab current selected"', $start );
        $offset = strpos( $output, '<a ', $offset );
        $offset = strpos( $output, '>', $offset ) + 1;
        $end    = strpos( $output, '</a>', $offset );
        $length = $end - $offset;
        $label  = substr( $output, $offset, $length );
        $label  = trim( $label );

		$output = substr_replace( $output, sprintf( '<div class="dropdown">%2$s<button class="btn btn-outline-secondary dropdown-toggle" type="button" id="subnav-dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">%1$s</button>%2$s', $label, "\n" ), $start, 0 );
		$offset = strpos( $output, '</ul>', $start );
		$output = substr_replace( $output, "\n" . '</div>', $offset + 5, 0 );

        $end    = strpos( $output, '</ul>', $start );
        $offset = strpos( $output, '<a ', $start );
        while ( false !== $offset && $offset < $end ) {
            $output = substr_replace( $output, 'class="dropdown-item" ', $offset + 3, 0 );

            $end    = strpos( $output, '</ul>', $start );
            $offset = strpos( $output, '<a ', $offset + 1 );
        }

        $end    = strpos( $output, '</ul>', $start );
        $offset = strpos( $output, 'class="bp-personal-sub-tab current selected"', $start );
        if ( false !== $offset && $offset < $end ) {
            $offset = strpos( $output, 'class="dropdown-item"', $offset );
            $output = substr_replace( $output, ' active', $offset + 20, 0 );
        }
	}

	$offset = strpos( $output, '<ul id="member-secondary-nav" class="subnav bp-priority-subnav-nav-items">' );
	if ( false !== $offset ) {
		$output = substr_replace( $output, ' dropdown-menu dropdown-menu-end" aria-labelledby="subnav-dropdown-toggle"', $offset + 44, 29 );
	} else {
		$offset = strpos( $output, '<ul class="subnav">' );
		if ( false !== $offset ) {
			$output = substr_replace( $output, ' dropdown-menu dropdown-menu-end" aria-labelledby="subnav-dropdown-toggle"', $offset + 17, 1 );
		}
	}

    $output = str_replace( '<ul class="subnav">', '<ul class="subnav dropdown-menu dropdown-menu-end" aria-labelledby="subnav-dropdown-toggle">', $output );

    return $output;
}
add_filter( 'enlightenment_bp_filter_template_member_activity_output', 'enlightenment_bp_filter_template_member_activity_output', 12 );

add_action( 'bp_activity_post_form_options', '__return_false' );

function enlightenment_bp_filter_activity_content_body( $output ) {
	return str_replace( '<span class="activity-read-more"', ' <span class="activity-read-more"', $output );
}
add_filter( 'bp_get_activity_content_body', 'enlightenment_bp_filter_activity_content_body', 10 );

function enlightenment_bp_filter_activity_excerpt_append_text( $output ) {
    $output = str_replace( '[', '', $output );
    $output = str_replace( ']', '', $output );

    return $output;
}
add_filter( 'bp_activity_excerpt_append_text', 'enlightenment_bp_filter_activity_excerpt_append_text' );

function enlightenment_bp_filter_activity_entry_buttons( $buttons ) {
    foreach ( $buttons as $key => $button ) {
        $label = '';

        switch ( $key ) {
            case 'activity_conversation':
                $label = __( 'Comment', 'enlightenment' );

                break;

            case 'activity_favorite':
                $class = explode( ' ', $buttons[ $key ]['button_attr']['class'] );

                if ( in_array( 'fav', $class ) ) {
                    $label = __( 'Favorite', 'enlightenment' );
                } elseif ( in_array( 'unfav', $class ) ) {
                    $label = __( 'Unfavorite', 'enlightenment' );
                }

                break;

            case 'activity_delete':
                $label = __( 'Delete', 'enlightenment' );

                break;
        }

        if ( ! empty( $label ) ) {
            $offset = strpos( $buttons[ $key ]['link_text'], '<span class="bp-screen-reader-text">' );

            if ( false !== $offset ) {
                $offset = strpos( $buttons[ $key ]['link_text'], '</span>', $offset );
                $buttons[ $key ]['link_text'] = substr_replace( $buttons[ $key ]['link_text'], sprintf( ' <span class="bp-button-label" aria-hidden="true">%s</span>', $label ), $offset + 7, 0 );
            }

            // $buttons[ $key ]['link_text'] = str_replace( 'class="bp-screen-reader-text"', 'class="bp-button-label"', $button['link_text'] );

            $buttons[ $key ]['button_attr']['class'] = str_replace( ' bp-tooltip', '', $button['button_attr']['class'] );

            unset( $buttons[ $key ]['button_attr']['data-bp-tooltip'] );
        }
    }

    return $buttons;
}
add_filter( 'bp_nouveau_get_activity_entry_buttons', 'enlightenment_bp_filter_activity_entry_buttons', 8 );

function enlightenment_bp_filter_template_activity_activity_loop_output( $output ) {
    return str_replace( 'class="view activity-time-since bp-tooltip"', 'class="view activity-time-since"', $output );
}
add_action( 'enlightenment_bp_filter_template_activity/activity-loop_output', 'enlightenment_bp_filter_template_activity_activity_loop_output', 12 );

function enlightenment_theme_bp_filter_activity_entry_comments( $output ) {
    $output = str_replace( 'class="ac-textarea mb-2"', 'class="ac-textarea"', $output );
    $output = str_replace( 'class="btn btn-secondary btn-sm"', 'class="btn btn-outline-secondary btn-sm"', $output );
    $output = str_replace( 'class="ac-reply-cancel btn btn-secondary btn-sm"', 'class="ac-reply-cancel btn btn-outline-secondary btn-sm"', $output );
    $output = str_replace( '&nbsp; <button ', ' <button ', $output );

    return $output;
}
add_action( 'enlightenment_bp_filter_activity_entry_comments', 'enlightenment_theme_bp_filter_activity_entry_comments', 12 );

function enlightenment_bp_add_member_sidebar_to_activity( $output ) {
    $alert = '';
    if ( bp_current_user_can( 'bp_moderate' ) && ! is_customize_preview() && ! is_active_sidebar( 'sidebar-buddypress-members' ) ) {
        $alert .= '<div class="bp-feedback custom-homepage-info info alert alert-info">';
        $alert .= sprintf( '<button type="button" class="bp-tooltip btn-close float-end ms-1" data-bp-tooltip="%s" aria-label="%s" data-bp-close="remove"></button>', esc_attr_x( 'Close', 'button', 'enlightenment' ), esc_attr__( 'Close this notice', 'enlightenment' ) );
		$alert .= sprintf( '<h3 class="alert-heading h4">%s</h3>', esc_html__( 'Manage the members sidebar', 'enlightenment' ) );
        $alert .= sprintf(
			/* translators: 1: link to the customizer option. 2: link to the customizer widgets section. */
			esc_html__( 'You can set the preferences of the %1$s or add %2$s to it.', 'enlightenment' ),
            bp_nouveau_get_customizer_link(
        		array(
        			'object'    => 'user',
        			'autofocus' => 'bp_nouveau_user_front_page',
        			'text'      => __( 'members sidebar', 'enlightenment' ),
        		)
        	),
			bp_nouveau_members_get_customizer_widgets_link()
		);
		$alert .= '</div>';
    }

    $description   = '';
    $user_settings = bp_nouveau_get_appearance_settings();
    if ( ! empty( $user_settings['user_front_bio'] ) ) {
        if ( get_the_author_meta( 'description', bp_displayed_user_id() ) ) {
            $description .= '<div class="member-description">';

			$description .= '<blockquote class="member-bio">';
            ob_start();
            bp_nouveau_member_description( bp_displayed_user_id() );
            $description .= ob_get_clean();
            $description .= '</blockquote>';

            if ( bp_is_my_profile() ) {
                $description .= bp_nouveau_member_get_description_edit_link();
            }

    		$description .= '</div>';
		} elseif ( bp_is_my_profile() ) {
            $description .= '<div class="member-description">';
            $description .= bp_nouveau_member_get_description_edit_link();
    		$description .= '</div>';
        }
    }

    $widgets = '';
    if ( is_active_sidebar( 'sidebar-buddypress-members' ) ) {
		$widgets .= '<div id="member-front-widgets" class="bp-sidebar bp-widget-area" role="complementary">';
        ob_start();
        dynamic_sidebar( 'sidebar-buddypress-members' );
        $widgets .= ob_get_clean();
		$widgets .= '</div>';
	}

    if ( empty( $alert ) && empty( $description ) && empty( $widgets ) ) {
        return $output;
    }

    $output  = sprintf( '<div class="bp-member-activity">%s</div>', $output );
    $sidebar = sprintf( '<div class="member-front-page widget-area sidebar sidebar-bp-member">%s %s %s</div>', $alert, $description, $widgets );

    return sprintf( '<div class="row"><div class="col-lg-8">%s</div><div class="col-lg-4 order-lg-first">%s</div></div>', $output, $sidebar );
}
add_filter( 'enlightenment_bp_filter_template_member_activity_output', 'enlightenment_bp_add_member_sidebar_to_activity', 14 );

function enlightenment_bp_filter_template_member_profile_change_avatar_output( $output ) {
    return str_replace( 'class="button avatar-webcam-capture btn btn-secondary btn-lg"', 'class="button avatar-webcam-capture btn btn-outline-secondary btn-lg"', $output );
}
add_filter( 'enlightenment_bp_filter_template_member_profile_change-avatar_output', 'enlightenment_bp_filter_template_member_profile_change_avatar_output', 12 );

function enlightenment_bp_filter_template_members_single_notifications_notifications_loop_output( $output ) {
	return str_replace( 'class="button action btn btn-secondary"', 'class="button action btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_bp_filter_template_members/single/notifications/notifications-loop_output', 'enlightenment_bp_filter_template_members_single_notifications_notifications_loop_output', 12 );

function enlightenment_theme_bp_filter_member_messages_content( $output ) {
	$offset = strpos( $output, 'id="tmpl-bp-bulk-actions"' );
	if ( false !== $offset ) {
		$offset = strpos( $output, 'class="messages-button btn btn-secondary ', $offset );
		$output = substr_replace( $output, 'outline-', $offset + 31, 0 );
	}

    $offset = strpos( $output, 'id="tmpl-bp-messages-thread"' );
	if ( false !== $offset ) {
		$offset = strpos( $output, 'class="thread-cb ', $offset );
		$offset = strpos( $output, '>', $offset );
		$output = substr_replace( $output, "\n" . '<div class="form-check">', $offset + 1, 0 );
		$offset = strpos( $output, 'class="message-check"', $offset );
        $output = substr_replace( $output, ' form-check-input', $offset + 20, 0 );
		$offset = strpos( $output, 'for="bp-message-thread-{{data.id}}" class="', $offset );
        $output = substr_replace( $output, 'form-check-label"><span class="', $offset + 43, 0 );
		$offset = strpos( $output, '</label>', $offset );
        $output = substr_replace( $output, '</span>', $offset, 0 );
		$offset = strpos( $output, '</div>', $offset );
        $output = substr_replace( $output, '</div>' . "\n", $offset, 0 );


        $offset_a = strpos( $output, 'src="{{{data.sender_avatar}}}"', $offset );
		$length   = 26;
		if ( false === $offset_a ) {
			$offset_a = strpos( $output, 'src="{{data.sender_avatar}}"', $offset );
			$length   = 25;
		}
		if ( false !== $offset_a ) {
	        $output   = substr_replace( $output, ".replace('?s=32&', '?s=96&').replace('?s=28&', '?s=96&')", $offset_a + $length, 0 );
	        $offset_a = strpos( $output, 'alt=', $offset_a );
	        $output   = substr_replace( $output, 'width="48" height="48" ', $offset_a, 0 );
		}

        $offset_a = strpos( $output, 'src="{{{recipient.avatar}}}"', $offset );
		$length   = 24;
		if ( false === $offset_a ) {
			$offset_a = strpos( $output, 'src="{{recipient.avatar}}"', $offset );
			$length   = 23;
		}
		if ( false !== $offset_a ) {
	        $output   = substr_replace( $output, ".replace('?s=32&', '?s=96&').replace('?s=28&', '?s=96&')", $offset_a + $length, 0 );
	        $offset_a = strpos( $output, 'alt=', $offset );
	        $output   = substr_replace( $output, 'width="48" height="48" ', $offset_a, 0 );
		}

        $offset = strpos( $output, '<span class="thread-count">(', $offset );
        $output = substr_replace( $output, '', $offset + 27, 1 );
        $offset = strpos( $output, ')</span>', $offset );
        $output = substr_replace( $output, '', $offset, 1 );
	}

    $offset = strpos( $output, 'id="tmpl-bp-messages-preview"' );
	if ( false !== $offset ) {
        $offset_a = strpos( $output, 'src="{{{data.recipients[i].avatar}}}"', $offset );
		$length   = 33;
		if ( false === $offset_a ) {
			$offset_a = strpos( $output, 'src="{{data.recipients[i].avatar}}"', $offset );
			$length   = 32;
		}
		if ( false !== $offset_a ) {
	        $output   = substr_replace( $output, ".replace('?s=28&', '?s=48&')", $offset_a + $length, 0 );
	        $offset_a = strpos( $output, 'alt=', $offset );
	        $output   = substr_replace( $output, 'width="24" height="24" ', $offset_a, 0 );
		}
    }

    $offset = strpos( $output, 'id="tmpl-bp-messages-single-header"' );
    if ( false !== $offset ) {
        $offset_a = strpos( $output, 'src="{{{data.recipients[i].avatar}}}"', $offset );
		$length   = 33;
		if ( false === $offset_a ) {
			$offset_a = strpos( $output, 'src="{{data.recipients[i].avatar}}"', $offset );
			$length   = 32;
		}
		if ( false !== $offset_a ) {
	        $output   = substr_replace( $output, ".replace('?s=28&', '?s=48&')", $offset_a + $length, 0 );
	        $offset_a = strpos( $output, 'alt=', $offset );
	        $output   = substr_replace( $output, 'width="24" height="24" ', $offset_a, 0 );
		}
    }

    $offset = strpos( $output, 'id="tmpl-bp-messages-single-list"' );
	if ( false !== $offset ) {
		$offset = strpos( $output, '>', $offset );
        $output = substr_replace( $output, "\n" . '<div class="message<# if( data.sender_id == document.getElementById(\'item-header\').dataset.bpItemId ) { #> by-me<# } #>">', $offset + 1, 0 );

        $offset_a = strpos( $output, 'src="{{{data.sender_avatar}}}"', $offset );
		$length   = 26;
		if ( false === $offset_a ) {
			$offset_a = strpos( $output, 'src="{{{data.sender_avatar}}}"', $offset );
			$length   = 25;
		}
		if ( false !== $offset_a ) {
	        $output   = substr_replace( $output, ".replace('?s=32&', '?s=96&')", $offset_a + $length, 0 );
	        $offset_a = strpos( $output, 'alt=', $offset );
	        $output   = substr_replace( $output, 'width="48" height="48" ', $offset_a, 0 );
		}

        $offset = strpos( $output, '</script>', $offset );
        $output = substr_replace( $output, '</div>' . "\n", $offset, 0 );
    }

    $offset = strpos( $output, 'id="tmpl-bp-messages-single"' );
	if ( false !== $offset ) {
		$offset = strpos( $output, '?s=30&', $offset );
		$output = substr_replace( $output, '96', $offset + 3, 2 );
        $offset = strpos( $output, 'width="30"', $offset );
		$output = substr_replace( $output, '48', $offset + 7, 2 );
        $offset = strpos( $output, 'height="30"', $offset );
		$output = substr_replace( $output, '48', $offset + 8, 2 );
	}

	$offset = strpos( $output, 'id="tmpl-bp-messages-form"' );
	if ( false !== $offset ) {
		$offset = strpos( $output, ' btn btn-secondary btn-lg"', $offset );
		$output = substr_replace( $output, '-outline', $offset + 8, 0 );
	}

    return $output;
}
add_filter( 'enlightenment_bp_filter_member_messages_content', 'enlightenment_theme_bp_filter_member_messages_content', 12 );
add_filter( 'enlightenment_bp_filter_wp_footer_content', 'enlightenment_theme_bp_filter_member_messages_content', 12 );

function enlightenment_theme_bp_filter_message_search_form( $output ) {
	return str_replace( 'class="btn btn-light"', 'class="btn btn-theme-inverse"', $output );
}
add_filter( 'bp_message_search_form', 'enlightenment_theme_bp_filter_message_search_form', 12 );

function enlightenment_bp_filter_template_member_friends_output( $output ) {
    $output = str_replace( 'class="list-wrap row"', 'class="list-wrap row align-items-center"', $output );
    $output = str_replace( '?s=384&', '?s=192&', $output );
    $output = str_replace( 'width="384"', 'width="96"', $output );
    $output = str_replace( 'height="384"', 'height="96"', $output );
    $output = str_replace( 'class="button accept btn btn-primary"', 'class="button accept btn btn-primary btn-sm"', $output );
	$output = str_replace( 'class="button reject btn btn-secondary"', 'class="button reject btn btn-outline-secondary btn-sm"', $output );

    return $output;
}
add_filter( 'enlightenment_bp_filter_template_member_friends_output', 'enlightenment_bp_filter_template_member_friends_output', 12 );

function enlightenment_bp_filter_template_member_groups_output( $output ) {
    $output = str_replace( 'class="wrap row"', 'class="wrap row align-items-center"', $output );
    $output = str_replace( 'class="button accept btn btn-primary ', 'class="button accept btn btn-primary btn-sm ', $output );
    $output = str_replace( 'class="button reject btn btn-secondary ', 'class="button reject btn btn-outline-secondary btn-sm ', $output );

    return $output;
}
add_filter( 'enlightenment_bp_filter_template_member_groups_output', 'enlightenment_bp_filter_template_member_groups_output', 12 );

function enlightenment_bp_filter_loop_item( $output ) {
    $output = str_replace( 'class="list-wrap row"', 'class="list-wrap row align-items-center"', $output );
    $output = str_replace( '?s=384&', '?s=192&', $output );
    $output = str_replace( 'width="384"', 'width="96"', $output );
    $output = str_replace( 'height="384"', 'height="96"', $output );
	$output = str_replace( 'class="friendship-button btn btn-secondary ', 'class="friendship-button btn btn-outline-secondary btn-sm ', $output );
    $output = str_replace( 'class="group-button btn btn-secondary', 'class="group-button btn btn-outline-secondary btn-sm ', $output );

    return $output;
}
add_filter( 'enlightenment_bp_filter_template_members/members-loop_output', 'enlightenment_bp_filter_loop_item', 12 );
add_filter( 'enlightenment_bp_filter_template_groups/groups-loop_output', 'enlightenment_bp_filter_loop_item', 12 );
add_filter( 'enlightenment_bp_filter_template_groups/single/members-loop_output', 'enlightenment_bp_filter_loop_item', 12 );

function enlightenment_theme_bp_filter_member_latest_update( $output, $args ) {
    global $members_template;

    $update = maybe_unserialize( $members_template->member->latest_update );

	if ( ! isset( $update['content'] ) ) {
		return $output;
	}

    $content = trim( wp_kses( bp_create_excerpt( $update['content'], $args['length'] ), 'strip' ) );
    $content = apply_filters( 'bp_get_activity_latest_update_excerpt', $content, $args );

    $output = str_replace( sprintf( '- &quot;%s&quot;', $content ), sprintf( '&ldquo;%s&rdquo;', $content ), $output );

    return $output;
}
add_filter( 'bp_get_member_latest_update', 'enlightenment_theme_bp_filter_member_latest_update', 10, 2 );

function enlightenment_bp_filter_template_member_settings_general_output( $output ) {
	$output = str_replace( 'class="button wp-generate-pw btn btn-secondary ', 'class="button wp-generate-pw btn btn-outline-secondary ', $output );
	$output = str_replace( 'class="button wp-hide-pw btn btn-light"', 'class="button wp-hide-pw btn btn-theme-inverse"', $output );
	$output = str_replace( 'class="button wp-cancel-pw btn btn-light"', 'class="button wp-cancel-pw btn btn-theme-inverse"', $output );

	return $output;
}
add_filter( 'enlightenment_bp_filter_template_member_settings_general_output', 'enlightenment_bp_filter_template_member_settings_general_output', 12 );

function enlightenment_theme_bp_filter_group_creation_screen( $output ) {
	return str_replace( 'class="btn btn-secondary btn-lg"', 'class="btn btn-outline-secondary btn-lg"', $output );
}
add_filter( 'enlightenment_bp_group_creation_screen', 'enlightenment_theme_bp_filter_group_creation_screen', 12 );

function enlightenment_bp_filter_group_invites_screen( $output ) {
	$output = str_replace( 'class="nouveau-search-submit btn btn-light"', 'class="nouveau-search-submit btn btn-theme-inverse"', $output );
    $output = str_replace( 'class="button bp-secondary-action btn btn-secondary"', 'class="button bp-secondary-action btn btn-outline-secondary"', $output );
    $output = str_replace( '{{data.avatar}}', "{{data.avatar.replace('?s=50&', '?s=96&')}}", $output );
    $output = str_replace( '{{data.invited_by[i].avatar}}', "{{data.invited_by[i].avatar.replace('?s=50&', '?s=48&')}}", $output );
    $output = str_replace( 'width="30px"', 'width="24" height="24"', $output );

    return $output;
}
add_filter( 'enlightenment_bp_filter_group_creation_group-invites_screen', 'enlightenment_bp_filter_group_invites_screen', 12 );
add_filter( 'enlightenment_bp_filter_group_admin_group-invites_screen', 'enlightenment_bp_filter_group_invites_screen', 12 );
add_filter( 'enlightenment_bp_filter_template_group_send-invites_output', 'enlightenment_bp_filter_group_invites_screen', 12 );
add_filter( 'enlightenment_bp_filter_wp_footer_content', 'enlightenment_bp_filter_group_invites_screen', 12 );

function enlightenment_bp_filter_template_group_activity_output( $output ) {
	$output = str_replace( 'class="bp-screen-title"', 'class="bp-screen-title screen-reader-text visually-hidden"', $output );

    $offset = strpos( $output, '<div id="bp-activity-ajax-loader">' );
    if ( false !== $offset ) {
		$offset = strpos( $output, 'class="ms-2 mb-0"', $offset );
        $output = substr_replace( $output, 'screen-reader-text visually-hidden', $offset + 7, 9 );
    }

    return $output;
}
add_filter( 'enlightenment_bp_filter_template_group_activity_output', 'enlightenment_bp_filter_template_group_activity_output', 12 );

function enlightenment_bp_add_group_sidebar_to_activity( $output ) {
    $alert = '';
    if ( bp_current_user_can( 'bp_moderate' ) && ! is_customize_preview() && ! is_active_sidebar( 'sidebar-buddypress-groups' ) && ! bp_nouveau_groups_do_group_boxes() ) {
        $alert .= '<div class="bp-feedback custom-homepage-info info no-icon alert alert-info">';
		$alert .= sprintf( '<h3 class="alert-heading h4">%s</h3>', esc_html__( 'Manage the groups sidebar', 'enlightenment' ) );
        $alert .= sprintf(
			/* translators: 1: link to the customizer option. 2: link to the customizer widgets section. */
			esc_html__( 'You can set the preferences of the %1$s or add %2$s to it.', 'enlightenment' ),
            bp_nouveau_get_customizer_link(
        		array(
                    'object'    => 'group',
        			'autofocus' => 'bp_nouveau_group_front_page',
        			'text'      => __( 'groups sidebar', 'enlightenment' ),
        		)
        	),
			bp_nouveau_groups_get_customizer_widgets_link()
		);
		$alert .= '</div>';
    }

    $description    = '';
    $group_settings = bp_nouveau_get_appearance_settings();
    if ( ! empty( $group_settings['group_front_description'] ) && bp_nouveau_group_has_meta( 'description' ) ) {
        $description .= '<div class="group-description">';
		$description .= bp_get_group_description();
		$description .= '</div>';
    }

    $header_actions = '';
    if ( ! has_action( 'enlightenment_page_header', 'enlightenment_bp_group_header_actions' ) ) {
        ob_start();
        enlightenment_bp_group_header_actions();
        $header_actions .= ob_get_clean();
    }

    $group_boxes = '';
    if ( bp_nouveau_groups_do_group_boxes() ) {
		$group_boxes .= '<div class="bp-plugin-widgets">';
        ob_start();
        bp_custom_group_boxes();
        $group_boxes .= ob_get_clean();
		$group_boxes .= '</div>';
	}

    $widgets = '';
    if ( is_active_sidebar( 'sidebar-buddypress-groups' ) ) {
		$widgets .= '<div id="group-front-widgets" class="bp-sidebar bp-widget-area" role="complementary">';
        ob_start();
        dynamic_sidebar( 'sidebar-buddypress-groups' );
        $widgets .= ob_get_clean();
		$widgets .= '</div>';
	}

    if ( empty( $alert ) && empty( $description ) && empty( $header_actions ) && empty( $group_boxes ) && empty( $widgets ) ) {
        return $output;
    }

    $output  = sprintf( '<div class="bp-group-activity">%s</div>', $output );
    $sidebar = sprintf( '<div class="group-front-page widget-area sidebar sidebar-bp-group">%s %s %s %s %s</div>', $alert, $description, $header_actions, $group_boxes, $widgets );

    return sprintf( '<div class="row"><div class="col-lg-8">%s</div><div class="col-lg-4 order-lg-first">%s</div></div>', $output, $sidebar );
}
add_filter( 'enlightenment_bp_filter_template_group_activity_output', 'enlightenment_bp_add_group_sidebar_to_activity', 14 );

function enlightenment_bp_filter_ajax_get_users_to_invite_args( $args ) {
    $args['per_page'] = 24;

    return $args;
}
add_filter( 'bp_before_nouveau_ajax_get_users_to_invite_parse_args', 'enlightenment_bp_filter_ajax_get_users_to_invite_args' );

function enlightenment_bp_filter_bbp_subscription_toggle( $output, $args, $user_id ) {
    if ( ! bp_is_group() ) {
        return $output;
    }

    return str_replace( 'class="subscription-toggle btn btn-light btn-lg"', 'class="subscription-toggle btn btn-outline-secondary btn-lg"', $output );
}
add_filter( 'bbp_get_user_subscribe_link', 'enlightenment_bp_filter_bbp_subscription_toggle', 14, 3 );

function enlightenment_bp_filter_bbp_user_favorites_link( $output ) {
    if ( ! bp_is_group() ) {
        return $output;
    }

    return str_replace( 'class="btn btn-light btn-lg dropdown-toggle"', 'class="btn btn-outline-secondary btn-lg dropdown-toggle"', $output );
}
add_filter( 'bbp_get_user_favorites_link', 'enlightenment_bp_filter_bbp_user_favorites_link', 14 );

function enlightenment_bp_filter_template_group_members_output( $output ) {
	return str_replace( 'class="bp-screen-title"', 'class="bp-screen-title screen-reader-text visually-hidden"', $output );
}
add_filter( 'enlightenment_bp_filter_template_group_members_output', 'enlightenment_bp_filter_template_group_members_output' );

function enlightenment_bp_filter_group_admin_manage_members_screen( $output ) {
    $output = str_replace( "{{{data.avatar_urls.thumb}}}", "{{{data.avatar_urls.thumb.replace('?s=96&', '?s=192&')}}}", $output );
    $output = str_replace( 'class="btn btn-secondary btn-sm"', 'class="btn btn-outline-secondary btn-sm"', $output );
	$output = str_replace( 'class="bp-button bp-search btn btn-light"', 'class="bp-button bp-search btn btn-theme-inverse"', $output );

    $offset = strpos( $output, 'id="tmpl-bp-manage-members-updating"' );
    if ( false !== $offset ) {
        $offset_a = strpos( $output, '<small>', $offset );
		$end_a    = strpos( $output, '</script>', $offset );
		while ( false !== $offset_a && $offset_a < $end_a ) {
			$output   = substr_replace( $output, '<div><i class="fas fa-spinner fa-pulse"></i> <span class="screen-reader-text visually-hidden">', $offset_a, 7 );
            $offset_a = strpos( $output, '</small>', $offset );
            $output   = substr_replace( $output, '</span></div>', $offset_a, 8 );

			$end_a    = strpos( $output, '</script>', $offset );
			$offset_a = strpos( $output, '<small>', $offset_a );
		}
    }

    return $output;
}
add_filter( 'enlightenment_bp_filter_group_admin_manage-members_screen', 'enlightenment_bp_filter_group_admin_manage_members_screen', 12 );
add_filter( 'enlightenment_bp_filter_wp_footer_content', 'enlightenment_bp_filter_group_admin_manage_members_screen', 12 );

function enlightenment_bp_filter_template_groups_single_requests_loop_output( $output ) {
    $output = str_replace( '?s=96&', '?s=192&', $output );
	$output = str_replace( 'class="button btn btn-secondary ', 'class="button btn btn-outline-secondary ', $output );

    return $output;
}
add_filter( 'enlightenment_bp_filter_template_groups/single/requests-loop_output', 'enlightenment_bp_filter_template_groups_single_requests_loop_output', 12 );

function enlightenment_bp_filter_template_blogs_blogs_loop_output( $output ) {
    return str_replace( 'class="blog-button btn btn-secondary ', 'class="blog-button btn btn-outline-secondary btn-sm ', $output );
}
add_filter( 'enlightenment_bp_filter_template_blogs/blogs-loop_output', 'enlightenment_bp_filter_template_blogs_blogs_loop_output', 12 );

function enlightenment_bp_filter_register_form( $output ) {
	return str_replace( 'class="button wp-hide-pw btn btn-light"', 'class="button wp-hide-pw btn btn-theme-inverse"', $output );
}
add_filter( 'enlightenment_bp_register_form', 'enlightenment_bp_filter_register_form', 12 );

function enlightenment_bp_filter_main_script_args( $args ) {
    $args['bp_group_description_more'] = _x( 'More', 'expand group description', 'enlightenment' );
    $args['bp_group_description_less'] = _x( 'Less', 'collapse group description', 'enlightenment' );

    return $args;
}
// add_filter( 'enlightenment_main_script_args', 'enlightenment_bp_filter_main_script_args' );

function enlightenment_bp_filter_member_block( $output ) {
	return str_replace( '?s=96&', '?s=192&', $output );
}
add_filter( 'enlightenment_render_block_bp_member', 'enlightenment_bp_filter_member_block' );

function enlightenment_bp_dynamic_list_block( $output ) {
	$separator = apply_filters( 'bp_members_widget_separator', '|' );
    $output    = str_replace( sprintf( '<span class="bp-separator" role="separator">%s</span>', esc_html( $separator ) ), '', $output );

    $separator = apply_filters( 'bp_groups_widget_separator', '|' );
    $output    = str_replace( sprintf( '<span class="bp-separator" role="separator">%s</span>', esc_html( $separator ) ), '', $output );

    $selected = '';
    $offset   = strpos( $output, 'class="item-options"' );
    if ( false !== $offset ) {
        $offset   = strpos( $output, 'class="selected"', $offset );
        $start    = strpos( $output, '>', $offset ) + 1;
        $end      = strpos( $output, '<', $offset );
        $length   = $end - $start;
        $selected = substr( $output, $start, $length );
    }

    $offset = strpos( $output, 'class="item-options"' );
    if ( false !== $offset ) {
        $output = substr_replace( $output, ' dropdown', $offset + 19, 0 );
        $offset = strpos( $output, '>', $offset );
        $output = substr_replace( $output, "\n" . sprintf( '<button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">%s</button>', $selected ), $offset + 1, 0 );
        $offset = strpos( $output, '</button>', $offset );
        $output = substr_replace( $output, "\n" . '<div class="dropdown-menu dropdown-menu-end">', $offset + 9, 0 );

        $offset_a = strpos( $output, '<a ', $offset );
        $end_a    = strpos( $output, '</div>', $offset );
        while ( false !== $offset_a && $offset_a < $end_a ) {
            $offset_b = strpos( $output, 'class="selected"', $offset_a );
            $end_b    = strpos( $output, '>', $offset_a );
            if ( false !== $offset_b && $offset_b < $end_b ) {
                $output = substr_replace( $output, 'dropdown-item active ', $offset_b + 7, 0 );
            } else {
                $output = substr_replace( $output, ' class="dropdown-item"', $offset_a + 2, 0 );
            }

            $offset_c = strpos( $output, '|', $offset_a );
            $end_c    = strpos( $output, '</div>', $offset_c );
            if ( false !== $offset_c && $offset_c < $end_c ) {
                $output = substr_replace( $output, '', $offset_c, 1 );
            }

            $offset_a = strpos( $output, '<a ', $offset_a + 1 );
        }

        $offset = strpos( $output, '</div>', $offset );
        $output = substr_replace( $output, '</div>' . "\n", $offset, 0 );
    }

	return $output;
}
add_filter( 'enlightenment_render_block_bp_dynamic_members', 'enlightenment_bp_dynamic_list_block' );
add_filter( 'enlightenment_render_block_bp_friends',         'enlightenment_bp_dynamic_list_block' );
add_filter( 'enlightenment_render_block_bp_dynamic_groups',  'enlightenment_bp_dynamic_list_block' );

function enlightenment_bp_filter_latest_activities_block( $output ) {
	$output = str_replace( '?s=40&', '?s=108&', $output );
	$output = str_replace( ' avatar-40 ', ' avatar-54 ', $output );
	$output = str_replace( 'width="40"', 'width="54"', $output );
	$output = str_replace( 'height="40"', 'height="54"', $output );

	return $output;
}
add_filter( 'enlightenment_render_block_bp_latest_activities', 'enlightenment_bp_filter_latest_activities_block' );

function enlightenment_bp_filter_bp_login_form_block( $output ) {
	if ( is_user_logged_in() ) {
		$output = str_replace( '?s=50&', '?s=128&', $output );
        $output = str_replace( ' avatar-50 ', ' avatar-64 ', $output );
        $output = str_replace( 'width="50"', 'width="64"', $output );
        $output = str_replace( 'height="50"', 'height="64"', $output );
    	$output = str_replace( 'class="logout btn btn-secondary ', 'class="logout btn btn-outline-secondary ', $output );
	} else {
		$output = str_replace( 'class="btn btn-secondary ', 'class="btn btn-outline-secondary ', $output );
	}

	return $output;
}
add_filter( 'enlightenment_render_block_bp_login_form', 'enlightenment_bp_filter_bp_login_form_block', 12 );

function enlightenment_bp_filter_bp_core_login_widget( $output, $widget, $instance ) {
    $title = isset( $instance['title'] ) ? $instance['title'] : '';
    $title = apply_filters( 'widget_title', $title, $instance, $widget['callback'][0]->id_base );

    if ( empty( $title ) ) {
        if ( is_user_logged_in() ) {
            $title = __( 'My Account', 'enlightenment' );
        } else {
            $title = __( 'Log In', 'enlightenment' );;
        }

        $output = str_replace( '<h3 class="widget-title"></h3>', sprintf( '<h3 class="widget-title">%s</h3>', $title ), $output );
    }

    return $output;
}
add_filter( 'enlightenment_widget_bp_core_login_widget', 'enlightenment_bp_filter_bp_core_login_widget', 10, 3 );
add_filter( 'enlightenment_widget_bp_classic_core_login_widget', 'enlightenment_bp_filter_bp_core_login_widget', 10, 3 );

function enlightenment_bp_filter_latest_activities_widget( $output ) {
	$output = str_replace( '?s=40&', '?s=48&', $output );
    $output = str_replace( 'width="40"', 'width="24"', $output );
    $output = str_replace( 'height="40"', 'height="24"', $output );
    $output = str_replace( ' avatar-40 ', ' avatar-24 ', $output );

	return $output;
}
add_filter( 'enlightenment_widget_bp_latest_activities', 'enlightenment_bp_filter_latest_activities_widget' );
add_filter( 'enlightenment_widget_bp_classic_templates_nouveau_latest_activities', 'enlightenment_bp_filter_latest_activities_widget' );

function enlightenment_bp_filter_list_widget( $output ) {
    $separator = apply_filters( 'bp_members_widget_separator', '|' );
    $output    = str_replace( sprintf( '<span class="bp-separator" role="separator">%s</span>', esc_html( $separator ) ), '', $output );

    $separator = apply_filters( 'bp_groups_widget_separator', '|' );
    $output    = str_replace( sprintf( '<span class="bp-separator" role="separator">%s</span>', esc_html( $separator ) ), '', $output );

	// $output    = str_replace( '| <a ', '<a ', $output );

    $selected = '';
    $start    = strpos( $output, '<div class="item-options"' );
    if ( false !== $start ) {
		$end      = strpos( $output, '</div>', $start );
        $offset   = strpos( $output, 'class="selected"', $start );

		if ( false === $offset || $offset > $end ) {
			$offset = strpos( $output, '<a ', $start );
			$offset = strpos( $output, '<a ', $offset + 1 );
			$offset = strpos( $output, 'class="', $offset );
			$output = substr_replace( $output, 'selected', $offset + 7, 0 );
		}

		if ( false !== $offset && $offset < $end ) {
	        $start_a  = strpos( $output, '>', $offset ) + 1;
	        $end_a    = strpos( $output, '<', $offset );
	        $length_a = $end_a - $start_a;
	        $selected = substr( $output, $start_a, $length_a );
		}
    }

    $offset = strpos( $output, 'class="item-options"' );
    if ( false !== $offset ) {
        $output = substr_replace( $output, ' dropdown', $offset + 19, 0 );
        $offset = strpos( $output, '>', $offset );
        $output = substr_replace( $output, "\n" . sprintf( '<button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">%s</button>', $selected ), $offset + 1, 0 );
        $offset = strpos( $output, '</button>', $offset );
        $output = substr_replace( $output, "\n" . '<div class="dropdown-menu dropdown-menu-end">', $offset + 9, 0 );

        $offset_a = strpos( $output, '<a ', $offset );
        $end_a    = strpos( $output, '</div>', $offset );
        while ( false !== $offset_a && $offset_a < $end_a ) {
            $offset_b = strpos( $output, 'class="selected"', $offset_a );
            $end_b    = strpos( $output, '>', $offset_a );
            if ( false !== $offset_b && $offset_b < $end_b ) {
                $output = substr_replace( $output, 'dropdown-item active ', $offset_b + 7, 0 );
            } else {
				$offset_b = strpos( $output, 'class="', $offset_a );

				if ( false !== $offset_b && $offset_b < $end_b ) {
					$output = substr_replace( $output, 'dropdown-item', $offset_b + 7, 0 );
				} else {
	                $offset_b = strpos( $output, 'id="', $offset_a );
	                $offset_b = strpos( $output, '"', $offset_b + 4 );
	                $output = substr_replace( $output, ' class="dropdown-item"', $offset_b + 1, 0 );
				}
            }

            $offset_c = strpos( $output, '|', $offset_a );
            $end_c    = strpos( $output, '</div>', $offset_c );
            if ( false !== $offset_c && $offset_c < $end_c ) {
                $output = substr_replace( $output, '', $offset_c, 1 );
            }

            $offset_a = strpos( $output, '<a ', $offset_a + 1 );
        }

        $offset = strpos( $output, '</div>', $offset );
        $output = substr_replace( $output, '</div>' . "\n", $offset, 0 );
    }

    return $output;
}
add_filter( 'enlightenment_widget_bp_core_members_widget', 'enlightenment_bp_filter_list_widget' );
add_filter( 'enlightenment_widget_bp_classic_members_widget', 'enlightenment_bp_filter_list_widget' );
add_filter( 'enlightenment_widget_bp_core_friends_widget', 'enlightenment_bp_filter_list_widget' );
add_filter( 'enlightenment_widget_bp_classic_friends_widget', 'enlightenment_bp_filter_list_widget' );
add_filter( 'enlightenment_widget_bp_groups_widget', 'enlightenment_bp_filter_list_widget' );
add_filter( 'enlightenment_widget_bp_classic_groups_widget', 'enlightenment_bp_filter_list_widget' );

function enlightenment_bp_filter_friends_widget_title( $title, $instance = null, $id_base = '' ) {
    if ( empty( $instance ) ) {
        return $title;
    }

    if ( empty( $id_base ) ) {
        return $title;
    }

    if ( 'bp_core_friends_widget' == $id_base ) {
        $title = __( 'Friends', 'enlightenment' );
    }

    return $title;
}
add_filter( 'widget_title', 'enlightenment_bp_filter_friends_widget_title', 10, 3 );

function enblightenment_bp_filter_random_friends_widget_avatar_size( $size ) {
    return 144;
}
add_filter( 'enblightenment_bp_random_friends_widget_avatar_size', 'enblightenment_bp_filter_random_friends_widget_avatar_size' );

function enlightenment_theme_bp_filter_login_widget( $output ) {
    if ( is_user_logged_in() ) {
        $output = str_replace( '?s=50&', '?s=128&', $output );
        $output = str_replace( ' avatar-50 ', ' avatar-64 ', $output );
        $output = str_replace( 'width="50"', 'width="64"', $output );
        $output = str_replace( 'height="50"', 'height="64"', $output );
    	$output = str_replace( 'class="logout btn btn-secondary btn-sm"', 'class="logout btn btn-outline-secondary btn-sm"', $output );
    } else {
        $output = str_replace( 'class="btn btn-secondary ', 'class="btn btn-outline-secondary ', $output );
    }

    return $output;
}
add_filter( 'enlightenment_widget_bp_core_login_widget', 'enlightenment_theme_bp_filter_login_widget', 12 );
add_filter( 'enlightenment_widget_bp_classic_core_login_widget', 'enlightenment_theme_bp_filter_login_widget', 12, 3 );

/**
 * Notices
**/

function enlightenment_bp_remove_nouveau_sitewide_notices() {
    remove_action( 'bp_init', 'bp_nouveau_push_sitewide_notices', 99 );
}
add_action( 'bp_nouveau_includes', 'enlightenment_bp_remove_nouveau_sitewide_notices', 12 );

// Only hook the 'sitewide_notices' overlay if the Sitewide
// Notices widget is not in use (to avoid duplicate content).
if ( bp_is_active( 'messages' ) && ! is_active_widget( false, false, 'bp_messages_sitewide_notices_widget', true ) ) {
    add_action( 'wp_footer', 'enlightenment_bp_sitewide_notices', 9999 );
}

function enlightenment_bp_filter_option_rtmedia_options( $value ) {
	$value['defaultSizes_photo_thumbnail_width']  = 360;
    $value['defaultSizes_photo_thumbnail_height'] = 360;

	return $value;
}
add_filter( 'option_rtmedia-options', 'enlightenment_bp_filter_option_rtmedia_options' );
add_filter( 'site_option_rtmedia-options', 'enlightenment_bp_filter_option_rtmedia_options' );

function enlightenment_theme_bp_filter_option_rtmedia_options( $value ) {
	$value['general_masonry_layout_activity'] = false;

	return $value;
}
add_filter( 'option_rtmedia-options', 'enlightenment_theme_bp_filter_option_rtmedia_options' );
add_filter( 'site_option_rtmedia-options', 'enlightenment_theme_bp_filter_option_rtmedia_options' );

function enlightenment_bp_filter_rtmedia_options() {
	global $rtmedia;

    if ( ! isset( $rtmedia ) ) {
        return;
    }

    $rtmedia->options['general_masonry_layout_activity'] = false;

    remove_action( 'wp_head', array( &$rtmedia, 'custom_style_for_image_size' ) );
}
add_action( 'init', 'enlightenment_bp_filter_rtmedia_options' );

function enlightenment_bp_filter_rtmedia_bootstrap_custom_layouts( $layouts ) {
    if ( ! function_exists( 'is_rtmedia_page' ) ) {
		return $layouts;
	}

    if ( is_buddypress() ) {
		return $layouts;
	}

    if ( is_rtmedia_page() ) {
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
add_filter( 'enlightenment_custom_layouts', 'enlightenment_bp_filter_rtmedia_bootstrap_custom_layouts', 12 );

function enlightenment_bp_filter_rtmedia_display_content_add_itmes( $render_options, $options ) {
    if ( isset( $render_options['general_masonry_layout_activity'] ) ) {
        unset( $render_options['general_masonry_layout_activity'] );
    }

    return $render_options;
}
add_filter( 'rtmedia_display_content_add_itmes', 'enlightenment_bp_filter_rtmedia_display_content_add_itmes', 10, 2 );

function enlightenment_bp_filter_bootstrap_rtmedia_sub_nav( $output ) {
	return str_replace( 'class="count badge text-bg-light ', 'class="count badge text-bg-theme-inverse ', $output );
}
add_filter( 'enlightenment_bp_rtmedia_sub_nav', 'enlightenment_bp_filter_bootstrap_rtmedia_sub_nav', 12 );

function enlightenment_bp_filter_rtmedia_media_gallery_header( $output ) {
    $output = str_replace( 'class="clicker rtmedia-action-buttons btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown"', 'class="clicker rtmedia-action-buttons btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static"', $output );
    $output = str_replace( 'class="primary rtmedia-upload-media-link btn btn-secondary ', 'class="primary rtmedia-upload-media-link btn btn-outline-secondary ', $output );
    $output = str_replace( 'class="primary rtmedia-upload-media-link btn btn-secondary"', 'class="primary rtmedia-upload-media-link btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="rtmedia-upload-media-link primary btn btn-secondary ', 'class="rtmedia-upload-media-link primary btn btn-outline-secondary ', $output );
	$output = str_replace( 'class="rtmedia-upload-media-link primary btn btn-secondary"', 'class="rtmedia-upload-media-link primary btn btn-outline-secondary"', $output );

    return $output;
}
add_filter( 'enlightenment_bp_filter_template_rtmedia_media/media-gallery', 'enlightenment_bp_filter_rtmedia_media_gallery_header', 12 );
add_filter( 'enlightenment_bp_filter_template_rtmedia_media/album-gallery', 'enlightenment_bp_filter_rtmedia_media_gallery_header', 12 );

function enlightenment_bp_filter_template_group_rtmedia_media_gallery_pagination( $output ) {
	return str_replace( 'class="rtmedia-page-link button btn btn-secondary"', 'class="rtmedia-page-link button btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_bp_filter_template_rtmedia_media/media-gallery', 'enlightenment_bp_filter_template_group_rtmedia_media_gallery_pagination', 12 );
add_filter( 'enlightenment_bp_filter_template_rtmedia_media/album-gallery', 'enlightenment_bp_filter_template_group_rtmedia_media_gallery_pagination', 12 );

function enlightenment_bp_filter_rtmedia_gallery_search( $output ) {
	$output = str_replace( 'class="media_search_remove search_option btn btn-light ', 'class="media_search_remove search_option btn btn-theme-inverse ', $output );
	$output = str_replace( 'class="search_option btn btn-light"', 'class="search_option btn btn-theme-inverse"', $output );

	return $output;
}
add_filter( 'rtmedia_gallery_search', 'enlightenment_bp_filter_rtmedia_gallery_search', 12 );

function enlightenment_bp_filter_template_rtmedia_media_album_single_edit( $output ) {
    $output = str_replace( 'class="button rtm-button rtm-button-back btn btn-secondary btn-lg"', 'class="button rtm-button rtm-button-back btn btn-outline-secondary btn-lg"', $output );
    $output = str_replace( 'class="button rtmedia-move btn btn-secondary"', 'class="button rtmedia-move btn btn-outline-secondary"', $output );
    $output = str_replace( 'class="button rtmedia-delete-selected btn btn-danger"', 'class="button rtmedia-delete-selected btn btn-outline-danger"', $output );
    $output = str_replace( 'class="rtmedia-move-selected btn btn-secondary"', 'class="rtmedia-move-selected btn btn-outline-secondary"', $output );

    return $output;
}
add_filter( 'enlightenment_bp_filter_template_rtmedia_media/album-single-edit', 'enlightenment_bp_filter_template_rtmedia_media_album_single_edit', 12 );

function enlightenment_bp_filter_rtmedia_single_media_profile_picture_size( $size ) {
    return 128;
}
add_filter( 'rtmedia_single_media_profile_picture_size', 'enlightenment_bp_filter_rtmedia_single_media_profile_picture_size' );

function enlightenment_bp_filter_template_rtmedia_media_media_single( $output ) {
    global $rtmedia_media;

    $media_type = ! empty( $rtmedia_media->media_type ) ? $rtmedia_media->media_type : 'none';

    switch ( $media_type ) {
        case 'music':
            $output = str_replace( "class='btn btn-secondary btn-sm ", "class='btn btn-outline-secondary btn-sm ", $output );
            $output = str_replace( 'class="btn btn-secondary btn-sm ', 'class="btn btn-outline-secondary btn-sm ', $output );
            $output = str_replace( 'class="btn btn-secondary btn-sm"', 'class="btn btn-outline-secondary btn-sm"', $output );
            $output = str_replace( 'class="bp-suggestions ac-input form-control w-100 mb-2"', 'class="bp-suggestions ac-input form-control w-100"', $output );
            $output = str_replace( 'class="rtmedia-comment-media-upload btn btn-secondary btn-sm"', 'class="rtmedia-comment-media-upload btn btn-link btn-sm"', $output );

            break;

        default:
            $output = str_replace( "class='btn btn-secondary btn-sm ", "class='btn btn-outline-light btn-sm ", $output );
            $output = str_replace( 'class="btn btn-secondary btn-sm ', 'class="btn btn-outline-light btn-sm ', $output );
            $output = str_replace( 'class="btn btn-secondary btn-sm"', 'class="btn btn-outline-light btn-sm"', $output );
            $output = str_replace( 'class="bp-suggestions ac-input form-control w-100 mb-2"', 'class="bp-suggestions ac-input form-control w-100"', $output );
            $output = str_replace( 'class="rtmedia-comment-media-upload btn btn-secondary btn-sm"', 'class="rtmedia-comment-media-upload btn btn-link btn-sm"', $output );

            break;
    }

    return $output;
}
add_filter( 'enlightenment_bp_filter_template_rtmedia_media/media-single', 'enlightenment_bp_filter_template_rtmedia_media_media_single', 12 );

function enlightenment_bp_ajax_template_rtmedia_media_media_single_add_comment_link( $output ) {
    global $rtmedia_media;

    if ( ! rtmedia_comments_enabled() ) {
        return $output;
    }

    $start = strpos( $output, '<div class="rtm-like-comments-info ' );
    if ( false !== $start ) {
        $end = strpos( $output, '<div class="rtmedia-comments-container">', $start );

        if ( false !== $end ) {
            $count = count( get_comments(
    			array(
    				'post_id' => $rtmedia_media->media_id,
    				'order'   => 'ASC',
    			)
    		) );
            $label = $count ? sprintf( _n( '1 Comment', '%s Comments', $count, 'enlightement' ), number_format_i18n( $count ) ) : ( is_user_logged_in() ? __( 'Leave a Comment', 'enlightement' ) : '' );

            $start_a = strpos( $output, "<div class='rtmedia-like-info ", $start );
            if ( false !== $start_a && $start_a < $end ) {
                $offset = $start_a;
                $output = substr_replace( $output, '<div class="rtm-like-comments-wrap">', $offset, 0 );
                $offset = strpos( $output, "<div class='rtmedia-like-info ", $offset );
                $offset = strpos( $output, '</div>', $offset );

                if ( ! empty( $label ) ) {
                    $output = substr_replace( $output, sprintf( '<div class="rtm-comments-link">%s</div>', $label ), $offset + 6, 0 );
                    $offset = strpos( $output, '<div class="rtm-comments-link">', $offset );
                    $offset = strpos( $output, '</div>', $offset );
                }

                $output = substr_replace( $output, '</div>', $offset + 6, 0 );
                $offset = strpos( $output, '<div class="rtmedia-comments-container">', $offset );
                $output = substr_replace( $output, sprintf( '<div class="rtmedia-comments-header"><div class="rtmedia-comments-title">%s</div><div class="rtmedia-comments-close">&times;</div></div>', $label ), $offset + 40, 0 );
            }
        }
    }

    return $output;
}
add_filter( 'enlightenment_bp_filter_ajax_template_rtmedia_media/media-single', 'enlightenment_bp_ajax_template_rtmedia_media_media_single_add_comment_link', 8 );

function enlightenment_bp_filter_ajax_template_rtmedia_media_media_single( $output ) {
    $output = str_replace( 'class="rtm-lightbox-container row"', 'class="rtm-lightbox-container row g-0"', $output );
    $output = str_replace( 'class="rtm-lightbox-container row"', 'class="rtm-lightbox-container row g-0"', $output );
    $output = str_replace( 'class="carousel-control-prev-icon ', 'class="fas fa-chevron-left ', $output );
    $output = str_replace( 'class="carousel-control-next-icon ', 'class="fas fa-chevron-right ', $output );
    $output = str_replace( 'class="bp-suggestions ac-input form-control w-100 mb-2"', 'class="bp-suggestions ac-input form-control w-100"', $output );
    $output = str_replace( 'class="rtmedia-comment-media-upload btn btn-secondary btn-sm"', 'class="rtmedia-comment-media-upload btn btn-link btn-sm"', $output );

    return $output;
}
add_filter( 'enlightenment_bp_filter_ajax_template_rtmedia_media/media-single', 'enlightenment_bp_filter_ajax_template_rtmedia_media_media_single', 12 );

function enlightenment_bp_filter_rtmedia_single_content_filter( $output, $media ) {
    global $rtmedia;

    if ( 'video' === $media->media_type ) {
        $output = str_replace( 'max-width:96%;max-height:80%;', 'max-width:100%;max-height:100%;', $output );
    } elseif ( 'music' === $media->media_type ) {
        $width  = $rtmedia->options['defaultSizes_music_singlePlayer_width'];
        $width  = ( $width * 75 ) / 640;
        $output = str_replace( sprintf( 'width= %s%% ', esc_attr( $width ) ), 'width= 100% ', $output );
    }

    return $output;
}
add_filter( 'rtmedia_single_content_filter', 'enlightenment_bp_filter_rtmedia_single_content_filter', 10, 2 );

function enlightenment_bp_filter_template_rtmedia_media_media_single_edit( $output ) {
	$output = str_replace( 'class="button rtm-button rtm-button-back btn btn-secondary btn-lg"', 'class="button rtm-button rtm-button-back btn btn-outline-secondary btn-lg"', $output );
    $output = str_replace( 'class="button rtmedia-image-edit btn btn-secondary float-none"', 'class="button rtmedia-image-edit btn btn-outline-secondary float-none"', $output );

    return $output;
}
add_filter( 'enlightenment_bp_filter_template_rtmedia_media/media-single-edit', 'enlightenment_bp_filter_template_rtmedia_media_media_single_edit', 12 );

function enlightenment_bp_filter_rtmedia_post_form_uploader( $output ) {
	$max_file_size     = ( wp_max_upload_size() ) / ( 1024 * 1024 ) . 'M';
	$max_file_size_msg = sprintf( __( 'Max. File Size: %s', 'enlightement' ), $max_file_size );

	$output = str_replace( 'class="rtmedia-add-media-button btn btn-secondary"', 'class="rtmedia-add-media-button btn btn-theme-inverse bp-tooltip"', $output );
	$output = str_replace( 'id="rtmedia-add-media-button-post-update"', sprintf( 'id="rtmedia-add-media-button-post-update" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="%s"', $max_file_size_msg ), $output );

	return $output;
}
add_filter( 'enlightenment_bp_activity_post_form', 'enlightenment_bp_filter_rtmedia_post_form_uploader', 12 );
add_filter( 'enlightenment_bp_filter_member_activity_post_form', 'enlightenment_bp_filter_rtmedia_post_form_uploader', 12 );

function enlightenment_theme_bp_filter_rtmedia_activity_content_body( $output ) {
	$offset = strpos( $output, '<span class="activity-read-more"' );
	if ( false !== $offset ) {
        $start  = $offset;
		$end    = strpos( $output, '</span>', $start ) + 7;
		$length = $end - $start;
		$tag    = substr( $output, $start, $length );

        $offset = strpos( $output, '<div class="rtmedia-activity-text">' );
        if ( false !== $offset ) {
    		$output = substr_replace( $output, '', $start, $length );

            $offset = strpos( $output, '</div>', $offset );
            $output = substr_replace( $output,  ' ' . $tag, $offset, 0 );
        }
	}

	return $output;
}
add_filter( 'enlightenment_bp_filter_rtmedia_activity_content_body', 'enlightenment_theme_bp_filter_rtmedia_activity_content_body' );

function enlightenment_bp_filter_rtmedia_activity_privacy( $output ) {
    $offset = strpos( $output, sprintf( '<select id="rtm-ac-privacy-%s"', bp_get_activity_id() ) );
	if ( false !== $offset ) {
        $output = substr_replace( $output, '<div class="rtm-activity-privacy-wrap d-flex align-items-center ms-auto">' . "\n", $offset, 0 );
		$offset = strpos( $output, '</select>', $offset );
        $output = substr_replace( $output, "\n" . '</div>', $offset + 9, 0 );
    }

	return $output;
}
add_filter( 'enlightenment_bp_filter_activity_entry_meta', 'enlightenment_bp_filter_rtmedia_activity_privacy' );

function enlightenment_bp_filter_rtmedia_activity_comment_media_upload( $output ) {
	$output = str_replace( 'class="rtmedia-comment-media-upload btn btn-secondary btn-sm"', 'class="rtmedia-comment-media-upload btn btn-link btn-sm"', $output );
    $output = str_replace( 'class="ac-textarea w-100 mb-2"', 'class="ac-textarea w-100"', $output );

    $offset = strpos( $output, 'class="rtmedia-container rtmedia-uploader-div w-100"' );
	if ( false !== $offset ) {
		$offset = strpos( $output, '</form>', $offset );
        $output = substr_replace( $output, "\n" . '<input type="file" class="d-none" aria-hidden="true" />', $offset + 7, 0 );
    }

    return $output;
}
add_action( 'enlightenment_bp_filter_activity_entry_comments', 'enlightenment_bp_filter_rtmedia_activity_comment_media_upload', 12 );

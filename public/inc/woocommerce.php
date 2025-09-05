<?php
/**
 * WooCommerce Compatibility File
 *
 * @link https://woocommerce.com/
 *
 * @package Enlightenment_Framework
 * @subpackage Enlightenment_Theme
 */

function enlightenment_theme_woocommerce_default_theme_mods( $mods ) {
	return array_merge( $mods, array(
		'hide_shipping_when_free_is_available' => true,
	) );
}
add_filter( 'enlightenment_default_theme_mods', 'enlightenment_theme_woocommerce_default_theme_mods' );

function enlightenment_woocommerce_setup_theme() {
    // Shop Navigation Menus
	register_nav_menu( 'shop', __( 'Shop',   'enlightenment' ) );
}
add_action( 'after_setup_theme', 'enlightenment_woocommerce_setup_theme' );

function enlightenment_woocommerce_theme_taxonomy_meta_fields() {
    if ( ! is_admin() ) {
		return;
	}

    add_action( 'product_cat_add_form_fields',  'enlightenment_theme_taxonomy_form_fields', 12 );
    add_action( 'product_cat_edit_form_fields', 'enlightenment_theme_taxonomy_form_fields', 12 );
}
add_action( 'init', 'enlightenment_woocommerce_theme_taxonomy_meta_fields' );

function enlightenment_woocommerce_styles() {
	wp_enqueue_style( 'enlightenment-woocommerce', get_theme_file_uri( 'assets/css/woocommerce.css' ), array( 'enlightenment-theme-stylesheet', 'select2' ), null );
	wp_enqueue_style( 'enlightenment-woocommerce-blocks', get_theme_file_uri( 'assets/css/woocommerce-blocks.css' ), array( 'enlightenment-core-blocks' ), null );
}
add_action( 'wp_enqueue_scripts', 'enlightenment_woocommerce_styles', 30 );

function enlightenment_woocommerce_filter_main_script_deps( $deps ) {
    if ( is_woocommerce() ) {
        $deps[] = 'select2';
    }

    return $deps;
}
add_filter( 'enlightenment_main_script_deps', 'enlightenment_woocommerce_filter_main_script_deps' );

function enlightenment_woocommerce_filter_header_image( $url ) {
    if ( is_tax( 'product_cat' ) ) {
        $image_id  = get_term_meta( get_queried_object()->term_id, 'thumbnail_id', true );

        if ( empty( $image_id ) ) {
            return $url;
        }

        $image_src = wp_get_attachment_image_src( $image_id, 'full' );

        return $image_src[0];
    }

    return $url;
}
add_filter( 'theme_mod_header_image', 'enlightenment_woocommerce_filter_header_image' );

function enlightenment_theme_woocommerce_default_hooks() {
	remove_action( 'enlightenment_site_header', 'enlightenment_social_nav_menu' );
	remove_action( 'enlightenment_site_header', 'enlightenment_bootstrap_color_mode_switcher' );
	remove_action( 'enlightenment_site_header', 'enlightenment_search_form' );

	add_action( 'enlightenment_site_header', 'enlightenment_woocommerce_user_account' );
	add_action( 'enlightenment_site_header', 'enlightenment_shopping_cart' );
	add_action( 'enlightenment_site_header', 'enlightenment_currency_switcher' );
	add_action( 'enlightenment_site_header', 'enlightenment_bootstrap_color_mode_switcher' );
	add_action( 'enlightenment_site_header', 'enlightenment_search_form' );
}
add_action( 'after_setup_theme', 'enlightenment_theme_woocommerce_default_hooks', 0 );

function enlightenment_theme_shop_layout_hooks() {
    if( ! is_woocommerce() ) {
		return;
	}

    if( is_shop() || is_product_taxonomy() ) {
    	remove_action( 'enlightenment_before_page_content', 'enlightenment_breadcrumbs' );
    } elseif( is_product() ) {
        $layout = enlightenment_current_layout();

		if (
			'full-width' == $layout['tablet-landscape'] ||
			(
				'inherit' == $layout['tablet-landscape'] &&
				(
					'full-width' == $layout['tablet-portrait'] ||
					(
						'full-width' == $layout['smartphone-landscape'] &&
						'inherit'    == $layout['tablet-portrait']
					) ||
					(
						'full-width' == $layout['smartphone-portrait']  &&
						'inherit'    == $layout['smartphone-landscape'] &&
						'inherit'    == $layout['tablet-portrait']
					)
				)
			)
		) {
            remove_action( 'enlightenment_before_page_content', 'enlightenment_breadcrumbs' );
        }
    }

    if ( is_shop() || is_product_category() || is_product_tag() ) {
		add_action( 'enlightenment_before_page_content', 'enlightenment_nav_menu' );
	}
}
add_action( 'wp', 'enlightenment_theme_shop_layout_hooks', 12 );

function enlightenment_theme_shop_entry_hooks() {
    if ( ! is_singular( 'product' ) ) {
		return;
	}

    remove_action( 'enlightenment_after_entry_header', 'woocommerce_show_product_sale_flash' );
    remove_action( 'enlightenment_before_entry_content', 'woocommerce_template_single_meta' );

    $layout = enlightenment_current_layout();

    if (
		'full-width' == $layout['tablet-landscape'] ||
		(
			'inherit' == $layout['tablet-landscape'] &&
			(
				'full-width' == $layout['tablet-portrait'] ||
				(
					'full-width' == $layout['smartphone-landscape'] &&
					'inherit'    == $layout['tablet-portrait']
				) ||
				(
					'full-width' == $layout['smartphone-portrait']  &&
					'inherit'    == $layout['smartphone-landscape'] &&
					'inherit'    == $layout['tablet-portrait']
				)
			)
		)
	) {
		remove_action( 'enlightenment_before_page_content',  'enlightenment_breadcrumbs' );
        remove_action( 'enlightenment_before_entry_content', 'woocommerce_template_single_title' );
        remove_action( 'enlightenment_before_entry_content', 'woocommerce_template_single_rating' );
        remove_action( 'enlightenment_before_entry_content', 'woocommerce_template_single_price' );
		remove_action( 'enlightenment_before_entry_content', 'woocommerce_template_single_excerpt' );
        remove_action( 'enlightenment_before_entry_content', 'woocommerce_template_single_add_to_cart' );
        remove_action( 'enlightenment_before_entry_content', 'woocommerce_template_single_sharing' );

        add_action( 'enlightenment_before_entry_content', 'enlightenment_breadcrumbs' );
        add_action( 'enlightenment_before_entry_content', 'woocommerce_show_product_sale_flash' );
        add_action( 'enlightenment_before_entry_content', 'woocommerce_template_single_title' );
        add_action( 'enlightenment_before_entry_content', 'woocommerce_template_single_rating' );
        add_action( 'enlightenment_before_entry_content', 'woocommerce_template_single_price' );
		add_action( 'enlightenment_before_entry_content', 'woocommerce_template_single_excerpt' );
        add_action( 'enlightenment_before_entry_content', 'woocommerce_template_single_add_to_cart' );
        add_action( 'enlightenment_before_entry_content', 'woocommerce_template_single_sharing' );
    } else {
        remove_action( 'enlightenment_before_entry_content', 'woocommerce_template_single_title' );

        add_action( 'enlightenment_entry_header', 'woocommerce_show_product_sale_flash' );
        add_action( 'enlightenment_entry_header', 'woocommerce_template_single_title' );
    }

    add_action( 'enlightenment_before_entry_content', 'woocommerce_template_single_meta' );
}
add_action( 'enlightenment_before_entry', 'enlightenment_theme_shop_entry_hooks', 8 );

function enlightenment_theme_user_page_entry_hooks() {
    if ( ! is_cart() && ! is_checkout() && ! is_account_page() ) {
		return;
	}

    remove_action( 'enlightenment_entry_header', 'enlightenment_entry_title' );
	remove_action( 'enlightenment_entry_header', 'enlightenment_entry_meta' );

    add_action( 'enlightenment_entry_header', 'enlightenment_breadcrumbs' );
    add_action( 'enlightenment_entry_header', 'enlightenment_entry_title' );
}
add_action( 'enlightenment_before_entry', 'enlightenment_theme_user_page_entry_hooks', 8 );

function enlightenment_theme_woocommerce_entry_hooks( $hooks ) {
	$hooks['enlightenment_entry_header']['functions'][] = 'woocommerce_show_product_sale_flash';
	$hooks['enlightenment_entry_header']['functions'][] = 'woocommerce_template_single_title';
	$hooks['enlightenment_entry_header']['functions'][] = 'enlightenment_breadcrumbs';

	$hooks['enlightenment_before_entry_content']['functions'][] = 'woocommerce_show_product_sale_flash';

	return $hooks;
}
add_filter( 'enlightenment_entry_hooks', 'enlightenment_theme_woocommerce_entry_hooks' );

function enlightenment_woocommerce_filter_archive_grids( $grids ) {
	$grids['product']['grid']['smartphone-landscape'] = 'twocol';
	$grids['product']['grid']['tablet-portrait']      = 'threecol';

	$grids['product_cat']['grid']['smartphone-landscape'] = 'twocol';
	$grids['product_cat']['grid']['tablet-portrait']      = 'threecol';

	$grids['product_tag']['grid']['smartphone-landscape'] = 'twocol';
	$grids['product_tag']['grid']['tablet-portrait']      = 'threecol';

	return $grids;
}
add_filter( 'enlightenment_archive_grids', 'enlightenment_woocommerce_filter_archive_grids' );

function enlightenment_woocommerce_remove_layout_filter() {
    if ( is_woocommerce() ) {
        remove_filter( 'enlightenment_current_layout', 'enlightenment_filter_current_layout' );
    }
}
add_action( 'wp', 'enlightenment_woocommerce_remove_layout_filter' );

function enlightenment_woocommerce_filter_current_layout( $layout ) {
	if ( is_singular() ) {
		return $layout;
	}

	$grids = enlightenment_current_grid();

	foreach ( $grids as $breakpoint => $grid ) {
		if( 'inherit' == $grid ) {
			continue;
		}

		$atts = enlightenment_get_grid( $grid );

		if ( 3 < $atts['content_columns'] ) {
			$layout[ $breakpoint ] = 'full-width';
		}
	}

	return $layout;
}
add_filter( 'enlightenment_current_layout', 'enlightenment_woocommerce_filter_current_layout' );

function enlightenment_woocommerce_filter_bootstrap_custom_layouts( $layouts ) {
    if ( is_singular( 'product' ) ) {
        foreach ( $layouts as $layout => $atts ) {
            $atts['content_class'] = str_replace( 'col%s-8',   'col%s-9',   $atts['content_class'] );
            $atts['sidebar_class'] = str_replace( 'col%1$s-4', 'col%1$s-3', $atts['sidebar_class'] );
            $atts['content_class'] = str_replace( 'col%1$s-8', 'col%1$s-9', $atts['content_class'] );
            $atts['sidebar_class'] = str_replace( 'col%1$s-4', 'col%1$s-3', $atts['sidebar_class'] );

            $layouts[ $layout ]['content_class'] = $atts['content_class'];
            $layouts[ $layout ]['sidebar_class'] = $atts['sidebar_class'];
        }
    } else {
        $grids = array_reverse( enlightenment_current_grid() );

        foreach ( $grids as $breakpoint => $grid ) {
            if( 'inherit' == $grid ) {
    			continue;
    		}

            $atts = enlightenment_get_grid( $grid );

    		if ( 3 == $atts['content_columns'] ) {
                foreach ( $layouts as $layout => $atts ) {
                    $atts['content_class'] = str_replace( 'col%s-8',   'col%s-9',   $atts['content_class'] );
                    $atts['sidebar_class'] = str_replace( 'col%1$s-4', 'col%1$s-3', $atts['sidebar_class'] );
                    $atts['content_class'] = str_replace( 'col%1$s-8', 'col%1$s-9', $atts['content_class'] );
                    $atts['sidebar_class'] = str_replace( 'col%1$s-4', 'col%1$s-3', $atts['sidebar_class'] );

                    $layouts[ $layout ]['content_class'] = $atts['content_class'];
                    $layouts[ $layout ]['sidebar_class'] = $atts['sidebar_class'];
                }

                break;
    		}
        }
    }

    return $layouts;
}
add_filter( 'enlightenment_custom_layouts', 'enlightenment_woocommerce_filter_bootstrap_custom_layouts', 12 );

function enlightenment_woocommerce_filter_wp_nav_menu_args( $args ) {
	if ( ! is_woocommerce() ) {
		return $args;
	}

	if ( doing_action( 'enlightenment_before_page_content' ) ) {
		$args['theme_location'] = 'shop';
	}

	return $args;
}
add_filter( 'enlightenment_wp_nav_menu_args', 'enlightenment_woocommerce_filter_wp_nav_menu_args', 12 );

function enlightenment_woocommerce_filter_user_account_args( $args ) {
	$args['toggle_extra_atts']['data-bs-display'] = 'static';

	if ( enlightenment_is_navbar_inversed() ) {
		$args['dropdown_menu_extra_atts']['data-bs-theme'] = 'light';
	}

	return $args;
}
add_filter( 'enlightenment_woocommerce_user_account_args', 'enlightenment_woocommerce_filter_user_account_args' );

function enlightenment_filter_shopping_cart_args( $args ) {
	$dropdown = ! apply_filters( 'woocommerce_widget_cart_is_hidden', is_cart() || is_checkout() );

	$args['count_class'] = str_replace( ' badge text-bg-light text-dark', '', $args['count_class'] );

	$args['link_extra_atts']['data-bs-display'] = 'static';

	if ( $dropdown && enlightenment_is_navbar_inversed() ) {
		$args['cart_contents_extra_atts']['data-bs-theme'] = 'light';
	}

	return $args;
}
add_filter( 'enlightenment_shopping_cart_args', 'enlightenment_filter_shopping_cart_args', 12 );

function enlightenment_filter_currency_switcher_args( $args ) {
	$args['toggle_extra_atts']['data-bs-display'] = 'static';
	$args['toggle_label_format']                  = '%2$s';
	$args['dropdown_button_label_format']         = '%1$s %2$s %4$s';

	if ( enlightenment_is_navbar_inversed() ) {
		$args['dropdown_menu_extra_atts']['data-bs-theme'] = 'light';
	}

	return $args;
}
add_filter( 'enlightenment_woopayments_currency_switcher_args', 'enlightenment_filter_currency_switcher_args' );

function enlightenment_woocommerce_filter_template_single_product_add_to_cart_output( $output ) {
    return str_replace( ' btn btn-primary btn-lg', ' btn btn-danger btn-lg', $output );
}
add_filter( 'enlightenment_woocommerce_filter_template_single_product_add_to_cart_simple_output', 'enlightenment_woocommerce_filter_template_single_product_add_to_cart_output', 12 );
add_filter( 'enlightenment_woocommerce_filter_template_single_product_add_to_cart_variable_output', 'enlightenment_woocommerce_filter_template_single_product_add_to_cart_output', 12 );
add_filter( 'enlightenment_woocommerce_filter_template_single_product_add_to_cart_grouped_output', 'enlightenment_woocommerce_filter_template_single_product_add_to_cart_output', 12 );
add_filter( 'enlightenment_woocommerce_filter_template_single_product_add_to_cart_external_output', 'enlightenment_woocommerce_filter_template_single_product_add_to_cart_output', 12 );
add_filter( 'enlightenment_woocommerce_filter_template_single_product_add_to_cart_subscription_output', 'enlightenment_woocommerce_filter_template_single_product_add_to_cart_output', 12 );
add_filter( 'enlightenment_woocommerce_filter_template_single_product_add_to_cart_variable_subscription_output', 'enlightenment_woocommerce_filter_template_single_product_add_to_cart_output', 12 );
add_filter( 'enlightenment_woocommerce_filter_template_single_product_add_to_cart_booking_output', 'enlightenment_woocommerce_filter_template_single_product_add_to_cart_output', 12 );
add_filter( 'enlightenment_woocommerce_filter_template_single_product_add_to_cart_bundle_button_output', 'enlightenment_woocommerce_filter_template_single_product_add_to_cart_output', 12 );

function enlightenment_woocommerce_filter_reset_variations_link( $output ) {
	$offset = str_replace( 'class="reset_variations btn btn-link"', 'class="reset_variations btn btn-link text-nowrap"', $output );

	$offset = strpos( $output, '<a ' );
	if ( false !== $offset ) {
		$offset = strpos( $output, '>', $offset );
		$output = substr_replace( $output, '<i class="fas fa-sync" aria-hidden="true" role="presentation"></i> <span class="d-none d-sm-inline">', $offset + 1, 0 );
		$offset = strpos( $output, '</a>', $offset );
		$output = substr_replace( $output, '</span>', $offset, 0 );
	}

	return $output;
}
add_filter( 'woocommerce_reset_variations_link', 'enlightenment_woocommerce_filter_reset_variations_link', 12 );

function enlightenment_woocommerce_filter_template_product_searchform_output( $output ) {
	$output = str_replace( 'class="btn btn-light ', 'class="btn btn-theme-inverse ', $output );
    $output = str_replace( 'class="btn btn-light"', 'class="btn btn-theme-inverse"', $output );

	$offset = strpos( $output, '<button type="submit"' );
	if ( false !== $offset ) {
		$offset = strpos( $output, '>', $offset );
		$output = substr_replace( $output, '<i class="fas fa-search" aria-hidden="true" role="presentation"></i> <span class="screen-reader-text visually-hidden">', $offset + 1, 0 );
		$offset = strpos( $output, '</button>', $offset );
		$output = substr_replace( $output, '</span>', $offset, 0 );
	}

    return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_product_searchform_output', 'enlightenment_woocommerce_filter_template_product_searchform_output', 12 );

function enlightenment_woocommerce_filter_template_content_widget_price_filter_output( $output ) {
	return str_replace( 'class="button btn btn-secondary ', 'class="button btn btn-outline-secondary ', $output );
}
add_filter( 'enlightenment_woocommerce_filter_template_content_widget_price_filter_output', 'enlightenment_woocommerce_filter_template_content_widget_price_filter_output', 12 );

function enlightenment_woocommerce_bootstrap_template_addons_image_output( $output ) {
	$offset = strpos( $output, '<p class="form-row ' );
	if ( false !== $offset ) {
		$offset = strpos( $output, '>', $offset );
		$output = substr_replace( $output, '<span class="wc-pao-addon-image-swatch-wrap">', $offset + 1, 0 );
		$offset = strpos( $output, '<select ', $offset );
		$output = substr_replace( $output, '</span>', $offset, 0 );
	}

	return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_addons_image_output', 'enlightenment_woocommerce_bootstrap_template_addons_image_output' );

function enlightenment_filter_woocommerce_pao_reset_date_link( $output ) {
	$offset = strpos( $output, '<a ' );
	if ( false !== $offset ) {
		$offset = strpos( $output, '>', $offset );
		$output = substr_replace( $output, '<span class="screen-reader-text visually-hidden">', $offset + 1, 0 );
		$offset = strpos( $output, '</a>', $offset );
		$output = substr_replace( $output, '</span><i class="fas fa-times" aria-hidden="true"></i>', $offset, 0 );
	}

	return $output;
}
add_filter( 'woocommerce_pao_reset_date_link', 'enlightenment_filter_woocommerce_pao_reset_date_link' );

function enlightenment_woocommerce_filter_template_add_to_wishlist_link_output( $output ) {
    global $add_to_wishlist_args;

    if ( in_array( 'button', $add_to_wishlist_args['btn_class'] ) ) {
        $class  = join( ' ', $add_to_wishlist_args['btn_class'] );
        $output = str_replace( sprintf( 'class="%s btn btn-secondary"', $class ), sprintf( 'class="%s btn btn-outline-secondary"', $class ), $output );
    }

    $output = str_replace( 'class="woocommerce wl-button-wrap wl-row wl-clear ', 'class="woocommerce wl-button-wrap wl-row wl-clear order-last p-0 mt-3 ', $output );

    return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_add_to_wishlist_link_output', 'enlightenment_woocommerce_filter_template_add_to_wishlist_link_output', 12 );

function enlightenment_woocommerce_filter_template_add_to_wishlist_modal_output( $output ) {
    return str_replace( 'class="wl-add-to-single button btn btn-secondary"', 'class="wl-add-to-single button btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_woocommerce_filter_template_add_to_wishlist_modal_output', 'enlightenment_woocommerce_filter_template_add_to_wishlist_modal_output', 12 );

function enlightenment_filter_bootstrap_cart_item_name( $output ) {
	return str_replace( 'class="edit_bundle_in_cart_text edit_in_cart_text btn btn-secondary btn-sm"', 'class="edit_bundle_in_cart_text edit_in_cart_text btn btn-outline-secondary btn-sm"', $output );
}
add_filter( 'woocommerce_cart_item_name', 'enlightenment_filter_bootstrap_cart_item_name', 14 );

function enlightenment_wcs_cart_totals_shipping_method_price_label( $label ) {
    return sprintf( '<span class="recurring-price-wrap">%s</span>', $label );
}
add_filter( 'wcs_cart_totals_shipping_method_price_label', 'enlightenment_wcs_cart_totals_shipping_method_price_label' );

function enlightenment_woocommerce_filter_template_cart_proceed_to_checkout_button_output( $output ) {
    return str_replace( 'class="checkout-button btn btn-primary ', 'class="checkout-button btn btn-danger ', $output );
}
add_action( 'enlightenment_woocommerce_filter_template_cart_proceed_to_checkout_button_output', 'enlightenment_woocommerce_filter_template_cart_proceed_to_checkout_button_output', 12 );

function enlightenment_woocommerce_filter_template_paypal_ec_button_output( $output ) {
    if ( is_cart() ) {
        $output .= sprintf( '<div class="paypal-button-separator">&mdash; %s &mdash;</div>', __( 'or', 'enlightenment' ) );
    }

    return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_paypal_ec_button_output', 'enlightenment_woocommerce_filter_template_paypal_ec_button_output' );

function enlightenment_woocommerce_filter_template_checkout_payment_output( $output ) {
	$output = str_replace( 'class="button alt btn btn-secondary ', 'class="button alt btn btn-outline-secondary ', $output );
	$output = str_replace( 'class="button alt btn btn-secondary"', 'class="button alt btn btn-outline-secondary"', $output );

    return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_checkout_payment_output', 'enlightenment_woocommerce_filter_template_checkout_payment_output', 12 );

function enlightenment_woocommerce_filter_template_checkout_payment_method_output( $output, $template_name, $template_path, $located, $args ) {
    if ( isset( $args['gateway'] ) && $offset = strpos( $output, sprintf( '<label class="form-check-label" for="payment_method_%s">', esc_attr( $args['gateway']->id ) ) ) ) {
        $offset = strpos( $output, $args['gateway']->get_title(), $offset );
        $output = substr_replace( $output, '<span class="payment-method-title">', $offset, 0 );
        $output = substr_replace( $output, '</span>', $offset + 35 + strlen( $args['gateway']->get_title() ), 0 );
    }

    $output = str_replace( 'class="description">- ', 'class="description">', $output );
    $output = str_replace( 'class="button sv-wc-payment-gateway-payment-form-manage-payment-methods btn btn-secondary d-block w-100"', 'class="button sv-wc-payment-gateway-payment-form-manage-payment-methods btn btn-outline-secondary d-block w-100"', $output );

	switch( $args['gateway']->id ) {
		case 'ppcp-credit-card-gateway':
			$output = str_replace( '&nbsp;<span class="required">', '<span class="required">', $output );

			break;

		case 'square_credit_card':
		    /**
		     * Square Gateway
		    **/
		    if ( $offset = strpos( $output, '<div id="wc-square-credit-card-account-number-hosted"' ) ) {
		        $offset = strpos( $output, '</div>', $offset );
		        $output = substr_replace( $output, '<i class="fab"></i><span class="form-control-ghost"></span>', $offset + 6, 0 );
		    }

		    if ( $offset = strpos( $output, '<div id="wc-square-credit-card-expiry-hosted"' ) ) {
		        $offset = strpos( $output, '</div>', $offset );
		        $output = substr_replace( $output, '<span class="form-control-ghost"></span>', $offset + 6, 0 );
		    }

		    if ( $offset = strpos( $output, '<div id="wc-square-credit-card-csc-hosted"' ) ) {
		        $offset = strpos( $output, '</div>', $offset );
		        $output = substr_replace( $output, '<span class="form-control-ghost"></span>', $offset + 6, 0 );
		    }

		    if ( $offset = strpos( $output, '<div id="wc-square-credit-card-postal-code-hosted"' ) ) {
		        $offset = strpos( $output, '</div>', $offset );
		        $output = substr_replace( $output, '<span class="form-control-ghost"></span>', $offset + 6, 0 );
		    }

			break;
	}

    return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_checkout_payment_method_output', 'enlightenment_woocommerce_filter_template_checkout_payment_method_output', 12, 5 );

add_action( apply_filters( 'woocommerce_paypal_payments_checkout_dcc_renderer_hook', 'woocommerce_review_order_after_submit' ), 'enlightenment_ob_start', 7 );
add_action( apply_filters( 'woocommerce_paypal_payments_checkout_dcc_renderer_hook', 'woocommerce_pay_order_after_submit' ), 'enlightenment_ob_start', 7 );

function enlightenment_woocommerce_filter_dcc_renderer() {
	$output = ob_get_clean();
	$output = str_replace( 'class="button alt btn btn-primary ', 'class="button alt btn btn-danger ', $output );

	echo $output;
}
add_action( apply_filters( 'woocommerce_paypal_payments_checkout_dcc_renderer_hook', 'woocommerce_review_order_after_submit' ), 'enlightenment_woocommerce_filter_dcc_renderer', 15 );
add_action( apply_filters( 'woocommerce_paypal_payments_checkout_dcc_renderer_hook', 'woocommerce_pay_order_after_submit' ), 'enlightenment_woocommerce_filter_dcc_renderer', 15 );

function enlightenment_woocommerce_filter_order_shipping_to_display_shipped_via( $output ) {
	return str_replace( '&nbsp;<small class="shipped_via">', ' <small class="shipped_via">', $output );
}
add_filter( 'woocommerce_order_shipping_to_display_shipped_via', 'enlightenment_woocommerce_filter_order_shipping_to_display_shipped_via' );

function enlightenment_woocommerce_filter_order_button( $output ) {
    return str_replace( 'class="button btn btn-primary ', 'class="button btn btn-danger ', $output );
}
add_filter( 'woocommerce_order_button_html', 'enlightenment_woocommerce_filter_order_button', 12 );

function enlightenment_woocommerce_filter_template_checkout_order_receipt_output( $output ) {
    return str_replace( 'class="button-alt btn btn-primary btn-lg" id="submit_payfast_payment_form"', 'class="button-alt btn btn-danger btn-lg" id="submit_payfast_payment_form"', $output );
}
add_filter( 'enlightenment_woocommerce_filter_template_checkout_order_receipt_output', 'enlightenment_woocommerce_filter_template_checkout_order_receipt_output', 12 );

function enlightenment_woocommerce_filter_template_checkout_thankyou_output( $output ) {
	$output = str_replace( 'class="button pay btn btn-primary"', 'class="button pay btn btn-primary btn-lg"', $output );
	$output = str_replace( 'class="button pay btn btn-secondary"', 'class="button pay btn btn-outline-secondary btn-lg"', $output );

	return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_checkout_thankyou_output', 'enlightenment_woocommerce_filter_template_checkout_thankyou_output', 12 );

function enlightenment_woocommerce_filter_template_myaccount_my_subscriptions_output( $output ) {
    return str_replace( 'class="woocommerce-button button btn btn-secondary ', 'class="woocommerce-button button btn btn-outline-secondary ', $output );
}
add_filter( 'enlightenment_woocommerce_filter_template_myaccount_my_subscriptions_output', 'enlightenment_woocommerce_filter_template_myaccount_my_subscriptions_output', 12 );

function enlightenment_woocommerce_filter_template_myaccount_related_subscriptions_output( $output ) {
    return str_replace( 'class="woocommerce-button button view btn btn-secondary"', 'class="woocommerce-button button view btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_woocommerce_filter_template_myaccount_related_subscriptions_output', 'enlightenment_woocommerce_filter_template_myaccount_related_subscriptions_output', 12 );

function enlightenment_woocommerce_filter_template_myaccount_subscription_details_output( $output ) {
    $output = str_replace( ' btn btn-secondary ', ' btn btn-outline-secondary ', $output );

    $start  = strpos( $output, '<div class="btn-group">' );
    $end    = strpos( $output, '</div>', $start );
    $first  = strpos( $output, '<a', $start );
	if ( false !== $first && $first < $end ) {
	    $second = strpos( $output, '<a', $first + 1 );

	    if ( false !== $second && $second < $end ) {
			$output = substr_replace( $output, ' dropdown', $start + 21, 0 );
	        $offset = strpos( $output, '</a>', $start );
			$output = substr_replace( $output, sprintf( '<button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static" aria-haspopup="true" aria-expanded="false" aria-label="%s"></button><div class="dropdown-menu dropdown-menu-end">', esc_attr( __( 'Toggle more actions', 'enlightenment' ) ) ), $offset + 4, 0 );
	        $offset = strpos( $output, '<div class="dropdown-menu dropdown-menu-end">', $offset );

	        $start  = strpos( $output, '<a', $offset );
	        $end    = strpos( $output, '</div>', $start );
	        $offset = $start;

	        while ( $offset = strpos( $output, ' btn btn-outline-secondary ', $offset ) ) {
	            if ( $offset > $end ) {
	                break;
	            }

	            $output = substr_replace( $output, 'dropdown-item', $offset + 1, 25 );
	            $end    = strpos( $output, '</div>', $start );
	            $offset++;
	        }

	        $output = substr_replace( $output, '</div>', $end, 6 );
	    }
	}

    return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_myaccount_subscription_details_output', 'enlightenment_woocommerce_filter_template_myaccount_subscription_details_output', 12 );

function enlightenment_woocommerce_filter_memberships_buttons( $output ) {
	return str_replace( 'class="button btn btn-secondary ', 'class="button btn btn-outline-secondary ', $output );
}
add_filter( 'enlightenment_woocommerce_filter_template_myaccount_my_memberships_output', 'enlightenment_woocommerce_filter_memberships_buttons', 12 );
add_filter( 'enlightenment_woocommerce_filter_template_myaccount_my_membership_content_output', 'enlightenment_woocommerce_filter_memberships_buttons', 12 );
add_filter( 'enlightenment_woocommerce_filter_template_myaccount_my_membership_products_output', 'enlightenment_woocommerce_filter_memberships_buttons', 12 );
add_filter( 'enlightenment_woocommerce_filter_template_myaccount_my_membership_discounts_output', 'enlightenment_woocommerce_filter_memberships_buttons', 12 );
add_filter( 'enlightenment_woocommerce_filter_template_myaccount_my_membership_details_output', 'enlightenment_woocommerce_filter_memberships_buttons', 12 );

function enlightenment_woocommerce_filter_template_myaccount_my_membership_products_output( $output ) {
	return str_replace( ' style="min-width: 84px;"', '', $output );
}
add_filter( 'enlightenment_woocommerce_filter_template_myaccount_my_membership_products_output', 'enlightenment_woocommerce_filter_template_myaccount_my_membership_products_output' );

function enlightenment_woocommerce_filter_wc_memberships_members_area_sorting_link( $output ) {
	$output = str_replace( '&nbsp;',   '', $output );
	$output = str_replace( '&#x25B4;', '', $output );
	$output = str_replace( '&#x25BE;', '', $output );
	$output = str_replace( '>  <', '><', $output );

	$offset = strpos( $output, '<span class="sort-order-icon ' );
	while ( false !== $offset ) {
        $output = substr_replace( $output, 'i', $offset + 1, 4 );
        $offset = strpos( $output, '</span>', $offset );
        $output = substr_replace( $output, 'i', $offset + 2, 4 );

		$offset = strpos( $output, '<span class="sort-order-icon ', $offset );
	}

	$offset = strpos( $output, '<span class="sort-status ' );
	while ( false !== $offset ) {
		$offset = strpos( $output, '<i ', $offset );
        $output = substr_replace( $output, '<span class="sort-order-icons">', $offset, 0 );
        $offset = strpos( $output, '</span>', $offset );
        $output = substr_replace( $output, '</span>', $offset, 0 );

		$offset = strpos( $output, '<span class="sort-status ', $offset );
	}

	return $output;
}
add_filter( 'wc_memberships_members_area_sorting_link', 'enlightenment_woocommerce_filter_wc_memberships_members_area_sorting_link' );

function enlightenment_woocommerce_filter_bootstrap_memberships_note_modal( $output ) {
	return str_replace( 'class="btn btn-secondary"', 'class="btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_woocommerce_bootstrap_memberships_note_modal', 'enlightenment_woocommerce_filter_bootstrap_memberships_note_modal' );

function enlightenment_woocommerce_filter_template_myaccount_related_orders_output( $output ) {
    return str_replace( 'class="woocommerce-button button btn btn-secondary ', 'class="woocommerce-button button btn btn-outline-secondary ', $output );
}
add_filter( 'enlightenment_woocommerce_filter_template_myaccount_related_orders_output', 'enlightenment_woocommerce_filter_template_myaccount_related_orders_output', 12 );

function enlightenment_woocommerce_filter_template_checkout_form_change_payment_method_output( $output ) {
    $output = str_replace( 'class="button alt btn btn-primary ', 'class="button alt btn btn-danger ', $output );

    return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_checkout_form_change_payment_method_output', 'enlightenment_woocommerce_filter_template_checkout_form_change_payment_method_output', 12 );

function enlightenment_woocommerce_filter_template_checkout_terms_output( $output ) {
    $offset_a = strpos( $output, '<span class="woocommerce-terms-and-conditions-checkbox-text">' );
	$offset_b = strpos( $output, '<span class="required">' );
	if ( false !== $offset_a && false !== $offset_b ) {
		$offset = $offset_a;
		$output = substr_replace( $output, '<span class="woocommerce-terms-and-conditions-checkbox-wrap">', $offset, 0 );
		$offset = strpos( $output, '<span class="required">', $offset );
		$offset = strpos( $output, '</span>', $offset );
		$output = substr_replace( $output, '</span>', $offset + 7, 0 );
	}

    return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_checkout_terms_output', 'enlightenment_woocommerce_filter_template_checkout_terms_output', 12 );

function enlightenment_woocommerce_filter_template_myaccount_html_modal_output( $output ) {
    return str_replace( 'class="button alt btn btn-primary ', 'class="button alt btn btn-danger ', $output );
}
add_filter( 'enlightenment_woocommerce_filter_template_html_modal_output', 'enlightenment_woocommerce_filter_template_myaccount_html_modal_output', 12 );

function enlightenment_woocommerce_filter_template_myaccount_form_add_payment_method_output( $output ) {
	return str_replace( 'class="woocommerce-Button woocommerce-Button--alt button alt btn btn-primary ', 'class="woocommerce-Button woocommerce-Button--alt button alt btn btn-danger ', $output );
}
add_filter( 'enlightenment_woocommerce_filter_template_myaccount_form_add_payment_method_output', 'enlightenment_woocommerce_filter_template_myaccount_form_add_payment_method_output', 12 );

function enlightenment_woocommerce_filter_template_find_a_list_output( $output ) {
    $output = str_replace( 'class="button btn btn-secondary"', 'class="button btn btn-outline-secondary"', $output );
    $output = str_replace( '<hr/>', '', $output );

    return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_find_a_list_output', 'enlightenment_woocommerce_filter_template_find_a_list_output', 12 );

function enlightenment_woocommerce_filter_template_my_lists_output( $output ) {
    $output = str_replace( 'class="button alt btn btn-secondary btn-lg ', 'class="button alt btn btn-outline-secondary btn-lg ', $output );
    $output = str_replace( '<div class="row-actions">', '<div class="row-actions order-1 w-100 mt-2 mb-0">', $output );
    $output = str_replace( '<a class="btn btn-secondary ', '<a class="btn btn-outline-secondary ', $output );

    return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_my_lists_output', 'enlightenment_woocommerce_filter_template_my_lists_output', 12 );

function enlightenment_woocommerce_filter_template_edit_my_list_output( $output ) {
	$output = str_replace( '>&times;<', '><i class="fas fa-times" aria-hidden="true" role="presentation"></i><', $output );
    $output = str_replace( 'class="wl-row d-flex mb-3"', 'class="wl-row d-flex mb-4"', $output );
    $output = str_replace( 'class="wl-tab-wrap woocommerce-tabs"', 'class="wl-tab-wrap woocommerce-tabs mb-0"', $output );
    $output = str_replace( 'class="wl-row d-flex mt-3 mb-0"', 'class="wl-row d-flex mt-4 mb-0"', $output );
    $output = str_replace( 'class="wlconfirm btn btn-secondary"', 'class="wlconfirm btn btn-outline-secondary"', $output );
    $output = str_replace( 'class="btn btn-secondary"', 'class="btn btn-outline-secondary"', $output );
    $output = str_replace( 'class="wishlist-add-to-cart-button button alt btn btn-secondary"', 'class="wishlist-add-to-cart-button button alt btn btn-outline-secondary"', $output );
    $output = str_replace( 'class="button small wl-but wl-add-to btn-apply btn btn-secondary"', 'class="button small wl-but wl-add-to btn-apply btn btn-outline-secondary"', $output );

    $offset = 0;
    while ( $offset = strpos( $output, '<div class="quantity">', $offset ) ) {
		$value  = 1;
		$min    = 0;
		$max    = false;

		$start  = strpos( $output, '<input', $offset );
		$end    = strpos( $output, '</div>', $offset );
		if ( false !== $start && $start < $end ) {
			$end = strpos( $output, '/>', $start );

			$start_a = strpos( $output, 'value="', $start );
			if ( false !== $start_a && $start_a < $end ) {
				$start_a += 7;
				$end_a    = strpos( $output, '"', $start_a );
				$length   = $end_a - $start_a;
				$value    = substr( $output, $start_a, $length );
			}

			$start_a = strpos( $output, 'min="', $start );
			if ( false !== $start_a && $start_a < $end ) {
				$start_a += 5;
				$end_a    = strpos( $output, '"', $start_a );
				$length   = $end_a - $start_a;
				$min      = substr( $output, $start_a, $length );
			}

			$start_a = strpos( $output, 'max="', $start );
			if ( false !== $start_a && $start_a < $end ) {
				$start_a += 5;
				$end_a    = strpos( $output, '"', $start_a );
				$length   = $end_a - $start_a;
				$max      = substr( $output, $start_a, $length );
			}
		}

        $output = substr_replace( $output, sprintf(
			'<button class="manipulate-quantity decrease-quantity"%s><i class="fas fa-minus"></i> <span class="screen-reader-text visually-hidden">%s</span></button>',
			$value <= $min ? ' disabled="disabled"' : '',
			__( 'Decrease quantity', 'enlightenment' )
		), $offset + 22, 0 );
        $offset = strpos( $output, '/>', $offset );
        $output = substr_replace( $output, sprintf(
			'<button class="manipulate-quantity increase-quantity"%s><i class="fas fa-plus"></i> <span class="screen-reader-text visually-hidden">%s</span></button>',
			false !== $max && $value >= $max ? ' disabled="disabled"' : '',
			__( 'Increase quantity', 'enlightenment' )
		), $offset, 0 );
    }

    return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_edit_my_list_output', 'enlightenment_woocommerce_filter_template_edit_my_list_output', 12 );

function enlightenment_woocommerce_filter_template_view_a_list_output( $output ) {
    $output = str_replace( 'class="wl-row d-flex justify-content-end mb-3"', 'class="wl-row d-flex justify-content-end mb-4"', $output );
    $output = str_replace( 'class="button small wl-but btn btn-secondary"', 'class="button small wl-but btn btn-outline-secondary"', $output );
    $output = str_replace( 'class="wishlist-add-to-cart-button-view button btn btn-secondary"', 'class="wishlist-add-to-cart-button-view button btn btn-outline-secondary"', $output );
	$output = str_replace( 'class="button wishlist-add-to-cart-button btn btn-secondary"', 'class="button wishlist-add-to-cart-button btn btn-outline-secondary"', $output );
    $output = str_replace( 'class="button btn btn-secondary"', 'class="button btn btn-outline-secondary"', $output );

	$offset = strpos( $output, '<div class="quantity">' );
	while ( false !== $offset ) {
		$value  = 1;
		$min    = 0;
		$max    = false;

		$start  = strpos( $output, '<input', $offset );
		$end    = strpos( $output, '</div>', $offset );
		if ( false !== $start && $start < $end ) {
			$end = strpos( $output, '/>', $start );

			$start_a = strpos( $output, 'value="', $start );
			if ( false !== $start_a && $start_a < $end ) {
				$start_a += 7;
				$end_a    = strpos( $output, '"', $start_a );
				$length   = $end_a - $start_a;
				$value    = substr( $output, $start_a, $length );
			}

			$start_a = strpos( $output, 'min="', $start );
			if ( false !== $start_a && $start_a < $end ) {
				$start_a += 5;
				$end_a    = strpos( $output, '"', $start_a );
				$length   = $end_a - $start_a;
				$min      = substr( $output, $start_a, $length );
			}

			$start_a = strpos( $output, 'max="', $start );
			if ( false !== $start_a && $start_a < $end ) {
				$start_a += 5;
				$end_a    = strpos( $output, '"', $start_a );
				$length   = $end_a - $start_a;
				$max      = substr( $output, $start_a, $length );
			}
		}

        $output = substr_replace( $output, sprintf(
			'<button class="manipulate-quantity decrease-quantity"%s><i class="fas fa-minus"></i> <span class="screen-reader-text visually-hidden">%s</span></button>',
			$value <= $min ? ' disabled="disabled"' : '',
			__( 'Decrease quantity', 'enlightenment' )
		), $offset + 22, 0 );
		$offset = strpos( $output, '</div>', $offset );
		$output = substr_replace( $output, sprintf(
			'<button class="manipulate-quantity increase-quantity"%s><i class="fas fa-plus"></i> <span class="screen-reader-text visually-hidden">%s</span></button>',
			false !== $max && $value >= $max ? ' disabled="disabled"' : '',
			__( 'Increase quantity', 'enlightenment' )
		), $offset, 0 );

		$offset = strpos( $output, '<div class="quantity">', $offset );
	}

    return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_view_a_list_output', 'enlightenment_woocommerce_filter_template_view_a_list_output', 12 );

function enlightenment_woocommerce_filter_wp_list_categories( $output, $args ) {
    if ( empty( $args['walker'] ) || ! ( $args['walker'] instanceof WC_Product_Cat_List_Walker ) ) {
        return $output;
    }

    $output = str_replace( ' <span class="count">(', ' <span class="count">', $output );
    $output = str_replace( ')</span>',               '</span>',               $output );

    return $output;
}
add_filter( 'wp_list_categories', 'enlightenment_woocommerce_filter_wp_list_categories', 10, 2 );

function enlightenment_woocommerce_filter_layered_nav_count( $output ) {
    $output = str_replace( '<span class="count">(', '<span class="count">', $output );
    $output = str_replace( ')</span>',              '</span>',              $output );

    return $output;
}
add_filter( 'woocommerce_layered_nav_count', 'enlightenment_woocommerce_filter_layered_nav_count' );

function enlightenment_woocommerce_filter_rating_filter_count( $output, $count ) {
    // return $output;
    return sprintf( '<em class="count">%s</em>', $count );
}
add_filter( 'woocommerce_rating_filter_count', 'enlightenment_woocommerce_filter_rating_filter_count', 10, 2 );

function enlightenment_woocommerce_before_template_part( $template_name ) {
    if ( 'cart/cart-item-data.php' != $template_name ) {
        return;
    }

    echo '<div class="dropdown variations-dropdown"><a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a><ul class="dropdown-menu"><li class="dropdown-item">';
}
// add_action( 'woocommerce_before_template_part', 'enlightenment_woocommerce_before_template_part' );

function enlightenment_woocommerce_after_template_part( $template_name ) {
    if ( 'cart/cart-item-data.php' != $template_name ) {
        return;
    }

    echo '</li></ul></div>';
}
// add_action( 'woocommerce_after_template_part', 'enlightenment_woocommerce_after_template_part' );

function enlightenment_woocommerce_filter_template_cart_cart_item_data_output( $output ) {
	$output = str_replace( '<dl class="variation">', '<ul class="variation dropdown-menu dropdown-menu-end">', $output );
	$output = str_replace( '</dl>', '</ul>', $output );

	$offset = strpos( $output, '<dt ' );
	while ( false !== $offset ) {
		$output = substr_replace( $output, '<li class="dropdown-item"><strong ', $offset, 4 );
		$offset = strpos( $output, '</dt>', $offset );
		$output = substr_replace( $output, '</strong>', $offset, 5 );
		$offset = strpos( $output, '<dd ', $offset );
		$output = substr_replace( $output, '<span ', $offset, 4 );
		$offset = strpos( $output, '</dd>', $offset );
		$output = substr_replace( $output, '</span></li>', $offset, 5 );

		$offset = strpos( $output, '<dt ', $offset );
	}

	$output = sprintf( '<div class="dropdown variations-dropdown"><a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a>%s</div>', $output );

	return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_cart_cart_item_data_output', 'enlightenment_woocommerce_filter_template_cart_cart_item_data_output' );

function enlightenment_theme_filter_woocommerce_sale_flash( $output ) {
	if (
		doing_action( 'wp_ajax_elementor_ajax' )
		||
		doing_action( 'admin_action_elementor' )
		||
		doing_filter( 'the_content' )
		||
		enlightenment_has_in_call_stack( array(
			array(
				'key'     => 'class',
				'value'   => 'Elementor',
				'compare' => 'STARTS_WITH',
			),
		) )
	) {
		$output = sprintf( '<div class="sale-flash-wrap">%s</div>', $output );
	}

	return $output;
}
add_filter( 'woocommerce_sale_flash', 'enlightenment_theme_filter_woocommerce_sale_flash' );

function enlightenment_theme_filter_product_get_image( $output ) {
	if (
		doing_action( 'wp_ajax_elementor_ajax' )
		||
		doing_action( 'admin_action_elementor' )
		||
		doing_filter( 'the_content' )
		||
		enlightenment_has_in_call_stack( array(
			array(
				'key'     => 'class',
				'value'   => 'Elementor',
				'compare' => 'STARTS_WITH',
			),
		) )
	) {
		$output = sprintf( '<div class="woocommerce-loop-product__image">%s</div>', $output );
	}

	return $output;
}
add_filter( 'woocommerce_product_get_image', 'enlightenment_theme_filter_product_get_image' );

function enlightenment_woocommerce_filter_template_global_quantity_input_output( $output, $template_name, $template_path, $located, $args ) {
	if ( ! $args['readonly'] && 'hidden' != $args['type'] ) {
		$offset = strpos( $output, '<div class="quantity">' );
		if ( false !== $offset ) {
			$value  = 1;
			$min    = 0;
			$max    = false;

			$start  = strpos( $output, '<input', $offset );
			$end    = strpos( $output, '</div>', $offset );
			if ( false !== $start && $start < $end ) {
				$end = strpos( $output, '/>', $start );

				$start_a = strpos( $output, 'value="', $start );
				if ( false !== $start_a && $start_a < $end ) {
					$start_a += 7;
					$end_a    = strpos( $output, '"', $start_a );
					$length   = $end_a - $start_a;
					$value    = substr( $output, $start_a, $length );
				}

				$start_a = strpos( $output, 'min="', $start );
				if ( false !== $start_a && $start_a < $end ) {
					$start_a += 5;
					$end_a    = strpos( $output, '"', $start_a );
					$length   = $end_a - $start_a;
					$min      = substr( $output, $start_a, $length );
				}

				$start_a = strpos( $output, 'max="', $start );
				if ( false !== $start_a && $start_a < $end ) {
					$start_a += 5;
					$end_a    = strpos( $output, '"', $start_a );
					$length   = $end_a - $start_a;
					$max      = substr( $output, $start_a, $length );
				}
			}

	        $output = substr_replace( $output, sprintf(
				'<button class="manipulate-quantity decrease-quantity"%s><i class="fas fa-minus"></i> <span class="screen-reader-text visually-hidden">%s</span></button>',
				$value <= $min ? ' disabled="disabled"' : '',
				__( 'Decrease quantity', 'enlightenment' )
			), $offset + 22, 0 );
			$offset = strrpos( $output, '</div>', $offset );
			$output = substr_replace( $output, sprintf(
				'<button class="manipulate-quantity increase-quantity"%s><i class="fas fa-plus"></i> <span class="screen-reader-text visually-hidden">%s</span></button>',
				false !== $max && $value >= $max ? ' disabled="disabled"' : '',
				__( 'Increase quantity', 'enlightenment' )
			), $offset, 0 );
		}
	}

	return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_global_quantity_input_output', 'enlightenment_woocommerce_filter_template_global_quantity_input_output', 10, 5 );

function enlightenment_woocommerce_filter_loop_add_to_cart_link( $output, $product, $args = array() ) {
	if ( false !== strpos( $output, '<div class="wp-block-button ' ) ) {
		return $output;
	}

    $class = esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' );

	$output = str_replace( sprintf( ' %s btn btn-secondary', $class ), sprintf( ' %s btn btn-outline-secondary', $class ), $output );
	$output = str_replace( sprintf( '"%s btn btn-secondary', $class ), sprintf( '"%s btn btn-outline-secondary', $class ), $output );

    if (
		$product instanceof WC_Product_Simple    &&
		$product->supports( 'ajax_add_to_cart' ) &&
		$product->is_purchasable()               &&
		$product->is_in_stock()
	) {
        $output = str_replace( '</a>', ' <i class="fas fa-spinner fa-pulse"></i></a>', $output );
    }

    $output = sprintf( '<span class="btn-group" role="group">%s</span>', $output );

	// WooCommerce Bundles
	$output = str_replace( 'class="button product_type_bundle btn btn-secondary ', 'class="button product_type_bundle btn btn-outline-secondary ', $output );

    return $output;
}
add_filter( 'woocommerce_loop_add_to_cart_link', 'enlightenment_woocommerce_filter_loop_add_to_cart_link', 12, 3 );

function enlightenment_woocommerce_filter_bundled_items_grid_layout_columns( $columns, $bundle ) {
	if ( 'after_summary' === $bundle->get_add_to_cart_form_location() ) {
		$columns = 4;
	}

	if ( 'default' === $bundle->get_add_to_cart_form_location() ) {
		$columns = 2;
	}

	return $columns;
}
add_filter( 'woocommerce_bundled_items_grid_layout_columns', 'enlightenment_woocommerce_filter_bundled_items_grid_layout_columns', 10, 2 );

function enlightenment_theme_woocommerce_filter_widget_shopping_cart_buttons( $output ) {
	return str_replace( '" class="button btn btn-secondary ', '" class="button btn btn-outline-secondary ', $output );
}
add_filter( 'enlightenment_woocommerce_filter_widget_shopping_cart_buttons', 'enlightenment_theme_woocommerce_filter_widget_shopping_cart_buttons', 12 );

function enlightenment_woocommerce_filter_template_single_product_tabs_tabs_output( $output ) {
	$offset = strpos( $output, '<ul class="tabs wc-tabs nav nav-tabs" role="tablist">' );
	if ( false !== $offset ) {
		$output = substr_replace( $output, '<div class="wc-tabs-wrap">' . "\n", $offset, 0 );
		$offset = strpos( $output, '</ul>', $offset );
		$output = substr_replace( $output, "\n" . '</div>', $offset + 5, 0 );
	}

	return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_single_product_tabs_tabs_output', 'enlightenment_woocommerce_filter_template_single_product_tabs_tabs_output', 12 );

function enlightenment_woocommerce_filter_upsell_display_args( $args ) {
	$args['limit'] = 4;

	return $args;
}
add_filter( 'enlightenment_woocommerce_upsell_display_args', 'enlightenment_woocommerce_filter_upsell_display_args' );

function enlightenment_woocommerce_output_filter_related_products_args( $args ) {
    $layouts = array_reverse( enlightenment_current_layout() );

    foreach ( $layouts as $layout ) {
        if ( 'inherit' == $layout ) {
            continue;
        }

        switch ( $layout ) {
            case 'sidebar-content-sidebar':
                $args['posts_per_page'] = 2;
                $args['columns'] = 2;
                break;

            case 'full-width' :
                $args['posts_per_page'] = 4;
                $args['columns'] = 4;
                break;

            case 'content-sidebar':
            case 'sidebar-content':
            default:
                $args['posts_per_page'] = 3;
                $args['columns'] = 3;
				break;
        }

        break;
    }

    return $args;
}
add_action( 'woocommerce_output_related_products_args', 'enlightenment_woocommerce_output_filter_related_products_args' );

function enlightenment_woocommerce_filter_template_cart_cart_output( $output ) {
    $output = str_replace( 'class="button btn btn-secondary ', 'class="button btn btn-outline-secondary ', $output );
	$output = str_replace( 'class="button wp-element-button btn btn-secondary ', 'class="button wp-element-button btn btn-outline-secondary ', $output );

	$offset = strpos( $output, '<label for="coupon_code" ' );
	if ( false !== $offset ) {
		$offset = strpos( $output, 'class="button btn btn-light', $offset );
		$output = substr_replace( $output, 'theme-inverse', $offset + 22, 5 );
	}

	$offset = strpos( $output, '<div class="coupon e-cart-section ' );
	if ( false !== $offset ) {
		$offset = strpos( $output, 'class="button e-apply-coupon btn btn-light ', $offset );
		$output = substr_replace( $output, 'theme-inverse', $offset + 37, 5 );
	}

	return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_cart_cart_output', 'enlightenment_woocommerce_filter_template_cart_cart_output', 12 );

function enlightenment_woocommerce_filter_template_checkout_form_coupon_output( $output ) {
	$output = str_replace( 'class="button btn btn-light ', 'class="button btn btn-theme-inverse ', $output );
	$output = str_replace( 'class="button btn btn-light"', 'class="button btn btn-theme-inverse"', $output );

	return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_checkout_form_coupon_output', 'enlightenment_woocommerce_filter_template_checkout_form_coupon_output', 12 );

function enlightenment_woocommerce_filter_cart_item_remove_link( $output ) {
	return str_replace( '>&times;<', '><i class="fas fa-times" aria-hidden="true" role="presentation"></i><', $output );
}
add_filter( 'woocommerce_cart_item_remove_link', 'enlightenment_woocommerce_filter_cart_item_remove_link' );

remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

add_action( 'woocommerce_after_cart_table', 'woocommerce_cross_sell_display' );

function enlightenment_woocommerce_cross_sells_total() {
	return 3;
}
add_filter( 'woocommerce_cross_sells_total', 'enlightenment_woocommerce_cross_sells_total' );

function enlightenment_woocommerce_cross_sells_columns() {
	return 3;
}
add_filter( 'woocommerce_cross_sells_columns', 'enlightenment_woocommerce_cross_sells_columns' );

function enlightenment_woocommerce_filter_template_cart_shipping_calculator_output( $output ) {
	$output = str_replace( 'class="button btn btn-secondary btn-sm ', 'class="button btn btn-outline-secondary btn-sm ', $output );
    $output = str_replace( 'class="button btn btn-secondary btn-sm"', 'class="button btn btn-outline-secondary btn-sm"', $output );

	return $output;
}
add_action( 'enlightenment_woocommerce_filter_template_cart_shipping_calculator_output', 'enlightenment_woocommerce_filter_template_cart_shipping_calculator_output', 12 );

add_action( 'woocommerce_account_payment_methods_column_actions', 'enlightenment_ob_start', 8 );

function enlightenment_woocommerce_filter_account_payment_methods_column_actions() {
    $output = ob_get_clean();
    $output = str_replace( 'class="button btn btn-secondary ', 'class="button btn btn-outline-secondary ', $output );

    echo $output;
}
add_action( 'woocommerce_account_payment_methods_column_actions', 'enlightenment_woocommerce_filter_account_payment_methods_column_actions', 12 );

function enlightenment_woocommerce_filter_save_to_account_text( $output ) {
    return __( 'Save payment information to my account.', 'enlightenment' );
}
add_filter( 'wc_stripe_save_to_account_text', 'enlightenment_woocommerce_filter_save_to_account_text' );

function enlightenment_woocommerce_filter_paypal_icon( $icon ) {
    return array();
}
add_filter( 'woocommerce_paypal_icon', 'enlightenment_woocommerce_filter_paypal_icon' );

function enlightenment_woocommerce_filter_eway_icon( $output ) {
    return str_replace( 'eway-tiny.png', 'eway-siteseal.png', $output );
}
add_filter('woocommerce_eway_icon', 'enlightenment_woocommerce_filter_eway_icon' );

function enlightenment_woocommerce_filter_klarna_checkout_icon_html( $output ) {
    return str_replace( '?width=100', '?width=214', $output );
}
add_filter( 'wc_klarna_checkout_icon_html', 'enlightenment_woocommerce_filter_klarna_checkout_icon_html' );

function enlightenment_woocommerce_filter_gateway_icon( $output, $id ) {
    switch ( $id ) {
        case 'stripe' :
            if ( 'USD' === get_woocommerce_currency() ) {
                $output = '<i class="far fa-credit-card"></i>';
            }

            break;

        case 'braintree_credit_card' :
        case 'square_credit_card' :
            $count  = 0;
            $offset = 0;

            while ( false !== strpos( $output, '<img ', $offset ) ) {
                $offset = strpos( $output, '<img ', $offset );
                $count++;
                $offset++;
            }

            if ( 3 < $count ) {
                $output = '<i class="far fa-credit-card"></i>';
            }

            break;
        case 'braintree_paypal' :
            $output = '';
            break;
    }

    return $output;
}
add_filter( 'woocommerce_gateway_icon', 'enlightenment_woocommerce_filter_gateway_icon', 10, 2 );

function enlightenment_woocommerce_filter_stripe_description( $output, $id ) {
    if ( ! doing_action( 'woocommerce_checkout_order_review' ) && ! doing_action( 'wc_ajax_update_order_review' ) ) {
        return $output;
    }

    if ( 'stripe' !== $id ) {
        return $output;
    }

    if ( 'USD' !== get_woocommerce_currency() ) {
        return $output;
    }

    $output .= '<div class="stripe-cc-icons">
        <i class="fab fa-cc-visa"></i>
        <i class="fab fa-cc-amex"></i>
        <i class="fab fa-cc-mastercard"></i>
        <i class="fab fa-cc-discover"></i>
        <i class="fab fa-cc-jcb"></i>
        <i class="fab fa-cc-diners-club"></i>
    </div>';

    return $output;
}
add_filter( 'wc_stripe_description', 'enlightenment_woocommerce_filter_stripe_description', 10, 2 );

function enlightenment_woocommerce_filter_braintree_credit_card_payment_form_description( $output ) {
    if ( ! doing_action( 'woocommerce_checkout_order_review' ) && ! doing_action( 'wc_ajax_update_order_review' ) ) {
        return $output;
    }

    $settings   = get_option('woocommerce_braintree_credit_card_settings');
    $card_types = apply_filters( 'wc_braintree_credit_card_available_card_types', $settings['card_types'] );
    $icons_html = '';
    $icons_map  = array(
        'VISA'    => '<i class="fab fa-cc-visa"></i>',
        'MC'      => '<i class="fab fa-cc-mastercard"></i>',
        'AMEX'    => '<i class="fab fa-cc-amex"></i>',
        'DISC'    => '<i class="fab fa-cc-discover"></i>',
        'DINERS'  => '<i class="fab fa-cc-diners-club"></i>',
        'MAESTRO' => '<i class="fab fa-cc-mastercard"></i>',
        'JCB'     => '<i class="fab fa-cc-jcb"></i>',
    );

    if ( 3 >= count( $card_types ) ) {
        return $output;
    }

    foreach ( $icons_map as $provider => $icon_html ) {
        if ( 'MAESTRO' == $provider && in_array( 'MC', $card_types ) ) {
            continue;
        }

        if ( in_array( $provider, $card_types ) ) {
            $icons_html .= $icon_html;
        }
    }

    if ( ! empty( $icons_html ) ) {
        $icons_html = sprintf( '<div class="braintree-cc-icons">%s</div>', $icons_html );
    }

    $output .= $icons_html;

    return $output;
}
add_filter( 'wc_braintree_credit_card_payment_form_description', 'enlightenment_woocommerce_filter_braintree_credit_card_payment_form_description' );

function enlightenment_woocommerce_filter_square_credit_card_payment_form_description( $output ) {
    if ( ! doing_action( 'woocommerce_checkout_order_review' ) && ! doing_action( 'wc_ajax_update_order_review' ) ) {
        return $output;
    }

    $settings   = get_option('woocommerce_square_credit_card_settings');
    $card_types = apply_filters( 'wc_square_credit_card_available_card_types', $settings['card_types'] );
    $icons_html = '';
    $icons_map  = array(
        'VISA'    => '<i class="fab fa-cc-visa"></i>',
        'MC'      => '<i class="fab fa-cc-mastercard"></i>',
        'AMEX'    => '<i class="fab fa-cc-amex"></i>',
        'DISC'    => '<i class="fab fa-cc-discover"></i>',
        'DINERS'  => '<i class="fab fa-cc-diners-club"></i>',
        'JCB'     => '<i class="fab fa-cc-jcb"></i>',
    );

    if ( 3 >= count( $card_types ) ) {
        return $output;
    }

    foreach ( $icons_map as $provider => $icon_html ) {
        if ( in_array( $provider, $card_types ) ) {
            $icons_html .= $icon_html;
        }
    }

    if ( ! empty( $icons_html ) ) {
        $icons_html = sprintf( '<div class="square-cc-icons">%s</div>', $icons_html );
    }

    $output .= $icons_html;

    return $output;
}
add_filter( 'wc_square_credit_card_payment_form_description', 'enlightenment_woocommerce_filter_square_credit_card_payment_form_description' );

function enlightenment_woocommerce_stripe_gateway_icon( $output, $id ) {
    if ( ! doing_action( 'woocommerce_account_content' ) ) {
        return $output;
    }

    if ( 'stripe' != $id ) {
        return $output;
    }

    return '<div class="stripe-cc-icons">
        <i class="fab fa-cc-visa"></i>
        <i class="fab fa-cc-amex"></i>
        <i class="fab fa-cc-mastercard"></i>
        <i class="fab fa-cc-discover"></i>
        <i class="fab fa-cc-jcb"></i>
        <i class="fab fa-cc-diners-club"></i>
    </div>';
}
add_filter( 'woocommerce_gateway_icon', 'enlightenment_woocommerce_stripe_gateway_icon', 10, 2 );

function enlightenment_woocommerce_braintree_credit_card_gateway_icon( $output, $id ) {
    if ( ! doing_action( 'woocommerce_account_content' ) ) {
        return $output;
    }

    if ( 'braintree_credit_card' != $id ) {
        return $output;
    }

    $output     = '';
    $settings   = get_option('woocommerce_braintree_credit_card_settings');
    $card_types = apply_filters( 'wc_braintree_credit_card_available_card_types', $settings['card_types'] );
    $icons_html = '';
    $icons_map  = array(
        'VISA'    => '<i class="fab fa-cc-visa"></i>',
        'MC'      => '<i class="fab fa-cc-mastercard"></i>',
        'AMEX'    => '<i class="fab fa-cc-amex"></i>',
        'DISC'    => '<i class="fab fa-cc-discover"></i>',
        'DINERS'  => '<i class="fab fa-cc-diners-club"></i>',
        'MAESTRO' => '<i class="fab fa-cc-mastercard"></i>',
        'JCB'     => '<i class="fab fa-cc-jcb"></i>',
    );

    foreach ( $icons_map as $provider => $icon_html ) {
        if ( 'MAESTRO' == $provider && in_array( 'MC', $card_types ) ) {
            continue;
        }

        if ( in_array( $provider, $card_types ) ) {
            $icons_html .= $icon_html;
        }
    }

    if ( ! empty( $icons_html ) ) {
        $icons_html = sprintf( '<div class="braintree-cc-icons">%s</div>', $icons_html );
    }

    $output .= $icons_html;

    return $output;
}
add_filter( 'woocommerce_gateway_icon', 'enlightenment_woocommerce_braintree_credit_card_gateway_icon', 10, 2 );

function enlightenment_woocommerce_braintree_paypal_gateway_icon( $output, $id ) {
    if ( ! doing_action( 'woocommerce_account_content' ) ) {
        return $output;
    }

    if ( 'braintree_paypal' != $id ) {
        return $output;
    }

    return '<div class="braintree-paypal-icon"><i class="fab fa-paypal"></i></div>';
}
add_filter( 'woocommerce_gateway_icon', 'enlightenment_woocommerce_braintree_paypal_gateway_icon', 10, 2 );

function enlightenment_woocommerce_filter_credit_card_form_fields( $fields ) {
    if ( false !== strpos( $fields['card-number-field'], 'eway_payments-card-number' ) ) {
        $fields['card-number-field'] = str_replace( '/>', '/><i class="fab"></i>', $fields['card-number-field'] );
    }

    return $fields;
}
add_filter( 'woocommerce_credit_card_form_fields', 'enlightenment_woocommerce_filter_credit_card_form_fields', 999 );

function enlightenment_woocommerce_filter_braintree_credit_card_hosted_fields_styles( $styles ) {
	$styles['input']['font-size'] = '1rem';

	return $styles;
}
add_filter( 'wc_braintree_credit_card_hosted_fields_styles', 'enlightenment_woocommerce_filter_braintree_credit_card_hosted_fields_styles' );

function enlightenment_woocommerce_filter_square_credit_card_payment_form_input_styles( $styles ) {
	if ( isset( $styles[0] ) && isset( $styles[0]['fontSize'] ) ) {
		$styles[0]['fontSize'] = '1em';
	}

	return $styles;
}
add_filter( 'wc_square_credit_card_payment_form_input_styles', 'enlightenment_woocommerce_filter_square_credit_card_payment_form_input_styles' );

function enlightenment_woocommerce_filter_pay_order_button_html( $output ) {
    return str_replace( 'class="button alt btn btn-primary ', 'class="button alt btn btn-danger ', $output );
}
add_filter( 'woocommerce_pay_order_button_html', 'enlightenment_woocommerce_filter_pay_order_button_html', 12 );

function enlightenment_theme_woocommerce_filter_klarna_checkout( $output ) {
	$output = str_replace( 'class="checkout-button btn btn-primary btn-lg ', 'class="checkout-button btn btn-danger btn-lg ', $output );
	$output = str_replace( 'class="checkout-button btn btn-primary btn-lg"', 'class="checkout-button btn btn-danger btn-lg"', $output );
    $output = str_replace( 'class="checkout-button button btn btn-primary btn-lg ', 'class="checkout-button button btn btn-danger btn-lg ', $output );
	$output = str_replace( 'class="checkout-button button btn btn-primary btn-lg"', 'class="checkout-button button btn btn-danger btn-lg"', $output );
	$output = str_replace( 'class="tribe-checkout-backlink btn btn-secondary ', 'class="tribe-checkout-backlink btn btn-outline-secondary ', $output );
	$output = str_replace( 'class="woocommerce-button button e-apply-coupon btn btn-light ', 'class="woocommerce-button button e-apply-coupon btn btn-theme-inverse ', $output );

	return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_checkout_form_checkout_output', 'enlightenment_theme_woocommerce_filter_klarna_checkout', 12 );

function enlightenment_woocommerce_filter_display_item_meta( $output ) {
    if ( empty ( $output ) ) {
        return $output;
    }

    return sprintf( '<div class="dropdown variations-dropdown"><a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a><ul class="dropdown-menu"><li class="dropdown-item">%s</li></ul></div>', $output );
}
add_filter( 'woocommerce_display_item_meta', 'enlightenment_woocommerce_filter_display_item_meta' );

function enlightenment_woocommerce_filter_review_author_args( $args, $comment ) {
    if ( 'review' != $comment->comment_type ) {
        return $args;
    }

    if ( 'yes' === get_option( 'woocommerce_review_rating_verification_label' ) && wc_review_is_from_verified_owner( $comment->comment_ID ) ) {
        $args['after'] = sprintf( '<span class="woocommerce-review__verified verified badge text-bg-success rounded-pill"><i class="fas fa-check"></i> <span class="screen-reader-text visually-hidden">%s</span></span> ', esc_attr__( 'verified owner', 'enlightenment' ) );
    }

    return $args;
}
add_filter( 'enlightenment_comment_author_args', 'enlightenment_woocommerce_filter_review_author_args', 12, 2 );

function enlightenment_woocommerce_filter_template_myaccount_view_order_output( $output ) {
    $output = str_replace( 'class="button btn btn-secondary ', 'class="button btn btn-outline-secondary ', $output );
	$output = str_replace( 'class="button btn btn-secondary"', 'class="button btn btn-outline-secondary"', $output );

	return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_myaccount_view_order_output', 'enlightenment_woocommerce_filter_template_myaccount_view_order_output', 12 );

function enlightenment_woocommerce_filter_template_myaccount_orders_output( $output ) {
    $output = str_replace( 'woocommerce-button btn btn-secondary ', 'woocommerce-button btn btn-outline-secondary ', $output );

    // YITH WooCommerce Order Tracking
    $output = str_replace( 'track-button button btn btn-secondary ', 'track-button button btn btn-outline-secondary ', $output );

    return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_myaccount_orders_output', 'enlightenment_woocommerce_filter_template_myaccount_orders_output', 12 );

function enlightenment_woocommerce_filter_template_order_order_downloads_output( $output ) {
    return str_replace( 'class="woocommerce-MyAccount-downloads-file button alt">', 'class="woocommerce-MyAccount-downloads-file button alt btn btn-outline-secondary"><i class="fas fa-cloud-download-alt"></i> ', $output );
}
add_filter( 'enlightenment_woocommerce_filter_template_order_order_downloads_output', 'enlightenment_woocommerce_filter_template_order_order_downloads_output' );

function enlightenment_woocommerce_filter_template_myaccount_my_address_output( $output ) {
    return str_replace( 'class="edit"', 'class="edit btn btn-outline-secondary"', $output );
}
add_filter( 'enlightenment_woocommerce_filter_template_myaccount_my_address_output', 'enlightenment_woocommerce_filter_template_myaccount_my_address_output' );

function enlightenment_woocommerce_remove_bootstrap_customer_login_form() {
    remove_filter( 'enlightenment_woocommerce_filter_template_myaccount_form_login_output', 'enlightenment_woocommerce_bootstrap_template_myaccount_form_login_output' );
}
add_action( 'after_setup_theme', 'enlightenment_woocommerce_remove_bootstrap_customer_login_form', 42 );

function enlightenment_woocommerce_customer_login_form() {
    $tabs  = '<ul class="nav nav-tabs" role="tablist">';
    $tabs .= '<li class="nav-item">';
    $tabs .= sprintf( '<a class="nav-link active" id="wc-login-tab" data-bs-toggle="tab" href="#wc-login-form" role="tab" aria-controls="wc-login-form" aria-selected="true">%s</a>', esc_html( 'Login', 'enlightenment' ) );
    $tabs .= '</li>';
    $tabs .= '<li class="nav-item">';
    $tabs .= sprintf( '<a class="nav-link" id="wc-register-tab" data-bs-toggle="tab" href="#wc-register-form" role="tab" aria-controls="wc-login-form" aria-selected="false">%s</a>', esc_html( 'Register', 'enlightenment' ) );
    $tabs .= '</li>';
    $tabs .= '</ul>';

    $output = ob_get_clean();
    $output = str_replace( '<div class="u-columns col2-set"', '<div class="tab-content"', $output );
    $output = str_replace( '<div class="u-column1 col-1">', '<div class="tab-pane active" id="wc-login-form" role="tabpanel" aria-labelledby="wc-login-tab">', $output );
    $output = str_replace( '<div class="u-column2 col-2">', '<div class="tab-pane" id="wc-register-form" role="tabpanel" aria-labelledby="wc-register-tab">', $output );
    $output = str_replace( '<h2>', '<h2 class="screen-reader-text visually-hidden">', $output );

    // $output = str_replace( '<h2>', '<div class="woocommerce-account-form-login-or-register"><h2>', $output );
    // $output = str_replace( '</form>', '</form></div>', $output );

    printf( '<div class="woocommerce-account-form-login-register-wrap">%s %s</div>', $tabs, $output );
}
// add_action( 'woocommerce_after_customer_login_form', 'enlightenment_woocommerce_customer_login_form' );

function enlightenment_woocommerce_filter_template_myaccount_form_login_output( $output ) {
	if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) {
	    $tabs  = '<ul class="nav nav-tabs" role="tablist">';
	    $tabs .= '<li class="nav-item">';
	    $tabs .= sprintf( '<a class="nav-link active" id="wc-login-tab" data-bs-toggle="tab" href="#wc-login-form" role="tab" aria-controls="wc-login-form" aria-selected="true">%s</a>', esc_html__( 'Login', 'enlightenment' ) );
	    $tabs .= '</li>';
	    $tabs .= '<li class="nav-item">';
	    $tabs .= sprintf( '<a class="nav-link" id="wc-register-tab" data-bs-toggle="tab" href="#wc-register-form" role="tab" aria-controls="wc-login-form" aria-selected="false">%s</a>', esc_html__( 'Register', 'enlightenment' ) );
	    $tabs .= '</li>';
	    $tabs .= '</ul>';

	    $output = str_replace( '<div class="u-columns col2-set"', '<div class="tab-content"', $output );
	    $output = str_replace( '<div class="u-column1 col-1">', '<div class="tab-pane active" id="wc-login-form" role="tabpanel" aria-labelledby="wc-login-tab">', $output );
	    $output = str_replace( '<div class="u-column2 col-2">', '<div class="tab-pane" id="wc-register-form" role="tabpanel" aria-labelledby="wc-register-tab">', $output );
	    $output = str_replace( '<h2>', '<h2 class="screen-reader-text visually-hidden">', $output );

		$output = sprintf( '<div class="woocommerce-account-form-login-register-wrap">%s %s</div>', $tabs, $output );
	} else {
		$output = sprintf( '<div class="woocommerce-account-form-login-wrap">%s</div>', $output );
	}

    return $output;
}
add_filter( 'enlightenment_woocommerce_filter_template_myaccount_form_login_output', 'enlightenment_woocommerce_filter_template_myaccount_form_login_output' );

function enlightenment_woocommerce_filter_blocks_product_grid_item_html( $output, $data, $product ) {
    if ( defined( 'REST_REQUEST' ) && true === REST_REQUEST ) {
        return $output;
    }

	if ( ! empty( $data->badge ) ) {
		$output = str_replace( 'class="wc-block-grid__product-onsale"', 'class="onsale"', $output );
	}

    if ( ! empty( $data->button ) ) {
        $button = $data->button;
    	$button = str_replace( 'class="wp-block-button wc-block-grid__product-add-to-cart"', 'class="wp-block-button btn-group"', $button );
        $button = str_replace( 'class="wp-block-button__link add_to_cart_button', 'class="add_to_cart_button btn btn-secondary', $button );
		$button = str_replace( 'class="wp-block-button__link  add_to_cart_button', 'class="add_to_cart_button btn btn-secondary', $button );
		$button = str_replace( 'class="wp-block-button__link wp-element-button add_to_cart_button', 'class="wp-element-button add_to_cart_button btn btn-secondary', $button );
		$button = str_replace( 'class="wp-block-button__link  wp-element-button add_to_cart_button', 'class="wp-element-button add_to_cart_button btn btn-secondary', $button );

        $data->button = $button;
        $button = str_replace( 'class="add_to_cart_button btn btn-secondary', 'class="add_to_cart_button btn btn-outline-secondary', $button );
		$button = str_replace( 'class="wp-element-button add_to_cart_button btn btn-secondary', 'class="wp-element-button add_to_cart_button btn btn-outline-secondary', $button );

        if ( $product instanceof WC_Product_Simple ) {
            $button = str_replace( '</a>', ' <i class="fas fa-spinner fa-pulse"></i></a>', $button );
        }

        $output = str_replace( $data->button, $button, $output );
    }

    return $output;
}
add_filter( 'woocommerce_blocks_product_grid_item_html', 'enlightenment_woocommerce_filter_blocks_product_grid_item_html', 12, 3 );

function enlightenment_get_cart_contents_count() {
    global $woocommerce;

    wp_send_json( array(
        'count' => $woocommerce->cart->cart_contents_count,
    ) );

    die();
}
add_action( 'wp_ajax_enlightenment_get_cart_contents_count', 'enlightenment_get_cart_contents_count' );
add_action( 'wp_ajax_nopriv_enlightenment_get_cart_contents_count', 'enlightenment_get_cart_contents_count' );

/**
 * Hide shipping rates when free shipping is available, but keep "Local pickup"
 * Updated to support WooCommerce 2.6 Shipping Zones
 *
 * @link https://docs.woocommerce.com/document/hide-other-shipping-methods-when-free-shipping-is-available/
**/

function enlightenment_woocommerce_hide_shipping_when_free_is_available( $rates, $package ) {
	if ( true !== get_theme_mod( 'hide_shipping_when_free_is_available' ) ) {
		return $rates;
	}

	$new_rates = array();
	foreach ( $rates as $rate_id => $rate ) {
		// Only modify rates if free_shipping is present.
		if ( 'free_shipping' === $rate->method_id ) {
			$new_rates[ $rate_id ] = $rate;
			break;
		}
	}

	if ( ! empty( $new_rates ) ) {
		//Save local pickup if it's present.
		foreach ( $rates as $rate_id => $rate ) {
			if ('local_pickup' === $rate->method_id ) {
				$new_rates[ $rate_id ] = $rate;
				break;
			}
		}
		return $new_rates;
	}

	return $rates;
}
add_filter( 'woocommerce_package_rates', 'enlightenment_woocommerce_hide_shipping_when_free_is_available', 10, 2 );

function enlightenment_filter_wc_payments_save_to_account_text( $output ) {
	return __( 'Save payment information to my account.', 'enlightenment' );
}
add_filter( 'wc_payments_save_to_account_text', 'enlightenment_filter_wc_payments_save_to_account_text' );

function enlightenment_woocommerce_filter_mollie_settings( $value, $option ) {
    $option = str_replace( 'mollie_components_', '', $option );
    switch ( $option ) {
        case 'backgroundColor' :
        case 'invalid_backgroundColor' :
            $value = '#ffffff';
            break;

        case 'color' :
            $value = '#495057';
            break;

        case '::placeholder' :
            $value = '#6c757d';
            break;

        case 'fontSize' :
            $value = '16px';
            break;

        case 'fontWeight' :
            $value = 'normal';
            break;

        case 'letterSpacing' :
            $value = '0';
            break;

        case 'lineHeight' :
            $value = '1.5';
            break;

        case 'padding' :
            $value = '0';
            break;

        case 'textAlign' :
            $value = 'left';
            break;

        case 'textTransform' :
            $value = 'none';
            break;

        case 'invalid_color' :
            $value = '#eb1c26';
            break;
    }

    return $value;
}
add_filter( 'pre_option_mollie_components_backgroundColor', 'enlightenment_woocommerce_filter_mollie_settings', 10, 2 );
add_filter( 'pre_option_mollie_components_color', 'enlightenment_woocommerce_filter_mollie_settings', 10, 2 );
add_filter( 'pre_option_mollie_components_::placeholder', 'enlightenment_woocommerce_filter_mollie_settings', 10, 2 );
add_filter( 'pre_option_mollie_components_fontSize', 'enlightenment_woocommerce_filter_mollie_settings', 10, 2 );
add_filter( 'pre_option_mollie_components_fontWeight', 'enlightenment_woocommerce_filter_mollie_settings', 10, 2 );
add_filter( 'pre_option_mollie_components_letterSpacing', 'enlightenment_woocommerce_filter_mollie_settings', 10, 2 );
add_filter( 'pre_option_mollie_components_lineHeight', 'enlightenment_woocommerce_filter_mollie_settings', 10, 2 );
add_filter( 'pre_option_mollie_components_padding', 'enlightenment_woocommerce_filter_mollie_settings', 10, 2 );
add_filter( 'pre_option_mollie_components_textAlign', 'enlightenment_woocommerce_filter_mollie_settings', 10, 2 );
add_filter( 'pre_option_mollie_components_textTransform', 'enlightenment_woocommerce_filter_mollie_settings', 10, 2 );
add_filter( 'pre_option_mollie_components_invalid_backgroundColor', 'enlightenment_woocommerce_filter_mollie_settings', 10, 2 );
add_filter( 'pre_option_mollie_components_invalid_color', 'enlightenment_woocommerce_filter_mollie_settings', 10, 2 );

function enlightenment_woocommerce_filter_settings_tabs_array( $tabs ) {
    if ( isset( $tabs['mollie_components'] ) ) {
        unset( $tabs['mollie_components'] );
    }

    return $tabs;
}
add_filter( 'woocommerce_settings_tabs_array', 'enlightenment_woocommerce_filter_settings_tabs_array', 999 );

function enlightenment_woocommerce_filter_featured_category_block( $output, $block ) {
	if ( isset( $block['attrs'] ) && isset( $block['attrs']['align'] ) && 'full' == $block['attrs']['align'] ) {
	    $offset = strpos( $output, 'class="wc-block-featured-category alignfull"' );

		if ( false === $offset ) {
			$offset = strpos( $output, 'class="wc-block-featured-category alignfull ' );
		}

		if ( false !== $offset ) {
	        $output  = str_replace( '<div class="container" style="z-index:1;">', '<div class="container" style="z-index:1;"><div class="wc-block-featured-category__text-wrapper">', $output );
	        $output .= '</div>';
	    } else {
	        $offset = strpos( $output, 'class="wc-block-featured-category ' );
			$offset = strpos( $output, '>', $offset );
	        $output = substr_replace( $output, '<div class="wc-block-featured-category__text-wrapper">', $offset + 1, 0 );
			$output .= '</div>';
	    }
	}

	$isBackground = (
		isset( $block['attrs'] )
		&& (
			( isset( $block['attrs']['hasParallax'] ) && true === $block['attrs']['hasParallax'] )
			||
			( isset( $block['attrs']['isRepeated'] ) && true === $block['attrs']['isRepeated'] )
		)
	);

	$start = strpos( $output, '<div class="background-dim__overlay"' );
	if ( false === $start ) {
		$start = strpos( $output, '<div class="wc-block-featured-category__overlay"' );
	}
	$offset = strpos( $output, 'class="wc-block-featured-category"' );
	if ( false === $offset ) {
		$offset = strpos( $output, 'class="wc-block-featured-category ' );
	}
	if ( false === $offset ) {
		$offset = strpos( $output, ' wc-block-featured-category"' );
	}
	if ( false === $offset ) {
		$offset = strpos( $output, ' wc-block-featured-category ' );
	}
	if ( false !== $start && false !== $offset ) {
		$end = strpos( $output, 'class="wc-block-featured-category__background-image"', $start );

		if ( false === $end ) {
			$end = strpos( $output, 'class="wc-block-featured-category__background-image ', $start );
		}

		if ( false !== $end ) {
			$end     = $isBackground ? strpos( $output, '</div>', $end ) + 6 : strpos( $output, '>', $end ) + 1;
			$length  = $end - $start;
			$overlay = substr( $output, $start, $length );
			$output  = substr_replace( $output, '', $start, $length );

			$offset  = strpos( $output, '>', $offset );
			$output  = substr_replace( $output, "\n" . $overlay, $offset + 1, 0 );
		}
	}

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
add_filter( 'enlightenment_render_block_woocommerce_featured_category', 'enlightenment_woocommerce_filter_featured_category_block', 12, 2 );

function enlightenment_woocommerce_filter_bootstrap_product_categories_block( $output, $block ) {
	if ( isset( $block['attrs'] ) && isset( $block['attrs']['isDropdown'] ) && true === $block['attrs']['isDropdown'] ) {
		$output = str_replace( 'class="wc-block-product-categories__button btn btn-secondary"', 'class="wc-block-product-categories__button btn btn-outline-secondary"', $output );
	}

	return $output;
}
add_filter( 'enlightenment_render_block_woocommerce_product_categories', 'enlightenment_woocommerce_filter_bootstrap_product_categories_block', 12, 2 );

function enlightenment_woocommerce_filter_product_button_block( $output ) {
	$output = str_replace( 'class="btn btn-secondary ',  'class="btn btn-outline-secondary ', $output );
	$output = str_replace( ' btn btn-secondary ',  ' btn btn-outline-secondary ', $output );
	$output = str_replace( '</button>', ' <i class="fas fa-spinner fa-pulse"></i></button>', $output );
	$output = str_replace( 'data-wc-bind--hidden="!state.displayViewCart"', 'data-wc-bind--hidden="!state.displayViewCart" class="btn-group"', $output );

	$offset = strpos( $output, '<div data-block-name="woocommerce/product-button" ' );
	if ( false !== $offset ) {
		$offset = strpos( $output, '>', $offset );
		$output = substr_replace( $output, "\n" . '<span class="btn-group">', $offset + 1, 0 );
		$offset = strrpos( $output, '</div>', $offset );
		$output = substr_replace( $output, '</span>' . "\n", $offset, 0 );
	}

	return $output;
}
add_filter( 'enlightenment_render_block_woocommerce_product_button', 'enlightenment_woocommerce_filter_product_button_block', 12 );

function enlightenment_woocommerce_filter_featured_product_block( $output, $block ) {
	if ( isset( $block['attrs'] ) && isset( $block['attrs']['align'] ) && 'full' == $block['attrs']['align'] ) {
	    $offset = strpos( $output, 'class="wc-block-featured-product alignfull"' );

		if ( false === $offset ) {
			$offset = strpos( $output, 'class="wc-block-featured-product alignfull ' );
		}

		if ( false !== $offset ) {
	        $output  = str_replace( '<div class="container" style="z-index:1;">', '<div class="container" style="z-index:1;"><div class="wc-block-featured-product__text-wrapper">', $output );
	        $output .= '</div>';
	    } else {
	        $offset = strpos( $output, 'class="wc-block-featured-product ' );
			$offset = strpos( $output, '>', $offset );
	        $output = substr_replace( $output, '<div class="wc-block-featured-product__text-wrapper">', $offset + 1, 0 );
			$output .= '</div>';
	    }
	}

	$isBackground = (
		isset( $block['attrs'] )
		&& (
			( isset( $block['attrs']['hasParallax'] ) && true === $block['attrs']['hasParallax'] )
			||
			( isset( $block['attrs']['isRepeated'] ) && true === $block['attrs']['isRepeated'] )
		)
	);

	$start = strpos( $output, '<div class="background-dim__overlay"' );
	if ( false === $start ) {
		$start = strpos( $output, '<div class="wc-block-featured-product__overlay"' );
	}
	$offset = strpos( $output, 'class="wc-block-featured-product"' );
	if ( false === $offset ) {
		$offset = strpos( $output, 'class="wc-block-featured-product ' );
	}
	if ( false === $offset ) {
		$offset = strpos( $output, ' wc-block-featured-product"' );
	}
	if ( false === $offset ) {
		$offset = strpos( $output, ' wc-block-featured-product ' );
	}
	if ( false !== $start && false !== $offset ) {
		$end = strpos( $output, 'class="wc-block-featured-product__background-image"', $start );

		if ( false === $end ) {
			$end = strpos( $output, 'class="wc-block-featured-product__background-image ', $start );
		}

		if ( false !== $end ) {
			$end     = $isBackground ? strpos( $output, '</div>', $end ) + 6 : strpos( $output, '>', $end ) + 1;
			$length  = $end - $start;
			$overlay = substr( $output, $start, $length );
			$output  = substr_replace( $output, '', $start, $length );

			$offset  = strpos( $output, '>', $offset );
			$output  = substr_replace( $output, "\n" . $overlay, $offset + 1, 0 );
		}
	}

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
add_filter( 'enlightenment_render_block_woocommerce_featured_product', 'enlightenment_woocommerce_filter_featured_product_block', 12, 2 );

function enlightenment_woocommerce_filter_product_collection_block( $output ) {
	$offset = strpos( $output, '"wp-block-woocommerce-product-collection ' );

	if ( false === $offset ) {
		$offset = strpos( $output, ' wp-block-woocommerce-product-collection ' );
	}

	if ( false !== $offset ) {
		$output = substr_replace( $output, ' wp-block-query', $offset + 40, 0 );
	}

	return $output;
}
add_filter( 'enlightenment_render_block_woocommerce_product_collection', 'enlightenment_woocommerce_filter_product_collection_block' );

function enlightenment_woocommerce_filter_product_template_block( $output ) {
	$offset = strpos( $output, '"wc-block-product-template ' );

	if ( false === $offset ) {
		$offset = strpos( $output, ' wc-block-product-template ' );
	}

	if ( false !== $offset ) {
		$output = substr_replace( $output, ' wp-block-post-template', $offset + 26, 0 );
	}

	$offset = strpos( $output, ' class="wc-block-product ' );
	while ( false !== $offset ) {
		$output = substr_replace( $output, ' wp-block-post', $offset + 24, 0 );

		$offset = strpos( $output, ' class="wc-block-product ', $offset + 1 );
	}

	return $output;
}
add_filter( 'enlightenment_render_block_woocommerce_product_template', 'enlightenment_woocommerce_filter_product_template_block' );

function enlightenment_theme_woocommerce_filter_currency_switcher_block( $output ) {
	return str_replace( '.wp-block-currency-switcher select{', '.wp-block-currency-switcher select,.wp-block-currency-switcher .select2-container--default .select2-selection--single{', $output );
}
add_filter( 'enlightenment_render_block_woocommerce_payments_multi_currency_switcher', 'enlightenment_theme_woocommerce_filter_currency_switcher_block', 12 );

function enlightenment_wc_memberships_single_post_custom_excerpt( $output ) {
	if ( ! is_singular() ) {
		return $output;
	}

	if ( ! class_exists( 'SkyVerge\WooCommerce\Memberships\Restrictions\Posts' ) ) {
		return $output;
	}

	if (
		! current_user_can( 'wc_memberships_view_restricted_post_content', get_the_ID() ) ||
		! current_user_can( 'wc_memberships_view_delayed_post_content', get_the_ID() )
	) {
		return '';
	}

	return $output;
}
add_filter( 'enlightenment_post_excerpt', 'enlightenment_wc_memberships_single_post_custom_excerpt' );

function enlightenment_filter_wcpay_elements_appearance( $appearance ) {
	if ( empty( $appearance ) ) {
		$appearance = new stdClass();
	}

	if ( ! isset( $appearance->rules ) ) {
		$appearance->rules = new stdClass();
	}

	if ( ! isset( $appearance->rules->{'.Label'} ) ) {
		$appearance->rules->{'.Label'} = new stdClass();
	}

	$appearance->rules->{'.Label'}->paddingTop    = '0';
	$appearance->rules->{'.Label'}->paddingLeft   = '0';
	$appearance->rules->{'.Label'}->paddingRight  = '0';
	$appearance->rules->{'.Label'}->paddingBottom = '0';
	$appearance->rules->{'.Label'}->marginTop     = '0';
	$appearance->rules->{'.Label'}->marginBottom  = '4px';

	if ( ! isset( $appearance->rules->{'.Label--resting'} ) ) {
		$appearance->rules->{'.Label--resting'} = new stdClass();
	}

	$appearance->rules->{'.Label--resting'}->marginLeft   = '12px';
	$appearance->rules->{'.Label--resting'}->marginBottom = '0';

	if ( ! isset( $appearance->rules->{'.Label--floating'} ) ) {
		$appearance->rules->{'.Label--floating'} = new stdClass();
	}

	$appearance->rules->{'.Label--floating'}->paddingTop    = '0';
	$appearance->rules->{'.Label--floating'}->paddingLeft   = '0';
	$appearance->rules->{'.Label--floating'}->paddingRight  = '0';
	$appearance->rules->{'.Label--floating'}->paddingBottom = '0';
	$appearance->rules->{'.Label--floating'}->marginTop     = '2px';
	$appearance->rules->{'.Label--floating'}->opacity       = '0.65';

	if ( ! isset( $appearance->rules->{'.Input'} ) ) {
		$appearance->rules->{'.Input'} = new stdClass();
	}

	$appearance->rules->{'.Input'}->paddingTop    = '6px';
	// $appearance->rules->{'.Input'}->paddingBottom = '10px';
	$appearance->rules->{'.Input'}->paddingBottom = '6px';

	if ( ! isset( $appearance->rules->{'.Input--invalid'} ) ) {
		$appearance->rules->{'.Input--invalid'} = new stdClass();
	}

	// $appearance->rules->{'.Input--invalid'}->paddingBottom = '10px';
	$appearance->rules->{'.Input--invalid'}->paddingBottom = '6px';

	if ( ! isset( $appearance->rules->{'.Error'} ) ) {
		$appearance->rules->{'.Error'} = new stdClass();
	}

	$appearance->rules->{'.Error'}->marginTop  = '4px';
	$appearance->rules->{'.Error'}->color      = '#ea868f';
	$appearance->rules->{'.Error'}->fontSize   = '14px';
	$appearance->rules->{'.Error'}->lineHeight = '1.5';

	return $appearance;
}
add_filter( 'wcpay_elements_appearance', 'enlightenment_filter_wcpay_elements_appearance' );

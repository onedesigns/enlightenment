<?php

function enlightenment_theme_meta_boxes_scripts( $hook ) {
    if ( 'post.php' != $hook ) {
        return;
    }

    if ( WP_Screen::get()->is_block_editor() ) {
        return;
    }

    wp_enqueue_style( 'enlightenment-theme-meta-boxes', get_theme_file_uri( 'assets/css/meta-boxes.css' ), array( 'enlightenment-admin-form-controls' ) );
    wp_enqueue_script( 'enlightenment-theme-meta-boxes', get_theme_file_uri( 'assets/js/meta-boxes.js' ), array( 'enlightenment-admin-form-controls' ), true );
}
add_action( 'admin_enqueue_scripts', 'enlightenment_theme_meta_boxes_scripts' );

function enlightenment_theme_meta_boxes() {
    if ( WP_Screen::get()->is_block_editor() ) {
        return;
    }

	add_meta_box(
        'enlightenment_header_style',
        __( 'Header Style', 'enlightenment' ),
        'enlightenment_header_style_form',
        array( 'post', 'page' ),
        'side',
        'low'
    );
}
add_action( 'add_meta_boxes', 'enlightenment_theme_meta_boxes' );

function enlightenment_header_style_form( $post ) {
    wp_nonce_field( 'enlightenment_header_style_form', 'enlightenment_header_style_form_nonce' );

	if ( 'page' == $post->post_type ) {
		printf( '<p class="post-attributes-label-wrapper navbar-style-label-wrapper"><label class="post-attributes-label">%s</label></p>', __( 'Navbar style', 'enlightenment' ) );

	    $navbar_transparent = get_post_meta( $post->ID, '_enlightenment_navbar_transparent', true );

	    enlightenment_checkbox( array(
	        'name'        => 'enlightenment_navbar_transparent',
	        'label'       => __( 'Force transparent navbar', 'enlightenment' ),
			'description' => __( 'If the first item in the content is aligned full, it will slide under the navbar.', 'enlightenment' ),
	        'checked'     => '1' === $navbar_transparent,
	    ) );
	}

	printf( '<p class="post-attributes-label-wrapper featured-image-style-label-wrapper"><label class="post-attributes-label" for="enlightenment_single_post_thumbnail">%s</label></p>', __( 'Featured image style', 'enlightenment' ) );

    $style = get_post_meta( $post->ID, '_enlightenment_single_post_thumbnail', true );

    echo '<p><label>';
    printf( '<input name="enlightenment_single_post_thumbnail" value="" type="radio" %s /> ', checked( $style, '', false ) );
    _e( 'Use global image style', 'enlightenment' );
    echo '</label></p>';

    enlightenment_image_radio_buttons( array(
        'container_class' => 'featured-image-style',
        'name'            => 'enlightenment_single_post_thumbnail',
        'value'           => $style,
        'buttons'         => array(
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
    ) );

	printf( '<p class="post-attributes-label-wrapper featured-image-position-label-wrapper"><label class="post-attributes-label" for="enlightenment_header_image_position">%s</label></p>', __( 'Header image position', 'enlightenment' ) );

    $position = get_post_meta( $post->ID, '_enlightenment_header_image_position', true );

	enlightenment_position_radio_buttons( array(
        'name'        => 'enlightenment_header_image_position',
        'value'       => $position,
		'blank_label' => __( 'Use global image position', 'enlightenment' ),
	) );

	printf( '<p class="post-attributes-label-wrapper featured-image-overlay-label-wrapper"><label class="post-attributes-label" for="enlightenment_header_overlay">%s</label></p>', __( 'Header image overlay', 'enlightenment' ) );

    $color   = get_post_meta( $post->ID, '_enlightenment_header_overlay_color', true );
	$checked = empty( $color );

    enlightenment_checkbox( array(
        'name'    => 'enlightenment_default_header_overlay',
        'label'   => __( 'Use global overlay settings', 'enlightenment' ),
        'checked' => $checked,
    ) );

    if ( empty( $color ) ) {
        $color = get_theme_mod( 'header_overlay_color' );
    }

    enlightenment_color_picker( array(
        'name'            => 'enlightenment_header_overlay',
        'container_class' => 'enlightenment-header-overlay' . ( $checked ? ' hidden' : '' ),
        'container_id'    => 'enlightenment_header_overlay',
        'alpha'           => true,
        'value'           => $color,
    ) );
}

function enlightenment_header_style_form_save_postdata( $post_id ) {
    if ( ! isset( $_POST['enlightenment_header_style_form_nonce'] ) ) {
        return;
    }

	$nonce = $_POST['enlightenment_header_style_form_nonce'];

	if ( ! wp_verify_nonce( $nonce, 'enlightenment_header_style_form' ) ) {
		return;
    }

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
    }

	$post = get_post( $post_id );

	if ( ! current_user_can( get_post_type_object( $post->post_type )->cap->edit_post, $post_id ) ) {
		return;
    }

    if ( empty( $_POST['enlightenment_navbar_transparent'] ) ) {
		delete_post_meta( $post_id, '_enlightenment_navbar_transparent' );
    } elseif ( '1' === $_POST['enlightenment_navbar_transparent'] ) {
        update_post_meta( $post_id, '_enlightenment_navbar_transparent', '1' );
    }

    if ( isset( $_POST['enlightenment_single_post_thumbnail'] ) ) {
        $value = sanitize_text_field( $_POST['enlightenment_single_post_thumbnail'] );

        if ( empty( $value ) ) {
            delete_post_meta( $post_id, '_enlightenment_single_post_thumbnail' );
        } else {
            if ( in_array( $value, array( 'normal', 'medium', 'large', 'cover', 'full-screen', 'none' ) ) ) {
                update_post_meta( $post_id, '_enlightenment_single_post_thumbnail', $value );
            }
        }
    }

    if ( isset( $_POST['enlightenment_header_image_position'] ) ) {
        $value = sanitize_text_field( $_POST['enlightenment_header_image_position'] );

        if ( empty( $value ) ) {
            delete_post_meta( $post_id, '_enlightenment_header_image_position' );
        } else {
            if ( in_array( $value, array( 'left-top', 'left-center', 'left-bottom', 'center-top', 'center-center', 'center-bottom', 'right-top', 'right-center', 'right-bottom' ) ) ) {
                update_post_meta( $post_id, '_enlightenment_header_image_position', $value );
            }
        }
    }

    if ( empty( $_POST['enlightenment_default_header_overlay'] ) ) {
        if ( isset( $_POST['enlightenment_header_overlay'] ) ) {
            $value = sanitize_text_field( $_POST['enlightenment_header_overlay'] );

            if ( empty( $value ) ) {
                delete_post_meta( $post_id, '_enlightenment_header_overlay_color' );
            } else {
                update_post_meta( $post_id, '_enlightenment_header_overlay_color', $value );
            }
        }
    } elseif ( $_POST['enlightenment_default_header_overlay'] ) {
        delete_post_meta( $post_id, '_enlightenment_header_overlay_color' );
    }
}
add_action( 'save_post', 'enlightenment_header_style_form_save_postdata' );

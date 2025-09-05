<?php

function enlightenment_theme_taxonomy_scripts( $hook ) {
    if ( 'edit-tags.php' != $hook && 'term.php' != $hook ) {
        return;
    }

    wp_enqueue_style( 'enlightenment-theme-taxonomy-fields', get_theme_file_uri( 'assets/css/taxonomy-fields.css' ), array( 'enlightenment-admin-form-controls' ) );
    wp_enqueue_script( 'enlightenment-theme-taxonomy-fields', get_theme_file_uri( 'assets/js/taxonomy-fields.js' ), array( 'enlightenment-admin-form-controls' ), true );
}
add_action( 'admin_enqueue_scripts', 'enlightenment_theme_taxonomy_scripts' );

function enlightenment_theme_taxonomy_meta_fields() {
    if (
        ! function_exists( 'z_taxonomy_image_url' )   &&
        ! function_exists( 'get_wp_term_image' )      &&
        ! class_exists( 'Taxonomy_Images_Supported' ) &&
        ! class_exists( 'WP_Term_Images' )
    ) {
        return;
    }

    $taxonomies = get_taxonomies( array(
        'show_ui' => true,
    ) );

    foreach ( $taxonomies as $taxonomy ) {
        add_action( "{$taxonomy}_add_form_fields",  'enlightenment_theme_taxonomy_form_fields', 12 );
        add_action( "{$taxonomy}_edit_form_fields", 'enlightenment_theme_taxonomy_form_fields', 12 );
    }
}
add_action( 'init', 'enlightenment_theme_taxonomy_meta_fields' );

function enlightenment_theme_taxonomy_form_fields( $term = null ) {
    if ( ! $term instanceof WP_Term ) {
        $term = null;
    }

	enlightenment_taxonomy_form_field( array(
        'term'     => $term,
        'key'      => '_enlightenment_header_image_position',
        'label'    => __( 'Image position', 'enlightenment' ),
		'callback' => 'enlightenment_position_radio_buttons',
		'cb_args'  => array(
			'blank_label' => __( 'Use global image position', 'enlightenment' ),
		),
    ) );

    enlightenment_taxonomy_form_field( array(
        'term'     => $term,
        'key'      => 'enlightenment_header_image_overlay',
        'label'    => __( 'Image overlay', 'enlightenment' ),
        'callback' => 'enlightenment_theme_taxonomy_image_overlay_fields',
        'cb_args'  => array(
            'term' => $term,
        ),
    ) );
}

function enlightenment_theme_taxonomy_image_overlay_fields( $args = null ) {
    $defaults = array(
        'term'  => null,
    );
    $args = wp_parse_args( $args, $defaults );

    if ( $args['term'] instanceof WP_Term ) {
        $color = get_term_meta( $args['term']->term_id, '_enlightenment_header_overlay_color',   true );
    } else {
        $color = '';
    }

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

function enlightenment_theme_taxonomy_save_meta( $term_id ) {
    if ( isset( $_POST['_enlightenment_header_image_position'] ) ) {
        $value = sanitize_text_field( $_POST['_enlightenment_header_image_position'] );

        if ( empty( $value ) ) {
            delete_term_meta( $term_id, '_enlightenment_header_image_position' );
        } else {
            if ( in_array( $value, array( 'left-top', 'left-center', 'left-bottom', 'center-top', 'center-center', 'center-bottom', 'right-top', 'right-center', 'right-bottom' ) ) ) {
                update_term_meta( $term_id, '_enlightenment_header_image_position', $value );
            }
        }
    }

    if ( empty( $_POST['enlightenment_default_header_overlay'] ) ) {
        if ( isset( $_POST['enlightenment_header_overlay'] )  ) {
            $value = sanitize_text_field( $_POST['enlightenment_header_overlay'] );

            if ( empty( $value ) ) {
                delete_term_meta( $term_id, '_enlightenment_header_overlay_color' );
            } else {
                update_term_meta( $term_id, '_enlightenment_header_overlay_color', $value );
            }
        }
    } elseif ( $_POST['enlightenment_default_header_overlay'] ) {
        delete_term_meta( $term_id, '_enlightenment_header_overlay_color' );
    }
}
add_action( 'create_term', 'enlightenment_theme_taxonomy_save_meta' );
add_action( 'edit_term',   'enlightenment_theme_taxonomy_save_meta' );

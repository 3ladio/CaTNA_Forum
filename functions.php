<?php
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'catna-child-style', get_stylesheet_uri(), [ get_template() . '-style' ] );
} );

add_theme_support( 'theme-templates' );

add_action( 'after_setup_theme', function() {
    add_theme_support( 'block-templates' );
    add_theme_support( 'theme-templates' );
});

add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'catna-community',
        get_stylesheet_directory_uri() . '/assets/css/community.css',
        [],
        filemtime( get_stylesheet_directory() . '/assets/css/community.css' )
    );
});

add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_script(
        'catna-community-js',
        get_stylesheet_directory_uri() . '/assets/js/community.js',
        [],
        filemtime( get_stylesheet_directory() . '/assets/js/community.js' ),
        true
    );
});

add_action( 'wp_enqueue_scripts', function() {
    $user_id = get_current_user_id();
    $groups = get_user_meta( $user_id, 'ccrm_groups', true );

    wp_localize_script( 'catna-community-js', 'ccrmUserGroups', [
        'groups' => $groups ? $groups : [],
    ]);
});

add_action( 'init', function() {

    $labels = [
        'name'               => 'Volunteer Opportunities',
        'singular_name'      => 'Volunteer Opportunity',
        'add_new'            => 'Add Opportunity',
        'add_new_item'       => 'Add New Opportunity',
        'edit_item'          => 'Edit Opportunity',
        'new_item'           => 'New Opportunity',
        'view_item'          => 'View Opportunity',
        'search_items'       => 'Search Opportunities',
        'not_found'          => 'No opportunities found',
        'not_found_in_trash' => 'No opportunities found in trash',
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'menu_icon'          => 'dashicons-hammer',
        'supports'           => ['title', 'editor', 'excerpt', 'thumbnail'],
        'has_archive'        => true,
        'rewrite'            => ['slug' => 'volunteer-opportunities'],
        'show_in_rest'       => true,
    ];

    register_post_type( 'volunteer_opportunity', $args );
});

add_action( 'init', function() {

    register_post_meta( 'volunteer_opportunity', 'ccrm_location', [
        'show_in_rest' => true,
        'single'       => true,
        'type'         => 'string',
    ]);

    register_post_meta( 'volunteer_opportunity', 'ccrm_groups', [
        'show_in_rest' => true,
        'single'       => false,
        'type'         => 'array',
    ]);

    register_post_meta( 'volunteer_opportunity', 'ccrm_deadline', [
        'show_in_rest' => true,
        'single'       => true,
        'type'         => 'string',
    ]);
});

// add_filter( 'bbp_default_styles', '__return_false' );

add_action( 'wp_enqueue_scripts', function() {
    wp_dequeue_script( 'wp-interactivity' );
    wp_deregister_script( 'wp-interactivity' );
}, 999 );

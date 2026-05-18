<?php
/**
 * CaTNA Child Theme Functions and SPA API Endpoints
 */

// 1. Enqueue Main Child Theme Styles
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'catna-child-style', get_stylesheet_uri(), [ get_template() . '-style' ] );
} );

// 2. Add Theme Template Support Flags
add_theme_support( 'theme-templates' );
add_action( 'after_setup_theme', function() {
    add_theme_support( 'block-templates' );
    add_theme_support( 'theme-templates' );
});

// 3. Enqueue Custom Dashboard Community CSS Sheet
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'catna-community',
        get_stylesheet_directory_uri() . '/assets/css/community.css',
        [],
        filemtime( get_stylesheet_directory() . '/assets/css/community.css' )
    );
});

// 4. Enqueue Custom SPA JavaScript Engine & Script Localizer Attributes
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_script(
        'catna-community-js',
        get_stylesheet_directory_uri() . '/assets/js/community.js',
        [],
        filemtime( get_stylesheet_directory() . '/assets/js/community.js' ),
        true
    );

    // Contextual Group Meta Passing
    $user_id = get_current_user_id();
    $groups  = get_user_meta( $user_id, 'ccrm_groups', true );

    wp_localize_script( 'catna-community-js', 'ccrmUserGroups', [
        'groups' => $groups ? $groups : [],
    ]);
});

// 5. Register Volunteer Opportunity Custom Post Types
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

// 6. Register Metadata Fields for Volunteer Post Types
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

// Remove Interactivity block issues
add_action( 'wp_enqueue_scripts', function() {
    wp_dequeue_script( 'wp-interactivity' );
    wp_deregister_script( 'wp-interactivity' );
}, 999 );


/**
 * 7. SPA AJAX ENDPOINT: Fetch topics belonging to a selected Forum ID
 */
add_action( 'wp_ajax_ccrm_get_forum_topics', 'ccrm_ajax_get_forum_topics' );
add_action( 'wp_ajax_nopriv_ccrm_get_forum_topics', 'ccrm_ajax_get_forum_topics' );

function ccrm_ajax_get_forum_topics() {
    $forum_id = isset($_GET['forum_id']) ? intval($_GET['forum_id']) : 0;
    
    if (!$forum_id) {
        wp_send_json_error('Invalid Forum ID');
    }

    ob_start();

    // Query topics matching this single forum
    $args = array(
        'post_parent'    => $forum_id,
        'posts_per_page' => -1
    );

    if ( bbp_has_topics( $args ) ) {
        echo '<div class="ccrm-topic-cards">';
        while ( bbp_topics() ) : bbp_the_topic();
            bbp_get_template_part( 'loop', 'single-topic' ); 
        endwhile;
        echo '</div>';
    } else {
        echo '<p>No topics found in this forum.</p>';
    }
    
    $content = ob_get_clean();
    wp_send_json_success($content);
}


/**
 * 8. SPA AJAX ENDPOINT: Fetch bbPress Topic Creation form with validation bypass context
 */
add_action( 'wp_ajax_ccrm_get_topic_form', 'ccrm_ajax_get_topic_form' );

function ccrm_ajax_get_topic_form() {
    if ( ! function_exists( 'bbp_get_template_part' ) ) {
        wp_send_json_error( array( 'message' => 'bbPress engine not loaded.' ) );
    }

    // Capture template content cleanly 
    ob_start();
    
    // 1. Force environmental overrides so bbPress checks pass inside AJAX
    add_filter( 'bbp_is_single_forum', '__return_true' );
    add_filter( 'bbp_is_forum_archive', '__return_true' );
    add_filter( 'bbp_current_user_can_publish_topics', '__return_true' );

    // 2. Query the first available forum ID to use as a hard context fallback
    $forum_id = isset($_GET['forum_id']) ? intval($_GET['forum_id']) : 0;
    if ( ! $forum_id ) {
        $forums = get_posts(array(
            'post_type'      => bbp_get_forum_post_type(),
            'posts_per_page' => 1,
            'fields'         => 'ids'
        ));
        if ( ! empty($forums) ) {
            $forum_id = $forums[0];
        }
    }

    // 3. Set up global post properties so inputs map somewhere valid
    if ( $forum_id ) {
        global $post;
        $post = get_post( $forum_id );
        setup_postdata( $post );
    }

    // 4. PATH LOCATOR: Look for the form in your child theme first, fallback to bbPress core plugin folder
    $template_path = locate_template( array( 'bbpress/form-topic.php', 'form-topic.php' ) );
    
    if ( ! $template_path && defined( 'BBP_PLUGIN_DIR' ) ) {
        // Absolute fallback to core bbPress template location
        $template_path = BBP_PLUGIN_DIR . 'templates/default/bbpress/form-topic.php';
    }

    if ( $template_path && file_exists( $template_path ) ) {
        include( $template_path );
    } else {
        // Ultimate backup using bbPress function directly if paths fail
        bbp_get_template_part( 'form', 'topic' );
    }
    
    // Clean up contextual variables and filters right away
    remove_filter( 'bbp_is_single_forum', '__return_true' );
    remove_filter( 'bbp_is_forum_archive', '__return_true' );
    remove_filter( 'bbp_current_user_can_publish_topics', '__return_true' );
    wp_reset_postdata();

    $form_html = ob_get_clean();

    // Verify we actually captured code instead of whitespace
    if ( ! empty( trim( $form_html ) ) ) {
        wp_send_json_success( $form_html );
    } else {
        wp_send_json_error( array( 'message' => 'Form markup generation failed completely.' ) );
    }
}


/**
 * 9. SPA AJAX ENDPOINT: Securely Intercept and Process New Topic Submissions
 */
add_action( 'wp_ajax_ccrm_submit_ajax_topic', 'ccrm_ajax_submit_ajax_topic' );

function ccrm_ajax_submit_ajax_topic() {
    // 1. Verify Authentication & Nonce
    if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'bbp-new-topic' ) ) {
        wp_send_json_error( array( 'message' => 'Security token verification timeout. Please refresh and retry.' ) );
    }

    if ( ! is_user_logged_in() ) {
        wp_send_json_error( array( 'message' => 'You must be logged in to post.' ) );
    }

    // 2. Extract Fields safely
    $title    = isset( $_POST['bbp_topic_title'] ) ? sanitize_text_field( $_POST['bbp_topic_title'] ) : '';
    $content  = isset( $_POST['bbp_topic_content'] ) ? wp_kses_post( $_POST['bbp_topic_content'] ) : '';
    $forum_id = isset( $_POST['bbp_forum_id'] ) ? intval( $_POST['bbp_forum_id'] ) : 0;

    // Fallback forum check if zero
    if ( ! $forum_id && function_exists('bbp_get_forum_post_type') ) {
        $forums = get_posts( array( 'post_type' => bbp_get_forum_post_type(), 'posts_per_page' => 1, 'fields' => 'ids' ) );
        $forum_id = ! empty( $forums ) ? $forums[0] : 0;
    }

    // 3. Input Validation
    if ( empty( $title ) ) {
        wp_send_json_error( array( 'message' => 'Please provide a descriptive title for your topic.' ) );
    }
    if ( empty( $content ) ) {
        wp_send_json_error( array( 'message' => 'The discussion body field cannot be published empty.' ) );
    }

    $current_user_id = get_current_user_id();

    // 4. Safe direct WordPress Core Insertion mapping bbPress meta structures
    $post_args = array(
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => 'publish',
        'post_type'    => 'topic', // bbPress Topic Post Type
        'post_author'  => $current_user_id,
        'post_parent'  => $forum_id, // Links topic directly to parent forum
    );

    $topic_id = wp_insert_post( $post_args );

    if ( ! is_wp_error( $topic_id ) && $topic_id > 0 ) {
        
        // 5. Apply the standard meta keys bbPress relies on for queries and counts
        update_post_meta( $topic_id, '_bbp_forum_id', $forum_id );
        update_post_meta( $topic_id, '_bbp_author_id', $current_user_id );
        update_post_meta( $topic_id, '_bbp_reply_count', 0 );
        update_post_meta( $topic_id, '_bbp_voice_count', 1 );
        update_post_meta( $topic_id, '_bbp_status', 'publish' );
        
        // Update freshness timestamps on topic and parent forum
        $now = current_time( 'mysql' );
        update_post_meta( $topic_id, '_bbp_last_active_time', $now );
        update_post_meta( $topic_id, '_bbp_last_reply_id', $topic_id );
        update_post_meta( $topic_id, '_bbp_last_active_id', $topic_id );

        if ( $forum_id ) {
            update_post_meta( $forum_id, '_bbp_last_topic_id', $topic_id );
            update_post_meta( $forum_id, '_bbp_last_reply_id', $topic_id );
            update_post_meta( $forum_id, '_bbp_last_active_id', $topic_id );
            update_post_meta( $forum_id, '_bbp_last_active_time', $now );
            
            // Increment forum topic counters gently
            $topic_count = (int) get_post_meta( $forum_id, '_bbp_topic_count', true );
            update_post_meta( $forum_id, '_bbp_topic_count', ++$topic_count );
        }

        // 6. Handle tags if provided
        if ( ! empty( $_POST['bbp_topic_tags'] ) && function_exists('bbp_handle_topic_tags') ) {
            bbp_handle_topic_tags( $topic_id, sanitize_text_field( $_POST['bbp_topic_tags'] ) );
        }
        
        wp_send_json_success( array( 'message' => 'Thread published successfully!', 'forum_id' => $forum_id ) );
    } else {
        $error_msg = is_wp_error( $topic_id ) ? $topic_id->get_error_message() : 'Database insertion failed.';
        wp_send_json_error( array( 'message' => $error_msg ) );
    }
}
<?php
/*
Plugin Name: P3 Unfiltered HTML
Plugin URI: http://planet3.org
Version 1.0
Author: Dan Moutal
Author URI: http://ofdan.ca
License: GPLv2
 */

// Remove KSES if user has unfiltered_html cap
function p3_kses_init() {
    if ( current_user_can( 'unfiltered_html' ) )
        kses_remove_filters();
}

add_action( 'init', 'p3_kses_init', 11 );
add_action( 'set_current_user', 'p3_kses_init', 11 );


function p3_unfilter_roles() {
    // Makes sure $wp_roles is initialized
    get_role( 'administrator' );

    global $wp_roles;
    // Dont use get_role() wrapper, it doesn't work as a one off.
    // (get_role does not properly return as reference)
    $wp_roles->role_objects['administrator']->add_cap( 'unfiltered_html' );
    $wp_roles->role_objects['editor']->add_cap( 'unfiltered_html' );
}

function p3_refilter_roles() {
    get_role( 'administrator' );
    global $wp_roles;
    // Could use the get_role() wrapper here since this function is never
    // called as a one off.  It is always called to alter the role as
    // stored in the DB.
    $wp_roles->role_objects['administrator']->remove_cap( 'unfiltered_html' );
    $wp_roles->role_objects['editor']->remove_cap( 'unfiltered_html' );
}

register_activation_hook( __FILE__, 'p3_unfilter_roles' );   // Add on activate
register_deactivation_hook( __FILE__, 'p3_refilter_roles' ); // Remove on deactivate


// Add the unfiltered_html capability back in to WordPress 3.0 multisite.
function p3_unfilter_multisite( $caps, $cap, $user_id, $args ) {
    if ( $cap == 'unfiltered_html' ) {
        unset( $caps );
        $caps[] = $cap;
    }
    return $caps;
}
add_filter( 'map_meta_cap', 'p3_unfilter_multisite', 10, 4 );

}
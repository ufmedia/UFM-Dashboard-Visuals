<?php
/*
Plugin Name: UFM Dashboard Visuals
Description: Functions and styles specific to the WP Dashboard visuals.
Version: 1.0.0
Author: John Thompson
Author URI: http://ufmedia.net
*/

/** Adds additional styles to tweak the admin layout and visual **/
function dash_admin_styles()
{
    wp_register_style('custom_wp_admin_css', plugins_url('css/admin-style.css', __FILE__));
    wp_enqueue_style('custom_wp_admin_css');
}
add_action('admin_enqueue_scripts', 'dash_admin_styles');
add_action( 'login_enqueue_scripts', 'dash_admin_styles' );

/** Add a custom logo to the login screen **/
function dash_login_logo()
{
    echo '<style  type="text/css"> h1 a {  background-image:url(' . plugins_url('images/admin-logo.png', __FILE__) . ') !important; background-size: 255px 139px !important; width: 255px !important; height: 139px !important; margin-bottom: -20px !important;} </style>';
}
add_action('login_head', 'dash_login_logo');


/** Add a custom logo to the Dashboard **/
function dash_menu_logo()
{
    $logopath = plugins_url('images/admin-dashboard-logo.png', __FILE__);
    $image    = '<a href="#"><img src="' . $logopath . '" /></a>';
    add_menu_page("client-logo", $image, "edit_posts", "client-logo", "displayPage", null, 1);
}
add_action('admin_menu', 'dash_menu_logo');


/** Removes various links from the menu bar **/
function dash_remove_nodes($wp_admin_bar)
{
    $updates_node = $wp_admin_bar->get_node('updates');
    if ($updates_node) {
        $wp_admin_bar->remove_node('updates');
    }
    $wp_admin_bar->remove_node('wp-logo');
    $wp_admin_bar->remove_node('comments');
    $wp_admin_bar->remove_node('new-content');
    $wp_admin_bar->remove_node('customize-themes');
    $wp_admin_bar->remove_node('customize');
    $wp_admin_bar->remove_node('search');
	$wp_admin_bar->remove_node('themes');
	$wp_admin_bar->remove_node('w3tc');
	// if (in_array(get_the_ID(), array(294,7,79,287,165,274,283,308) )) {
	// $wp_admin_bar->remove_node( 'edit' );
	// }
	 
}
add_action('admin_bar_menu', 'dash_remove_nodes', 999);


/** Adds additional class to body tag if custom logo is detected **/
function custom_toolbar_logo_body_class($classes)
{
    $iconurl = plugins_url('images/admin-toolbar-icon.png', __FILE__);
    
    if (file_exists($iconurl)) {
        $classes .= 'custom-toolbar-logo';
    } else {
        $classes .= 'shake-toolbar-logo';
    }
    return $classes;
}
//add_filter('admin_body_class', 'custom_toolbar_logo_body_class');


/** Remove update from footer **/
function dash_remove_footer_update()
{
    remove_filter('update_footer', 'core_update_footer');
}
add_action('admin_menu', 'dash_remove_footer_update');


/** Replace 'How are you, ' **/
function dash_replace_howdy($wp_admin_bar)
{
    $my_account = $wp_admin_bar->get_node('my-account');
    $newtitle   = str_replace('How are you,', 'Logged in as', $my_account->title);
    $wp_admin_bar->add_node(array(
        'id' => 'my-account',
        'title' => $newtitle
    ));
}
add_filter('admin_bar_menu', 'dash_replace_howdy', 25);


/** Hide admin bar from subscribers **/
function dash_hide_admin_bar()
{
    if (!current_user_can('edit_posts')) {
        show_admin_bar(false);
    }
}
add_action('set_current_user', 'dash_hide_admin_bar');


/** Hide WP Help **/
function dash_hide_help()
{
    echo '<style type="text/css">
            #contextual-help-link-wrap { display: none !important; }
          </style>';
}
add_action('admin_head', 'hide_help');


/** Remove Dashboard Items **/
function dash_remove_dashboard_meta() {
        remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
		remove_meta_box( 'rg_forms_dashboard', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');//since 3.8
}
add_action( 'admin_init', 'dash_remove_dashboard_meta' );


/** Hide welcome panel **/
remove_action( 'welcome_panel', 'wp_welcome_panel' );


/** Change the Dashboard title **/
function dash_custom_name(){
        if ( $GLOBALS['title'] != 'Dashboard' ){
            return;
        }

        $GLOBALS['title'] =  __( 'Welcome to the ' . get_bloginfo() . ' Dashboard' ); 
    }
add_action( 'admin_head', 'dash_custom_name' );

//Hide uypdate notices
function dash_hide_update_notice()
{
    if (!current_user_can('update_core')) {
        remove_action( 'admin_notices', 'update_nag', 3 );
    }
}
add_action( 'admin_head', 'dash_hide_update_notice', 1 );


//Remove add new page ability
function dash_disable_new_posts() {
// Hide sidebar link
global $submenu;
unset($submenu['edit.php?post_type=CUSTOM_POST_TYPE'][10]);

// Hide link on listing page
if (isset($_GET['post_type']) && $_GET['post_type'] == 'page' || $_GET['action'] == 'edit') {
    echo '<style type="text/css">
    .page-title-action, .page-title-action { display:none; }
    </style>';
}
}
add_action('admin_menu', 'dash_disable_new_posts');

//hide all the pages we don't want people editing!

function dash_exclude_this_page( $query ) {
	if( !is_admin() )
		return $query;

	global $pagenow;

	// if( 'edit.php' == $pagenow && ( get_query_var('post_type') && 'page' == get_query_var('post_type') ) )
		// $query->set( 'post__not_in', array(294,7,79,287,165,274,283,308) );
	
	return $query;
}
add_action( 'pre_get_posts' ,'dsah_exclude_this_page' );

//hide the fields yoast adds to profile
//add_filter('user_contactmethods','hide_profile_fields',20,1);
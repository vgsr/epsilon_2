<?php

/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package Epsilon_2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// 
// Layout
// 

/**
 * Adds custom classes to the array of body classes.
 */
function epsilon_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author
	if ( is_multi_author() )
		$classes[] = 'group-blog';

	// Adds a class of small-ribbon to all pages but the front page
	if ( ! is_front_page() )
		$classes[] = 'small-ribbon';

	// Adds a class of single-column to page not found pages
	if ( is_404() )
		$classes[] = 'single-column';

	// On 'small-ribbon' pages replace the site description with the site title
	// $classes[] = 'replace-site-description';

	// $classes[] = 'sidebar-right';

	return $classes;
}
add_filter( 'body_class', 'epsilon_body_classes' );

//
// Interaction
// 

/**
 * Filter in a link to a content ID attribute for the next/previous image links on image attachment pages
 */
function epsilon_enhanced_image_navigation( $url, $id ) {
	if ( ! is_attachment() && ! wp_attachment_is_image( $id ) )
		return $url;

	$image = get_post( $id );
	if ( ! empty( $image->post_parent ) && $image->post_parent != $id )
		$url .= '#main';

	return $url;
}
add_filter( 'attachment_link', 'epsilon_enhanced_image_navigation', 10, 2 );

// 
// Main Markup
// 

/**
 * Output custom styles when admin bar is showing
 */
function epsilon_admin_bar_showing_styles() {
	if ( ! is_admin_bar_showing() ) 
		return;	
	?>
<style type="text/css">
	/* Theme Epsilon */
	/* Compensate 100% html/body height with admin-bar. See http://caniuse.com/#search=calc */
	html, body { height: -webkit-calc(100% - 28px); height: calc(100% - 28px); }

	/* Hide admin bar on small screens */
	@media screen and (max-width:600px) {
		html { margin-top: 0 !important; }
		* html body { margin-top: 28px !important; }
		html, body { min-height: 100%; }
		#wpadminbar { display: none; }
	}
</style>
	<?php
}
add_action( 'wp_head', 'epsilon_admin_bar_showing_styles', 99 );

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 */
function epsilon_wp_title( $title, $sep ) {
	global $page, $paged;

	if ( is_feed() )
		return $title;

	// Add the blog name
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $sep $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf( __( 'Page %s', 'epsilon' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'epsilon_wp_title', 10, 2 );

/**
 * Output bloginfo with theme filter 
 */
function epsilon_bloginfo( $show = '' ) {
	echo apply_filters( 'epsilon_bloginfo', get_bloginfo( $show, 'display' ), $show );
}

/**
 * Filter get_bloginfo to alter the bloginfo output
 */
function epsilon_get_bloginfo( $output, $show ) {
	switch ( $show ) {
		// Wraps blog description in an <a> tag
		case 'description' :
			$output = sprintf( '<a href="%s">%s</a>', home_url() . '/', $output );
			break;
	}

	return $output;
}
add_filter( 'epsilon_bloginfo', 'epsilon_get_bloginfo', 10, 2 );

//
// Primary Menu
// 

/**
 * Rewrite primary page menu args
 */
function epsilon_wp_page_menu_args( $args ) {
	if ( isset( $args['theme_location'] ) && 'primary' == $args['theme_location'] ) {
		$args['menu_class'] = 'primary-menu';
		$args['walker']     = new Epsilon_Walker_Primary_Page_Menu;
	}

	return $args;
}
add_filter( 'wp_page_menu_args', 'epsilon_wp_page_menu_args' );

/**
 * Extend page menu walker to create list item globals
 */
class Epsilon_Walker_Primary_Page_Menu extends Walker_Page {

	function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {

		// Find list parents
		$GLOBALS['epsilon_item_has_children'] = (bool) isset( $children_elements[$element->ID] );

		// Find items that are first list child
		if ( isset( $element->post_parent ) && ! empty( $element->post_parent ) ) {
			foreach ( $children_elements[$element->post_parent] as $k => $child ) {
				if ( 0 == $k )
					$GLOBALS['epsilon_item_first_child'] = $child->ID == $element->ID;
			}
		}

		// Run display_element parent method
		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );

		unset( $GLOBALS['epsilon_item_has_children'], $GLOBALS['epsilon_item_first_child'] );
	}
}

/**
 * Adds additional classes to the page list items
 */
function epsilon_page_css_class( $classes, $page, $depth, $args, $current_page ) {
	global $epsilon_item_has_children, $epsilon_item_first_child;

	if ( isset( $args['theme_location'] ) && 'primary' == $args['theme_location'] ) {
		$classes[] = 'item-level-'. $depth;

		if ( $epsilon_item_has_children )
			$classes[] = 'item-has-children';

		if ( $epsilon_item_first_child )
			$classes[] = 'list-divider-before';
	}

	return $classes;
}
add_filter( 'page_css_class', 'epsilon_page_css_class', 10, 5 );

/**
 * Adds login list item to wp_page_menu
 */
function epsilon_wp_page_menu( $menu, $args ) {
	// Adds login list item for primary nav menu
	if ( isset( $args['theme_location'] ) && 'primary' == $args['theme_location'] ) {
		$pos  = strlen( $menu ) - ( strpos( '<ul>', $menu ) ? 17 : 12 ); // With or without pages
		$menu = substr( $menu, 0, $pos ) . epsilon_nav_menu_login_list_item() . substr( $menu, $pos );
	}

	return $menu;
}
add_filter( 'wp_page_menu', 'epsilon_wp_page_menu', 10, 2 );

/**
 * Rewrite primary nav menu args
 */
function epsilon_wp_nav_menu_args( $args ) {
	if ( 'primary' == $args['theme_location'] ) {
		$args['container_class'] = 'primary-menu';
		$args['items_wrap']      = '<ul>%3$s</ul>';
	}

	return $args;
}
add_filter( 'wp_nav_menu_args', 'epsilon_wp_nav_menu_args' );

/**
 * Extend nav menu walker to create list item globals
 */
class Epsilon_Walker_Primary_Nav_Menu extends Walker_Nav_Menu {

	// Hook into display_element method of the main class
	function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ){

		// Find depth and list parents
		$GLOBALS['epsilon_item_level_depth']  = (int) $depth;
		$GLOBALS['epsilon_item_has_children'] = (bool) isset( $children_elements[$element->ID] );

		// Find items that are first list child
		if ( isset( $element->menu_item_parent ) && ! empty( $element->menu_item_parent ) ) {
			foreach ( $children_elements[$element->menu_item_parent] as $k => $child ) {
				if ( 0 == $k )
					$GLOBALS['epsilon_item_first_child'] = $child->ID == $element->ID;
			}
		}

		// Run display_element parent method
		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );

		unset( $GLOBALS['epsilon_item_level_depth'], $GLOBALS['epsilon_item_has_children'], $GLOBALS['epsilon_item_first_child'] );
	}
}

/**
 * Adds additional classes to the nav menu list items
 */
function epsilon_nav_menu_css_class( $classes, $item, $args ) {
	global $epsilon_item_level_depth, $epsilon_item_has_children, $epsilon_item_first_child;

	if ( 'primary' == $args->theme_location ) {
		$classes[] = 'item-level-'. $epsilon_item_level_depth;

		if ( $epsilon_item_has_children )
			$classes[] = 'item-has-children';

		if ( $epsilon_item_first_child )
			$classes[] = 'list-divider-before';
	}

	return $classes;
}
add_filter( 'nav_menu_css_class', 'epsilon_nav_menu_css_class', 10, 3 );

/**
 * Adds login nav menu item to wp_nav_menu
 */
function epsilon_wp_nav_menu_items( $items, $args ) {
	if ( 'primary' == $args->theme_location )
		$items .= epsilon_nav_menu_login_list_item();

	return $items;
}
add_filter( 'wp_nav_menu_items', 'epsilon_wp_nav_menu_items', 99, 2 );

/**
 * Return menu list divider class or span tag
 */
function epsilon_menu_list_divider( $span = false, $after = true ) {
	$divider = 'list-divider';

	if ( $span )
		$divider = sprintf( '<span class="%s"></span>', $divider );
	else
		$divider .= $after ? '-after' : '-before';

	return $divider;
}

//
// Primary Menu Login
// 

/**
 * Setup nav menu login list item
 */
function epsilon_nav_menu_login_list_item() {
	$class = array( 'login-item', 'page-level-0', 'rtl' );
	if ( is_user_logged_in() )
		$class[] = 'logged-in';

	$sub = '<ul class="children">'. epsilon_nav_menu_login_content() .'</ul>';

	// Setup item
	$item = sprintf( 
		'<li class="%s"><a href="%s">%s</a>%s</li>',
		implode( ' ', $class ),
		is_user_logged_in() ? wp_logout_url() : wp_login_url(),
		is_user_logged_in() ? epsilon_get_icon( 'unlocked' ) . '<span class="ir">' . __('Logout') . '</span>' : epsilon_get_icon( 'locked' ) . '<span class="ir">' . __('Login') . '</span>',
		is_user_logged_in() ? $sub : sprintf( '<form class="nav-menu-login" name="loginform" method="post" action="%s">%s</form>', site_url() .'/wp-login.php', $sub )
	);

	return $item;
}

/**
 * Returns nav menu login content
 */
function epsilon_nav_menu_login_content() {
	$items = array();

	// Setup login list items
	if ( ! is_user_logged_in() ) {
		$hook = 'epsilon_nav_menu_login_items';

		// Username
		$items[0] = array(
			'<p class="input-prepend">
				<span class="add-on"><i class="icon-user"></i></span>
				<input type="text" name="log" id="user_login" placeholder="'. __('Username').'">
			</p>',
			array( 'list-divider-before' )
		);

		// Password
		$items[1] = 
			'<p class="input-prepend">
				<span class="add-on"><i class="icon-key"></i></span>
				<input type="password" name="pwd" id="user_pass" placeholder="'. __('Password') .'">
			</p>';

		// Hidden inputs + submit
		$items[49] = array(
			'<input type="hidden" name="_wp_http_referer" value="/">
				<input type="hidden" name="redirect_to" value="' . epsilon_current_url() . '">
				<input type="hidden" name="testcookie" value="1">
				<input type="submit" name="wp-submit" class="submit" value="' . __('Login') . '" />',
			array( 'list-divider-after' )
		);

		// Lost password
		$items[75] = sprintf( '<a href="%s">%s</a>', wp_lostpassword_url(), __('Lost your password?') );

		// Register
		$items[80] = wp_register( '', '', false );

	// Setup logged-in list items
	} else {
		$hook = 'epsilon_nav_menu_loggedin_items';
		$user = wp_get_current_user();

		// Logged in as
		$items[0] = array(
			sprintf( '<a class="nav-menu-login loggedin-as" href="%1$s"><div class="profile-summary">%2$s</div></a>',
				admin_url( '/profile.php' ),
				get_avatar( $user->ID, 32 ) . sprintf( '<strong class="user-display-name">%s</strong>', $user->display_name ) . sprintf( '<span class="metadata">%s</span>', __('Edit profile', 'epsilon') )
			),
			array( 'list-divider-before', 'list-divider-after' )
		);

		// Network admin
		if ( is_super_admin( get_current_user_id() ) )
			$items[9]  = sprintf( '<a href="%s">%s</a>', network_admin_url(), epsilon_get_icon( 'settings' ) . __('Network Admin', 'epsilon') );

		// Dashboard
		if ( current_user_can( 'edit_posts' ) )
			$items[10] = sprintf( '<a href="%s">%s</a>', get_admin_url(),     epsilon_get_icon( 'settings' ) . __('Dashboard', 'epsilon')     );

		// Logout
		$items[99] = array(
			sprintf( '<a href="%s">%s</a>', wp_logout_url(), epsilon_get_icon( 'reply' ) . __('Logout', 'epsilon') ),
			array( 'list-divider-before' )
		);
	}

	// Filter items and sort
	$items = apply_filters( $hook, $items );
	ksort( $items );

	// Render output
	$output = '';
	foreach ( $items as $key => $item ) {
		$class   = is_array( $item ) ? sprintf( 'class="%s"', is_array( $item[1] ) ? implode( ' ', $item[1] ) : $item[1] ) : '';
		$content = is_array( $item ) ? $item[0] : $item;
		$output .= "<li $class>$content</li>";
	}

	return $output;
}

/**
 * Remove logout list divider class when there are only 2 items
 */
function epsilon_nav_menu_logout_class( $items ) {
	if ( 2 == count( $items ) )
		$items[99][1] = array_diff( $items[99][1], array( epsilon_menu_list_divider( false, false ) ) );

	return $items;
}
add_filter( 'epsilon_nav_menu_loggedin_items', 'epsilon_nav_menu_logout_class', 99 );

//
// Other
// 

/**
 * Return the current url
 */
function epsilon_current_url(){
	return esc_url( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
}

/**
 * Redirect logouts to the previous page when no redirect is given
 */
function epsilon_wp_logout_url_redirect( $url, $redirect ) {
	if ( empty( $redirect ) )
		$url = add_query_arg( 'redirect_to', epsilon_current_url(), $url );

	return $url;
}
apply_filters( 'logout_url', 'epsilon_wp_logout_url_redirect', 10, 2 );

/**
 * Returns <i> tag with icon class
 */
function epsilon_get_icon( $label = '' ) {
	return sprintf( '<i class="icon-%s"></i>', $label );
}


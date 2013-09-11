<?php

/**
 * Plugins Compatibility File
 *
 * @package Epsilon_2
 */

/**
 * Load is_plugin_active() functionality
 */
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/** bbPress ********************************************************/

/**
 * Add bbpress profile link to nav menu loggedin items
 */
function epsilon_bbp_nav_menu_view_profile( $items ) {
	if ( is_plugin_active( 'bbpress/bbpress.php' ) ) {
		$items[2] = sprintf( '<a href="%s">%s</a>', bbp_get_user_profile_url( get_current_user_id() ), epsilon_get_icon( 'user' ) . __('View profile', 'epsilon') );
	}

	return $items;
}
add_filter( 'epsilon_nav_menu_loggedin_items', 'epsilon_bbp_nav_menu_view_profile' );

/** User Switching *************************************************/

/**
 * Move switch-on link into the footer
 */
function epsilon_us_move_switch_on() {
	if ( ! is_plugin_active( 'user-switching/user-switching.php' ) ) 
		return;

	?>
<script type="text/javascript">
	jQuery('#user_switching_switch_on').css({ 'float' : 'left', 'margin' : 0 }).prependTo('#colophon .site-info');
</script>
	<?php
}
add_action( 'wp_footer', 'epsilon_us_move_switch_on' );

<?php

/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package Epsilon_2
 */

/**
 * Add theme support for Infinite Scroll.
 * See: http://jetpack.me/support/infinite-scroll/
 */
function epsilon_infinite_scroll_setup() {
	add_theme_support( 'infinite-scroll', array(
		'container' => 'content',
		'footer'    => 'page',
	) );
}
add_action( 'after_setup_theme', 'epsilon_infinite_scroll_setup' );

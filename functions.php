<?php

/**
 * Epsilon functions and definitions
 *
 * @package Epsilon_2
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 500; /* pixels */

/*
 * Load Jetpack compatibility file.
 */
require( get_template_directory() . '/inc/jetpack.php' );

if ( ! function_exists( 'epsilon_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
function epsilon_setup() {

	/**
	 * Custom template tags for this theme.
	 */
	require( get_template_directory() . '/inc/template-tags.php' );

	/**
	 * Custom functions that act independently of the theme templates
	 */
	require( get_template_directory() . '/inc/extras.php' );

	/**
	 * Custom theme support for some common plugins
	 */
	require( get_template_directory() . '/inc/plugins.php' );

	/**
	 * Customizer additions
	 */
	require( get_template_directory() . '/inc/customizer.php' );

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on Epsilon, use a find and replace
	 * to change 'epsilon' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'epsilon', get_template_directory() . '/languages' );

	/**
	 * Add default posts and comments RSS feed links to head
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Enable support for Post Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'epsilon' ),
	) );

	/**
	 * Enable support for Post Formats
	 */
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );
}
endif; // epsilon_setup
add_action( 'after_setup_theme', 'epsilon_setup' );

/**
 * Setup the WordPress core custom background feature.
 *
 * Use add_theme_support to register support for WordPress 3.4+
 * as well as provide backward compatibility for WordPress 3.3
 * using feature detection of wp_get_theme() which was introduced
 * in WordPress 3.4.
 *
 * @todo Remove the 3.3 support when WordPress 3.6 is released.
 *
 * Hooks into the after_setup_theme action.
 */
function epsilon_register_custom_background() {
	$args = array(
		'default-color' => 'ffffff',
		'default-image' => '',
	);

	$args = apply_filters( 'epsilon_custom_background_args', $args );

	if ( function_exists( 'wp_get_theme' ) ) {
		add_theme_support( 'custom-background', $args );
	} else {
		define( 'BACKGROUND_COLOR', $args['default-color'] );
		if ( ! empty( $args['default-image'] ) )
			define( 'BACKGROUND_IMAGE', $args['default-image'] );
		add_custom_background();
	}
}
add_action( 'after_setup_theme', 'epsilon_register_custom_background' );

/**
 * Register widgetized area and update sidebar with default widgets
 */
function epsilon_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'epsilon' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget list-divider-after %2$s"><div class="widget-content">',
		'after_widget'  => '</div></aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'epsilon_widgets_init' );

/**
 * Enqueue scripts and styles
 */
function epsilon_scripts() {
	global $is_IE;

	wp_enqueue_style( 'epsilon-style', get_stylesheet_uri() );
	wp_enqueue_style( 'epsilon-icomoon', get_template_directory_uri() .'/css/icomoon.css' );

	if ( $is_IE )
		wp_enqueue_style( 'epsilon-style-ie', get_template_directory_uri() .'/css/ie.css' );

	wp_enqueue_script( 'epsilon-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );
	wp_enqueue_script( 'epsilon-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	if ( is_singular() && wp_attachment_is_image() )
		wp_enqueue_script( 'epsilon-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );

	if ( is_front_page() )
		wp_enqueue_script( 'epsilon-front-page', get_template_directory_uri() . '/js/front-page.js', array(), '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'epsilon_scripts' );

/**
 * Implement the Custom Header feature
 */
//require( get_template_directory() . '/inc/custom-header.php' );

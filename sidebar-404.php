<?php

/**
 * The Sidebar containing the main widget areas for the page not found page.
 *
 * @package Epsilon_2
 */
?>
	<div id="secondary" class="widget-area error404" role="complementary">
		<?php do_action( 'before_sidebar' ); ?>

		<?php $widget_args = array(
			'before_widget' => '<aside class="widget list-divider-after"><div class="widget-content">',
			'after_widget'  => '</div></aside>'
			); ?>

		<aside id="search" class="widget widget_search list-divider-after">
			<div class="widget-content">
				<?php get_search_form(); ?>
			</div>
		</aside>

		<?php the_widget( 'WP_Widget_Recent_Posts', '', $widget_args ); ?>

		<?php if ( epsilon_categorized_blog() ) : // Only show the widget if site has multiple categories. ?>
		<aside class="widget widget_categories list-divider-after">
			<div class="widget-content">
				<h3 class="widgettitle"><?php _e( 'Most Used Categories', 'epsilon' ); ?></h3>
				<ul>
					<?php wp_list_categories( array( 'orderby' => 'count', 'order' => 'DESC', 'show_count' => 1, 'title_li' => '', 'number' => 10 ) ); ?>
				</ul>
			</div>
		</aside><!-- .widget -->
		<?php endif; ?>

		<?php the_widget( 'WP_Widget_Archives', 'dropdown=1', $widget_args ); ?>

		<?php the_widget( 'WP_Widget_Tag_Cloud', '', $widget_args ); ?>

	</div><!-- #secondary -->

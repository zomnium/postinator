<?php

/*
Plugin Name: Postinator
Plugin URI: https://github.com/zomnium/postinator
Version: 0.0.1
Author: Zomnium
Author URI: http://zomnium.com/
Description: Content widgets created for developers, easy for the users.
*/

class Postinator extends WP_Widget {

	/**
	 * Construct
	 */

	function __construct() {
		parent::__construct(
			'postinator', // Base ID
			__( 'Show content', 'postinator'), // Name
			array( 'description' => __( 'Pick me! I show content.', 'postinator' ), ) // Args
		);
	}

	/**
	 * Widget
	 */

	public function widget( $args, $instance )
	{
		// Get posts.
		$posts = array();
		$posts = new WP_Query( $posts );

		// No posts found.
		if ( ! $posts->have_posts() )
			return false;

		// Widget prefix.
		echo $args['before_widget'];

		// Loop through results.
		while( $posts->have_posts() )
		{
			$posts->the_post();
			get_template_part( 'content', get_post_type() );
		}

		// Widget suffix.
		echo $args['after_widget'];

		// Reset postdata to clean everything up.
		wp_reset_postdata();
	}

	/**
	 * Form
	 */

	public function form( $instance )
	{
	}

	/**
	 * Update
	 */

	public function update( $new_instance, $old_instance )
	{
	}

	// content (get/fields):
	// - post_types
	// - taxonomy_terms

	// extra's:
	// - sort
	// - limit

	// extend:
	// - before widget
	// - after widget

	// brainfarts:
	// - API
	// - Timber
	// - add fields
	// - translations
}

function postinatorInit() {
	register_widget( 'Postinator' );
}

add_action( 'widgets_init', 'postinatorInit' );

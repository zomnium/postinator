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
	 * Register widget
	 */

	function __construct() {
		parent::__construct(
			'postinator', // Base ID
			__( 'Show content', 'postinator'), // Name
			array( 'description' => __( 'Pick me! I show content.', 'postinator' ), ) // Args
		);
	}

	// functions
	// - widget
	// - form
	// - update

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

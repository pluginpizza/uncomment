<?php
/**
 * Functionality related to rewrite rules
 *
 * @package PluginPizza\Uncomment
 */

namespace PluginPizza\Uncomment\Rewrites;

// Remove rewrite rules for comment feed archives.
add_filter( 'comments_rewrite_rules', '__return_empty_array', 99 );

// Remove rewrite rules for the legacy comment feed and post type comment pages.
add_filter( 'rewrite_rules_array', __NAMESPACE__ . '\filter_rewrite_rules_array', 99 );

/**
 * Remove rewrite rules for the legacy comment feed and post type comment pages.
 *
 * @param array $rules The compiled array of rewrite rules.
 * @return array
 */
function filter_rewrite_rules_array( $rules ) {

	if ( is_array( $rules ) ) {

		// Remove the legacy comment feed rule.
		foreach ( $rules as $k => $v ) {
			if ( false !== strpos( $k, '|commentsrss2' ) ) {
				$new_k = str_replace( '|commentsrss2', '', $k );
				unset( $rules[ $k ] );
				$rules[ $new_k ] = $v;
			}
		}

		// Remove all other comment related rules.
		foreach ( $rules as $k => $v ) {
			if ( false !== strpos( $k, 'comment-page-' ) ) {
				unset( $rules[ $k ] );
			}
		}
	}

	return $rules;
}


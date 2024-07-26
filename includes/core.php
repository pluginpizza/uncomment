<?php
/**
 * Core plugin functionality
 *
 * @package PluginPizza\Uncomment
 */

namespace PluginPizza\Uncomment;

// Prevent running query in wp_count_comments().
add_filter( 'wp_count_comments', __NAMESPACE__ . '\filter_wp_count_comments' );

// Short-circuit WP_Comment_Query.
add_filter( 'comments_pre_query', __NAMESPACE__ . '\filter_comments_pre_query', 10, 2 );

// Remove post type support for comments and trackbacks.
add_action( 'registered_post_type', __NAMESPACE__ . '\remove_comment_support', 99 );

// Set comment and ping status to closed when running a \WP_Query.
add_filter( 'the_posts', __NAMESPACE__ . '\filter_post_comment_status', 99, 2 );

// Return 'closed' status when using comments_open() or pings_open().
add_filter( 'comments_open', __NAMESPACE__ . '\close_comments', 20, 2 );
add_filter( 'pings_open', __NAMESPACE__ . '\close_comments', 20, 2 );

// Remove comments from the admin bar.
add_action( 'admin_bar_menu', __NAMESPACE__ . '\remove_admin_bar_comment_items', 999 );
add_action( 'admin_bar_menu', __NAMESPACE__ . '\remove_admin_bar_network_comment_items', 999 );

// Replace the theme's or the core comments template with an empty one.
add_filter( 'comments_template', __NAMESPACE__ . '\comments_template' );

// Remove comments popup.
add_filter( 'query_vars', __NAMESPACE__ . '\filter_query_vars' );

// Replace core comment blocks output with an empty string.
add_filter( 'render_block', __NAMESPACE__ . '\replace_comment_blocks_output', 99, 2 );

/**
 * Prevent running a query in wp_count_comments().
 *
 * Also used by get_comment() and generally for a \WP_Comment_Query
 *
 * @param stdClass $count An object containing comment counts.
 */
function filter_wp_count_comments( $count ) {

	$count = (object) array(
		'approved'            => 0,
		'awaiting_moderation' => 0,
		'spam'                => 0,
		'trash'               => 0,
		'post-trashed'        => 0,
		'total_comments'      => 0,
		'moderated'           => 0,
		'all'                 => 0,
	);

	return $count;
}

/**
 * Short-circuit WP_Comment_Query.
 *
 * Most visible result is the removal of the comments section on the
 * Activity dashboard widget.
 *
 * @param array|int|null   $comment_data Return a non-null value to short-circuit WP's comment query.
 * @param WP_Comment_Query $query        The WP_Comment_Query instance, passed by reference.
 * @return [type] [description]
 */
function filter_comments_pre_query( $comment_data, $query ) {

	if ( is_a( $query, '\WP_Comment_Query' ) && $query->query_vars['count'] ) {
		return 0;
	}

	return array();
}

/**
 * Remove post type support for comments and trackbacks.
 *
 * @param string $post_type Post type.
 * @return void
 */
function remove_comment_support( $post_type ) {

	remove_post_type_support( $post_type, 'comments' );
	remove_post_type_support( $post_type, 'trackbacks' );
}

/**
 * Set comment and ping status to closed when running a \WP_Query.
 *
 * @param WP_Post  $posts Post data object.
 * @param WP_Query $query Query object.
 * @return array
 */
function filter_post_comment_status( $posts, $query ) {

	if ( ! empty( $posts ) && $query->is_singular() ) {
		$posts[0]->comment_status = 'closed';
		$posts[0]->ping_status    = 'closed';
	}

	return $posts;
}

/**
 * Close comments, if open.
 *
 * @param string|boolean $open Whether the current post is open for comments.
 * @param string|integer $post_id The post ID or WP_Post object.
 *
 * @return bool|string $open
 */
function close_comments( $open, $post_id ) {

	if ( ! $open ) {
		return $open;
	}

	$post = get_post( $post_id );

	// @see http://codex.wordpress.org/Option_Reference#Discussion
	if ( is_a( $post, '\WP_Post' ) ) {
		return false;
	}

	return $open;
}

/**
 * Remove comment items from the admin bar.
 *
 * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference.
 *
 * @return void
 */
function remove_admin_bar_comment_items( $wp_admin_bar ) {

	if ( ! is_admin_bar_showing() ) {
		return;
	}

	// Remove comment item in "My Sites" list.
	if ( isset( $GLOBALS['blog_id'] ) ) {
		$wp_admin_bar->remove_node( 'blog-' . $GLOBALS['blog_id'] . '-c' );
	}

	$wp_admin_bar->remove_node( 'comments' );
}

/**
 * Remove comment items from the network admin bar.
 *
 * @return void
 */
function remove_admin_bar_network_comment_items() {

	if ( ! is_admin_bar_showing() ) {
		return;
	}

	global $wp_admin_bar;

	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
	}

	if ( is_multisite() && is_plugin_active_for_network( UNCOMMENT_PLUGIN_BASENAME ) ) {

		foreach ( (array) $wp_admin_bar->user->blogs as $blog ) {
			$wp_admin_bar->remove_node( 'blog-' . $blog->userblog_id . '-c' );
		}
	}
}

/**
 * Replace the theme or core comments template with an empty one.
 *
 * @return string The path to the empty template file.
 */
function comments_template() {

	return UNCOMMENT_PLUGIN_DIR . 'includes/templates/comments.php';
}

/**
 * Remove comments popup.
 *
 * @see https://core.trac.wordpress.org/ticket/28617
 *
 * @param  array $public_query_vars The array of whitelisted query variables.
 * @return array
 */
function filter_query_vars( $public_query_vars ) {

	$key = array_search( 'comments_popup', $public_query_vars, true );

	if ( false !== $key ) {
		unset( $public_query_vars[ $key ] );
	}

	return $public_query_vars;
}

/**
 * Replace core comment blocks output with an empty string.
 *
 * We're using the 'render_block' filter instead of replacing the render_callback via the
 * 'register_block_type_args' filter. The latter allows us to replace the block content
 * but will still output the wrapping div.
 *
 * @param string $block_content The block content about to be appended.
 * @param array  $block         The full block, including name and attributes.
 * @return array
 */
function replace_comment_blocks_output( $block_content, $block ) {

	if ( isset( $block['blockName'] ) ) {

		$comment_block_names = \PluginPizza\Uncomment\Helpers\get_comment_block_names();

		if ( in_array( $block['blockName'], $comment_block_names, true ) ) {
			$block_content = '';
		}
	}

	return $block_content;
}

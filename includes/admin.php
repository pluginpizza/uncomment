<?php
/**
 * Functionality specific to the admin
 *
 * @package PluginPizza\Uncomment
 */

namespace PluginPizza\Uncomment\Admin;

// Remove admin pages.
add_action( 'admin_init', __NAMESPACE__ . '\remove_admin_pages' );

// Remove admin menu items.
add_action( 'admin_menu', __NAMESPACE__ . '\remove_menu_items' );

// Remove the commentsdiv meta box.
add_action( 'admin_init', __NAMESPACE__ . '\remove_commentsdiv_meta_box' );

// Remove "Turn comments on or off" from the Welcome Panel.
add_action( 'admin_head-index.php', __NAMESPACE__ . '\remove_welcome_panel_item' );

// Remove Keyboard Shortcuts options from the profile page.
add_action( 'load-profile.php', __NAMESPACE__ . '\remove_profile_items' );

// Remove the 'Discussion Settings' help tab from the post edit screen.
add_action( 'admin_head-post.php', __NAMESPACE__ . '\remove_help_tabs', 10, 3 );

// Unregister the core comment widget.
add_action( 'widgets_init', __NAMESPACE__ . '\unregister_comment_widget', 1 );

// Unregister the core comment widget styles.
add_filter( 'show_recent_comments_widget_style', '__return_false' );

// Remove core comment blocks from the inserter.
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\remove_comments_blocks_from_inserter' );

// Remove Akismet plugin "At a Glance" section.
add_action( 'rightnow_end', __NAMESPACE__ . '\akismet_remove_at_a_glance', 9 );

// Remove Akismet plugin "Set up your Akismet account" notice.
add_action( 'admin_notices', __NAMESPACE__ . '\akismet_remove_notice', 9 );

/**
 * Remove admin pages.
 *
 * @return void
 */
function remove_admin_pages() {

	global $pagenow;

	if ( in_array(
		$pagenow,
		array(
			'comment.php',
			'edit-comments.php',
			'moderation.php',
			'options-discussion.php',
		),
		true
	) ) {
		wp_die(
			esc_html__( 'Comments are closed.', 'default' ),
			'',
			array( 'response' => 403 )
		);
		exit();
	}
}

/**
 * Remove admin menu items.
 *
 * @return void
 */
function remove_menu_items() {

	remove_menu_page( 'edit-comments.php' );
	remove_submenu_page( 'options-general.php', 'options-discussion.php' );
}

/**
 * Remove the commentsdiv meta box.
 *
 * Removing post type support for comments removes the commentstatusdiv
 * and trackbacksdiv, but does not affect commentsdiv as it might still
 * be registered if $post->comment_count is bigger than 0.
 *
 * @todo Find a better place than admin_init to run this hook.
 *
 * @return void
 */
function remove_commentsdiv_meta_box() {

	foreach ( get_post_types() as $post_type ) {
		remove_meta_box( 'commentsdiv', $post_type, 'normal' );
	}
}

/**
 * Remove "Turn comments on or off" from the Welcome Panel.
 *
 * @return void
 */
function remove_welcome_panel_item() {

	$script = <<<TAG
(function(){
	document.addEventListener( 'DOMContentLoaded', function() {
		var el = document.querySelector( '.welcome-comments' );
		if ( el ) {
			el.parentNode.remove();
		}
	});
})();
TAG;

	wp_add_inline_script(
		'common',
		$script
	);
}

/**
 * Remove Keyboard Shortcuts options from the profile page.
 *
 * @return void
 */
function remove_profile_items() {

	wp_add_inline_style(
		'forms',
		'.user-comment-shortcuts-wrap{display:none;}'
	);
}

/**
 * Remove the 'Discussion Settings' help tab from the post edit screen.
 *
 * @return void
 */
function remove_help_tabs() {

	$current_screen = get_current_screen();

	if ( $current_screen->get_help_tab( 'discussion-settings' ) ) {
		$current_screen->remove_help_tab( 'discussion-settings' );
	}
}

/**
 * Unregister the core comment widget.
 *
 * @return void
 */
function unregister_comment_widget() {

	unregister_widget( 'WP_Widget_Recent_Comments' );
}

/**
 * Remove core comment blocks from the inserter.
 *
 * Note that instead of unregistering comment-related blocks via the unregisterBlockType() function,
 * we'll remove them from the block inserter to prevent adding comment blocks to a post.
 *
 * - Unregistering a block may lead to an error notice when editing a post that already contains a
 *   comment block. The Uncomment plugin aims to not touch existing content.
 * - Prevent possible race condition when trying to unregister blocks.
 *
 * @return void
 */
function remove_comments_blocks_from_inserter() {

	$comment_block_names = wp_json_encode( \PluginPizza\Uncomment\Helpers\get_comment_block_names() );

	$script = <<<TAG
(function(){
	var commentBlockNames = {$comment_block_names};
	wp.hooks.addFilter('blocks.registerBlockType', 'uncomment/exclude-blocks', function(settings, name) {
		if ( commentBlockNames.indexOf(name) !== -1 ) {
			return Object.assign({}, settings, {
				supports: Object.assign({}, settings.supports, {inserter: false})
			});
		}
		return settings;
	});
})();
TAG;

	wp_add_inline_script( 'wp-blocks', $script, 'after' );
}

/**
 * Remove Akismet plugin "At a Glance" section.
 *
 * Useful on multisite installs where Akismet is network activated or on VIP Go.
 *
 * @return void
 */
function akismet_remove_at_a_glance() {

	remove_action( 'rightnow_end', array( 'Akismet_Admin', 'rightnow_stats' ), 10 );
}

/**
 * Remove Akismet plugin "Set up your Akismet account" notice.
 *
 * Useful on multisite installs where Akismet is network activated or on VIP Go.
 *
 * @return void
 */
function akismet_remove_notice() {

	remove_action( 'admin_notices', array( 'Akismet_Admin', 'display_notice' ), 10 );
}

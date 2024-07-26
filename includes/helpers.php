<?php
/**
 * Contains plugin helper functions
 *
 * @package PluginPizza\Uncomment
 */

namespace PluginPizza\Uncomment\Helpers;

/**
 * Get the names of all core comment-related blocks.
 *
 * @return array
 */
function get_comment_block_names() {

	return array(
		'core/comment-author-name',
		'core/comment-content',
		'core/comment-date',
		'core/comment-edit-link',
		'core/comment-reply-link',
		'core/comment-template',
		'core/comments',
		'core/comments-pagination',
		'core/comments-pagination-next',
		'core/comments-pagination-numbers',
		'core/comments-pagination-previous',
		'core/comments-query-loop',
		'core/comments-title',
		'core/latest-comments',
		'core/post-comments',
		'core/post-comments-form',
	);
}

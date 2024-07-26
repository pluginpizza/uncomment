<?php
/**
 * Functionality related to XML-RPC endpoints
 *
 * @package PluginPizza\Uncomment
 */

namespace PluginPizza\Uncomment\XMLRPC;

// Replace xmlrpc methods.
add_filter( 'xmlrpc_methods', __NAMESPACE__ . '\replace_xmlrpc_methods' );

/**
 * Replace XML_RPC methods.
 *
 * @param array $methods An array of XML-RPC methods.
 *
 * @return array
 */
function replace_xmlrpc_methods( $methods ) {

	$comment_methods = array(
		'wp.getCommentCount',
		'wp.getComment',
		'wp.getComments',
		'wp.deleteComment',
		'wp.editComment',
		'wp.newComment',
		'wp.getCommentStatusList',
	);

	foreach ( $comment_methods as $method_name ) {

		if ( isset( $methods[ $method_name ] ) ) {
			$methods[ $method_name ] = __NAMESPACE__ . '\xmlrpc_placeholder_method';
		}
	}

	return $methods;
}

/**
 * XML_RPC placeholder method.
 *
 * @return \IXR_Error
 */
function xmlrpc_placeholder_method() {

	return new \IXR_Error(
		403,
		__( 'Comments are closed.', 'default' )
	);
}

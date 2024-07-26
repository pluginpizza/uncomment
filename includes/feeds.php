<?php
/**
 * Functionality related to feeds
 *
 * @package PluginPizza\Uncomment
 */

namespace PluginPizza\Uncomment\Feeds;

// Set the content of <wfw:commentRss> to an empty string.
add_filter( 'post_comments_feed_link', '__return_empty_string' );

// Set the content of <slash:comments> to zero.
add_filter( 'get_comments_number', '__return_zero' );

// Remove comment feed pingback HTTP headers.
add_filter( 'wp_headers', __NAMESPACE__ . '\filter_wp_headers' );

// Return an empty string for post comment link, which takes care of <comments>.
add_filter( 'get_comments_link', '__return_empty_string' );

// Remove comments feed link.
add_filter( 'feed_links_show_comments_feed', '__return_false' );

// Remove or replace extra feed links, removing the post comment feeds.
add_action(
	'init',
	function() {
		/*
		 * WordPress 6.1.0 introduces filters that allows us to specify whether
		 * to display the post comments feed link.
		 *
		 * @see https://core.trac.wordpress.org/changeset/54161
		 *
		 * For versions lower than 6.1.0 we'll replace the core feed_links_extra
		 * function with our own near-identical one.
		 */
		if ( version_compare( get_bloginfo( 'version' ), '6.1.0 ', '>=' ) ) {
			add_filter( 'feed_links_extra_show_post_comments_feed', '__return_false' );
			add_filter( 'feed_links_show_comments_feed', '__return_false' );
		} else {
			remove_action( 'wp_head', 'feed_links_extra', 3 );
			add_action( 'wp_head', __NAMESPACE__ . '\feed_links_extra', 3 );
		}
	}
);

// Redirect requests for a comment feed.
add_action( 'template_redirect', __NAMESPACE__ . '\filter_query', 9 );

/**
 * Remove comment feed pingback HTTP headers.
 *
 * @param array $headers The list of headers to be sent.
 * @return array $headers
 */
function filter_wp_headers( $headers ) {

	if ( array_key_exists( 'X-Pingback', $headers ) ) {
		unset( $headers['X-Pingback'] );
	}

	return $headers;
}

/**
 * Display the links to the extra feeds such as category feeds.
 *
 * Note that this is a near-clone of the core feed_links_extra() function from
 * WordPress core version 6.0.6, except that we remove the is_singular()
 * conditional, add the 'default' namespace to i18n functions and add additional
 * escaping.
 *
 * The core function will add the comment feed link if a post has existing
 * comments, which we cannot seem to circumvent without actually deleting
 * existing comments.
 *
 * @param array $args Optional arguments.
 */
function feed_links_extra( $args = array() ) {
	$defaults = array(
		/* translators: Separator between blog name and feed type in feed links. */
		'separator'     => _x( '&raquo;', 'feed link', 'default' ),
		/* translators: 1: Blog name, 2: Separator (raquo), 3: Post title. */
		'singletitle'   => __( '%1$s %2$s %3$s Comments Feed', 'default' ),
		/* translators: 1: Blog name, 2: Separator (raquo), 3: Category name. */
		'cattitle'      => __( '%1$s %2$s %3$s Category Feed', 'default' ),
		/* translators: 1: Blog name, 2: Separator (raquo), 3: Tag name. */
		'tagtitle'      => __( '%1$s %2$s %3$s Tag Feed', 'default' ),
		/* translators: 1: Blog name, 2: Separator (raquo), 3: Term name, 4: Taxonomy singular name. */
		'taxtitle'      => __( '%1$s %2$s %3$s %4$s Feed', 'default' ),
		/* translators: 1: Blog name, 2: Separator (raquo), 3: Author name. */
		'authortitle'   => __( '%1$s %2$s Posts by %3$s Feed', 'default' ),
		/* translators: 1: Blog name, 2: Separator (raquo), 3: Search query. */
		'searchtitle'   => __( '%1$s %2$s Search Results for &#8220;%3$s&#8221; Feed', 'default' ),
		/* translators: 1: Blog name, 2: Separator (raquo), 3: Post type name. */
		'posttypetitle' => __( '%1$s %2$s %3$s Feed', 'default' ),
	);

	$args = wp_parse_args( $args, $defaults );

	if ( is_post_type_archive() ) {
		$post_type = get_query_var( 'post_type' );
		if ( is_array( $post_type ) ) {
			$post_type = reset( $post_type );
		}

		$post_type_obj = get_post_type_object( $post_type );
		$title         = sprintf( $args['posttypetitle'], get_bloginfo( 'name' ), $args['separator'], $post_type_obj->labels->name );
		$href          = get_post_type_archive_feed_link( $post_type_obj->name );
	} elseif ( is_category() ) {
		$term = get_queried_object();

		if ( $term ) {
			$title = sprintf( $args['cattitle'], get_bloginfo( 'name' ), $args['separator'], $term->name );
			$href  = get_category_feed_link( $term->term_id );
		}
	} elseif ( is_tag() ) {
		$term = get_queried_object();

		if ( $term ) {
			$title = sprintf( $args['tagtitle'], get_bloginfo( 'name' ), $args['separator'], $term->name );
			$href  = get_tag_feed_link( $term->term_id );
		}
	} elseif ( is_tax() ) {
		$term = get_queried_object();

		if ( $term ) {
			$tax   = get_taxonomy( $term->taxonomy );
			$title = sprintf( $args['taxtitle'], get_bloginfo( 'name' ), $args['separator'], $term->name, $tax->labels->singular_name );
			$href  = get_term_feed_link( $term->term_id, $term->taxonomy );
		}
	} elseif ( is_author() ) {
		$author_id = (int) get_query_var( 'author' );

		$title = sprintf( $args['authortitle'], get_bloginfo( 'name' ), $args['separator'], get_the_author_meta( 'display_name', $author_id ) );
		$href  = get_author_feed_link( $author_id );
	} elseif ( is_search() ) {
		$title = sprintf( $args['searchtitle'], get_bloginfo( 'name' ), $args['separator'], get_search_query( false ) );
		$href  = get_search_feed_link();
	}

	if ( isset( $title ) && isset( $href ) ) {
		echo '<link rel="alternate" type="' . esc_attr( feed_content_type() ) . '" title="' . esc_attr( $title ) . '" href="' . esc_url( $href ) . '" />' . "\n";
	}
}

/**
 * Redirect requests for a comment feed.
 *
 * @return void
 */
function filter_query() {

	if ( ! is_comment_feed() ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['feed'] ) ) {
		wp_safe_redirect( remove_query_arg( 'feed' ), 301 );
		exit();
	}

	set_query_var( 'feed', '' );
}

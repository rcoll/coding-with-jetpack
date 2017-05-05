<?php

/**
 * Use Jetpack to get post IDs related to a provided post ID
 *
 * @param int $post_id The post ID to get related posts for
 * @param int $size Number of results to return
 *
 * @uses Jetpack_RelatedPosts
 * @uses Jetpack_RelatedPosts::init_raw()
 * @uses Jeptack_RelatedPosts::set_query_name()
 * @uses Jetpack_RelatedPosts::get_for_post_id()
 * @uses wp_cache_set()
 * @uses wp_cache_get()
 *
 * @return bool|array
 */
function cwj_get_related_posts_for( $post_id, $size = 3 ) {
	// Make sure we only run this if we have access to the Jetpack_RelatedPosts class and init_raw method
	if ( class_exists( 'Jetpack_RelatedPosts' ) && method_exists( 'Jetpack_RelatedPosts', 'init_raw' ) ) {
		// Create a cache key
		$cache_key = serialize( sprintf( "jprp-%d-%d", $post_id, $size ) );
		
		// Attempt to get from cache
		$related_posts = wp_cache_get( $cache_key, 'jprp' );

		if ( $related_posts ) {
			// Return cache if it exists
			return $related_posts;
		} else {
			// No cache, hit the WordPress.com API instead
			$related_posts = Jetpack_RelatedPosts::init_raw()
				->set_query_name( $cache_key )
				->get_for_post_id( $post_id, array( 'size' => $size ) );
		}

		// Save the results for future use
		wp_cache_set( $cache_key, $related_posts, 'jprp', 86400 );

		// Return the results
		return $related_posts;
	}

	// Jetpack_RelatedPosts class is missing
	return false;
}

// eof
<?php

// Inject your own results into the related posts results
function cwj_rp_featured( $hits, $post_id ) {
	// Remove the last result in the array
	array_pop( $hits )

	// Add an id set in a WP option
	$hits[] = array( 'id' => absint( get_option( 'jp_rp_featured_id' ) ) );

	// Return the new results
	return $hits;
}
add_filter( 'jetpack_relatedposts_filter_hits', 'cwj_rp_featured' );

// eof
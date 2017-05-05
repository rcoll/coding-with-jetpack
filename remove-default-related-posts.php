<?php

/**
 * Prevent the default Jetpack Related Posts widget from being inserted into post content
 *
 * @uses remove_filter()
 * @uses Jetpack_RelatedPosts
 */
function cwj_remove_jetpack_relatedposts() {
	if ( class_exists( 'Jetpack_RelatedPosts' ) ) {
		remove_filter( 'the_content', array( Jetpack_RelatedPosts::init(), 'filter_add_target_to_dom' ), 40 );
	}
}
add_filter( 'wp', 'cwj_remove_jetpack_relatedposts', 20 );
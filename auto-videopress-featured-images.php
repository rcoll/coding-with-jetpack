<?php

/**
 * Automatically add a featured image to any post that has a VideoPress video embed
 *
 * @param int $post_id The post ID of the post
 *
 * @uses has_post_thumbnail()
 * @uses get_post()
 * @uses has_shortcode()
 * @uses get_shortcode_regex()
 * @uses sanitize_text_field()
 * @uses cwj_get_videopress_poster_url()
 * @uses update_post_meta()
 * @uses wp_upload_dir()
 * @uses sanitize_file_name()
 * @uses wp_mkdir_p()
 * @uses trailingslashit()
 * @uses wp_check_filetype()
 * @uses wp_insert_attachment()
 * @uses wp_generate_attachment_metadata()
 * @uses wp_update_attachment_metadata()
 * @uses set_post_thumbnail()
 *
 * @return null
 */
function cwj_automatic_videopress_featured_image( $post_id ) {
	// Bail if this post has a thumbnail already
	if ( has_post_thumbnail( $post_id ) ) {
		return false;
	}

	// Get the post object
	$post = get_post( $post_id );

	// Bail if the post doesn't have a VideoPress video
	if ( ! has_shortcode( $post->post_content, 'wpvideo' ) ) {
		return false;
	}

	// Get the WP shortcode regex
	$pattern = get_shortcode_regex();

	// Match the first VideoPress shortcode
	preg_match( '/' . $pattern . '/s', $post->post_content, $matches );

	// Check to be sure the matches include what we need
	if ( is_array( $matches ) && isset( $matches[2] ) && 'wpvideo' == $matches[2] ) {
		// Get and sanitize the GUID
		$guid = sanitize_text_field( $matches[3] );

		// Get the VideoPress poster URL
		$poster_url = cwj_get_videopress_poster_url( $guid );

		// Add some metadata for future reference (not necessary)
		update_post_meta( $post->ID, '_cwj_videopress_guid', $guid );
		update_post_meta( $post->ID, '_cwj_videopress_poster_url', $poster_url );

		// Get the upload dir locations
		$upload_dir = wp_upload_dir();

		// Get the poster image contents
		$image_contents = @file_get_contents( $poster_url );

		// Create the new filename
		$filename = sanitize_file_name( basename( $guid . '.jpg' ) );

		// Create the upload dir if needed and get the full file path
		if ( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = trailingslashit( $upload_dir['path'] ) . $filename;
		} else {
			$file = trailingslashit( $upload_dir['basedir'] ) . $filename;
		}

		// Write the file
		@file_put_contents( $file, $image_contents );

		// Get the mime type of the file
		$mimetype = wp_check_filetype( $filename, null );

		// Create attachment metadata array
		$attachment = array(
			'post_mime_type' => $mimetype['type'],
			'post_title' => $filename,
			'post_content' => '',
			'post_status' => 'inherit',
		);

		// Insert the attachment into the database
		$attachment_id = wp_insert_attachment( $attachment, $file, $post_id );

		// Be sure to include the image functions
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		// Generate the metadata for the attachment
		$attachment_metadata = wp_generate_attachment_metadata( $attachment_id, $file );

		// Store the metadata for the attachment
		wp_update_attachment_metadata( $attachment_id, $attachment_metadata );

		// Finally, set the post thumbnail
		set_post_thumbnail( $post_id, $attachment_id );
	}
}
add_action( 'save_post', 'cwj_automatic_videopress_featured_image' );

// eof
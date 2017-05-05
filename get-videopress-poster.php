<?php

/**
 * Get a VideoPress poster image based on a VideoPress Video GUID
 *
 * @param string $guid A VideoPress GUID
 *
 * @uses sanitize_text_field()
 * @uses VideoPress_Video
 * @uses esc_url_raw()
 *
 * @return mixed False on failure, URL on success
 */
function cwj_get_videopress_poster_url( $guid ) {
	// Can't be too careful
	$guid = sanitize_text_field( $guid );

	// Include VideoPress_Video class if not loaded
	if ( ! class_exists( 'VideoPress_Video' ) ) {
		$file_path = ABSPATH . 'wp-content/plugins/jetpack/modules/videopress/class.videopress-video.php';

		if ( ! file_exists( $file_path ) ) {
			return false;
		}

		require_once( $file_path );
	}

	// Create the video object from the GUID
	$video = new VideoPress_Video( $guid );

	// Most likely a bad GUID
	if ( is_null( $video->videos ) ) {
		return false;
	}

	// Return the poster URL if we can
	if ( property_exists( $video, 'poster_frame_uri' ) ) {
		return esc_url_raw( $video->poster_frame_uri );
	}

	// Something funky happened
	return false;
}
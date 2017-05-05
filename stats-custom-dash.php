<?php

function cwj_register_menu_page() {
	add_menu_page(
		__( 'Jetpack Custom Dash', 'cwj' ),
		__( 'Jetpack Rocks!', 'cwj' ),
		'manage_options',
		'jp-custom-dash',
		'cwj_custom_dash', '', 2
	);
}
add_action( 'admin_menu', 'cwj_register_menu_page' );

function cwj_custom_dash() {
	$stats = stats_get_from_restapi();
	
	echo sprintf( '
		<table>
			<tr><td>Total Views</td><td>%s</td></tr>
			<tr><td>Total Visitors</td><td>%s</td></tr>
			<tr><td>Views Today</td><td>%s</td></tr>
			<tr><td>Visitors Today</td><td>%s</td></tr>
			<tr><td>Views Best Day</td><td>%s</td></tr>
			<tr><td>Views Best Day Total</td><td>%s</td></tr>
		</table>', 
		number_format( $stats->stats->views ),
		number_format( $stats->stats->visitors ),
		number_format( $stats->stats->views_today ),
		number_format( $stats->stats->visitors_today ),
		date( 'F j, Y', strtotime( $stats->stats->views_best_day ) ),
		number_format( $stats->stats->views_best_day_total )
	);
}

// eof

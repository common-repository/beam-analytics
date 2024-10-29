<?php
/**
 * Promotional functions to get users to upgrade.
 *
 * @package WPSBeamAnalytics\promos
 * @version 1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Show the upgrade notice in header.
 */
function wps_beam_analytics_header_upgrade() {
	echo '<a href="https://www.wpsunshine.com/plugins/beam-analytics/?utm_source=plugin&utm_medium=button&utm_content=upgrade&utm_campaign=plugin_upgrade" target="_blank" class="wps-button" id="wps-beam-analytics-header-upgrade">' . __( 'Upgrade to premium!', 'beam-analytics' ) . '</a>';
}
add_action( 'wps_beam_analytics_header', 'wps_beam_analytics_header_upgrade' );

/**
 * Show the upgrade notice when viewing the options page.
 */
function wps_beam_analytics_options_upgrade() {
	?>
	<div class="wps-beam-analytics-upgrade">
		<h2>Upgrade for more features!</h2>
		<ul>
			<li>Create custom event tracking without code: Scroll length, time on page, custom click events, and more.</li>
			<li>Integrate with e-commerce plugins to automatically track purchases and products:
				<ul>
					<li>WooCommerce</li>
					<li>Easy Digital Downloads</li>
					<li>Restrict Content Pro</li>
					<li>Sunshine Photo Cart</li>
					<li>WP Simple Pay</li>
				</ul>
			</li>
			<li>Integrate with forms plugins to track form submissions:
				<ul>
					<li>Gravity Forms</li>
					<li>Contact Form 7</li>
					<li>WP Forms</li>
					<li>WS Form</li>
					<li>Ninja Forms</li>
					<li>Formidable Forms</li>
					<li>Forminator</li>
				</ul>
			</li>
			<li>Integrate with LMS plugins to track sign ups:
				<ul>
					<li>LifterLMS</li>
				</ul>
			</li>
		</ul>
		<p><a href="https://www.wpsunshine.com/plugins/beam-analytics/?utm_source=plugin&utm_medium=banner&utm_content=upgrade&utm_campaign=plugin_upgrade" target="_blank" class="wps-button" id="wps-beam-analytics-upgrade"><?php _e( 'Upgrade Now!', 'beam-analytics' ); ?></a></p>
	</div>
	<?php
}
add_action( 'wps_beam_analytics_options_before', 'wps_beam_analytics_options_upgrade' );

/**
 * Request a review notice.
 */
function wps_beam_analytics_review_request() {
	$review = WPS_Beam_Analytics()->get_option( 'review' );
	if ( ! empty( $review ) && 'dismissed' == $review ) {
		return;
	}
	if ( empty( WPS_Beam_Analytics()->get_option( 'install_time' ) ) ) {
		WPS_Beam_Analytics()->update_option( 'install_time', time() );
	}
	if ( ( time() - WPS_Beam_Analytics()->get_option( 'install_time' ) ) < DAY_IN_SECONDS * 15 ) {
		return;
	}
	?>
		<div class="notice notice-info is-dismissable" id="wps-beam-analytics-review">
			<p>You having been using Beam Analytics by WP Sunshine for a bit and that's awesome! Could you please do a big favor and give it a review on WordPress?  Reviews from users like you really help our plugins to grow and continue to improve.</p>
			<p>- Derek, WP Sunshine Lead Developer</p>
			<p><a href="https://wordpress.org/support/view/plugin-reviews/beam-analytics?filter=5#postform" target="_blank" class="button-primary wps-beam-analytics-review-dismiss-button">Sure thing!</a> &nbsp; <a href="#" class="button wps-beam-analytics-review-dismiss-button">No thanks</a>
		</div>
		<script>
			jQuery( document ).on( 'click', '.wps-beam-analytics-review-dismiss-button', function() {
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'wps_beam_analytics_dismiss_review',
					},
					success: function( data, textStatus, jqXHR ) {
						jQuery( '#wps-beam-analytics-review' ).remove();
					}
				});
			});
		</script>
	<?php
}
add_action( 'admin_notices', 'wps_beam_analytics_review_request' );

/**
 * Processes the dismiss notice for the review.
 */
function wps_beam_analytics_review_dismiss() {
	WPS_Beam_Analytics()->update_option( 'review', 'dismissed' );
	wp_die();
}
add_action( 'wp_ajax_wps_beam_analytics_dismiss_review', 'wps_beam_analytics_review_dismiss' );

?>

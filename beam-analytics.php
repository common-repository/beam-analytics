<?php
/**
 * Plugin Name: Beam Analytics
 * Plugin URI: https://www.wpsunshine.com/plugins/beam-analytics
 * Description: Connect your Beam Analytics account to your WordPress site
 * Version: 1.0.1
 * Author: WP Sunshine
 * Author URI: https://www.wpsunshine.com
 * Text Domain: beam-analytics
 *
 * @package WPSBeamAnalytics
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WPS_BEAM_ANALYTICS_VERSION' ) ) {

	define( 'WPS_BEAM_ANALYTICS_VERSION', '1.0.1' );
	define( 'WPS_BEAM_ANALYTICS_NAME', 'Beam Analytics' );
	define( 'WPS_BEAM_ANALYTICS_PLUGIN_FILE', __FILE__ );
	define( 'WPS_BEAM_ANALYTICS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	define( 'WPS_BEAM_ANALYTICS_ABSPATH', dirname( __FILE__ ) );

	include_once WPS_BEAM_ANALYTICS_ABSPATH . '/includes/class-beam-analytics.php';

	/**
	 * Returns the main instance of WPSunshine_Beam_Analytics.
	 *
	 * @since  1.0
	 * @return WPSunshine_Beam_Analytics
	 */
	function WPS_Beam_Analytics() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
		return WPSunshine_Beam_Analytics::instance();
	}

	/**
	 * Sets WPS_Beam_Analytics to global variable.
	 *
	 * @since  1.0
	 */
	function wps_beam_analytics_load_me() {
		$GLOBALS['wps_beam_analytics'] = WPS_Beam_Analytics();
	}
	add_action( 'plugins_loaded', 'wps_beam_analytics_load_me' );

	function wps_beam_analytics_activate() {
		$options = get_option( 'wps_beam_analytics' );
		$options_default = array(
			'roles' => array(
				'administrator',
			),
			'environments' => array(
				'development',
				'staging',
				'local',
			),
		);
		$options = wp_parse_args( $options, $options_default );
		update_option( 'wps_beam_analytics', $options );
	}
	register_activation_hook( __FILE__, 'wps_beam_analytics_activate' );

}

<?php
/**
 * WPSunshine_Beam_Analytics
 *
 * Main Beam Analytics instance class
 *
 * @package WPSBeamAnalytics\Classes
 * @version 1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WPSunshine_Beam_Analytics class.
 */
class WPSunshine_Beam_Analytics {

	/**
	 * Contains an array of cart items.
	 *
	 * @var class|WPSunshine_Beam_Analytics
	 */
	protected static $_instance = null;

	/**
	 * User entered options.
	 *
	 * @var array
	 */
	private $options = array();

	/**
	 * Available addons.
	 *
	 * @var array
	 */
	private $addons = array();

	/**
	 * Gets the WPSunshine_Beam_Analytics instance.
	 *
	 * @return class|WPSunshine_Beam_Analytics Instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor to get initial options.
	 */
	public function __construct() {

		$options = get_option( 'wps_beam_analytics' );
		if ( ! empty( $options ) ) {
			$this->options = $options;
		}

		$this->includes();
		$this->init_hooks();

	}

	/**
	 * Include needed files.
	 */
	private function includes() {

		if ( is_admin() ) {
			include_once WPS_BEAM_ANALYTICS_ABSPATH . '/includes/admin/class-options.php';
			include_once WPS_BEAM_ANALYTICS_ABSPATH . '/includes/admin/promos.php';
		} 

	}

	/**
	 * Setup init hook.
	 */
	private function init_hooks() {

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 1 );
		add_filter( 'script_loader_tag', array( $this, 'custom_script_tag' ), 10, 2 );

	}

	/**
	 * Get enabled addons.
	 */
	public function get_addons() {
		$addons = apply_filters( 'wps_beam_analytics_addons', array() );
		return $addons;
	}

	/**
	 * Register a new addon.
	 */
	public function register_addon( $type, $key, $name ) {
		$this->addons[ $type ][ $key ] = $name;
	}

	/**
	 * Get options.
	 */
	public function get_options( $force = false ) {
		if ( empty( $this->options ) || $force ) {
			$this->options = get_option( 'wps_beam_analytics' );
		}
		return apply_filters( 'wps_beam_analytics_options', $this->options );
	}

	/**
	 * Get an option by key.
	 *
	 * @param string $key Key to get option value for.
	 */
	public function get_option( $key ) {
		if ( ! empty( $this->options[ $key ] ) ) {
			return $this->options[ $key ];
		}
		return false;
	}

	/**
	 * Set an option by key.
	 *
	 * @param string $key Key to set option value for.
	 * @param string $value Value to set option to.
	 */
	public function update_option( $key, $value ) {
		$this->options[ $key ] = $value;
		update_option( 'wps_beam_analytics', $this->options );
	}

	/**
	 * Register the needed JS scripts.
	 */
	public function enqueue_scripts() {

		if ( $this->can_track() ) {
			wp_enqueue_script( 'beam-analytics', 'https://beamanalytics.b-cdn.net/beam.min.js', '', WPS_BEAM_ANALYTICS_VERSION );
		}

	}

	function custom_script_tag( $tag, $handle ) {
  		if ( $handle == 'beam-analytics' ) {
			$token = $this->get_option( 'token' );
    		$tag = str_replace( ' src=', ' async data-token="' . esc_attr( $token ) . '" src=', $tag );
  		}
  		return $tag;
	}

	private function can_track() {
		$allow = true;

		// Check for disallowed roles.
		$disallowed_roles = $this->get_option( 'roles' );
		if ( is_user_logged_in() && ! empty( $disallowed_roles ) ) {
			$user = wp_get_current_user();
			$user_roles = ( array ) $user->roles;
			foreach ( $user_roles as $user_role ) {
				if ( in_array( $user_role, $disallowed_roles ) ) {
					$allow = false;
				}
			}
		}

		// Disable for selected environments
		$environments = $this->get_option( 'environments' );
		if ( ! empty( $environments ) && in_array( wp_get_environment_type(), $environments ) ) {
			$allow = false;
		}

		return $allow;
		
	}

	public function get_view( $value, $onload = true, $script = true ) {
		if ( $this->can_track() ) {
			$view = esc_js( WPS_Beam_Analytics()->get_option( 'prefix' ) ) . '/' . esc_js( $value );
			$code = 'window.beam("/' . esc_js( WPS_Beam_Analytics()->get_option( 'prefix' ) ) . '/' . esc_js( $value ) . '");';

			if ( $onload ) {
				$code = 'window.addEventListener( "load", function() { ' . $code . ' } );';
			}

			if ( $script ) {
				$code = '<script class="wps-beam-analytics-event">' . $code . '</script>';
			}

			return $code;
		}
		return false;		
	}

	public function render_view( $value, $onload = true, $script = true ) {
		echo $this->get_view( $value, $onload, $script );
	}

}

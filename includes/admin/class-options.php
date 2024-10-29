<?php
/**
 * WPSunshine_Beam_Analytics_Options
 *
 * @package WPSBeamAnalytics\Classes
 * @version 1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WPSunshine_Beam_Analytics_Options class.
 */
class WPSunshine_Beam_Analytics_Options {

	/**
	 * Array of notices based on user interactions.
	 *
	 * @var array
	 */
	protected static $notices = array();

	/**
	 * Array of errors based on user interactions.
	 *
	 * @var array
	 */
	protected static $errors = array();

	/**
	 * Plugin settings main navigation tabs.
	 *
	 * @var array
	 */
	private $tabs;

	/**
	 * Current settings navigation active tab.
	 *
	 * @var array
	 */
	private $tab;

	/**
	 * Constructor setup all needed hooks.
	 */
	public function __construct() {

		// Add settings page.
		add_action( 'admin_menu', array( $this, 'options_page_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wps_beam_analytics_header_links', array( $this, 'header_links' ) );

		// Tabs.
		add_action( 'admin_init', array( $this, 'set_tabs' ) );

		// Show settings.
		add_action( 'wps_beam_analytics_options_tab_options', array( $this, 'options_tab' ) );

		// Save settings.
		add_action( 'admin_init', array( $this, 'save_options' ) );
		add_action( 'wps_beam_analytics_save_tab_options', array( $this, 'save_options_tab' ), 1, 2 );
		add_action( 'admin_notices', array( $this, 'show_notices' ) );

	}

	/**
	 * Create Settings page menu.
	 */
	public function options_page_menu() {
		add_options_page( __( 'Beam Analytics', 'beam-analytics' ), __( 'Beam Analytics', 'beam-analytics' ), 'manage_options', 'wps_beam_analytics', array( $this, 'options_page' ) );
	}

	/**
	 * Enqueue scripts for admin.
	 */
	public function admin_enqueue_scripts() {

		if ( isset( $_GET['page'] ) && 'wps_beam_analytics' == $_GET['page'] ) {
			wp_enqueue_style( 'beam-analytics-admin', WPS_BEAM_ANALYTICS_PLUGIN_URL . 'assets/css/admin.css', false, WPS_BEAM_ANALYTICS_VERSION );
		}

	}

	/**
	 * Setup plugin admin screen header resource links.
	 *
	 * @param array $links Array of links to include in the header on plugin settings page
	 */
	public function header_links( $links ) {
		$links = array(
			'documentation' => array(
				'url'   => 'https://wpsunshine.com/support/',
				'label' => 'Documentation',
			),
			'review'        => array(
				'url'   => 'https://wordpress.org/support/plugin/beam-analytics/reviews/#new-post',
				'label' => 'Write a Review',
			),
			'feedback'      => array(
				'url'   => 'https://wpsunshine.com/feedback',
				'label' => 'Feedback',
			),
			'upgrade'       => array(
				'url'   => 'https://wpsunshine.com/plugins/beam-analytics/',
				'label' => 'Upgrade',
			),
		);
		return $links;
	}

	/**
	 * Return if we are running the Premium version of this plugin.
	 */
	public function is_premium() {
		return apply_filters( 'wps_beam_analytics_premium', false );
	}

	/**
	 * Get available tabs and set the current.
	 */
	public function set_tabs() {
		$this->tabs = apply_filters( 'wps_beam_analytics_tabs', array( 'options' => __( 'Options', 'beam-analytics' ) ) );
		$this->tab  = array_key_first( $this->tabs );
		if ( isset( $_GET['tab'] ) ) {
			$this->tab = sanitize_key( $_GET['tab'] );
		}
	}

	/**
	 * Display options page.
	 */
	public function options_page() {
		$options = WPS_Beam_Analytics()->get_options( true );
		?>
		<div id="wps-aa-admin">

			<div class="wps-header">
				<a href="https://www.wpsunshine.com/?utm_source=plugin&utm_medium=link&utm_campaign=beam-analytics" target="_blank" class="wps-logo"><img src="<?php echo esc_url( WPS_BEAM_ANALYTICS_PLUGIN_URL ); ?>/assets/images/logo.svg" alt="Beam Analytics for WordPress by WP Sunshine" /></a>

				<?php
				$header_links = apply_filters( 'wps_beam_analytics_header_links', array() );
				if ( ! empty( $header_links ) ) {
					echo '<div id="wps-header-links">';
					foreach ( $header_links as $key => $link ) {
						echo '<a href="' . esc_url( $link['url'] ) . '?utm_source=plugin&utm_medium=link&utm_campaign=beam-analytics" target="_blank" class="wps-header-link--' . esc_attr( $key ) . '">' . esc_html( $link['label'] ) . '</a>';
					}
					echo '</div>';
				}
				?>

				<?php if ( count( $this->tabs ) > 1 ) { ?>
				<nav class="wps-options-menu">
					<ul>
						<?php foreach ( $this->tabs as $key => $label ) { ?>
							<li
							<?php
							if ( $this->tab == $key ) {
								?>
  									class="wps-options-active"<?php } ?>><a href="<?php echo admin_url( 'options-general.php?page=wps_beam_analytics&tab=' . esc_attr( $key ) ); ?>"><?php echo esc_html( $label ); ?></a></li>
						<?php } ?>
					</ul>
				</nav>
				<?php } ?>

			</div>

			<div class="wrap wps-wrap">
				<h2></h2>
				<form method="post" action="<?php echo admin_url( 'options-general.php?page=wps_beam_analytics&tab=' . $this->tab ); ?>">
				<?php wp_nonce_field( 'wps_beam_analytics_options', 'wps_beam_analytics_options' ); ?>

				<?php do_action( 'wps_beam_analytics_options_before', $options, $this->tab ); ?>

				<?php do_action( 'wps_beam_analytics_options_tab_' . $this->tab, $options ); ?>

				<?php do_action( 'wps_beam_analytics_options_after', $options, $this->tab ); ?>

				<p id="wps-settings-submit">
					<input type="submit" value="<?php _e( 'Save Changes', 'beam-analytics' ); ?>" class="button button-primary" />
				</p>

				</form>
			</div>

		</div>
		<?php
	}

	/**
	 * Get available tabs and set the current.
	 */
	public function options_tab( $options ) {
	?>

		<table class="form-table" id="">
			<tr>
				<th><?php _e( 'Token', 'beam-analytics' ); ?></th>
				<td>
					<input type="text" name="token" size="50" value="<?php echo esc_attr( $options['token'] ); ?>" required /><br />
					<span class="wps-description"><a href="https://beamanalytics.io/dashboard/onboarding" target="_blank"><?php _e( 'Get your token here', 'beam-analytics' ); ?></a></span>
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Disable for Roles', 'beam-analytics' ); ?></th>
				<td>
					<?php
					$roles = wp_roles();
					foreach ( $roles->roles as $key => $role ) {
					?>
						<label><input type="checkbox" name="roles[]" value="<?php echo esc_attr( $key ); ?>" <?php checked( ! empty( $options['roles'] ) && in_array( $key, $options['roles'] ) ); ?> /> <?php esc_html_e( $role['name'] ); ?></label><br />
					<?php } ?>
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Disable for Environments', 'beam-analytics' ); ?></th>
				<td>
					<?php
					$environments = array(
						'production' => __( 'Production', 'beam-analytics' ),
						'development' => __( 'Development', 'beam-analytics' ),
						'staging' => __( 'Staging', 'beam-analytics' ),
						'local' => __( 'Local', 'beam-analytics' ),
					);
					foreach ( $environments as $key => $environment ) {
					?>
						<label><input type="checkbox" name="environments[]" value="<?php echo esc_attr( $key ); ?>" <?php checked( ! empty( $options['environments'] ) && in_array( $key, $options['environments'] ) ); ?> /> <?php esc_html_e( $environment ); ?></label><br />
					<?php } ?>
				</td>
			</tr>

		</table>

		<?php
	}

	/**
	 * Save options based on which tab we are viewing.
	 */
	public function save_options() {

		if ( ! isset( $_POST['wps_beam_analytics_options'] ) ) {
			return;
		}

		$nonce = sanitize_text_field( $_POST['wps_beam_analytics_options'] );

		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'wps_beam_analytics_options' ) ) {
			return;
		}

		$options = get_option( 'wps_beam_analytics' );
		if ( empty( $options ) ) {
			$options = array();
		}

		$options = apply_filters( 'wps_beam_analytics_save_tab_' . $this->tab, $options );

		// If all valid.
		if ( count( self::$errors ) > 0 ) {
			foreach ( self::$errors as $error ) {
				$this->add_notice( $error, 'error' );
			}
		} else {
			update_option( 'wps_beam_analytics', $options );
			$this->add_notice( __( 'Settings saved!', 'beam-analytics' ) );
		}

	}

	/**
	 * Save options for the options tab.
	 */
	public function save_options_tab( $options ) {
		$options['token'] = isset( $_POST['token'] ) ? sanitize_text_field( $_POST['token'] ) : '';
		$options['roles'] = isset( $_POST['roles'] ) ? array_map( 'sanitize_text_field', $_POST['roles'] ) : '';
		$options['environments'] = isset( $_POST['environments'] ) ? array_map( 'sanitize_text_field', $_POST['environments'] ) : '';
		return $options;
	}

	/**
	 * Add a notice to be shown after action such as save option.
	 */
	public function add_notice( $text, $type = 'success' ) {
		self::$notices[] = array(
			'text' => $text,
			'type' => $type,
		);
	}

	/**
	 * Output/show the notices.
	 */
	public function show_notices() {
		if ( ! empty( self::$notices ) ) {
			foreach ( self::$notices as $notice ) {
				echo '<div class="notice notice-' . esc_attr( $notice['type'] ) . '"><p>' . wp_kses_post( $notice['text'] ) . '</p></div>';
			}
		}
	}

}

new WPSunshine_Beam_Analytics_Options();

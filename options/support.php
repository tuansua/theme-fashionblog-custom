<?php

class MTS_Options_Tab_Support {

	protected static $instance = null;

	function __construct() {

		add_action( 'nhp-opts-enqueue', array( $this, 'enqueue_scripts' ) );
		add_filter( 'nhp-opts-extra-tabs', array( $this, 'add_support_tab' ) );
		add_action( 'wp_ajax_mts_get_debug_log', array( $this, 'get_debug_log' ) );
	}

	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function enqueue_scripts() {

		wp_enqueue_script(
			'nhp-clipboard',
			NHP_OPTIONS_URL.'/js/clipboard.min.js', 
			array('jquery'),
			MTS_THEME_VERSION,
			true
		);

		wp_enqueue_script(
			'nhp-help',
			NHP_OPTIONS_URL.'/js/help.js', 
			array( 'jquery', 'nhp-clipboard', 'nhp-opts-js' ),
			MTS_THEME_VERSION,
			true
		);
	}

	public function add_support_tab( $sections ) {

		$sections['support'] = array(
			'icon' => 'fa fa-life-ring',
			'title' => __('Support', 'mythemeshop' ),
			'desc' => '<p class="description">' . 
			/* Translators: %s expands to "our Support Forums" link */
			sprintf( __( 'We offer technical support through our %s. Please <strong>copy and paste the following information in your ticket</strong> when contacting support:' , 'mythemeshop' ), '<a href="https://community.mythemeshop.com/forum/7-theme-support/" target="_blank">' . __( 'Support Ticket System', 'mythemeshop' ) . '</a>' )
			 . '</p>',
			'callback' => array( $this, 'add_support_tab_content' )
		);

		return $sections;
	}

	public function add_support_tab_content() {
		?>
		<div class="mts-options-extra-tab-content">
			<?php $this->support_buttons(); ?>

			<textarea class="large-text" id="mts-debug-data-field" rows="16" readonly="readonly" disabled><?php _e( 'Generating log. Please wait...', 'mythemeshop' ); ?></textarea>

			<?php $this->support_buttons(); ?>
		</div>
		<?php
	}

	public function get_debug_log() {
		echo $this->debug_data_output();
		die();
	}

	public function support_buttons() {
		?>
		<div class="mts-help-buttons">
			<button type="button" class="button mts-support-copy" disabled>
				<!-- <span class="dashicons dashicons-clipboard"></span> -->
				<?php _e( 'Copy Data for Support Request', 'mythemeshop' ); ?>
			</button> 
			<a href="https://community.mythemeshop.com/forum/7-theme-support/" target="_blank" class="button button-primary mts-support-link">
				<!-- <span class="dashicons dashicons-external"></span> -->
				<?php _e( 'Open Support Forum', 'mythemeshop' ); ?>
			</a>
		</div>
		<?php
	}

	public function debug_data_output() {
		$data = $this->get_debug_data();
		$output = "`\n";
		foreach ( $data as $section_id => $section_data ) {
			$output .= $this->debug_section_output( $section_data );
		}
		$output = trim( $output );
		$output .= "\n`";
		return $output;
	}

	public function debug_section_output( $debug_section ) {
		$output = '';

		$output .= '--- ' . $debug_section['title'] . ' ---' . "\n";
		$output .= $this->debug_data_prettify( $debug_section['data'] );

		$output .= "\n";
		return $output;
	}

	public function debug_data_prettify( $data, $level = 0 ) {
		$output = '';
		$pad_to = 0;
		foreach ( $data as $key => $value ) {
			$pad_to = max( $pad_to, strlen( $key ) + 2 );
		}
		foreach ( $data as $key => $value ) {
			$key = str_replace( '_', ' ', $key );
			$key = ucwords( $key );
			$key = str_replace( 'Wp ', 'WP ', $key );

			if ( is_array( $value ) ) {
				$output .= str_repeat( ' ', $level * 2 ) . "$key: \n";
				$output .= $this->debug_data_prettify( $value, $level + 1 );
				continue;
			}
			if ( $value === true ) {
				$value = 'Yes';
			} elseif ( $value === false ) {
				$value = 'No';
			}
			$output .= str_repeat( ' ', $level * 2 );
			$output .= str_pad( $key . ': ', $pad_to );
			$output .= "{$value}\n";
		}
		return $output;
	}

	/**
	 *
	 * @return array
	 */
	public function get_debug_data() {
		$data = array();
		$data['environment'] = array(
			'title' => __( 'Environment', 'mythemeshop' ),
			'data' => $this->get_environment_info(),
		);

		$data['wp'] = array(
			'title' => __( 'WordPress', 'mythemeshop' ),
			'data' => $this->get_wp_info(),
		);

		$data['plugins'] = array(
			'title' => __( 'Plugins', 'mythemeshop' ),
			'data' => $this->get_plugins_info(),
		);

		$data['themes'] = array(
			'title' => __( 'Theme', 'mythemeshop' ),
			'data' => $this->get_theme_info(),
		);

		$data['settings'] = array(
			'title' => __( 'Theme Settings', 'mythemeshop' ),
			'data' => $this->get_theme_settings(),
		);

		return apply_filters( 'mts_support_log', $data );
	}

	public function get_environment_info() {
		global $wpdb;
		// WP memory limit
		// Figure out cURL version, if installed.
		$curl_version = '';
		if ( function_exists( 'curl_version' ) ) {
			$curl_version = curl_version();
			$curl_version = $curl_version['version'] . ', ' . $curl_version['ssl_version'];
		}

		return array(
			'server_info'               => $_SERVER['SERVER_SOFTWARE'],
			'php_version'               => phpversion(),
			'php_post_max_size'         => $this->let_to_num( ini_get( 'post_max_size' ) ),
			'php_max_execution_time'    => ini_get( 'max_execution_time' ),
			'php_max_input_vars'        => ini_get( 'max_input_vars' ),
			'curl_version'              => $curl_version,
			'max_upload_size'           => wp_max_upload_size(),
			'mysql_version'             => ( ! empty( $wpdb->is_mysql ) ? $wpdb->db_version() : '' ),
			'default_timezone'          => date_default_timezone_get(),
			'fsockopen_or_curl_enabled' => ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ),
			'domdocument_enabled'       => class_exists( 'DOMDocument' ),
			'simplexml_enabled' 		=> extension_loaded( 'SimpleXML' ),
			'gd_extension'              => extension_loaded( 'gd' ) && function_exists( 'gd_info' ),
			'imagick_extension'         => extension_loaded( 'imagick' ),
		);
	}

	public function get_wp_info() {
		$wp_memory_limit = $this->let_to_num( WP_MEMORY_LIMIT );
		if ( function_exists( 'memory_get_usage' ) ) {
			$wp_memory_limit = max( $wp_memory_limit, $this->let_to_num( @ini_get( 'memory_limit' ) ) );
		}

		return array(
			'home_url'                  => get_option( 'home' ),
			'site_url'                  => get_option( 'siteurl' ),
			'secure_connection' 		=> is_ssl(),
			'hide_errors'      			=> ! ( defined( 'WP_DEBUG' ) && defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG && WP_DEBUG_DISPLAY ) || 0 === intval( ini_get( 'display_errors' ) ),
			'wp_version'                => get_bloginfo( 'version' ),
			'wp_multisite'              => is_multisite(),
			'wp_memory_limit'           => $wp_memory_limit,
			'wp_debug_mode'             => ( defined( 'WP_DEBUG' ) && WP_DEBUG ),
			'wp_cron'                   => ! ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ),
			'language'                  => get_locale(),
		);
	}

	public function get_plugins_info() {
		$out = array();
		$plugins = $this->get_active_plugins();
		foreach ( $plugins as $key => $value ) {
			$name = $value['name'];
			if ( isset( $value['known_conflict'] ) && $value['known_conflict'] ) {
				$name = '(!) ' . $name;
			}
			$out[] = $name . ' v' . $value['version'] . ' ' . ( empty( $value['author_url'] ) ? $value['author_name'] : $value['author_url'] );
		}
		return $out;
	}

	public function get_active_plugins() {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		// Get both site plugins and network plugins
		$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
			$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
		}

		$active_plugins_data = array();

		foreach ( $active_plugins as $plugin ) {
			$data           = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
			$dirname        = dirname( $plugin );
			$version_latest = '';
			$slug           = explode( '/', $plugin );
			$slug           = explode( '.', end( $slug ) );
			$slug           = $slug[0];

			/**
			 * @todo Special checks for premium MTS plugins
			 */
			include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
			$api = plugins_api( 'plugin_information', array(
				'slug'     => $slug,
				'fields'   => array(
					'sections' => false,
					'tags'     => false,
				),
			) );
			if ( is_object( $api ) && ! is_wp_error( $api ) && ! empty( $api->version ) ) {
				$version_latest = $api->version;
			}

			// convert plugin data to json response format.
			$active_plugins_data[] = array(
				'plugin'            => $plugin,
				'name'              => $data['Name'],
				'version'           => $data['Version'],
				'version_latest'    => $version_latest,
				'url'               => $data['PluginURI'],
				'author_name'       => $data['AuthorName'],
				'author_url'        => esc_url_raw( $data['AuthorURI'] ),
				'network_activated' => $data['Network'],
			);
		}// End foreach().
		return $active_plugins_data;
	}

	public function get_theme_info() {
		$active_theme = wp_get_theme();
		// Get parent theme info if this theme is a child theme, otherwise
		// pass empty info in the response.
		if ( is_child_theme() ) {
			$parent_theme      = wp_get_theme( $active_theme->Template );
			$parent_theme_info = array(
				'parent_name'           => $parent_theme->Name,
				'parent_version'        => $parent_theme->Version,
				'parent_author_url'     => $parent_theme->{'Author URI'},
			);
		} else {
			// $parent_theme_info = array( 'parent_name' => '', 'parent_version' => '', 'parent_version_latest' => '', 'parent_author_url' => '' );
			$parent_theme_info = array();
		}

		$active_theme_info = array(
			'name'                    => $active_theme->Name,
			'version'                 => $active_theme->Version,
			'author_url'              => esc_url_raw( $active_theme->{'Author URI'} ),
			'is_child_theme'          => is_child_theme(),
		);
		return array_merge( $active_theme_info, $parent_theme_info );
	}

	public function get_theme_settings() {
		$mts_options = get_option( MTS_THEME_NAME );

		//return $mts_options;

		$lazy_load = array();
		if ( ! empty( $mts_options['mts_lazy_load'] ) ) {
			$lazy_load['featured_images'] = ! empty( $mts_options['mts_lazy_load_thumbs'] );
			$lazy_load['content_images']  = ! empty( $mts_options['mts_lazy_load_content'] );
		}

		return apply_filters( 'mts_support_log_options', array(
			'lazy_load' => ( empty( $lazy_load ) ? false : $lazy_load ),
			'async_javascript' => !empty( $mts_options['mts_async_js'] ),
			'optimize_WC' => !empty( $mts_options['mts_optimize_wc'] ),
			'lightbox' => !empty( $mts_options['mts_lightbox'] ),
		) );
	}

	/**
	 * let_to_num function.
	 *
	 * This function transforms the php.ini notation for numbers (like '2M') to an integer.
	 *
	 * @param $size
	 * @return int
	 */
	public function let_to_num( $size ) {
		$l   = substr( $size, -1 );
		$ret = substr( $size, 0, -1 );
		switch ( strtoupper( $l ) ) {
			case 'P':
				$ret *= 1024;
			case 'T':
				$ret *= 1024;
			case 'G':
				$ret *= 1024;
			case 'M':
				$ret *= 1024;
			case 'K':
				$ret *= 1024;
		}
		return $ret;
	}
}

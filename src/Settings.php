<?php /** @noinspection PhpUndefinedVariableInspection */

namespace Tribe\Extensions\CustomAllEventsUrl;

use Tribe__Settings_Manager;

if ( ! class_exists( Settings::class ) ) {
	/**
	 * Do the Settings.
	 */
	class Settings {

		/**
		 * The Settings Helper class.
		 *
		 * @var Settings_Helper
		 */
		protected $settings_helper;

		/**
		 * The prefix for our settings keys.
		 *
		 * Gets set automatically from the Text Domain or can be set manually.
		 * The prefix should not end with underscore `_`.
		 *
		 * @var string
		 */
		private $options_prefix = '';

		/**
		 * Settings constructor.
		 *
		 * @param $options_prefix
		 */
		public function __construct( $options_prefix ) {

			$this->settings_helper = new Settings_Helper();

			$this->set_options_prefix( $options_prefix );

			add_action( 'admin_init', [ $this, 'add_settings' ] );
		}

		/**
		 * Allow access to set the Settings Helper property.
		 *
		 * @see get_settings_helper()
		 *
		 * @param Settings_Helper $helper
		 *
		 * @return Settings_Helper
		 */
		public function set_settings_helper( Settings_Helper $helper ) {
			$this->settings_helper = $helper;

			return $this->get_settings_helper();
		}

		/**
		 * Allow access to get the Settings Helper property.
		 *
		 * @see set_settings_helper()
		 */
		public function get_settings_helper() {
			return $this->settings_helper;
		}

		/**
		 * Set the options prefix to be used for this extension's settings.
		 *
		 * Recommended: the plugin text domain, with hyphens converted to underscores.
		 * Is forced to end with a single underscore. All double-underscores are converted to single.
		 *
		 * @see get_options_prefix()
		 *
		 * @param string $options_prefix
		 */
		private function set_options_prefix( $options_prefix = '' ) {
			if ( empty( $opts_prefix ) ) {
				$opts_prefix = str_replace( '-', '_', 'tribe-ext-extension-template' ); // The text domain.
			}

			$opts_prefix = $opts_prefix . '_';
			$this->options_prefix = str_replace( '__', '_', $opts_prefix );
		}

		/**
		 * Get this extension's options prefix.
		 *
		 * @return string
		 */
		public function get_options_prefix() {
			return $this->options_prefix;
		}

		/**
		 * Given an option key, get this extension's option value.
		 *
		 * This automatically prepends this extension's option prefix so you can just do `$this->get_option( 'a_setting' )`.
		 *
		 * @param string $key
		 *
		 * @param string $default
		 *
		 * @return mixed
		 * @see tribe_get_option()
		 */
		public function get_option( $key = '', $default = '' ) {
			$key = $this->sanitize_option_key( $key );

			return tribe_get_option( $key, $default );
		}

		/**
		 * Get an option key after ensuring it is appropriately prefixed.
		 *
		 * @param string $key
		 *
		 * @return string
		 */
		private function sanitize_option_key( $key = '' ) {
			$prefix = $this->get_options_prefix();

			if ( 0 === strpos( $key, $prefix ) ) {
				$prefix = '';
			}

			return $prefix . $key;
		}

		/**
		 * Adds the setting field to Events > Settings > General tab
		 * The setting will appear above the "End of day cutoff" setting
		 * (below the "Single event URL slug" setting)
		 */
		public function add_settings() {
			$fields = [
				$this->options_prefix . 'custom_all_events_url' => [
					'type'            => 'text',
					'label'           => esc_html__( 'Custom "All Events" URL', 'tribe-ext-custom-all-events-url' ),
					'tooltip'         => sprintf( esc_html__( 'Enter your custom URL, including "http://" or "https://", for example %s.', 'tribe-ext-custom-all-events-url' ), '<code>https://demo.theeventscalendar.com/events/</code>' ),
					'validation_type' => 'html',
				]
			];

			$this->settings_helper->add_fields( $fields, 'general', 'multiDayCutoff', true );
		}

	} // class
}
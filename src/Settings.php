<?php

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
		private $opts_prefix = '';

		/**
		 * Settings constructor.
		 */
		public function __construct( $opts_prefix = 'tribe_ext' ) {

			$this->settings_helper = new Settings_Helper();

			$this->set_options_prefix( $opts_prefix );

			// Add settings specific to OSM
			add_action( 'admin_init', [ $this, 'add_settings' ] );
		}

		/**
		 * Set the options prefix to be used for this extension's settings.
		 *
		 * Prefixes with `tribe_ext_` and ends with `_`.
		 *
		 * @param string $opts_prefix
		 */
		private function set_options_prefix( $opts_prefix = '' ) {
			if ( empty( $opts_prefix ) ) {
				$opts_prefix = str_replace( '-', '_', PLUGIN_TEXT_DOMAIN );
			}
			$prefix = 'tribe_ext';
			if ( 0 === strpos( $opts_prefix, $prefix ) ) {
				$prefix = '';
			}
			$this->opts_prefix = $prefix . $opts_prefix . '_';
		}

		/**
		 * Given an option key, get this extension's option value.
		 *
		 * This automatically prepends this extension's option prefix so you can just do `$this->get_option( 'a_setting' )`.
		 *
		 * @param string $key
		 *
		 * @return mixed
		 * @see tribe_get_option()
		 *
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
		 * Get this extension's options prefix.
		 *
		 * @return string
		 */
		public function get_options_prefix() {
			return $this->opts_prefix;
		}

		/**
		 * Given an option key, delete this extension's option value.
		 *
		 * This automatically prepends this extension's option prefix so you can just do `$this->delete_option( 'a_setting' )`.
		 *
		 * @param string $key
		 *
		 * @return mixed
		 */
		public function delete_option( $key = '' ) {
			$key     = $this->sanitize_option_key( $key );
			$options = Tribe__Settings_Manager::get_options();
			unset( $options[ $key ] );

			return Tribe__Settings_Manager::set_options( $options );
		}

		/**
		 * Adds the setting field to Events > Settings > General tab
		 * The setting will appear above the "End of day cutoff" setting
		 * (below the "Single event URL slug" setting)
		 */
		public function add_settings() {
			$fields = [
				$this->opts_prefix . 'custom_all_events_url' => [
					'type'            => 'text',
					'label'           => esc_html__( 'Custom "All Events" URL', PLUGIN_TEXT_DOMAIN ),
					'tooltip'         => sprintf( esc_html__( 'Enter your custom URL, including "http://" or "https://", for example %s.', PLUGIN_TEXT_DOMAIN ), '<code>https://wpshindig.com/events/</code>' ),
					'validation_type' => 'html',
				]
			];

			$this->settings_helper->add_fields( $fields, 'general', 'multiDayCutoff', true );
		}

	} // class
}
<?php
/**
 * Plugin Name:       The Events Calendar Extension: Custom 'All Events' URL
 * Plugin URI:        https://theeventscalendar.com/extensions/custom-all-events-url/
 * GitHub Plugin URI: https://github.com/mt-support/tribe-ext-custom-all-events-url/
 * Description:       Allows the definition of a custom URL for the 'All Events' link. The setting can be found under <em>Events > Settings > General tab</em>. Useful if you are using a different starting page for the calendar than the main calendar page, for example when embedding the calendar with a shortcode.
 * Version:           1.0.0
 * Extension Class:   Tribe\Extensions\CustomAllEventsUrl\Main
 * Author:            Modern Tribe, Inc.
 * Author URI:        http://m.tri.be/1971
 * License:           GPL version 3 or any later version
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       tribe-ext-custom-all-events-url
 *
 *     This plugin is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     any later version.
 *
 *     This plugin is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *     GNU General Public License for more details.
 */


namespace Tribe\Extensions\CustomAllEventsUrl;

use Tribe__Autoloader;
use Tribe__Dependency;
use Tribe__Extension;

/**
 * Define Constants
 */

if ( ! defined( __NAMESPACE__ . '\NS' ) ) {
	define( __NAMESPACE__ . '\NS', __NAMESPACE__ . '\\' );
}

if ( ! defined( NS . 'PLUGIN_TEXT_DOMAIN' ) ) {
	// `Tribe\Extensions\Example\PLUGIN_TEXT_DOMAIN` is defined
	define( NS . 'PLUGIN_TEXT_DOMAIN', 'tribe-ext-custom-all-events-url' );
}

// Do not load unless Tribe Common is fully loaded and our class does not yet exist.
if (
	class_exists( 'Tribe__Extension' )
	&& ! class_exists( NS . 'Main' )
) {
	/**
	 * Extension main class, class begins loading on init() function.
	 */
	class Main extends Tribe__Extension {

		/** @var Tribe__Autoloader */
		private $class_loader;

		/**
		 * @var Settings
		 */
		private $settings;

		/**
		 * Setup the Extension's properties.
		 *
		 * This always executes even if the required plugins are not present.
		 */
		public function construct() {
			$this->add_required_plugin( 'Tribe__Events__Main' );
		}

		/**
		 * Get this plugin's options prefix.
		 *
		 * Settings_Helper will append a trailing underscore before each option.
		 *
		 * @see \Tribe\Extensions\Example\Settings::set_options_prefix()
		 *
		 * @return string
		 */
		private function get_options_prefix() {
			return (string) str_replace( '-', '_', 'tribe-ext-custom-all-events-url' );
		}

		/**
		 * Get Settings instance.
		 *
		 * @return Settings
		 */
		private function get_settings() {
			if ( empty( $this->settings ) ) {
				$this->settings = new Settings( $this->get_options_prefix() );
			}

			return $this->settings;
		}

		/**
		 * Extension initialization and hooks.
		 */
		public function init() {

			// Load plugin textdomain
			load_plugin_textdomain( PLUGIN_TEXT_DOMAIN, false, basename( dirname( __FILE__ ) ) . '/languages/' );

			// PHP version check
			if ( ! $this->php_version_check() ) {
				return;
			}

			// Load classes
			$this->class_loader();

			$this->get_settings();

			// Filters and hooks
			add_filter( 'tribe_get_events_link', [ $this, 'custom_all_events_url' ] );
		}

		/**
		 * PHP version check
		 *
		 * @return bool
		 */
		private function php_version_check() {
			$php_required_version = '5.6';

			if ( version_compare( PHP_VERSION, $php_required_version, '<' ) ) {
				if (
					is_admin()
					&& current_user_can( 'activate_plugins' )
				) {
					$message = '<p>';
					$message .= sprintf( __( '%s requires PHP version %s or newer to work. Please contact your website host and inquire about updating PHP.', PLUGIN_TEXT_DOMAIN ), $this->get_name(), $php_required_version );
					$message .= sprintf( ' <a href="%1$s">%1$s</a>', 'https://wordpress.org/about/requirements/' );
					$message .= '</p>';

					tribe_notice( PLUGIN_TEXT_DOMAIN . '-php-version', $message, [ 'type' => 'error' ] );
				}

				return false;
			}

			return true;
		}

		/**
		 * Use Tribe Autoloader for all class files within this namespace in the 'src' directory.
		 *
		 * @return Tribe__Autoloader
		 */
		public function class_loader() {

			if ( empty( $this->class_loader ) ) {
				$this->class_loader = new Tribe__Autoloader;
				$this->class_loader->set_dir_separator( '\\' );
				$this->class_loader->register_prefix(
					__NAMESPACE__ . '\\',
					__DIR__ . DIRECTORY_SEPARATOR . 'src'
				);
			}

			$this->class_loader->register_autoloader();

			return $this->class_loader;
		}

		/**
		 * Reads and returns the custom 'All Events' URL if it is set
		 *
		 * @see tribe_get_events_link()
		 *
		 * @param $url
		 *
		 * @return mixed
		 */
		function custom_all_events_url( $url ) {

			$custom_url = $this->get_custom_url();

			if ( ! empty ( $custom_url ) ) {
				$url = $custom_url;
			}

			return $url;
		}

		/**
		 * Getting the `custom_all_events_url` value.
		 *
		 * @return mixed
		 */
		public function get_custom_url() {
			$settings = new Settings();
			$value = $settings->get_option( 'custom_all_events_url' );
			return $value;
		}

	} // end class
} // end if class_exists check

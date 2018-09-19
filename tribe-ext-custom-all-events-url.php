<?php
/**
 * Plugin Name:       The Events Calendar Extension: Custom All Events URL
 * Plugin URI:        https://theeventscalendar.com/extensions/custom-all-events-url/
 * GitHub Plugin URI: https://github.com/mt-support/tribe-ext-custom-all-events-url/
 * Description:       Allows you to set up a custom URL for the 'All Events' link under Events > Settings > General
 * Version:           1.0.0
 * Extension Class:   Tribe__Extension__Custom_All_Events_Url
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

// Do not load unless Tribe Common is fully loaded and our class does not yet exist.
if (
	class_exists( 'Tribe__Extension' )
	&& ! class_exists( 'Tribe__Extension__Custom_All_Events_Url' )
) {
	/**
	 * Extension main class, class begins loading on init() function.
	 */
	class Tribe__Extension__Custom_All_Events_Url extends Tribe__Extension {

		protected $opts_prefix = 'tribe_ext_';

		/**
		 * Setup the Extension's properties.
		 *
		 * This always executes even if the required plugins are not present.
		 */
		public function construct() {
			// Requirements and other properties such as the extension homepage can be defined here.
			// Examples:
			$this->add_required_plugin( 'Tribe__Events__Main' );
		}

		/**
		 * Extension initialization and hooks.
		 */
		public function init() {
			// Load plugin textdomain
			// Don't forget to generate the 'languages/match-the-plugin-directory-name.pot' file
			load_plugin_textdomain( 'tribe-ext-custom-all-events-url', false, basename( dirname( __FILE__ ) ) . '/languages/' );

			/**
			 * Protect against fatals by specifying the required minimum PHP
			 * version. Make sure to match the readme.txt header.
			 * All extensions require PHP 5.6+, following along with https://theeventscalendar.com/knowledgebase/php-version-requirement-changes/
			 *
			 * Delete this paragraph and the non-applicable comments below.
			 *
			 * Note that older version syntax errors may still throw fatals even
			 * if you implement this PHP version checking so QA it at least once.
			 *
			 * @link https://secure.php.net/manual/en/migration56.new-features.php
			 * 5.6: Variadic Functions, Argument Unpacking, and Constant Expressions
			 *
			 * @link https://secure.php.net/manual/en/migration70.new-features.php
			 * 7.0: Return Types, Scalar Type Hints, Spaceship Operator, Constant Arrays Using define(), Anonymous Classes, intdiv(), and preg_replace_callback_array()
			 *
			 * @link https://secure.php.net/manual/en/migration71.new-features.php
			 * 7.1: Class Constant Visibility, Nullable Types, Multiple Exceptions per Catch Block, `iterable` Pseudo-Type, and Negative String Offsets
			 *
			 * @link https://secure.php.net/manual/en/migration72.new-features.php
			 * 7.2: `object` Parameter and Covariant Return Typing, Abstract Function Override, and Allow Trailing Comma for Grouped Namespaces
			 */
			$php_required_version = '5.6';

			if ( version_compare( PHP_VERSION, $php_required_version, '<' ) ) {
				if (
					is_admin()
					&& current_user_can( 'activate_plugins' )
				) {
					$message = '<p>';
					$message .= sprintf( __( '%s requires PHP version %s or newer to work. Please contact your website host and inquire about updating PHP.', 'match-the-plugin-directory-name' ), $this->get_name(), $php_required_version );
					$message .= sprintf( ' <a href="%1$s">%1$s</a>', 'https://wordpress.org/about/requirements/' );
					$message .= '</p>';
					tribe_notice( $this->get_name(), $message, 'type=error' );
				}

				return;
			}

			// Insert filters and hooks here
			add_action( 'admin_init', array( $this, 'add_settings' ) );
			add_filter( 'tribe_get_events_link', array( $this, 'custom_all_events_url' ) );

			//singleEventSlug
		}

		public function add_settings() {
			require_once dirname( __FILE__ ) . '/src/Tribe/Settings_Helper.php';

			$setting_helper = new Tribe__Settings_Helper();

			$fields = array(
				$this->opts_prefix . 'custom_all_events_url' => array(
					'type'            => 'text',
					'label'           => esc_html__( 'Custom \'All Events\' URL', 'tribe-ext-custom-all-events-url' ),
					'tooltip'         => 'Enter your custom URL, including \'http://\' or \'https://\', for example <code>https://mydomain.com/events/</code>.',
					'validation_type' => 'html',
				)
			);
			$setting_helper->add_fields(
				$fields,
				'general',
				'multiDayCutoff',
				true
			);
		}

		/**
		 * Include a docblock for every class method and property.
		 */
		function custom_all_events_url( $url ) {
			$custom_url = tribe_get_option( $this->opts_prefix . 'custom_all_events_url' );

			if ( ! empty ( $custom_url ) ) {
				$url = $custom_url;
			}
			return $url;
		}

	} // end class
} // end if class_exists check

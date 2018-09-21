<?php
/**
 * Plugin Name:       The Events Calendar Extension: Custom 'All Events' URL
 * Plugin URI:        https://theeventscalendar.com/extensions/custom-all-events-url/
 * GitHub Plugin URI: https://github.com/mt-support/tribe-ext-custom-all-events-url/
 * Description:       Allows the definition of a custom URL for the 'All Events' link. The setting can be found under <em>Events > Settings > General tab</em>
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
			load_plugin_textdomain( 'tribe-ext-custom-all-events-url', false, basename( dirname( __FILE__ ) ) . '/languages/' );

			/**
			 * Protect against fatals by specifying the required minimum PHP
			 * version.
			 */
			$php_required_version = '5.6';

			if ( version_compare( PHP_VERSION, $php_required_version, '<' ) ) {
				if (
					is_admin()
					&& current_user_can( 'activate_plugins' )
				) {
					$message = '<p>';
					$message .= sprintf( __( '%s requires PHP version %s or newer to work. Please contact your website host and inquire about updating PHP.', 'tribe-ext-custom-all-events-url' ), $this->get_name(), $php_required_version );
					$message .= sprintf( ' <a href="%1$s">%1$s</a>', 'https://wordpress.org/about/requirements/' );
					$message .= '</p>';
					tribe_notice( $this->get_name(), $message, 'type=error' );
				}
				return;
			}

			// Filters and hooks
			add_action( 'admin_init', array( $this, 'add_settings' ) );
			add_filter( 'tribe_get_events_link', array( $this, 'custom_all_events_url' ) );

			//singleEventSlug
		}

		/**
		 * Adds the setting field to Events > Settings > General tab
		 * The setting will appear above the "End of day cutoff" setting
		 * (below the "Single event URL slug" setting)
		 */
		public function add_settings() {

			require_once dirname( __FILE__ ) . '/src/Tribe/Settings_Helper.php';

			$setting_helper = new Tribe__Settings_Helper();

			$fields = array(
				$this->opts_prefix . 'custom_all_events_url' => array(
					'type'            => 'text',
					'label'           => esc_html__( 'Custom "All Events" URL', 'tribe-ext-custom-all-events-url' ),
					'tooltip'         => sprintf( esc_html__( 'Enter your custom URL, including "http://" or "https://", for example %s.', 'tribe-ext-custom-all-events-url' ), '<code>https://mydomain.com/events/</code>' ),
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
		 * Reads and returns the custom 'All Events' URL if it is set
		 *
		 * @param $url
		 *
		 * @return mixed
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

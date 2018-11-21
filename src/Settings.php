<?php

namespace Tribe\Extensions\CustomAllEventsUrl;

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
	 * @var string
	 */
	protected $opts_prefix = 'tribe_ext_';

	/**
	 * Settings constructor.
	 */
	public function __construct() {
		$this->settings_helper = new Settings_Helper();

		// Add settings specific to OSM
		add_action( 'admin_init', [ $this, 'add_settings' ] );
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
				'tooltip'         => sprintf( esc_html__( 'Enter your custom URL, including "http://" or "https://", for example %s.', PLUGIN_TEXT_DOMAIN ), '<code>https://mydomain.com/events/</code>' ),
				'validation_type' => 'html',
			]
		];

		$this->settings_helper->add_fields(
			$fields,
			'general',
			'multiDayCutoff',
			true
		);
	}

} // class
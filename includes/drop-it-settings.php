<?php
/**
 *
 */


class Drop_It_Settings {

	private $settings_api, $slug, $caps, $settings_suffix = 'di_settings';

	function __construct( $slug, $caps ) {
		$this->settings_api = new WeDevs_Settings_API;
		$this->slug = $slug;
		$this->caps = $caps;

		add_action( 'current_screen', array( $this, 'action_current_screen' ) );
		add_action( 'admin_menu', array( $this, 'action_admin_menu' ) );
	}

	function action_admin_init() {

	}
	/**
	 * Only run if current screen is plugin settings or options.php
	 * @return [type] [description]
	 */
	function action_current_screen() {
		$screen = get_current_screen();
		if ( ! in_array( $screen->base, array( "{$this->slug}_page_{$this->settings_suffix}", 'options' ) ) ) 
			return;
		$this->settings_api->set_sections( $this->get_settings_sections() );
		$this->settings_api->set_fields( $this->get_settings_fields() );
		$this->settings_api->admin_init();
	}


	function action_admin_menu() {
		add_submenu_page( $this->slug, __( 'Settings', 'drop-it' ) , __('Settings', 'drop-it' ), $this->caps, $this->settings_suffix, array( $this, 'plugin_page' ) );
	}

	function get_settings_sections() {
		return array(
			array(
				'id' => 'drop_it_settings',
				'title' => __( 'Basic Settings', 'drop-it' ),
			),
		);
	}

	/**
	 * Returns all the settings fields
	 *
	 * @return array settings fields
	 */
	function get_settings_fields() {
		$settings_fields = array(
			'drop_it_settings' => array(
				array(
					'name' => 'default_columns',
					'label' => __( 'Default number of columns', 'drop-it' ),
					'desc' => __( '', 'drop-it' ),
					'type' => 'select',
					'default' => '3',
					'options' => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
					),
				),
			),
		);
		return $settings_fields;
	}

	function plugin_page() {
		echo '<div class="wrap">';

		$this->settings_api->show_navigation();
		$this->settings_api->show_forms();

		echo '</div>';
	}
}

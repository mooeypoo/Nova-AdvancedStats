<?php
namespace AdvancedStats;

/**
 * System model for AdvancesStats
 * Handles installation and communication against Nova's system settings and menus.
 */
class System {
	protected $ci;
	protected $settingsKeyPrefix = 'ext_advancedStats_';
	protected $validRecurrence = [ 'daily', 'monthly', 'yearly' ];

	public function __construct() {
		$this->ci =& get_instance();

		$this->ci->load->model('menu_model');
		$this->ci->load->model('settings_model', 'settings');

		// // set the options
		$this->requiredSettings = [
			'color_missionposts' => 'Graph color for mission posts',
			'color_personallogs' => 'Graph color for personal logs',
			'color_newsitems' => 'Graph color for news items',
			'recurrence' => 'The recurrence for the statistics: "daily", "monthly" or "yearly"',
		];
	}

	public function getValidRecurrence() {
		return $this->validRecurrence;
	}
	public function install() {
		$this->installSettings();
		$this->installMenuItem();
	}
	public function saveSetting( $settingKey, $settingValue ) {
		$name = $this->settingsKeyPrefix . $settingKey;
		return $this->ci->settings->update_setting(
			$name,
			[ 'setting_value'=> $settingValue ]
		);
	}

	public function getSettingValue( $settingKey ) {
		$name = $this->settingsKeyPrefix . $settingKey;
		return $this->ci->settings->get_setting( $name );
	}

	protected function installSettings() {
		foreach ( $this->requiredSettings as $set => $desc ) {
			$name = $this->settingsKeyPrefix . $set;
			$value = $this->ci->settings->get_setting( $name );
			if ( $value === false ) {
				// Add this setting
				$this->ci->settings->add_new_setting( [
					'setting_key' => $name,
					'setting_label' => $desc,
					'setting_value' => ''
				] );
			}
		}
	}

	protected function installMenuItem() {
		// Install menu item
		$expectedLink = 'extensions/AdvancedStats/Show/general';
		$items = $this->ci->menu_model->get_menu_items( 'adminsub', 'report' );
		$itemLinks = array_map( function ( $row ) { return $row->menu_link; }, $items->result() );
		if ( !in_array( $expectedLink, $itemLinks ) ) {
			// Menu item isn't available; Add item
			$insertItem = $this->ci->menu_model->add_menu_item( [
				'menu_name' => 'Advanced Stats',
				'menu_group' => 0,
				'menu_order' => 0,
				'menu_sim_type' => 1,
				'menu_link' => $expectedLink,
				'menu_link_type' => 'onsite',
				'menu_need_login' => 'none',
				'menu_use_access' => 'y',
				'menu_access' => 'report/activity',
				'menu_access_level' => 0,
				'menu_display' => 'y',
				'menu_type' => 'adminsub',
				'menu_cat' => 'report',
			] );
		}
	}

	public function getRecurrence( $recurrence = '' ) {
		if ( !$recurrence ) {
			$recurrence = $this->getSettingValue( 'recurrence' );
		}
		if (
			!$recurrence ||
			!in_array( $recurrence, $this->getValidRecurrence() )
		) {
			// Default
			return 'daily';
		}
		return $recurrence;
	}
}

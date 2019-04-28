<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once MODPATH.'core/controllers/nova_ajax.php';
require_once dirname( __DIR__ ) . '/bootstrap.php';

class __extensions__AdvancedStats__Ajax extends Nova_ajax {
	protected $statSystem;

	public function __construct() {
		parent::__construct();
		$this->statSystem = new \AdvancedStats\System();
	}

	public function settings( $save = '' ) {
		if (
			$save &&
			isset( $_POST['advancedStats-colors'] ) &&
			isset( $_POST['advancedStats-recurrence'] )
		) {
			$status = [];

			// Save the color values
			$values = $_POST['advancedStats-colors'];
			foreach ( $values as $type => $color ) {
				$status[$type] = $this->statSystem->saveSetting( 'color_' . $type, $color ) ? 'saved' : 'failed';
			}
			$recurrence = $this->statSystem->getRecurrence( $_POST['advancedStats-recurrence'] );
			$status['recurrence'] = $this->statSystem->saveSetting( 'recurrence', $recurrence ) ? 'saved' : 'failed';

			redirect( 'extensions/AdvancedStats/Show/general' );
		}

		$data = [
			'header' => 'AdvancedStats :: Settings',
			'labels' => [
				'headings' => [
					'graph_colors' => 'Graph colors',
					'graph_recurrence' => 'Resolution',
					'data_settings' => 'Data settings',
				],
				'colors' => [
					'missionposts' => ucwords(lang('global_missionposts')),
					'personallogs' => ucwords(lang('global_personallogs')),
					'newsitems' => ucwords(lang('global_newsitems')),
				],
				'inputs' => [
					'recurrence' => []
				],
			],
			'inputs' => [
				'submit' => [
					'type' => 'submit',
					'class' => 'hud_button ext-advancedStats-settings-submit',
					'name' => 'submit',
					'value' => 'submit',
					'content' => ucwords(lang('actions_submit'))
				],
				'colors' => [
					'missionposts' => [
						'name' => 'advancedStats-colors[missionposts]',
						'class' => 'jscolor',
						'value' => $this->statSystem->getSettingValue( 'color_missionposts' )
					],
					'personallogs' => [
						'name' => 'advancedStats-colors[personallogs]',
						'class' => 'jscolor',
						'value' => $this->statSystem->getSettingValue( 'color_personallogs' )
					],
					'newsitems' => [
						'name' => 'advancedStats-colors[newsitems]',
						'class' => 'jscolor',
						'value' => $this->statSystem->getSettingValue( 'color_newsitems' )
					]
				],
				'recurrence' => [],
			]
		];
		$possibleRecurrence = $this->statSystem->getValidRecurrence();
		$currentRecurrence = $this->statSystem->getRecurrence();
		foreach ( $possibleRecurrence as $val ) {
			$data['labels']['inputs']['recurrence'][$val] = ucwords( $val );
			$data['inputs']['recurrence'][$val] = [
				'name' => 'advancedStats-recurrence',
				'id' => 'advancedStats-recurrence-' . $val,
				'value' => $val,
				'checked' => $currentRecurrence === $val,
			];
		}

		// figure out the skin
		$skin = $this->session->userdata('skin_admin');
		$this->_regions['content'] = $this->extension['AdvancedStats']->view('ajax_settings', $skin, 'admin', $data);
		$this->_regions['controls'] = form_button($data['inputs']['submit']).form_close();

		Template::assign($this->_regions);
		Template::render();
	}
}

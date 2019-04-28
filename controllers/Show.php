<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once MODPATH.'core/libraries/Nova_controller_main.php';
require_once dirname( __DIR__ ) . '/bootstrap.php';

class __extensions__AdvancedStats__Show extends Nova_controller_main {
	protected $system;

	public function __construct()
	{
		parent::__construct();
		$this->system = new \AdvancedStats\System();
		$this->system->install(); // No-op install
		$this->resultsManager = new \AdvancedStats\ResultsManager();
		$this->resultsManager->setRecurrence( $this->system->getRecurrence() );

		$this->_regions['nav_sub'] = Menu::build('adminsub', 'report');
	}

	public function general() {
		Auth::check_access( 'report/activity' );

		$base = strtotime(date('Y-m-d',time()) . '-01 00:00:01');
		$dates = [
			'start' => strtotime('-1 month', $base ),
			'end' => time(),
		];
		$dates['display'] =[
			'start' => $this->resultsManager->getHumanReadableDate( $dates['start'] ),
			'end' => $this->resultsManager->getHumanReadableDate( $dates['end'] ),
		];

		$data = [
			'header' => 'Advanced Statistics',
			'labels' => [
				'loading' => ucfirst(lang('actions_loading')) .'...',
				'missionposts' => ucwords(lang('global_missionposts')),
				'date_range' => 'Date range',
				'posting_activity' => 'Posting activity',
				'user_stats' => 'User statistics',
				'user_chars' => 'User characters',
				'sort_instruction' => 'Click the headings to sort the table.',
				'no_user' => 'Unassigned to user',
				'name' => ucwords(lang('global_user') . ' ' . lang('labels_name')),
				'char_name' => ucwords(lang('global_character') . ' ' . lang('labels_name')),
				'user_bio' => ucwords(lang('global_user') .' '. lang('labels_bio')),
				'character_bio' => ucwords(lang('global_character') .' '. lang('labels_bio')),
				'user_posts' => ucwords(lang('global_user') .' '. lang('global_posts')),
				'character_posts' =>  ucwords(lang('global_character') .' '. lang('global_posts')),
				'character_list' => ucwords(lang('global_characters')),
				'character_user' => ucwords(lang('global_user') ),
				'count_logs' => ucwords(lang('global_logs')),
				'count_news' => 'News items',
				'count_posts' => ucwords(lang('global_posts')),
				'count_posts_alone' => 'alone',
				'count_posts_with_others' => 'with others',
				'count_posts_total' => 'total',
				'count_totals' => ucfirst(lang('labels_totals')),
			],
			'images' => [
				'loading' => [
					'src' => Location::img('loading-circle-large.gif', $this->skin, 'admin'),
					'alt' => lang('actions_loading'),
					'class' => 'image'
				],
			],
			'buttons' => [
				'settings' => [
					'class' => 'button-main',
					'name' => 'settings',
					'value' => 'settings',
					'id' => 'advancedStats-settings',
					// TODO: SRSLY, i18n... !!!!
					'content' => 'Settings',
				],
			],
			'date' => $dates,
			'recurrence' => $this->system->getRecurrence(),
			'initialCounts' => $this->resultsManager->getMissionPostCounts( $dates['start'], $dates['end'] ),
			'userstats' => $this->resultsManager->getUserStats( $dates['start'], $dates['end'] ),
			'charstats' => $this->resultsManager->getAllCharStats( $dates['start'], $dates['end'] ),
		];

		// Render the template
		$this->_regions['title'] = 'Advanced Statistics';
		$this->_regions['content'] = $this->extension['AdvancedStats']->view('general', $this->skin, 'admin', $data);
		$this->_regions['javascript'] .= join( "\n", [
			// Extension css
			$this->extension['AdvancedStats']->inline_css('general', 'admin', $data),
			// Moment.js
			'<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js"></script>',
			// Chart.js
			'<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css">',
			'<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>',
			// Datepicker
			$this->extension['AdvancedStats']->inline_js('jquery-ui-datepicker.min','admin'),
			$this->extension['AdvancedStats']->inline_css('jquery.ui.datepicker', 'admin'),
			$this->extension['AdvancedStats']->inline_css('jquery.ui.theme', 'admin'),
			// Extension JS
			$this->extension['AdvancedStats']->inline_js('general','admin'),
			$this->extension['AdvancedStats']->inline_js('lib.jscolor','admin'),
			$this->extension['AdvancedStats']->inline_js('lib.stupidtable.min','admin'),
		] );

		Template::assign($this->_regions);
		Template::render();
	}
}

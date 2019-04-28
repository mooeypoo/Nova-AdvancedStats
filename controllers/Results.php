<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once MODPATH.'core/libraries/Nova_controller_main.php';
require_once dirname( __DIR__ ) . '/bootstrap.php';

class __extensions__AdvancedStats__Results extends CI_Controller {
	protected $resultManager;
	protected $statSystem;

	public function __construct() {
		parent::__construct();

		// load the resources
		$this->load->database();
		$this->load->library('session');
		$this->resultManager = new \AdvancedStats\ResultsManager();
		$this->statSystem = new \AdvancedStats\System();

		// check to see if they are logged in
		Auth::is_logged_in();

		// set and load the language file needed
		$this->lang->load('app', $this->session->userdata('language'));
	}

	public function userstats( $startTime = 0, $endTime = 0 ) {
		echo json_encode( $this->resultManager->getUserStats( strtotime( $startTime ), strtotime( $endTime ) ) );
	}

	public function combined( $startTime = 0, $endTime = 0 ) {
		$this->resultManager->setRecurrence( $this->statSystem->getRecurrence() );
		echo json_encode( [
			$this->prepForView(
				'missionposts',
				ucwords(lang('global_missionposts')),
				$this->resultManager->getMissionPostCounts( strtotime( $startTime ), strtotime( $endTime ) )
			),
			$this->prepForView(
				'personallogs',
				ucwords(lang('global_personallogs')),
				$this->resultManager->getPersonalLogCounts( strtotime( $startTime ), strtotime( $endTime ) )
			),
			$this->prepForView(
				'newsitems',
				ucwords(lang('global_news')),
				$this->resultManager->getNewsItemsCounts( strtotime( $startTime ), strtotime( $endTime ) )
			)
		] );
	}

	public function missionposts( $startTime = 0, $endTime = 0 ) {
		$this->resultManager->setRecurrence( $this->statSystem->getRecurrence() );
		echo json_encode( $this->prepForView(
			'missionposts',
			ucwords(lang('global_missionposts')),
			$this->resultManager->getMissionPostCounts( strtotime( $startTime ), strtotime( $endTime ) )
		) );
	}

	public function personallogs( $startTime = 0, $endTime = 0 ) {
		$this->resultManager->setRecurrence( $this->statSystem->getRecurrence() );
		echo json_encode( $this->prepForView(
			'personallogs',
			ucwords(lang('global_personallogs')),
			$this->resultManager->getPersonalLogCounts( strtotime( $startTime ), strtotime( $endTime ) )
		) );
	}

	public function newsitems( $startTime = 0, $endTime = 0 ) {
		$this->resultManager->setRecurrence( $this->statSystem->getRecurrence() );
		echo json_encode( $this->prepForView(
			'newsitems',
			ucwords(lang('global_news')),
			$this->resultManager->getPersonalLogCounts( strtotime( $startTime ), strtotime( $endTime ) )
		) );
	}

	/**
	 * Prepare the data for view for the graph.
	 * Split the result display for x/y data, add labels and set color.
	 *
	 * @param  [type] $type Data type "missionposts", "personallogs" or "newsitems"
	 * @param  string $label Label for the dataset
	 * @param  array  $counts The array of counts per date
	 * @return array Combined object for the javascript to display the graph and table
	 */
	private function prepForView( $type, $label = '', $counts = [] ) {
		$color = $this->statSystem->getSettingValue( 'color_' . $type );

		$result = [];
		$labels = [];
		foreach ( $counts as $date => $count ) {
			$labels[] = $date;
			$result[] = [
				'x' => $date,
				'y' => $count,
			];
		}
		return [
			'label' => $label,
			'data' => $result,
			'labels' => $labels,
			'color' => $color,
		];
	}
}

<?php
namespace AdvancedStats;

/**
 * ResultsManager model for AdvancedStats
 * Handles asking the database for wanted statistics and results
 */
class ResultsManager {
	protected $ci;
	protected $startTime = 0;
	protected $endTime = 0;
	protected $recurrence = 'daily';

	public function __construct() {
		$this->ci =& get_instance();

		// Install menu item
		$this->ci->load->database();

		$this->ci->load->model('users_model', 'user');
		$this->ci->load->model('characters_model', 'char');
		$this->ci->load->model('personallogs_model', 'logs');
		$this->ci->load->model('posts_model', 'posts');
		$this->ci->load->model('positions_model', 'pos');
		$this->ci->load->model('news_model', 'news');
		$this->ci->load->model('settings_model', 'settings');

		// Reset to default
		$this->setRange( 0, 0 );
	}

	/**
	 * Set the recurrence for the data requested.
	 * 'daily', 'monthly' or 'yearly'
	 *
	 * @param string $recurrence 'daily', 'monthly' or 'yearly'
	 */
	public function setRecurrence( $recurrence ) {
		$this->recurrence = $recurrence;
	}

	/**
	 * Get a human-readable version of the date
	 *
	 * @param  string $date Unix timestamp
	 * @return string Human readable date
	 */
	public function getHumanReadableDate( $date ) {
		$dateFormat = 'j-M-Y';
		switch ( $this->recurrence ) {
			case 'monthly':
				$dateFormat = 'M-Y';
				break;
			case 'yearly':
				$dateFormat = 'Y';
				break;
		}

		return date($dateFormat, $date);
	}

	/**
	 * Get an array of data for counts of mission posts between the
	 * given dates.
	 *
	 * @param  integer $start Start date. If not given, will calculate from the
	 *  "beginning of time" (earliest that is available in the database)
	 * @param  integer $end End date. If not given, will calculate up to today
	 * @param  string  $status Post status, 'activated', 'saved' or 'pending'
	 * @return array An array where the keys are dates and the values are post counts.
	 *  The given interval of the dates are by days.
	 */
	public function getMissionPostCounts( $start = 0, $end = 0, $status = 'activated' ) {
		return $this->getCountData( 'posts', $start, $end, $status );
	}

	/**
	 * Get an array of data for counts of personal logs between the
	 * given dates.
	 *
	 * @param  integer $start Start date. If not given, will calculate from the
	 *  "beginning of time" (earliest that is available in the database)
	 * @param  integer $end End date. If not given, will calculate up to today
	 * @param  string  $status Post status, 'activated', 'saved' or 'pending'
	 * @return array An array where the keys are dates and the values are post counts.
	 *  The given interval of the dates are by days.
	 */
	public function getPersonalLogCounts( $start = 0, $end = 0, $status = 'activated' ) {
		return $this->getCountData( 'logs', $start, $end, $status );
	}

	/**
	 * Get an array of data for counts of news item between the
	 * given dates.
	 *
	 * @param  integer $start Start date. If not given, will calculate from the
	 *  "beginning of time" (earliest that is available in the database)
	 * @param  integer $end End date. If not given, will calculate up to today
	 * @param  string  $status Post status, 'activated', 'saved' or 'pending'
	 * @return array An array where the keys are dates and the values are post counts.
	 *  The given interval of the dates are by days.
	 */
	public function getNewsItemsCounts( $start = 0, $end = 0, $status = 'activated' ) {
		return $this->getCountData( 'news', $start, $end, $status );
	}

	/**
	 * Get user posting statistics
	 *
	 * @param  integer $start Start date. If not given, will calculate from the
	 *  "beginning of time" (earliest that is available in the database)
	 * @param  integer $end End date. If not given, will calculate up to today
	 * @return array Object representing users with data associated with them for display
	 */
	public function getUserStats( $start = 0, $end = 0 ) {
		// Get all users
		$users = $this->ci->user->get_users();
		$settings = $this->ci->settings->get_settings( [
			'posting_requirement',
			'date_format',
			'timezone',
			'daylight_savings',
			'display_rank',
		] );

		$data = [ 'users' => [] ];
		$leaderboard = [];

		foreach ( $users->result() as $p ) {
			// set the posting requirement threshold
			$requirement = now() - ( 86400 * $settings['posting_requirement'] );

			$counts = [
				'logs' => $this->getItemCount( 'logs', $p->userid, $start, $end ),
				'news' => $this->getItemCount( 'news', $p->userid, $start, $end ),
				'posts' => $this->getItemCount( 'posts', $p->userid, $start, $end ),
			];
			$counts['total'] = (int)$counts['logs'] + (int)$counts['news'] + (int)$counts['posts'];
			$leaderboard[ (int)$counts['total'] ] = (int)$p->userid;

			$data['users'][$p->userid] = array(
				'name' => $p->name,
				'email' => $p->email,
				'id' => $p->userid,
				'last_post' => timespan_short($p->last_post, now()),
				'last_login' => timespan_short($p->last_login, now()),
				'requirement_post' => ($p->last_post < $requirement) ? ' red' : '',
				'requirement_login' => ($p->last_login < $requirement) ? ' red' : '',
				'loa' => ($p->loa != 'active') ? '['. strtoupper($p->loa) .']' : '',
				'chars' => [
					'main' => $p->main_char,
					'active' => $this->ci->char->get_user_characters($p->userid, 'active', 'array'),
					'npc' => $this->ci->char->get_user_characters($p->userid, 'npc', 'array'),
					'inactive' => $this->ci->char->get_user_characters($p->userid, 'inactive', 'array'),
					'pending' => $this->ci->char->get_user_characters($p->userid, 'pending', 'array'),
				],
				'counts' => $counts,
			);
		}

		// Sort in reverse order; keys are total counts
		krsort( $leaderboard );
		$data['leaderboard'] = $leaderboard;

		return $data;
	}

	public function getAllCharStats( $start = 0, $end = 0 ) {
		$settings = $this->ci->settings->get_settings( [
			'display_rank',
			'posting_requirement',
		] );
		$requirement = now() - ( 86400 * $settings['posting_requirement'] );
		$result = [];

		$stasus = [ 'active', 'inactive', 'npc', 'pending' ];
		foreach ( $stasus as $charstatus ) {
			$characters = $this->ci->char->get_all_characters( $status );
			foreach ( $characters->result() as $c ) {
				$username = $this->ci->user->get_user( $c->user, 'name' );
				if ( !$username ) {
					$username = $this->ci->user->get_user( $c->user, 'email' );
				}

				// Count posts and divide to together vs alone
				$charPosts = $this->getItemCount( 'posts', $c->charid, $start, $end, 'char', 'activated', false );
				$countPosts = [ 'alone' => 0, 'with_others' => 0, 'total' => 0 ];
				foreach ( $charPosts->result() as $prow ) {
					$authors = explode( ',', $prow->post_authors );
					if ( count( $authors ) > 1 ) {
						$countPosts['with_others']++;
					} else {
						$countPosts['alone']++;
					}
					$countPosts['total']++;
				}

				$result[ $c->charid ] = [
					'userid' => $c->user,
					'name' => $this->ci->char->get_character_name( $c->charid, $settings['display_rank'] ),
					'username' => $username,
					'position_1' => $c->position_1 ? $this->ci->pos->get_position( $c->position_1, 'pos_name' ) : null,
					'position_2' => $c->position_2 ? $this->ci->pos->get_position( $c->position_2, 'pos_name' ) : null,
					'last_post' => timespan_short($c->last_post, now()),
					'requirement_post' => ($c->last_post < $requirement) ? ' red' : '',
					'counts' => [
						'posts' => $countPosts,
						'logs' => $this->getItemCount( 'logs', $c->charid, $start, $end, 'char' ),
						'news' => $this->getItemCount( 'news', $c->charid, $start, $end, 'char' ),
					],
				];
			}
		}

		return $result;
	}

	/**
	 * Get character statistics per users
	 *
	 * @param  integer $start Start date. If not given, will calculate from the
	 *  "beginning of time" (earliest that is available in the database)
	 * @param  integer $end End date. If not given, will calculate up to today
	 * @return array Object representing users with their characters and post count per each
	 */
	public function getUserCharStats( $start = 0, $end = 0 ) {
		$settings = $this->ci->settings->get_settings( [
			'display_rank',
		] );

		$users = $this->ci->user->get_users();
		$usersOnline = $this->ci->user->get_online_users();
		$result = [];
		foreach ( $users->result() as $u ) {
			$stasus = [ 'active', 'inactive', 'npc', 'pending' ];
			$result[ $u->userid ] = [
				'name' => $u->name,
				'online' => in_array( $u->userid, $usersOnline ),
				'chars' => [],
			];

			$charnum = 0;
			foreach ( $stasus as $charstatus ) {
				$charQuery = $this->ci->char->get_user_characters( $u->userid, $charstatus );
				$charnum += $charQuery->num_rows();
				foreach ( $charQuery->result() as $row ) {
					$result[ $u->userid ]['chars'][ $row->charid ] = [
						'status' => $charstatus,
						'name' => $this->ci->char->get_character_name( $row->charid, $settings['display_rank'] ),
						'email' => $u->email,
						'count' => $this->getItemCount( 'posts', $row->charid, $start, $end, 'char' ),
					];
				}
			}

			$result[$u->userid]['num'] = $charnum;
		}

		return $result;
	}

	/**
	 * Get a count for requested items, for the user or character
	 *
	 * @param  string  $type Item type. 'posts', 'news' or 'logs'
	 * @param  integer $id Character or user ID
	 * @param  integer $start Start date. If not given, will calculate from the
	 *  "beginning of time" (earliest that is available in the database)
	 * @param  integer $end End date. If not given, will calculate up to today
	 * @param  string  $who Who we are counting for. 'user' or 'char'. Defaults to 'user'
	 * @param  string  $status Item status, 'activated', 'saved' or 'pending'. Defaults to 'activated'
	 * @return integer Item count for the requested details
	 */
	protected function getItemCount( $type = 'posts', $id, $start = 0, $end = 0, $who = 'user', $status = 'activated', $isCount = true ) {
		$this->setRange( $start, $end );


		if ( $who === 'char' ) {
			if ( $type === 'posts' ) {
				$whereField = 'authors';
			} else if ( $type === 'logs' || $type === 'news' ) {
				$whereField = 'author_character';
			} else {
				$whereField = 'author';
			}
		} else {
			if ( $type === 'posts' ) {
				$whereField = 'authors_users';
			} else if ( $type === 'logs' || $type === 'news' ) {
				$whereField = 'author_user';
			} else {
				$whereField = 'author_user';
			}
		}

		switch ( $type ) {
			case 'logs':
				$prefix = 'log_';
				$table = 'personallogs';
				$where = "({$prefix}{$whereField} = $id)";
				break;
			case 'news':
				$prefix = 'news_';
				$table = 'news';
				$where = "({$prefix}{$whereField} = $id)";
				break;
			default:
			case 'posts':
				$prefix = 'post_';
				$table = 'posts';
				$where = "({$prefix}{$whereField} LIKE '%,$id' OR {$prefix}{$whereField} LIKE '$id,%' OR {$prefix}{$whereField} LIKE '%,$id,%' OR {$prefix}{$whereField} = $id)";
				break;
		}

		$count = 0;
		$this->ci->db->from($table);
		$this->ci->db->where($prefix . 'status', $status);

		$this->ci->db->where($prefix . 'date >=', $this->startTime);
		$this->ci->db->where($prefix . 'date <=', $this->endTime);

		$this->ci->db->where("($where)", null);

		return $isCount ? $this->ci->db->count_all_results() : $this->ci->db->get();
	}

	/**
	 * A general method to get the count per requested type and for the period requested.
	 *
	 * @param  string  $type Data type: 'logs', 'posts' or 'news'
	 * @param  integer $start Start date UNIX timestamp
	 * @param  integer $end End date UNIX timestamp
	 * @param  string  $status Item status, 'activated', 'saved' or 'pending'
	 * @return array An array where the keys are dates and the values are data item counts.
	 *  The given interval of the dates are by days.
	 */
	protected function getCountData(
		$type = 'posts',
		$start = 0,
		$end = 0,
		$status = 'activated',
		$countField = 'id'
	) {
		$this->setRange( $start, $end );

		$prefix = '';
		$table = '';
		switch ( $type ) {
			case 'posts':
				$prefix = 'post_';
				$table = 'posts';
				break;
			case 'logs':
				$prefix = 'log_';
				$table = 'personallogs';
				break;
			case 'news':
				$prefix = 'news_';
				$table = 'news';
				break;
			default:
				// Bail out if we don't recognize the type
				return [];
		}
		/*
		SELECT COUNT(log_id) as stat_count, DATE_FORMAT(FROM_UNIXTIME( log_date ), '%Y-%m-%d') as stat_date
		FROM (`nova_personallogs`)
		WHERE `log_status` =  'activated'
		GROUP BY stat_date
		 */
		$dateFormat = '%Y-%m-%d'; // Daily resolution
		switch ( $this->recurrence ) {
			case 'monthly':
				$dateFormat = '%Y-%m';
				break;
			case 'yearly':
				$dateFormat = '%Y';
				break;
		}
		$this->ci->db->select( [
			'COUNT(' . $prefix . $countField . ') as stat_count',
			'DATE_FORMAT(FROM_UNIXTIME( ' . $prefix . 'date ), \'' . $dateFormat . '\') as stat_date',
		] );

		$this->ci->db->from( $table );

		if ( ! empty($status))
		{
			$this->ci->db->where($prefix . 'status', $status);
		}
		$this->ci->db->where( $prefix . 'date >=', $this->startTime);
		$this->ci->db->where( $prefix . 'date <=', $this->endTime);

		$this->ci->db->group_by( 'stat_date' );

		$result = $this->ci->db->get();

		$data = [];
		foreach ( $result->result() as $res ) {
			$data[$res->stat_date] = $res->stat_count;
		}

		$data = $this->fillEmptyDates( $data, $this->startTime, $this->endTime );

		return $data;
	}


	/**
	 * Fill in values with 0s for dates, in the given range, that have
	 * no value. This is to make sure that the graph shows zeros on those
	 * dates rather than a line from one value directly to another.
	 *
	 * If forceStart and/or forceEnd aren't given, the zeros would be added
	 * to values between the dates that the given data object already has.
	 *
	 * @param  array  $data Array with date => counts values
	 * @param  integer [$forceStart] Start date
	 * @param  integer [$forceEnd] End date
	 * @return array Array with date => counts with daily values within the
	 *  requested dates.
	 */
	protected function fillEmptyDates( $data = [], $forceStart = 0, $forceEnd = 0 ) {
		$diffFormat = '%a'; // Daily resolution
		$dateFormat = 'Y-m-d';
		$label = 'days';
		switch ( $this->recurrence ) {
			case 'monthly':
				$dateFormat = 'Y-m';
				$diffFormat = '%m';
				$label = 'months';
				break;
			case 'yearly':
				$dateFormat = 'Y';
				$diffFormat = '%y';
				$label = 'years';
				break;
		}

		if ( count( $data ) === 0 ) {
			$earliest = new \DateTime( date( $dateFormat, $this->startTime ) );
			$latest = new \DateTime( date( $dateFormat, $this->endTime ) );
		} else {
			$dates = array_keys( $data );
			$earliest = new \DateTime( $dates[0] );
			$latest = new \DateTime( $dates[ count( $dates ) - 1 ] );
		}

		if ( $forceStart ) {
			$earliest = new \DateTime( date( $dateFormat, $forceStart ) );
		}
		if ( $forceEnd ) {
			$latest = new \DateTime( date( $dateFormat, $forceEnd ) );
		}

		// Difference in days. See https://php.net/manual/en/dateinterval.format.php
		$numOfDays = $latest->diff( $earliest )->format( $diffFormat );

		for ( $i = 0; $i < $numOfDays; $i++ ) {
			$iDate = date( $dateFormat, strtotime( '+' . $i . ' ' . $label, $earliest->getTimestamp() ) );

			if ( !isset($data[$iDate] ) ) {
				$data[$iDate] = 0;
			}
		}

		// Sort by key (date)
		ksort( $data );

		return $data;
	}

	/**
	 * Set date range.
	 *
	 * @param  integer $start Start date (unix timestamp).
	 *  If not given, will calculate from the "beginning of time"
	 *  (earliest that is available in the database)
	 * @param  integer $end End date (unix timestamp)
	 *  If not given, will calculate up to today
	 */
	protected function setRange( $start = 0, $end = 0 ) {
		// Default time
		// From the "beginning of time" computer-wise
		$this->startTime = mktime( 0, 0, 0, 1, 1, 1970 );
		// To now
		$this->endTime = time();

		if ( $start ) {
			$this->startTime = $start;
		}
		if ( $end ) {
			$this->endTime = $end;
		}
	}
}

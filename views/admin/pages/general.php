<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php echo text_output($header, 'h1', 'page-head');?>

<!-- div id="loading" class="loader">
	<?php echo img($images['loading']);?>
	<?php echo text_output($label['loading'], 'h3', 'gray');?>
</div -->

<?php echo text_output($labels['date_range'], 'h2');?>

<div id="ext-advancedStats-controls" class="ext-advancedStats-controls">
	<div class="ext-advancedStats-controls-date">
		From: <input id="ext-advancedStats-date-start" value="<?php echo $date['display']['start']; ?>">
		To: <input id="ext-advancedStats-date-end" value="<?php echo $date['display']['end']; ?>">
		(UTC+0:00)
	</div>
	<div class="ext-advancedStats-controls-settings">
		<?php echo form_button( $buttons['settings'] );?>
	</div>
</div>

<div id="ext-advancedStats-container" data-date-start="<?php echo $date['start']; ?>" data-date-end="<?php echo $date['end']; ?>" data-url="<?php echo site_url('extensions/AdvancedStats/') ?>" data-recurrence="<?php echo $recurrence;?>">
	<div class="ext-advancedStats-title">
		<span class="ext-advancedStats-title-text"><?php echo $labels['posting_activity']; ?></span>
		<span class="ext-advancedStats-title-dates fontTiny gray"><?php echo $date['display']['start'] . ' to ' . $date['display']['end']; ?></span>
	</div>
	<canvas id="ext-advancedStats-chart" width="400" height="120"></canvas>

	<div class="ext-advancedStats-title">
		<span class="ext-advancedStats-title-text"><?php echo $labels['user_stats']; ?></span>
		<span class="ext-advancedStats-title-dates fontTiny gray"><?php echo $date['display']['start'] . ' to ' . $date['display']['end']; ?></span>
	</div>

	<div id="ext-advancedStats-container-userstats">
		<span class="fontTiny"><?php echo $labels['sort_instruction']; ?></span>
		<table class="table100 zebra ext-advancedStats-userstat-table">
			<thead>
				<tr>
					<th data-sort="string"><?php echo $labels['name']; ?></th>
					<th data-sort="int"><?php echo $labels['count_posts']; ?></th>
					<th data-sort="int"><?php echo $labels['count_logs']; ?></th>
					<th data-sort="int"><?php echo $labels['count_news']; ?></th>
					<th data-sort="int"><?php echo $labels['count_totals']; ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($userstats['leaderboard'] as $leadid) : ?>
				<?php 	$u = $userstats['users'][$leadid]; ?>
				<tr class="userstats-user" data-userid="<?php echo $u['id'];?>">
					<td class="col_40pct">
						<strong class="fontMedium"><?php echo $u['name'];?></strong><br />
						<span class="fontTiny gray">
							<?php echo anchor('personnel/user/'. $u['id'], $labels['user_bio']);?> |
							<?php echo anchor('personnel/viewposts/u/'. $u['id'], $labels['user_posts']);?>
						</span><br />
						<span class="fontSmall gray">
							<strong><?php echo $u['email'];?></strong>
						</span>
					</td>
					<td class="align_center ext-advancedStats-userstats-count" data-userid="<?php echo $u['id'];?>" data-type="logs"><?php echo $u['counts']['logs']; ?></td>
					<td class="align_center ext-advancedStats-userstats-count" data-userid="<?php echo $u['id'];?>" data-type="news"><?php echo $u['counts']['news']; ?></td>
					<td class="align_center ext-advancedStats-userstats-count" data-userid="<?php echo $u['id'];?>" data-type="posts"><?php echo $u['counts']['posts']; ?></td>
					<td class="align_center ext-advancedStats-userstats-count" data-userid="<?php echo $u['id'];?>" data-type="total"><?php echo $u['counts']['total']; ?></td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	</div>

	<div class="ext-advancedStats-title">
		<span class="ext-advancedStats-title-text"><?php echo $labels['user_chars']; ?></span>
		<span class="ext-advancedStats-title-dates fontTiny gray"><?php echo $date['display']['start'] . ' to ' . $date['display']['end']; ?></span>
	</div>

</div>

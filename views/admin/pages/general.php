<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php echo text_output($header, 'h1', 'page-head');?>

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
	<div id="tabs">
		<ul>
			<li><a href="#one"><span><?php echo $labels['posting_activity'];?></span></a></li>
			<li><a href="#two"><span><?php echo $labels['user_stats'];?></span></a></li>
			<li><a href="#three"><span><?php echo $labels['char_stats'];?></span></a></li>
		</ul>

		<div id="one">
			<canvas id="ext-advancedStats-chart" width="400" height="200"></canvas>
		</div>

		<div id="two">
			<div id="ext-advancedStats-container-userstats">
				<span class="fontTiny"><?php echo $labels['sort_instruction']; ?></span>
				<table class="table100 ext-advancedStats-userstat-table ext-advancedStats-sortable">
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
						<?php foreach ($userstats['leaderboard'] as $leadid => $counts ) : ?>
							<?php 	$u = $userstats['users'][$leadid];?>
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
		</div>

		<div id="three">
			<div id="ext-advancedStats-container-charstats">
				<span class="fontTiny"><?php echo $labels['sort_instruction']; ?></span>

				<div id="sub_tabs">
					<ul>
						<li><a href="#sub-posts"><span><?php echo $labels['tabs_posts'];?></span></a></li>
						<li><a href="#sub-logs"><span><?php echo $labels['tabs_logs'];?></span></a></li>
						<li><a href="#sub-news"><span><?php echo $labels['tabs_news'];?></span></a></li>
					</ul>

					<div id="sub-posts">
						<table class="table100 ext-advancedStats-charstats-table-posts ext-advancedStats-sortable">
							<thead>
								<tr>
									<th data-sort="string"><?php echo $labels['char_name']; ?></th>
									<th data-sort="string"><?php echo $labels['char_positions']; ?></th>
									<th data-sort="string"><?php echo $labels['character_user']; ?></th>
									<th data-sort="int"><?php echo $labels['count_posts'];?><br /><span class="fontTiny"><?php echo $labels['count_posts_alone']; ?></span></th>
									<th data-sort="int"><?php echo $labels['count_posts'];?><br /><span class="fontTiny"><?php echo $labels['count_posts_with_others']; ?></span></th>
									<th data-sort="int"><?php echo $labels['count_posts'];?><br /><span class="fontTiny"><?php echo $labels['count_posts_total']; ?></span></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($charstats as $cid => $cdata ) : ?>
								<tr>
									<td class="col_30pct">
										<strong class="fontMedium"><?php echo $cdata['name'];?></strong><br />
										<span class="fontTiny gray">
											<?php echo anchor('personnel/character/'. $cid, $labels['character_bio']);?> |
											<?php echo anchor('personnel/viewposts/c/'. $cid, $labels['character_posts']);?>
										</span>
									</td>
									<td>
										<?php if ( $cdata['positions'][1] ) { ?>
											<span><?php echo $cdata['positions'][1]; ?></span><br />
										<?php } ?>
										<?php if ( $cdata['positions'][2] ) { ?>
											<span class="fontTiny"><?php echo $cdata['positions'][2]; ?></span>
										<?php } ?>
									</td>
									<td class="col_30pct">
										<?php if ( !$cdata['username'] ) { ?>
											<em><?php echo $labels['no_user'];?></em>
										<?php } else { ?>
											<strong><?php echo $cdata['username'];?></strong><br />
											<span class="fontTiny gray">
												<?php echo anchor('personnel/user/'. $cdata['userid'], $labels['user_bio']);?> |
												<?php echo anchor('personnel/viewposts/u/'. $cdata['userid'], $labels['user_posts']);?>
											</span>
										<?php } ?>
									</td>
									<td class="align_center ext-advancedStats-charstats-count" data-charid="<?php echo $cid;?>" data-type="posts_alone"><?php echo $cdata['counts']['posts']['alone']; ?></td>
									<td class="align_center ext-advancedStats-charstats-count" data-charid="<?php echo $cid;?>" data-type="posts_with_others"><?php echo $cdata['counts']['posts']['with_others']; ?></td>
									<td class="align_center ext-advancedStats-charstats-count" data-charid="<?php echo $cid;?>" data-type="posts_total"><?php echo $cdata['counts']['posts']['total']; ?></td>
								</tr>
							<?php endforeach;?>
							</tbody>
						</table>

					</div>

					<div id="sub-logs">
						<table class="table100 ext-advancedStats-charstats-table-logs ext-advancedStats-sortable">
							<thead>
								<tr>
									<th data-sort="string"><?php echo $labels['char_name']; ?></th>
									<th data-sort="string"><?php echo $labels['char_positions']; ?></th>
									<th data-sort="string"><?php echo $labels['character_user']; ?></th>
									<th data-sort="int"><?php echo $labels['count_logs']; ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($charstats as $cid => $cdata ) : ?>
								<tr>
									<td class="col_30pct">
										<strong class="fontMedium"><?php echo $cdata['name'];?></strong><br />
										<span class="fontTiny gray">
											<?php echo anchor('personnel/character/'. $cid, $labels['character_bio']);?> |
											<?php echo anchor('personnel/viewposts/c/'. $cid, $labels['character_posts']);?>
										</span>
									</td>
									<td>
										<?php if ( $cdata['positions'][1] ) { ?>
											<span><?php echo $cdata['positions'][1]; ?></span><br />
										<?php } ?>
										<?php if ( $cdata['positions'][2] ) { ?>
											<span class="fontTiny"><?php echo $cdata['positions'][2]; ?></span>
										<?php } ?>
									</td>
									<td class="col_30pct">
										<?php if ( !$cdata['username'] ) { ?>
											<em><?php echo $labels['no_user'];?></em>
										<?php } else { ?>
											<strong><?php echo $cdata['username'];?></strong><br />
											<span class="fontTiny gray">
												<?php echo anchor('personnel/user/'. $cdata['userid'], $labels['user_bio']);?> |
												<?php echo anchor('personnel/viewposts/u/'. $cdata['userid'], $labels['user_posts']);?>
											</span>
										<?php } ?>
									</td>
									<td class="align_center ext-advancedStats-charstats-count" data-charid="<?php echo $cid;?>" data-type="logs"><?php echo $cdata['counts']['logs']; ?></td>
								</tr>
							<?php endforeach;?>
							</tbody>
						</table>

					</div>

					<div id="sub-news">
						<table class="table100 ext-advancedStats-charstats-table-news ext-advancedStats-sortable">
							<thead>
								<tr>
									<th data-sort="string"><?php echo $labels['char_name']; ?></th>
									<th data-sort="string"><?php echo $labels['char_positions']; ?></th>
									<th data-sort="string"><?php echo $labels['character_user']; ?></th>
									<th data-sort="int"><?php echo $labels['count_news']; ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($charstats as $cid => $cdata ) : ?>
								<tr>
									<td class="col_30pct">
										<strong class="fontMedium"><?php echo $cdata['name'];?></strong><br />
										<span class="fontTiny gray">
											<?php echo anchor('personnel/character/'. $cid, $labels['character_bio']);?> |
											<?php echo anchor('personnel/viewposts/c/'. $cid, $labels['character_posts']);?>
										</span>
									</td>
									<td>
										<?php if ( $cdata['positions'][1] ) { ?>
											<span><?php echo $cdata['positions'][1]; ?></span><br />
										<?php } ?>
										<?php if ( $cdata['positions'][2] ) { ?>
											<span class="fontTiny"><?php echo $cdata['positions'][2]; ?></span>
										<?php } ?>
									</td>
									<td class="col_30pct">
										<?php if ( !$cdata['username'] ) { ?>
											<em><?php echo $labels['no_user'];?></em>
										<?php } else { ?>
											<strong><?php echo $cdata['username'];?></strong><br />
											<span class="fontTiny gray">
												<?php echo anchor('personnel/user/'. $cdata['userid'], $labels['user_bio']);?> |
												<?php echo anchor('personnel/viewposts/u/'. $cdata['userid'], $labels['user_posts']);?>
											</span>
										<?php } ?>
									</td>
									<td class="align_center ext-advancedStats-charstats-count" data-charid="<?php echo $cid;?>" data-type="news"><?php echo $cdata['counts']['news']; ?></td>
								</tr>
							<?php endforeach;?>
							</tbody>
						</table>

					</div>
				</div>



			</div>
		</div>

	</div>
</div>

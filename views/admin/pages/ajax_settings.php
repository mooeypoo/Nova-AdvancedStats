<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<?php echo text_output($header, 'h2');?>

<?php echo form_open('extensions/AdvancedStats/Ajax/settings/save');?>

<table class="table100">
	<tbody>
		<tr>
			<td colspan="3"><?php echo text_output($labels['headings']['graph_colors'], 'h3');?></td>
		</tr>
		<tr>
			<td class="cell-label"><?php echo $labels['colors']['missionposts'];?></td>
			<td class="cell-spacer"></td>
			<td><?php echo form_input($inputs['colors']['missionposts']);?></td>
		</tr>
		<tr>
			<td class="cell-label"><?php echo $labels['colors']['personallogs'];?></td>
			<td class="cell-spacer"></td>
			<td><?php echo form_input($inputs['colors']['personallogs']);?></td>
		</tr>
		<tr>
			<td class="cell-label"><?php echo $labels['colors']['newsitems'];?></td>
			<td class="cell-spacer"></td>
			<td><?php echo form_input($inputs['colors']['newsitems']);?></td>
		</tr>
		<tr>
			<td colspan="3"><?php echo text_output($labels['headings']['data_settings'], 'h3');?></td>
		</tr>
		<tr>
			<td class="cell-label"><?php echo $labels['headings']['graph_recurrence'];?></td>
			<td class="cell-spacer"></td>
			<td>
<?php foreach ( $inputs['recurrence'] as $name => $data ) {
	echo form_radio( $data ) . form_label( $labels['inputs']['recurrence'][$name] );
} ?>
			</td>
		</tr>
	</tbody>
</table>

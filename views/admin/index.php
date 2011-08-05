<?php if ($videos): ?>

	<?php echo form_open('admin/videos/action'); ?>

	<table border="0" class="table-list">
		<thead>
			<tr>
				<th width="20"><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all')); ?></th>
				<th><?php echo lang('video:video_label'); ?></th>
				<th><?php echo lang('video:channel_label'); ?></th>
				<th width="70"><?php echo lang('video:date_label'); ?></th>
				<th width="70"><?php echo lang('video:feature_label'); ?></th>
				<th><?php echo lang('video:schedule_on_label'); ?></th>
				<th width="180" class="align-center"><span><?php echo lang('video:actions_label'); ?></span></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($videos as $video): ?>
				<tr>
					<td><?php echo form_checkbox('action_to[]', $video->id); ?></td>
					<td><?php echo $video->title; ?></td>
					<td><?php echo $video->channel_title; ?></td>
					<td><?php echo format_date($video->created_on); ?></td>
					<td><?php echo $video->featured_on ? format_date($video->featured_on) : ''; ?></td>
					<td><?php echo $video->schedule_on >= now() ? format_date($video->schedule_on) : lang('video:live_label'); ?></td>
					<td class="align-center buttons buttons-small">
						<?php echo anchor('admin/videos/preview/' . $video->id, lang($video->schedule_on >= now() ? 'video:view_label' : 'video:preview_label'), 'rel="modal" class="iframe button preview" target="_blank"'); ?>
						<?php echo anchor('admin/videos/feature/' . $video->id, lang('video:feature_label'), 'class="button feature"'); ?>
						<?php echo anchor('admin/videos/edit/' . $video->id, lang('video:edit_label'), 'class="button edit"'); ?>
						<?php echo anchor('admin/videos/delete/' . $video->id, lang('video:delete_label'), array('class'=>'confirm button delete')); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div class="buttons align-right padding-top">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
	</div>

	<?php echo form_close(); ?>

<?php else: ?>
	<div class="blank-slate">
		<h2><?php echo lang('video:currently_no_videos'); ?></h2>
	</div>
<?php endif; ?>

<?php if ($channels): ?>

	<h3><?php echo lang('video:list_title'); ?></h3>

	<?php echo form_open('admin/videos/channels/delete'); ?>

	<table border="0" class="table-list">
		<thead>
		<tr>
			<th width="20"><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all')); ?></th>
			<th><?php echo lang('video:channel_label'); ?></th>
			<th width="200" class="align-center"><span><?php echo lang('global:actions'); ?></span></th>
		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3">
					<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($channels as $channel): ?>
			<tr>
				<td><?php echo form_checkbox('action_to[]', $channel->id); ?></td>
				<td><?php echo $channel->title; ?></td>
				<td class="align-center buttons buttons-small">
					<?php echo anchor('admin/videos/channels/edit/' . $channel->id, lang('global:edit'), 'class="button edit"'); ?>
					<?php echo anchor('admin/videos/channels/delete/' . $channel->id, lang('global:delete'), 'class="confirm button delete"') ;?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div class="buttons align-right padding-top">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete') )); ?>
	</div>

	<?php echo form_close(); ?>

<?php else: ?>
	<div class="blank-slate">
		<h2><?php echo lang('video:no_channels'); ?></h2>
	</div>
<?php endif; ?>

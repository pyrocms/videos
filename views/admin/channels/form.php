<section class="title">
	<?php if ($this->controller == 'admin_channels' && $this->method === 'edit'): ?>
		<h4><?php echo sprintf(lang('video_channel:edit_title'), $channel->title);?></h4>
	<?php else: ?>
		<h4><?php echo lang('video_channel:create_title');?></h4>
	<?php endif; ?>
</section>

<section class="item">
	
	<?php echo form_open_multipart('', 'class="form_inputs" id="channels"'); ?>

	<fieldset>
		<ul>
			<li class="even">
				<label for="title"><?php echo lang('global:title');?> <span>*</span></label>
				<?php echo form_input('title', $channel->title); ?>
			</li>
			<li>
				<label for="description"><?php echo lang('global:description');?> <span>*</span></label>
				<?php echo form_textarea('description', $channel->description); ?>
			</li>
			<li class="even">
				<label for="parent_id">Parent</label>
				<?php echo form_dropdown('parent_id', $channels, $channel->parent_id); ?>
			</li>
			<?php if (Settings::get('video_thumb_enabled')): ?>
			<li>
				<label for="thumbnail"><?php echo lang('video:thumbnail_label');?></label>
		
				<?php echo form_upload('thumbnail'); ?>
		
				<?php if ( ! empty($channel->thumbnail)): ?>
					<br /><img src="<?php echo base_url().UPLOAD_PATH.'videos/channel_thumbs/'.$channel->thumbnail ?>" />
				<?php endif; ?>
			</li>
			<?php endif ?>
		</ul>
	</fieldset>

	<div class="buttons">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?>
	</div>

	<?php echo form_close(); ?>
</section>
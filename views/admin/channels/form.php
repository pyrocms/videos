<section class="title">
	<?php if ($this->controller == 'admin_channels' && $this->method === 'edit'): ?>
		<h4><?php echo sprintf(lang('video_channel:edit_title'), $channel->title);?></h4>
	<?php else: ?>
		<h4><?php echo lang('video_channel:create_title');?></h4>
	<?php endif; ?>
</section>

<section class="item">
	<?php echo form_open_multipart('', 'class="crud" id="channels"'); ?>

	<ul>
		<li class="even">
			<label for="title"><?php echo lang('title_label');?></label><br>
			<?php echo form_input('title', $channel->title); ?>
			<span class="required-icon tooltip"><?php echo lang('required_label');?></span>
		</li>
		<hr>
		<li>
			<label for="description"><?php echo lang('global:description');?></label><br>
			<?php echo form_textarea('description', $channel->description); ?>
			<span class="required-icon tooltip"><?php echo lang('required_label');?></span>
		</li>
		<hr>
		<li class="even">
			<label for="parent_id">Parent</label><br>
			<?php echo form_dropdown('parent_id', $channels, $channel->parent_id); ?>
		</li>
		<?php if (Settings::get('video_thumb_enabled')): ?>
		<hr>
		<li>
			<label for="thumbnail"><?php echo lang('video:thumbnail_label');?></label><br>
		
			<?php echo form_upload('thumbnail'); ?>
		
			<?php if ( ! empty($channel->thumbnail)): ?>
				<br /><img src="<?php echo base_url().UPLOAD_PATH.'videos/channel_thumbs/'.$channel->thumbnail ?>" />
			<?php endif; ?>
		</li>
		<?php endif ?>
	</ul>

	<div class="buttons">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?>
	</div>

	<?php echo form_close(); ?>
</section>
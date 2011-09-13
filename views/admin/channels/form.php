<?php if ($this->controller == 'admin_channels' && $this->method === 'edit'): ?>
<h3><?php echo sprintf(lang('video_channel:edit_title'), $channel->title);?></h3>

<?php else: ?>
<h3><?php echo lang('video_channel:create_title');?></h3>

<?php endif; ?>

<?php echo form_open_multipart('', 'class="crud" id="channels"'); ?>

<fieldset>
	<ul>
		<li class="even">
			<label for="title"><?php echo lang('video_channel:title_label');?></label>
			<?php echo  form_input('title', $channel->title); ?>
			<span class="required-icon tooltip"><?php echo lang('required_label');?></span>
		</li>
		<li>
			<label for="description"><?php echo lang('global:description');?></label>
			<?php echo  form_textarea('description', $channel->description); ?>
			<span class="required-icon tooltip"><?php echo lang('required_label');?></span>
		</li>
		<?php if (Settings::get('video_thumb_enabled')): ?>
		<li class="even">
			<label for="thumbnail"><?php echo lang('video:thumbnail_label');?></label>
			
			<div style="float:left">
			<?php echo form_upload('thumbnail'); ?>
			
			<?php if ( ! empty($channel->thumbnail)): ?>
				<br /><img src="<?php echo base_url().UPLOAD_PATH.'videos/channel_thumbs/'.$channel->thumbnail ?>" />
			<?php endif; ?>
			</div>
		</li>
		<?php endif ?>
	</ul>

	<div class="buttons float-right padding-top">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?>
	</div>
</fieldset>

<?php echo form_close(); ?>

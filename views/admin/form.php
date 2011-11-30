<section class="title">
	
<?php if ($this->method == 'create'): ?>
	<h4><?php echo lang('video:create_title'); ?></h4>
<?php else: ?>
	<h4><?php echo sprintf(lang('video:edit_title'), $video->title); ?></h4>
<?php endif; ?>

</section>

<section class="item">
	
	<?php echo form_open_multipart(null, 'class="crud"'); ?>

	<div class="tabs">

		<ul class="tab-menu">
			<li><a href="#video-content-tab"><span><?php echo lang('video:details_label'); ?></span></a></li>
			<li><a href="#video-options-tab"><span><?php echo lang('video:options_label'); ?></span></a></li>
		</ul>

		<!-- Content tab -->
		<div id="video-content-tab" class="form_inputs">
			<fieldset>
				<ul>
					<li>
						<label for="title"><?php echo lang('video:title_label'); ?><span>*</span></label>
						<?php echo form_input('title', htmlspecialchars_decode($video->title), 'maxlength="100"'); ?>
					</li>

					<li>
						<label for ="slug"><?php echo lang('video:slug_label'); ?><span>*</span></label>
						<?php echo site_url('videos/view') ?>/<?php echo form_input('slug', $video->slug, 'maxlength="100" class="width-20"'); ?>
					</li>
			
					<?php if (Settings::get('video_thumb_enabled')): ?>
					<li>
						<label for="thumbnail"><?php echo lang('video:thumbnail_label'); ?><span>*</span></label>
	
							<?php echo form_upload('thumbnail'); ?>

							<?php if ( ! empty($video->thumbnail)): ?>
								<br /><img src="<?php echo base_url().UPLOAD_PATH.'videos/thumbs/'.$video->thumbnail ?>" />
							<?php endif; ?>
					</li>
					
					<? endif; ?>
					<li class="even">
						<label for="channel_id"><?php echo lang('video:channel_label'); ?></label>
						
						<select name="channel_id">
					
							<option value=""><?php echo lang('select.none') ?></option>
					
							<?php foreach ($channels[0] as $channel): ?>
								<option value="<?php echo $channel->id ?>" <?php echo $video->channel_id == $channel->id ? 'selected="selected"' : '' ?>><?php echo $channel->title; ?></option>

								<?php if ( ! empty($channels[$channel->id])): ?>
									<?php foreach ($channels[$channel->id] as $channel): ?>
									<option value="<?php echo $channel->id ?>" <?php echo $video->channel_id == $channel->id ? 'selected="selected"' : '' ?>><?php echo '-- '.$channel->title; ?></option>
									<?php endforeach; ?>
								<?php endif ?>
							<?php endforeach; ?>
							</select>
				
							[ <?php echo anchor('admin/videos/channels/create', lang('video:new_channel_label'), 'target="_blank"'); ?> ]
					</li>
				
					<li>
						<label for="intro"><?php echo lang('video:intro_label'); ?><span>*</span></label>
						<?php echo form_input('intro', htmlspecialchars_decode($video->intro), 'maxlength="80"'); ?>
					</li>

					<li class="even">
						<label for="keywords"><?php echo lang('global:keywords'); ?></label>
						<?php echo form_input('keywords', $video->keywords); ?>
					</li>

					<li>
						<label class="description" for="description"><?php echo lang('global:description'); ?><span>*</span></label>
						<br style="clear: both;">
						<?php echo form_textarea(array('name' => 'description', 'value' => $video->description, 'rows' => 5, 'class' => 'wysiwyg-simple')); ?>
					</li>

					<li class="even">
						<label for="embed_code"><?php echo lang('video:embed_code_label'); ?><span>*</span></label>
						<?php echo form_textarea('embed_code', $video->embed_code); ?>
					</li>
				</ul>
			
				<?php echo form_hidden('width', $video->width); ?>
				<?php echo form_hidden('height', $video->height); ?>
			</fieldset>
		</div>

		<!-- Options tab -->
		<div id="video-options-tab" class="form_inputs">
			<fieldset>
				<ul>
					<li class="date-meta">
						<label for="schedule_on"><?php echo lang('video:schedule_on_label');?></label>
						<div style="float:left;">
							<?php echo form_input('schedule_on', date('Y-m-d', $video->schedule_on), 'maxlength="10" id="datepicker" class="text width-20"'); ?>
						</div>
						<label class="time-meta"><?php echo lang('video:time_label'); ?></label>
						<?php echo form_dropdown('schedule_on_hour', $hours, date('H', $video->schedule_on)) ?>
						<?php echo form_dropdown('schedule_on_minute', $minutes, date('i', $video->schedule_on)) ?>
				
					</li>
					
					<li class="even">
						<label for="restricted_to[]"><?php echo lang('video:access_label');?></label>
						<?php echo form_multiselect('restricted_to[]', array(0 => lang('select.any')) + $group_options, $video->restricted_to, 'size="'.(($count = count($group_options)) > 1 ? $count : 2).'"'); ?>
					</li>
					
					<li>
						<label for="comments_enabled"><?php echo lang('video:comments_enabled_label');?></label>
						<?php echo form_checkbox('comments_enabled', 1, ($this->method == 'create' && ! $_POST) or $video->comments_enabled == 1); ?>
					</li>
				</ul>
			</fieldset>
		</div>

	</div>

	<div class="buttons">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'save_exit', 'cancel'))); ?>
	</div>

	<?php echo form_close(); ?>
</section>

<style type="text/css">
form.crudli.date-meta div.selector {
    float: left;
    width: 30px;
}
form.crud li.date-meta div input#datepicker { width: 8em; }
form.crud li.date-meta div.selector { width: 5em; }
form.crud li.date-meta div.selector span { width: 1em; }
form.crud li.date-meta label.time-meta { min-width: 4em; width:4em; }
</style>
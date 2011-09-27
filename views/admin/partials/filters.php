<div class="filter">
<?php echo form_open(); ?>
<?php echo form_hidden('f_module', $module_details['slug']); ?>
<ul>  
	<li>
            <?php echo lang('video:status_label', 'f_status'); ?>
            <?php echo form_dropdown('f_status', array(0 => lang('select.all'), 'draft'=>lang('video:draft_label'), 'live'=>lang('video:live_label'))); ?>
        </li>
	<li>
            <?php echo lang('video:channel_label', 'f_category'); ?>

			<select name="f_channel">
				
				<option value=""><?php echo lang('select.all') ?></option>
				
				<?php foreach ($channels[0] as $channel): ?>
					<option value="<?php echo $channel->id ?>" <?php echo set_select('f_channel') ?>><?php echo $channel->title; ?></option>

					<?php if ( ! empty($channels[$channel->id])): ?>
						<?php foreach ($channels[$channel->id] as $channel): ?>
						<option value="<?php echo $channel->id ?>" <?php echo set_select('f_channel') ?>><?php echo '-- '.$channel->title; ?></option>
						<?php endforeach; ?>
					<?php endif ?>
				<?php endforeach; ?>
				</select>
				
        </li>
	<li><?php echo form_input('f_keywords'); ?></li>
	<li><?php echo anchor(current_url() . '#', lang('buttons.cancel'), 'class="cancel"'); ?></li>
</ul>
<?php echo form_close(); ?>
<br class="clear-both">
</div>
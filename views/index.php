<?php if ( ! empty($videos)): ?>
	
	<?php function display_videos($videos) { foreach ($videos as $video): ?>
	
	<div class="video listing <?php echo $video->channel_parent_id ? 'child' : 'parent' ?>">
		<!-- Post heading -->
		<div class="heading">

			<?php if (Settings::get('video_thumb_enabled') && $video->thumbnail): ?>
				<div class="thumbnail">
					<img src="<?php echo base_url().UPLOAD_PATH.'videos/thumbs/'.$video->thumbnail ?>" width="<?php echo $thumb_width ?>" />
				</div>
			<?php endif; ?>

			<h4><?php echo $video->title; ?></h4>
		</div>
		<div class="intro"><?php echo $video->intro; ?></div>
		
	</div>

	<?php endforeach; } ?>
	
	<?php foreach ($channels[0] as $channel): ?>
		
		<?php if (empty($videos[$channel->id]) and empty($channels[$channel->id])) continue; ?>
		
		<h3 class="channel"><?php echo anchor('videos/channel/'.$channel->slug, $channel->title);?></h3>
		
		<?php if ( ! empty($channels[$channel->id])): ?>
	
			<?php foreach ($channels[$channel->id] as $sub_channel): ?>
		
			<?php if (empty($videos[$sub_channel->id])) continue ?>
		
			<h3 class="channel child"><?php echo anchor('videos/channel/'.$channel->slug.'/'.$sub_channel->slug, $sub_channel->title);?></h3>
		
			<?php echo display_videos($videos[$sub_channel->id]) ?>
		
			<?php endforeach; ?>
		
		<?php endif ?>
		
		<?php if (empty($videos[$channel->id])) continue ?>
		
		<?php echo display_videos($videos[$channel->id]) ?>
		
	<?php endforeach; ?>

	<?php echo $pagination['links']; ?>

<?php else: ?>
	<p><?php echo lang('video:currently_no_videos');?></p>
<?php endif; ?>

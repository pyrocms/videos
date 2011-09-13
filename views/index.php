<?php if ( ! empty($videos)): ?>
	
	<?php foreach ($videos as $video): ?>
	
		<div class="video listing">
			<!-- Post heading -->
			<div class="heading">
	
				<?php if (Settings::get('video_thumb_enabled')): ?>
					<?php if ($video->thumbnail): ?>
					<div class="thumbnail">
						<img src="<?php echo base_url(UPLOAD_PATH.'videos/thumbs/'.$video->thumbnail) ?>" width="<?php echo $thumb_width ?>" />
					</div>
				
					<?php else: ?>
					<!-- No thumbnail -->
					<!--<div class="thumbnail missing" style="width: <?php echo $thumb_width ?>px; height: <?php echo $thumb_height ?>px">
					</div>-->
					<?php endif; ?>
				<?php endif; ?>

				<h3><?php echo anchor('videos/view/'. $video->slug, $video->title); ?></h3>
				<div class="date"><?php echo lang('video:date_label');?>: <?php echo format_date($video->created_on); ?></div>
				<?php if ($video->channel_slug): ?>
				<div class="channel">
					<?php echo lang('video:channel_label');?>: <?php echo anchor('videos/channel/'.$video->channel_slug, $video->channel_title);?>
				</div>
				<?php endif; ?>
			</div>
			<div class="intro"><?php echo $video->intro; ?></div>
		</div>
	<?php endforeach; ?>

	<?php echo $pagination['links']; ?>

<?php else: ?>
	<p><?php echo lang('video:currently_no_videos');?></p>
<?php endif; ?>

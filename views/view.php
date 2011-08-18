<div class="video view">
	
	<div class="heading">
		
		<h2 style="display:inline-block"><?php echo $video->title; ?></h2>
		
		<p><?php echo $video->description ?></p>
		
	</div>
	
	<div class="body">
		<?php echo $video->embed_code; ?>
	</div>
	
	<div class="details">
		
		<ul>
			<li class="date"><span class="date-label"><?php echo lang('video:video_label');?>: </span><?php echo format_date($video->created_on); ?></li>
			
			<li class="channel">
				<?php echo lang('video:channel_label');?>: <?php echo anchor('videos/channel/'.$video->channel->slug, $video->channel->title);?>
			</li>
		
			<li class="views">
				<?php echo lang('video:views_label');?>: <?php echo $video->views ?>
			</li>

			<li class="tags">
				<?php echo lang('global:keywords');?>: 
				
				<?php if ($video->keywords): ?>
				<ul>
				<?php foreach ($video->keywords as $keyword): ?>
				<li><?php echo anchor('videos/search?q='.$keyword, '#'.$keyword) ?></li>
				<?php endforeach; ?>
				</ul>
				<?php else: ?>
				<span class="no-tags">None</span>
				<?php endif; ?>
			</li>
		</ul>
		
	</div>

	<?php if ( ! empty($related_videos)): ?>
		
		<div class="related">

		<h3><?php echo lang('video:related_videos') ?></h3>

		<?php foreach ($related_videos as $related_video): ?>
		
			<?php /* HACK */ if ($video->id == $related_video->id) continue; ?>
	
			<div class="video">

				<h4><?php echo anchor('videos/view/'. $related_video->slug, $related_video->title); ?></h4>

				<?php if (Settings::get('video_thumb_enabled')): ?>
				
					<?php if ($related_video->thumbnail): ?>
					<div class="thumbnail">
						<a href="<?php echo site_url('videos/view/'. $related_video->slug) ?>">
							<img src="<?php echo base_url().UPLOAD_PATH ?>videos/thumbs/<?php echo $related_video->thumbnail ?>" width="<?php echo $thumb_width ?>" />
						</a>
					</div>
			
					<?php else: ?>
					<div class="thumbnail missing" style="width: <?php echo $thumb_width ?>px;">
					</div>
					<?php endif; ?>
				
				<?php endif ?>
			
				<div class="intro"><?php echo $related_video->intro; ?></div>

				<br style="clear:both" />
			</div>

		<?php endforeach; ?>

		</div>
	
	<?php endif; ?>

	<?php if ( ! empty($channel_videos)): ?>
		<div class="same-channel">

		<h3><?php echo sprintf(lang('video:channel_videos'), $video->channel->slug) ?></h3>

		<?php foreach ($channel_videos as $channel_video): ?>
	
			<div class="video">

				<h4><?php echo anchor('videos/view/'. $channel_video->slug, $channel_video->title); ?></h4>

				<?php if ($channel_video->thumbnail): ?>
				<div class="thumbnail">
					<a href="<?php echo site_url('videos/view/'. $channel_video->slug) ?>">
						<img src="<?php echo base_url().UPLOAD_PATH ?>videos/thumbs/<?php echo $related_video->thumbnail ?>" width="<?php echo $thumb_width ?>" />
					</a>
				</div>
			
				<?php else: ?>
				<div class="thumbnail missing" style="width: <?php echo $thumb_width ?>px;">
				</div>
				<?php endif; ?>

				<div class="intro"><?php echo $channel_video->intro; ?></div>

				<br style="clear:both" />
			</div>

		<?php endforeach; ?>

		<?php echo anchor('videos/channel/'.$video->channel->slug, lang('video:more_videos'), 'id="view-more"') ?>

		</div>
	<?php endif; ?>

<?php if ($video->comments_enabled or 1): ?>
	<?php echo display_comments($video->id); ?>
<?php endif; ?>

</div>

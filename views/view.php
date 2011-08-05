<div class="video view">
	
	<div class="heading">
		
		<h2 style="display:inline-block"><?php echo $video->title; ?></h2>
		
		<p><?php echo $video->description ?>
		
	</div>
	
	<div class="body">
		<?php echo $video->embed_code; ?>
	</div>
	
	<div class="details">
		
		<ul>
			<li class="date"><span class="date-label"><?php echo lang('video:videoed_label');?>: </span><?php echo format_date($video->created_on); ?></li>
			
			<li class="channel">
				<?php echo lang('video:channel_label');?>: <?php echo anchor('videos/channel/'.$video->channel->slug, $video->channel->title);?>
			</li>
		
			<li class="views">
				<?php echo lang('video:views_label');?>: <?php echo $video->views ?>
			</li>

			<li class="tags">
				<?php echo lang('video:tags_label');?>: 
				
				<?php if ($video->tags): ?>
				<ul>
				<?php foreach (explode(',', $video->tags) as $tag): ?>
				<li><?php echo anchor('videos/search?q='.trim($tag), '#'.trim($tag)) ?></li>
				<?php endforeach; ?>
				</ul>
				<?php else: ?>
				<span class="no-tags">None</span>
				<?php endif; ?>
			</li>
		</ul>
		
	</div>
	
</div>

<?php if ($video->comments_enabled): ?>
	<?php echo display_comments($video->id); ?>
<?php endif; ?>

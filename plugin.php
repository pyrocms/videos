<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Videos Plugin
 *
 * Create lists of posts
 *
 * @package		PyroCMS
 * @author		PyroCMS Dev Team
 * @copyright	Copyright (c) 2008 - 2011, PyroCMS
 *
 */
class Plugin_Videos extends Plugin
{
	/**
	 * Blog List
	 *
	 * Creates a list of blog posts
	 *
	 * Usage:
	 * {pyro:videos:videos order-by="title" limit="5"}
	 *	<h2>{pyro:title}</h2>
	 *	{pyro:embed_code}
	 * {/pyro:videos:videos}
	 *
	 * @param	array
	 * @return	array
	 */
	public function videos()
	{
		$limit		= $this->attribute('limit', 10);
		$channel	= $this->attribute('channel');
		$order_by 	= $this->attribute('order-by', 'created_on');
		$order_dir	= $this->attribute('order-dir', 'ASC');

		if ($channel)
		{
			$this->db->where('video_channels.' . (is_numeric($channel) ? 'id' : 'slug'), $channel);
		}

		return $foo = $this->db
			->select('videos.*, video_channels.title as channel_title, video_channels.slug as channel_slug')
			->where('schedule_on <=', now())
			->join('video_channels', 'videos.channel_id = video_channels.id', 'LEFT')
			->order_by('videos.' . $order_by, $order_dir)
			->limit($limit)
			->get('videos')
			->result();
			
			var_dump($foo);
	}
	
	/**
	 * Featured List
	 *
	 * Creates a list of blog posts
	 *
	 * Usage:
	 * {pyro:videos:featured limit="2"}
	 *	<h2>{pyro:title}</h2>
	 *	{pyro:embed_code}
	 * {/pyro:videos:featured}
	 *
	 * @param	array
	 * @return	array
	 */
	public function featured()
	{
		$width		= $this->attribute('width');

		$limit		= $this->attribute('limit', 1);
		$order_by 	= $this->attribute('order-by', 'featured_on');
		$order_dir	= $this->attribute('order-dir', 'DESC');

		$videos = $this->db
			->select('videos.*, video_channels.title as channel_title, video_channels.slug as channel_slug')
			->where('schedule_on <=', now())
			->where('featured_on > 0')
			->join('video_channels', 'videos.channel_id = video_channels.id', 'LEFT')
			->order_by('videos.' . $order_by, $order_dir)
			->limit($limit)
			->get('videos')
			->result();

		$html = '';

		foreach ($videos as &$video)
		{
			// Custom width detected, lets do some awkward shit
			if ($width)
			{
				$ratio = $width / $video->width;

				$new_width = round($width);
				$new_height = round($video->height * $ratio);

				$video->embed_code = str_replace(array(
					'width="'.$video->width.'"',
					'height="'.$video->height.'"',
					'width:'.$video->width.'px',
					'height:'.$video->height.'px',
				), array(
					'width="'.$new_width.'"',
					'height="'.$new_height.'"',
					'width:'.$new_width.'px',
					'height:"'.$new_height.'px',
				), $video->embed_code);
			}

			// Single tag? Build up HTML
			$this->content() or $html .= $video->embed_code;
		}

		return $this->content() ? $videos : $html;
	}


	/**
	 * Channel List
	 *
	 * Creates a list of channels
	 *
	 * Usage:
	 * {pyro:videos:channels order-by="title" limit="5" include-count="yes"}
	 *	<h2>{pyro:title}</h2>
	 *	There are {pyro:video_count} video(s) in this channel
	 * {/pyro:videos:channels}
	 *
	 * @param	array
	 * @return	array
	 */
	public function channels()
	{
		$limit			= $this->attribute('limit');
		$order_by 		= $this->attribute('order-by', 'created_on');
		$order_dir		= $this->attribute('order-dir', 'ASC');
		$include_count 	= (bool) in_array(strtolower($this->attribute('include-count')), array('y', 'yes', 'true'));
		
		$this->db
			->select('video_channels.id, video_channels.title, video_channels.slug, video_channels.description, video_channels.thumbnail')
			->select('pvc.id as parent_id, pvc.title as parent_title, pvc.slug as parent_slug');
		
		if ($include_count)
		{
			$this->db->select('(SELECT count(id) FROM '.$this->db->dbprefix('videos').' v WHERE '.$this->db->dbprefix('video_channels').'.id = v.channel_id) as video_count	', FALSE);
			$this->db->having('video_count > 0');
		}
		
		$limit && $this->db->limit($limit);

		return $this->db
			->order_by($order_by, $order_dir)
			->join('video_channels pvc', 'pvc.id = video_channels.parent_id', 'left')
			->get('video_channels')
			->result_array();
	}
}

/* End of file plugin.php */
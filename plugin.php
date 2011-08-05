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
	 * {pyro:videos:list order-by="title" limit="5"}
	 *	<h2>{pyro:title}</h2>
	 *	{pyro:embed_code}
	 * {/pyro:videos:list}
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
			$this->db->where('c.' . (is_numeric($channel) ? 'id' : 'slug'), $channel);
		}

		return $this->db
			->select('videos.*, video_channels.title as channel_title, video_channels.slug as channel_slug')
			->where('schedule_on <=', now())
			->join('video_channels', 'videos.channel_id = video_channels.id', 'LEFT')
			->order_by('videos.' . $order_by, $order_dir)
			->limit($limit)
			->get('videos')
			->result_array();
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

		// Custom width detected, lets do some awkward shit
		if ($width)
		{
			foreach ($videos as &$video)
			{
				$ratio = $width / $video->width;

				$new_width = round($width);
				$new_height = round($video->height * $ratio);

				$video->embed_code = str_replace(array(
					'width="'.$video->width.'"',
					'height="'.$video->height.'"',
				), array(
					'width="'.$new_width.'"',
					'height="'.$new_height.'"',
				), $video->embed_code);
			}
		}

		return $videos;
	}


	/**
	 * Blog List
	 *
	 * Creates a list of blog posts
	 *
	 * Usage:
	 * {pyro:videos:list order-by="title" limit="5"}
	 *	<h2>{pyro:title}</h2>
	 *	{pyro:embed_code}
	 * {/pyro:videos:list}
	 *
	 * @param	array
	 * @return	array
	 */
	public function channels()
	{
		$limit		= $this->attribute('limit', 10);
		$order_by 	= $this->attribute('order-by', 'created_on');
		$order_dir	= $this->attribute('order-dir', 'ASC');

		return $this->db
		->order_by($order_by, $order_dir)
			->get('video_channels')
			->result_array();
	}
}

/* End of file plugin.php */

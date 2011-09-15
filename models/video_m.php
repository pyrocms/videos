<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Video_m extends MY_Model {

	public function get_all()
	{
		$this->db->select('videos.*, video_channels.title AS channel_title, video_channels.slug AS channel_slug');
		$this->db->join('video_channels', 'videos.channel_id = video_channels.id', 'left');

		$this->db->order_by('schedule_on', 'DESC');

		return $this->db->get('videos')->result();
	}
	

	public function get_search($query)
	{
		 return $this->db->query('
			SELECT 
				v.*, vc.title AS channel_title, vc.slug AS channel_slug,
				MATCH (v.title, intro, v.description) AGAINST (?) as relevance
			FROM '.$this->db->dbprefix('videos').' v
			JOIN '.$this->db->dbprefix('video_channels').' vc ON channel_id = vc.id
			WHERE schedule_on <= '.now().'
			HAVING relevance > 0
			ORDER BY relevance DESC
		', array($query))->result();
	}
	
	public function get_related($video, $limit = null)
	{
		if (empty($video->tags))
		{
			return array();
		}

		foreach (explode(',', $video->tags) as $tag)
		{
			$tag = trim($tag);
	
			$this->db
				->or_like('tags', $tag.",", 'after')
				->or_like('tags', $tag, 'before')
				->or_like('tags', $tag.",", 'both')
				->or_like('tags', $tag);
		}

		// TODO: CodeIgniter will shove this in without groupings . Hacked in view for now
		// $this->db->where('id !=', $video->id);

		$this->db->order_by('RAND()', null, false);

		return $this->db->get('videos')->result();
	}

	public function count_by($params = array())
	{
		$this->db->join('video_channels', 'videos.channel_id = video_channels.id', 'left');

		if ( ! empty($params['channel']))
		{
			if (is_numeric($params['channel']))
				$this->db->where('video_channels.id', $params['channel']);
			else
				$this->db->where('video_channels.slug', $params['channel']);
		}

		// Is a status set?
		if (empty($params['show_future']))
		{
			$this->db->where('schedule_on <=', now());
		}

		return $this->db->count_all_results('videos');
	}

	public function insert($input)
	{
		$input['created_on'] = now();
		
		if ( ! empty($input['channel_id']))
		{
			$last_video = $this->db
				->select('episode')
				->order_by('episode', 'desc')
				->limit(1)
				->where('episode >', 0)
				->where('channel_id', $input['channel_id'])
				->get('videos')
				->row();
			
			$input['episode'] = $last_video ? $last_video->episode + 1 : 1;
		}
		
		return parent::insert($input);
	}

	public function update($id, $input)
	{
		$input['updated_on'] = now();
		
		$result = parent::update($id, $input);
		
		if ( ! empty($input['channel_id']))
		{
			$videos = $this->db
				->select('id')
				->where('channel_id', $input['channel_id'])
				->order_by('created_on')
				->get('videos')
				->result();
		
			$i = 0;
			foreach ($videos as $video)
			{
				$this->db
					->set('episode', ++$i)
					->where('id', $video->id)
					->update('videos');
			}
		}
		
		return $result;
	}
	
	public function update_views($id)
	{
		return $this->db
			->set('views', 'views + 1', false)
			->where('id', $id)
			->update('videos');
	}

	public function check_exists($field, $value = '', $id = 0)
	{
		if (is_array($field))
		{
			$params = $field;
			$id = $value;
		}
		else
		{
			$params[$field] = $value;
		}
		$params['id !='] = (int) $id;

		return parent::count_by($params) == 0;
	}

	/**
	 * Searches blog posts based on supplied data array
	 * @param $data array
	 * @return array
	 */
	public function search($data = array())
	{
		if (array_key_exists('channel_id', $data))
		{
			$this->db->where('channel_id', $data['channel_id']);
		}

		if (array_key_exists('keywords', $data))
		{
			$matches = array();
			if (strstr($data['keywords'], '%'))
			{
				preg_match_all('/%.*?%/i', $data['keywords'], $matches);
			}

			if ( ! empty($matches[0]))
			{
				foreach ($matches[0] as $match)
				{
					$phrases[] = str_replace('%', '', $match);
				}
			}
			else
			{
				$temp_phrases = explode(' ', $data['keywords']);
				foreach ($temp_phrases as $phrase)
				{
					$phrases[] = str_replace('%', '', $phrase);
				}
			}

			$counter = 0;
			foreach ($phrases as $phrase)
			{
				if ($counter == 0)
				{
					$this->db->like('videos.title', $phrase);
				}
				else
				{
					$this->db->or_like('videos.title', $phrase);
				}

				$this->db->or_like('videos.body', $phrase);
				$this->db->or_like('videos.intro', $phrase);
				$counter++;
			}
		}
		return $this->get_all();
	}

}

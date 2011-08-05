<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Video_m extends MY_Model {

	public function get_all()
	{
		$this->db->select('videos.*, video_channels.title AS channel_title, video_channels.slug AS channel_slug');
		$this->db->join('video_channels', 'videos.channel_id = video_channels.id', 'left');

		$this->db->order_by('schedule_on', 'DESC');

		return $this->db->get('videos')->result();
	}
	

	public function get_many_by($params = array())
	{
		if ( ! empty($params['channel']))
		{
			if (is_numeric($params['channel']))
				$this->db->where('video_channels.id', $params['channel']);
			else
				$this->db->where('video_channels.slug', $params['channel']);
		}

		// Limit the results based on 1 number or 2 (2nd is offset)
		if (isset($params['limit']) && is_array($params['limit']))
			$this->db->limit($params['limit'][0], $params['limit'][1]);
		elseif (isset($params['limit']))
			$this->db->limit($params['limit']);

		return $this->get_all();
	}

	public function get_search($query)
	{
		 return $this->db->query('
			SELECT 
				v.*, vc.title AS channel_title, vc.slug AS channel_slug,
				MATCH (v.title, intro, tags, v.description) AGAINST (?) as relevance
			FROM '.$this->db->dbprefix('videos').' v
			JOIN '.$this->db->dbprefix('video_channels').' vc ON channel_id = vc.id
			HAVING relevance > 0
			WHERE schedule_on <= '.now().'
			ORDER BY relevance DESC
		', array($query))->result();
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
		return parent::insert($input);
	}

	public function update($id, $input)
	{
		$input['updated_on'] = now();
		return parent::update($id, $input);
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

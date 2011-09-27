<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Categories model
 *
 * @package		PyroCMS
 * @subpackage	Video Module
 * @category	Modules
 * @author		Phil Sturgeon - PyroCMS Dev Team
 */
class Video_channel_m extends MY_Model
{
	/**
	 * Insert a new channel into the database
	 * @access public
	 * @param array $input The data to insert
	 * @return string
	 */
	public function insert($input = array())
	{
		return parent::insert(array(
			'title' => $input['title'],
			'slug' => url_title(strtolower(convert_accented_characters($input['title']))),
			'description' => $input['description'],
			'parent_id' => $input['parent_id'],
			'thumbnail' => isset($input['thumbnail']) ? $input['thumbnail'] : NULL,
		));
	}
    
	public function count_videos($id)
	{
		return $this->db
			->where('channel_id', $id)
			->count_all_results('videos');
	}

	/**
	 * Update an existing channel
	 * @access public
	 * @param int $id The ID of the channel
	 * @param array $input The data to update
	 * @return bool
	 */
	public function update($id, $input)
	{
		$input['slug'] = url_title(strtolower(convert_accented_characters($input['title'])));
		return parent::update($id, $input);
	}

	/**
	 * Callback method for validating the title
	 * @access public
	 * @param string $title The title to validate
	 * @return mixed
	 */
	public function check_title($title = '')
	{
		return parent::count_by('slug', url_title($title)) > 0;
	}
}
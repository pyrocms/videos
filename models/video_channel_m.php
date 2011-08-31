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
		parent::insert(array(
			'title'=>$input['title'],
			'slug'=>url_title(strtolower(convert_accented_characters($input['title']))),
			'description' => $input['description'],
		));
		
		return $input['title'];
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
		return parent::update($id, array(
			'title'	=> $input['title'],
		    'slug'	=> url_title(strtolower(convert_accented_characters($input['title']))),
			'description' => $input['description'],
		));
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
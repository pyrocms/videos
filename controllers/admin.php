<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * @package  	PyroCMS
 * @subpackage  Categories
 * @channel  	Module
 */
class Admin extends Admin_Controller {

	/**
	 * Array that contains the validation rules
	 * @access protected
	 * @var array
	 */
	protected $validation_rules = array(
		array(
			'field' => 'title',
			'label' => 'lang:video:title_label',
			'rules' => 'trim|htmlspecialchars|required|max_length[100]'
		),
		array(
			'field' => 'slug',
			'label' => 'lang:video:slug_label',
			'rules' => 'trim|required|alpha_dot_dash|max_length[100]|callback__check_slug'
		),
		array(
			'field' => 'channel_id',
			'label' => 'lang:video:channel_label',
			'rules' => 'trim|required|numeric'
		),
		array(
			'field' => 'keywords',
			'label' => 'lang:global:keywords',
			'rules' => 'trim',
		),
		array(
			'field' => 'intro',
			'label' => 'lang:video:intro_label',
			'rules' => 'trim|max_length[80]|required',
		),
		array(
			'field' => 'description',
			'label' => 'lang:video:description_label',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'embed_code',
			'label' => 'lang:video:embed_code_label',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'width',
			'label' => 'lang:video:width_label',
			'rules' => 'trim|required|numeric'
		),
		array(
			'field' => 'height',
			'label' => 'lang:video:height_label',
			'rules' => 'trim|required|numeric'
		),
		array(
			'field' => 'restricted_to[]',
			'label'	=> 'lang:video:access_label',
			'rules'	=> 'trim|numeric'
		),
		array(
			'field' => 'schedule_on',
			'label' => 'lang:video:schedule_on_label',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'comments_enabled',
			'label'	=> 'lang:video:comments_enabled_label',
			'rules'	=> 'trim|numeric'
		)
	);

	/**
	 * The constructor
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->library('keywords/keywords');
		$this->load->model('video_m');
		$this->load->model('video_channel_m');
		$this->lang->load('video');
		$this->lang->load('channel');

		$this->data->channels = array();
		if ($channels = $this->video_channel_m->order_by('title')->get_all())
		{
			foreach ($channels as $channel)
			{
				$this->data->channels[$channel->id] = $channel->title;
			}
		}

		$this->template->set_partial('shortcuts', 'admin/partials/shortcuts');
	}

	/**
	 * Show all created video
	 * @access public
	 * @return void
	 */
	public function index()
	{
		$base_where = array();//array('status' => 'all');
		
		//add post values to base_where if f_module is posted
		$this->input->post('f_channel') and $base_where += array('channel' => $this->input->post('f_channel'));

		//$base_where['status'] = $this->input->post('f_status') ? $this->input->post('f_status') : $base_where['status'];

		// Create pagination links
		$total_rows = $this->video_m->count_by($base_where);
		$pagination = create_pagination('admin/videos/index', $total_rows);

		// Using this data, get the relevant results
		$videos = $this->video_m->limit($pagination['limit'])->get_many_by($base_where);

		//do we need to unset the layout because the request is ajax?
		$this->input->is_ajax_request() ? $this->template->set_layout(FALSE) : '';

		$this->template
			->title($this->module_details['name'])
			->set_partial('filters', 'admin/partials/filters')
			->append_metadata(js('admin/filter.js'))
			->set('pagination', $pagination)
			->set('videos', $videos)
			->build('admin/index', $this->data);
	}

	/**
	 * Create new post
	 * @access public
	 * @return void
	 */
	public function create()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules($this->validation_rules);

		if ($this->form_validation->run())
		{
			$input = array(
				'title'				=> $this->input->post('title'),
				'slug'				=> $this->input->post('slug'),
				'channel_id'		=> $this->input->post('channel_id'),
				'intro'				=> $this->input->post('intro'),
				'keywords'			=> Keywords::process($this->input->post('keywords')),
				'description'		=> $this->input->post('description'),
				'embed_code'		=> $this->input->post('embed_code'),
				'width'				=> $this->input->post('width'),
				'height'			=> $this->input->post('height'),
				'schedule_on'		=> $this->input->post('schedule_on') ? strtotime($this->input->post('schedule_on')) : now(),
				'created_on'		=> $this->input->post('created_on') ? strtotime($this->input->post('created_on')) : now(),
				'comments_enabled'	=> $this->input->post('comments_enabled'),
				'restricted_to' 	=> $this->input->post('restricted_to') ? json_encode($this->input->post('restricted_to')) : '',
			);

			if ( ! empty($_FILES['thumbnail']['name']))
			{
				$this->_prep_upload();

				if ( ! $this->upload->do_upload('thumbnail'))
				{
					$this->template->messages = array('error' => $this->upload->display_errors());
					
					goto display;
				}

				$thumbnail = $this->upload->data();

				list($width, $height)=explode('x', Settings::get('video_thumb_size'));

				$config['source_image']	= $thumbnail['full_path'];
				$config['maintain_ratio'] = TRUE;
				$config['width']	 = $width;
				$config['height']	= $height;

				$this->load->library('image_lib', $config); 
				
				if ( ! $this->image_lib->resize())
				{
					$this->template->messages = array('error' => $this->image_lib->display_errors());
					
					goto display;
				}

				$input['thumbnail'] = $thumbnail['file_name'];
			}

			if ($this->video_m->insert($input))
			{
				$this->pyrocache->delete_all('video_m');
				$this->session->set_flashdata('success', sprintf($this->lang->line('video:video_add_success'), $this->input->post('title')));
			}
			else
			{
				$this->session->set_flashdata('error', $this->lang->line('video:post_add_error'));
			}

			// Redirect back to the form or main page
			$this->input->post('btnAction') == 'save_exit' ? redirect('admin/videos') : redirect('admin/videos/edit/' . $id);
		}
		else
		{
			// Go through all the known fields and get the post values
			foreach ($this->validation_rules as $rule)
			{
				if ($rule['field'] === 'restricted_to[]')
				{
					$video->restricted_to = set_value($rule['field']);

					continue;
				}
				$video->$rule['field'] = set_value($rule['field']);
			}
		}

		display:

		$this->load->model('groups/group_m');
		$groups = $this->group_m->get_all();
		foreach($groups as $group)
		{
			$group->name !== 'admin' && $group_options[$group->id] = $group->name;
		}
		$this->template->group_options = $group_options;

		$this->template
			->title($this->module_details['name'], lang('video:create_title'))
			->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
			->append_metadata(js('video_form.js', 'videos'))
			->set('video', $video)
			->build('admin/form');
	}

	/**
	 * Edit video
	 * @access public
	 * @param int $id the ID of the video to edit
	 * @return void
	 */
	public function edit($id = 0)
	{
		$id or redirect('admin/videos');

		$this->load->library('form_validation');

		$this->form_validation->set_rules($this->validation_rules);

		$video = $this->video_m->get($id);
		
		
		$video->keywords = Keywords::get_string($video->keywords);

		// It's stored as a CSV list
		$video->restricted_to = json_decode($video->restricted_to);

		$this->id = $video->id;
		
		if ($this->form_validation->run())
		{  
			$author_id = empty($post->author) ? $this->user->id : $post->author_id;

			$input = array(
				'title'				=> $this->input->post('title'),
				'slug'				=> $this->input->post('slug'),
				'channel_id'		=> $this->input->post('channel_id'),
				'intro'				=> $this->input->post('intro'),
				'keywords'			=> Keywords::process($this->input->post('keywords')),
				'description'		=> $this->input->post('description'),
				'embed_code'		=> $this->input->post('embed_code'),
				'width'				=> $this->input->post('width'),
				'height'			=> $this->input->post('height'),
				'schedule_on'		=> $this->input->post('schedule_on') ? strtotime($this->input->post('schedule_on')) : now(),
				'created_on'		=> $this->input->post('created_on') ? strtotime($this->input->post('created_on')) : now(),
				'comments_enabled'	=> $this->input->post('comments_enabled'),
				'restricted_to' 	=> $this->input->post('restricted_to') ? json_encode($this->input->post('restricted_to')) : '',	
			);

			if ( ! empty($_FILES['thumbnail']['name']))
			{
				$this->_prep_upload();

				if ( ! $this->upload->do_upload('thumbnail'))
				{
					$this->template->messages = array('error' => $this->upload->display_errors());
					
					goto display;
				}

				$thumbnail = $this->upload->data();

				list($width, $height)=explode('x', Settings::get('video_thumb_size'));

				$config['source_image']	= $thumbnail['full_path'];
				$config['maintain_ratio'] = TRUE;
				$config['width']	 = $width;
				$config['height']	= $height;

				$this->load->library('image_lib', $config); 

				if ( ! $this->image_lib->resize())
				{
					$this->template->messages = array('error' => $this->image_lib->display_errors());
					
					goto display;
				}
				
				$input['thumbnail'] = $thumbnail['file_name'];
			}

			if ($this->video_m->update($id, $input))
			{
				$this->session->set_flashdata('success', sprintf(lang('video:edit_success'), $this->input->post('title')));
			}
			else
			{
				$this->session->set_flashdata('error', lang('video:edit_error'));
			}

			// Redirect back to the form or main page
			$this->input->post('btnAction') == 'save_exit' ? redirect('admin/videos') : redirect('admin/videos/edit/' . $id);
		}

		// Go through all the known fields and get the post values
		foreach ($this->validation_rules as $rule)
		{
			if ($rule['field'] === 'restricted_to[]')
			{
				$video->restricted_to = set_value($rule['field'], $video->restricted_to);
				continue;
			}

			if (isset($_POST[$rule['field']]))
			{
				$video->{$rule['field']} = $this->form_validation->{$rule['field']};
			}
		}
		$video->schedule_on = date('Y-m-d h:i:s', $video->schedule_on);

		display:

		$this->load->model('groups/group_m');
		$groups = $this->group_m->get_all();
		foreach($groups as $group)
		{
			$group->name !== 'admin' && $group_options[$group->id] = $group->name;
		}
		$this->template->group_options = $group_options;

		$this->template
			->title($this->module_details['name'], sprintf(lang('video:edit_title'), $video->title))
			->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
			->append_metadata(js('video_form.js', 'videos'))
			->set('video', $video)
			->build('admin/form');
	}

	private function _prep_upload()
	{
		$upload_path = UPLOAD_PATH.'videos/thumbs/';
		is_dir($upload_path) or mkdir($upload_path, 0777, true);

		$config['upload_path'] = $upload_path;
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		$config['encrypt_name']	= true;
		$config['max_width']  = '1200';
		$config['max_height']  = '800';

		$this->load->library('upload', $config);
	}


	/**
	 * Preview video
	 * @access public
	 * @param int $id the ID of the video to preview
	 * @return void
	 */
	public function preview($id = 0)
	{
		$video = $this->video_m->get($id);

		$this->template
			->set_layout('modal', 'admin')
			->set('video', $video)
			->build('admin/preview');
	}


	public function feature($id)
	{
		$where['id'] = $id;

		$video = $this->db->get_where('videos', $where)->row();

		$this->db
			->where($where)
			->update('videos', array('featured_on' => now()));

		$this->session->set_flashdata('success', 'The video "'.$video->title.'" is now a featured video.');

		redirect('admin/videos');
	}


	/**
	 * Helper method to determine what to do with selected items from form post
	 * @access public
	 * @return void
	 */
	public function action()
	{
		switch ($this->input->post('btnAction'))
		{
			case 'publish':
				role_or_die('videos', 'put_live');
				$this->publish();
				break;
			
			case 'delete':
				role_or_die('videos', 'delete_live');
				$this->delete();
				break;
			
			default:
				redirect('admin/videos');
				break;
		}
	}

	/**
	 * Delete video
	 * @access public
	 * @param int $id the ID of the video to delete
	 * @return void
	 */
	public function delete($id = 0)
	{
		// Delete one
		$ids = ($id) ? array($id) : $this->input->post('action_to');

		// Go through the array of slugs to delete
		if ( ! empty($ids))
		{
			$titles = array();
			foreach ($ids as $id)
			{
				// Get the current page so we can grab the id too
				if ($video = $this->video_m->get($id))
				{
					$this->video_m->delete($id);

					// Wipe cache for this model, the content has changed
					$this->pyrocache->delete('video_m');
					$titles[] = $video->title;
				}
			}
		}

		// Some pages have been deleted
		if ( ! empty($titles))
		{
			// Only deleting one page
			if (count($titles) == 1)
			{
				$this->session->set_flashdata('success', sprintf(lang('video:delete_success'), current($titles)));
			}
			// Deleting multiple pages
			else
			{
				$this->session->set_flashdata('success', sprintf(lang('video:mass_delete_success'), implode('", "', $titles)));
			}
		}
		// For some reason, none of them were deleted
		else
		{
			$this->session->set_flashdata('notice', lang('video:delete_error'));
		}

		redirect('admin/videos');
	}

	/**
	 * Callback method that checks the title of an post
	 * @access public
	 * @param string title The Title to check
	 * @return bool
	 */
	public function _check_title($title = '')
	{
		if ( ! $this->video_m->check_exists('title', $title, $this->id))
		{
			$this->form_validation->set_message('_check_title', sprintf(lang('video:already_exist_error'), lang('video:title_label')));
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Callback method that checks the slug of an post
	 * @access public
	 * @param string slug The Slug to check
	 * @return bool
	 */
	public function _check_slug($slug = '')
	{
		if ( ! $this->video_m->check_exists('slug', $slug, isset($this->id) ? $this->id : 0))
		{
			$this->form_validation->set_message('_check_slug', sprintf(lang('video:already_exist_error'), lang('video:slug_label')));
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * method to fetch filtered results for video list
	 * @access public
	 * @return void
	 */
	public function ajax_filter()
	{
		$channel = $this->input->post('f_channel');
		// $status = $this->input->post('f_status');

		$post_data = array();

/*
		if ($status == 'live')
		{
			$post_data['schedule_on >='] = now();
		}

		else
		{
			$post_data['schedule_on <'] = now();
		}
*/
		if ($channel != 0)
		{
			$post_data['channel_id'] = $channel;
		}

		$results = $this->video_m->search($post_data);

		//set the layout to false and load the view
		$this->template
				->set_layout(FALSE)
				->set('videos', $results)
				->build('admin/index');
	}
}

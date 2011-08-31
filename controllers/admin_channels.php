<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package  	PyroCMS
 * @subpackage  Categories
 * @channel  	Module
 * @author  	Phil Sturgeon - PyroCMS Dev Team
 */
class Admin_Channels extends Admin_Controller
{
	/**
	 * Array that contains the validation rules
	 * @access protected
	 * @var array
	 */
	protected $validation_rules = array(
		array(
			'field' => 'title',
			'label' => 'lang:video_channel:title_label',
			'rules' => 'trim|required|max_length[100]|callback__check_title'
		),
		array(
			'field' => 'description',
			'label' => 'lang:video:description_label',
			'rules' => 'trim|required'
		),
	);
	
	/** 
	 * The constructor
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('video_channel_m');
		$this->lang->load('channel');
		$this->lang->load('video');
		
	    $this->template->set_partial('shortcuts', 'admin/partials/shortcuts');
		
		// Load the validation library along with the rules
		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->validation_rules);
	}
	
	/**
	 * Index method, lists all channels
	 * @access public
	 * @return void
	 */
	public function index()
	{
		$this->pyrocache->delete_all('modules_m');
		// Create pagination links
		$total_rows = $this->video_channel_m->count_all();
		$pagination = create_pagination('admin/video/channels/index', $total_rows);
			
		// Using this data, get the relevant results
		$channels = $this->video_channel_m->order_by('title')->limit($pagination['limit'])->get_all();

		$this->template
			->title($this->module_details['name'], lang('video_channel:list_title'))
			->set('channels', $channels)
			->set('pagination', $pagination)
			->build('admin/channels/index', $this->data);
	}
	
	/**
	 * Create method, creates a new channel
	 * @access public
	 * @return void
	 */
	public function create()
	{
		// Validate the data
		if ($this->form_validation->run())
		{
			$result = $this->video_channel_m->insert(array(
				'title' => $this->input->post('title'),
				'description' => $this->input->post('description'),
			));
			
			$result
				? $this->session->set_flashdata('success', sprintf( lang('video_channel:add_success'), $this->input->post('title')) )
				: $this->session->set_flashdata(array('error'=> lang('video_channel:add_error')));

			redirect('admin/videos/channels');
		}
		
		// Loop through each validation rule
		foreach($this->validation_rules as $rule)
		{
			$channel->{$rule['field']} = set_value($rule['field']);
		}
			
		$this->template
			->title($this->module_details['name'], lang('video_channel:create_title'))
			->set('channel', $channel)
			->build('admin/channels/form', $this->data);	
	}
	
	/**
	 * Edit method, edits an existing channel
	 * @access public
	 * @param int id The ID of the channel to edit 
	 * @return void
	 */
	public function edit($id = 0)
	{	
		// Get the channel
		$channel = $this->video_channel_m->get($id);
		
		// ID specified?
		$channel or redirect('admin/video/channels/index');
		
		// Validate the results
		if ($this->form_validation->run())
		{		
			$this->video_channel_m->update($id, $_POST)
				? $this->session->set_flashdata('success', sprintf( lang('video_channel:edit_success'), $this->input->post('title')) )
				: $this->session->set_flashdata(array('error'=> lang('video_channel:edit_error')));
			
			redirect('admin/videos/channels');
		}
		
		// Loop through each rule
		foreach($this->validation_rules as $rule)
		{
			if($this->input->post($rule['field']) !== FALSE)
			{
				$channel->{$rule['field']} = $this->input->post($rule['field']);
			}
		}

		$this->template->title($this->module_details['name'], sprintf(lang('video_channel:edit_title'), $channel->title))
			->set('channel', $channel)
			->build('admin/channels/form', $this->data);
	}	

	/**
	 * Delete method, deletes an existing channel (obvious isn't it?)
	 * @access public
	 * @param int id The ID of the channel to edit 
	 * @return void
	 */
	public function delete($id = 0)
	{	
		$id_array = (!empty($id)) ? array($id) : $this->input->post('action_to');
		
		// Delete multiple
		if(!empty($id_array))
		{
			$deleted = 0;
			$to_delete = 0;
			foreach ($id_array as $id) 
			{
				if($this->video_channel_m->delete($id))
				{
					$deleted++;
				}
				else
				{
					$this->session->set_flashdata('error', sprintf($this->lang->line('video_channel:mass_delete_error'), $id));
				}
				$to_delete++;
			}
			
			if( $deleted > 0 )
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('video_channel:mass_delete_success'), $deleted, $to_delete));
			}
		}		
		else
		{
			$this->session->set_flashdata('error', $this->lang->line('video_channel:no_select_error'));
		}
		
		redirect('admin/videos/channels');
	}
		
	/**
	 * Create method, creates a new channel via ajax
	 * @access public
	 * @return void
	 */
	public function create_ajax()
	{
		// Loop through each validation rule
		foreach($this->validation_rules as $rule)
		{
			$channel->{$rule['field']} = set_value($rule['field']);
		}
		
		$this->data->method = 'create';
		$this->data->channel =& $channel;
		
		if ($this->form_validation->run())
		{
			$id = $this->video_channel_m->insert($_POST);
			
			if($id > 0)
			{
				$message = sprintf( lang('video_channel:add_success'), $this->input->post('title'));
			}
			else
			{
				$message = lang('video_channel:add_error');
			}

			return $this->template->build_json(array(
				'message'		=> $message,
				'title'			=> $this->input->post('title'),
				'channel_id'	=> $id,
			));
		}	
		else
		{
			// Render the view
			$form = $this->load->view('admin/channels/form', $this->data, TRUE);

			if ($errors = validation_errors())
			{
				return $this->template->build_json(array(
					'message'	=> $errors,
					'status'	=> 'error',
					'form'		=> $form
				));
			}

			echo $form;
		}
	}
}
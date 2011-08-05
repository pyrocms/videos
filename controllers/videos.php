<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Videos extends Public_Controller
{
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('video_m');
		$this->load->model('video_channel_m');
		$this->load->model('comments/comments_m');
		$this->load->helper('text');
		$this->lang->load('video');

		list($this->template->thumb_width, $this->template->thumb_height)=explode('x', Settings::get('video_thumb_size'));

	}

	// video/page/x also routes here
	public function index()
	{
		$where = array('schedule_on <=' => now());

		$pagination = create_pagination('videos/page', $this->video_m->where($where)->count_by(), NULL, 3);
		$videos = $this->video_m->limit($pagination['limit'])->where($where)->get_all();

		$this->template
			->title($this->module_details['name'])
			->set_breadcrumb( lang('video:video_title'))
			->build('index', array(
				'videos' => $videos,
				'pagination' => $pagination,
			));
	}

	// video/page/x also routes here
	public function search()
	{
		$query = $this->input->get_post('q');

		$pagination = create_pagination('videos/search', count($this->video_m->get_search($query)), NULL, 3);
		$videos = $this->video_m->limit($pagination['limit'])->get_search($query);

		$this->template
			->title($this->module_details['name'], 'Search', $query)
			->set_breadcrumb(lang('video:video_title'))
			->set_breadcrumb('Search')
			->build('index', array(
				'videos' => $videos,
				'pagination' => $pagination,
			));
	}

	public function channel($slug = '')
	{
		$slug OR redirect('videos');

		// Get channel data
		$channel = $this->video_channel_m->get_by('slug', $slug) OR show_404();

		// Count total video videos and work out how many pages exist
		$pagination = create_pagination('video/channel/'.$slug, $this->video_m->count_by(array(
			'channel' => $slug,
			'schedule_on <=' => now(),
		)), NULL, 4);

		// Get the current page of video videos
		$videos = $this->video_m->limit($pagination['limit'])->get_many_by(array(
			'channel'=> $slug,
			'schedule_on <=' => now(),
		));

		// Build the page
		$this->template->title($this->module_details['name'], $channel->title )
			->set_metadata('description', $channel->description)
			->set_metadata('keywords', $channel->title )
			->set_breadcrumb( lang('video:videos_title'), 'videos')
			->set_breadcrumb( $channel->title )
			->set('videos', $videos)
			->set('channel', $channel)
			->set('pagination', $pagination)
			->build('channel', $this->data );
	}
	
	// Public: View an video
	public function view($slug = '')
	{
		if ( ! $slug or ! $video = $this->video_m->get_by('slug', $slug))
		{
			redirect('videos');
		}

		if ($video->schedule_on > now() && ! $this->ion_auth->is_admin())
		{
			redirect('videos');
		}

		if ( ! $channel = $this->video_channel_m->get($video->channel_id))
		{
			redirect('videos');
		}
		
		$video->channel = $channel;

		$this->video_m->update_views($video->id);

		$this->template->title($video->title, lang('video_video_title'))
			->set_metadata('description', $video->description)
			->set_metadata('keywords', $video->tags)
			->set_breadcrumb(lang('video:videos_title'), 'videos')
			->set_breadcrumb($video->channel->title, 'video/channel/'.$video->channel->slug)
			->set_breadcrumb($video->title)
			->set('video', $video)
			->build('view', $this->data);
	}

}

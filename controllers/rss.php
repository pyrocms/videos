<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Rss extends Public_Controller
{
	function __construct()
	{
		parent::__construct();	
		$this->load->model('video_m');
		$this->lang->load('video');
		$this->load->helper('xml');
	}
	
	public function index()
	{
		$result = $this->video_m
			->limit($this->settings->item('rss_feed_items'))
			->order_by('created_on', 'DESC')
			->get_all();
		
		$this->_build_feed( $result );		
		$this->output->set_header('Content-Type: application/rss+xml');
		$this->load->view('rss', $this->data);
	}
	
	public function category( $slug = '')
	{ 
		$this->load->model('video_channel_m');
		
		if ( ! $category = $this->video_channel_m->get_by('slug', $slug))
		{
			redirect('videos/rss');
		}
		
		$posts = $this->video_channel_m
			->limit($this->settings->item('rss_feed_items'))
			->order_by('created_on', 'DESC')
			->get_many_by('category_id', $category->id);
		
		$this->_build_feed( $posts );		
		$this->data->rss->feed_name .= ' '. $category->title;		
		$this->output->set_header('Content-Type: application/rss+xml');
		$this->load->view('rss', $this->data);
	}
	
	private function _build_feed( $posts = array() )
	{
		$this->data->rss->encoding = $this->config->item('charset');
		$this->data->rss->feed_name = $this->settings->item('site_name');
		$this->data->rss->feed_url = site_url('videos');
		$this->data->rss->page_description = $this->settings->item('site_name');
		$this->data->rss->page_language = 'en-gb';
		$this->data->rss->creator_email = $this->settings->item('contact_email');
		
		if ( ! empty($posts))
		{        
			foreach ($posts as $row)
			{
				//$row->created_on = human_to_unix($row->created_on);
				$row->link = site_url('videos/view/'. $row->slug);
				$row->created_on = standard_date('DATE_RSS', $row->created_on);
				
				$item = array(
					//'author' => $row->author,
					'title' => xml_convert($row->title),
					'link' => $row->link,
					'guid' => $row->link,
					'description'  => $row->intro,
					'date' => $row->created_on
				);				
				$this->data->rss->items[] = (object) $item;
			} 
		}	
	}
}
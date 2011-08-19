<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author 		PyroCMS Dev Team
 * @package 	PyroCMS
 * @subpackage 	Modules
 * @category 	videos
 */
class Sitemap extends Public_Controller
{
	/**
	 * XML
	 * @access public
	 * @return void
	 */
	public function xml() 
	{
		$this->load->model('video_m');

		$doc = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" />');

		// Get all videos
		$videos = $this->video_m->get_many_by(array('schedule_on <=' => now()));
		
		// send em to XML!
		foreach ($videos as $video)
		{
			$node = $doc->addChild('url');
		
			$loc = site_url('videos/view/'.$video->slug);
			$node->addChild('loc', $loc);

			if ($video->updated_on)
			{
				$node->addChild('lastmod', date(DATE_W3C, $video->updated_on));
			}
		}

		$this->output
			->set_content_type('application/xml')
			->set_output($doc->asXML());
	}
}

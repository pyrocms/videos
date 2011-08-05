<?php defined('BASEPATH') or exit('No direct script access allowed');

class Module_Videos extends Module {

	public $version = '1.0';

	public function info()
	{
		return array(
			'name' => array(
				'en' => 'Videos',
			),
			'description' => array(
				'en' => 'Display videos in channels, based on content from various sources.',
			),
			'frontend'	=> TRUE,
			'backend'	=> TRUE,
			'skip_xss'	=> TRUE,
			'menu'		=> 'content',

			'roles' => array(
				'put_live', 'delete_live'
			),
		);
	}

	public function install()
	{
		$this->dbforge->drop_table('video_channels');
		$this->dbforge->drop_table('videos');
		
		$video_channels = "
			CREATE TABLE " . $this->db->dbprefix('video_channels') . " (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `slug` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `title` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `description` text COLLATE utf8_unicode_ci,
			  `thumbnail` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `slug - unique` (`slug`),
			  UNIQUE KEY `title - unique` (`title`),
			  KEY `slug - normal` (`slug`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Video Channels.';
		";

		$video = "
			CREATE TABLE " . $this->db->dbprefix('videos') . " (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `slug` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `channel_id` int(11) NOT NULL,
			  `thumbnail` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `intro` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `tags` text COLLATE utf8_unicode_ci NOT NULL,
			  `embed_code` text COLLATE utf8_unicode_ci NOT NULL,
			  `width` int(4) NOT NULL,
			  `height` int(4) NOT NULL,
			  `views` int(10) unsigned NOT NULL DEFAULT '0',
			  `featured_on` int(10) unsigned DEFAULT NULL,
			  `schedule_on` int(10) unsigned DEFAULT NULL,
			  `user_id` int(11) NOT NULL DEFAULT '0',
			  `created_on` int(11) NOT NULL,
			  `updated_on` int(11) NOT NULL DEFAULT '0',
			  `comments_enabled` int(1) NOT NULL DEFAULT '1',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `title` (`title`),
			  KEY `channel_id - normal` (`channel_id`),
			  FULLTEXT KEY `search` (`title`,`intro`,`tags`,`description`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Videos.';
		";

		$this->db->query("INSERT INTO ".$this->db->dbprefix('settings')." (
`slug` ,`title` ,`description` ,`type` ,`default` ,`value` ,`options` ,`is_required` ,`is_gui` ,`module` ,`order`)
VALUES (
	'video_thumb_size',  'Video Thumb Size',  'The width and height that video thumbnails will be resized to. E.g: 120x90.',  'text',  '120x90',  '',  '',  '1', '1',  'videos',  '0'
), (
	'video_display_size',  'Video Display Size',  'The width and height that videos will be displayed at on the website. E.g: 550x400.',  'text',  '550x400', '',  '',  '1',  '1',  'videos',  '0'
);");

		if ($this->db->query($video_channels) && $this->db->query($video))
		{
			return TRUE;
		}
	}

	public function uninstall()
	{		
		$this->dbforge->drop_table('video_channels');
		$this->dbforge->drop_table('videos');
		
		return true;
	}

	public function upgrade($old_version)
	{
		// Your Upgrade Logic
		return TRUE;
	}

	public function help()
	{
		/**
		 * Either return a string containing help info
		 * return "Some help info";
		 *
		 * Or add a language/help_lang.php file and
		 * return TRUE;
		 *
		 * help_lang.php contents
		 * $lang['help_body'] = "Some help info";
		*/
		return TRUE;
	}
}

/* End of file details.php */

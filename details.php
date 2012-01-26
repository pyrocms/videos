<?php defined('BASEPATH') or exit('No direct script access allowed');

class Module_Videos extends Module {

	public $version = '1.4.2';

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
			
			'sections' => array(
			    'videos' => array(
				    'name' => 'video:videos_title',
				    'uri' => 'admin/videos',
				    'shortcuts' => array(
						array(
					 	   'name' => 'video:create_title',
						    'uri' => 'admin/videos/create',
						),
					),
				),
				'channels' => array(
				    'name' => 'video_channel:list_title',
				    'uri' => 'admin/videos/channels',
				    'shortcuts' => array(
						array(
						    'name' => 'video_channel:create_title',
						    'uri' => 'admin/videos/channels/create',
						),
				    ),
			    ),
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
			  `slug` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
			  `title` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
			  `parent_id` int unsigned DEFAULT NULL,
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
			  `id` int unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
			  `slug` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
			  `channel_id` int(11) NOT NULL,
			  `thumbnail` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `episode` int unsigned DEFAULT 0,
			  `intro` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `keywords` char(32) COLLATE utf8_unicode_ci NOT NULL,
			  `embed_code` text COLLATE utf8_unicode_ci NOT NULL,
			  `width` int(4) NOT NULL,
			  `height` int(4) NOT NULL,
			  `views` int(10) unsigned NOT NULL DEFAULT '0',
			  `restricted_to` varchar(255) DEFAULT NULL,
			  `featured_on` int(10) unsigned DEFAULT NULL,
			  `schedule_on` int(10) unsigned DEFAULT NULL,
			  `user_id` int(11) NOT NULL DEFAULT '0',
			  `created_on` int(11) NOT NULL,
			  `updated_on` int(11) NOT NULL DEFAULT '0',
			  `comments_enabled` int(1) NOT NULL DEFAULT '1',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `title` (`title`),
			  KEY `channel_id - normal` (`channel_id`),
			  FULLTEXT KEY `search` (`title`,`intro`,`description`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Videos.';
		";

		$this->db->query("INSERT INTO ".$this->db->dbprefix('settings')." (
		`slug` ,`title` ,`description` ,`type` ,`default` ,`value` ,`options` ,`is_required` ,`is_gui` ,`module` ,`order`)
		VALUES (
		 	'video_thumb_size',  'Video Thumbnail Size',  'The width and height that video thumbnails will be resized to. E.g: 120x90.',  'text',  '120x90',  '',  '',  '1', '1',  'videos',  '2'
		), (
			'video_display_width',  'Video Display Width',  'The width that videos will be displayed at on the website. E.g: 550.',  'text',  '550', '',  '',  '1',  '1',  'videos',  '3'
		), (
			'video_thumb_enabled',  'Video Thumbnails Enabled?',  'Do you want thumbnails to be uploaded and displayed for videos and channels?',  'radio', '1', '1', '1=Enabled|0=Disabled',  '1',  '1',  'videos',  '1'
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
		
		$this->db
			->where_in('slug', array('video_thumb_size', 'video_display_width', 'video_thumb_enabled'))
			->delete('settings');
		
		return true;
	}

	public function upgrade($old_version)
	{
		switch ($old_version)
		{
			case '1.0':
				$this->db->query("REPLACE INTO ".$this->db->dbprefix('settings')." (
				`slug` ,`title` ,`description` ,`type` ,`default` ,`value` ,`options` ,`is_required` ,`is_gui` ,`module` ,`order`)
				VALUES (
					'video_thumb_enabled',  'Video Thumbnails',  'Do you want thumbnails to be uploaded and displayed for videos and channels?',  'radio', '1', '1', '1=Enabled|0=Disabled',  '1',  '1',  'videos',  '1'
				), (
					'video_display_width',  'Video Display Width',  'The width that videos will be displayed at on the website. E.g: 550.',  'text',  '550', '',  '',  '1',  '1',  'videos',  '3'
				);");
				
				$this->db->delete('settings', array('slug' => 'video_display_size'));
				
			break;
			
			case '1.1':
			
				$this->load->dbforge();
				
				$this->dbforge->drop_column('videos', 'tags');
				
				$this->dbforge->add_column('videos', array(
					'keywords' => array(
						'type' => 'char',
						'constraint' => 32,
						'null' => false,
					),
				));

			break;
				
			case '1.2.0':
			case '1.2.1':
			
				$this->dbforge->add_column('videos', array(
					'episode' => array(
						'type' => 'int',
						'constraint' => 5,
						'null' => false,
						'default' => 1,
					),
				));

			break;
				
			case '1.3.0':

				$this->dbforge->add_column('video_channels', array(
					'parent_id' => array(
						'type' => 'int',
						'constraint' => 11,
						'null' => false,
						'default' => 0,
					),
				));
				
			break;
			
			case '1.3.1':
			
				$this->db->query('ALTER TABLE '.$this->db->dbprefix('video_channels').' 
				DROP INDEX `slug - unique` ,
				ADD UNIQUE `slug - unique` ( `slug` , `parent_id` ),
				DROP INDEX `title - unique` ,
				ADD UNIQUE `title - unique` ( `title` , `parent_id` )');
				
				$this->db->query('ALTER TABLE '.$this->db->dbprefix('video_channels').'
				CHANGE `title` `title` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
				CHANGE `slug` `slug` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL');
				
			break;

		}
		
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
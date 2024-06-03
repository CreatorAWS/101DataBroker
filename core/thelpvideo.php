<?php

	class THelpVideo
		{
			var $info;

			function __construct($id_or_cahcedinfo)
				{
					global $sm;
					if (is_array($id_or_cahcedinfo))
						{
							$this->info=$id_or_cahcedinfo;
						}
					else
						{
							$this->info=TQuery::ForTable('help_videos')->Add('id', intval($id_or_cahcedinfo))->Get();
						}
				}
			public static function UsingCache($id)
				{
					global $sm;
					if (!is_array($sm['cache']['THelpVideo'][$id]))
						{
							$object = new THelpVideo($id);
							if ($object->Exists())
								$sm['cache']['THelpVideo'][$id] = $object->GetRawData();
						}
					else
						$object = new THelpVideo($sm['cache']['THelpVideo'][$id]);
					return $object;
				}

			public static function withID($id)
				{
					$asset=new THelpVideo($id);
					return $asset;
				}
			function Exists()
				{
					return !empty($this->info['id']);
				}
			function GetRawData()
				{
					return $this->info;
				}
			function Title()
				{
					return $this->info['title'];
				}
			function SetTitle($val)
				{
					$this->UpdateValues(Array('title' => $val));
				}
			function Comment()
				{
					return $this->info['comment'];
				}
			function HasComment()
				{
					return !empty($this->info['comment']);
				}
			function SetComment($val)
				{
					$this->UpdateValues(Array('comment' => $val));
				}
			function SetOrder($val)
				{
					$this->UpdateValues(Array('order' => $val));
				}
			function ID()
				{
					return intval($this->info['id']);
				}
			function UpdateValues($params)
				{
					global $sm;
					if (empty($params) || !is_array($params))
						return;
					$q=new TQuery('help_videos');
					foreach ($params as $key=>$val)
						{
							$this->info[$key]=$val;
							$q->Add($key, dbescape($this->info[$key]));
						}
					$q->Update('id', $this->ID());
				}

			function VideoPath()
				{
					use_api('path');
					return Path::DealersRoot().'files/video/help_video_'.intval($this->ID()).'.mp4';
				}
			function VideoURL()
				{
					use_api('path');
					return 'https://'.image_domain().'/files/video/help_video_'.intval($this->ID()).'.mp4';
				}

			public static function Create()
				{
					$q=new TQuery('help_videos');
					$id = $q->Insert();
					$video = new THelpVideo($id);
					return $video;
				}
		}

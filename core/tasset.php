<?php

	class TAsset
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
							$this->info=TQuery::ForTable('company_assets')->Add('id', intval($id_or_cahcedinfo))->Get();
						}
				}

			public static function UsingCache($id)
				{
					global $sm;
					if (!is_array($sm['cache']['TAsset'][$id]))
						{
							$object = new TAsset($id);
							if ($object->Exists())
								$sm['cache']['TAsset'][$id] = $object->GetRawData();
						}
					else
						$object = new TAsset($sm['cache']['TAsset'][$id]);
					return $object;
				}

			function GetRawData()
				{
					return $this->info;
				}

			public static function withID($id)
				{
					$asset=new TAsset($id);
					return $asset;
				}
			function Exists()
				{
					return !empty($this->info['id']);
				}
			function FileName()
				{
					return $this->info['filename'];
				}
			function Comment()
				{
					return $this->info['comment'];
				}

			function Access()
				{
					return $this->info['access'];
				}

			function HasComment()
				{
					return !empty($this->info['comment']);
				}
			function FileType()
				{
					return $this->info['type'];
				}
			function ID()
				{
					return intval($this->info['id']);
				}
			function CompanyID()
				{
					return intval($this->info['id_company']);
				}
			function UpdateValues($params)
				{
					global $sm;
					if (empty($params) || !is_array($params))
						return;
					$q=new TQuery('company_assets');
					foreach ($params as $key=>$val)
						{
							$this->info[$key]=$val;
							$q->Add($key, dbescape($this->info[$key]));
						}
					$q->Update('id', $this->ID());
				}
			function FilePath()
				{
					use_api('path');
					return Path::DealersRoot().'files/download/ca_'.intval($this->ID());
				}
			function ImagePath()
				{
					use_api('path');
					return 'https://'.image_domain().'/files/download/ca_'.intval($this->ID());
				}
			function ImagePathTwilio()
				{
					use_api('path');
					return 'https://'.main_domain().'/flo'.$this->ID();
				}
			function ThumbPath()
				{
					use_api('path');
					return Path::DealersRoot().'files/img/caprv'.intval($this->ID()).'.jpg';
				}
			function ThumbExists()
				{
					return file_exists($this->ThumbPath());
				}
			function ThumbURL()
				{
					use_api('path');
					return 'https://'.image_domain().'/files/img/caprv'.intval($this->ID()).'.jpg';
				}
			function DownloadURL()
				{
					return 'index.php?m=companyassets&d=twiliomms&id='.intval($this->ID());
				}
			function isImage()
				{
					$supported_types=Array(
						'image/jpeg',
						'image/gif',
						'image/png'
					);
					return in_array($this->FileType(), $supported_types);
				}
			function isVideo()
				{
					$supported_types=Array(
						'video/mpeg',
						'video/mp4',
						'video/quicktime'
					);
					return in_array($this->FileType(), $supported_types);
				}
			function isEligibleForMMS()
				{
					$supported_types=Array(
						'image/jpeg',
						'image/gif',
						'image/png',
						'audio/mp4',
						'audio/mp3',
						'audio/mpeg',
						'audio/ogg',
						'audio/vorbis',
						'audio/ac3',
						'audio/vnd.wave',
						'video/mpeg',
						'video/mp4',
						'video/quicktime'
					);
					if (!in_array($this->FileType(), $supported_types))
						return false;
					if (!file_exists($this->FilePath()))
						return false;
					if (filesize($this->FilePath())>500000)
						return false;
					return true;
				}

			function isAudio()
				{
					$supported_types=Array(
						'audio/mpeg',
						'audio/mp3'
					);
					return in_array($this->FileType(), $supported_types);
				}

			function isEligibleForVoiceMessages()
				{
					return $this->isAudio();
				}

			function FileNameWithComment()
				{
					$r=$this->FileName();
					if ($this->HasComment())
						$r.=' - '.$this->Comment();
					return $r;
				}
		}

?>
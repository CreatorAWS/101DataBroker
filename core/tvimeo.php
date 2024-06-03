<?php

	if (!defined("tvimeo_DEFINED"))
		{

			class TVimeo
				{
					protected $id;
					protected $data=NULL;

					function __construct($id_or_data)
						{
							if (is_array($id_or_data))
								{
									$this->data = $id_or_data;
									$this->id = $id_or_data['id']; 
								}
							else
								$this->id = $id_or_data;
						}
					
					function ID()
						{
							return $this->id;
						}
					
					function Valid()
						{
							return !(empty($this->id)) && is_numeric($this->id);
						}
					
					function LoadData($rewritecahce=false)
						{
							if ($rewritecahce || $this->data===NULL)
								{
									sm_extcore();
									$json=sm_url_content('https://vimeo.com/api/v2/video/'.$this->ID().'.json');
									$this->data=@json_decode($json, true);
									$this->data=$this->data[0];
								}
							return !empty($this->data);
						}

					public static function withURL($url)
						{
							preg_match('|^http.*?://.*?vimeo.com/([^/]+)$|is', $url, $matches);
							$vimeo=new TVimeo($matches[1]);
							return $vimeo;
						}

					function Title()
						{
							$this->LoadData();
							return $this->data['title'];
						}
					
					function Description()
						{
							$this->LoadData();
							return $this->data['description'];
						}
					
					function URL()
						{
							$this->LoadData();
							return $this->data['url'];
						}
					
					function UploadDateStr()
						{
							$this->LoadData();
							return $this->data['upload_date'];
						}
					
					function ThumbSmall()
						{
							$this->LoadData();
							return $this->data['thumbnail_small'];
						}
					
					function ThumbMedium()
						{
							$this->LoadData();
							return $this->data['thumbnail_medium'];
						}
					
					function ThumbLarge()
						{
							$this->LoadData();
							return $this->data['thumbnail_large'];
						}
					
					function VimeoUserID()
						{
							$this->LoadData();
							return $this->data['user_id'];
						}
					
					function VimeoUserName()
						{
							$this->LoadData();
							return $this->data['user_name'];
						}
					
					function VimeoUserURL()
						{
							$this->LoadData();
							return $this->data['user_url'];
						}
					
					function VimeoUserPortraitSmall()
						{
							$this->LoadData();
							return $this->data['user_portrait_small'];
						}
					
					function VimeoUserPortraitMedium()
						{
							$this->LoadData();
							return $this->data['user_portrait_medium'];
						}
					
					function VimeoUserPortraitLarge()
						{
							$this->LoadData();
							return $this->data['user_portrait_large'];
						}
					
					function VimeoUserPortraitHuge()
						{
							$this->LoadData();
							return $this->data['user_portrait_huge'];
						}
					
					function LikesCount()
						{
							$this->LoadData();
							return $this->data['stats_number_of_likes'];
						}
					
					function PlaysCount()
						{
							$this->LoadData();
							return $this->data['stats_number_of_plays'];
						}
					
					function CommentsCount()
						{
							$this->LoadData();
							return $this->data['stats_number_of_comments'];
						}
					
					function DurationSec()
						{
							$this->LoadData();
							return $this->data['duration'];
						}
					
					function Width()
						{
							$this->LoadData();
							return $this->data['width'];
						}
					
					function Height()
						{
							$this->LoadData();
							return $this->data['height'];
						}
					
					function Tags()
						{
							$this->LoadData();
							return $this->data['tags'];
						}
					
					function EmbedPrivacyKeyword()
						{
							$this->LoadData();
							return $this->data['embed_privacy'];
						}
					
					function ExportData()
						{
							$this->LoadData();
							return $this->data;
						}
					
					function GetEmbedCode($width=NULL, $height=NULL)
						{
							if ($width===NULL)
								$width=$this->Width();
							if ($height===NULL)
								$height=$this->Height();
							return '<iframe src="https://player.vimeo.com/video/'.$this->ID().'" width="'.$width.'" height="'.$height.'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
						}
				}

			define("tvimeo_DEFINED", 1);
		}
?>
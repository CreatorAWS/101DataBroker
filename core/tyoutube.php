<?php

	if (!defined("TYouTube_DEFINED"))
		{

			class TYouTube
				{
					protected $id;
					protected $data=NULL;

					function __construct($youtube_id)
						{
							$this->id = $youtube_id;
						}
					
					function ID()
						{
							return $this->id;
						}
					
					function ValidID()
						{
							return !empty($this->id);
						}

					public static function withURL($url)
						{
							$pattern = '/https*:\/\/www\.youtube\.com\/watch\?(.*)v=([a-zA-Z0-9_\-]+)(\S*)/i';
							preg_match($pattern, $url, $matches);
							$yt=new TYouTube($matches[2]);
							return $yt;
						}

					function URL()
						{
							return 'https://www.youtube.com/watch?v='.$this->ID();
						}
					
					function ThumbSmall()
						{
							return 'https://i.ytimg.com/vi/'.$this->ID().'/default.jpg';
						}

					function ThumbMedium()
						{
							return 'https://i.ytimg.com/vi/'.$this->ID().'/mqdefault.jpg';
						}

					function ThumbLarge()
						{
							return 'https://i.ytimg.com/vi/'.$this->ID().'/hqdefault.jpg';
						}

					function GetEmbedCode($width=NULL, $height=NULL)
						{
							if ($width===NULL)
								$width=$this->Width();
							if ($height===NULL)
								$height=$this->Height();
							return '<iframe class="yt-iframe" width="'.$width.'" height="'.$height.'" src="//www.youtube.com/embed/'.$this->ID().'" frameborder="0" allowfullscreen></iframe>';
						}
				}

			define("TYouTube_DEFINED", 1);
		}
?>
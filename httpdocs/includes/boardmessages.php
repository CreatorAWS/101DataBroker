<?php

//------------------------------------------------------------------------------
//|                                                                            |
//|            Content Management System SiMan CMS                             |
//|                                                                            |
//------------------------------------------------------------------------------

if (!defined("boardmessages_DEFINED"))
	{
		sm_add_cssfile('common_boardmessages.css');
		
		class TBoardMessages
			{
				var $boardmessages;
				var $rownumber;
				var $shownumber;
				function TBoardMessages($initialnumber=0)
					{
						$this->rownumber=-1;
						$this->shownumber=$initialnumber;
						$this->boardmessages['postfix']=mt_rand(1000, 9999);
						$this->boardmessages['type']='simple';
					}
				function SetForumStyle()
					{
						$this->boardmessages['type']='forum';
					}
				function Message($message)
					{
						$this->boardmessages['rows'][$this->rownumber]['message']=$message;
					}
				function CopyInfoOf($rownumber)
					{
						$this->boardmessages['rows'][$this->rownumber]['signature']=$this->boardmessages['rows'][$rownumber]['signature'];
						$this->boardmessages['rows'][$this->rownumber]['sender']=$this->boardmessages['rows'][$rownumber]['sender'];
					}
				function Signature($signature)
					{
						$this->boardmessages['rows'][$this->rownumber]['signature']=$signature;
					}
				function Title($title)
					{
						$this->boardmessages['rows'][$this->rownumber]['title']=$title;
					}
				function Sender($name, $url='')
					{
						$this->boardmessages['rows'][$this->rownumber]['sender']['name']=$name;
						$this->boardmessages['rows'][$this->rownumber]['sender']['url']=$url;
					}
				function Status($status, $statusimage='')
					{
						$this->boardmessages['rows'][$this->rownumber]['sender']['status']=$status;
						$this->boardmessages['rows'][$this->rownumber]['sender']['statusimage']=$statusimage;
					}
				function AddInfo($caption, $name, $url='')
					{
						$index=count($this->boardmessages['rows'][$this->rownumber]['info']);
						$this->boardmessages['rows'][$this->rownumber]['info'][$index]['caption']=$caption;
						$this->boardmessages['rows'][$this->rownumber]['info'][$index]['name']=$name;
						$this->boardmessages['rows'][$this->rownumber]['info'][$index]['url']=$url;
						$this->boardmessages['rows'][$this->rownumber]['infofields']=$index+1;
					}
				function Avatar($image)
					{
						if (!empty($image))
							{
								if (file_exists('files/img/'.$image))
									$image='files/img/'.$image;
								$this->boardmessages['rows'][$this->rownumber]['sender']['avatar']=$image;
							}
					}
				function TimeStamp($timestamp, $strftimeformat='')
					{
						global $lang;
						if (empty($strftimeformat))
							$strftimeformat=$lang["datetimemask"];
						$this->boardmessages['rows'][$this->rownumber]['time']=strftime($strftimeformat, $timestamp);
					}
				function TimeStr($customformattedtime)
					{
						$this->boardmessages['rows'][$this->rownumber]['time']=$customformattedtime;
					}
				function NewRow($message='', $title='')
					{
						$this->rownumber++;
						$this->shownumber++;
						if (!empty($message))
							$this->AddMessage($message);
						if (!empty($title))
							$this->boardmessages['rows'][$this->rownumber]['title']=$title;
						$this->boardmessages['rows'][$this->rownumber]['showmumber']=$this->shownumber;
					}
				function AddButton($title, $url, $image='')
					{
						global $_settings;
						$btnsindex=count($this->boardmessages['rows'][$this->rownumber]['buttons']);
						$this->boardmessages['rows'][$this->rownumber]['buttons'][$btnsindex]['title']=$title;
						$this->boardmessages['rows'][$this->rownumber]['buttons'][$btnsindex]['url']=$url;
						if (!empty($image))
							{
								if (file_exists('themes/'.$_settings['default_theme'].'/images/boardmessages/'.$image))
									$image='themes/'.$_settings['default_theme'].'/images/boardmessages/'.$image;
								elseif (file_exists('themes/default/images/boardmessages/'.$image))
									$image='themes/default/images/boardmessages/'.$image;
								$this->boardmessages['rows'][$this->rownumber]['buttons'][$btnsindex]['image']=$image;
							}
						$this->boardmessages['rows'][$this->rownumber]['buttonscount']=$btnsindex+1;
					}
				function AddMessageButton($message, $title, $url, $image='')
					{
						global $_settings;
						$btnsindex=count($this->boardmessages['rows'][$this->rownumber]['buttons']);
						$this->AddButton($title, $url, $image);
						$this->boardmessages['rows'][$this->rownumber]['buttons'][$btnsindex]['message']=$message;
						$this->boardmessages['rows'][$this->rownumber]['buttons'][$btnsindex]['messagebox']=1;
					}
				function Output()
					{
						$this->boardmessages['rowcount']=count($this->boardmessages['rows']);
						return $this->boardmessages;
					}
				function AddStyle($style)
					{
						$this->boardmessages['rows'][$this->rownumber]['customstyle'].=$style;
					}
			}

		define("boardmessages_DEFINED", 1);
	}

?>
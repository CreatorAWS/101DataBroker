<?php

	//------------------------------------------------------------------------------
	//|                                                                            |
	//|            Content Management System SiMan CMS                             |
	//|                                                                            |
	//------------------------------------------------------------------------------

	if (!defined("admindashboard_DEFINED"))
		{
			sm_add_cssfile('common_admindashboard.css');

			class TDashBoard
				{
					var $board;
					private $currentitem;

					function TDashBoard()
						{
						}

					function AddItem($title, $url, $image='', $name='')
						{
							if (empty($name))
								$name = md5(rand(1, 999999));
							$this->SetActiveItem($name);
							$this->SetTitle($title);
							$this->SetURL($url);
							$this->SetImage($image);
							return $this;
						}
					
					function SetActiveItem($name)
						{
							$this->currentitem=&$this->board['items'][$name];
							$this->currentitem['name']=$name;
							return $this;
						}

					function Count()
						{
							return count($this->board['items']);
						}

					function SetURL($url, $name='')
						{
							if (!empty($name))
								$this->SetActiveItem($name);
							$this->currentitem['url']=$url;
							return $this;
						}
					
					function SetBadge($text, $type='info', $name='')
						{
							if (!empty($name))
								$this->SetActiveItem($name);
							$this->currentitem['badge']['text']=$text;
							$this->currentitem['badge']['attrs']['class']='adash-badge adash-badge-'.$type;
							return $this;
						}
					
					function SetBadgeInfo($text, $name='')
						{
							$this->SetBadge($text, 'info');
							return $this;
						}
					
					function SetBadgeAlert($text, $name='')
						{
							$this->SetBadge($text, 'alert');
							return $this;
						}
					
					function SetBadgeWarning($text, $name='')
						{
							$this->SetBadge($text, 'warning');
							return $this;
						}
					
					function SetBadgeSuccess($text, $name='')
						{
							$this->SetBadge($text, 'success');
							return $this;
						}
					
					function SetTitle($title, $name='')
						{
							if (!empty($name))
								$this->SetActiveItem($name);
							$this->currentitem['title']=$title;
							return $this;
						}
					
					function SetImage($image, $name='')
						{
							if (!empty($name))
								$this->SetActiveItem($name);
							if (strlen($image)>0 && strpos($image, '://')===false && strpos($image, '.')===false)
								$image.='.png';
							if (!empty($image) && strpos($image, '/')===false)
								{
									if (!file_exists('themes/'.sm_current_theme().'/images/admindashboard/'.$image))
										$image='themes/default/images/admindashboard/'.$image;
									else
										$image='themes/'.sm_current_theme().'/images/admindashboard/'.$image;
								}
							$this->currentitem['image']=$image;
							return $this;
						}
					
					function AddClassname($classname, $name='')
						{
							if (!empty($name))
								$this->SetActiveItem($name);
							$this->currentitem['class'].=(empty($this->currentitem['class'])?'':' ').$classname;
							return $this;
						}
					
					function AddClassnameGlobal($classname)
						{
							$this->board['class'].=(empty($this->board['class'])?'':' ').$classname;
							return $this;
						}
					
					function SetStyle($style, $name='')
						{
							if (!empty($name))
								$this->SetActiveItem($name);
							$this->currentitem['style']=$style;
							return $this;
						}
					
					function SetStyleGlobal($style)
						{
							$this->board['style']=$style;
							return $this;
						}
					
					function Output()
						{
							if (is_array($this->board['items']))
								{
									foreach ($this->board['items'] as $itemname=>&$itemparams)
										{
											$itemparams['class'].=(empty($itemparams['class'])?'':' ').'adash-element';
											if (!empty($itemparams['url']))
												{
													$itemparams['htmltitle']='<a href="'.$itemparams['url'].'">'.$itemparams['title'].'</a>';
													$itemparams['htmlimagestart']='<a href="'.$itemparams['url'].'">';
													$itemparams['htmlimageend']='</a>';
												}
											else
												$itemparams['htmltitle']=$itemparams['title'];
											$itemparams['attrs']['style']=$itemparams['style'];
											$itemparams['attrs']['onclick']=$itemparams['onclick'];
											$itemparams['attrs']['class']=$itemparams['class'];
											if (!empty($itemparams['badge']['text']))
												{
													$itemparams['htmltop'].='<span class="'.$itemparams['badge']['attrs']['class'].'">'.$itemparams['badge']['text'].'</span>';
												}
										}
								}
							$this->board['count']=count($this->board['items']);
							return $this->board;
						}
				}

			define("admindashboard_DEFINED", 1);
		}

?>
<?php

	//------------------------------------------------------------------------------
	//|                                                                            |
	//|            Content Management System SiMan CMS                             |
	//|                                                                            |
	//------------------------------------------------------------------------------

	if (!defined("adminnavigation_DEFINED"))
		{
			sm_add_cssfile('common_adminnavigation.css');

			class TNavigation
				{
					var $board;
					private $currentitem;

					function TNavigation()
						{
							global $sm;
							if (!empty($sm['adminnavigation']['globalclass']))
								$this->AddClassnameGlobal($sm['adminnavigation']['globalclass']);
						}

					function AddItem($title, $url, $name='')
						{
							if (empty($name))
								$name = md5(rand(1, 999999));
							$this->SetActiveItem($name);
							$this->SetTitle($title);
							$this->SetURL($url);
							$this->currentitem['level']=1;
							return $this;
						}
					
					function SetActiveItem($name)
						{
							$this->currentitem=&$this->nav['items'][$name];
							$this->currentitem['name']=$name;
							return $this;
						}
					
					function SetURL($url, $name='')
						{
							if (!empty($name))
								$this->SetActiveItem($name);
							$this->currentitem['attrs']['href']=$url;
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
									if (!file_exists('themes/'.sm_current_theme().'/images/adminnavigation/'.$image))
										$image='themes/default/images/adminnavigation/'.$image;
									else
										$image='themes/'.sm_current_theme().'/images/adminnavigation/'.$image;
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
							$this->nav['class'].=(empty($this->nav['class'])?'':' ').$classname;
							return $this;
						}
					
					function SetStyle($style, $name='')
						{
							if (!empty($name))
								$this->SetActiveItem($name);
							$this->currentitem['style']=$style;
							return $this;
						}
					
					function SetActive($name='')
						{
							if (!empty($name))
								$this->SetActiveItem($name);
							$this->currentitem['active']=true;
							return $this;
						}
					
					function SetAutodetectionPartialMode($name='')
						{
							if (!empty($name))
								$this->SetActiveItem($name);
							$this->currentitem['active_on_partial']=true;
							return $this;
						}
					
					function OpenInNewWindow($name='')
						{
							if (!empty($name))
								$this->SetActiveItem($name);
							$this->currentitem['target']='_blank';
							return $this;
						}
					
					function SetStyleGlobal($style)
						{
							$this->nav['style']=$style;
							return $this;
						}
					
					function AutoDetectActive()
						{
							global $sm;
							if (is_array($this->nav['items']))
								{
									$tmp_index=strpos($sm['_s']['resource_url'], '/');
									$main_suburl=substr($sm['_s']['resource_url'], $tmp_index);
									foreach ($this->nav['items'] as $itemname=>&$itemparams)
										{
											if (
												(strcmp($main_suburl.$itemparams['attrs']['href'], $sm['server']['REQUEST_URI'])==0
												||
												strcmp($main_suburl.$itemparams['attrs']['href'], $sm['server']['REQUEST_URI'].'index.php')==0)
												|| ($sm['s']['is_index_page'] == 1 && strcmp($itemparams['attrs']['href'], $sm['s']['page']['scheme'].'://'.$sm['_s']['resource_url'])==0)
											)
												$this->SetActive($itemname);
											if (!$itemparams['active'] && $itemparams['active_on_partial'])
												{
													if (strpos($sm['server']['REQUEST_URI'], $main_suburl.$itemparams['attrs']['href'])===0)
														$this->SetActive($itemname);
												}
										}
								}
							return $this;
						}
					
					function Output()
						{
							if (is_array($this->nav['items']))
								{
									foreach ($this->nav['items'] as $itemname=>&$itemparams)
										{
											$itemparams['class'].=(empty($itemparams['class'])?'':' ').'anav-a';
											$itemparams['html']=$itemparams['title'];
											$itemparams['attrs']['style']=$itemparams['style'];
											$itemparams['attrs']['onclick']=$itemparams['onclick'];
											$itemparams['attrs']['class']=$itemparams['class'];
										}
									$items=Array();
									foreach ($this->nav['items'] as $itemname=>&$itemparams)
										{
											$items[]=$itemparams;
										}
									$this->nav['items']=$items;
								}
							$this->nav['count']=count($this->nav['items']);
							return $this->nav;
						}
				}

			define("adminnavigation_DEFINED", 1);
		}

?>
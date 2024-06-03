<?php

//------------------------------------------------------------------------------
//|                                                                            |
//|            Content Management System SiMan CMS                             |
//|                                                                            |
//------------------------------------------------------------------------------

if (!defined("adminbuttons_DEFINED"))
	{
		sm_add_cssfile('common_adminbuttons.css');
		sm_add_jsfile('common_adminbuttons.js');
		
		class TButtons
			{
				var $bar;
				private $currentbuttonname;
				function TButtons($buttonbar_title='')
					{
						$this->bar['buttonbar_title']=$buttonbar_title;
					}
				function Button($title, $url='')
					{
						$this->AddButton('', $title, $url, 'button');
						return $this;
					}
				function AddButton($name, $title, $url='', $type='button', $style='', $messagebox_message='', $javascript='')
					{
						if (empty($name))
							$name=md5(rand(1, 999999));
						$this->currentbuttonname=$name;
						$this->bar['buttons'][$name]['name']=$name;
						$this->bar['buttons'][$name]['caption']=$title;
						$this->bar['buttons'][$name]['url']=$url;
						$this->bar['buttons'][$name]['type']=$type;
						$this->bar['buttons'][$name]['javascript']=$javascript;
						$this->bar['buttons'][$name]['style']=$style;
						$this->bar['buttons'][$name]['message']=addslashes($messagebox_message);
						$this->SetAttr('type', 'button');
						return $this;
					}
				function AddSeparator($name, $title=' | ', $style='')
					{
						if (empty($name))
							$name=md5(rand(1, 999999));
						$this->currentbuttonname=$name;
						$this->bar['buttons'][$name]['name']=$name;
						$this->bar['buttons'][$name]['caption']=$title;
						$this->bar['buttons'][$name]['style']=$style;
						$this->bar['buttons'][$name]['type']='separator';
						return $this;
					}
				function AddToggle($name, $title, $toggle_id_focus, $style='')
					{
						if (!is_array($toggle_id_focus))
							$toggle_id_focus=Array($toggle_id_focus);
						$javascript="tmp=document.getElementById('".$toggle_id_focus[0]."');tmp.style.display=(tmp.style.display=='none')?'':'none';";
						if (!empty($toggle_id_focus[1]))
							$javascript.="tmp1=document.getElementById('".$toggle_id_focus[1]."');if(tmp.style.display=='')tmp1.focus();";
						$this->AddButton($name, $title, '', 'button', $style, '', $javascript);
						return $this;
					}
				function MessageBox($title, $url, $messagebox_message=NULL)
					{
						$this->AddMessageBox('', $title, $url, $messagebox_message);
						return $this;
					}
				function AddMessageBox($name, $title, $url, $messagebox_message=NULL, $style='')
					{
						global $lang;
						if ($messagebox_message===NULL)
							$messagebox_message=$lang['common']['are_you_sure'].(substr($lang['common']['are_you_sure'], -1)=='?'?'':'?');
						$this->AddButton($name, $title, $url, 'messagebox', $style, $messagebox_message);
						return $this;
					}
				function Bold($buttonname=NULL)
					{
						if ($buttonname==NULL)
							$buttonname=$this->currentbuttonname;
						$this->bar['buttons'][$buttonname]['bold']=true;
						return $this;
					}
				function Width($buttonname, $width)
					{
						if ($buttonname==NULL)
							$buttonname=$this->currentbuttonname;
						$this->bar['buttons'][$buttonname]['width']=$width;
						return $this;
					}
				function SetWidth($width)
					{
						$this->Width(NULL, $width);
						return $this;
					}
				function Height($buttonname, $height)
					{
						if ($buttonname==NULL)
							$buttonname=$this->currentbuttonname;
						$this->bar['buttons'][$buttonname]['height']=$height;
						return $this;
					}
				function SetHeight($height)
					{
						$this->Height(NULL, $height);
						return $this;
					}
				function Style($name, $style)
					{
						$this->bar['buttons'][$name]['style']=$style;
						return $this;
					}
				function AssignImage($name, $imagename)
					{
						$this->bar['buttons'][$name]['image']=$imagename;
						return $this;
					}
				function AddClassnameGlobal($classname)
					{
						$this->bar['class']=(empty($this->bar['class'])?'':' ').$classname;
						return $this;
					}
				function AddClassname($classname, $buttonname=NULL)
					{
						if ($buttonname==NULL)
							$buttonname=$this->currentbuttonname;
						$this->bar['buttons'][$buttonname]['class']=(empty($this->bar['buttons'][$buttonname]['class'])?'':' ').$classname;
						return $this;
					}
				function SetAttr($attrname, $attrvalue, $buttonname=NULL)
					{
						if ($buttonname==NULL)
							$buttonname=$this->currentbuttonname;
						$this->bar['buttons'][$buttonname]['attrs'][$attrname]=$attrvalue;
						return $this;
					}
				function ApplyClassnameForAll($classname)
					{
						if (is_array($this->bar['buttons']))
							{
								foreach ($this->bar['buttons'] as $buttonname=>&$buttonparams)
									{
										$this->AddClassname($classname, $buttonname);
									}
							}
						return $this;
					}
				function Count()
					{
						return count($this->bar['buttons']);
					}
				function OnClick($javascript, $buttonname=NULL)
					{
						if ($buttonname==NULL)
							$buttonname=$this->currentbuttonname;
						$this->bar['buttons'][$buttonname]['javascript']=$javascript;
						return $this;
					}
				function SetStyleGlobal($style)
					{
						$this->bar['style']=$style;
						return $this;
					}
				function Output()
					{
						if (is_array($this->bar['buttons']))
							{
								foreach ($this->bar['buttons'] as $buttonname=>&$buttonparams)
									{
										if ($buttonparams['type']=='separator')
											{
												$buttonparams['htmlelement']='span';
												$buttonparams['class'].=(empty($buttonparams['class'])?'':' ').'ab-separator';
											}
										else
											{
												$buttonparams['htmlelement']='button';
												$buttonparams['class'].=(empty($buttonparams['class'])?'':' ').'ab-button';
											}
										if ($buttonparams['bold'])
											$buttonparams['style'].=(empty($buttonparams['style'])?'':';').'font-weight:bold;';
										if (!empty($buttonparams['width']))
											$buttonparams['style'].=(empty($buttonparams['style'])?'':';').'width:'.$buttonparams['width'].';';
										if (!empty($buttonparams['height']))
											$buttonparams['style'].=(empty($buttonparams['style'])?'':';').'height:'.$buttonparams['height'].';';
										if (!empty($buttonparams['url']) || !empty($buttonparams['javascript']))
											{
												if ($buttonparams['type']=='messagebox')
													$buttonparams['onclick']="button_msgbox('".$buttonparams['url']."', '".jsescape($buttonparams['message'])."');";
												elseif (!empty($buttonparams['javascript']))
													$buttonparams['onclick']=$buttonparams['javascript'];
												else
													$buttonparams['onclick']="location.href='".$buttonparams['url']."'";
											}
										$buttonparams['html']=$buttonparams['caption'];
										$buttonparams['attrs']['style']=$buttonparams['style'];
										$buttonparams['attrs']['onclick']=$buttonparams['onclick'];
										$buttonparams['attrs']['class']=$buttonparams['class'];
									}
							}
						$this->bar['count']=count($this->bar['buttons']);
						return $this->bar;
					}
			}

		define("adminbuttons_DEFINED", 1);
	}

?>
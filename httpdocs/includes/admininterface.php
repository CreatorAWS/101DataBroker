<?php

//------------------------------------------------------------------------------
//|                                                                            |
//|            Content Management System SiMan CMS                             |
//|                                                                            |
//------------------------------------------------------------------------------

//==============================================================================
//#ver 1.6.7
//#revision 2014-05-20
//==============================================================================

if (!defined("admininterface_DEFINED"))
	{
		sm_add_cssfile('common_admininterface.css');
		
		class TInterface
			{
				var $blocks;
				var $currentblock;
				var $items;
				var $item;
				function TInterface($title='', $showborders=1)
					{
						$this->currentblock=-1;
						$this->AddBlock($title, $showborders);
					}
				function SetActiveItem()
					{
						$this->item=&$this->blocks[$this->currentblock]['items'][count($this->blocks[$this->currentblock]['items'])];
					}
				function AddBlock($title, $showborders=1)
					{
						if (!($this->currentblock==0 && $this->blocks[$this->currentblock]['itemscount']==0))
							$this->currentblock++;
						$this->blocks[$this->currentblock]['title']=$title;
						$this->blocks[$this->currentblock]['show_borders']=$showborders;
						$this->blocks[$this->currentblock]['items']=Array();
						$this->items=&$this->blocks[$this->currentblock]['items'];
						$this->blocks[$this->currentblock]['itemscount']=0;
						$this->SetActiveItem();
					}
				function AddOutputObject($type, $object, $tpl='', $use_data_as_output=false)
					{
						$this->blocks[$this->currentblock]['itemscount']++;
						$this->item['type']=$type;
						$this->item['tpl']=$tpl;
						if (is_object($object) && !$use_data_as_output)
							$this->item[$type]=$object->Output();
						else
							$this->item['data']=$object->Output();
						$this->SetActiveItem();
					}
				function Add($object)
					{
						if (get_class($object)=='TGrid')
							$this->AddOutputObject('table', $object);
						elseif (get_class($object)=='TForm')
							$this->AddOutputObject('form', $object);
						elseif (get_class($object)=='TButtons')
							$this->AddOutputObject('bar', $object);
						elseif (get_class($object)=='TPanel')
							$this->AddOutputObject('panel', $object);
						elseif (get_class($object)=='TBoardMessages')
							$this->AddOutputObject('board', $object);
						elseif (get_class($object)=='TTabs')
							$this->AddOutputObject('tabs', $object, 'common_admintabs.tpl', true);
						elseif (get_class($object)=='TDashBoard')
							$this->AddOutputObject('dashboard', $object, 'common_admindashboard.tpl', true);
						elseif (get_class($object)=='TNavigation')
							$this->AddOutputObject('navigation', $object, 'common_adminnavigation.tpl', true);
					}
				function AddForm($form)
					{
						$this->AddOutputObject('form', $form);
					}
				function AddTPL($tplname, $action='view', $data=Array())
					{
						$this->blocks[$this->currentblock]['itemscount']++;
						$this->item['type']='tpl';
						$this->item['action']=$action;
						$this->item['tpl']=$tplname;
						$this->item['data']=$data;
						$this->SetActiveItem();
					}
				function AddGrid($grid)
					{
						$this->AddOutputObject('table', $grid);
					}
				function AddConversation($messages)
					{
						$this->AddOutputObject('board', $messages);
					}
				function AddButtons($buttons)
					{
						$this->AddOutputObject('bar', $buttons);
					}
				function AddPanel($panel)
					{
						$this->AddOutputObject('panel', $panel);
					}
				function AddPagebarParams($count, $limit, $offset)
					{
						global $sm;
						$sm['m']['pages']['url'] = sm_this_url('from', '');
						$sm['m']['pages']['selected'] = ceil(($offset + 1) / $limit);
						$sm['m']['pages']['interval'] = $limit;
						$sm['m']['pages']['records'] = $count;
						$sm['m']['pages']['selected'] = ceil(($offset + 1) / $sm['m']['pages']['interval']);
						$sm['m']['pages']['pages'] = ceil(intval($sm['m']['pages']['records']) / $sm['m']['pages']['interval']);
						$this->AddPagebar();
					}
				function AddPagebar($html_not_used='')
					{
						$this->blocks[$this->currentblock]['itemscount']++;
						$this->item['type']='pagebar';
						$this->SetActiveItem();
					}
				function AddDashboard($dashboard)
					{
						$this->AddOutputObject('dashboard', $dashboard, 'common_admindashboard.tpl', true);
					}
				function AddNavigation($navigation)
					{
						$this->AddOutputObject('navigation', $navigation, 'common_adminnavigation.tpl', true);
					}
				function html($html)
					{
						$this->blocks[$this->currentblock]['itemscount']++;
						$this->item['type']='html';
						$this->item['html']=$html;
						$this->SetActiveItem();
					}
				function div($html, $id='', $class='', $style='', $additionaltagattrs='', $writeclosetag=true)
					{
						$code='<div'.(empty($id)?'':' id="'.$id.'"').''.(empty($class)?'':' class="'.$class.'"').''.(empty($style)?'':' style="'.$style.'"').$additionaltagattrs.'>'.$html.($writeclosetag?'</div>':'');
						$this->html($code);
					}
				function div_open($id='', $class='', $style='', $additionaltagattrs='')
					{
						$this->div('', $id, $class, $style, $additionaltagattrs, false);
					}
				function div_close()
					{
						$this->html('</div>');
					}
				function p($html, $id='', $class='', $style='', $additionaltagattrs='', $writeclosetag=true)
					{
						$code='<p'.(empty($id)?'':' id="'.$id.'"').''.(empty($class)?'':' class="'.$class.'"').''.(empty($style)?'':' style="'.$style.'"').$additionaltagattrs.'>'.$html.($writeclosetag?'</p>':'');
						$this->html($code);
					}
				function p_open($id='', $class='', $style='', $additionaltagattrs='')
					{
						$this->p('', $id, $class, $style, $additionaltagattrs, false);
					}
				function p_close()
					{
						$this->html('</p>');
					}
				function img($src, $id='', $class='', $style='', $additionaltagattrs='')
					{
						$code='<img src="'.$src.'"'.(empty($id)?'':' id="'.$id.'"').''.(empty($class)?'':' class="'.$class.'"').''.(empty($style)?'':' style="'.$style.'"').$additionaltagattrs.' />';
						$this->html($code);
					}
				function h($type=1, $html, $id='', $class='', $style='', $additionaltagattrs='')
					{
						$code='<h'.$type.(empty($id)?'':' id="'.$id.'"').''.(empty($class)?'':' class="'.$class.'"').''.(empty($style)?'':' style="'.$style.'"').$additionaltagattrs.'>'.$html.'</h'.$type.'>';
						$this->html($code);
					}
				function a($href, $html, $id='', $class='', $style='', $onclick='', $additionaltagattrs='')
					{
						$code='<a href="'.$href.'"'.(empty($id)?'':' id="'.$id.'"').''.(empty($class)?'':' class="'.$class.'"').''.(empty($style)?'':' style="'.$style.'"').''.(empty($onclick)?'':' onclick="'.$onclick.'"').$additionaltagattrs.'>'.$html.'</a>';
						$this->html($code);
					}
				function br()
					{
						$this->html('<br />');
					}
				function style($css)
					{
						$this->html('<style type="text/css">'.$css.'</style>');
					}
				function javascript($jscode)
					{
						$this->html('<script type="text/javascript">'.$jscode.'</script>');
					}
				function hr($id='', $class='', $style='', $additionaltagattrs='')
					{
						$code='<hr '.(empty($id)?'':' id="'.$id.'"').''.(empty($class)?'':' class="'.$class.'"').''.(empty($style)?'':' style="'.$style.'"').$additionaltagattrs.' />';
						$this->html($code);
					}
				function NotificationError($message, $url='', $open_url_in_new_window=false)
					{
						if (strlen($url)>0)
							$message='<a href="'.$url.'"'.($open_url_in_new_window?' target="_blank"':'').'>'.$message.'</a>';
						$this->div($message, '', 'aui-message aui-message-error');
					}
				function NotificationWarning($message, $url='', $open_url_in_new_window=false)
					{
						if (strlen($url)>0)
							$message='<a href="'.$url.'"'.($open_url_in_new_window?' target="_blank"':'').'>'.$message.'</a>';
						$this->div($message, '', 'aui-message aui-message-warning');
					}
				function NotificationInfo($message, $url='', $open_url_in_new_window=false)
					{
						if (strlen($url)>0)
							$message='<a href="'.$url.'"'.($open_url_in_new_window?' target="_blank"':'').'>'.$message.'</a>';
						$this->div($message, '', 'aui-message aui-message-info');
					}
				function NotificationSuccess($message, $url='', $open_url_in_new_window=false)
					{
						if (strlen($url)>0)
							$message='<a href="'.$url.'"'.($open_url_in_new_window?' target="_blank"':'').'>'.$message.'</a>';
						$this->div($message, '', 'aui-message aui-message-success');
					}
				function Output($replace_template=false)
					{
						global $modules, $modules_index;
						if ($replace_template)
							{
								$modules[$modules_index]['module']='common_admininterface';
								$modules[$modules_index]['mode']='common_admininterface_launcher';
								$modules[$modules_index]['common_admininterface_output']=$this->blocks;
								return $this->blocks;
							}
						else
							return $this->blocks;
					}
			}
		
		class TPanel extends TInterface
			{
				function TPanel($width='', $height='', $style='', $class='', $id='')
					{
						$this->TInterface('', 0);
						if (!empty($width))
							$style.='width:'.$width.';';
						if (!empty($height))
							$style.='height:'.$height.';';
						$this->html('<div class="common_adminpanel'.(empty($class)?'':' '.$class).'" style="'.$style.'"'.(empty($id)?'':' id="'.$id.'"').'>');
					}
				function Output()
					{
						$this->html('</div>');
						return $this->blocks;
					}
			}
		
		define("admininterface_DEFINED", 1);
	}

?>
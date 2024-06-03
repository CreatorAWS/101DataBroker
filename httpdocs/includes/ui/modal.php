<?php

	//------------------------------------------------------------------------------
	//|                                                                            |
	//|            Content Management System SiMan CMS                             |
	//|                                                                            |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#revision 2017-05-09
	//==============================================================================

	if (!defined("ui_modal_DEFINED"))
		{
			sm_add_cssfile('ext/modal/jsmodal/css/jsmodal-siman.css', true);
			sm_add_jsfile('ext/modal/jsmodal/js/jsmodal-1.0d.js', true);

			class TModalHelper
				{
					var $info;

					function __construct()
						{
							global $sm;
							$this->SetWidth('50%');
							$this->SetHeight('50%');
						}

					function SetContent($html)
						{
							$html=str_replace('"', '&quot;', $html);
							$this->info['content']=jsescape($html);
							return $this;
						}

					function SetContentDOMSource($dom_element)
						{
							$this->info['content_dom']=$dom_element;
							return $this;
						}

					function SetAJAXSource($url)
						{
							$this->info['ajaxContent']=$url;
							return $this;
						}

					function SetAJAXSourceCallback($ajaxSuccessCallback)
						{
							$this->info['ajaxSuccessCallback']=$ajaxSuccessCallback;
							return $this;
						}

					function SetWidth($width)
						{
							$this->info['width']=$width;
							return $this;
						}

					function SetHeight($height)
						{
							$this->info['height']=$height;
							return $this;
						}

					function GetJSCode()
						{
							$js="  Modal.open({";
							if (!empty($this->info['ajaxContent']))
							    $js.="ajaxContent:'".$this->info['ajaxContent']."',";
							if (!empty($this->info['width']))
							    $js.="width:'".$this->info['width']."',";
							if (!empty($this->info['height']))
							    $js.="height:'".$this->info['height']."',";
							if ($this->info['hideClose'])
							    $js.="hideClose: true,";
							if (!empty($this->info['closeAfter']))
								$js.="closeAfter:".intval($this->info['closeAfter']).",";
							if (!empty($this->info['content']))
								$js.="content:'".$this->info['content']."',";
							if (!empty($this->info['content_dom']))
								$js.="content:$('".$this->info['content_dom']."').html(),";
							if (!empty($this->info['openCallback']))
								$js.="openCallback:".$this->info['openCallback'].",";
							if (!empty($this->info['ajaxSuccessCallback']))
								$js.="ajaxSuccessCallback:".$this->info['ajaxSuccessCallback'].",";
							if ($this->info['draggable'])
								$js.="draggable: true";
							else
								$js.="draggable: false";
							$js.="});";
							return $js;
						}
					
					public static function ModalDOMSelector()
						{
							return '#modal-content';
						}

					public static function GetCloseJSCode()
						{
							return 'Modal.close()';
						}

				}

			define("ui_modal_DEFINED", 1);
		}

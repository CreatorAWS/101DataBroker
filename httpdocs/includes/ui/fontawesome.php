<?php

	//------------------------------------------------------------------------------
	//|                                                                            |
	//|            Content Management System SiMan CMS                             |
	//|                                                                            |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#revision 2016-05-26
	//==============================================================================

	if (!defined("ui_fa_DEFINED"))
		{
			//sm_add_cssfile('ext/tools/fontawesome/css/font-awesome.css', true);

			class FA
				{
					protected $icon='';
					protected $size='';

					function __construct()
						{
						}
					
					function SetIcon($iconkeyword)
						{
							$this->icon=$iconkeyword;
							return $this;
						}

					function Size($sizekeyword)
						{
							$this->size=$sizekeyword;
							return $this;
						}

					function Code()
						{
							$str='<i class="fa fa-';
							$str.=$this->icon;
							if (!empty($this->size))
								$str.=' fa-'.$this->size;
							$str.='"></i>';
							return $str;
						}

					public static function Icon($iconkeyword)
						{
							$fa=new FA();
							$fa->SetIcon($iconkeyword);
							return $fa;
						}

					public static function EmbedCodeFor($iconkeyword)
						{
							$fa=new FA();
							$fa->SetIcon($iconkeyword);
							return $fa->Code();
						}
				}

			define("ui_fa_DEFINED", 1);
		}

?>
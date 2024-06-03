<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	if (!defined("EXTEDITOR_FUNCTIONS_DEFINED"))
		{
			function siman_prepare_to_exteditor($str)
				{
					return $str;
				}

			function siman_exteditor_insert_image($image)
				{
					return "tinyMCE.execCommand('mceInsertContent',false,'<img src=\\'".jsescape($image)."\\'>')";
				}

			$sm['tinymce5_1_6_default_params']=',menubar: \'file edit insert view format table tools insert_tag\', relative_urls : false, document_base_url : "https://\'.main_domain().\'/", remove_script_host : false, convert_urls : false, plugins: [
				\'advlist autolink lists link image charmap anchor\',
				\'searchreplace visualblocks code fullscreen\',
				\'insertdatetime media table paste code wordcount inserttag\'
			  ],
			  toolbar: [\'formatselect | fontsizeselect | fontselect | \' +
			  \' bullist numlist | bold italic | forecolor backcolor link | image inserttag uploadimage |\' +
			  \'  | alignleft aligncenter alignright alignjustify removeformat | \']';

			define("EXTEDITOR_FUNCTIONS_DEFINED", 1);
		}


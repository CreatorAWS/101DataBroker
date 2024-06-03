<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	if (!defined("AUTOCOMPLETE_FUNCTIONS_DEFINED"))
		{
			//Call sm_use('autocomplete') to include the autocomplete library
			
			function sm_autocomplete_init_controls() 
				{
					sm_add_cssfile('autocomplete.css');
					sm_add_jsfile('ext/autocomplete/jquery.autocomplete.js', true);
				}
			
			function sm_autocomplete_for($dom_element, $ajax_url) 
				{
					sm_html_headend("<script type=\"text/javascript\">$(document).ready(function() {
							$('".$dom_element."').autocomplete({
   								 serviceUrl: '".$ajax_url."'
							});
						});</script>");
				}
			
			function sm_autocomplete_output($array, $return_instead_print=false) 
				{
					global $sm;
					sm_nocache();
					if (!is_array($array))
						$array=Array($array);
					$array=Array('suggestions'=>$array);
					if ($return_instead_print)
						return(json_encode($array));
					@header('Content-type: text/html; charset='.$sm['s']['charset']);
					print(json_encode($array));
					exit;
				}
			
			define("AUTOCOMPLETE_FUNCTIONS_DEFINED", 1);
		}

?>
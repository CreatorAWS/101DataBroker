<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	if (!defined("DATEPICKER_FUNCTIONS_DEFINED"))
		{
			sm_add_cssfile('ext/tools/datepicker/bootstrap-datepicker.css', true);
			sm_add_jsfile('ext/tools/datepicker/bootstrap-datepicker.min.js', true);
			if (sm_current_language()=='ua' || sm_current_language()=='ukr')
				sm_add_jsfile('ext/tools/datepicker/bootstrap-datepicker.uk.min.js', true);
			elseif (file_exists('ext/tools/datepicker/bootstrap-datepicker.'.sm_current_language().'.min.js'))
				sm_add_jsfile(file_exists('ext/tools/datepicker/bootstrap-datepicker.'.sm_current_language().'.min.js'), true);
			define("DATEPICKER_FUNCTIONS_DEFINED", 1);
		}

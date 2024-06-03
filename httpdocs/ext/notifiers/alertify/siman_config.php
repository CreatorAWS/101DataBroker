<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	if (!defined("NOTIFIER_FUNCTIONS_DEFINED"))
		{
			if (is_array($sm['s']['notifications']))
				{
					sm_add_cssfile('ext/notifiers/alertify/themes/alertify.core.css', 1);
					sm_add_cssfile('ext/notifiers/alertify/themes/alertify.default.css', 1);
					sm_add_jsfile('ext/notifiers/alertify/lib/alertify.min.js', 1);
					sm_html_bodyend('<script>(function() {alertify.set({ delay: 7000 });');
					foreach ($sm['s']['notifications'] as $key=>$val)
						{
							if (!empty($val['onpage']) && strcmp($val['onpage'], sm_relative_url(sm_this_url()))!=0)
								continue;
							if (strcmp($val['type'], 'error')==0)
								sm_html_bodyend('alertify.error("'.(empty($val['title'])?'':'<b>'.jsescape($val['title']).'<b><br />').jsescape($val['message']).'");');
							elseif (strcmp($val['type'], 'info')==0)
								sm_html_bodyend('alertify.log("'.(empty($val['title'])?'':'<b>'.jsescape($val['title']).'<b><br />').jsescape($val['message']).'");');
							else
								sm_html_bodyend('alertify.success("'.(empty($val['title'])?'':'<b>'.jsescape($val['title']).'<b><br />').jsescape($val['message']).'");');
						}
					sm_html_bodyend('})();</script>');
				}
		}

?>
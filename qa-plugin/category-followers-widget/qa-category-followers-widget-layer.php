<?php
/*
	Crowdask further on Question2Answer 1.6.2

	http://www.question2answer.org/
	
	File: qa-plugin/category-followers-widget/qa-category-followers-widget-layer.php
	Version: See define()s at top of qa-include/qa-base.php
	Description: Theme layer class for category followers widget plugin

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: http://www.question2answer.org/license.php
*/

class qa_html_theme_layer extends qa_html_theme_base {

	const PLUGIN			= 'categoryfollowerswidget';
	const CUSTOM_CSSPATH	= 'categoryfollowerswidget_custom_csspath';
	
	function head_css() {
		qa_html_theme_base::head_css();
		$option = qa_opt(self::CUSTOM_CSSPATH);
		if(!empty($option)) {
			$this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="'.qa_html(qa_path_to_root().$option).'"/>');
		}
	}
}

/*
	Omit PHP closing tag to help avoid accidental output
*/
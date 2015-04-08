<?php
/*
	Crowdask further on Question2Answer 1.6.2

	http://www.question2answer.org/
	
	File: qa-plugin/category-followers-widget/qa-plugin.php
	Version: See define()s at top of qa-include/qa-base.php
	Description: Initiates category followers widget plugin

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

/*
	Plugin Name: Category Followers Widget
	Plugin URI: 
	Plugin Description: Provides list of users who follow category
	Plugin Version: 1.1.2
	Plugin Date: 2013-05-23
	Plugin Author: sama55@CMSBOX
	Plugin Author URI: http://www.cmsbox.jp/
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.5
	Plugin Update Check URI: 
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

qa_register_plugin_phrases('qa-category-followers-widget-lang-*.php', 'categoryfollowerswidget');
qa_register_plugin_module('widget', 'qa-category-followers-widget.php', 'qa_category_followers_widget', 'Category Followers Widget');
qa_register_plugin_layer('qa-category-followers-widget-layer.php', 'Category Followers Widget');

/*
	Omit PHP closing tag to help avoid accidental output
*/
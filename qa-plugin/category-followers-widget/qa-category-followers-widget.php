<?php
/*
	Crowdask further on Question2Answer 1.6.2

	http://www.question2answer.org/
	
	File: qa-plugin/category-followers-widget/qa-category-followers-widget.php
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

class qa_category_followers_widget {

	const PLUGIN			= 'categoryfollowerswidget';
	const COUNT				= 'categoryfollowerswidget_count';
	const COUNT_DFL			= 10;
	const TITLE				= 'categoryfollowerswidget_title';
	const TITLE_DFL			= true;
	const DESC				= 'categoryfollowerswidget_desc';
	const DESC_DFL			= true;
	const AVATAR			= 'categoryfollowerswidget_avatar';
	const AVATAR_DFL		= true;
	const AVATAR_SIZE		= 'categoryfollowerswidget_avatar_size';
	const AVATAR_SIZE_DFL	= 50;
	const NAME				= 'categoryfollowerswidget_name';
	const NAME_DFL			= 'handle';
	const NAME_NON			= '';
	const NAME_HANDLE		= 'handle';
	const NAME_FULLNAME		= 'fullname';
	const CUSTOM_CSSPATH	= 'categoryfollowerswidget_custom_csspath';
	const CUSTOM_CSSPATH_DFL= 'qa-plugin/category-followers-widget/qa-category-followers-widget-styles.css';	
	const SAVE_BUTTON		= 'categoryfollowerswidget_save_button';
	const DFL_BUTTON		= 'categoryfollowerswidget_dfl_button';
	const SAVED_MESSAGE		= 'categoryfollowerswidget_saved_message';
	const WIDGET_TITLE		= 'categoryfollowerswidget_widget_title';
	const WIDGET_DESC		= 'categoryfollowerswidget_widget_desc';
	const WIDGET_SETTING	= 'categoryfollowerswidget_widget_setting';

	var $directory;
	var $urltoroot;

	function load_module($directory, $urltoroot) {
		$this->directory=$directory;
		$this->urltoroot=$urltoroot;
	}

	function option_default($option) {
		if ($option==self::COUNT) return self::COUNT_DFL;
		if ($option==self::TITLE) return self::TITLE_DFL;
		if ($option==self::DESC) return self::DESC_DFL;
		if ($option==self::AVATAR) return self::AVATAR_DFL;
		if ($option==self::AVATAR_SIZE) return self::AVATAR_SIZE_DFL;
		if ($option==self::NAME) return self::NAME_DFL;
		if ($option==self::CUSTOM_CSSPATH) return self::CUSTOM_CSSPATH_DFL;
	}

	function admin_form(&$qa_content) {
		$saved=false;
		$error='';
		if (qa_clicked(self::SAVE_BUTTON)) {
			if (trim(qa_post_text(self::COUNT.'_field')) == '')
				$error = qa_lang_html(self::PLUGIN.'/'.self::COUNT.'_error');
			if (!is_numeric(trim(qa_post_text(self::COUNT.'_field'))))
				$error = qa_lang_html(self::PLUGIN.'/'.self::COUNT.'_error');
			if (trim(qa_post_text(self::AVATAR_SIZE.'_field')) == '')
				$error = qa_lang_html(self::PLUGIN.'/'.self::AVATAR_SIZE.'_error');
			if (!is_numeric(trim(qa_post_text(self::AVATAR_SIZE.'_field'))))
				$error = qa_lang_html(self::PLUGIN.'/'.self::AVATAR_SIZE.'_error');
			if ($error == '') {
				qa_opt(self::COUNT,(int)qa_post_text(self::COUNT.'_field'));
				qa_opt(self::TITLE,(int)qa_post_text(self::TITLE.'_field'));
				qa_opt(self::DESC,(int)qa_post_text(self::DESC.'_field'));
				qa_opt(self::AVATAR,(int)qa_post_text(self::AVATAR.'_field'));
				qa_opt(self::AVATAR_SIZE,(int)qa_post_text(self::AVATAR_SIZE.'_field'));
				qa_opt(self::NAME,qa_post_text(self::NAME.'_field'));
				qa_opt(self::CUSTOM_CSSPATH,qa_post_text(self::CUSTOM_CSSPATH.'_field'));
				$saved=true;
			}
		}
		if (qa_clicked(self::DFL_BUTTON)) {
			qa_opt(self::COUNT,self::COUNT_DFL);
			qa_opt(self::TITLE,self::TITLE_DFL);
			qa_opt(self::DESC,self::DESC_DFL);
			qa_opt(self::AVATAR,self::AVATAR_DFL);
			qa_opt(self::AVATAR_SIZE,self::AVATAR_SIZE_DFL);
			qa_opt(self::NAME,self::NAME_DFL);
			qa_opt(self::CUSTOM_CSSPATH,self::CUSTOM_CSSPATH_DFL);
			$saved=true;
		}
		
		$rules = array();
		$rules[self::AVATAR_SIZE] = self::AVATAR.'_field';
		qa_set_display_rules($qa_content, $rules);

		$ret = array();
		if($saved)
			$ret['ok'] = qa_lang_html(self::PLUGIN.'/'.self::SAVED_MESSAGE);
		else {
			if($error != '')
				$ret['ok'] = '<SPAN STYLE="color:#F00;">'.$error.'</SPAN>';
		}

		$fields = array();
		$fields[] = array(
			'id' => self::COUNT,
			'label' => qa_lang_html(self::PLUGIN.'/'.self::COUNT.'_label'),
			'type' => 'number',
			'value' => (int)qa_opt(self::COUNT),
			'tags' => 'NAME="'.self::COUNT.'_field" ID="'.self::COUNT.'_field"',
			'suffix' => qa_lang_html(self::PLUGIN.'/'.self::COUNT.'_suffix'),
		);
		$fields[] = array(
			'id' => self::TITLE,
			'label' => qa_lang_html(self::PLUGIN.'/'.self::TITLE.'_label'),
			'type' => 'checkbox',
			'value' => (int)qa_opt(self::TITLE),
			'tags' => 'NAME="'.self::TITLE.'_field" ID="'.self::TITLE.'_field"',
		);
		$fields[] = array(
			'id' => self::DESC,
			'label' => qa_lang_html(self::PLUGIN.'/'.self::DESC.'_label'),
			'type' => 'checkbox',
			'value' => (int)qa_opt(self::DESC),
			'tags' => 'NAME="'.self::DESC.'_field" ID="'.self::DESC.'_field"',
		);
		$fields[] = array(
			'id' => self::AVATAR,
			'label' => strtr(qa_lang_html(self::PLUGIN.'/'.self::AVATAR.'_label'), array(
						'^1' => '<A href="'.qa_opt('site_url').'admin/users#avatar_default_show" target="_blank">',
						'^2' => '</A>')),
			'type' => 'checkbox',
			'value' => (int)qa_opt(self::AVATAR),
			'tags' => 'NAME="'.self::AVATAR.'_field" ID="'.self::AVATAR.'_field"',
		);
		$fields[] = array(
			'id' => self::AVATAR_SIZE,
			'label' => qa_lang_html(self::PLUGIN.'/'.self::AVATAR_SIZE.'_label'),
			'type' => 'number',
			'value' => (int)qa_opt(self::AVATAR_SIZE),
			'tags' => 'NAME="'.self::AVATAR_SIZE.'_field" ID="'.self::AVATAR_SIZE.'_field"',
			'suffix' => qa_lang_html(self::PLUGIN.'/'.self::AVATAR_SIZE.'_suffix'),
		);
		$fields[] = array(
			'id' => self::NAME,
			'label' => qa_lang_html(self::PLUGIN.'/'.self::NAME.'_label'),
			'type' => 'select',
			'value' => qa_opt(self::NAME),
			'tags' => 'NAME="'.self::NAME.'_field"',
			'options' => array(self::NAME_NON=>self::NAME_NON, self::NAME_HANDLE=>self::NAME_HANDLE, self::NAME_FULLNAME=>self::NAME_FULLNAME),
			'suffix' => qa_lang_html(self::PLUGIN.'/'.self::NAME.'_suffix'),
		);
		$fields[] = array(
			'id' => self::CUSTOM_CSSPATH,
			'label' => qa_lang_html(self::PLUGIN.'/'.self::CUSTOM_CSSPATH.'_label'),
			'value' => qa_opt(self::CUSTOM_CSSPATH),
			'tags' => 'NAME="'.self::CUSTOM_CSSPATH.'_field" ID="'.self::CUSTOM_CSSPATH.'_field"',
		);
		$ret['fields'] = $fields;

		$buttons = array();
		$buttons[] = array(
			'label' => qa_lang_html(self::PLUGIN.'/'.self::SAVE_BUTTON),
			'tags' => 'NAME="'.self::SAVE_BUTTON.'" ID="'.self::SAVE_BUTTON.'"',
		);
		$buttons[] = array(
			'label' => qa_lang_html(self::PLUGIN.'/'.self::DFL_BUTTON),
			'tags' => 'NAME="'.self::DFL_BUTTON.'" ID="'.self::DFL_BUTTON.'"',
		);
		$ret['buttons'] = $buttons;

		return $ret;
	}
	
	function allow_template($template) {
		$allow=false;
		switch ($template) {
			case 'qa':
			case 'activity':
			case 'questions':
				$allow=true;
				break;
		}
		return $allow;
	}
	
	function allow_region($region) {
		$allow=false;
		switch ($region) {
		case 'main':
		case 'side':
		case 'full':
			$allow=true;
			break;
		default:
			break;
		}
		return $allow;
	}
	
	function output_widget($region, $place, $themeobject, $template, $request, $qa_content){
		$catid = false;
		if (isset($qa_content['categoryids']) && count($qa_content['categoryids']) >= 1 && $qa_content['categoryids'][0] != '')
			$catid = $qa_content['categoryids'][count($qa_content['categoryids'])-1];
		
		if ($catid) {
			$users = qa_db_single_select($this->qa_db_user_favorit_cat_selectspec($catid));			
			shuffle($users);
			
			$themeobject->output('<DIV CLASS="qa-category-followers-region-'.$region.'">');

			if(qa_opt(self::TITLE))
				$themeobject->output('<H2 CLASS="qa-category-followers-header">', qa_lang_html(self::PLUGIN.'/'.self::WIDGET_TITLE), '</H2>');
			if(qa_get_logged_in_level() >= QA_USER_LEVEL_SUPER) {
				$themeobject->output('<P class="qa-category-followers-setting">');
				$themeobject->output(strtr(qa_lang_html(self::PLUGIN.'/' . self::WIDGET_SETTING),
					array(
						'^1' => '<A HREF="'.qa_path_html('admin/plugins', null, null, null, md5('widget/Category Followers Widget')).'">',
						'^2' => '</A>')
					));
				$themeobject->output('</P>');
			}
			if(qa_opt(self::DESC))
				$themeobject->output('<P CLASS="qa-category-followers-desc">', qa_lang_html_sub(self::PLUGIN.'/'.self::WIDGET_DESC, count($users)), '</P>');
			
			if (count($users) >= 1) {
				$class = '';
				if(qa_opt(self::AVATAR))
					$class .= ' qa-category-followers-list-avatar-exist';
				else
					$class .= ' qa-category-followers-list-avatar-non';
				if(qa_opt(self::NAME))
					$class .= ' qa-category-followers-list-name-exist';
				else
					$class .= ' qa-category-followers-list-name-non';
				$themeobject->output('<UL CLASS="qa-category-followers-list'.$class.'">');
				foreach (array_slice($users,0,(int)qa_opt(self::COUNT)) as $user){
					if(!QA_FINAL_EXTERNAL_USERS)
						$userprofile=qa_db_single_select(qa_db_user_profile_selectspec($user['handle'], false));
					else
						$userprofile=null;
					$themeobject->output('<LI>');
					if(qa_opt(self::AVATAR))
						$themeobject->output('<SPAN CLASS="qa-category-followers-avatar">'.qa_get_user_avatar_html($user['flags'], $user['email'], $user['handle'], $user['avatarblobid'], $user['avatarwidth'], $user['avatarheight'], (int)qa_opt(self::AVATAR_SIZE), false).'</SPAN>');
					if(qa_opt(self::NAME)) {
						if(qa_opt(self::NAME) == self::NAME_NON)
							$name = '';
						elseif(qa_opt(self::NAME) == self::NAME_HANDLE)
							$name = $user['handle'];
						else
							if(isset($userprofile['name']) && @$userprofile['name'] != '')
								$name = $userprofile['name'];
							else
								$name = $user['handle'];
						if(qa_opt(self::AVATAR))
							$themeobject->output('<SPAN CLASS="qa-category-followers-name" STYLE="width:'.qa_opt(self::AVATAR_SIZE).'px;"><A HREF="'.qa_path_html('user/'.$user['handle']).'" CLASS="qa-user-link" TITLE="'.$name.'">'.$name.'</A></SPAN>');
						else
							$themeobject->output('<SPAN CLASS="qa-category-followers-name"><A HREF="'.qa_path_html('user/'.$user['handle']).'" CLASS="qa-user-link" TITLE="'.$name.'">'.$name.'</A></SPAN>');
					}
					$themeobject->output('</LI>');
				}
				$themeobject->output('</UL>');
			}
			$themeobject->output('</DIV>');
			$themeobject->output('<DIV CLASS="qa-category-followers-clear"></DIV>');
		}
	}
	
	function qa_db_user_favorit_cat_selectspec($catid)
	{
		require_once QA_INCLUDE_DIR.'qa-app-updates.php';
		return array(
			'columns' => array('^users.userid', 'handle', 'points', 'flags', '^users.email', 'avatarblobid', 'avatarwidth', 'avatarheight'),
			'source' => "^users JOIN ^userpoints ON ^users.userid=^userpoints.userid JOIN ^userfavorites ON ^users.userid=^userfavorites.userid JOIN ^categories ON ^userfavorites.entityid=^categories.categoryid WHERE ^categories.categoryid=$ AND ^userfavorites.entitytype=$",
			'arguments' => array($catid, QA_ENTITY_CATEGORY),
			'sortdesc' => 'points',
		);
	}
};

/*
	Omit PHP closing tag to help avoid accidental output
*/
<?php
	
/*
	Crowdask further on Question2Answer 1.6.2

	http://www.question2answer.org/

	
	File: qa-include/qa-page-account.php
	Version: See define()s at top of qa-include/qa-base.php
	Description: Controller for user account page


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

	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../');
		exit;
	}

	require_once QA_INCLUDE_DIR.'qa-db-users.php';
	require_once QA_INCLUDE_DIR.'qa-app-format.php';
	require_once QA_INCLUDE_DIR.'qa-app-users.php';
	require_once QA_INCLUDE_DIR.'qa-db-selects.php';
	require_once QA_INCLUDE_DIR.'qa-util-image.php';
	
	
//	Check we're not using single-sign on integration, that we're logged in
	
	if (QA_FINAL_EXTERNAL_USERS)
		qa_fatal_error('User accounts are handled by external code');
	
	$userid=qa_get_logged_in_userid();
	
	if (!isset($userid))
		qa_redirect('login');
		

//	Get current information on user

	$useraccount=qa_db_select_with_pending(qa_db_user_account_selectspec($userid, true));
	
	$haspassword=isset($useraccount['passsalt']) && isset($useraccount['passcheck']);



//	Process change password if clicked

	if (qa_clicked('dochangepassword')) {
		require_once QA_INCLUDE_DIR.'qa-app-users-edit.php';
		
		$inoldpassword=qa_post_text('oldpassword');
		$innewpassword1=qa_post_text('newpassword1');
		$innewpassword2=qa_post_text('newpassword2');
		
		if (!qa_check_form_security_code('password', qa_post_text('code')))
			$errors['page']=qa_lang_html('misc/form_security_again');
		
		else {
			$errors=array();
			
			if ($haspassword && (strtolower(qa_db_calc_passcheck($inoldpassword, $useraccount['passsalt'])) != strtolower($useraccount['passcheck'])))
				$errors['oldpassword']=qa_lang('users/password_wrong');
			
			$useraccount['password']=$inoldpassword;
			$errors=$errors+qa_password_validate($innewpassword1, $useraccount); // array union
	
			if ($innewpassword1 != $innewpassword2)
				$errors['newpassword2']=qa_lang('users/password_mismatch');
				
			if (empty($errors)) {
				qa_db_user_set_password($userid, $innewpassword1);
				qa_db_user_set($userid, 'sessioncode', ''); // stop old 'Remember me' style logins from still working
				qa_set_logged_in_user($userid, $useraccount['handle'], false, $useraccount['sessionsource']); // reinstate this specific session
	
				qa_report_event('u_password', $userid, $useraccount['handle'], qa_cookie_get());
			
				qa_redirect('change-password', array('state' => 'password-changed'));
			}
		}
	}


//	Prepare content for theme

	$qa_content=qa_content_prepare();

	$qa_content['title']='Change password details';	
	$qa_content['error']=@$errors['page'];

//	Change password form

	$qa_content['form_password']=array(
		'tags' => 'method="post" action="'.qa_self_html().'"',
		
		'style' => 'wide',
		
		'title' => qa_lang_html('users/change_password'),
		
		'fields' => array(
			'old' => array(
				'label' => qa_lang_html('users/old_password'),
				'tags' => 'name="oldpassword"',
				'value' => qa_html(@$inoldpassword),
				'type' => 'password',
				'error' => qa_html(@$errors['oldpassword']),
			),
		
			'new_1' => array(
				'label' => qa_lang_html('users/new_password_1'),
				'tags' => 'name="newpassword1"',
				'type' => 'password',
				'error' => qa_html(@$errors['password']),
			),

			'new_2' => array(
				'label' => qa_lang_html('users/new_password_2'),
				'tags' => 'name="newpassword2"',
				'type' => 'password',
				'error' => qa_html(@$errors['newpassword2']),
			),
		),
		
		'buttons' => array(
			'change' => array(
				'label' => qa_lang_html('users/change_password'),
			),
		),
		
		'hidden' => array(
			'dochangepassword' => '1',
			'code' => qa_get_form_security_code('password'),
		),
	);
	
	if (!$haspassword) {
		$qa_content['form_password']['fields']['old']['type']='static';
		$qa_content['form_password']['fields']['old']['value']=qa_lang_html('users/password_none');
	}
	
	if (qa_get_state()=='password-changed')
		$qa_content['form_password']['ok']=qa_lang_html('users/password_changed');
		

	$qa_content['navigation']['sub']=qa_account_sub_navigation();
		
		
	return $qa_content;
	

/*
	Omit PHP closing tag to help avoid accidental output
*/
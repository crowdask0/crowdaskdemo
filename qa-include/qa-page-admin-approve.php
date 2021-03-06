<?php
	
/*
	Crowdask further on Question2Answer 1.6.2

	http://www.question2answer.org/

	
	File: qa-include/qa-page-admin-approve.php
	Version: See define()s at top of qa-include/qa-base.php
	Description: Controller for admin page showing new users waiting for approval


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

	require_once QA_INCLUDE_DIR.'qa-app-admin.php';
	require_once QA_INCLUDE_DIR.'qa-db-admin.php';

	
//	Check we're not using single-sign on integration
	
	if (QA_FINAL_EXTERNAL_USERS)
		qa_fatal_error('User accounts are handled by external code');


//	Find most flagged questions, answers, comments

    $start=qa_get_start();
    $pagesize=qa_opt('page_size_users');

    $userid=qa_get_logged_in_userid();

    $search = trim(qa_get('q_1'));
    $inquery = trim(qa_get('q_2'));
    if(!isset($inquery))
        $inquery = "";

    $users=qa_db_get_unapproved_users_match_keyword($start,$pagesize,$inquery);
	$userfields=qa_db_select_with_pending(qa_db_userfields_selectspec());


//	Check admin privileges (do late to allow one DB query)

	if (qa_get_logged_in_level()<QA_USER_LEVEL_MODERATOR) {
		$qa_content=qa_content_prepare();
		$qa_content['error']=qa_lang_html('users/no_permission');
		return $qa_content;
	}
		
		
//	Check to see if any were approved or blocked here

	$pageerror=qa_admin_check_clicks();
	

//	Prepare content for theme
	
	$qa_content=qa_content_prepare();

	$qa_content['title']=qa_lang_html('admin/approve_users_title');

	$qa_content['error']=isset($pageerror) ? $pageerror : qa_admin_page_error();


    // 
    $qa_content['message_list']=array(
        'form' => array(
            'tags' => 'method="post" action="'.qa_self_html().'"',

            'hidden' => array(
                'code' => qa_get_form_security_code('admin/click'),
            ),
            'buttons' => array(
                'select_all' => array(
                    'tags' => 'name="select_all" class="qa-form-tall-button qa-form-tall-button-reset" onclick="return qa_admin_approve_select_all(this, 1); "',
                    'label' => 'Select All',
                ),

                'unselect_all' => array(
                    'tags' => 'name="unselect_all" class="qa-form-tall-button qa-form-tall-button-reset" onclick="return qa_admin_approve_select_all(this,0);"',
                    'label' => 'Unselect All',
                ),
                'approve_selected' => array(
                    'tags' => 'name="approve_selected" class="qa-form-tall-button qa-form-tall-button-save" onclick="return qa_admin_approve_selected(this);"',
                    'label' => 'Approve Selected'
                ),
            ),
        ),

        'messages' => array(),
    );

    // hide select buttons if there are no pending users
    if(count($users) == 0)
        unset($qa_content['message_list']['form']['buttons']);


	if (count($users)) {
		foreach ($users as $user) {
			$message=array();

            $message['checkbox'] = 1;
			
			$message['tags']='id="p'.qa_html($user['userid']).'"'; // use p prefix for qa_admin_click() in qa-admin.js
						
			$message['content']=qa_lang_html('users/registered_label').' '.
				strtr(qa_lang_html('users/x_ago_from_y'), array(
					'^1' => qa_time_to_string(qa_opt('db_time')-$user['created']),
					'^2' => qa_ip_anchor_html($user['createip']),
				)).'<br/>';
				
			$htmlemail=qa_html($user['email']);
			
			$message['content'].=qa_lang_html('users/email_label').' <a href="mailto:'.$htmlemail.'">'.$htmlemail.'</a>';
			
			foreach ($userfields as $userfield)
				if (strlen(@$user['profile'][$userfield['title']]))
					$message['content'].='<br/>'.qa_html($userfield['content'].': '.$user['profile'][$userfield['title']]);
				
			$message['meta_order']=qa_lang_html('main/meta_order');
			$message['who']['data']=qa_get_one_user_html($user['handle']);
			
			$message['form']=array(
				'style' => 'light',

				'buttons' => array(
					'approve' => array(
						'tags' => 'name="admin_'.$user['userid'].'_userapprove" onclick="return qa_admin_click(this);"',
						'label' => qa_lang_html('question/approve_button'),
					),

					'block' => array(
						'tags' => 'name="admin_'.$user['userid'].'_userblock" onclick="return qa_admin_click(this);"',
						'label' => qa_lang_html('admin/block_button'),
					),
				),
			);
			
			$qa_content['message_list']['messages'][]=$message;

            $qa_content['admin-search-users'] = 2;
        }
		
	} else
		$qa_content['title']=qa_lang_html('admin/no_unapproved_found');


	$qa_content['navigation']['sub']=qa_admin_sub_navigation();
	$qa_content['script_rel'][]='qa-content/qa-admin.js?'.QA_VERSION;

    //
    $usercount = qa_db_get_unapproved_users_numbers($inquery);
    $qa_content['page_links']=qa_html_page_links(qa_request(), $start, $pagesize, $usercount, qa_opt('pages_prev_next'),
        array(
            'q_1' => 'search',
            'q_2' => $inquery,
        )
    );
	
	return $qa_content;
	

/*
	Omit PHP closing tag to help avoid accidental output
*/
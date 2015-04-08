<?php

/*
	Crowdask further on Question2Answer 1.6.2

	http://www.question2answer.org/
	

	File: qa-include/qa-page-users-search.php
	Version: See define()s at top of qa-include/qa-base.php
    Author: 
	Description: Controller for users search
    Keywords are searched against handle, email and oemail


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
	require_once QA_INCLUDE_DIR.'qa-db-selects.php';
	require_once QA_INCLUDE_DIR.'qa-app-format.php';

    // do not allow page to be requested by low level users
    $level=qa_get_logged_in_level();

    if($level < QA_USER_LEVEL_MODERATOR)
        exit;

//	Get list of all users
	$pagesize=qa_opt('page_size_users');
    $users = qa_db_select_with_pending(qa_db_all_users_selectspec());

//	Prepare content for theme
	
	$qa_content=qa_content_prepare();

	$qa_content['title']='Search Users';

    $qa_content['search-users']=1;

    //	Perform the search if appropriate

    if (strlen(qa_get('q'))) {

        $inquery=trim(qa_get('q'));

        $qa_content['ranking']=array(
            'items' => array(),
            'rows' => ceil($pagesize/qa_opt('columns_users')),
            'type' => 'users'
        );

        // 
        // the following code does the search functions
        if(count($users))
        {
            foreach ($users as $userid => $user)
            {
                if((empty($user['handle'])||strstr($user['handle'],$inquery) === false)
                &&(empty($user['email'])||strstr($user['email'],$inquery) === false)
                &&(empty($user['oemail'])||strstr($user['oemail'], $inquery) === false))
                    unset($users[$userid]);
            }
        }

        $usershtml=qa_userids_handles_html($users);

        if (count($users)) {
            foreach ($users as $userid => $user)
                $qa_content['ranking']['items'][]=array(
                    'label' =>
                    (QA_FINAL_EXTERNAL_USERS
                        ? qa_get_external_avatar_html($user['userid'], qa_opt('avatar_users_size'), true)
                        : qa_get_user_avatar_html($user['flags'], $user['email'], $user['handle'],
                            $user['avatarblobid'], $user['avatarwidth'], $user['avatarheight'], qa_opt('avatar_users_size'), true)
                    ).' '.$usershtml[$user['userid']],
                    //'score' => qa_html(number_format($user['points'])),
                );
        } else
            $qa_content['title'] = 'No results found';
    }

    $qa_content['navigation']['sub']=qa_users_sub_navigation();

	return $qa_content;


/*
	Omit PHP closing tag to help avoid accidental output
*/
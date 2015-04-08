<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 
 * Date: 10/21/13
 * Time: 4:40 PM
 * To change this template use File | Settings | File Templates.
 */

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
    header('Location: ../');
    exit;
}

require_once QA_INCLUDE_DIR.'qa-app-admin.php';
require_once QA_INCLUDE_DIR.'qa-db-selects.php';
require_once QA_INCLUDE_DIR.'qa-db-admin.php';

//	Check admin privileges (do late to allow one DB query)
if (!qa_admin_check_privileges($qa_content))
    return $qa_content;



//process form submission when new rule is added or existing rule is updated
if(qa_clicked('docancel'))
{
	qa_redirect(qa_request());
	
}elseif(qa_clicked('dosavebrule')){
    //hidden field from post form
    $editbruleid = qa_post_text('edit');

    //process post form
    require_once QA_INCLUDE_DIR.'qa-util-string.php';
    $iname = trim(qa_post_text('name'));
    $incontent = trim(qa_post_text('content'));
    $indesc = trim(qa_post_text('desc1'));
    $intype = !strcmp(qa_post_text('type'), "0")? 'GOLD':
        (!strcmp(qa_post_text('type'), "1")? 'SILVER':'BRONZE');
    $errors = array();

    //verify input string validity
    if(empty($iname))
        $errors['name']=qa_lang('main/field_required');
    elseif(qa_strlen($iname) > QA_DB_MAX_BRULE_TITLE_LENGTH)
        $errors['name'] = qa_lang_sub('main/max_length_x',QA_DB_MAX_BRULE_TITLE_LENGTH);

    if(empty($incontent))
        $errors['content']=qa_lang('main/field_required');
    elseif(strlen($incontent) > QA_DB_MAX_BRULE_CONTENT_LENGTH)
        $errors['content'] = qa_lang_sub('main/max_length_x',QA_DB_MAX_BRULE_CONTENT_LENGTH);
    //check the validity of badge rule definition
    elseif(check_brule($incontent) == false)
        $errors['content'] = qa_lang("main/brule_format");

    if(strlen($indesc) > QA_DB_MAX_BRULE_DESC_LENGTH)
        $errors['desc1'] = qa_lang_sub('main/max_length_x',QA_DB_MAX_BRULE_DESC_LENGTH);


    if(empty($errors) ){
        if(!empty($editbruleid)){
            //perform appropriate database update
            qa_db_brule_rename($editbruleid,$iname);
            qa_db_brule_update_rule($editbruleid, $incontent);
            qa_db_brule_update_type($editbruleid, $intype);
            qa_db_brule_update_desc($editbruleid, $indesc);

            qa_redirect(qa_request(),array('edit'=>$editbruleid,'saved' => true));
        }else{
            //create the new database entry
            $editbruleid = qa_db_brule_create($iname,$intype,$incontent,$indesc);

            qa_redirect(qa_request(),array('edit'=>$editbruleid,'added' => true));
        }
    }
}

$editbrule =false;
//Get the brule to edit
if(!isset($editbruleid))
    $editbruleid = qa_get('edit');

//get the list of badge rules
if($editbruleid != null){
    $brules = qa_db_select_with_pending(qa_db_brule_nav_selectspec($editbruleid));
    if(sizeof($brules) != 0)
        $editbrule = true;
}else{
    $brules = qa_db_select_with_pending(qa_db_brule_nav_selectspec());
}

//navigate to add new rule page
if(qa_clicked('doaddbrule'))
    $brules=array();

//prepare content for theme
$qa_content=qa_content_prepare();
$qa_content['title'] = qa_lang_html('admin/admin_title').' - '.qa_lang_html('admin/brules_page_title');

//if adding brule fails, redo the operation
$redoaddbrule = qa_post_text('redoaddbrule');

//display brule form for either editing or adding
if($editbrule || sizeof($brules) == 0 || !empty($redoaddbrule))
{
    //add brule form
    $qa_content['form'] = array(
        'tags' => 'method="post" action="'.qa_path_html(qa_request()).'"',
        'style' => 'tall',
        'ok' => qa_get('saved')? qa_lang_html('admin/brule_saved'):(qa_get('added')? qa_lang_html('admin/brule_added'):null),
        'fields' => array(
                'name' => array(
                    'id' => 'name_display',
                    'tags' => 'name="name" id="name"',
                    'label' => qa_lang_html( 'admin/bRule_name'),
                    'value' => $editbrule? $brules[0]['title']:($redoaddbrule? qa_post_text('name'):''),
                    'error' => qa_html(@$errors['name']),
                 ),
                'type' => array(
                    'id' => 'type_display',
                    'tags' => 'name="type"',
                    'label' => qa_lang_html('admin/bRule_type'),
                    'type' => 'select',
                    'options' => array("GOLD", "SILVER","BRONZE"),
                    'value' => $editbrule? $brules[0]['type']:($redoaddbrule?
                        (!strcmp(qa_post_text('type'), "0")? 'GOLD':
                            (!strcmp(qa_post_text('type'), "1")? 'SILVER':'BRONZE')):''
                    ),
                    'error' => qa_html(@$errors['type']),
                ),
                'rule'=>array(
                    'id' => 'content_display',
                    'tags' => 'name="content"',
                    'label' => qa_lang_html('admin/bRule_rule'),
                    'value' => $editbrule? $brules[0]['content']:($redoaddbrule? qa_post_text('content'):''),
                    'error' => qa_html(@$errors['content']),
                    'rows' => 2,
                ),

                'desc1'=>array(
                    'id' => 'desc1_display',
                    'tags' => 'name="desc1"',
                    'label' => qa_lang_html('admin/bRule_desc1'),
                    'value' => $editbrule? $brules[0]['desc1']:($redoaddbrule? qa_post_text('desc1'):''),
                    'error' => qa_html(@$errors['desc1']),
                    'rows' => 2,
                )

        ),
        'buttons' => array(
            'save' => array(
                'tags' => 'id="dosaveoptions"', // just used for qa_recalc_click
                'label' => $editbrule? qa_lang_html('main/save_button'):qa_lang_html('admin/add_brule_button'),
            ),

            'cancel' => array(
                'tags' => 'name="docancel"',
                'label' => qa_lang_html('main/cancel_button'),
            ),
        ),
        'hidden' => array(
            'dosavebrule' => '1', // for IE
            'redoaddbrule' => $editbrule? null:'1',
            'edit' => $editbruleid,
        ),
    );

}else{
    //display all brules and add brule button
    $qa_content['form'] = array(
        'tags' =>  'method="post" action="'.qa_path_html(qa_request()).'"',
        'style' =>'tall',
        'buttons' => array(
            'add' => array(
                'tags' => 'name="doaddbrule"',
                'label' => qa_lang_html('admin/add_brule_button'),
            ),
        ),
    );


    if(count($brules)){
        $navbrulehtml = '<table class="qa-badges-list-table"><tbody>';
        foreach($brules as $brule){

            $navbrulehtml .= '<tr><td>'.
                "<span class='badge-container'>
              <a class='badge' title='".$brule['desc1']."' href='".qa_path_html('admin/brule',array('edit'=>$brule['ruleid']))."'>
              <span class='";

            switch($brule['type']){
                case 'GOLD': $navbrulehtml .= "badge1"; break;
                case 'SILVER': $navbrulehtml .= "badge2"; break;
                case 'BRONZE': $navbrulehtml .= "badge3"; break;
            };

            $navbrulehtml .= "'></span>".
                $brule['title']
                ."</a>
              </span>".
                '</td></tr>';

        }
        $navbrulehtml .=  '</tbody></table>';
        $qa_content['form']['field']['nav']=array(
            'label' => qa_lang_html('admin/brules'),
            'type'  => 'static',
            'value' => $navbrulehtml,
        );
    }



    $qa_content['form']['fields']['nav']=array(
        'label' => '<h3>Click below to Edit</h3>',
        'type'  => 'static',
        'value' => $navbrulehtml,
    );
}

//navigation still there
$qa_content['navigation']['sub']=qa_admin_sub_navigation();

return $qa_content;
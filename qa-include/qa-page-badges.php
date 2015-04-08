<?php

/*
	

	File: qa-include/qa-page-badges.php
	Version: See define()s at top of qa-include/qa-base.php
	Description: Controller for badges page
*/

	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../');
		exit;
	}

	require_once QA_INCLUDE_DIR.'qa-db-selects.php';
	require_once QA_INCLUDE_DIR.'qa-app-format.php';


//	Get popular tags
	
	$start=qa_get_start();
	
	$populartags=qa_db_select_with_pending(
		qa_db_popular_tags_selectspec($start, qa_opt_if_loaded('page_size_tags'))
	);
	
	$brules = qa_db_select_with_pending(qa_db_brule_nav_selectspec());	
	
//	Prepare content for theme

	$qa_content=qa_content_prepare();
	
	if (count($brules)) {
		$qa_content['title']='User Badges Help';
		
		// For gold badges 
		$navbrulehtml = '<h3>Gold Badges </h3>';
		$navbrulehtml .= '<table class="qa-badges-list-table"><tbody>';
		
		foreach($brules as $brule){
			
			if($brule['type'] == 'GOLD'){
				$navbrulehtml .= '<tr id="'.str_replace(' ','',$brule['title']).'"><td class="badge-cell-large">'.
						"<span class='badge-container'>
	              <a class='badge' title='".$brule['desc1']."' href=''>
	              <span class='";
				
				switch($brule['type']){
					case 'GOLD': $navbrulehtml .= "badge1"; break;
					case 'SILVER': $navbrulehtml .= "badge2"; break;
					case 'BRONZE': $navbrulehtml .= "badge3"; break;
				};
				
				$navbrulehtml .= "'></span>".
						$brule['title']
						.'</a>
	             		</span>
				        </td><td class="badge-description">'.$brule['desc1']."</td></tr>";
			}
		}
		$navbrulehtml .=  '</tbody></table>';
		
		$navbrulehtml .= '<br><br>';
		
		// For Silver badges
		$navbrulehtml .= '<h3>Silver Badges </h3>';
		$navbrulehtml .= '<table class="qa-badges-list-table"><tbody>';
		
		foreach($brules as $brule){
				
			if($brule['type'] == 'SILVER'){
				$navbrulehtml .= '<tr id="'.str_replace(' ','',$brule['title']).'"><td class="badge-cell-large">'.
						"<span class='badge-container'>
	              <a class='badge' title='".$brule['desc1']."' href=''>
	              <span class='";
		
				switch($brule['type']){
					case 'GOLD': $navbrulehtml .= "badge1"; break;
					case 'SILVER': $navbrulehtml .= "badge2"; break;
					case 'BRONZE': $navbrulehtml .= "badge3"; break;
				};
		
				$navbrulehtml .= "'></span>".
						$brule['title']
						.'</a>
	             		</span>
				        </td><td class="badge-description">'.$brule['desc1']."</td></tr>";
			}
		}
		$navbrulehtml .=  '</tbody></table>';
		
		$navbrulehtml .= '<br><br>';
		
		// For Bronze badges
		$navbrulehtml .= '<h3>Bronze Badges </h3>';
		$navbrulehtml .= '<table class="qa-badges-list-table"><tbody>';
		
		foreach($brules as $brule){
		
			if($brule['type'] == 'BRONZE'){
				$navbrulehtml .= '<tr id="'.str_replace(' ','',$brule['title']).'"><td class="badge-cell-large">'.
						"<span class='badge-container'>
	              <a class='badge' title='".$brule['desc1']."' href=''>
	              <span class='";
		
				switch($brule['type']){
					case 'GOLD': $navbrulehtml .= "badge1"; break;
					case 'SILVER': $navbrulehtml .= "badge2"; break;
					case 'BRONZE': $navbrulehtml .= "badge3"; break;
				};
		
				$navbrulehtml .= "'></span>".
						$brule['title']
						.'</a>
	             		</span>
				        </td><td class="badge-description">'.$brule['desc1']."</td></tr>";
			}
		}
		$navbrulehtml .=  '</tbody></table>';
		
		
		$qa_content['custome'] = $navbrulehtml;

	} else
		$qa_content['title']='No Badges Found';
		

	return $qa_content;


/*
	Omit PHP closing tag to help avoid accidental output
*/
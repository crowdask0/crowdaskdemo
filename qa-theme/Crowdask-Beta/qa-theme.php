<?php


	class qa_html_theme extends qa_html_theme_base
	{	

		function head_script() // change style of WYSIWYG editor to match theme better
		{
			qa_html_theme_base::head_script();
			
			$this->output(
				'<script type="text/javascript">',
				"if (typeof qa_wysiwyg_editor_config == 'object')",
				"\tqa_wysiwyg_editor_config.skin='kama';",
				'</script>'
			);
		}
		
		function nav_user_search() // outputs login form if user not logged in
		{			
			qa_html_theme_base::nav_user_search();
		}
		
		function logged_in() 
		{
			if (qa_is_logged_in()) // output user avatar to login bar
				$this->output(
					'<div class="qa-logged-in-avatar">',
					QA_FINAL_EXTERNAL_USERS
					? qa_get_external_avatar_html(qa_get_logged_in_userid(), 24, true)
					: qa_get_user_avatar_html(qa_get_logged_in_flags(), qa_get_logged_in_email(), qa_get_logged_in_handle(),
						qa_get_logged_in_user_field('avatarblobid'), qa_get_logged_in_user_field('avatarwidth'), qa_get_logged_in_user_field('avatarheight'),
						24, true),
            		'</div>'
            	);				
			
			qa_html_theme_base::logged_in();


			if (qa_is_logged_in()) { // adds points count after logged in username
				$userpoints=qa_get_logged_in_points();
				
				$pointshtml=($userpoints==1)
					? qa_lang_html_sub('main/1_point', '1', '1')
					: qa_lang_html_sub('main/x_points', qa_html(number_format($userpoints)));

                //modified by , only display points for < Moderator
                switch(qa_get_logged_in_level()){
                    case QA_USER_LEVEL_MODERATOR:
                        $pointshtml = "Moderator";
                        break;
                    case QA_USER_LEVEL_ADMIN:
                        $pointshtml = "Administrator";
                        break;
                    case QA_USER_LEVEL_SUPER;
                        $pointshtml = "Super Administrator";
                }

				$this->output(
					'<span class="qa-logged-in-points">',
					'('.$pointshtml.')',
					'</span>'
				);
			}

            //
            qa_html_theme_base::display_badge_summary();
		}
    
		function body_header() // adds login bar, user navigation and search at top of page in place of custom header content
		{
			
			$this->output('<div id="qa-login-bar"><div id="qa-login-group">');
			$this->nav_user_search();
			
            $this->output('</div></div>');
            
        }
		
		function header_custom() // allows modification of custom element shown inside header after logo
		{
			if (isset($this->content['body_header'])) {
				$this->output('<div class="header-banner">');
				$this->output_raw($this->content['body_header']);
				$this->output('</div>');
			}
		}
		
		function header() // removes user navigation and search from header and replaces with custom header content. Also opens new <div>s
		{	
			$this->output('<div class="qa-header">');
			
			$this->logo();	
			$this->nav_main_sub();
			
			//only display the demo part at home page			
			if(qa_request() == null) //home page
				$this->demo();
			
			$this->header_clear();
			$this->header_custom();

			$this->output('</div> <!-- END qa-header -->', '');

			$this->output('<div class="qa-main-shadow">', '');
			$this->output('<div class="qa-main-wrapper">', '');
			

		}
		
		function demo()
		{
			$this->output('<div id="qa-demo" class="qa-demo" style="width:100%;">');
			$this->demo_close_button();
			$this->demo_blurb();
			$this->output('</div>');
			//resize the demo section according to background image size
			$this->output('<script type="text/javascript">',
							'var demo_sec = document.getElementById("qa-demo");',
							'if(demo_sec)',
							'{',
							'var width = demo_sec.offsetWidth;',
							'var height = Math.round(1.0 * 941 / 4081 * width);',
							'demo_sec.setAttribute("style", "height:" + height +"px");',
							'} </script>');
		}
		
		function demo_close_button()
		{			
			$this->output('<script type="text/javascript">',
							'function close_demo()
							{
								var close_button = document.getElementById("demo_close");
								if(close_button != null){
									var demo_sec = document.getElementById("qa-demo");
									var demo_blurb = document.getElementById("qa-blurb");
									var demo_close = document.getElementById("demo_close");
									if(demo_sec)
										demo_sec.style.display = "none";
									if(demo_close)
										demo_close.style.display = "none";						
									if(demo_blurb)
										demo_blurb.style.display = "none";
									var width = demo_sec.offsetWidth;
									var height = Math.round(1.0 * 941 / 4081 * width);	
									demo_sec.setAttribute("style", "height:" + height +"px");
					
									var restore = document.getElementById("qa-nav-main-restore");
									restore.style.display = "";
								}

							}',
							'</script>'
			);
			
			$this->output('<div id="demo_close">');
			$this->output('<a onclick="close_demo();" title="click to minimize">');
			$this->output('_');
			$this->output('</a>');
			$this->output('</div>');
		}
		
		//the Text and Get Started section of demo part
		function demo_blurb()
		{
			$userlinks=qa_get_login_links(qa_path_to_root(),null);
			$this->output('<div id="qa-blurb" style="">
                CrowdAsk is a question and answer site for academics, librarians and students.<p>We are working together to share knowledge and answer questions.
			    <br><br> <br>
			    <a id="tell-me-more" class="demo-button" href="');
			
			if(!qa_is_logged_in())
				$this->output(qa_html(@$userlinks['register']));
			else 
				$this->output(qa_path_html('ask',null));
			
			$this->output('">');

			if(!qa_is_logged_in())
				$this->output('Get Started');
			else 
				$this->output('Ask A Question');
			$this->output('</a>
				</div>');
			
		}
		
		function body() /* overwrite body function in theme-base */
		{
			$this->output('<body');
			$this->body_tags();
			$this->output('>');
				
			// facebook sdk loading
			$this->output('<div id="fb-root"></div>
					<script>(function(d, s, id) {
					  var js, fjs = d.getElementsByTagName(s)[0];
					  if (d.getElementById(id)) return;
					  js = d.createElement(s); js.id = id;
					  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=564025277012065";
					  fjs.parentNode.insertBefore(js, fjs);
					}(document, "script", "facebook-jssdk"));
					</script>');
				
				
			$this->body_script();
			$this->body_header();
			$this->body_content();
			$this->body_footer();
			$this->body_hidden();
			
			$this->output('</body>');
		}
		
		function sidepanel() // removes sidebar for user profile pages
		{
			if ($this->template!='user')
				qa_html_theme_base::sidepanel();
		}
		
		function footer() // prevent display of regular footer content (see body_suffix()) and replace with closing new <div>s
		{
			$this->output('</div> <!-- END main-wrapper -->');
			$this->output('</div> <!-- END main-shadow -->');	
	
		}		
		
		function title() // add RSS feed icon after the page title
		{
			qa_html_theme_base::title();
			
			//
			//remove RSS feed after the page title
			/*
			$feed=@$this->content['feed'];
			
			if (!empty($feed))
				$this->output('<a href="'.$feed['url'].'" title="'.@$feed['label'].'"><img src="'.$this->rooturl.'images/feed-icon-14x14.png" alt="" width="16" height="16" border="0" class="qa-rss-icon"/></a>');
			*/
		}
		
		function q_item_stats($q_item) // add view count to question list
		{
			$this->output('<div class="qa-q-item-stats">');
			
			$this->voting($q_item, true);
			$this->a_count($q_item);
			qa_html_theme_base::view_count($q_item);

			$this->output('</div>');
		}
		
		function view_count($q_item) // prevent display of view count in the usual place
		{	
			if ($this->template=='question')
				qa_html_theme_base::view_count($q_item);
		}
		
		function body_suffix() // to replace standard Q2A footer
        {
			$this->output('<div class="qa-footer-bottom-group">');
			qa_html_theme_base::footer();
			$this->output('</div> <!-- END footer-bottom-group -->', '');
        }
		
		function attribution()
		{			


			qa_html_theme_base::attribution();
			
			//Add our own attributes
			//TODO
		}
		
		function body_script()// to replace standard Q2A body scripts
		{
			qa_html_theme_base::body_script();
		}
		
	}


/*
	Omit PHP closing tag to help avoid accidental output
*/
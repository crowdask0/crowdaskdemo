(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

/* Add your Google Analytics ID here
*/
ga('create', 'UA-XXXXXX-X', 'mydomain.com');
ga('send', 'pageview');

function addListener(element, type, callback) {
	if (element.addEventListener) element.addEventListener(type, callback);
	else if (element.attachEvent) element.attachEvent('on' + type, callback);
}


function addEventAction()
{
	const debug = false;

	//Events of navigation links
	var nav_links = document.getElementsByClassName('qa-nav-main-link');

	for(var i = 0; i < nav_links.length; i++){
		var link_href = nav_links[i].href;
		
		if(link_href.indexOf('qa=questions') != -1){
			addListener(nav_links[i], 'click', function() {
				ga('send', 'event', 'nav_main_links', 'click', 'nav-questions');
				if(debug) alert("questions");
			});
		}else if(link_href.indexOf('qa=unanswered') != -1){
			addListener(nav_links[i], 'click', function() {
				ga('send', 'event', 'nav_main_links', 'click', 'nav-unanswered');
				if(debug) alert("unanswered");
			});
		}else if(link_href.indexOf('qa=tags') != -1){
			addListener(nav_links[i], 'click', function() {
				ga('send', 'event', 'nav_main_links', 'click', 'nav-tags');
				if(debug) alert("tags");
			});	
		}else if(link_href.indexOf('qa=users') != -1){
			addListener(nav_links[i], 'click', function() {
				ga('send', 'event', 'nav_main_links', 'click', 'nav-users');
				if(debug) alert("users");
			});	
		}else if(link_href.indexOf('qa=ask') != -1){
			addListener(nav_links[i], 'click', function() {
				ga('send', 'event', 'nav_main_links', 'click', 'nav-ask');
				if(debug) alert("ask");
			});	
		}else if(link_href.indexOf('qa=about') != -1){
			addListener(nav_links[i], 'click', function() {
				ga('send', 'event', 'nav_main_links', 'click', 'nav-about');
				if(debug) alert("about");
			});								
		}else if(link_href.indexOf('wiki') != -1){
			addListener(nav_links[i], 'click', function() {
				ga('send', 'event', 'nav_main_links', 'click', 'nav-wiki');
				if(debug) alert("help");
			});	
		}
	}
	
	//get started button in demo
	//ask question button in demo	
	var demo_button = document.getElementById('tell-me-more');
	if(demo_button != null){
		if(demo_button.href.indexOf('qa=register') != -1)
			addListener(demo_button, 'click', function() {
				ga('send', 'event', 'demo_buttons', 'click', 'demo-start');
				if(debug) alert("start");
			});
		else if(demo_button.href.indexOf('qa=ask') != -1)
			addListener(demo_button, 'click', function() {
				ga('send', 'event', 'demo_buttons', 'click', 'demo-ask');
				if(debug) alert("ask");
			});
	}
		
	
	//expand links in home page
	var suggest_expand = document.getElementsByClassName('qa-suggest-next')[0];
	if(suggest_expand != null){
		var children = suggest_expand.children;
		
		for(var i = 0; i < children.length; i++){
			var link = children[i];
			if(link.href.indexOf('qa=questions') != -1)
				addListener(link, 'click', function() {
					ga('send', 'event', 'expand_links', 'click', 'expand-questions');
					if(debug) alert("expand-questions");
				});
			else if(link.href.indexOf('qa=tags') != -1)
				addListener(link, 'click', function() {
					ga('send', 'event', 'expand_links', 'click', 'expand-tags');
					if(debug) alert("expand-tags");
				});
		}
	}
	
	//sub-navigation links for questions
	var sub_nav_links = document.getElementsByClassName('qa-nav-sub-link');
	
	for(var i = 0; i < sub_nav_links.length; i++){
		var link_href = sub_nav_links[i].href;
		
		if(link_href.indexOf('qa=questions') != -1 && i == 0){
			sub_str = "qa=questions";
			
			if((link_href.indexOf('qa=questions') + sub_str.length) == link_href.length)
				addListener(sub_nav_links[i], 'click', function() {
					ga('send', 'event', 'nav_sub_links', 'click', 'sub_nav-recent');
					if(debug) alert("recent");
				});

		}else if(link_href.indexOf('sort=bounty') != -1){
			addListener(sub_nav_links[i], 'click', function() {
				ga('send', 'event', 'nav_sub_links', 'click', 'sub_nav-featured');
				if(debug) alert("featured");
			});
		}else if(link_href.indexOf('sort=hot') != -1){
			addListener(sub_nav_links[i], 'click', function() {
				ga('send', 'event', 'nav_sub_links', 'click', 'sub_nav-hot');
				if(debug) alert("hot");
			});
		}else if(link_href.indexOf('sort=votes') != -1){
			addListener(sub_nav_links[i], 'click', function() {
				ga('send', 'event', 'nav_sub_links', 'click', 'sub_nav-votes');
				if(debug) alert("votes");
			});
		}else if(link_href.indexOf('sort=answers') != -1){
			addListener(sub_nav_links[i], 'click', function() {
				ga('send', 'event', 'nav_sub_links', 'click', 'sub_nav-answers');
				if(debug) alert("answers");
			});
		}else if(link_href.indexOf('sort=views') != -1){
			addListener(sub_nav_links[i], 'click', function() {
				ga('send', 'event', 'nav_sub_links', 'click', 'sub_nav-views');
				if(debug) alert("views");
			});
		}
	}
	
	//tags links
	var tag_links = document.getElementsByClassName('qa-tag-link');
	
	for(i = 0; i < tag_links.length; i++){
		tag_name = tag_links[i].innerHTML;
		
		(function(_i,_tag_name){
		addListener(tag_links[_i],'click',function(){
			ga('send', 'event', 'tag_links', 'click', _tag_name);
			if(debug) alert(i+": "+_tag_name);
		});
		})(i,tag_name);
	}
	
	//categories links
	var cat_links = document.getElementsByClassName('qa-nav-cat-link');
	
	for(i = 0; i<cat_links.length;i++){
		cat_name = cat_links[i].innerHTML;
		
		(function(_i,_cat_name){
			addListener(cat_links[_i],'click',function(){
				ga('send', 'event', 'cat_links', 'click', _cat_name);
				if(debug) alert(i+": "+_cat_name);
			});
		})(i,cat_name);
	}
	
}
					
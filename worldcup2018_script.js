
jQuery(document).ready(function($){
	//when refresh button is clicked by user
	jQuery("#refresh_wc_news").live('click',function(){
		
		jQuery(this).empty().append('<span style="color:green !important;"> Loading .....</span>');
		  
		
		  var data = {
					'action': 'update_worldcup_news_ajax',
					'refresh': 'true'      
				};
		  
		// We can also pass the url value separately from ajaxurl for front end AJAX implementations
			jQuery.post(my_ajax_url, data, function(response) {
				
				jQuery("#word_cup_news_feed").empty().append(response);
			});
		
		
	});
	

})
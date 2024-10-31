/**
 * Wrapper function to safely use $
 */
jQuery(document).ready(function($) {
	var data = {		
		'apikey': idx_mailchimp_ajax_object.apikey,		
		'list_id': idx_mailchimp_ajax_object.list_id,		
	};
	// We can also pass the url value separately from ajaxurl for front end AJAX implementations
	jQuery.post(idx_mailchimp_ajax_object.ajaxurl, data, function(response) {
		
		$("#sync-image").hide();		
	});
});

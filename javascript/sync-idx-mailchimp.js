/**
 * Wrapper function to safely use $
 */
jQuery(document).ready(function($) {
	var entries = {"iDisplayLength": 500};
	//Start plugin data table
    $('#table-idx-leads').dataTable(entries);
    var tablemembers = $('#table-members').dataTable(entries);
	function check_color() {
		var emails_idx = jQuery("#table-idx-leads [data-email-idx]");
		var emails_mailchimp = jQuery("#table-members [data-email-mailchimp]");
		$.each(emails_mailchimp, function(i, item) {			
			$.each(emails_idx ,function(index, email) {
				if ($(item).data('email-mailchimp') == $(email).data('email-idx')) {
					$(email).parent().css("background-color","lightgreen");								
				}
			});
			
	    });
	}
	//Drop Box Mailchimp get members
	$("#list-subs").change(function(){
		var data = {		
			'apikey': sync_ajax_object.apikey,		
			'getSubscribers': 'true',		
			'list_id': $(this).val(),		
		};
		
		if ($(this).val() != 'empty') {
			//Remove previus tables
			tablemembers.fnDestroy();

			//Show load Icon 
			$("#load-members").show();	
			jQuery.post(sync_ajax_object.ajaxurl, data, function(response) {
							        
			    var emails_idx = jQuery("#table-idx-leads [data-email-idx]");
			    jQuery("#table-idx-leads tbody tr").css('background-color', 'lightpink');
			    var content = "";

			   console.log("forweach");
				$.each(response, function(i, item) {
					$.each(emails_idx ,function(index, email) {									
						if (item.email_address == $(email).data('email-idx')) {	
							//$(email).parent().stop().animate({ backgroundColor: "lightgreen" },500);
							$(email).parent().css("background-color","lightgreen");								
						}
					});
					content += "<tr><td data-email-mailchimp='"+item.email_address+"'>"+item.email_address+"</td><td>"+item.merge_fields.FNAME+"</td><td>"+item.merge_fields.LNAME+"</td></tr>";
			    });	
				$("#result-members-mailchimp").html(content);	
				$("#load-members").hide();				
				//Refresh data table
         		tablemembers = $('#table-members').dataTable(entries);
	         

			});
		}
	    
	});
	//Send Data Idx to Mailchimp
	$('#sub-idx-to-mailc').click(function(event) {
		
		var leads = jQuery('#table-idx-leads').find('input[type="checkbox"]:checked');
		var data = [];
		if (leads != '') {
			if ($("#list-subs").val() != 'empty') {
				$( this ).append('<span id="glyphicon-sync-ajax" class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span>');
				$(leads).each(function(index, row) {						
						var member = {id:$(row).parent().nextAll().andSelf().eq(0).data('id-idx'), firstName:$(row).parent().nextAll().andSelf().eq(1).data('firstname-idx'),lastName:$(row).parent().nextAll().andSelf().eq(2).data('lastname-idx'),email:$(row).parent().nextAll().andSelf().eq(3).data('email-idx')};
						data.push(member);
				});
				
				$.ajax({
					url: sync_ajax_object.ajaxurl,
					type: 'POST',				
					data: {leads_checkbox: data, checkbox_lead: 'true', list_id: $("#list-subs").val(), apikey : sync_ajax_object.apikey},
				})
				.done(function(response) {
					tablemembers.fnDestroy();
					$('#glyphicon-sync-ajax').remove();
					$('#table-members tr:last').after(response);
					tablemembers.fnDraw();
					tablemembers = $('#table-members').dataTable(entries);
					check_color();
					$('input:checkbox').removeAttr('checked')
				})
				.fail(function() {
					console.log("error");
				})
				.always(function() {
					
				});
			}else{

			}
		}
		event.preventDefault();
	});
	//Check all leads
	$('#checkall:checkbox').change(function () {
	   if($(this).attr("checked")) $('input:checkbox').attr('checked','checked');
	   else $('input:checkbox').removeAttr('checked');
	});

	
	
});

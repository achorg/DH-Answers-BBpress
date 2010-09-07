
jQuery(document).ready(function() {
	
	jQuery(".thanks-vote").click(function() {
		var post_id = jQuery(this).attr("id");
		var user_id = jQuery(this).attr("user");
		jQuery.post(ajaxThanksUrl, { 'post_id': post_id, 'user_id': user_id  }, function(data) {
			html(post_id, data);
		}, "text"); // Ajax Post		
	});
	
});

function html(component, content) {
	jQuery("#thanks-"+component).html(content);
}

function change_class(component, clss) {
	jQuery("#thanks-"+component).removeClass();
	jQuery("#thanks-"+component).addClass(clss);
}

function enable(components) {
	for (var i=0; i<components.length; i++) {
		jQuery("#thanks-"+components[i]).removeAttr('disabled');
	}
}

function disable(components) {
	for (var i=0; i<components.length; i++) {
		jQuery("#thanks-"+components[i]).attr('disabled','disabled');
	}
}
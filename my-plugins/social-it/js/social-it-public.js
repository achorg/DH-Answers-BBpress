/*
 Public Javascript File for
 Social It plugin (for bbPress) by www.gaut.am
*/

jQuery(document).ready(function() {

	// xhtml 1.0 strict way of using target _blank
	jQuery('.social-it a.external').attr("target", "_blank");

	// this block sets the auto vertical expand when there are more than 
	// one row of bookmarks.
	var socialitBaseHeight=jQuery('.social-it').height();
	var socialitFullHeight=jQuery('.social-it ul.socials').height();
	if (socialitFullHeight>socialitBaseHeight) {
		jQuery('.social-it-expand').hover(
			function() {
				jQuery(this).animate({
						height: socialitFullHeight+'px'
				}, {duration: 400, queue: false});
			},
			function() {
				jQuery(this).animate({
						height: socialitBaseHeight+'px'
				}, {duration: 400, queue: false});
			}
		);
	}
	// autocentering
	if (jQuery('.social-it-center') || jQuery('.social-it-spaced')) {
		var socialitFullWidth=jQuery('.social-it').width();
		var socialitBookmarkWidth=jQuery('.social-it:first ul.socials li').width();
		var socialitBookmarkCount=jQuery('.social-it:first ul.socials li').length;
		var numPerRow=Math.floor(socialitFullWidth/socialitBookmarkWidth);
		var socialitRowWidth=Math.min(numPerRow, socialitBookmarkCount)*socialitBookmarkWidth;
		if (jQuery('.social-it-spaced').length>0) { 
			var socialitLeftMargin=Math.floor((socialitFullWidth-socialitRowWidth)/(Math.min(numPerRow, socialitBookmarkCount)+1)); 
			jQuery('.social-it ul.socials li').css('margin-left', socialitLeftMargin+'px'); 
		} else if (jQuery('.social-it-center'.length>0)) { 
			var socialitLeftMargin=(socialitFullWidth-socialitRowWidth)/2; 
			jQuery('.social-it-center').css('margin-left', socialitLeftMargin+'px'); 
		}
	}
});
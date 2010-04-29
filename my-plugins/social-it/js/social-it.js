/*
 Javascript file for Admin Settings Page for
 Social It plugin (for bbPress) by www.gaut.am
*/

jQuery(document).ready(function() {
	if (jQuery('#iconator')) jQuery('#socialit-networks').sortable({ 
	delay:        250,
	cursor:      'move',
	scroll:       true,
	revert:       true, 
	opacity:      0.7
});
	if (jQuery('.social-it')) { jQuery('#socialit-sortables').sortable({ 
	handle:      '.box-mid-head',
	delay:        250,
	cursor:      'move',
	scroll:       true,
	revert:       true, 
	opacity:      0.7
});
	
//Select all icons upon clicking 
jQuery('#sel-all').click(function() { 
      jQuery('#socialit-networks').each(function() { 
	    jQuery('#socialit-networks input').attr('checked', 'checked'); 
      }); 
}); 

//Deselect all icons upon clicking 
jQuery('#sel-none').click(function() { 
      jQuery('#socialit-networks').each(function() { 
	    jQuery('#socialit-networks input').removeAttr('checked'); 
      }); 
}); 

//Select most popular icons upon clicking 
jQuery('#sel-pop').click(function() { 
      jQuery('#socialit-networks').each(function() {
	    jQuery('#socialit-networks input').removeAttr('checked'); 
	    jQuery('#socialit-digg').attr('checked', 'checked'); 
	    jQuery('#socialit-reddit').attr('checked', 'checked'); 
	    jQuery('#socialit-delicious').attr('checked', 'checked'); 
	    jQuery('#socialit-stumbleupon').attr('checked', 'checked'); 
	    jQuery('#socialit-mixx').attr('checked', 'checked'); 
	    jQuery('#socialit-comfeed').attr('checked', 'checked'); 
	    jQuery('#socialit-twitter').attr('checked', 'checked'); 
	    jQuery('#socialit-technorati').attr('checked', 'checked'); 
	    jQuery('#socialit-misterwong').attr('checked', 'checked'); 
	    jQuery('#socialit-diigo').attr('checked', 'checked');
	    jQuery('#socialit-orkut').attr('checked', 'checked');
	    jQuery('#socialit-facebook').attr('checked', 'checked'); 
      }); 
});

// textbox prompt
if(jQuery("#defaulttags").value == ""){
       jQuery(this).addClass("defaulttags-label").val("enter,default,tags,here");
};
jQuery("#defaulttags").focus(function(){
    if(this.value == "enter,default,tags,here" )
    {
       jQuery(this).removeClass("defaulttags-label").val("");
    };
 });
jQuery("#defaulttags").blur(function(){
    if(this.value == "")
    {
       jQuery(this).addClass("defaulttags-label").val("enter,default,tags,here");
    };
 });
jQuery('#social-it').submit(function(){
    if(jQuery("#defaulttags").val() == "enter,default,tags,here")
    {
       jQuery("#defaulttags").removeClass("defaulttags-label").val("");
    };
 });

// if checkbox isn't already checked, open warning message... 
jQuery("#custom-mods").click(function() { 
      if(jQuery(this).is(":not(:checked)")) { 
	    jQuery("#custom-mods-notice").css("display", "none"); 
      } 
      else { 
	    jQuery("#custom-mods-notice").fadeIn("fast"); 
	    jQuery("#custom-mods-notice").css("display", "table"); 
      } 
}); 

// close custom mods warning when they click the X 
jQuery(".custom-mods-notice-close").click(function() { 
      jQuery("#custom-mods-notice").fadeOut('fast'); 
});

/* import/export start */
jQuery('#import-warning').css({ display:'none' });
if (jQuery('#import-warn-yes').is(':checked')){
	this.checked=jQuery('#import-warning').fadeOut();
	this.checked=jQuery(this).is(':not(:checked)');
}
if (jQuery('#import-warn-cancel').is(':checked')){
	this.checked=jQuery('#import-warning').fadeOut();
	this.checked=jQuery(this).is(':not(:checked)');
}

jQuery('#import-submit').click(function() {
	this.checked=jQuery('#import-warning').fadeIn('fast');
});

jQuery('#import-short-urls-warning').css({ display:'none' });
jQuery('#import_short_urls').click(function() {
	if (jQuery('#import_short_urls').is(':checked')) {
		this.checked=jQuery('#import-short-urls-warning').fadeIn('fast');
	}else{
		this.checked=jQuery(this).is(':not(:checked)');
	}
	this.checked=jQuery(this).is(':not(:checked)');
});

jQuery('#import-short-urls-warn-no').click(function() {
	this.checked=jQuery('#import-short-urls-warning').fadeOut();
	this.checked=jQuery(this).is(':not(:checked)');
});

jQuery('#import-short-urls-warn-yes').click(function() {
	this.checked=jQuery('#import-short-urls-warning').fadeOut();
	this.checked=jQuery('#import_short_urls').attr('checked', 'checked');
	this.checked=jQuery(this).is(':not(:checked)');
});

jQuery('#export-short-urls-warning').css({ display:'none' });
jQuery('#export_short_urls').click(function() {
	if (jQuery('#export_short_urls').is(':checked')) {
		this.checked=jQuery('#export-short-urls-warning').fadeIn('fast');
	}else{
		this.checked=jQuery(this).is(':not(:checked)');
	}
	this.checked=jQuery(this).is(':not(:checked)');
});

jQuery('#export-short-urls-warn-no').click(function() {
	this.checked=jQuery('#export-short-urls-warning').fadeOut();
	this.checked=jQuery(this).is(':not(:checked)');
});

jQuery('#export-short-urls-warn-yes').click(function() {
	this.checked=jQuery('#export-short-urls-warning').fadeOut();
	this.checked=jQuery('#export_short_urls').attr('checked', 'checked');
	this.checked=jQuery(this).is(':not(:checked)');
});
/* import/export end */

/* short urls start */

jQuery('#clear-warning').css({ display:'none' });
jQuery('div#clearurl img.del-x').click(function() {
  jQuery('div#clearurl').fadeOut();
});

jQuery('#clearShortUrls').click(function() {
	if (jQuery('#clearShortUrls').is(':checked')) {
		this.checked=jQuery('#clear-warning').fadeIn('fast');
	}else{
		this.checked=jQuery(this).is(':not(:checked)');
	}
	this.checked=jQuery(this).is(':not(:checked)');
});

jQuery('#warn-cancel').click(function() {
	this.checked=jQuery('#clear-warning').fadeOut();
	this.checked=jQuery(this).is(':not(:checked)');
});

jQuery('#warn-yes').click(function() {
	this.checked=jQuery('#clear-warning').fadeOut();
	this.checked=jQuery('#clearShortUrls').attr('checked', 'checked');
	this.checked=jQuery(this).is(':not(:checked)');
});

jQuery('#shorty').change(function() {
	this.checked=jQuery('#shortyapimdiv-bitly').fadeOut('fast');
	this.checked=jQuery('#shortyapimdiv-trim').fadeOut('fast');
	this.checked=jQuery('#shortyapimdiv-snip').fadeOut('fast');
	this.checked=jQuery('#shortyapimdiv-tinyarrow').fadeOut('fast');
	this.checked=jQuery('#shortyapimdiv-cligs').fadeOut('fast');
	this.checked=jQuery('#shortyapimdiv-supr').fadeOut('fast');
	if(this.value=='trim'){
		jQuery('#shortyapimdiv-trim').fadeIn('fast');
	}
	else if(this.value=='bitly'){
		jQuery('#shortyapimdiv-bitly').fadeIn('fast');
	}
	else if(this.value=='snip'){
		jQuery('#shortyapimdiv-snip').fadeIn('fast');
	}
	else if(this.value=='tinyarrow'){
		jQuery('#shortyapimdiv-tinyarrow').fadeIn('fast');
	}
	else if(this.value=='cligs'){
		jQuery('#shortyapimdiv-cligs').fadeIn('fast');
	}
	else if(this.value=='supr'){
		jQuery('#shortyapimdiv-supr').fadeIn('fast');
	}
});

jQuery('#shortyapichk-trim').click(function() {
	if (jQuery(this).attr('checked')) {
		this.checked=jQuery('#shortyapidiv-trim').fadeIn('fast');
	}
	else {
		jQuery('#shortyapidiv-trim').fadeOut('fast');
	}
});

jQuery('#shortyapichk-tinyarrow').click(function() {
	if (jQuery(this).attr('checked')) {
		this.checked=jQuery('#shortyapidiv-tinyarrow').fadeIn('fast');
	}
	else {
		jQuery('#shortyapidiv-tinyarrow').fadeOut('fast');
	}
});

jQuery('#shortyapichk-cligs').click(function() {
	if (jQuery(this).attr('checked')) {
		this.checked=jQuery('#shortyapidiv-cligs').fadeIn('fast');
	}
	else {
		jQuery('#shortyapidiv-cligs').fadeOut('fast');
	}
});

jQuery('#shortyapichk-supr').click(function() {
	if (jQuery(this).attr('checked')) {
		this.checked=jQuery('#shortyapidiv-supr').fadeIn('fast');
	}
	else {
		jQuery('#shortyapidiv-supr').fadeOut('fast');
	}
});

/* short urls end */

jQuery('#autocenter-space').click(function() {
	jQuery('#xtrastyle').attr('disabled', true);
	jQuery('#xtrastyle').val('Custom CSS has been disabled because you are using either the "Auto Space" or "Auto Center" option above.');
});

jQuery('#autocenter-center').click(function() {
	jQuery('#xtrastyle').attr('disabled', true);
	jQuery('#xtrastyle').val('Custom CSS has been disabled because you are using either the "Auto Space" or "Auto Center" option above.');
});

jQuery('#autocenter-no').click(function() {
	jQuery('#xtrastyle').removeAttr('disabled');
	jQuery('#xtrastyle').val('margin:20px 0 0 0 !important;\npadding:25px 0 0 10px !important;\nheight:29px;/*the height of the icons (29px)*/\ndisplay:block !important;\nclear:both !important;');
});

jQuery('.toggle').click(function(){
	var id = jQuery(this).attr('id');
	jQuery('#tog'+ id).slideToggle('slow');

	if (jQuery('#'+ id + ' img.close').is(':hidden')){
		jQuery('#'+ id +' img.close').show();
		jQuery('#'+ id +' img.open').fadeOut();
	} else {
		jQuery('#'+ id + ' img.open').show();
		jQuery('#'+ id + ' img.close').fadeOut();
	}
});

jQuery('#bgimg-yes').click(function() {
  jQuery('#bgimgs').toggleClass('hidden').toggleClass('');
});

// Apply "smart options" to Yahoo! Buzz
if (jQuery('#socialit-yahoobuzz').is(':checked')) {
	jQuery('#ybuzz-defaults').is(':visible');
}
else if (jQuery('#socialit-yahoobuzz').is(':not(:checked)')) {
	jQuery('#ybuzz-defaults').is(':hidden');
}
jQuery('#socialit-yahoobuzz').click(function() {
	if (jQuery(this).attr('checked')) {
		this.checked=jQuery('#ybuzz-defaults').fadeIn('fast');
	}
	else {
		jQuery('#ybuzz-defaults').fadeOut();
	}
});

// Apply "smart options" to Twittley
if (jQuery('#socialit-twittley').is(':checked')) {
	jQuery('#twittley-defaults').is(':visible');
}
else if (jQuery('#socialit-twittley').is(':not(:checked)')) {
	jQuery('#twittley-defaults').is(':hidden');
}
jQuery('#socialit-twittley').click(function() {
	if (jQuery(this).attr('checked')) {
		this.checked=jQuery('#twittley-defaults').fadeIn('fast');
	}
	else {
		jQuery('#twittley-defaults').fadeOut();
	}
});

// Apply "smart options" to Twitter
if (jQuery('#socialit-twitter').is(':checked')) {
	jQuery('#twitter-defaults').is(':visible');
}
else if (jQuery('#socialit-twitter').is(':not(:checked)')) {
	jQuery('#twitter-defaults').is(':hidden');
}
jQuery('#socialit-twitter').click(function() {
	if (jQuery(this).attr('checked')) {
		this.checked=jQuery('#twitter-defaults').fadeIn('fast');
	}
	else {
		jQuery('#twitter-defaults').fadeOut();
	}
});

jQuery('.dtags-info').click(function() {
	jQuery('#tag-info').fadeIn('fast');
});

jQuery('.dtags-close').click(function() {
	jQuery('#tag-info').fadeOut();
});

jQuery('.sfp-info').click(function() {
	jQuery('#sfi-info').fadeIn('fast');
});

jQuery('.sfi-close').click(function() {
	jQuery('#sfi-info').fadeOut();
});

jQuery('#yourversion .del-x').click(function() {
	jQuery('#yourversion').fadeOut();
});

jQuery('div#message img.del-x').click(function() {
	jQuery('div#message').fadeOut();
});

}});
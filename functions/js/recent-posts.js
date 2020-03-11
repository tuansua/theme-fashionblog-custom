jQuery(document).ready(function($) {	

	$('.arp_show-expert-options').live('change', function(){
		if( $(this).is(':checked') ) {
			$(this).parent().parent().find('.arp_expert-panel').show();
		} else {
			$(this).parent().parent().find('.arp_expert-panel').hide();
		}
	});
	
	$('.arp_show-expert-options').trigger('change');
});
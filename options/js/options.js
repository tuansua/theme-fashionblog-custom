jQuery(document).ready(function(){
	
	if(jQuery('#last_tab').val() == ''){

		jQuery('.nhp-opts-group-tab:first').slideDown('fast');
		jQuery('#nhp-opts-group-menu li:first').addClass('active');
	
	}else{
		
		tabid = jQuery('#last_tab').val();
		jQuery('#'+tabid+'_section_group').slideDown('fast');
		jQuery('#'+tabid+'_section_group_li').addClass('active');
		
	}
	
	
	
	jQuery('.nhp-opts-group-tab-link-a').click(function(){
		relid = jQuery(this).attr('data-rel');
		
		jQuery('#last_tab').val(relid);
		
		jQuery('.nhp-opts-group-tab').each(function(){
			if(jQuery(this).attr('id') == relid+'_section_group'){
				jQuery(this).delay(140).fadeIn(400);
			}else{
				jQuery(this).fadeOut('fast');
			}
			
		});
		
		jQuery('.nhp-opts-group-tab-link-li').each(function(){
				if(jQuery(this).attr('id') != relid+'_section_group_li' && jQuery(this).hasClass('active')){
					jQuery(this).removeClass('active');
				}
				if(jQuery(this).attr('id') == relid+'_section_group_li'){
					jQuery(this).addClass('active');
				}
		});
	});
	
	
	
	
	if(jQuery('#nhp-opts-save').is(':visible')){
		jQuery('#nhp-opts-save').delay(4000).slideUp('slow');
	}
	
	if(jQuery('#nhp-opts-imported').is(':visible')){
		jQuery('#nhp-opts-imported').delay(4000).slideUp('slow');
	}	
	
	jQuery('input, textarea, select').change(function(){
		jQuery('#nhp-opts-save-warn').slideDown('slow');
	});
	
	
	jQuery('#nhp-opts-import-code-button').click(function(){
		if(jQuery('#nhp-opts-import-link-wrapper').is(':visible')){
			jQuery('#nhp-opts-import-link-wrapper').fadeOut('fast');
			jQuery('#import-link-value').val('');
		}
		jQuery('#nhp-opts-import-code-wrapper').fadeIn('slow');
	});
	
	jQuery('#nhp-opts-import-link-button').click(function(){
		if(jQuery('#nhp-opts-import-code-wrapper').is(':visible')){
			jQuery('#nhp-opts-import-code-wrapper').fadeOut('fast');
			jQuery('#import-code-value').val('');
		}
		jQuery('#nhp-opts-import-link-wrapper').fadeIn('slow');
	});
	
	
	
	
	jQuery('#nhp-opts-export-code-copy').click(function(){
		if(jQuery('#nhp-opts-export-link-value').is(':visible')){jQuery('#nhp-opts-export-link-value').fadeOut('slow');}
		jQuery('#nhp-opts-export-code').toggle('fade');
	});
	
	jQuery('#nhp-opts-export-link').click(function(){
		if(jQuery('#nhp-opts-export-code').is(':visible')){jQuery('#nhp-opts-export-code').fadeOut('slow');}
		jQuery('#nhp-opts-export-link-value').toggle('fade');
	});
	
	

	
	
	// Presets
	function scrollImportLogToBottom(){
		var element = document.getElementById("importing-modal-content");
		element.scrollTop = element.scrollHeight;
	}
	jQuery('#presets .preset .import-demo-button, #presets .preset .import-demo-widgets-button, #presets .preset .import-demo-options-button').on('click', function(e) {
		e.preventDefault();

		var $this = jQuery(this);
		var $parent = $this.closest('.preset');
		var confirmText = nhpopts.import_opt_confirm;
		if ( $this.hasClass('import-demo-button') ) {
			confirmText = nhpopts.import_all_confirm;
		}
		if ( $this.hasClass('import-demo-widgets-button') ) {
			confirmText = nhpopts.import_widget_confirm;
		}

		var result = confirm( confirmText );
		if ( result ) {

			var data = {};
			data.action = "mts_install_demo";
			data.demo_import_id = $parent.attr("data-demo-id");
			data.nonce = $parent.attr("data-nonce");
			data.demo_import_options = '1';
			data.demo_import_content = '0';
			data.demo_import_widgets = '0';

			if ( $this.hasClass('import-demo-button') ) {
				data.demo_import_content = '1';
				data.demo_import_widgets = '1';
			}

			if ( $this.hasClass('import-demo-widgets-button') ) {
				data.demo_import_widgets = '1';
			}

			$this.magnificPopup({
				items: {
					src: '#importing-overlay',
					type: 'inline'
				},
				modal: true
			}).magnificPopup('open');

			var last_response_len = false;
			jQuery.ajax( ajaxurl, {
				data: data,
				xhrFields: {
					onprogress: function(e) {

						var this_response, response = e.currentTarget.response;
						if(last_response_len === false) {

							this_response = response;
							last_response_len = response.length;

						} else {

							this_response = response.substring(last_response_len);
							last_response_len = response.length;
						}

						jQuery('#importing-modal-content').append(this_response);
						scrollImportLogToBottom();
					}
				}
			})
			.done(function(data) {
				jQuery('#importing-modal-header h2').text(nhpopts.import_done);
				jQuery('#importing-modal-footer-info').text(nhpopts.import_done);
				jQuery('#importing-modal-footer-button').show();
			})
			.fail(function(data) {
				jQuery('#importing-modal-header h2').text(nhpopts.import_fail);
				jQuery('#importing-modal-footer-info').text(nhpopts.import_fail);
				jQuery('#importing-modal-footer-button').show();
			});
		}

		return false;
	});

	jQuery('.remove-demo-button').on('click', function(e) {
		e.preventDefault();

		var result = confirm( nhpopts.remove_all_confirm );
		if ( result ) {

			var $this = jQuery(this);
			var data = {};
			data.action = "mts_install_demo";
			data.mts_remove_demos = "1";
			data.nonce = $this.attr("data-nonce");

			$this.magnificPopup({
				items: {
					src: '#importing-overlay',
					type: 'inline'
				},
				modal: true
			}).magnificPopup('open');

			var last_response_len = false;
			jQuery.ajax( ajaxurl, {
				data: data,
				xhrFields: {
					onprogress: function(e) {

						var this_response, response = e.currentTarget.response;
						if(last_response_len === false) {

							this_response = response;
							last_response_len = response.length;

						} else {

							this_response = response.substring(last_response_len);
							last_response_len = response.length;
						}

						jQuery('#importing-modal-content').append(this_response);
						scrollImportLogToBottom();
					}
				}
			})
			.done(function(data) {
				jQuery('#importing-modal-header h2').text(nhpopts.remove_done);
				jQuery('#importing-modal-footer-info').text(nhpopts.remove_done);
				jQuery('#importing-modal-footer-button').show();
			})
			.fail(function(data) {
				jQuery('#importing-modal-header h2').text(nhpopts.remove_fail);
				jQuery('#importing-modal-footer-info').text(nhpopts.remove_fail);
				jQuery('#importing-modal-footer-button').show();
			});
		}

		return false;
	});

	function mtsRemoveURLParameter(url, parameter) {

		//prefer to use l.search if you have a location/link object
		var urlparts= url.split('?');
		if ( urlparts.length >= 2 ) {

			var prefix= encodeURIComponent(parameter)+'=';
			var pars= urlparts[1].split(/[&;]/g);

			//reverse iteration as may be destructive
			for (var i= pars.length; i-- > 0;) {
				//idiom for string.startsWith
				if (pars[i].lastIndexOf(prefix, 0) !== -1) {
					pars.splice(i, 1);
				}
			}

			url= urlparts[0]+'?'+pars.join('&');
			return url;

		} else {

			return url;
		}
	}
	
	jQuery('#importing-modal-footer-button').on('click', function(e) {
		e.preventDefault();
		jQuery(this).prop('disabled', true ).text(nhpopts.reloading_page);
		var a = mtsRemoveURLParameter( window.location.href , 'tab' );
		window.location.href = a;
	});
});
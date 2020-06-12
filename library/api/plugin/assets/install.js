var q_api = q_api || {};

jQuery(document).ready(function($) {

	"use strict";

	var is_loading = false;

	/*
	*  plugin_install
	*  Install the plugin
	*
	*
	*  @param el       object Button element
	*  @param plugin   string Plugin slug
	*  @since 1.0
	*/

	q_api.plugin_install = function(el, plugin){

		// Confirm activation
		var r = confirm(q_api_plugin.install_now);

		if (r) {

			is_loading = true;
			el.addClass('installing');

			$.ajax({
				type: 'POST',
				url: q_api_plugin.ajax_url,
				data: {
					action: 'api_install',
					plugin: plugin,
					nonce: q_api_plugin.admin_nonce,
					dataType: 'json'
				},
				success: function(data) {
					if(data){
						if(data.status === 'success'){
							el.attr('class', 'activate button button-primary');
							el.html(q_api_plugin.activate_btn);
						} else {
							el.removeClass('installing');
						}
					} else {
							el.removeClass('installing');
					}
					is_loading = false;
				},
				error: function(xhr, status, error) {
					console.log(status);
					el.removeClass('installing');
					is_loading = false;
				}
			});

		}
	}



	/*
	*  plugin_activate
	*  Activate the plugin
	*
	*
	*  @param el       object Button element
	*  @param plugin   string Plugin slug
	*  @since 1.0
	*/

	q_api.plugin_activate = function(el, plugin){

		$.ajax({
			type: 'POST',
			url: q_api_plugin.ajax_url,
			data: {
				action: 'api_activate',
				plugin: plugin,
				nonce: q_api_plugin.admin_nonce,
				dataType: 'json'
			},
			success: function(data) {
				if(data){
					if(data.status === 'success'){
						el.attr('class', 'installed button disabled');
						el.html(q_api_plugin.installed_btn);
					}
				}
				is_loading = false;
			},
			error: function(xhr, status, error) {
				console.log(status);
				is_loading = false;
			}
		});

	};



	/*
   *  Install/Activate Button Click
   *
   *  @since 1.0
   */

	$(document).on('click', '.q-plugin-install a.button', function(e){
		var
			el = $(this),
			plugin = el.data('slug');

		e.preventDefault();

		if(!el.hasClass('disabled')){

			if(is_loading) return false;

			// Installation
			if(el.hasClass('install')){
				q_api.plugin_install(el, plugin);
			}

			// Activation
			if(el.hasClass('activate')){
				q_api.plugin_activate(el, plugin);
			}
		}
	});

});

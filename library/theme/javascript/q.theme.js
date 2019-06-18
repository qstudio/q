/**
Plugin:     Q Theme
Version:    2.4.3
Date:       18/06/2019 07:26:51 pm
*/
$q_modal_hash_value=!1;$q_modal_key=!1;$q_modal_args=!1;if(typeof jQuery!=='undefined'){jQuery(document).ready(function(){jQuery(window).bind('hashchange',function(e){e.preventDefault();q_modal_toggle($q_modal_args)})})}
function q_modal($args)
{$args=$args||!1;if(!$args){return!1}
$q_modal_args=$args;q_modal_toggle($q_modal_args)}
function q_modal_hash($q_modal_args)
{$q_modal_key=q_get_hash_value_from_key('modal');return $q_modal_key}
var q_modal_close=function q_modal_close_function(e){q_modal_do_close(e)}
var q_modal_get_device=function(){$dev=jQuery('body').first().hasClass('device-mobile')?'handheld':'desktop';jQuery('.featherlight-content').parent().addClass('modal-'+$dev)}
function q_modal_do_close(e){$hash='';if($close=jQuery('[data-modal-key="'+$q_modal_key+'"]').find('span').attr('data-modal-close')){$hash=$close}
if($scroll=jQuery('[data-modal-key="'+$q_modal_key+'"]').find('span').attr('data-modal-scroll')){if(jQuery('[data-scroll="'+$scroll+'"]').length){$hash=$hash+'/scroll/'+$scroll}}
jQuery('html').removeClass('modal-open');jQuery('.featherlight').removeClass('modal-'+$q_modal_key);window.location.hash=$hash;return!1}
function q_modal_toggle($q_modal_args)
{$q_modal_key=q_modal_hash();jQuery('.featherlight').remove();if(!$q_modal_key){return!1}
q_modal_callback($q_modal_args,$q_modal_key);jQuery('html').addClass('modal-open');$content=jQuery('[data-modal-key="'+$q_modal_key+'"]').html();$q_modal_close=jQuery.featherlight($content,{type:'html',beforeOpen:q_modal_get_device,afterClose:q_modal_close,afterOpen:q_do_lazy,variant:'modal-'+$q_modal_key})}
function q_html_decode(input)
{var doc=new DOMParser().parseFromString(input,"text/html");return doc.documentElement.textContent}
function q_modal_callback($q_modal_args,$q_modal_key){$q_modal_args=$q_modal_args||!1;$q_modal_key=$q_modal_key||!1
if(!$q_modal_args||!$q_modal_key){return!1}
if($q_modal_args.callback){_function=$q_modal_args.callback;if(window[_function]){window[_function]($q_modal_key)}else{}}else{}}
if(typeof jQuery!=='undefined'){jQuery(window).bind("load",function(){if($the_hash=q_tab_hash()){q_tab($the_hash)}else{q_tab_default()}});jQuery(document).ready(function(){jQuery(window).bind('hashchange',function(e){history.navigationMode='compatible';e.preventDefault();$the_hash=q_tab_hash();if($the_hash)q_tab($the_hash)});jQuery('select.tab-navigation').on('change',function(){$trigger=jQuery(this).find('option:selected').attr("data-tab-trigger");q_tab($trigger);$trigger='#tab/'+$trigger
if(history.pushState){history.pushState(null,null,$trigger)}
else{location.hash=$trigger}})});function q_tab_default(){jQuery('.q-tab-target').hide().addClass('q-tab-hidden').removeClass('q-tab-current');jQuery('.q-tab-trigger').removeClass('q-tab-current');jQuery('.q-tab-trigger:first-child').addClass('q-tab-current');jQuery('.q-tab-target:first-child').removeClass('q-tab-hidden').addClass('q-tab-current').show()}
function q_tab(data_id){$target=jQuery("[data-tab-target='"+data_id+"']");if($target.length){jQuery('.q-tab-target').each(function(){jQuery(this).hide().addClass('q-tab-hidden').removeClass('q-tab-current')});jQuery(".q-tab-trigger").removeClass('q-tab-current');$target.show().addClass('q-tab-current').removeClass('q-tab-hidden');jQuery("[data-tab-trigger='"+data_id+"']").addClass('q-tab-current')}}
function q_tab_hash(){var $hash=q_get_hash_value_from_key('tab');if(!$hash){return!1}
return $hash}}
if(typeof jQuery!=='undefined'){jQuery('body').on('click','.q_scroll a',function(e){$the_hash=jQuery(this).attr('data-scroll-nav');if($the_hash)q_scroll($the_hash)});jQuery(window).bind("load",function(){$the_hash=q_scroll_hash();if($the_hash)q_scroll($the_hash)});jQuery(document).ready(function(){var $the_hash='';jQuery(window).bind('hashchange',function(e){history.navigationMode='compatible';e.preventDefault();$the_hash=q_scroll_hash();if($the_hash)q_scroll($the_hash)});jQuery('a[href^="#"]').on('click',function(e){$the_hash=q_push_hash();if($the_hash)q_scroll($the_hash)})});function q_scroll(data_id){jQuery(".q_scroll > span").removeClass('current');if(jQuery("[data-scroll-slug='"+data_id+"']").length){var target=jQuery("[data-scroll-slug='"+data_id+"']");var targetOffset=(target.offset().top);jQuery('html,body').animate({scrollTop:targetOffset+"px"},500,'swing');jQuery("#scroll-nav-"+data_id).parent('span').addClass('current')}}
function q_scroll_hash()
{var $hash=window.location.hash.substring(1);if($hash.indexOf('scroll/')==0){console.log('No scroll...');return!1}
$hash=$hash.replace('/scroll/','').trim().replace(/\//g,'');if(!$hash){return!1}
return $hash}}
if(typeof jQuery!=='undefined'){jQuery('body').on('click','.q-push a',function(e){$the_hash=jQuery(this).attr('data-push-nav');if($the_hash)q_push($the_hash)});jQuery(window).bind("load",function(){$the_hash=q_push_hash();if($the_hash)q_push($the_hash)});jQuery(document).ready(function(){jQuery(window).bind('hashchange',function(e){history.navigationMode='compatible';e.preventDefault();$the_hash=q_push_hash();if($the_hash)q_push($the_hash)})});function q_push(data_id){if(jQuery("[data-scroll-slug='"+data_id+"']").length){var target=jQuery("[data-scroll-slug='"+data_id+"']");var targetOffset=(target.offset().top);console.log('Element found: '+data_id);console.log('Push ScrollTo: '+targetOffset);jQuery('html,body').animate({scrollTop:targetOffset+"px"},500,'swing')}}
function q_push_hash()
{var $hash=window.location.hash.substring(1);if($hash.indexOf('push/')==0){console.log('No push...');return!1}
$hash=$hash.replace('/push/','').trim().replace(/\//g,'');if(!$hash){return!1}
return $hash}}
var $q_select_hash_value;var $q_select_args=!1;if(typeof jQuery!=='undefined'){jQuery(document).ready(function(){jQuery(window).bind('hashchange',function(e){q_select_hash();q_select_change()})});jQuery(document).on('change','select#q-select',function(e){$value=jQuery(this).val();$show=jQuery("div.q-select [data-select='"+$value+"']");$shown=!1;if($show&&!$shown){jQuery('.modal-data').removeClass('shown').addClass('hidden').hide(0);jQuery('.featherlight').remove();jQuery("div.q-select > *").fadeOut('fast');$show.fadeIn('fast');window.location.hash='/filter/'+$value;$shown=!0}})}
function q_select($args)
{$args=$args||!1;if(!1==$args){return!1}
$q_select_args=$args;$hash=window.location.hash.substring(1);if($hash.toLowerCase().indexOf('modal')>=0){return!1}
q_select_hash();q_select_default();q_select_change(!0)}
function q_select_hash()
{$q_select_hash_value=q_get_hash_value_from_key('filter');if(!$q_select_hash_value){$q_select_hash_value=!1;return!1}
return!0}
function q_select_change()
{if(!1==$q_select_args||!1==$q_select_hash_value){return!1}
if($scroll=q_get_hash_value_from_key('scroll')){jQuery('html,body').delay(2000).animate({scrollTop:jQuery('[data-scroll="'+$scroll+'"]').offset().top-400},500)}
jQuery('div.q-select > *').hide(0);jQuery("div.q-select [data-select='"+$q_select_hash_value+"']").show(0);jQuery('#q-select').find('option[value="'+$q_select_hash_value+'"]').prop('selected','selected')}
function q_select_default()
{if(!1==$q_select_args){return!1}
if($q_select_hash_value){return!1}
jQuery("div.q-select [data-select='"+$q_select_args.default+"']").show(0);jQuery('#q-select').find('option[value="'+$q_select_args.default+'"]').prop('selected','selected');window.location.hash='/filter/'+$q_select_args.default}
if(typeof jQuery!=='undefined'){function q_filter_disable_options(){jQuery('select.q-filter').each(function(){jQuery("select.q-filter > option:not(:selected)").each(function(){var type=jQuery(this).parent('select').data('filter-type');var $data='data-'+type;var $value=this.value;if('all'==$value){return}
if(0==jQuery('.q-filter').find('['+$data+'='+$value+']').length){jQuery(this).prop("disabled",!0)}else{jQuery(this).prop("disabled",!1)}})})}
function q_get_filters(){q_filters=jQuery('.filters').children();if(typeof q_filters=='undefined'||q_filters.length==0){return!1}else if(q_filters.length==1){q_filters=jQuery(q_filters[0]).val()}else{var temp_array=new Array();for(z=0;z<q_filters.length;z++){if(typeof jQuery(q_filters[z]).val()!=='undefined'){temp_array.push(jQuery(q_filters[z]).val())}}
q_filters=temp_array}
if(q_filters.length>0){return q_filters}else{return!1}}
function q_get_hash(hash){hash=hash||window.location.hash.substring(1);if(hash.indexOf('filter')!==1){return 'all'}
hash=hash.replace('/filter/','');if(hash.indexOf('/')>0){if(hash.indexOf('/modal/')>0){hash=hash.substring(0,hash.indexOf('/modal/'))}else{hash=hash.replace(/\/$/,"")}
hash=hash.split('/');if(hash.length==1)hash=hash[0]}
if(typeof hash!=='undefined'&&hash!==null&&hash!==''){return hash}else{return!1}}
function q_update_hash(ar){if(Array.isArray(ar))ar=(ar.join('/'));window.location.hash='/filter/'+ar}
function q_do_load(val){if(Array.isArray(val)){var frag=jQuery(document.createDocumentFragment());var results=!1;var filter_array=new Array();for(x=0;x<val.length;x++){var d_type='data-'+filter_class_types[x];var d_val=val[x];q_select_option(filter_class_types[x],d_val);if(results){if(d_val=='all'){var result=jQuery('.item['+d_type+']');results=jQuery(results).filter(result)}else{var result=jQuery('.item['+d_type+'="'+d_val+'"]');results=jQuery(results).filter(result)}}else{if(d_val=='all'){var results=jQuery('.item['+d_type+']')}else{var results=jQuery('.item['+d_type+'="'+d_val+'"]')}}}
jQuery(results).removeClass('hidden').addClass('shown').fadeIn('fast')}else{q_select_option(filter_class_types,val);if(Array.isArray(filter_class_types)){var data_query=filter_class_types[0]}else{var data_query=filter_class_types}
data_query='[data-'+data_query+'="'+val+'"]';jQuery('.item'+data_query).removeClass('hidden').addClass('shown').fadeIn('fast')}
jQuery(filter_class_filterable).each(function(){if(!jQuery(this).children('*').hasClass('shown')){jQuery(this).find('.filter-no-results').removeClass('hidden').addClass('shown').fadeIn("fast")}})}
function q_load_content(hash_val,scroll){scroll=scroll||!1;jQuery('.filter-no-results').removeClass('shown').addClass('hidden').hide(0);jQuery('.modal-data').removeClass('shown').addClass('hidden').hide(0);jQuery(filter_class_filterable).find('> .item').addClass('hidden').removeClass('shown').hide(0);q_filter_disable_options();if(scroll&&hash_val){jQuery('html,body').animate({scrollTop:jQuery(filter_class_filter).offset().top-60},500)}
if(!hash_val||hash_val=='all'){q_select_option(filter_class_types,'all');jQuery(filter_class_filterable).find('> .item').slice(0,filter_show_on_load).addClass('shown').fadeIn('fast')}else{q_do_load(hash_val)}
q_filter_callback()}
function q_select_option(element,option){jQuery('select.filter-'+element).find('option[value="'+option+'"]').prop('selected','selected')}
function q_filter_callback()
{if(filter_callback&&typeof(filter_callback)=="object"){jQuery.each(filter_callback,function(index,value){if(window[value]){window[value]()}else{}})}}}
jQuery(window).bind("load",function(){$the_hash=q_toggle_hash();if($the_hash){q_toggle($the_hash)}else{q_toggle_default()}});jQuery(document).ready(function(){jQuery(window).bind('hashchange',function(e){history.navigationMode='compatible';e.preventDefault();$the_hash=q_toggle_hash();if($the_hash)q_toggle($the_hash)})});function q_toggle_default(){jQuery('.q-toggle-target').hide().addClass('q-toggle-hidden').removeClass('q-toggle-current');jQuery('.q-toggle-trigger').removeClass('q-toggle-current');jQuery('.q-toggle-trigger:first-child').addClass('q-toggle-current');jQuery('.q-toggle-target:first-child').removeClass('q-toggle-hidden').addClass('q-toggle-current').show()}
function q_toggle(data_id){$target=jQuery("[data-toggle-target='"+data_id+"']");if($target.length){jQuery('.q-toggle-target').each(function(){jQuery(this).hide().addClass('q-toggle-hidden').removeClass('q-toggle-current')});jQuery(".q-toggle-trigger").removeClass('q-toggle-current');$target.show().addClass('q-toggle-current').removeClass('q-toggle-hidden');jQuery("[data-toggle-trigger='"+data_id+"']").addClass('q-toggle-current')}}
function q_toggle_hash(){var $hash=q_get_hash_value_from_key('toggle');if(!$hash){return!1}
return $hash}

/**
 * Q Load -> jQuery
 * 
 * @author: qstudio.us
 */

jQuery(document).ready(function($) {
    
    // console.dir( q_load_params )

    // global ##
    var $window = $(window);

    // variables ##
    var q_load_exclude = false; // track posts already loaded ##
    var taxonomy = false; // taxonomy filter ##
    var q_load_last_loaded = false; // reached last one ##
    var q_load_target = q_load_params.target;
    var $q_load_target = $(""+q_load_target+"");
    var q_load_trigger = q_load_params.trigger;
    var $q_load_trigger = $(""+q_load_trigger+"");

    // kick out if q_load_params.target selector not found ##
    if ( 
        ! $q_load_target
        || ! $q_load_target.length
        || $q_load_target.length == 0 
    ) {
        
        console.log( "Target NOT Found.." );

        q_load_last_loaded = true; // reached last one ##
        
        return;

    }
    
    // var handle = JSON.parse(q_load_params.handle);

    var ajaxurl = q_load_params.ajaxurl;
    var imageurl = ajaxurl.replace('admin-ajax.php','images/');
    q_load_last_msg = typeof q_load_last_msg !== 'undefined' ? q_load_last_msg : q_load_params.no_more_posts;
     
    var q_load = function( q_load_exclude = false ){
        
        // get updated tax_term ##
        q_load_tax_term = window.location.hash ? window.location.hash.substring(1) : 'false', // check if we have anything in the hash,

        // remove "/filter/" from string ##
        q_load_tax_term = q_load_tax_term.replace( '/filter/','' );

        $.ajax({
            type       : "POST",
            dataType   : "json",
            url        : ajaxurl,
            data       : {
                
                // wp_ajax ##
                action:             "q_load",

                // value updated on each routine ##
                exclude:            q_load_exclude,

                // make sure we have a real number ##
                posts_per_page :    parseInt( q_load_params.posts_per_page ),

                // standard data, not re-built - so pulled from DOM ##
                post_type :         q_load_params.post_type,
                order :             q_load_params.order,
                orderby :           q_load_params.orderby,
                meta_key :          q_load_params.meta_key,
                meta_type :         q_load_params.meta_type,
                taxonomy :          q_load_params.taxonomy,
                tax_term :          q_load_tax_term,
                view :              q_load_params.view,
                method :            q_load_params.method,
                handle :            q_load_params.handle,
                holder :            q_load_params.holder,
                markup :            q_load_params.markup,
                date_format :       q_load_params.date_format,

                // nonce ##
                security:           q_load_params.security
            
            },
            beforeSend : function(){

                // console.log( 'exclude: '+ q_load_exclude );
                // console.log( "tax_term: "+q_load_tax_term );

                $q_load_target.append('<span id="temp_load" class="ajax_lazy"></span>');

            },
            success    : function(data){

                // remove loader ##
                $("#temp_load").fadeOut().remove();
                
                // Make jQuery object from HTML string
                $markup = $(data.markup);
                // console.dir($markup);

                // last one ##
                if (
                    // $data.markup == '' 
                    data.markup == '' 
                    || $markup.selector == '0' 
                ) { 
                    
                    if ( $('#last-one').length == 0 ) {
                        
                        $q_load_target.append( $('<li>', { id: 'last-one'}) ); // make it ##
                        
                        $("#last-one").text(q_load_last_msg).addClass("center").css({"list-style-type" : "none", "font-size" : "small"});
                        
                        // console.log('last one reached');
                    
                    }
                    
                    q_load_last_loaded = true; // reached last one ##

                    // hide the triggers ##
                    $q_load_trigger.fadeOut("fast");

                    return false;
                 
                } else if ( $markup.length != 0 ) {

                    // append and show markup ##
                    $q_load_target.append( $markup.fadeIn() );

                    // callback is defined as an array, so loop over each ##
                    js_callback = q_load_params.callback;
                    // console.dir( js_callback );

                    // load up modal engine ##
                    if ( 
                        js_callback 
                        && typeof( js_callback ) == "object" 
                    ) {

                        jQuery.each( js_callback, function( index, value ){
                            
                            if ( window[value] ) {
                    
                                // console.log( 'calling callback from q.load: '+value );
                    
                                window[value]();
                    
                            } else {
                    
                                // console.log( 'callback not available: '+value );
                    
                            }
                    
                        });

                    }

                    // update trigger exclude value ##
                    $q_load_trigger.attr( 'data-q-load-exclude', data.exclude );

                    // show the triggers ##
                    $q_load_trigger.fadeIn("fast");

                }
                
            },
            error     : function(jqXHR, textStatus, errorThrown) {
                $("#temp_load").fadeOut().remove();
                // console.log('error: '+jqXHR + " :: " + textStatus + " :: " + errorThrown);
            }
        });
    }

    // trigger ##
    $q_load_trigger.click(function( e ){
 
        // console.log( 'clicked it..' );

        // see if we've got any posts already loaded ##
        q_load_exclude = $(this).attr("data-q-load-exclude");
        // console.log( q_load_exclude );

        // hide the triggers ##
        $(this).fadeOut("fast");

        // call loader ##
        q_load( q_load_exclude );

    });

});
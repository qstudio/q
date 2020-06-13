jQuery(document).ready(function() {

    // move sticky check box out of sticky span and hide it (must be present on page for sticky to stick when post saved)

    // if stick checkbox doesn't exist add it
    if( jQuery('#sticky').length == 0 ) {
        jQuery('<input id="sticky" name="sticky" type="checkbox" value="sticky" style="display: none;">').appendTo('#post-visibility-select').css('display','none');
    }
    // if sticky checkbox does exist move it
    else {
        jQuery('#sticky').appendTo('#post-visibility-select').css('display','none');
    }

    // remove sticky span
    jQuery('#sticky-span').remove();
    jQuery('.q-sticky').click(function(e) {
        q_sticky_toggle(jQuery(this).attr('href'), jQuery(this));
        e.preventDefault();
    });

});

function q_sticky_toggle( args, obj ) {

    //console.log( obj );

    jQuery.ajax({

        url:"admin-ajax.php",
        type:"POST",
        data:"action=q_sticky&"+args,
        success:function(results) {
            if(results != '') {
                //alert('Success: '+results);
                //console.log( 'success' );
                //console.log( results );

                if( results.trim() == 'added') {
                    // check sticky box
                    //console.log( 'remove sticky from' +obj[0] );
                    jQuery('#sticky').attr('checked','checked');
                    obj.addClass('is-sticky');
                    obj.attr('title', 'Remove Sticky');
                }
                if( results.trim() == 'removed') {
                    // uncheck sticky box
                    //console.log( 'add sticky from' +obj[0] );
                    jQuery('#sticky').removeAttr('checked');
                    obj.removeClass('is-sticky');
                    obj.attr('title', 'Make Sticky');
                    id = obj.attr('id');
                    id= id.replace('q-sticky', '');
                    jQuery.each(jQuery('#post-'+id+' .post-state'), function(index, value) { if(jQuery(value).html().search('Sticky') > -1) {jQuery(value).html(''); } });
                }
            }
            else {
                alert('There was a problem with your request, please logout, log back in and try again. If the problem persist contact website administrator. ['+results+']');
            }
        }
    });
}
